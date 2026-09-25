<?php

declare(strict_types=1);

/**
 * Public support center page — bare shell plus the "Online Consultant" widget.
 *
 * The look mirrors the reference consultant page: an empty, centred light-grey
 * stage with the chat panel on top of it. The widget itself is this install's
 * own widget.js, which talks to api.php and the agent console at /admin/, so
 * visitor messages arrive there in realtime. Nothing is proxied.
 */

require __DIR__ . '/lib/bootstrap.php';

$siteName = (string) ($config['site_name'] ?? 'Support Center');
$accent = (string) ($config['widget_color'] ?? '#1762f6');

// ---------------------------------------------------------------------------
// Public base URL: an explicit config value wins, otherwise detect it from the
// current request so the page is correct behind a proxy and in a subdirectory.
// ---------------------------------------------------------------------------
$baseUrl = rtrim(trim((string) ($config['base_url'] ?? '')), '/');
if ($baseUrl === '') {
    $scheme = is_https() ? 'https' : 'http';
    $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $dir = rtrim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'))), '/');
    $baseUrl = $scheme . '://' . $host . $dir;
}

// Widget options are handed to widget.js as data-* attributes on its <script>.
$quickReplies = $config['widget_quick_replies'] ?? [];
$quickReplies = is_array($quickReplies) ? array_values(array_filter($quickReplies, 'is_string')) : [];

$widgetOptions = [
    'support-url'   => $baseUrl,
    'title'         => (string) ($config['widget_title'] ?? 'Online Consultant'),
    'subtitle'      => (string) ($config['widget_subtitle'] ?? ''),
    'notice'        => (string) ($config['widget_notice'] ?? ''),
    'greeting'      => (string) ($config['widget_greeting'] ?? ''),
    'color'         => (string) ($config['widget_color'] ?? '#1762f6'),
    'position'      => (string) ($config['widget_position'] ?? 'right') === 'left' ? 'left' : 'right',
    'mode'          => (string) ($config['widget_mode'] ?? 'page') === 'bubble' ? 'bubble' : 'page',
    'poll'          => (string) (int) ($config['poll_interval'] ?? 4000),
    'max-upload-mb' => (string) (int) ($config['max_upload_mb'] ?? 25),
    'accept'        => implode(',', array_keys(is_array($config['allowed_media'] ?? null) ? $config['allowed_media'] : [])),
    'auto-open'     => ($config['widget_auto_open'] ?? false) === true ? '1' : '',
    'quick-replies' => $quickReplies === []
        ? ''
        : (string) json_encode($quickReplies, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
];

$widgetAttrs = '';
foreach ($widgetOptions as $name => $value) {
    if ($value !== '') {
        $widgetAttrs .= ' data-' . $name . '="' . e($value) . '"';
    }
}
?>
<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <!-- viewport-fit=cover lets the composer sit above the home indicator; zoom is left enabled. -->
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="<?= e($accent) ?>">
    <meta name="google" content="notranslate">
    <title><?= e($siteName) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
            overflow: hidden;
        }

        /* The full-page chat panel paints over this stage. */
        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            background-color: #eee;
        }
    </style>
</head>

<body>

    <div class="container"></div>

    <script src="<?= e($baseUrl) ?>/widget.js"<?= $widgetAttrs ?> async></script>
</body>

</html>
