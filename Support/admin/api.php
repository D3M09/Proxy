<?php

declare(strict_types=1);

/**
 * JSON endpoint for the console's live refresh.
 *
 *   GET ?what=list&id=<selected>            -> sidebar HTML + unread total
 *   GET ?what=thread&id=<conv>&after=<n>    -> new message bubbles + cursor
 */

require dirname(__DIR__) . '/lib/bootstrap.php';
require __DIR__ . '/partials.php';

header('X-Robots-Tag: noindex');

if (($_SESSION['agent'] ?? false) !== true) {
    json_out(['ok' => false, 'error' => 'Not signed in.'], 401);
}

$what = param('what', 20, true);
$id = param('id', 64, true);

try {
    switch ($what) {
        case 'list':
            $rows = $store->listConversations();
            json_out([
                'ok' => true,
                'list_html' => render_list_items($rows, $id !== '' ? $id : null),
                'unread' => $store->unreadCount($rows),
                'total' => count($rows),
            ]);
            // no break (json_out exits)

        case 'thread':
            if (preg_match('/^[a-f0-9]{32}$/', $id) !== 1) {
                json_out(['ok' => false, 'error' => 'Invalid conversation id.'], 400);
            }

            $after = max(0, (int) param('after', 10, true));
            // Reading the thread as the agent also clears its unread counter.
            $result = $store->poll($id, $after, Store::ROLE_AGENT);
            if ($result === null) {
                json_out(['ok' => false, 'error' => 'Conversation not found.'], 404);
            }

            $total = count($result['conversation']['messages'] ?? []);

            json_out([
                'ok' => true,
                'messages_html' => render_messages($result['messages']),
                'cursor' => $total,
                // Client cursor ran past the end (history trimmed/deleted).
                'reset' => $after > $total,
                'status' => (string) ($result['conversation']['status'] ?? 'open'),
                'total' => $total,
            ]);
            // no break (json_out exits)

        default:
            json_out(['ok' => false, 'error' => 'Unknown request.'], 400);
    }
} catch (Throwable $e) {
    error_log('[support-center] admin/api.php: ' . $e->getMessage());
    json_out(['ok' => false, 'error' => 'Server error.'], 500);
}
