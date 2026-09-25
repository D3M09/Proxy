<?php

declare(strict_types=1);

/**
 * Shared bootstrap: configuration, session, timezone, storage.
 * Every entry point (index.php, api.php, admin/*) requires this first.
 */

require_once __DIR__ . '/http.php';
require_once __DIR__ . '/store.php';
require_once __DIR__ . '/agents.php';
require_once __DIR__ . '/throttle.php';
require_once __DIR__ . '/upload.php';
require_once __DIR__ . '/menu.php';

/** @var array<string,mixed> $config */
$config = require dirname(__DIR__) . '/config.php';

date_default_timezone_set((string) ($config['timezone'] ?? 'UTC'));

boot_session((string) ($config['session_name'] ?? 'supportcenter'));

$store = new Store(
    (string) $config['data_dir'],
    (int) ($config['max_messages_per_chat'] ?? 500),
    (int) ($config['max_message_length'] ?? 4000),
);

/** Agent roster (console users). Only the admin console writes to it. */
$agents = new Agents((string) $config['data_dir']);

/**
 * Rate-limiter factory, one budget per family.
 *
 * Every family shares data/throttle.json but counts under its own key prefix
 * (see the call sites), so a visitor tapping through the menu can never eat the
 * login budget and a long conversation does not count against starting new
 * chats. $name selects the limits:
 *
 *   login       failed sign-ins          (strict: brute force is cheap)
 *   chat_start  new conversations        (per visitor IP)
 *   chat_send   messages in a chat       (per visitor IP)
 */
$throttle = static function (string $name) use ($config): Throttle {
    $limits = [
        'login' => [
            (int) ($config['login_max_attempts'] ?? 8),
            (int) ($config['login_window'] ?? 900),
        ],
        'chat_start' => [
            (int) ($config['chat_start_max_attempts'] ?? 20),
            (int) ($config['chat_start_window'] ?? 900),
        ],
        'chat_send' => [
            (int) ($config['chat_send_max_attempts'] ?? 60),
            (int) ($config['chat_send_window'] ?? 300),
        ],
    ];
    [$max, $window] = $limits[$name] ?? [8, 900];

    return new Throttle(
        rtrim((string) $config['data_dir'], "/\\") . DIRECTORY_SEPARATOR . 'throttle.json',
        max(1, $max),
        max(1, $window),
    );
};
