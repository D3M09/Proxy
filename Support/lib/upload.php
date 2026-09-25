<?php

declare(strict_types=1);

/**
 * Upload handling for chat attachments.
 *
 * A stored file lives at data/uploads/<token> — a random name with no extension,
 * so nothing in that directory is executable, and data/.htaccess already denies
 * web access. The real content type is kept with the message and every download
 * goes back out through media.php with an explicit Content-Type.
 */

/**
 * Validate an uploaded file and move it into data/uploads/.
 *
 * Returns null when the field was posted empty (no attachment), and throws a
 * RuntimeException carrying a message that is safe to show the user.
 *
 * @return array{token:string,mime:string,name:string,size:int}|null
 */
function save_upload(string $field, array $config): ?array
{
    if (!isset($_FILES[$field]) || !is_array($_FILES[$field])) {
        return null;
    }

    $file = $_FILES[$field];
    $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    $maxMb = max(1, (int) ($config['max_upload_mb'] ?? 25));

    if ($error === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException(upload_error_message($error, $maxMb));
    }

    $tmp = (string) ($file['tmp_name'] ?? '');
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        throw new RuntimeException('The upload did not arrive correctly. Please try again.');
    }

    $maxBytes = $maxMb * 1024 * 1024;
    $size = (int) ($file['size'] ?? 0);
    if ($size <= 0) {
        throw new RuntimeException('That file is empty.');
    }
    if ($size > $maxBytes) {
        throw new RuntimeException('That file is larger than the ' . $maxMb . ' MB limit.');
    }

    // Trust the file's real content, never the type the browser claims.
    $mime = detect_mime($tmp);
    $allowed = is_array($config['allowed_media'] ?? null) ? $config['allowed_media'] : [];
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only images and video can be sent.');
    }

    $dir = rtrim((string) $config['data_dir'], "/\\") . DIRECTORY_SEPARATOR . 'uploads';
    if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
        throw new RuntimeException('The uploads directory could not be created.');
    }

    $token = bin2hex(random_bytes(16));
    $target = $dir . DIRECTORY_SEPARATOR . $token;
    if (!move_uploaded_file($tmp, $target)) {
        throw new RuntimeException('The file could not be stored. Please try again.');
    }
    @chmod($target, 0600);

    return [
        'token' => $token,
        'mime' => $mime,
        'name' => clean_upload_name((string) ($file['name'] ?? '')),
        'size' => $size,
    ];
}

/** Content type of a local file, falling back to nothing when ext-fileinfo is absent. */
function detect_mime(string $path): string
{
    if (class_exists('finfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $detected = $finfo->file($path);
        if (is_string($detected) && $detected !== '') {
            return $detected;
        }
    }
    return '';
}

/** Reduce a client-supplied filename to something safe to echo back. */
function clean_upload_name(string $name): string
{
    $name = str_replace(['/', '\\', "\0", '"', '<', '>'], '', $name);
    $name = trim((string) preg_replace('/\s+/u', ' ', $name));
    return mb_substr($name, 0, 120);
}

function upload_error_message(int $code, int $maxMb): string
{
    return match ($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'That file is larger than the ' . $maxMb . ' MB limit.',
        UPLOAD_ERR_PARTIAL => 'The upload was interrupted. Please try again.',
        UPLOAD_ERR_NO_TMP_DIR => 'The server has no temporary directory configured.',
        UPLOAD_ERR_CANT_WRITE => 'The server could not write the upload.',
        UPLOAD_ERR_EXTENSION => 'The upload was blocked by a PHP extension.',
        default => 'The upload failed. Please try again.',
    };
}

function media_is_video(string $mime): bool
{
    return str_starts_with($mime, 'video/');
}
