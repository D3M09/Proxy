<?php
/**
 * One-time admin -> player impersonation.
 *
 * GET /player-login?token=... : requires the admin session, burns the single-use
 * token (60s), then serves the app's real /m/login page with a shim that
 * exchanges a short-lived handoff id for the stored credentials exactly once and
 * drives the genuine login form. The password is never placed in this HTML, the
 * URL, or any log; the shim only fetches it after the page has loaded.
 */

require_once __DIR__ . '/admin/store.php';

$app = config_load();
$admin = is_array($app['admin'] ?? null) ? $app['admin'] : [];

function pl_base(): string
{
    $dir = str_replace(chr(92), '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php')));
    $b = rtrim($dir, '/');
    if ($b === '/') {
        $b = '';
    }
    if ($b === '/Proxy' && strpos((string) ($_SERVER['REQUEST_URI'] ?? ''), '/Proxy') !== 0) {
        $b = '';
    }
    return $b;
}

function pl_page(int $code, string $title, string $msg, string $extra = ''): void
{
    http_response_code($code);
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('X-Robots-Tag: noindex, nofollow');
    $t = htmlspecialchars($title, ENT_QUOTES);
    $m = htmlspecialchars($msg, ENT_QUOTES);
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">'
        . '<meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>' . $t . '</title><style>'
        . 'body{margin:0;background:#0b0e14;color:#e6ecf5;font:15px/1.55 system-ui,Segoe UI,Arial;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px}'
        . '.c{max-width:480px;background:#121826;border:1px solid #253046;border-radius:14px;padding:22px}'
        . 'h1{font-size:18px;margin:0 0 8px}p{margin:0 0 14px;color:#9aa6c7}'
        . 'a.btn{display:inline-block;background:#2563eb;color:#fff;text-decoration:none;padding:9px 14px;border-radius:9px;font-weight:600}'
        . '</style></head><body><div class="c"><h1>' . $t . '</h1><p>' . $m . '</p>' . $extra . '</div></body></html>';
    exit;
}

session_name((string) ($admin['cookie'] ?? 'px_sid'));
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'secure' => $secure, 'samesite' => 'Lax']);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$base = pl_base();
$adminHome = ($base === '' ? '' : $base) . '/admin';

if (empty($_SESSION['px_admin'])) {
    pl_page(403, 'Not authorised',
        'Your admin session is missing or has expired. Sign in to the panel and try again.',
        '<a class="btn" href="' . htmlspecialchars($adminHome, ENT_QUOTES) . '">Open admin panel</a>');
}

$token = (string) ($_GET['token'] ?? '');
$row = player_token_burn($token);
if (!$row) {
    pl_page(410, 'Link expired',
        'This one-time login link was already used or has expired. Mint a new one from the Players tab.',
        '<a class="btn" href="' . htmlspecialchars($adminHome . '/players', ENT_QUOTES) . '">Back to Players</a>');
}

$username = (string) ($row['username'] ?? '');
$adminUser = (string) ($_SESSION['px_user'] ?? 'admin');
$ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
admin_audit_append(['action' => 'player_login_open', 'admin' => $adminUser, 'target' => $username, 'ip' => $ip]);

// Fetch the genuine login page from upstream so the app's own crypto signs in.
$up = rtrim((string) ($app['upstream'] ?? ''), '/');
$html = false;
if ($up !== '' && function_exists('curl_init')) {
    $ch = curl_init($up . '/m/login');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_USERAGENT      => (string) ($app['user_agent'] ?? 'Mozilla/5.0'),
        CURLOPT_HTTPHEADER     => ['Accept: text/html,application/xhtml+xml'],
    ]);
    $html = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($html === false || $code >= 400 || $html === '') {
        $html = false;
    }
}
if ($html === false) {
    pl_page(502, 'Login page unavailable',
        'The site login page could not be loaded from upstream. Copy the login ID and sign in manually instead.',
        '<a class="btn" href="' . htmlspecialchars($adminHome . '/players', ENT_QUOTES) . '">Back to Players</a>');
}

// Short-lived single-use handoff: the page carries only this id, never the password.
$handoff = player_token_mint($username, $adminUser, $ip, 30);
$cfg = json_encode([
    'h'        => $handoff['token'],
    'url'      => ($base === '' ? '' : $base) . '/api/player-credential.php',
    'username' => $username,
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

$js = <<<'JS'
(function(){
var C=__CFG__;
function setVal(x,v){try{var d=Object.getOwnPropertyDescriptor(x.constructor.prototype,"value");if(d&&d.set)d.set.call(x,v);else x.value=v;x.dispatchEvent(new Event("input",{bubbles:true}));x.dispatchEvent(new Event("change",{bubbles:true}));}catch(e){try{x.value=v;}catch(e2){}}}
function findUser(){var ins=document.querySelectorAll("input");for(var i=0;i<ins.length;i++){var e=ins[i],nm=(e.getAttribute("name")||"").toLowerCase(),ty=(e.getAttribute("type")||"text").toLowerCase();if(ty==="password"||ty==="hidden")continue;if(nm==="username"||nm==="mobilenum"||nm==="mobilenum1"||nm==="mobilenum2"||nm==="loginname"||nm==="account")return e;}return null;}
function findPw(){var p=document.querySelectorAll("input[type=password]");return p.length?p[0]:null;}
function note(m){var b=document.getElementById("px-pl-banner");if(b)b.textContent=m;try{if(window.console)console.warn("[player-login] "+m);}catch(e){}}
function submitForm(){var b=document.querySelector("button[type=submit],.submit_btn,.login-btn,.form_item .submit_btn");if(b){b.click();return true;}var p=findPw();if(p&&p.form){if(p.form.requestSubmit){p.form.requestSubmit();return true;}if(p.form.submit){p.form.submit();return true;}}return false;}
var tries=0;
function boot(){
 var u=findUser(),p=findPw();
 if(!u||!p){if(++tries<80){setTimeout(boot,250);}else{note("Login form not found. Use Copy login ID in the admin panel.");}return;}
 fetch(C.url,{method:"POST",credentials:"same-origin",headers:{"Content-Type":"application/json"},body:JSON.stringify({h:C.h})})
  .then(function(r){return r.json();})
  .then(function(j){
    if(!j||!j.ok||!j.password){note((j&&j.message)||"Could not load the stored password.");return;}
    setVal(u,j.username||C.username);setVal(p,j.password);
    note("Signing in as "+(j.username||C.username)+" ...");
    setTimeout(function(){
      if(!submitForm())note("Could not submit the login form. Use Copy login ID.");
      C.h="";
      setTimeout(function(){try{p.value="";}catch(e){}},3000);
    },400);
  }).catch(function(){note("Credential request failed. Use Copy login ID.");});
}
if(document.readyState!=="loading")boot();else document.addEventListener("DOMContentLoaded",boot);
})();
JS;

$banner = '<div id="px-pl-banner" style="position:fixed;top:0;left:0;right:0;z-index:2147483647;background:#2563eb;color:#fff;'
    . 'font:600 13px system-ui,Segoe UI,Arial;padding:8px 12px;text-align:center">Signing in as '
    . htmlspecialchars($username, ENT_QUOTES) . '&hellip;</div>';
$inject = $banner . '<script>' . str_replace('__CFG__', $cfg, $js) . '</script>';

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Vary: Cookie');
header('X-Robots-Tag: noindex, nofollow');
if ($base !== '' && preg_match('/<head[^>]*>/i', $html)) {
    $html = preg_replace('/(<head[^>]*>)/i', '$1<base href="' . htmlspecialchars($base, ENT_QUOTES) . '/">', $html, 1);
}
if (stripos($html, '</body>') !== false) {
    $html = preg_replace('/<\/body>/i', $inject . '</body>', $html, 1);
} else {
    $html .= $inject;
}
echo $html;
