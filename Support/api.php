<?php

declare(strict_types=1);

/**
 * Public chat API used by widget.js.
 *
 *   POST action=start   message, name?, email?, page? -> new conversation
 *   POST action=send    id, message, file?            -> append visitor message
 *   POST action=typing  id                            -> "visitor is typing" ping
 *   GET  action=poll    id, after                     -> new messages since index
 *
 * By default a conversation id acts as a 128-bit bearer token held in the
 * visitor's browser, which keeps the widget working when it is embedded on
 * another domain. Set 'bind_chat_to_session' => true in config.php to also
 * require that the visitor's PHP session started the conversation.
 */

require __DIR__ . '/lib/bootstrap.php';

header('X-Robots-Tag: noindex');

$action = param('action', 20, isset($_GET['action']));
$bindToSession = ($config['bind_chat_to_session'] ?? false) === true;

/** Bind a conversation to the current visitor session. */
function bind_chat(string $id): void
{
    $chats = is_array($_SESSION['chats'] ?? null) ? $_SESSION['chats'] : [];
    if (!in_array($id, $chats, true)) {
        $chats[] = $id;
        // Keep only the most recent conversations in the session.
        $_SESSION['chats'] = array_slice($chats, -20);
    }
}

/**
 * Whether this request may read or extend the conversation. With session
 * binding enabled, a conversation is only reachable from the session that
 * created it; otherwise the unguessable id is the capability.
 */
function may_access(string $id, bool $bindToSession): bool
{
    if (!$bindToSession) {
        return true;
    }
    $chats = is_array($_SESSION['chats'] ?? null) ? $_SESSION['chats'] : [];
    return in_array($id, $chats, true);
}

/**
 * Shape messages for the browser, tagging each with its absolute index so the
 * client can keep an accurate polling cursor.
 *
 * @param list<array<string,mixed>> $messages
 * @return list<array<string,mixed>>
 */
function shape_messages(array $messages, int $offset): array
{
    $out = [];
    foreach ($messages as $i => $m) {
        $entry = [
            'index' => $offset + $i,
            'role' => (string) ($m['role'] ?? 'visitor'),
            'text' => (string) ($m['text'] ?? ''),
            'at' => (int) ($m['at'] ?? 0),
        ];

        // Attachments travel with the message; the client builds the media.php
        // URL from the conversation id plus this token.
        $file = is_array($m['file'] ?? null) ? $m['file'] : null;
        if ($file !== null) {
            $entry['file'] = [
                'token' => (string) ($file['token'] ?? ''),
                'mime' => (string) ($file['mime'] ?? ''),
                'name' => (string) ($file['name'] ?? ''),
                'size' => (int) ($file['size'] ?? 0),
            ];
        }

        $out[] = $entry;
    }
    return $out;
}

/**
 * The scripted auto-reply for a visitor message, or null when nothing matches.
 * Keys are compared against the exact (trimmed) visitor text, which is what the
 * template buttons send.
 *
 * @param array<string,mixed> $config
 */
function auto_reply_for(string $message, array $config): ?string
{
    $map = $config['auto_replies'] ?? null;
    if (!is_array($map)) {
        return null;
    }
    $needle = trim($message);
    if ($needle === '') {
        return null;
    }
    foreach ($map as $trigger => $reply) {
        if (is_string($trigger) && is_string($reply) && trim($trigger) === $needle) {
            $reply = trim($reply);
            return $reply === '' ? null : $reply;
        }
    }
    return null;
}

/**
 * Presence as the browser needs it: whether either side is typing right now, and
 * how many messages each side has read (used for the Seen marker).
 *
 * @return array{typing:array<string,bool>,read:array<string,int>}
 */
function shape_presence(Store $store, string $id): array
{
    return [
        'typing' => [
            'visitor' => $store->isTyping($id, Store::ROLE_VISITOR),
            'agent' => $store->isTyping($id, Store::ROLE_AGENT),
        ],
        'read' => $store->presence($id)['read'],
    ];
}

function shape_conversation(array $conversation): array
{
    return [
        'id' => (string) ($conversation['id'] ?? ''),
        'name' => (string) ($conversation['name'] ?? ''),
        'email' => (string) ($conversation['email'] ?? ''),
        'status' => (string) ($conversation['status'] ?? 'open'),
        'created_at' => (int) ($conversation['created_at'] ?? 0),
        'total' => count($conversation['messages'] ?? []),
    ];
}

try {
    switch ($action) {
        case 'start':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                fail('Use POST for this action.', 405);
            }

            // Light spam control: 20 new chats per IP per 15 minutes.
            $limiter = $throttle('chat_start');
            if ($limiter->retryAfter(client_ip()) > 0) {
                fail('Too many conversations started from this network. Please try again later.', 429);
            }

            // Name and email are optional: a template reply can start a
            // conversation with no visitor details at all. The console renders
            // "Anonymous visitor" when the name is empty.
            $name = param('name', 80);
            $email = param('email', 160);
            $message = param('message', (int) $config['max_message_length']);

            try {
                $upload = save_upload('file', $config);
            } catch (RuntimeException $e) {
                fail($e->getMessage());
            }

            // An attachment on its own is a valid opening message.
            if ($message === '' && $upload === null) {
                fail('Please describe how we can help.');
            }
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                fail('That email address does not look valid.');
            }

            $limiter->hit(client_ip());

            $id = $store->create($name, $email, $message, param('page', 200), $upload);
            bind_chat($id);

            // Scripted auto-reply, like the reference consultant widget.
            $autoReply = auto_reply_for($message, $config);
            if ($autoReply !== null) {
                $store->addMessage($id, Store::ROLE_AGENT, $autoReply);
            }

            $conversation = $store->get($id);
            $total = count($conversation['messages'] ?? []);
            $store->markRead($id, Store::ROLE_VISITOR, $total);

            json_out([
                'ok' => true,
                'conversation' => shape_conversation($conversation ?? []),
                'messages' => shape_messages($conversation['messages'] ?? [], 0),
                'total' => $total,
                'presence' => shape_presence($store, $id),
            ]);
            // no break (json_out exits)

        case 'send':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                fail('Use POST for this action.', 405);
            }

            $id = param('id', 64);
            if (preg_match('/^[a-f0-9]{32}$/', $id) !== 1 || !may_access($id, $bindToSession)) {
                fail('Conversation not found. Please start a new chat.', 404);
            }

            $limiter = $throttle('chat_send');
            if ($limiter->retryAfter(client_ip()) > 0) {
                fail('You are sending messages too quickly. Please wait a moment.', 429);
            }

            $message = param('message', (int) $config['max_message_length']);

            try {
                $upload = save_upload('file', $config);
            } catch (RuntimeException $e) {
                fail($e->getMessage());
            }

            if ($message === '' && $upload === null) {
                fail('Message is empty.');
            }

            $limiter->hit(client_ip());

            $existing = $store->get($id);
            $countBefore = count($existing['messages'] ?? []);

            $conversation = $store->addMessage($id, Store::ROLE_VISITOR, $message, $upload);
            if ($conversation === null) {
                fail('This conversation cannot accept more messages. Please start a new chat.', 409);
            }

            // Scripted auto-reply, like the reference consultant widget.
            $autoReply = auto_reply_for($message, $config);
            if ($autoReply !== null) {
                $conversation = $store->addMessage($id, Store::ROLE_AGENT, $autoReply) ?? $conversation;
            }

            // Sending ends their typing state, so the console drops the hint.
            $store->clearTyping($id, Store::ROLE_VISITOR);

            $total = count($conversation['messages']);
            json_out([
                'ok' => true,
                'conversation' => shape_conversation($conversation),
                // Everything the visitor has not seen yet: their message plus the
                // scripted answer when one fired.
                'messages' => shape_messages(array_slice($conversation['messages'], $countBefore), $countBefore),
                'total' => $total,
                'presence' => shape_presence($store, $id),
            ]);
            // no break (json_out exits)

        case 'poll':
            $id = param('id', 64, true);
            $after = max(0, (int) param('after', 8, true));

            if (preg_match('/^[a-f0-9]{32}$/', $id) !== 1 || !may_access($id, $bindToSession)) {
                fail('Conversation not found.', 404);
            }

            $result = $store->poll($id, $after, Store::ROLE_VISITOR);
            if ($result === null) {
                fail('Conversation not found.', 404);
            }

            // Polling means the visitor is looking at the thread.
            $total = count($result['conversation']['messages'] ?? []);
            $store->markRead($id, Store::ROLE_VISITOR, $total);

            json_out([
                'ok' => true,
                'conversation' => shape_conversation($result['conversation']),
                'messages' => shape_messages($result['messages'], $after),
                'total' => $total,
                'presence' => shape_presence($store, $id),
            ]);
            // no break (json_out exits)

        case 'typing':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                fail('Use POST for this action.', 405);
            }

            $id = param('id', 64);
            if (preg_match('/^[a-f0-9]{32}$/', $id) !== 1 || !may_access($id, $bindToSession)) {
                fail('Conversation not found.', 404);
            }

            $store->touchTyping($id, Store::ROLE_VISITOR);
            json_out(['ok' => true]);
            // no break (json_out exits)

        default:
            fail('Unknown action.', 400);
    }
} catch (Throwable $e) {
    error_log('[support-center] api.php: ' . $e->getMessage());
    fail('Something went wrong on our side. Please try again.', 500);
}
