<?php
// config/github.php — kinukuha ang GitHub stats (may cache para mabilis at hindi ma-rate limit)
//
// .env:
//   GITHUB_USER=username
//   GITHUB_TOKEN=            ← opsyonal; kailangan para sa contribution graph
//
// Walang token  → REST API: repos, stars, followers, languages (walang contribution graph)
// May token     → GraphQL API: lahat, kasama ang contribution graph

if (!defined('ROOT_PATH')) {
    exit('No direct access');
}

const GITHUB_CACHE_FILE = 'storage/cache/github.json';
const GITHUB_CACHE_TTL  = 3600; // 1 oras

function github_stats(): ?array
{
    $user = env('GITHUB_USER');
    if (!$user) {
        return null;
    }

    $cacheFile = ROOT_PATH . GITHUB_CACHE_FILE;

    // 1) Gamitin ang cache kung bago pa
    if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < GITHUB_CACHE_TTL) {
        $cached = json_decode((string) file_get_contents($cacheFile), true);
        if (is_array($cached) && ($cached['login'] ?? '') === $user) {
            return $cached;
        }
    }

    // 2) Kumuha ng bago sa GitHub
    $token = env('GITHUB_TOKEN');
    $data  = $token ? github_fetch_graphql($user, $token) : github_fetch_rest($user);

    if ($data) {
        if (!is_dir(dirname($cacheFile))) {
            @mkdir(dirname($cacheFile), 0775, true);
        }
        @file_put_contents($cacheFile, json_encode($data));
        return $data;
    }

    // 3) Kapag pumalya ang GitHub, gamitin ang lumang cache kung meron
    if (is_file($cacheFile)) {
        return json_decode((string) file_get_contents($cacheFile), true) ?: null;
    }

    return null;
}

/**
 * HTTP request papunta sa GitHub
 */
function github_request(string $url, ?string $token = null, ?array $body = null): ?array
{
    if (!function_exists('curl_init')) {
        error_log('GitHub: walang cURL extension');
        return null;
    }

    $headers = [
        'User-Agent: portfolio-site',
        'Accept: application/vnd.github+json',
    ];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_HTTPHEADER     => $headers,
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($response === false || $status >= 400) {
        error_log("GitHub request failed ($status): $url $error");
        return null;
    }

    return json_decode($response, true);
}

/**
 * May token: GraphQL (kasama ang contribution graph)
 */
function github_fetch_graphql(string $user, string $token): ?array
{
    $query = <<<'GQL'
query($login: String!) {
  user(login: $login) {
    login
    name
    url
    followers { totalCount }
    repositories(first: 100, ownerAffiliations: OWNER, isFork: false, privacy: PUBLIC) {
      totalCount
      nodes {
        stargazerCount
        languages(first: 5, orderBy: {field: SIZE, direction: DESC}) {
          edges { size node { name } }
        }
      }
    }
    contributionsCollection {
      contributionCalendar {
        totalContributions
        weeks {
          contributionDays { date contributionCount contributionLevel weekday }
        }
      }
    }
  }
}
GQL;

    $res = github_request('https://api.github.com/graphql', $token, [
        'query'     => $query,
        'variables' => ['login' => $user],
    ]);

    $u = $res['data']['user'] ?? null;
    if (!$u) {
        error_log('GitHub GraphQL: ' . json_encode($res['errors'] ?? 'walang data'));
        return null;
    }

    // Stars + languages (by size)
    $stars = 0;
    $langs = [];
    foreach ($u['repositories']['nodes'] as $repo) {
        $stars += $repo['stargazerCount'];
        foreach ($repo['languages']['edges'] as $edge) {
            $name = $edge['node']['name'];
            $langs[$name] = ($langs[$name] ?? 0) + $edge['size'];
        }
    }

    // Contribution calendar
    $levels = ['NONE' => 0, 'FIRST_QUARTILE' => 1, 'SECOND_QUARTILE' => 2, 'THIRD_QUARTILE' => 3, 'FOURTH_QUARTILE' => 4];
    $weeks  = [];
    foreach ($u['contributionsCollection']['contributionCalendar']['weeks'] as $week) {
        $days = [];
        foreach ($week['contributionDays'] as $d) {
            $days[] = [
                'date'    => $d['date'],
                'count'   => $d['contributionCount'],
                'level'   => $levels[$d['contributionLevel']] ?? 0,
                'weekday' => $d['weekday'],
            ];
        }
        $weeks[] = $days;
    }

    return [
        'login'         => $u['login'],
        'name'          => $u['name'],
        'url'           => $u['url'],
        'repos'         => $u['repositories']['totalCount'],
        'followers'     => $u['followers']['totalCount'],
        'stars'         => $stars,
        'contributions' => $u['contributionsCollection']['contributionCalendar']['totalContributions'],
        'weeks'         => $weeks,
        'languages'     => github_top_languages($langs),
        'updated'       => date('c'),
    ];
}

/**
 * Walang token: REST (walang contribution graph)
 */
function github_fetch_rest(string $user): ?array
{
    $u = github_request('https://api.github.com/users/' . rawurlencode($user));
    if (!$u || empty($u['login'])) {
        return null;
    }

    $repos = github_request('https://api.github.com/users/' . rawurlencode($user) . '/repos?per_page=100&type=owner') ?? [];

    $stars = 0;
    $langs = [];
    foreach ($repos as $repo) {
        if (!empty($repo['fork'])) continue;
        $stars += $repo['stargazers_count'] ?? 0;
        if (!empty($repo['language'])) {
            $langs[$repo['language']] = ($langs[$repo['language']] ?? 0) + 1;   // bilang ng repo
        }
    }

    return [
        'login'         => $u['login'],
        'name'          => $u['name'],
        'url'           => $u['html_url'],
        'repos'         => $u['public_repos'],
        'followers'     => $u['followers'],
        'stars'         => $stars,
        'contributions' => null,
        'weeks'         => [],
        'languages'     => github_top_languages($langs),
        'updated'       => date('c'),
    ];
}

/**
 * Top 5 languages → porsyento
 */
function github_top_languages(array $langs, int $limit = 5): array
{
    arsort($langs);
    $total = array_sum($langs) ?: 1;
    $out   = [];
    foreach (array_slice($langs, 0, $limit, true) as $name => $value) {
        $out[] = ['name' => $name, 'percent' => round($value / $total * 100, 1)];
    }
    return $out;
}