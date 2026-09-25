<?php

declare(strict_types=1);

/**
 * Serves a chat attachment (image or video).
 *
 *   GET media.php?id=<conversation>&f=<token>
 *
 * The caller must either hold an agent session or know the conversation id,
 * which is the visitor's capability. The token alone is not enough: the file has
 * to be referenced by that conversation (see Store::findFile).
 *
 * Files live under data/uploads/ with no extension, which Apache denies
 * directly, so every byte is served from here with an explicit Content-Type and
 * nosniff. Nothing a visitor uploads can be executed.
 */

require __DIR__ . '/lib/bootstrap.php';

header('X-Robots-Tag: noindex');

$id = param('id', 64, true);
$token = param('f', 64, true);

/** Answer without leaking whether a conversation or file exists. */
function media_deny(int $status = 404): never
{
    http_response_code($status);
    header('Content-Type: text/plain; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: no-store');
    exit($status === 403 ? 'Forbidden.' : 'Not found.');
}

if (preg_match('/^[a-f0-9]{32}$/', $id) !== 1 || preg_match('/^[a-f0-9]{32}$/', $token) !== 1) {
    media_deny();
}

$isAgent = ($_SESSION['agent'] ?? false) === true;
$bindToSession = ($config['bind_chat_to_session'] ?? false) === true;

if (!$isAgent && $bindToSession) {
    // Stricter mode: only the session that started the conversation may read it.
    $chats = is_array($_SESSION['chats'] ?? null) ? $_SESSION['chats'] : [];
    if (!in_array($id, $chats, true)) {
        media_deny(403);
    }
}

$file = $store->findFile($id, $token);
if ($file === null) {
    media_deny();
}

$path = $file['path'];
$size = (int) $file['size'];
if ($size <= 0) {
    $size = (int) (filesize($path) ?: 0);
}

$mime = $file['mime'];
if (preg_match('#^[a-z]+/[a-z0-9.+-]+$#i', $mime) !== 1) {
    $mime = 'application/octet-stream';
}

$filename = str_replace(['"', '\\', "\r", "\n"], '', $file['name']) ?: 'attachment';

header('Content-Type: ' . $mime);
header('X-Content-Type-Options: nosniff');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: private, max-age=86400');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', (int) (filemtime($path) ?: time())) . ' GMT');

// ---------------------------------------------------------------------------
// Streaming, with single-range support so video seeking works in browsers.
// ---------------------------------------------------------------------------
$start = 0;
$end = $size > 0 ? $size - 1 : 0;
$range = (string) ($_SERVER['HTTP_RANGE'] ?? '');

if ($size > 0 && preg_match('/^bytes=(\d*)-(\d*)$/', trim($range), $m) === 1) {
    if ($m[1] === '' && $m[2] === '') {
        header('Content-Range: bytes */' . $size);
        http_response_code(416);
        header('Content-Length: 0');
        exit;
    }
    if ($m[1] !== '') {
        $start = (int) $m[1];
    }
    if ($m[2] !== '') {
        $end = (int) $m[2];
    }
    $end = min($end, $size - 1);

    if ($start > $end || $start >= $size) {
        header('Content-Range: bytes */' . $size);
        http_response_code(416);
        header('Content-Length: 0');
        exit;
    }

    http_response_code(206);
    header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
} else {
    header('Accept-Ranges: bytes');
}

header('Content-Length: ' . ($end - $start + 1));

$handle = @fopen($path, 'rb');
if ($handle === false) {
    media_deny();
}

try {
    if ($start > 0) {
        fseek($handle, $start);
    }
    $remaining = $end - $start + 1;
    while ($remaining > 0 && !feof($handle)) {
        $chunk = fread($handle, (int) min(262144, $remaining));
        if ($chunk === false || $chunk === '') {
            break;
        }
        $remaining -= strlen($chunk);
        echo $chunk;
        flush();
    }
} finally {
    fclose($handle);
}
