<?php
// helpers/admin/upload.php — ADMIN HELPER: pag-upload at pagbura ng larawan sa assets/img/
// Nilo-load lang sa admin pages (routes na may 'admin' => true).

if (!defined('ROOT_PATH')) {
    exit('No direct access');
}

/* =========================================================
 |  IMAGE UPLOAD
 * ========================================================= */

const UPLOAD_IMAGE_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
];

/**
 * I-upload ang larawan sa assets/img/{folder}/
 * Ibinabalik ang path na gagamitin sa img(), hal. "projects/3f9a1c.jpg"
 * Kapag may problema, RuntimeException na may mensaheng puwedeng ipakita sa user.
 */
function upload_image(?array $file, string $folder, int $maxMb = 3): string
{
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        throw new RuntimeException('Please choose an image.');
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException("Image is too large (max {$maxMb} MB).");
        default:
            throw new RuntimeException('Upload failed. Please try again.');
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Invalid upload.');
    }
    if ($file['size'] > $maxMb * 1024 * 1024) {
        throw new RuntimeException("Image is too large (max {$maxMb} MB).");
    }

    // Tingnan ang totoong laman ng file, hindi ang pangalan nito
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset(UPLOAD_IMAGE_TYPES[$mime]) || @getimagesize($file['tmp_name']) === false) {
        throw new RuntimeException('Only JPG, PNG, WEBP or GIF images are allowed.');
    }

    $folder = trim(preg_replace('/[^a-z0-9_\-\/]/i', '', $folder), '/');
    $dir    = ROOT_PATH . 'assets/img/' . $folder . '/';
    if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
        throw new RuntimeException('Cannot create the upload folder.');
    }

    // Bagong random na pangalan — hindi magagamit ang pangalan na galing sa user
    $name = date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.' . UPLOAD_IMAGE_TYPES[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
        throw new RuntimeException('Cannot save the image.');
    }

    return $folder . '/' . $name;
}

/** Burahin ang larawang na-upload (hal. kapag nag-delete ng project) */
function delete_image(?string $path): void
{
    if (!$path || str_contains($path, '..')) {
        return;
    }
    $file = ROOT_PATH . 'assets/img/' . ltrim($path, '/');
    if (is_file($file)) {
        @unlink($file);
    }
}

/* =========================================================
 |  MULTIPLE IMAGES — <input type="file" name="images[]" multiple>
 * ========================================================= */

/**
 * Ayusin ang $_FILES['images'] para maging listahan ng isa-isang file.
 * (Ang PHP ay naghahati nito sa name[], tmp_name[], error[]... na mahirap gamitin.)
 * Hindi kasama ang mga walang laman na slot.
 */
function files(string $key): array
{
    $raw = $_FILES[$key] ?? null;
    if (!$raw || !is_array($raw['name'])) {
        return ($raw && ($raw['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) ? [$raw] : [];
    }

    $list = [];
    foreach ($raw['name'] as $i => $name) {
        if ($raw['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        $list[] = [
            'name'     => $name,
            'type'     => $raw['type'][$i],
            'tmp_name' => $raw['tmp_name'][$i],
            'error'    => $raw['error'][$i],
            'size'     => $raw['size'][$i],
        ];
    }
    return $list;
}

/**
 * I-upload lahat. Kapag may isang pumalya, buburahin ang mga naunang na-upload
 * at magbibigay ng error na may pangalan ng file.
 * Ibinabalik ang listahan ng paths, hal. ["projects/a.jpg", "projects/b.png"]
 */
function upload_images(array $files, string $folder, int $maxMb = 3): array
{
    $saved = [];
    foreach ($files as $file) {
        try {
            $saved[] = upload_image($file, $folder, $maxMb);
        } catch (RuntimeException $ex) {
            delete_images($saved);
            throw new RuntimeException($file['name'] . ': ' . $ex->getMessage());
        }
    }
    return $saved;
}

/** Burahin ang maraming larawan */
function delete_images(array $paths): void
{
    foreach ($paths as $path) {
        delete_image($path);
    }
}