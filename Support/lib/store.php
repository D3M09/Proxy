<?php

declare(strict_types=1);

/**
 * Flat-file conversation store.
 *
 * Every conversation lives in its own JSON file (data/chats/<id>.json) so that
 * concurrent replies on different chats never contend. All read-modify-write
 * cycles happen while holding an exclusive flock() on the file.
 *
 * No database, no extensions beyond ext-json, easy to back up by copying data/.
 */
final class Store
{
    public const ROLE_VISITOR = 'visitor';
    public const ROLE_AGENT = 'agent';

    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';

    /** Seconds a typing ping stays live after the last keystroke. */
    public const TYPING_TTL = 6;

    private string $chatDir;
    private string $uploadDir;
    private string $presenceDir;

    public function __construct(
        string $dataDir,
        private int $maxMessages = 500,
        private int $maxTextLength = 4000,
    ) {
        $dataDir = rtrim($dataDir, "/\\");
        $this->chatDir = $dataDir . DIRECTORY_SEPARATOR . 'chats';
        $this->uploadDir = $dataDir . DIRECTORY_SEPARATOR . 'uploads';
        $this->presenceDir = $dataDir . DIRECTORY_SEPARATOR . 'presence';
        $this->ensureDir($this->chatDir);
    }

    private function ensureDir(string $dir): void
    {
        if (is_dir($dir)) {
            return;
        }
        if (!@mkdir($dir, 0700, true) && !is_dir($dir)) {
            throw new RuntimeException('Unable to create data directory: ' . $dir);
        }
    }

    /** Conversation ids are opaque hex tokens, which also prevents path traversal. */
    private function path(string $id): ?string
    {
        if (preg_match('/^[a-f0-9]{32}$/', $id) !== 1) {
            return null;
        }
        return $this->chatDir . DIRECTORY_SEPARATOR . $id . '.json';
    }

    public function exists(string $id): bool
    {
        $path = $this->path($id);
        return $path !== null && is_file($path);
    }

    /**
     * @param array{token:string,mime:string,name:string,size:int}|null $file
     * @return string The new conversation id.
     */
    public function create(string $name, string $email, string $text, string $page = '', ?array $file = null): string
    {
        $id = bin2hex(random_bytes(16));
        $now = time();

        $conversation = [
            'id' => $id,
            'name' => $this->clean($name, 80),
            'email' => $this->clean($email, 160),
            'page' => $this->clean($page, 200),
            'status' => self::STATUS_OPEN,
            'created_at' => $now,
            'updated_at' => $now,
            'agent_unread' => 0,
            'visitor_unread' => 0,
            'messages' => [],
        ];

        $conversation['messages'][] = $this->message(self::ROLE_VISITOR, $text, $now, $file);
        $conversation['agent_unread'] = 1;

        $this->writeNew($id, $conversation);

        return $id;
    }

    /** @return array<string,mixed>|null */
    public function get(string $id): ?array
    {
        $path = $this->path($id);
        if ($path === null || !is_file($path)) {
            return null;
        }
        $fh = @fopen($path, 'rb');
        if ($fh === false) {
            return null;
        }
        try {
            if (!flock($fh, LOCK_SH)) {
                return null;
            }
            $conversation = json_decode((string) stream_get_contents($fh), true);
            flock($fh, LOCK_UN);
        } finally {
            fclose($fh);
        }
        return is_array($conversation) ? $conversation : null;
    }

    /**
     * Messages from index $after onwards, plus the conversation header.
     *
     * @return array{conversation:array<string,mixed>, messages:list<array<string,mixed>>}|null
     */
    public function poll(string $id, int $after = 0, ?string $reader = null): ?array
    {
        $conversation = $this->get($id);
        if ($conversation === null) {
            return null;
        }
        if ($reader !== null) {
            // Clear the unread counter for whoever is currently looking at it.
            $key = $reader === self::ROLE_AGENT ? 'agent_unread' : 'visitor_unread';
            if (($conversation[$key] ?? 0) > 0) {
                $this->mutate($id, static function (array &$c) use ($key): void {
                    $c[$key] = 0;
                });
                $conversation[$key] = 0;
            }
        }

        $messages = array_slice($conversation['messages'] ?? [], max(0, $after));

        return ['conversation' => $conversation, 'messages' => $messages];
    }

    /**
     * Append a message. Text may be empty when an attachment carries the message.
     *
     * @param array{token:string,mime:string,name:string,size:int}|null $file
     * @return array<string,mixed>|null Updated conversation, or null when missing/full/invalid.
     */
    public function addMessage(string $id, string $role, string $text, ?array $file = null): ?array
    {
        if ($role !== self::ROLE_VISITOR && $role !== self::ROLE_AGENT) {
            return null;
        }
        $text = $this->clean($text, $this->maxTextLength);
        if ($text === '' && $file === null) {
            return null;
        }

        return $this->mutate($id, function (array &$c) use ($role, $text, $file): bool {
            if (count($c['messages'] ?? []) >= $this->maxMessages) {
                return false;
            }
            $c['messages'][] = $this->message($role, $text, time(), $file);
            if ($role === self::ROLE_AGENT) {
                $c['visitor_unread'] = ($c['visitor_unread'] ?? 0) + 1;
                // An agent actively replying reopens a closed conversation.
                $c['status'] = self::STATUS_OPEN;
            } else {
                $c['agent_unread'] = ($c['agent_unread'] ?? 0) + 1;
                if (($c['status'] ?? null) === self::STATUS_CLOSED) {
                    $c['status'] = self::STATUS_OPEN;
                }
            }
            return true;
        });
    }

    public function setStatus(string $id, string $status): bool
    {
        if ($status !== self::STATUS_OPEN && $status !== self::STATUS_CLOSED) {
            return false;
        }
        return $this->mutate($id, static function (array &$c) use ($status): bool {
            $c['status'] = $status;
            return true;
        }) !== null;
    }

    public function delete(string $id): bool
    {
        $path = $this->path($id);
        if ($path === null || !is_file($path)) {
            return false;
        }

        // Take the conversation's attachments with it, so uploads/ cannot grow
        // into a pile of unreferenced files.
        $conversation = $this->get($id);
        foreach ($conversation['messages'] ?? [] as $message) {
            $token = $message['file']['token'] ?? null;
            if (is_string($token) && preg_match('/^[a-f0-9]{32}$/', $token) === 1) {
                @unlink($this->uploadDir . DIRECTORY_SEPARATOR . $token);
            }
        }

        // Drop its presence file too.
        $presencePath = $this->presencePath($id);
        if ($presencePath !== null) {
            @unlink($presencePath);
        }

        return @unlink($path);
    }

    /**
     * Locate an attachment belonging to a conversation.
     *
     * Called by media.php, so it only ever returns a file that is actually
     * referenced by that conversation — the token alone is not enough.
     *
     * @return array{path:string,mime:string,name:string,size:int}|null
     */
    public function findFile(string $id, string $token): ?array
    {
        if (preg_match('/^[a-f0-9]{32}$/', $token) !== 1) {
            return null;
        }

        $conversation = $this->get($id);
        if ($conversation === null) {
            return null;
        }

        foreach ($conversation['messages'] ?? [] as $message) {
            $file = is_array($message['file'] ?? null) ? $message['file'] : null;
            if ($file === null || (string) ($file['token'] ?? '') !== $token) {
                continue;
            }

            $path = $this->uploadDir . DIRECTORY_SEPARATOR . $token;
            if (!is_file($path)) {
                return null;
            }

            return [
                'path' => $path,
                'mime' => (string) ($file['mime'] ?? 'application/octet-stream'),
                'name' => (string) ($file['name'] ?? 'file'),
                'size' => (int) ($file['size'] ?? (filesize($path) ?: 0)),
            ];
        }

        return null;
    }

    /**
     * Conversation summaries, newest activity first.
     *
     * @return list<array<string,mixed>>
     */
    public function listConversations(int $limit = 200): array
    {
        $files = glob($this->chatDir . DIRECTORY_SEPARATOR . '*.json') ?: [];
        $rows = [];
        foreach ($files as $file) {
            $raw = @file_get_contents($file);
            if ($raw === false) {
                continue;
            }
            $c = json_decode($raw, true);
            if (!is_array($c) || !isset($c['id'])) {
                continue;
            }
            $messages = $c['messages'] ?? [];
            $last = end($messages);
            $preview = is_array($last) ? (string) ($last['text'] ?? '') : '';
            if ($preview === '' && is_array($last['file'] ?? null)) {
                // An attachment-only message still needs something in the list.
                $preview = media_is_video((string) ($last['file']['mime'] ?? '')) ? '🎬 Video' : '🖼 Photo';
            }
            $rows[] = [
                'id' => $c['id'],
                'name' => $c['name'] ?? '',
                'email' => $c['email'] ?? '',
                'status' => $c['status'] ?? self::STATUS_OPEN,
                'created_at' => (int) ($c['created_at'] ?? 0),
                'updated_at' => (int) ($c['updated_at'] ?? 0),
                'agent_unread' => (int) ($c['agent_unread'] ?? 0),
                'message_count' => count($messages),
                'preview' => $preview,
            ];
        }
        usort($rows, static fn (array $a, array $b): int => $b['updated_at'] <=> $a['updated_at']);
        return array_slice($rows, 0, $limit);
    }

    public function unreadCount(?array $rows = null): int
    {
        $rows ??= $this->listConversations();
        return array_sum(array_column($rows, 'agent_unread'));
    }

    // -----------------------------------------------------------------------
    // Presence: typing pings and read watermarks
    // -----------------------------------------------------------------------

    /**
     * Ephemeral per-conversation state, kept in its own file so that a keystroke
     * never rewrites the conversation itself — doing so would bump updated_at,
     * reorder the console list and reset every "time ago" value.
     *
     * @return array{typing:array<string,int>,read:array<string,int>}
     */
    public function presence(string $id): array
    {
        $blank = ['typing' => ['visitor' => 0, 'agent' => 0], 'read' => ['visitor' => 0, 'agent' => 0]];

        $path = $this->presencePath($id);
        if ($path === null || !is_file($path)) {
            return $blank;
        }
        $raw = @file_get_contents($path);
        if ($raw === false || $raw === '') {
            return $blank;
        }
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return $blank;
        }

        return [
            'typing' => [
                'visitor' => (int) ($data['typing']['visitor'] ?? 0),
                'agent' => (int) ($data['typing']['agent'] ?? 0),
            ],
            'read' => [
                'visitor' => (int) ($data['read']['visitor'] ?? 0),
                'agent' => (int) ($data['read']['agent'] ?? 0),
            ],
        ];
    }

    /** Whether $role is inside the typing window right now. */
    public function isTyping(string $id, string $role): bool
    {
        $presence = $this->presence($id);
        $at = (int) ($presence['typing'][$role] ?? 0);
        return $at > 0 && (time() - $at) < self::TYPING_TTL;
    }

    /** Note that $role is composing a message. */
    public function touchTyping(string $id, string $role): bool
    {
        if ($role !== self::ROLE_VISITOR && $role !== self::ROLE_AGENT) {
            return false;
        }
        return $this->writePresence($id, static function (array &$presence) use ($role): bool {
            $presence['typing'][$role] = time();
            return true;
        });
    }

    /** Stop showing $role as typing (called when their message is sent). */
    public function clearTyping(string $id, string $role): void
    {
        $this->writePresence($id, static function (array &$presence) use ($role): bool {
            if ((int) ($presence['typing'][$role] ?? 0) === 0) {
                return false;
            }
            $presence['typing'][$role] = 0;
            return true;
        });
    }

    /** Record that $role has read $count messages. Writes only when it advances. */
    public function markRead(string $id, string $role, int $count): void
    {
        $this->writePresence($id, static function (array &$presence) use ($role, $count): bool {
            if ((int) ($presence['read'][$role] ?? 0) >= $count) {
                return false;
            }
            $presence['read'][$role] = $count;
            return true;
        });
    }

    // -----------------------------------------------------------------------
    // Internals
    // -----------------------------------------------------------------------

    private function presencePath(string $id): ?string
    {
        if (preg_match('/^[a-f0-9]{32}$/', $id) !== 1) {
            return null;
        }
        return $this->presenceDir . DIRECTORY_SEPARATOR . $id . '.json';
    }

    /**
     * Read-modify-write the presence file under an exclusive lock.
     *
     * @param callable(array{typing:array<string,int>,read:array<string,int>}&):bool $mutator
     *        Return false to skip the write.
     */
    private function writePresence(string $id, callable $mutator): bool
    {
        $path = $this->presencePath($id);
        if ($path === null) {
            return false;
        }
        $this->ensureDir($this->presenceDir);

        $handle = @fopen($path, 'c+b');
        if ($handle === false) {
            return false;
        }
        try {
            if (!flock($handle, LOCK_EX)) {
                return false;
            }
            $raw = (string) stream_get_contents($handle);
            $data = json_decode($raw, true);
            $presence = [
                'typing' => [
                    'visitor' => (int) ($data['typing']['visitor'] ?? 0),
                    'agent' => (int) ($data['typing']['agent'] ?? 0),
                ],
                'read' => [
                    'visitor' => (int) ($data['read']['visitor'] ?? 0),
                    'agent' => (int) ($data['read']['agent'] ?? 0),
                ],
            ];
            if ($mutator($presence) === false) {
                return false;
            }
            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, (string) json_encode($presence));
            fflush($handle);
            return true;
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    /** @param callable(array<string,mixed>&):bool $mutator Return false to abort the write. */
    private function mutate(string $id, callable $mutator): ?array
    {
        $path = $this->path($id);
        if ($path === null) {
            return null;
        }
        $fh = @fopen($path, 'r+b');
        if ($fh === false) {
            return null;
        }
        try {
            if (!flock($fh, LOCK_EX)) {
                return null;
            }
            $conversation = json_decode((string) stream_get_contents($fh), true);
            if (!is_array($conversation)) {
                return null;
            }
            if ($mutator($conversation) === false) {
                return null;
            }
            $conversation['updated_at'] = time();
            $encoded = json_encode(
                $conversation,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
            if ($encoded === false) {
                return null;
            }
            ftruncate($fh, 0);
            rewind($fh);
            fwrite($fh, $encoded);
            fflush($fh);
            return $conversation;
        } finally {
            flock($fh, LOCK_UN);
            fclose($fh);
        }
    }

    /** @param array<string,mixed> $conversation */
    private function writeNew(string $id, array $conversation): void
    {
        $path = $this->path($id);
        if ($path === null) {
            throw new RuntimeException('Invalid conversation id.');
        }
        $json = json_encode(
            $conversation,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
        if ($json === false || @file_put_contents($path, $json, LOCK_EX) === false) {
            throw new RuntimeException('Unable to write conversation file.');
        }
        @chmod($path, 0600);
    }

    /**
     * @param array{token:string,mime:string,name:string,size:int}|null $file
     * @return array<string,mixed>
     */
    private function message(string $role, string $text, int $at, ?array $file = null): array
    {
        $message = ['role' => $role, 'text' => $text, 'at' => $at];
        if ($file !== null) {
            $message['file'] = [
                'token' => (string) $file['token'],
                'mime' => (string) $file['mime'],
                'name' => (string) $file['name'],
                'size' => (int) $file['size'],
            ];
        }
        return $message;
    }

    private function clean(string $value, int $max): string
    {
        $value = str_replace(["\0", "\r\n"], ["", "\n"], $value);
        // Collapse runs of blank lines and strip trailing whitespace per line.
        $value = preg_replace('/[ \t]+\n/', "\n", $value) ?? $value;
        $value = preg_replace('/\n{4,}/', "\n\n\n", $value) ?? $value;
        return trim(mb_substr($value, 0, $max));
    }
}
