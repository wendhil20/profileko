<?php
// helpers/resume.php — SHARED HELPER: load the resume (table: portresume, id = 1)
// Loaded on every page (helpers/*.php), so both the user and admin controllers can use it.

if (!defined('ROOT_PATH')) {
    exit('No direct access');
}

/** Empty resume — used when the row doesn't exist yet */
function resume_defaults(): array
{
    return [
        'full_name' => '', 'title' => '', 'email' => '', 'phone' => '', 'address' => '',
        'linkedin' => '', 'github' => '', 'website' => '', 'summary' => '',
        'experience' => [], 'education' => [], 'skills' => [], 'certifications' => [],
        'updated_at' => null,
    ];
}

/** Load the resume with its JSON lists already decoded into arrays */
function resume_load(): array
{
    $resume = resume_defaults();

    try {
        $stmt = db()->prepare('SELECT * FROM portresume WHERE id = 1 LIMIT 1');
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    } catch (Throwable $ex) {
        error_log('Load resume failed: ' . $ex->getMessage());
        return $resume; // table missing etc. → empty resume instead of a crash
    }

    if (!$row) {
        return $resume;
    }

    foreach (['full_name', 'title', 'email', 'phone', 'address', 'linkedin', 'github', 'website', 'summary', 'updated_at'] as $key) {
        $resume[$key] = $row[$key] ?? $resume[$key];
    }
    foreach (['experience', 'education', 'skills', 'certifications'] as $list) {
        $decoded = json_decode((string) ($row[$list . '_json'] ?? ''), true);
        $resume[$list] = is_array($decoded) ? $decoded : [];
    }

    return $resume;
}

/** "https://www.linkedin.com/in/name/" → "linkedin.com/in/name" (for display) */
function resume_short_url(string $url): string
{
    $url = preg_replace('#^https?://(www\.)?#i', '', trim($url));
    return rtrim((string) $url, '/');
}

/** "June 2025" + "" + current → "June 2025 – Present" */
function resume_period(array $item): string
{
    $start = trim((string) ($item['start'] ?? ''));
    $end   = !empty($item['current']) ? 'Present' : trim((string) ($item['end'] ?? ''));
    if ($start === '' && $end === '') return '';
    if ($start === '' || $end === '') return $start . $end;
    return $start . ' – ' . $end;
}

/**
 * Split an experience's lines into groups.
 * A line that starts with "#" is a heading (e.g. "# CRM System"); the lines under it are its bullets.
 * Lines before the first heading form a group with no heading.
 *   → [ ['heading' => null|'CRM System', 'items' => ['…', '…']], … ]
 */
function resume_bullet_groups(array $lines): array
{
    $groups = [];
    $current = ['heading' => null, 'items' => []];

    foreach ($lines as $line) {
        $line = trim((string) $line);
        if ($line === '') continue;

        if ($line[0] === '#') {
            if ($current['heading'] !== null || $current['items']) {
                $groups[] = $current;
            }
            $current = ['heading' => trim(ltrim($line, '#')), 'items' => []];
        } else {
            $current['items'][] = $line;
        }
    }
    if ($current['heading'] !== null || $current['items']) {
        $groups[] = $current;
    }
    return $groups;
}