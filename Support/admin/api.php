<?php

declare(strict_types=1);

/**
 * JSON endpoint for the console's live refresh.
 *
 *   GET  ?what=list&id=<selected>           -> sidebar HTML + unread total
 *   GET  ?what=thread&id=<conv>&after=<n>   -> new message bubbles + cursor
 *   POST  what=typing&id=<conv>             -> "agent is typing" ping
 *   POST  what=reply&id=<conv>&message=..   -> send agent reply, no reload
 */

require dirname(__DIR__) . '/lib/bootstrap.php';
require __DIR__ . '/partials.php';

header('X-Robots-Tag: noindex');
security_headers("'none'");

if (($_SESSION['agent'] ?? false) !== true) {
    json_out(['ok' => false, 'error' => 'Not signed in.'], 401);
}

/**
 * Typing flags and read watermarks for the console's live refresh.
 *
 * @return array{typing:array<string,bool>,read:array<string,int>}
 */
function console_presence(Store $store, string $id): array
{
    return [
        'typing' => [
            'visitor' => $store->isTyping($id, Store::ROLE_VISITOR),
            'agent' => $store->isTyping($id, Store::ROLE_AGENT),
        ],
        'read' => $store->presence($id)['read'],
    ];
}

/**
 * Apply the console's current queue filter, read from the request.
 *
 * The console sends ?filter=…&agent=… (GET on polls, POST on a reply), so a
 * refresh re-renders exactly the slice that is on screen instead of widening
 * the list back to every conversation.
 *
 * @param list<array<string,mixed>> $rows
 * @return list<array<string,mixed>>
 */
function api_queue_rows(array $rows): array
{
    $filter = param('filter', 12, true) ?: param('filter', 12);
    if (!in_array($filter, console_filters(), true)) {
        $filter = 'all';
    }
    $agentFilter = param('agent', 12, true) ?: param('agent', 12);

    return filter_rows($rows, $filter, $agentFilter, (string) ($_SESSION['agent_id'] ?? ''));
}

// A typing ping changes state, so it is a POST carrying a CSRF token.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && param('what', 20) === 'typing') {
    csrf_check();

    $pingId = param('id', 64);
    if (preg_match('/^[a-f0-9]{32}$/', $pingId) !== 1) {
        json_out(['ok' => false, 'error' => 'Invalid conversation id.'], 400);
    }
    if ($store->get($pingId) === null) {
        json_out(['ok' => false, 'error' => 'Conversation not found.'], 404);
    }

    $store->touchTyping($pingId, Store::ROLE_AGENT);
    json_out(['ok' => true]);
}

// Agent reply without a page reload. Accepts text and/or an attachment,
// stores it as ROLE_AGENT, and returns the new bubbles for instant append.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && param('what', 20) === 'reply') {
    csrf_check();

    $replyId = param('id', 64);
    if (preg_match('/^[a-f0-9]{32}$/', $replyId) !== 1) {
        json_out(['ok' => false, 'error' => 'Invalid conversation id.'], 400);
    }

    $existing = $store->get($replyId);
    if ($existing === null) {
        json_out(['ok' => false, 'error' => 'That conversation no longer exists.'], 404);
    }
    $countBefore = count($existing['messages'] ?? []);

    try {
        $upload = save_upload('file', $config);
    } catch (RuntimeException $e) {
        json_out(['ok' => false, 'error' => $e->getMessage()], 400);
    }

    $message = param('message', (int) ($config['max_message_length'] ?? 4000));

    // An attachment with no caption is a valid reply.
    if ($message === '' && $upload === null) {
        json_out(['ok' => false, 'error' => 'Write a reply or attach an image/video.'], 400);
    }

    $updated = $store->addMessage($replyId, Store::ROLE_AGENT, $message, $upload);
    if ($updated === null) {
        json_out(['ok' => false, 'error' => 'The reply could not be saved.'], 409);
    }

    // First human reply joins the chat: claim it so the visitor's
    // "Agent session" bar shows who is responding.
    $me = (string) ($_SESSION['agent_id'] ?? '');
    if ($me !== '' && (string) ($existing['assignee'] ?? '') === '') {
        $store->setAssignee($replyId, $me, $me);
        $updated = $store->get($replyId) ?? $updated;
    }

    // The agent just looked at it: clear unread, advance read mark, drop typing.
    $afterPoll = $store->poll($replyId, 0, Store::ROLE_AGENT);
    $total = count($updated['messages'] ?? []);
    $store->markRead($replyId, Store::ROLE_AGENT, $total);
    $store->clearTyping($replyId, Store::ROLE_AGENT);

    $replyAll = $updated['messages'] ?? [];
    $newMessages = array_slice($replyAll, $countBefore);
    // The message before the slice: it keeps the day separator from repeating
    // on every incremental poll.
    $prevAt = $countBefore > 0 ? (int) ($replyAll[$countBefore - 1]['at'] ?? 0) : null;
    $rows = $store->listConversations();

    json_out([
        'ok' => true,
        'messages_html' => render_messages($newMessages, $replyId, $countBefore, $prevAt),
        'cursor' => $total,
        'total' => $total,
        'status' => (string) ($updated['status'] ?? 'open'),
        'agent_unread' => (int) ($afterPoll['conversation']['agent_unread'] ?? 0),
        'presence' => console_presence($store, $replyId),
        'list_html' => render_list_items(api_queue_rows($rows), $replyId, $agents->byId()),
        'unread' => $store->unreadCount($rows),
    ]);
}

$what = param('what', 20, true);
$id = param('id', 64, true);

try {
    switch ($what) {
        case 'list':
            $rows = $store->listConversations();
            json_out([
                'ok' => true,
                'list_html' => render_list_items(api_queue_rows($rows), $id !== '' ? $id : null, $agents->byId()),
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

            $threadAll = $result['conversation']['messages'] ?? [];
            $total = count($threadAll);
            $prevAt = $after > 0 ? (int) ($threadAll[$after - 1]['at'] ?? 0) : null;

            // The agent is looking at the thread, so their read mark advances.
            $store->markRead($id, Store::ROLE_AGENT, $total);

            // Reading a thread clears its unread counter, so the queue and the
            // unread badge come along — the console only re-renders the list
            // when the total actually changed.
            $rows = $store->listConversations();

            json_out([
                'ok' => true,
                'messages_html' => render_messages($result['messages'], $id, $after, $prevAt),
                'cursor' => $total,
                // Client cursor ran past the end (history trimmed/deleted).
                'reset' => $after > $total,
                'status' => (string) ($result['conversation']['status'] ?? 'open'),
                'total' => $total,
                'agent_unread' => (int) ($result['conversation']['agent_unread'] ?? 0),
                'list_html' => render_list_items(api_queue_rows($rows), $id, $agents->byId()),
                'unread' => $store->unreadCount($rows),
                'presence' => console_presence($store, $id),
            ]);
            // no break (json_out exits)

        default:
            json_out(['ok' => false, 'error' => 'Unknown request.'], 400);
    }
} catch (Throwable $e) {
    error_log('[support-center] admin/api.php: ' . $e->getMessage());
    json_out(['ok' => false, 'error' => 'Server error.'], 500);
}
