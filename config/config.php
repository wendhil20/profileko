<?php
// config/config.php — env loader + helpers

if (!defined('ROOT_PATH')) {
    exit('No direct access');
}

/**
 * Basahin ang .env file at ilagay sa $_ENV
 */
function loadEnv(string $file): void
{
    if (!is_file($file)) {
        return;
    }
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

function env(string $key, $default = null)
{
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }
    return match (strtolower($value)) {
        'true'  => true,
        'false' => false,
        default => $value,
    };
}

loadEnv(ROOT_PATH . '.env');

// --- Error display depende sa APP_ENV ---
if (env('APP_ENV', 'production') === 'local') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

// --- Base URL (folder kung saan naka-install, hal. /portfolio sa XAMPP, '' sa Hostinger root) ---
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
define('BASE_URL', $scriptDir);

// --- Helpers ---
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Image URL na may fallback — kapag wala pa ang file sa assets/img,
 * placeholder ang lalabas imbes na sirang larawan.
 */
function img(string $file, string $fallback = 'placeholder.svg'): string
{
    $file = ltrim($file, '/');
    if (is_file(ROOT_PATH . 'assets/img/' . $file)) {
        return asset('img/' . $file);
    }
    return asset('img/' . $fallback);
}

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function isActive(string $route): string
{
    global $path;
    return ($path ?? '') === $route ? 'text-amber-600 font-semibold' : 'text-slate-600 hover:text-slate-900';
}