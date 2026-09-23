<?php

declare(strict_types=1);

/**
 * Shared bootstrap: configuration, session, timezone, storage.
 * Every entry point (index.php, api.php, admin/*) requires this first.
 */

require_once __DIR__ . '/http.php';
require_once __DIR__ . '/store.php';
require_once __DIR__ . '/throttle.php';

/** @var array<string,mixed> $config */
$config = require dirname(__DIR__) . '/config.php';

date_default_timezone_set((string) ($config['timezone'] ?? 'UTC'));

boot_session((string) ($config['session_name'] ?? 'supportcenter'));

$store = new Store(
    (string) $config['data_dir'],
    (int) ($config['max_messages_per_chat'] ?? 500),
    (int) ($config['max_message_length'] ?? 4000),
);

$throttle = static fn (string $name): Throttle => new Throttle(
    rtrim((string) $config['data_dir'], "/\\") . DIRECTORY_SEPARATOR . 'throttle.json',
    (int) ($config['login_max_attempts'] ?? 8),
    (int) ($config['login_window'] ?? 900),
);
