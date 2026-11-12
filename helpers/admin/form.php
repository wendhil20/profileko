<?php
// helpers/admin/form.php — ADMIN HELPER: form input, error messages at flash messages
// Nilo-load lang sa admin pages (routes na may 'admin' => true).

if (!defined('ROOT_PATH')) {
    exit('No direct access');
}

/* =========================================================
 |  FORM INPUT
 * ========================================================= */

/** Value ng field na ipinadala ng form (naka-trim). Ginagamit din para manatili ang laman kapag may error. */
function input(string $key, string $default = ''): string
{
    return trim((string) ($_POST[$key] ?? $default));
}

/** Error message sa ilalim ng field (walang lalabas kung walang error) */
function field_error(array $errors, string $key): string
{
    if (empty($errors[$key])) {
        return '';
    }
    return '<p class="mt-1.5 text-sm text-red-600">' . e($errors[$key]) . '</p>';
}

/** aria-invalid para sa input na may error (pula ang border) */
function invalid(array $errors, string $key): string
{
    return 'aria-invalid="' . (empty($errors[$key]) ? 'false' : 'true') . '"';
}

/* =========================================================
 |  FLASH MESSAGE — mensaheng lalabas isang beses pagkatapos ng redirect
 * ========================================================= */

/** Mag-iwan ng mensahe: flash('success', 'Project added.') */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/** Kunin at burahin ang mga mensahe */
function flashes(): array
{
    $list = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $list;
}

/**
 * Lumampas ba sa post_max_size ng php.ini ang buong form?
 * Kapag oo, walang laman ang $_POST at $_FILES (kaya mukhang "expired" ang form).
 */
function post_too_large(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST'
        && empty($_POST) && empty($_FILES)
        && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0;
}

/** Listahan ng numbers mula sa form, hal. remove[] checkboxes → [3, 7] */
function input_ids(string $key): array
{
    $ids = array_map('intval', (array) ($_POST[$key] ?? []));
    return array_values(array_unique(array_filter($ids, fn($id) => $id > 0)));
}