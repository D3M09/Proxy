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
security_headers("'none'");

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

        // Menu-tree follow-up buttons attached to the agent message, so the
        // widget can render them as tappable options (tree.txt flow).
        $menu = is_array($m['menu'] ?? null) ? array_values(array_filter($m['menu'], 'is_string')) : [];
        if ($menu !== []) {
            $entry['menu'] = $menu;
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
 * Tutorial images configured for a visitor message, or [] when none match.
 * Exact (trimmed) match only — same rule as the text auto-reply. Only paths
 * under assets/tutorials/ are accepted, so config cannot point elsewhere.
 *
 * @param array<string,mixed> $config
 * @return list<string>
 */
function auto_reply_media_for(string $message, array $config): array
{
    $map = $config['auto_reply_media'] ?? null;
    if (!is_array($map)) {
        return [];
    }
    $needle = trim($message);
    if ($needle === '') {
        return [];
    }
    foreach ($map as $trigger => $files) {
        if (is_string($trigger) && is_array($files) && trim($trigger) === $needle) {
            $out = [];
            foreach ($files as $rel) {
                if (is_string($rel)
                    && preg_match('#^assets/tutorials/[A-Za-z0-9_.\-]+\.(jpg|jpeg|png|gif|webp)$#', $rel) === 1
                ) {
                    $out[] = $rel;
                }
            }
            return $out;
        }
    }
    return [];
}

/**
 * Copy a tutorial image into data/uploads/ as a chat attachment record.
 *
 * Each auto-reply gets fresh random tokens (one copy per conversation), so the
 * media.php capability check — token must be referenced by that conversation —
 * keeps working exactly as it does for visitor/agent uploads.
 *
 * @param array<string,mixed> $config
 * @return array{token:string,mime:string,name:string,size:int}|null
 */
function materialize_tutorial_media(string $rel, array $config): ?array
{
    $src = __DIR__ . '/' . str_replace('/', DIRECTORY_SEPARATOR, $rel);
    $real = realpath($src);
    $base = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'tutorials');
    if ($real === false || $base === false || strpos($real, $base) !== 0 || !is_file($real)) {
        return null;
    }
    // Trust the file's real content, never the extension.
    $mime = detect_mime($real);
    $allowed = is_array($config['allowed_media'] ?? null) ? $config['allowed_media'] : [];
    if (!isset($allowed[$mime])) {
        return null;
    }
    $dir = rtrim((string) $config['data_dir'], "/\\") . DIRECTORY_SEPARATOR . 'uploads';
    if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
        return null;
    }
    $token = bin2hex(random_bytes(16));
    if (!@copy($real, $dir . DIRECTORY_SEPARATOR . $token)) {
        return null;
    }
    @chmod($dir . DIRECTORY_SEPARATOR . $token, 0600);

    return [
        'token' => $token,
        'mime' => $mime,
        'name' => basename($rel),
        'size' => (int) (filesize($real) ?: 0),
    ];
}

/** Send the tutorial images for a visitor message as agent attachment messages. */
function send_auto_reply_media(string $id, string $message, array $config, Store $store): void
{
    foreach (auto_reply_media_for($message, $config) as $rel) {
        try {
            $file = materialize_tutorial_media($rel, $config);
        } catch (Throwable $e) {
            error_log('[support-center] auto-reply media: ' . $e->getMessage());
            continue;
        }
        if ($file === null) {
            continue;
        }
        // Stop on a full/closed conversation; the text reply above is kept.
        if ($store->addMessage($id, Store::ROLE_AGENT, '', $file) === null) {
            break;
        }
    }
}

/**
 * Answer a visitor message from the menu tree (tree.txt flow).
 *
 * Posts the node text, then its tutorial images, attaching the follow-up
 * option buttons to the last message of the burst so they render once.
 * Returns true when the message matched a menu node.
 */
function send_menu_reply(string $id, string $message, array $config, Store $store): bool
{
    $node = menu_find($message);
    if ($node === null) {
        return false;
    }
    $options = menu_options_for($node);
    $menu = $options === [] ? null : $options;
    $media = menu_media_for($node);
    // Menu media uses the same assets/tutorials/ whitelist as legacy replies.
    $media = array_values(array_filter($media, static fn ($rel): bool =>
        is_string($rel) && preg_match('#^assets/tutorials/[A-Za-z0-9_.\-]+\.(jpg|jpeg|png|gif|webp)$#', $rel) === 1));

    // Materialize first: if every image fails, the options still have a home
    // on the text message instead of being lost.
    $files = [];
    foreach ($media as $rel) {
        try {
            $file = materialize_tutorial_media($rel, $config);
        } catch (Throwable $e) {
            error_log('[support-center] menu media: ' . $e->getMessage());
            continue;
        }
        if ($file !== null) {
            $files[] = $file;
        }
    }

    if ($files === []) {
        $store->addMessage($id, Store::ROLE_AGENT, menu_text_for($node), null, $menu);
        return true;
    }

    $store->addMessage($id, Store::ROLE_AGENT, menu_text_for($node));
    $last = count($files) - 1;
    foreach ($files as $i => $file) {
        if ($store->addMessage($id, Store::ROLE_AGENT, '', $file, $i === $last ? $menu : null) === null) {
            break;
        }
    }
    return true;
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

/**
 * The agent currently handling the chat, for the visitor's "Agent session"
 * bar. Only the display name, photo and online state are exposed — never
 * handles, hashes or roster internals. Null while no human has joined
 * (bot auto-replies do not count as joining).
 *
 * @param array<string,mixed>|null $conversation
 * @return array{name:string,photo:string,online:bool,since:int}|null
 */
function shape_agent(?array $conversation): ?array
{
    $assignee = (string) ($conversation['assignee'] ?? '');
    if ($assignee === '' || $conversation === null) {
        return null;
    }
    $roster = $GLOBALS['agents'] ?? null;
    if (!is_object($roster) || !method_exists($roster, 'find') || !method_exists($roster, 'isOnline')) {
        return null;
    }
    $agent = $roster->find($assignee);
    if (!is_array($agent)) {
        return null;
    }
    return [
        'name' => (string) ($agent['name'] ?? ''),
        'photo' => (string) ($agent['photo'] ?? ''),
        'online' => (bool) $roster->isOnline($agent),
        'since' => (int) ($conversation['assignee_at'] ?? 0),
    ];
}

try {
    switch ($action) {
        case 'start':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                fail('Use POST for this action.', 405);
            }

            // Light spam control on starting new chats. The key is namespaced so
            // this budget is separate from message sending: see chat_start_* in
            // config.php for the numbers.
            $limiter = $throttle('chat_start');
            if ($limiter->retryAfter('chat-start:' . client_ip()) > 0) {
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

            $limiter->hit('chat-start:' . client_ip());

            // The IP and User-Agent are recorded once, when the conversation
            // starts: the console's visitor panel shows them to the agent.
            $id = $store->create($name, $email, $message, param('page', 200), $upload, [
                'ip' => client_ip(),
                'ua' => client_user_agent(),
            ]);
            bind_chat($id);

            // Menu-tree reply (tree.txt flow) first; legacy scripted replies
            // stay as the fallback for anything outside the tree.
            if (!send_menu_reply($id, $message, $config, $store)) {
                $autoReply = auto_reply_for($message, $config);
                if ($autoReply !== null) {
                    $store->addMessage($id, Store::ROLE_AGENT, $autoReply);
                    send_auto_reply_media($id, $message, $config, $store);
                }
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
                'agent' => shape_agent($conversation),
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

            // Separate budget from chat_start, so a long conversation is not
            // capped by how many chats the visitor has opened.
            $limiter = $throttle('chat_send');
            if ($limiter->retryAfter('chat-send:' . client_ip()) > 0) {
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

            $limiter->hit('chat-send:' . client_ip());

            $existing = $store->get($id);
            $countBefore = count($existing['messages'] ?? []);

            $conversation = $store->addMessage($id, Store::ROLE_VISITOR, $message, $upload);
            if ($conversation === null) {
                fail('This conversation cannot accept more messages. Please start a new chat.', 409);
            }

            // Menu-tree reply (tree.txt flow) first; legacy scripted replies
            // stay as the fallback for anything outside the tree.
            if (!send_menu_reply($id, $message, $config, $store)) {
                $autoReply = auto_reply_for($message, $config);
                if ($autoReply !== null) {
                    $conversation = $store->addMessage($id, Store::ROLE_AGENT, $autoReply) ?? $conversation;
                    send_auto_reply_media($id, $message, $config, $store);
                    $conversation = $store->get($id) ?? $conversation;
                }
            } else {
                $conversation = $store->get($id) ?? $conversation;
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
                'agent' => shape_agent($conversation),
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
                'agent' => shape_agent($result['conversation']),
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
