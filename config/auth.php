<?php
// config/auth.php — helpers para sa admin login (session + CSRF)
// Kailangang naka-session_start() na bago gamitin (nasa portfolio.php).

if (!defined('ROOT_PATH')) {
    exit('No direct access');
}

// Mga route ng admin (dapat tugma sa routes/web.php)
const ADMIN_LOGIN_ROUTE = 'loginadmin';   // login page
const ADMIN_HOME_ROUTE  = 'admindashboard';    // pupuntahan pagkatapos mag-login

/* =========================================================
 |  ADMIN SESSION
 * ========================================================= */

/** Naka-login ba ang admin? */
function is_admin(): bool
{
    return !empty($_SESSION['admin_id']);
}

/** Info ng naka-login na admin, o null kung wala */
function current_admin(): ?array
{
    if (!is_admin()) {
        return null;
    }
    return [
        'id'   => (int) $_SESSION['admin_id'],
        'name' => $_SESSION['admin_name'] ?? 'Admin',
    ];
}

/** Pangalan ng naka-login na admin (pang-display) */
function admin_name(): string
{
    return current_admin()['name'] ?? 'Admin';
}

/** I-login ang admin pagkatapos ma-verify ang password */
function admin_login(int $id, string $name): void
{
    session_regenerate_id(true);          // iwas session fixation
    unset($_SESSION['csrf']);             // bagong CSRF token pagkatapos mag-login
    $_SESSION['admin_id']   = $id;
    $_SESSION['admin_name'] = $name;
}

/** I-logout at burahin ang buong session */
function admin_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/** Bantay: kapag hindi naka-login, ipapadala sa login page */
function require_admin(): void
{
    if (!is_admin()) {
        redirect(ADMIN_LOGIN_ROUTE);
    }
}

/* =========================================================
 |  REDIRECT
 * ========================================================= */

/** Pumunta sa ibang route, hal. redirect('dashboard') */
function redirect(string $route): never
{
    header('Location: ' . url($route));
    exit;
}

/* =========================================================
 |  CSRF — proteksyon sa forms
 * ========================================================= */

/** Token ng kasalukuyang session (gagawa kung wala pa) */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/** Hidden input na ilalagay sa loob ng <form> */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/** Tama ba ang token na ipinadala ng form? */
function csrf_check(): bool
{
    return hash_equals(csrf_token(), (string) ($_POST['csrf'] ?? ''));
}