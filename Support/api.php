<?php

declare(strict_types=1);

/**
 * Public chat API used by widget.js.
 *
 *   POST action=start  name, email?, message, page?   -> new conversation
 *   POST action=send   id, message                    -> append visitor message
 *   GET  action=poll   id, after                      -> new messages since index
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
        $out[] = [
            'index' => $offset + $i,
            'role' => (string) ($m['role'] ?? 'visitor'),
            'text' => (string) ($m['text'] ?? ''),
            'at' => (int) ($m['at'] ?? 0),
        ];
    }
    return $out;
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

            $name = param('name', 80);
            $email = param('email', 160);
            $message = param('message', (int) $config['max_message_length']);

            if ($name === '') {
                fail('Please tell us your name.');
            }
            if ($message === '') {
                fail('Please describe how we can help.');
            }
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                fail('That email address does not look valid.');
            }

            $limiter->hit(client_ip());

            $id = $store->create($name, $email, $message, param('page', 200));
            bind_chat($id);

            $conversation = $store->get($id);
            json_out([
                'ok' => true,
                'conversation' => shape_conversation($conversation ?? []),
                'messages' => shape_messages($conversation['messages'] ?? [], 0),
                'total' => count($conversation['messages'] ?? []),
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
            if ($message === '') {
                fail('Message is empty.');
            }

            $limiter->hit(client_ip());

            $conversation = $store->addMessage($id, Store::ROLE_VISITOR, $message);
            if ($conversation === null) {
                fail('This conversation cannot accept more messages. Please start a new chat.', 409);
            }

            $total = count($conversation['messages']);
            json_out([
                'ok' => true,
                'conversation' => shape_conversation($conversation),
                'messages' => shape_messages([$conversation['messages'][$total - 1]], $total - 1),
                'total' => $total,
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

            json_out([
                'ok' => true,
                'conversation' => shape_conversation($result['conversation']),
                'messages' => shape_messages($result['messages'], $after),
                'total' => count($result['conversation']['messages'] ?? []),
            ]);
            // no break (json_out exits)

        default:
            fail('Unknown action.', 400);
    }
} catch (Throwable $e) {
    error_log('[support-center] api.php: ' . $e->getMessage());
    fail('Something went wrong on our side. Please try again.', 500);
}
