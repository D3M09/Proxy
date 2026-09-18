<?php
/**
 * Storage + auth helpers for the admin panel.
 * JSON stores live in /data (denied from the web by data/.htaccess).
 */

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
