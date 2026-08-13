<?php
// index.php
define('ROOT_PATH', __DIR__);

require_once ROOT_PATH . '/vendor/autoload.php';

// ─── Load .env ────────────────────────────────────────────────────────────────
$envFile = ROOT_PATH . '/.env';

if (!file_exists($envFile)) {
    die('.env file not found.');
}

foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#'))
        continue;
    if (!str_contains($line, '='))
        continue;

    [$key, $value] = explode('=', $line, 2);
    $_ENV[trim($key)] = trim($value);
}

// ─── Constants ────────────────────────────────────────────────────────────────
define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID'] ?? '');
define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET'] ?? '');

// ─── Base URL ─────────────────────────────────────────────────────────────────
$isLocalhost = (
    strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false
);

define(
    'BASE_URL',
    $isLocalhost
    ? 'http://localhost/portfolio'
    : $_ENV['APP_URL']
);

// ─── Routing ──────────────────────────────────────────────────────────────────
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = trim($request, '/');
$request = preg_replace('#^portfolio/?#', '', $request);
$request = trim($request, '/');

if ($request === '' || $request === 'home') {
    $request = 'home';
}

// ─── Define Admin Routes ──────────────────────────────────────────────────────
$adminRoutes = [

    //hr
    'hr1',


    'adminlog',
    'adminhandler',
    'adminlogout'

];

if (in_array($request, $adminRoutes)) {
    session_name('portadmin');
} else {
    session_name('portclient');
}

session_start();

$routes = [
    'home'                                                  => 'client/auth/auth-1/login.php',
    'login'                                                 => 'client/auth/auth-1/login-handler.php',
    'logout'                                                => 'client/auth/auth-1/logout.php',

    'adminlog'                                              => 'administration/auth/login/index.php',
    'adminhandler'                                          => 'administration/auth/login/handler.php',
    'adminlogout'                                           => 'administration/auth/login/logout.php',

    //admin
    'hr1'                                                   => 'administration/hr/page-1/hrmain.php',

    //page-1
    'main'                                                  => 'client/page/page-1/main.php',
];



$file = $routes[$request] ?? null;

if ($file === null) {
    header('Location: ' . BASE_URL . '/');
    exit;
}

$filepath = ROOT_PATH . '/' . $file;

if (file_exists($filepath)) {
    include $filepath;
} else {
    header('Location: ' . BASE_URL . '/');
    exit;
}