<?php

declare(strict_types=1);

/**
 * Agent roster (the people who sign in to the console).
 *
 * One JSON file (data/agents.json), the same discipline as the conversation
 * store: every read-modify-write happens while holding an exclusive flock(),
 * and passwords are bcrypt hashes. Nothing here is ever exposed to visitors.
 *
 * The roster is not a user database: it is a small, hand-managed team list, so
 * it is read whole and written whole — exactly like the rest of this project.
 */
final class Agents
{
    /** Seconds without a console visit before an agent is shown as away. */
    public const ONLINE_WINDOW = 90;

    /**
     * A real bcrypt hash of a string nobody should use as a password, verified
     * against when the handle is unknown so that "no such agent" takes the same
     * time as "wrong password" and cannot be told apart.
     */
    private const TIMING_HASH = '$2y$10$JEs6NcKVL.lvcNzSlnXK8.dminyg6GMbc/zs5VFLry30EVr2/sRNO';

    private string $file;

    public function __construct(string $dataDir)
    {
        $this->file = rtrim($dataDir, "/\\") . DIRECTORY_SEPARATOR . 'agents.json';
    }

    public function exists(): bool
    {
        return is_file($this->file);
    }

    public function path(): string
    {
        return $this->file;
    }

    /** @return list<array<string,mixed>> Online agents first, then most recent. */
    public function all(): array
    {
        $agents = $this->read();
        $now = time();
        usort($agents, static function (array $a, array $b) use ($now): int {
            $aOnline = (int) $a['last_seen'] > 0 && ($now - (int) $a['last_seen']) < self::ONLINE_WINDOW;
            $bOnline = (int) $b['last_seen'] > 0 && ($now - (int) $b['last_seen']) < self::ONLINE_WINDOW;
            if ($aOnline !== $bOnline) {
                return $aOnline ? -1 : 1;
            }
            $seen = (int) $b['last_seen'] <=> (int) $a['last_seen'];
            return $seen !== 0 ? $seen : strcmp((string) $a['name'], (string) $b['name']);
        });
        return $agents;
    }

    /** id => agent, for rendering assigned conversations. */
    public function byId(): array
    {
        $map = [];
        foreach ($this->all() as $agent) {
            $map[(string) $agent['id']] = $agent;
        }
        return $map;
    }

    /** @return array<string,mixed>|null */
    public function find(string $id): ?array
    {
        if ($id === '') {
            return null;
        }
        foreach ($this->read() as $agent) {
            if ((string) $agent['id'] === $id) {
                return $agent;
            }
        }
        return null;
    }

    /**
     * Login lookup: the handle first, then the display name, so an agent can
     * sign in with either.
     *
     * @return array<string,mixed>|null
     */
    public function findByHandle(string $handle): ?array
    {
        $needle = mb_strtolower(trim($handle));
        if ($needle === '') {
            return null;
        }
        foreach ($this->read() as $agent) {
            if ($agent['handle'] !== '' && $agent['handle'] === $needle) {
                return $agent;
            }
        }
        foreach ($this->read() as $agent) {
            if (mb_strtolower((string) $agent['name']) === $needle) {
                return $agent;
            }
        }
        return null;
    }

    /**
     * Verify a handle + password. Null for an unknown handle *or* a bad
     * password — callers must not tell the two apart.
     *
     * @return array<string,mixed>|null
     */
    public function verify(string $handle, string $password): ?array
    {
        $agent = $this->findByHandle($handle);
        if ($agent === null) {
            // Spend the same bcrypt work as a real check, so response timing
            // does not reveal whether the handle exists.
            password_verify($password, self::TIMING_HASH);
            return null;
        }
        if ($password === '') {
            return null;
        }
        $hash = (string) $agent['password_hash'];
        return $hash !== '' && password_verify($password, $hash) ? $agent : null;
    }

    /** @param array<string,mixed> $agent */
    public function isOnline(array $agent): bool
    {
        $seen = (int) ($agent['last_seen'] ?? 0);
        return $seen > 0 && (time() - $seen) < self::ONLINE_WINDOW;
    }

    /**
     * Note that an agent is using the console.
     *
     * Throttled, so the ordinary 4-second poll does not rewrite the roster
     * file on every request.
     */
    public function touch(string $id, int $minInterval = 30): void
    {
        if ($id === '') {
            return;
        }
        $this->mutate(static function (array &$state) use ($id, $minInterval): bool {
            foreach ($state['agents'] as $i => $agent) {
                if ((string) $agent['id'] !== $id) {
                    continue;
                }
                $now = time();
                if (($now - (int) $agent['last_seen']) < $minInterval) {
                    return false;
                }
                $state['agents'][$i]['last_seen'] = $now;
                return true;
            }
            return false;
        });
    }

    /**
     * @param list<string> $departments
     * @return array<string,mixed>|null The new agent, or null when the handle is taken.
     */
    public function add(string $name, string $handle, string $password, array $departments, string $photo = '', bool $demo = false): ?array
    {
        $name = trim($name);
        if ($name === '' || $password === '') {
            return null;
        }

        $id = bin2hex(random_bytes(4));
        $handle = $this->cleanHandle($handle);
        if ($handle === '') {
            $handle = 'agent' . $id;
        }

        $agent = [
            'id' => $id,
            'handle' => $handle,
            'name' => mb_substr($name, 0, 80),
            'photo' => $this->cleanPhoto($photo),
            'departments' => $this->cleanDepartments($departments),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'demo' => $demo,
            'created_at' => time(),
            'last_seen' => 0,
        ];

        $ok = $this->mutate(static function (array &$state) use ($agent): bool {
            foreach ($state['agents'] as $existing) {
                if ((string) $existing['handle'] === (string) $agent['handle']) {
                    return false;
                }
            }
            $state['agents'][] = $agent;
            return true;
        });

        return $ok ? $agent : null;
    }

    /**
     * Change an agent. Only the keys present in $fields are touched; an empty
     * password leaves the current one alone.
     *
     * @param array{name?:string,handle?:string,photo?:string,departments?:list<string>,password?:string,demo?:bool} $fields
     */
    public function update(string $id, array $fields): bool
    {
        return $this->mutate(function (array &$state) use ($id, $fields): bool {
            foreach ($state['agents'] as $i => $agent) {
                if ((string) $agent['id'] !== $id) {
                    continue;
                }

                if (isset($fields['name'])) {
                    $name = trim((string) $fields['name']);
                    if ($name === '') {
                        return false;
                    }
                    $state['agents'][$i]['name'] = mb_substr($name, 0, 80);
                }

                if (isset($fields['handle'])) {
                    $handle = $this->cleanHandle((string) $fields['handle']);
                    if ($handle !== '') {
                        foreach ($state['agents'] as $other) {
                            if ((string) $other['id'] !== $id && (string) $other['handle'] === $handle) {
                                return false;
                            }
                        }
                        $state['agents'][$i]['handle'] = $handle;
                    }
                }

                if (isset($fields['photo'])) {
                    $state['agents'][$i]['photo'] = $this->cleanPhoto((string) $fields['photo']);
                }

                if (isset($fields['departments'])) {
                    $state['agents'][$i]['departments'] = $this->cleanDepartments($fields['departments']);
                }

                if (isset($fields['password']) && (string) $fields['password'] !== '') {
                    $state['agents'][$i]['password_hash'] = password_hash((string) $fields['password'], PASSWORD_DEFAULT);
                    // Their own password is in use now, not the shared demo one.
                    $state['agents'][$i]['demo'] = false;
                }

                if (isset($fields['demo'])) {
                    $state['agents'][$i]['demo'] = (bool) $fields['demo'];
                }

                return true;
            }
            return false;
        });
    }

    public function remove(string $id): bool
    {
        if ($id === '') {
            return false;
        }
        return $this->mutate(static function (array &$state) use ($id): bool {
            foreach ($state['agents'] as $i => $agent) {
                if ((string) $agent['id'] === $id) {
                    array_splice($state['agents'], $i, 1);
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * Create the demo roster — once, on the first console visit.
     *
     * Every demo agent shares the documented password from config
     * (admin_demo_password) and carries demo=true, so the console can nag until
     * the passwords are changed.
     *
     * @param list<string> $departments
     * @return list<array<string,mixed>> The agents that were created.
     */
    public function seedDemo(array $departments, string $password): array
    {
        if ($this->exists() || $password === '') {
            return [];
        }

        $departments = $this->cleanDepartments($departments);
        if ($departments === []) {
            $departments = ['জমা', 'উত্তোলন', 'অ্যাকাউন্ট'];
        }
        $pick = static function (array $indexes) use ($departments): array {
            $out = [];
            foreach ($indexes as $index) {
                if (isset($departments[$index])) {
                    $out[] = $departments[$index];
                }
            }
            return $out;
        };

        $roster = [
            ['kabir', 'কবির হোসেন', $pick([0])],
            ['rahim', 'রহিম আহমেদ', $pick([1])],
            ['tania', 'তানিয়া আক্তার', $pick([2])],
            ['nasrin', 'নাসরিন সুলতানা', $pick([0, 1])],
            ['sakib', 'সাকিব রহমান', $pick([2, 0])],
            ['mehedi', 'মেহেদী হাসান (সুপারভাইজার)', $pick([0, 1, 2])],
        ];

        $created = [];
        foreach ($roster as [$handle, $name, $depts]) {
            $agent = $this->add((string) $name, (string) $handle, $password, $depts, '', true);
            if ($agent !== null) {
                $created[] = $agent;
            }
        }
        return $created;
    }

    // -----------------------------------------------------------------------
    // Internals
    // -----------------------------------------------------------------------

    /** @return list<array<string,mixed>> */
    private function read(): array
    {
        if (!is_file($this->file)) {
            return [];
        }
        $fh = @fopen($this->file, 'rb');
        if ($fh === false) {
            return [];
        }
        try {
            if (!flock($fh, LOCK_SH)) {
                return [];
            }
            $data = json_decode((string) stream_get_contents($fh), true);
            flock($fh, LOCK_UN);
        } finally {
            fclose($fh);
        }

        return is_array($data) ? $this->normalizeList($data['agents'] ?? null) : [];
    }

    /** @param mixed $raw @return list<array<string,mixed>> */
    private function normalizeList(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $agents = [];
        foreach ($raw as $agent) {
            if (!is_array($agent) || !isset($agent['id']) || !isset($agent['name'])) {
                continue;
            }
            $agents[] = [
                'id' => (string) $agent['id'],
                'handle' => $this->cleanHandle((string) ($agent['handle'] ?? '')),
                'name' => trim((string) $agent['name']),
                'photo' => trim((string) ($agent['photo'] ?? '')),
                'departments' => $this->cleanDepartments(is_array($agent['departments'] ?? null) ? $agent['departments'] : []),
                'password_hash' => (string) ($agent['password_hash'] ?? ''),
                'demo' => ($agent['demo'] ?? false) === true,
                'created_at' => (int) ($agent['created_at'] ?? 0),
                'last_seen' => (int) ($agent['last_seen'] ?? 0),
            ];
        }
        return $agents;
    }

    /**
     * Read-modify-write the roster under an exclusive lock.
     *
     * @param callable(array{version:int,agents:list<array<string,mixed>>}&):bool $mutator
     *        Return false to skip the write.
     */
    private function mutate(callable $mutator): bool
    {
        $dir = dirname($this->file);
        if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
            return false;
        }

        $fh = @fopen($this->file, 'c+b');
        if ($fh === false) {
            return false;
        }

        try {
            if (!flock($fh, LOCK_EX)) {
                return false;
            }
            $data = json_decode((string) stream_get_contents($fh), true);
            $state = [
                'version' => 1,
                'agents' => is_array($data) ? $this->normalizeList($data['agents'] ?? null) : [],
            ];

            if ($mutator($state) === false) {
                return false;
            }

            $json = json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if ($json === false) {
                return false;
            }
            ftruncate($fh, 0);
            rewind($fh);
            fwrite($fh, $json);
            fflush($fh);
            @chmod($this->file, 0600);
            return true;
        } finally {
            flock($fh, LOCK_UN);
            fclose($fh);
        }
    }

    /**
     * A profile picture is either an http(s) link or a path inside this
     * install, and it later lands in an <img src>, so anything else is dropped
     * here rather than stored and rendered on every page afterwards. A leading
     * "//" is kept: that is a protocol-relative URL, which the view resolves.
     */
    private function cleanPhoto(string $photo): string
    {
        $photo = str_replace(["\0", "\r", "\n", "\t"], '', trim($photo));
        $photo = mb_substr($photo, 0, 300);
        if ($photo === '') {
            return '';
        }
        if (strpbrk($photo, '<>"') !== false) {
            return '';
        }
        $hasScheme = preg_match('#^[a-z][a-z0-9+.\-]*:#i', $photo) === 1;
        if ($hasScheme && preg_match('#^(https?:|data:image/)#i', $photo) !== 1) {
            return '';
        }
        return $photo;
    }

    /** Handles are ascii login names, so they can be typed on any keyboard. */
    private function cleanHandle(string $handle): string
    {
        $handle = strtolower(trim($handle));
        $handle = preg_replace('/[^a-z0-9._-]+/', '', $handle) ?? '';
        return mb_substr($handle, 0, 40);
    }

    /**
     * @param mixed $departments
     * @return list<string>
     */
    private function cleanDepartments(mixed $departments): array
    {
        if (!is_array($departments)) {
            return [];
        }
        $out = [];
        foreach ($departments as $department) {
            if (!is_string($department)) {
                continue;
            }
            $department = trim($department);
            if ($department === '' || in_array($department, $out, true)) {
                continue;
            }
            $out[] = mb_substr($department, 0, 40);
        }
        return $out;
    }
}
