<?php
// portfolio.php — main entry point (front controller)
// Lahat ng request dadaan dito dahil sa .htaccess

define('ROOT_PATH', __DIR__ . '/');

ob_start();        // para gumana ang header('Location: ...') kahit sa loob ng page (hal. login)
session_start();   // para sa admin login

require_once ROOT_PATH . 'config/config.php';
require_once ROOT_PATH . 'config/auth.php';
require_once ROOT_PATH . 'config/database.php';
require_once ROOT_PATH . 'config/github.php';
require_once ROOT_PATH . 'routes/web.php';
require_once ROOT_PATH . 'includes/ui.php';
require_once ROOT_PATH . '/vendor/autoload.php';

// --- Kunin ang current route mula sa URL ---
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = substr($uri, strlen(BASE_URL));          // tanggalin ang base folder (hal. /portfolio)
$path = trim($path, '/');
if ($path === '' || $path === 'portfolio.php') {
    $path = 'home';
}

// --- Logout (walang page, redirect lang) ---
if ($path === 'admin/logout') {
    admin_logout();
    redirect(ADMIN_LOGIN_ROUTE);
}

// --- Hanapin sa routes ---
if (isset($routes[$path])) {
    $route = $routes[$path];
} else {
    http_response_code(404);
    $route = ['page' => 'user/404', 'title' => 'Page Not Found'];
}

// --- Bantay: admin page pero hindi naka-login → login page ---
if (!empty($route['admin'])) {
    require_admin();
}

$isAdmin = !empty($route['admin']);
$isBare = !empty($route['bare']);
$fullscreen = !empty($route['fullscreen']);

// --- Helpers ---
// helpers/*.php       → shared (hal. db.php) — lahat ng page
// helpers/admin/*.php → admin lang (form.php, upload.php)
foreach (glob(ROOT_PATH . 'helpers/*.php') as $helperFile) {
    require_once $helperFile;
}
if ($isAdmin) {
    foreach (glob(ROOT_PATH . 'helpers/admin/*.php') as $helperFile) {
        require_once $helperFile;
    }
}

// --- Controller: default GET → index(), POST → store() (puwedeng palitan gamit ang 'actions' sa route) ---
$viewData = [];
if (!empty($route['controller'])) {
    require_once ROOT_PATH . 'controllers/Controller.php';
    require_once ROOT_PATH . 'controllers/' . $route['controller'] . '.php';

    $class   = basename($route['controller']);                 // 'admin/ProjectController' → 'ProjectController'
    $actions = ($route['actions'] ?? []) + ['GET' => 'index', 'POST' => 'store'];
    $action  = $actions[$_SERVER['REQUEST_METHOD']] ?? $actions['GET'];

    [$route['page'], $viewData] = (new $class())->$action();
}

$pageFile = ROOT_PATH . 'pages/' . $route['page'] . '.php';
if (!is_file($pageFile)) {
    http_response_code(404);
    $route = ['page' => 'user/404', 'title' => 'Page Not Found'];
    $pageFile = ROOT_PATH . 'pages/user/404.php';
}

$pageTitle = $route['title'] . ' | ' . ($isAdmin ? 'Admin' : env('APP_NAME', 'Portfolio'));

// Aling sidebar ang gagamitin
$sidebarFile = $isAdmin ? 'includes/admin-sidebar.php' : 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <?php if ($isAdmin || $isBare): ?>
        <meta name="robots" content="noindex, nofollow">
    <?php endif; ?>

    <!-- Font (game-style) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (v4 browser CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Tailwind config: sariling font, kulay at animation -->
    <style type="text/tailwindcss">
        @theme {
            --font-game: 'Plus Jakarta Sans', system-ui, sans-serif;
            --color-ink: #0a0a0a;
            --color-muted: #737373;

            --animate-float: float 4s ease-in-out infinite;
            --animate-rise: rise 12s linear infinite;
            --animate-pop: pop .5s ease-out both;
            --animate-pane: pane .45s ease-out;
            --animate-ring: ring 2.5s ease-in-out infinite;

            @keyframes float { 50% { transform: translateY(-14px); } }
            @keyframes rise  { to { transform: translateY(-110vh); opacity: 0; } }
            @keyframes pop   { from { opacity: 0; transform: scale(.6); } }
            @keyframes pane  { from { opacity: 0; transform: translateX(-20px); } }
            @keyframes ring  { 50% { transform: scale(1.08); opacity: .4; } }
        }
    </style>
</head>

<body class="font-game bg-white text-neutral-900 antialiased overflow-x-hidden">

    <?php if ($isBare): ?>
        <!-- Walang sidebar (hal. login) -->
        <main class="min-h-screen flex flex-col">
            <?php extract($viewData, EXTR_SKIP); include $pageFile; ?>
        </main>
    <?php else: ?>
        <!-- Layout: sidebar sa kaliwa, page sa kanan -->
        <div
            class="grid grid-cols-1 grid-rows-[auto_1fr] min-h-screen lg:grid-rows-none lg:grid-cols-[auto_1fr] lg:h-screen lg:overflow-hidden">

            <?php include ROOT_PATH . $sidebarFile; ?>

            <main
                class="flex flex-col min-h-0 <?= $fullscreen ? '' : 'lg:overflow-y-auto' ?> <?= $isAdmin ? 'bg-neutral-50' : '' ?>">
                <?php extract($viewData, EXTR_SKIP); include $pageFile; ?>

                <?php if (!$fullscreen && !$isAdmin): ?>
                    <?php include ROOT_PATH . 'includes/footer.php'; ?>
                <?php endif; ?>
            </main>
        </div>
    <?php endif; ?>

    <script>
        window.APP = { baseUrl: "<?= e(BASE_URL) ?>" };
    </script>
    <script src="<?= asset('js/app.js') ?>?v=<?= filemtime(ROOT_PATH . 'assets/js/app.js') ?>"></script>
</body>

</html>