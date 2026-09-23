<?php
/**
 * Admin panel: layout, sections and router.
 * Included by admin/index.php. Direct access is blocked by .htaccess.
 */

require_once __DIR__ . '/store.php';

/* --------------------------- small helpers --------------------------- */

function admin_home_url(string $base): string
{
    return ($base === '' ? '' : $base) . '/admin';
}

function admin_redirect_home(string $base): void
{
    header('Location: ' . ($base === '' ? '/' : $base . '/'), true, 302);
    exit;
}

function admin_boot(string $cookie): void
{
    session_name($cookie !== '' ? $cookie : 'px_sid');
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'secure' => $secure, 'samesite' => 'Lax']);
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Referrer-Policy: no-referrer');
}

function admin_authed(): bool
{
    return !empty($_SESSION['px_admin']);
}

function admin_key_match(array $cfg): bool
{
    $k = (string) ($_GET['key'] ?? $_POST['key'] ?? '');
    return $k !== '' && hash_equals((string) ($cfg['key'] ?? ''), $k);
}

function admin_unlocked(array $cfg): bool
{
    return admin_authed() || !empty($_SESSION['px_key']) || admin_key_match($cfg);
}

function admin_csrf(): string
{
    if (empty($_SESSION['px_csrf'])) {
        $_SESSION['px_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['px_csrf'];
}

function admin_csrf_ok(): bool
{
    return isset($_POST['csrf'], $_SESSION['px_csrf']) && hash_equals((string) $_SESSION['px_csrf'], (string) $_POST['csrf']);
}

function admin_notice_html(string $notice): string
{
    $h = '';
    if (!empty($GLOBALS['admin_error'])) {
        $h .= '<div class="alert err">' . htmlspecialchars((string) $GLOBALS['admin_error']) . '</div>';
    }
    if ($notice !== '') {
        $h .= '<div class="alert ok">' . htmlspecialchars($notice) . '</div>';
    }
    return $h;
}

function admin_dir_size(string $dir): array
{
    $count = 0;
    $bytes = 0;
    if (is_dir($dir)) {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $f) {
            if ($f->isFile()) {
                $count++;
                $bytes += $f->getSize();
            }
        }
    }
    return [$count, $bytes];
}

function admin_purge_cache(string $dir): int
{
    $n = 0;
    if (!is_dir($dir)) {
        return 0;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($it as $f) {
        if ($f->isFile()) {
            @unlink($f->getPathname());
            $n++;
        } elseif ($f->isDir()) {
            @rmdir($f->getPathname());
        }
    }
    return $n;
}

function admin_icon(string $name): string
{
    $p = [
        'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
        'titles'    => '<path d="M4 7V5h16v2"/><path d="M9 5v14"/><path d="M15 5v14"/><path d="M7 19h4"/><path d="M13 19h4"/>',
        'logo'      => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M21 17l-5-5-4 4-2-2-5 5"/>',
        'tag'       => '<path d="M20.6 13.4L11 3.8A2 2 0 009.6 3H5a2 2 0 00-2 2v4.6a2 2 0 00.6 1.4l9.6 9.6a2 2 0 002.8 0l4.6-4.6a2 2 0 000-2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
        'banners'   => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 15l4-4 4 4 3-3 4 4"/>',
        'marquee'   => '<path d="M4 8h13l-3-3"/><path d="M20 12H7l3 3"/><path d="M4 16h13"/>',
        'users'     => '<circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0112 0"/><path d="M16 11a3 3 0 100-6"/><path d="M21 20a6 6 0 00-5-5.9"/>',
        'settings'  => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.6 1.6 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.6 1.6 0 00-2.7 1.1V21a2 2 0 11-4 0v-.1A1.6 1.6 0 006.5 19l-.1.1a2 2 0 11-2.8-2.8l.1-.1A1.6 1.6 0 003 13.6H3a2 2 0 110-4h.1A1.6 1.6 0 004.5 7L4.4 7a2 2 0 112.8-2.8l.1.1A1.6 1.6 0 0010 3.1V3a2 2 0 114 0v.1a1.6 1.6 0 002.7 1.1l.1-.1a2 2 0 112.8 2.8l-.1.1a1.6 1.6 0 001.1 2.7H21a2 2 0 110 4h-.1a1.6 1.6 0 00-1.5 1z"/>',
        'tools'     => '<path d="M14.7 6.3a4 4 0 00-5.4 5.4L4 17v3h3l5.3-5.3a4 4 0 005.4-5.4l-2.6 2.6-2.1-2.1z"/>',
        'favicon'   => '<path d="M12 3l2.6 5.5 6 .8-4.4 4.2 1.1 6L12 16.8 6.7 19.5l1.1-6L3.4 9.3l6-.8z"/>',
        'voucher'   => '<path d="M3 8a2 2 0 012-2h14a2 2 0 012 2v1.5a2.5 2.5 0 000 5V16a2 2 0 01-2 2H5a2 2 0 01-2-2v-1.5a2.5 2.5 0 000-5z"/><path d="M14 6v12"/>',
        'orders'    => '<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6"/><path d="M9 16h6"/>',
        'payment_methods' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h4"/>',
        'payment_settings' => '<circle cx="12" cy="12" r="3"/><path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>',
    ];
    $d = $p[$name] ?? $p['dashboard'];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' . $d . '</svg>';
}

function admin_nav(string $base, string $active): string
{
    $groups = [
        'Overview'   => [['dashboard', 'Dashboard']],
        'Appearance' => [['titles', 'Titles'], ['logo', 'Logo'], ['favicon', 'Favicon'], ['appname', 'App name']],
        'Content'    => [['banners', 'Banners'], ['marquee', 'Marquee'], ['voucher', 'Voucher Center']],
        'Payments'   => [['payment_methods', 'Pay Methods'], ['payment_settings', 'Pay Settings']],
        'Access'     => [['users', 'Users']],
        'System'     => [['settings', 'Settings'], ['tools', 'Tools']],
    ];
    $h = admin_home_url($base);
    $out = '';
    foreach ($groups as $label => $items) {
        $keys = array_column($items, 0);
        $open = (in_array($active, $keys, true) || $label === 'Content') ? ' open' : '';
        $out .= '<details class="nav-group"' . $open . '><summary class="nav-summary">'
            . '<span>' . htmlspecialchars($label) . '</span><svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>'
            . '</summary>';
        foreach ($items as [$key, $text]) {
            $cls = $active === $key ? 'nav-item active' : 'nav-item';
            $out .= '<a class="' . $cls . '" href="' . htmlspecialchars($h . ($key === 'dashboard' ? '' : '/' . $key)) . '">'
                . admin_icon($key === 'appname' ? 'tag' : $key) . '<span>' . htmlspecialchars($text) . '</span></a>';
        }
        $out .= '</details>';
    }
    return $out;
}

function admin_layout(string $base, string $active, string $title, string $content, string $user = ''): void
{
    $brand = htmlspecialchars(defined('BRAND_TO') && BRAND_TO !== '' ? BRAND_TO : 'Proxy', ENT_QUOTES);
    $u = htmlspecialchars($user !== '' ? $user : 'admin', ENT_QUOTES);
    $home = htmlspecialchars(admin_home_url($base), ENT_QUOTES);
    $nav = admin_nav($base, $active);
    $t = htmlspecialchars($title, ENT_QUOTES);
    $cc = content_load();
    $fav = trim((string) ($cc['favicon']['url'] ?? ''));
    $logoUrl = trim((string) ($cc['logo']['url'] ?? ''));
    $logoWidth = (int) ($cc['logo']['width'] ?? 0);
    $favTag = $fav !== '' ? '<link rel="icon" href="' . htmlspecialchars($fav, ENT_QUOTES) . '">' : '';
    $logoStyle = $logoWidth > 0 ? ' style="max-width:' . $logoWidth . 'px"' : '';
    // Hide Orders tab (removed per request, keep hidden via CSS as fallback for cached old panel)
    $favTag .= '<style>.nav-item[href$="/orders"],.nav-item[href*="/orders"],a[href*="order_detail"]{display:none!important}</style>';
    $logoHtml = $logoUrl !== ''
        ? '<img src="' . htmlspecialchars($logoUrl, ENT_QUOTES) . '" alt="' . $brand . ' logo"' . $logoStyle . '>'
        : '<span class="dot"></span>';
    header('X-Proxy-Panel: new');
    echo <<<HTML
<!doctype html><html><head><meta charset="utf-8">
<meta name="robots" content="noindex,nofollow,noarchive">
<meta name="viewport" content="width=device-width,initial-scale=1">
$favTag
<title>$t · $brand Admin</title>
<style>
:root{--bg:#0b0e14;--panel:#121722;--panel2:#0f141d;--line:#1e2633;--text:#e6ebf2;--muted:#8b97a8;--acc:#3b82f6;--acc2:#22c55e;--danger:#ef4444;--warn:#f59e0b}
*{box-sizing:border-box}body{margin:0;font:14.5px/1.55 Inter,system-ui,Segoe UI,Arial;background:var(--bg);color:var(--text)}
a{color:inherit;text-decoration:none}
.app{display:flex;min-height:100vh}
.side{width:250px;flex:0 0 250px;background:var(--panel2);border-right:1px solid var(--line);padding:18px 14px;position:sticky;top:0;height:100vh;overflow:auto}
.logo{display:flex;align-items:center;gap:10px;padding:6px 8px 16px;font-weight:700;font-size:16px;border-bottom:1px solid var(--line);margin-bottom:14px}
.logo .dot{width:26px;height:26px;border-radius:8px;background:linear-gradient(135deg,#3b82f6,#22c55e)}
.logo img{max-height:32px;max-width:160px;object-fit:contain;border-radius:6px;background:#0d1219;border:1px solid #26303f;padding:3px}
.nav-group{margin:0 0 1px}
.nav-summary{display:flex;align-items:center;justify-content:space-between;cursor:pointer;list-style:none;padding:7px 10px;color:var(--muted);font-size:11px;letter-spacing:.08em;text-transform:uppercase;border-radius:8px}
.nav-summary::-webkit-details-marker{display:none}
.nav-summary:hover{color:#dbe3ee;background:#10151e}
.nav-summary .chev{width:14px;height:14px;opacity:.65;transition:transform .18s ease}
.nav-group[open] .nav-summary .chev{transform:rotate(180deg)}
.nav-item{display:flex;align-items:center;gap:10px;padding:7px 9px 7px 13px;margin:1px 0;border-left:2px solid transparent;border-radius:0 8px 8px 0;color:#a9b4c4;font-weight:500;font-size:13.5px}
.nav-item svg{width:16px;height:16px;flex:0 0 16px;opacity:.85}
.nav-item:hover{background:#10151e;color:#fff}
.nav-item.active{background:#131b29;color:#fff;border-left-color:var(--acc)}
.main{flex:1;min-width:0;display:flex;flex-direction:column}
.top{display:flex;align-items:center;justify-content:space-between;padding:16px 26px;border-bottom:1px solid var(--line);background:rgba(11,14,20,.85);backdrop-filter:blur(6px);position:sticky;top:0;z-index:5}
.top h1{font-size:18px;margin:0;font-weight:650}
.crumb{color:var(--muted);font-size:12px}
.content{padding:24px 26px;max-width:1040px}
.card{background:var(--panel);border:1px solid var(--line);border-radius:14px;padding:18px;margin-bottom:18px}
.card h3{margin:0 0 4px;font-size:15px}.card .desc{color:var(--muted);font-size:12.5px;margin-bottom:14px}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px}
.stat{background:var(--panel);border:1px solid var(--line);border-radius:14px;padding:16px}
.stat .k{color:var(--muted);font-size:12px;text-transform:uppercase;letter-spacing:.05em}
.stat .v{font-size:20px;font-weight:700;margin-top:6px;word-break:break-word}
label{display:block;font-size:12.5px;color:var(--muted);margin:12px 0 6px}
input,select,textarea{width:100%;padding:10px 12px;border-radius:9px;border:1px solid #26303f;background:#0d1219;color:var(--text);font:inherit}
textarea{min-height:80px;resize:vertical}
input:focus,select:focus,textarea:focus{outline:none;border-color:var(--acc)}
.btn{display:inline-block;margin-top:14px;padding:10px 16px;border:0;border-radius:9px;background:var(--acc);color:#fff;font-weight:600;cursor:pointer}
.btn.ghost{background:#171e2a;border:1px solid #26303f;color:var(--text)}
.btn.danger{background:var(--danger)}
.btn.sm{padding:7px 11px;margin:0;font-size:12.5px;border-radius:8px}
.row{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
table{width:100%;border-collapse:collapse}
th{color:var(--muted);font-size:11.5px;text-transform:uppercase;letter-spacing:.05em;text-align:left;padding:10px 8px;border-bottom:1px solid var(--line)}
td{padding:10px 8px;border-bottom:1px solid var(--line);vertical-align:middle;font-size:13.5px}
tr:last-child td{border-bottom:0}
.badge{display:inline-block;padding:3px 9px;border-radius:999px;font-size:11.5px;background:#1b2637;color:#c8d3e3;border:1px solid #26364f}
.badge.owner{background:#12301f;color:#9ff0bd;border-color:#1f5a3a}
.alert{padding:11px 13px;border-radius:10px;font-size:13px;margin-bottom:14px}
.alert.ok{background:#12301f;border:1px solid #1f5a3a;color:#a7f3c6}
.alert.err{background:#3a1b1f;border:1px solid #6b2a34;color:#ffb4bc}
.alert.warn{background:#33280f;border:1px solid #6b5620;color:#ffdfa0}
.muted{color:var(--muted)}code{background:#0b0f16;border:1px solid #1e2633;border-radius:6px;padding:1px 6px;font-size:12.5px}
.login{max-width:360px;margin:9vh auto;padding:26px}
.login .dot{margin:0 auto 14px}
.side{transition:width .22s ease,flex-basis .22s ease,transform .22s ease}
.side-head{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:0 0 12px;margin-bottom:10px;border-bottom:1px solid var(--line)}
.side.collapsed{width:68px;flex:0 0 68px;padding:12px 8px;overflow:hidden}
.side.collapsed .logo{justify-content:center}
.side.collapsed .logo span:last-child{display:none}
.side.collapsed .nav-summary{display:none}
.side.collapsed .nav-item{justify-content:center;padding:10px 6px;border-left-width:0;border-radius:10px;gap:0}
.side.collapsed .nav-item span{display:none}
.side.collapsed .side-head{justify-content:center;padding-bottom:12px}
.side.collapsed .side-head .logo{flex:0 0 auto}
.side-toggle{width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #26303f;background:#0d1219;color:var(--muted);border-radius:8px;cursor:pointer;flex:0 0 28px;transition:transform .2s ease,background .15s,border-color .15s}
.side-toggle:hover{color:#fff;border-color:#334055;background:#131b29}
.side.collapsed #px-side-toggle svg{transform:rotate(180deg)}
.side-toggle svg{width:16px;height:16px}
.side-toggle-top{width:36px;height:36px}
.overlay{display:none}
@media(max-width:820px){
  .app{flex-direction:column}
  .side{position:fixed;left:0;top:0;bottom:0;z-index:40;width:260px;flex:none;max-width:86vw;transform:translateX(0);box-shadow:2px 0 18px rgba(0,0,0,.35)}
  .side.collapsed{width:260px;flex:none;transform:translateX(-100%);padding:18px 14px;overflow:auto}
  .side.collapsed .nav-summary{display:flex}
  .side.collapsed .nav-item span{display:inline}
  .side.collapsed .logo span:last-child{display:inline}
  .side.collapsed .nav-summary span{display:inline}
  .side.collapsed .nav-summary .chev{display:block}
  .overlay{position:fixed;inset:0;background:rgba(0,0,0,.42);backdrop-filter:blur(1px);z-index:30;display:none}
  .overlay.show{display:block}
}
</style></head><body>
 <div class="app">
<aside class="side" id="px-side">
  <div class="side-head">
    <div class="logo" style="border:0;margin:0;padding:0;flex:1">$logoHtml<span>$brand Admin</span></div>
    <button type="button" class="side-toggle" id="px-side-toggle" aria-expanded="true" title="Collapse sidebar [ ]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/><path d="M9 18l-6-6 6-6" style="opacity:.45"/></svg></button>
  </div>
  $nav
</aside>
<div class="overlay" id="px-overlay"></div>
<div class="main">
  <div class="top">
    <div style="display:flex;align-items:center;gap:12px"><button type="button" class="side-toggle side-toggle-top" id="px-top-toggle" aria-expanded="true" title="Toggle sidebar [ ]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h18"/><path d="M3 6h18"/><path d="M3 18h18"/></svg></button><div><div class="crumb">$brand / Admin</div><h1>$t</h1></div></div>
    <div class="row"><span class="badge">$u</span><a class="btn ghost sm" href="$home/login" onclick="fetch('$home/logout',{method:'GET',credentials:'same-origin'}).finally(()=>location.href='$home/login');return false;">Logout</a></div>
  </div>
  <div class="content">$content</div>
</div>
</div><script>(function(){
  var KEY='px_sidebar_collapsed';
  var side=document.getElementById('px-side');
  var app=document.querySelector('.app');
  var overlay=document.getElementById('px-overlay');
  function isMobile(){return window.matchMedia('(max-width:820px)').matches;}
  function apply(c){
    if(!side||!app) return;
    side.classList.toggle('collapsed',c);
    app.classList.toggle('sidebar-collapsed',c);
    try{localStorage.setItem(KEY,c?'1':'0');}catch(e){}
    var t=c?'Expand sidebar [':'Collapse sidebar [';
    document.querySelectorAll('.side-toggle').forEach(function(b){b.setAttribute('aria-expanded',String(!c));b.title=t;});
    if(overlay){
      if(isMobile()){ overlay.classList.toggle('show', !c); }
      else { overlay.classList.remove('show'); }
    }
  }
  var c=false;try{c=localStorage.getItem(KEY)==='1';}catch(e){}
  // on mobile, default to collapsed (hidden) if no stored pref
  try{ if(isMobile() && localStorage.getItem(KEY)===null) c=true; }catch(e){}
  apply(c);
  window.pxToggleSide=function(){apply(!side.classList.contains('collapsed'));};
  document.addEventListener('click',function(e){
    if(e.target.closest('.side-toggle')){e.preventDefault();window.pxToggleSide();}
    if(e.target===overlay){window.pxToggleSide();}
  });
  window.addEventListener('resize',function(){ apply(side.classList.contains('collapsed')); });
  document.addEventListener('keydown',function(e){
    if(e.key==='Escape' && isMobile() && !side.classList.contains('collapsed')){ window.pxToggleSide(); return; }
    if(e.target && /^(INPUT|TEXTAREA|SELECT)$/.test(e.target.tagName)) return;
    var k=(e.key||'').toLowerCase();
    if(e.key==='[' || e.key===']' || k==='[' || k===']' || k==='b'){
      e.preventDefault();window.pxToggleSide();
    }
  });
})();</script></body></html>
HTML;
}

/* --------------------------- login --------------------------- */

function admin_render_login(string $base, string $error): void
{
    $action = htmlspecialchars(admin_home_url($base) . '/login', ENT_QUOTES);
    $csrf = htmlspecialchars(admin_csrf(), ENT_QUOTES);
    $err = $error !== '' ? '<div class="alert err">' . htmlspecialchars($error) . '</div>' : '';
    $cc = content_load();
    $logoUrl = trim((string) ($cc['logo']['url'] ?? ''));
    $logoWidth = (int) ($cc['logo']['width'] ?? 0);
    $logoStyle = $logoWidth > 0 ? ' style="max-width:' . $logoWidth . 'px"' : '';
    $logoInner = $logoUrl !== ''
        ? '<img src="' . htmlspecialchars($logoUrl, ENT_QUOTES) . '" alt="logo"' . $logoStyle . '>'
        : '<span class="dot"></span>';
    $body = '<div class="card login"><div class="logo" style="border:0;margin:0 0 12px">' . $logoInner
        . '<span>Admin</span></div>'
        . '<h3 style="margin-bottom:14px">Sign in to continue</h3>'
        . '<form method="post" action="' . $action . '">'
        . '<input type="hidden" name="csrf" value="' . $csrf . '">'
        . '<label>Username</label><input name="username" autocomplete="username" autofocus>'
        . '<label>Password</label><input type="password" name="password" autocomplete="current-password">'
        . '<button class="btn" type="submit" style="width:100%">Sign in</button>' . $err . '</form></div>';
    admin_layout($base, '', 'Sign in', $body, '');
}

/* --------------------------- sections --------------------------- */

function admin_render_dashboard(string $base, string $notice = ''): void
{
    [$files, $bytes] = admin_dir_size(CACHE_DIR);
    $users = users_load();
    $content = content_load();
    $stats = [
        'Upstream' => UPSTREAM !== '' ? UPSTREAM : '—',
        'Brand' => (BRAND_FROM !== '' ? BRAND_FROM . ' → ' : '') . BRAND_TO,
        'Cache' => number_format($files) . ' files · ' . number_format($bytes / 1048576, 2) . ' MB',
        'Admin users' => (string) count($users),
        'Banners' => (string) count($content['banners']),
        'PHP' => PHP_VERSION,
    ];
    $cards = '';
    foreach ($stats as $k => $v) {
        $cards .= '<div class="stat"><div class="k">' . htmlspecialchars($k) . '</div><div class="v">' . htmlspecialchars((string) $v) . '</div></div>';
    }
    $ok = admin_notice_html($notice);
    $body = $ok . '<div class="grid">' . $cards . '</div>'
        . '<div class="card" style="margin-top:18px"><h3>Quick actions</h3><div class="desc">Common tasks</div>'
        . '<div class="row">'
        . '<a class="btn ghost sm" href="' . htmlspecialchars(admin_home_url($base) . '/payment_methods') . '">Payment methods</a>'
        . '<a class="btn ghost sm" href="' . htmlspecialchars(admin_home_url($base) . '/banners') . '">Manage banners</a>'
        . '<a class="btn ghost sm" href="' . htmlspecialchars(admin_home_url($base) . '/voucher') . '">Voucher Center</a>'
        . '<a class="btn ghost sm" href="' . htmlspecialchars(admin_home_url($base) . '/users') . '">Add admin user</a>'
        . '<a class="btn ghost sm" href="' . htmlspecialchars(admin_home_url($base) . '/tools') . '">Purge cache</a>'
        . '</div></div>';
    admin_layout($base, 'dashboard', 'Dashboard', $body, $_SESSION['px_user'] ?? '');
}

function admin_render_banners(string $base, string $notice = ''): void
{
    $c = content_load();
    $banners = $c['banners'];
    $rows = '';
    foreach ($banners as $i => $b) {
        $rows .= '<tr>'
            . '<td><input name="b[' . $i . '][image]" value="' . htmlspecialchars((string) ($b['image'] ?? '')) . '" placeholder="https://.../banner.png"></td>'
            . '<td><input name="b[' . $i . '][link]" value="' . htmlspecialchars((string) ($b['link'] ?? '')) . '" placeholder="https://..."></td>'
            . '<td><input name="b[' . $i . '][title]" value="' . htmlspecialchars((string) ($b['title'] ?? '')) . '" placeholder="Title"></td>'
            . '<td style="text-align:center"><input type="checkbox" name="b[' . $i . '][active]" value="1" ' . (!empty($b['active']) ? 'checked' : '') . ' style="width:auto"></td>'
            . '<td style="text-align:center"><input type="checkbox" name="b[' . $i . '][remove]" value="1" style="width:auto"></td>'
            . '</tr>';
    }
    $n = count($banners);
    $rows .= '<tr><td><input name="b[' . $n . '][image]" placeholder="Add image URL"></td>'
        . '<td><input name="b[' . $n . '][link]" placeholder="Link"></td>'
        . '<td><input name="b[' . $n . '][title]" placeholder="Title"></td>'
        . '<td style="text-align:center"><input type="checkbox" name="b[' . $n . '][active]" value="1" checked style="width:auto"></td>'
        . '<td class="muted" style="text-align:center">new</td></tr>';
    $ok = admin_notice_html($notice);
    $action = htmlspecialchars(admin_home_url($base) . '/banners');
    $csrf = htmlspecialchars(admin_csrf(), ENT_QUOTES);
    $body = $ok . '<div class="card"><h3>Banner control</h3>'
        . '<div class="desc">Drives the storefront\'s own game-banner carousel (home banner group w_home). Add multiple images; each becomes a slide. Use the last row to add one, tick "remove" to delete.</div>'
        . '<form method="post" action="' . $action . '">'
        . '<input type="hidden" name="csrf" value="' . $csrf . '"><input type="hidden" name="action" value="save_banners">'
        . '<table><thead><tr><th>Image URL</th><th>Link</th><th>Title</th><th>Active</th><th>Remove</th></tr></thead><tbody>'
        . $rows . '</tbody></table>'
        . '<button class="btn" type="submit">Save banners</button></form></div>';
    admin_layout($base, 'banners', 'Banners', $body, $_SESSION['px_user'] ?? '');
}

function admin_render_marquee(string $base, string $notice = ''): void
{
    $m = content_load()['marquee'];
    $items = $m['items'];
    $rows = '';
    foreach ($items as $i => $it) {
        $rows .= '<tr>'
            . '<td><input name="m[' . $i . '][text]" value="' . htmlspecialchars((string) ($it['text'] ?? '')) . '" placeholder="Message text"></td>'
            . '<td><input name="m[' . $i . '][link]" value="' . htmlspecialchars((string) ($it['link'] ?? '')) . '" placeholder="https://... (optional)"></td>'
            . '<td style="text-align:center"><input type="checkbox" name="m[' . $i . '][remove]" value="1" style="width:auto"></td>'
            . '</tr>';
    }
    $n = count($items);
    $rows .= '<tr><td><input name="m[' . $n . '][text]" placeholder="Add message"></td>'
        . '<td><input name="m[' . $n . '][link]" placeholder="Link (optional)"></td>'
        . '<td class="muted" style="text-align:center">new</td></tr>';
    $ok = admin_notice_html($notice);
    $action = htmlspecialchars(admin_home_url($base) . '/marquee');
    $csrf = htmlspecialchars(admin_csrf(), ENT_QUOTES);
    $body = $ok . '<div class="card"><h3>Marquee control</h3>'
        . '<div class="desc">Drives the site\'s own scrolling notice bar. Add multiple messages; each scrolls in turn. Use the last row to add one, tick "remove" to delete.</div>'
        . '<form method="post" action="' . $action . '">'
        . '<input type="hidden" name="csrf" value="' . $csrf . '"><input type="hidden" name="action" value="save_marquee">'
        . '<label>Enabled</label><select name="enabled"><option value="0">Off</option><option value="1"' . (!empty($m['enabled']) ? ' selected' : '') . '>On</option></select>'
        . '<label>Messages</label>'
        . '<table><thead><tr><th>Text</th><th>Link (optional)</th><th>Remove</th></tr></thead><tbody>' . $rows . '</tbody></table>'
        . '<div class="row" style="gap:16px">'
        . '<div style="flex:1"><label>Background</label><input name="bg" value="' . htmlspecialchars((string) $m['bg']) . '"></div>'
        . '<div style="flex:1"><label>Text color</label><input name="color" value="' . htmlspecialchars((string) $m['color']) . '"></div>'
        . '<div style="flex:1"><label>Scroll speed (higher = faster)</label><input type="number" name="speed" min="20" max="800" value="' . (int) $m['speed'] . '"></div>'
        . '</div>'
        . '<button class="btn" type="submit">Save marquee</button></form></div>';
    admin_layout($base, 'marquee', 'Marquee', $body, $_SESSION['px_user'] ?? '');
}

function admin_render_voucher(string $base, string $notice = ''): void
{
    $v       = content_load()['voucher'];
    $defs    = voucher_method_defaults();
    $methods = is_array($v['methods'] ?? null) ? $v['methods'] : [];
    $amounts = is_array($v['amounts'] ?? null) ? $v['amounts'] : [];
    $ok      = admin_notice_html($notice);
    $action  = htmlspecialchars(admin_home_url($base) . '/voucher');
    $csrf    = htmlspecialchars(admin_csrf(), ENT_QUOTES);
    $e       = function ($s) {
        return htmlspecialchars((string) $s, ENT_QUOTES);
    };
    $tiny = 'data:image/gif;base64,R0lGODlhAQABAAAAACw=';

    $style = '<style>'
        . '.vc-wrap h2{margin:0;font-size:20px;font-weight:700}'
        . '.vc-sub{color:var(--muted);font-size:13px;margin:4px 0 0}'
        . '.vc-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:18px}'
        . '.vc-sec{margin-bottom:18px}'
        . '.vc-sec-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px}'
        . '.vc-sec-head h3{margin:0;display:flex;align-items:center;font-size:15px}'
        . '.vc-num{display:inline-flex;width:22px;height:22px;border-radius:6px;background:#1b2637;border:1px solid #26364f;color:#c8d3e3;align-items:center;justify-content:center;font-size:12px;font-weight:700;margin-right:9px}'
        . '.vc-two{display:grid;grid-template-columns:1fr 1fr;gap:18px}'
        . '.vc-methods{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px}'
        . '.vc-method{border:1px solid var(--line);border-radius:12px;padding:14px;background:var(--panel2)}'
        . '.vc-method .hd{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px}'
        . '.vc-fields{display:grid;grid-template-columns:1fr 1fr;gap:10px}'
        . '.vc-fields .full{grid-column:1/-1}'
        . '.vc-field label{display:block;font-size:11px;color:var(--muted);margin:0 0 5px;text-transform:uppercase;letter-spacing:.05em}'
        . '.vc-field input{width:100%}'
        . '.vc-img{display:flex;align-items:center;gap:10px;margin-top:12px}'
        . '.vc-img img{width:40px;height:40px;object-fit:contain;background:#0d1219;border:1px solid #26303f;border-radius:8px;padding:3px}'
        . '.vc-img .muted{font-size:12px}'
        . '.vc-switch{position:relative;display:inline-flex;align-items:center;gap:8px;cursor:pointer;font-size:12.5px;color:var(--muted);user-select:none}'
        . '.vc-switch input{position:absolute;opacity:0;width:0;height:0}'
        . '.vc-switch .sl{width:40px;height:22px;border-radius:999px;background:#2a3444;position:relative;transition:.15s;flex:0 0 40px}'
        . '.vc-switch .sl:before{content:"";position:absolute;top:2px;left:2px;width:18px;height:18px;border-radius:50%;background:#9aa6b6;transition:.15s}'
        . '.vc-switch input:checked+.sl{background:#22c55e}'
        . '.vc-switch input:checked+.sl:before{transform:translateX(18px);background:#fff}'
        . '.vc-ch{border:1px solid var(--line);border-radius:10px;margin-bottom:10px;background:var(--panel2);overflow:hidden}'
        . '.vc-ch>summary{list-style:none;cursor:pointer;padding:11px 13px;font-weight:600;display:flex;align-items:center;justify-content:space-between;gap:10px}'
        . '.vc-ch>summary::-webkit-details-marker{display:none}'
        . '.vc-ch[open]>summary{border-bottom:1px solid var(--line)}'
        . '.vc-ch .body{padding:12px 13px}'
        . '.vc-ch .body table{margin:0}'
        . '.vc-ch .chev{transition:transform .15s;vertical-align:middle;margin-left:6px}'
        . '.vc-ch[open] .chev{transform:rotate(180deg)}'
        . '.vc-hint{font-size:12px;color:var(--muted);margin:8px 0 0}'
        . '.vc-savebar{position:sticky;bottom:14px;display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:12px 14px;background:rgba(18,23,34,.94);border:1px solid var(--line);border-radius:12px;backdrop-filter:blur(6px)}'
        . '@media(max-width:900px){.vc-two{grid-template-columns:1fr}}'
        . '</style>';

    $header = '<div class="vc-head"><div><h2>Voucher Center</h2>'
        . '<p class="vc-sub">Control the custom page served for the voucher-center route: redirect, logo, payment methods, channels and amounts.</p></div>'
        . '<button class="btn" type="submit">Save changes</button></div>';

    $secRedirect = '<section class="card vc-sec"><div class="vc-sec-head"><h3><span class="vc-num">1</span>Redirect</h3>'
        . '<label class="vc-switch"><input type="checkbox" name="enabled" value="1" ' . (!empty($v['enabled']) ? 'checked' : '') . '><span class="sl"></span><span class="txt" data-on="Enabled" data-off="Disabled">' . (!empty($v['enabled']) ? 'Enabled' : 'Disabled') . '</span></label></div>'
        . '<div class="vc-fields">'
        . '<div class="vc-field"><label>Source path</label><input name="path" value="' . $e($v['path'] ?? '/m/voucherCenter') . '" placeholder="/m/voucherCenter"></div>'
        . '<div class="vc-field"><label>Redirect URL (custom page)</label><input name="redirect_url" value="' . $e($v['redirect_url'] ?? '/voucherCenter/') . '" placeholder="/voucherCenter/"></div>'
        . '</div><p class="vc-hint">When enabled, loading or navigating to the source path is redirected automatically to the custom page (no reload).</p></section>';

    $logo = trim((string) ($v['logo'] ?? ''));
    $secMisc = '<div class="vc-two">'
        . '<section class="card vc-sec"><div class="vc-sec-head"><h3><span class="vc-num">2</span>Logo</h3></div>'
        . '<div class="vc-field"><label>Logo URL</label><input id="vc-logo" name="logo" value="' . $e($logo) . '" placeholder="https://.../logo.png"></div>'
        . '<div class="vc-img"><img id="vc-logo-prev" src="' . ($logo !== '' ? $e($logo) : $tiny) . '" alt=""><span class="muted">Leave blank to use the global logo</span></div></section>'
        . '<section class="card vc-sec"><div class="vc-sec-head"><h3><span class="vc-num">3</span>Amount options</h3></div>'
        . '<div class="vc-field"><label>Fixed amounts</label><textarea name="amounts" rows="5" placeholder="100, 200, 300, 500, 1000, 3000, 5000, 10000, 30000">' . $e(implode(', ', array_map('intval', $amounts))) . '</textarea></div>'
        . '<p class="vc-hint">Shown as quick-pick buttons. Separate with commas or new lines.</p></section>'
        . '</div>';

    $methodPanels = '';
    foreach ($defs as $key => $def) {
        $m   = is_array($methods[$key] ?? null) ? $methods[$key] : [];
        $en  = array_key_exists('enabled', $m) ? (bool) $m['enabled'] : true;
        $img = trim((string) ($m['image'] ?? ''));
        $methodPanels .= '<div class="vc-method">'
            . '<div class="hd"><span class="badge">' . $e($key) . '</span>'
            . '<label class="vc-switch"><input type="checkbox" name="m[' . $key . '][enabled]" value="1" ' . ($en ? 'checked' : '') . '><span class="sl"></span><span class="txt" data-on="On" data-off="Off">' . ($en ? 'On' : 'Off') . '</span></label></div>'
            . '<div class="vc-fields">'
            . '<div class="vc-field full"><label>Label</label><input name="m[' . $key . '][name]" value="' . $e($m['name'] ?? $def['name']) . '"></div>'
            . '<div class="vc-field full"><label>Image URL</label><input name="m[' . $key . '][image]" value="' . $e($img) . '" placeholder="Leave blank for built-in"></div>'
            . '<div class="vc-field"><label>Min</label><input type="number" name="m[' . $key . '][min]" value="' . (int) ($m['min'] ?? $def['min']) . '"></div>'
            . '<div class="vc-field"><label>Max</label><input type="number" name="m[' . $key . '][max]" value="' . (int) ($m['max'] ?? $def['max']) . '"></div>'
            . '</div>'
            . '<div class="vc-img"><img src="' . ($img !== '' ? $e($img) : $tiny) . '" alt=""><span class="muted">' . ($img !== '' ? 'Custom image' : 'Built-in image') . '</span></div>'
            . '</div>';
    }
    $secMethods = '<section class="card vc-sec"><div class="vc-sec-head"><h3><span class="vc-num">4</span>Payment methods</h3>'
        . '<span class="muted">' . count($defs) . ' methods</span></div><div class="vc-methods">' . $methodPanels . '</div></section>';

    $chDetails = '';
    foreach ($defs as $key => $def) {
        $m  = is_array($methods[$key] ?? null) ? $methods[$key] : [];
        $cs = is_array($m['channels'] ?? null) ? $m['channels'] : voucher_channel_defaults();
        $rws = '';
        foreach ($cs as $i => $ch) {
            $cen = !array_key_exists('enabled', $ch) || (bool) $ch['enabled'];
            $rws .= '<tr><td><input name="mc[' . $key . '][' . $i . '][label]" value="' . $e($ch['label'] ?? '') . '"></td>'
                . '<td style="text-align:center"><label class="vc-switch"><input type="checkbox" name="mc[' . $key . '][' . $i . '][enabled]" value="1" ' . ($cen ? 'checked' : '') . '><span class="sl"></span></label></td>'
                . '<td style="text-align:center"><input type="checkbox" name="mc[' . $key . '][' . $i . '][remove]" value="1" style="width:auto" title="Remove channel"></td></tr>';
        }
        $n = count($cs);
        $rws .= '<tr><td><input name="mc[' . $key . '][' . $n . '][label]" placeholder="Add channel..."></td>'
            . '<td style="text-align:center"><label class="vc-switch"><input type="checkbox" name="mc[' . $key . '][' . $n . '][enabled]" value="1" checked><span class="sl"></span></label></td>'
            . '<td class="muted" style="text-align:center">new</td></tr>';
        $chDetails .= '<details class="vc-ch"><summary>'
            . '<span><span class="badge">' . $e($key) . '</span> &nbsp;' . $e($m['name'] ?? $def['name']) . '</span>'
            . '<span class="muted">' . count($cs) . ' channels'
            . '<svg class="chev" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg></span>'
            . '</summary><div class="body"><table><thead><tr><th>Label</th><th style="width:90px;text-align:center">Enabled</th><th style="width:90px;text-align:center">Remove</th></tr></thead><tbody>'
            . $rws . '</tbody></table></div></details>';
    }
    $secChannels = '<section class="card vc-sec"><div class="vc-sec-head"><h3><span class="vc-num">5</span>Payment channels</h3>'
        . '<span class="muted">Each method has its own list</span></div>' . $chDetails . '</section>';

    $savebar = '<div class="vc-savebar"><span class="muted" style="margin-right:auto">Changes apply to the voucher page immediately.</span>'
        . '<button class="btn" type="submit">Save changes</button></div>';

    $preview = '<script>(function(){var T="' . $tiny . '";'
        . 'document.querySelectorAll(".vc-method").forEach(function(w){'
        . 'var i=w.querySelector(\'input[name$="[image]"]\'),g=w.querySelector(".vc-img img");'
        . 'if(i&&g){i.addEventListener("input",function(){g.src=i.value||T;});}});'
        . 'var li=document.getElementById("vc-logo"),lg=document.getElementById("vc-logo-prev");'
        . 'if(li&&lg){li.addEventListener("input",function(){lg.src=li.value||T;});}'
        . 'document.querySelectorAll(".vc-switch input").forEach(function(cb){'
        . 'cb.addEventListener("change",function(){var t=cb.parentElement.querySelector(".txt");'
        . 'if(t)t.textContent=cb.checked?(t.getAttribute("data-on")||"On"):(t.getAttribute("data-off")||"Off");});});'
        . '})();</script>';

    $body = $ok . $style . '<div class="vc-wrap"><form method="post" action="' . $action . '">'
        . '<input type="hidden" name="csrf" value="' . $csrf . '"><input type="hidden" name="action" value="save_voucher">'
        . $header . $secRedirect . $secMisc . $secMethods . $secChannels . $savebar
        . '</form></div>' . $preview;

    admin_layout($base, 'voucher', 'Voucher Center', $body, $_SESSION['px_user'] ?? '');
}

function admin_render_titles(string $base, string $notice = ''): void
{
    $t = content_load()['titles'];
    $ok = admin_notice_html($notice);
    $action = htmlspecialchars(admin_home_url($base) . '/titles');
    $csrf = htmlspecialchars(admin_csrf(), ENT_QUOTES);
    $body = $ok . '<div class="card"><h3>Titles</h3>'
        . '<div class="desc">Browser tab title for web and the title used by the mobile app.</div>'
        . '<form method="post" action="' . $action . '">'
        . '<input type="hidden" name="csrf" value="' . $csrf . '"><input type="hidden" name="action" value="save_titles">'
        . '<label>Web app title (browser tab)</label><input name="web_title" value="' . htmlspecialchars((string) $t['web_title']) . '" placeholder="My Brand">'
        . '<label>Mobile app title</label><input name="mobile_title" value="' . htmlspecialchars((string) $t['mobile_title']) . '" placeholder="My Brand App">'
        . '<button class="btn" type="submit">Save titles</button></form></div>';
    admin_layout($base, 'titles', 'Titles', $body, $_SESSION['px_user'] ?? '');
}

function admin_render_logo(string $base, string $notice = ''): void
{
    $l = content_load()['logo'];
    $ok = admin_notice_html($notice);
    $action = htmlspecialchars(admin_home_url($base) . '/logo');
    $csrf = htmlspecialchars(admin_csrf(), ENT_QUOTES);
    $preview = $l['url'] !== '' ? '<img src="' . htmlspecialchars($l['url']) . '" alt="logo" style="max-height:52px;margin-top:12px;background:#0d1219;border:1px solid #26303f;border-radius:8px;padding:6px">' : '';
    $body = $ok . '<div class="card"><h3>Logo control</h3>'
        . '<div class="desc">Replaces the storefront logo image. Leave blank to keep the original.</div>'
        . '<form method="post" action="' . $action . '">'
        . '<input type="hidden" name="csrf" value="' . $csrf . '"><input type="hidden" name="action" value="save_logo">'
        . '<label>Logo image URL</label><input name="url" value="' . htmlspecialchars((string) $l['url']) . '" placeholder="https://.../logo.png">'
        . '<label>Max width (px, optional)</label><input type="number" name="width" min="0" max="2000" value="' . (int) $l['width'] . '">'
        . '<button class="btn" type="submit">Save logo</button></form>' . $preview . '</div>';
    admin_layout($base, 'logo', 'Logo', $body, $_SESSION['px_user'] ?? '');
}

function admin_render_favicon(string $base, string $notice = ''): void
{
    $f = content_load()['favicon'];
    $ok = admin_notice_html($notice);
    $action = htmlspecialchars(admin_home_url($base) . '/favicon');
    $csrf = htmlspecialchars(admin_csrf(), ENT_QUOTES);
    $preview = $f['url'] !== ''
        ? '<div class="row" style="margin-top:12px"><img src="' . htmlspecialchars($f['url']) . '" alt="favicon" style="width:32px;height:32px;background:#0d1219;border:1px solid #26303f;border-radius:8px;padding:4px"><span class="muted">Preview</span></div>'
        : '';
    $body = $ok . '<div class="card"><h3>Favicon control</h3>'
        . '<div class="desc">Sets the browser tab icon. Use a .png/.ico/.svg URL. Leave blank to keep the original.</div>'
        . '<form method="post" action="' . $action . '">'
        . '<input type="hidden" name="csrf" value="' . $csrf . '"><input type="hidden" name="action" value="save_favicon">'
        . '<label>Favicon URL</label><input name="url" value="' . htmlspecialchars((string) $f['url']) . '" placeholder="https://.../favicon.png">'
        . '<button class="btn" type="submit">Save favicon</button></form>' . $preview . '</div>';
    admin_layout($base, 'favicon', 'Favicon', $body, $_SESSION['px_user'] ?? '');
}

function admin_render_appname(string $base, string $notice = ''): void
{
    $t = content_load()['titles'];
    $ok = admin_notice_html($notice);
    $action = htmlspecialchars(admin_home_url($base) . '/appname');
    $csrf = htmlspecialchars(admin_csrf(), ENT_QUOTES);
    $body = $ok . '<div class="card"><h3>App name control</h3>'
        . '<div class="desc">Name used by the installable app (PWA manifest) and app headers.</div>'
        . '<form method="post" action="' . $action . '">'
        . '<input type="hidden" name="csrf" value="' . $csrf . '"><input type="hidden" name="action" value="save_appname">'
        . '<label>App name</label><input name="app_name" value="' . htmlspecialchars((string) $t['app_name']) . '" placeholder="My Brand">'
        . '<button class="btn" type="submit">Save app name</button></form></div>';
    admin_layout($base, 'appname', 'App name', $body, $_SESSION['px_user'] ?? '');
}

function admin_render_users(string $base, string $notice = ''): void
{
    $users = users_load();
    $me = (string) ($_SESSION['px_user'] ?? '');
    $rows = '';
    foreach ($users as $u) {
        $name = (string) ($u['username'] ?? '');
        $role = (string) ($u['role'] ?? 'admin');
        $badge = $role === 'owner' ? '<span class="badge owner">owner</span>' : '<span class="badge">' . htmlspecialchars($role) . '</span>';
        $del = $role === 'owner' || $name === $me
            ? '<span class="muted">—</span>'
            : '<form method="post" action="' . htmlspecialchars(admin_home_url($base) . '/users') . '" onsubmit="return confirm(\'Delete this user?\')">'
                . '<input type="hidden" name="csrf" value="' . htmlspecialchars(admin_csrf(), ENT_QUOTES) . '">'
                . '<input type="hidden" name="action" value="delete_user"><input type="hidden" name="id" value="' . (int) $u['id'] . '">'
                . '<button class="btn danger sm" type="submit">Delete</button></form>';
        $rows .= '<tr><td>' . htmlspecialchars($name) . '</td><td>' . $badge . '</td>'
            . '<td class="muted">' . htmlspecialchars((string) ($u['created'] ?? '')) . '</td><td>' . $del . '</td></tr>';
    }
    $ok = admin_notice_html($notice);
    $action = htmlspecialchars(admin_home_url($base) . '/users');
    $csrf = htmlspecialchars(admin_csrf(), ENT_QUOTES);
    $body = $ok
        . '<div class="card"><h3>Admin users</h3><div class="desc">Accounts that can sign in to this panel.</div>'
        . '<table><thead><tr><th>Username</th><th>Role</th><th>Created</th><th></th></tr></thead><tbody>' . $rows . '</tbody></table></div>'
        . '<div class="card"><h3>Add user</h3><div class="desc">Create another admin account.</div>'
        . '<form method="post" action="' . $action . '">'
        . '<input type="hidden" name="csrf" value="' . $csrf . '"><input type="hidden" name="action" value="add_user">'
        . '<div class="row" style="gap:16px;align-items:flex-end">'
        . '<div style="flex:1"><label>Username</label><input name="username" required></div>'
        . '<div style="flex:1"><label>Password</label><input type="password" name="password" required></div>'
        . '<div style="width:150px"><label>Role</label><select name="role"><option value="admin">admin</option><option value="editor">editor</option></select></div>'
        . '</div><button class="btn" type="submit">Add user</button></form></div>'
        . '<div class="card"><h3>Reset a password</h3>'
        . '<form method="post" action="' . $action . '">'
        . '<input type="hidden" name="csrf" value="' . $csrf . '"><input type="hidden" name="action" value="reset_password">'
        . '<div class="row" style="gap:16px;align-items:flex-end">'
        . '<div style="flex:1"><label>Username</label><input name="username" required></div>'
        . '<div style="flex:1"><label>New password</label><input type="password" name="password" required></div>'
        . '</div><button class="btn ghost" type="submit">Update password</button></form></div>';
    admin_layout($base, 'users', 'Users', $body, $me);
}

function admin_render_settings(string $base, string $notice = ''): void
{
    $app = config_load();
    $admin = $app['admin'] ?? [];
    $ok = admin_notice_html($notice);
    $action = htmlspecialchars(admin_home_url($base) . '/settings');
    $csrf = htmlspecialchars(admin_csrf(), ENT_QUOTES);
    $body = $ok . '<div class="card"><h3>Proxy settings</h3>'
        . '<div class="desc">Core proxy behaviour. Saving writes config.php.</div>'
        . '<form method="post" action="' . $action . '">'
        . '<input type="hidden" name="csrf" value="' . $csrf . '"><input type="hidden" name="action" value="save_settings">'
        . '<label>Upstream URL</label><input name="upstream" value="' . htmlspecialchars((string) ($app['upstream'] ?? '')) . '">'
        . '<div class="row" style="gap:16px">'
        . '<div style="flex:1"><label>Brand from</label><input name="brand_from" value="' . htmlspecialchars((string) ($app['brand_from'] ?? '')) . '"></div>'
        . '<div style="flex:1"><label>Brand to</label><input name="brand_to" value="' . htmlspecialchars((string) ($app['brand_to'] ?? '')) . '"></div>'
        . '</div>'
        . '<label>User agent</label><input name="user_agent" value="' . htmlspecialchars((string) ($app['user_agent'] ?? '')) . '">'
        . '<div class="row" style="gap:16px">'
        . '<div style="flex:1"><label>Cache TTL (seconds)</label><input type="number" name="cache_ttl" min="0" value="' . (int) ($app['cache_ttl'] ?? 3600) . '"></div>'
        . '<div style="flex:1"><label>Admin key (URL secret)</label><input name="admin_key" value="' . htmlspecialchars((string) ($admin['key'] ?? '')) . '"></div>'
        . '<div style="flex:1"><label>Session cookie</label><input name="cookie" value="' . htmlspecialchars((string) ($admin['cookie'] ?? 'px_sid')) . '"></div>'
        . '</div><button class="btn" type="submit">Save settings</button></form></div>';
    admin_layout($base, 'settings', 'Settings', $body, $_SESSION['px_user'] ?? '');
}

function admin_render_tools(string $base, string $notice = ''): void
{
    [$files, $bytes] = admin_dir_size(CACHE_DIR);
    $ok = admin_notice_html($notice);
    $action = htmlspecialchars(admin_home_url($base) . '/tools');
    $csrf = htmlspecialchars(admin_csrf(), ENT_QUOTES);
    $body = $ok . '<div class="card"><h3>Cache</h3>'
        . '<div class="desc">' . number_format($files) . ' cached files · ' . number_format($bytes / 1048576, 2) . ' MB</div>'
        . '<form method="post" action="' . $action . '">'
        . '<input type="hidden" name="csrf" value="' . $csrf . '"><input type="hidden" name="action" value="purge">'
        . '<button class="btn danger" type="submit">Purge cache</button></form></div>';
    admin_layout($base, 'tools', 'Tools', $body, $_SESSION['px_user'] ?? '');
}

/* -------------------- payments: payment methods -------------------- */

function admin_render_payment_methods(string $base, string $notice = ''): void
{
    $ok = admin_notice_html($notice);
    $pmData = payment_methods_data_read();
    $methods = $pmData['methods'] ?? [];
    $action = htmlspecialchars(admin_home_url($base) . '/payment_methods');
    $csrf = htmlspecialchars(admin_csrf(), ENT_QUOTES);
    $e = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES); };
    $tiny = 'data:image/gif;base64,R0lGODlhAQABAAAAACw=';

    $panels = '';
    foreach ($methods as $key => $m) {
        $name = $m['name'] ?? $key;
        $enabled = !empty($m['enabled']);
        $color = $m['color'] ?? '';
        $accounts = $m['accounts'] ?? [];

        $accRows = '';
        $ai = 0;
        foreach ($accounts as $acc) {
            $accNum = $acc['number'] ?? '';
            $accName = $acc['name'] ?? '';
            $accEnabled = !empty($acc['enabled']);
            $channels = $acc['channels'] ?? [];

            $chRows = '';
            $ci = 0;
            foreach ($channels as $ch) {
                $chName = $ch['name'] ?? '';
                $chEnabled = !empty($ch['enabled']);
                $chMin = $ch['min'] ?? 100;
                $chMax = $ch['max'] ?? 30000;
                $chRows .= '<tr>'
                    . '<td><input name="m[' . $key . '][accounts][' . $ai . '][channels][' . $ci . '][name]" value="' . $e($chName) . '"></td>'
                    . '<td style="text-align:center"><label class="vc-switch"><input type="checkbox" name="m[' . $key . '][accounts][' . $ai . '][channels][' . $ci . '][enabled]" value="1" ' . ($chEnabled ? 'checked' : '') . '><span class="sl"></span></label></td>'
                    . '<td><input type="number" name="m[' . $key . '][accounts][' . $ai . '][channels][' . $ci . '][min]" value="' . (int) $chMin . '" style="width:80px"></td>'
                    . '<td><input type="number" name="m[' . $key . '][accounts][' . $ai . '][channels][' . $ci . '][max]" value="' . (int) $chMax . '" style="width:80px"></td>'
                    . '<td style="text-align:center"><input type="checkbox" name="m[' . $key . '][accounts][' . $ai . '][channels][' . $ci . '][remove]" value="1" style="width:auto" title="Remove"></td>'
                    . '</tr>';
                $ci++;
            }
            $nCh = count($channels);
            $chRows .= '<tr>'
                . '<td><input name="m[' . $key . '][accounts][' . $ai . '][channels][' . $nCh . '][name]" placeholder="Add channel..."></td>'
                . '<td style="text-align:center"><label class="vc-switch"><input type="checkbox" name="m[' . $key . '][accounts][' . $ai . '][channels][' . $nCh . '][enabled]" value="1" checked><span class="sl"></span></label></td>'
                . '<td><input type="number" name="m[' . $key . '][accounts][' . $ai . '][channels][' . $nCh . '][min]" value="100" style="width:80px"></td>'
                . '<td><input type="number" name="m[' . $key . '][accounts][' . $ai . '][channels][' . $nCh . '][max]" value="30000" style="width:80px"></td>'
                . '<td class="muted" style="text-align:center">new</td>'
                . '</tr>';

            $accRows .= '<details class="vc-ch" open><summary>'
                . '<span>' . $e($accNum) . ' — ' . $e($accName) . '</span>'
                . '<span class="muted">' . count($channels) . ' channels <svg class="chev" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg></span>'
                . '</summary><div class="body">'
                . '<div class="vc-fields">'
                . '<div class="vc-field"><label>Account Number</label><input name="m[' . $key . '][accounts][' . $ai . '][number]" value="' . $e($accNum) . '" placeholder="01XXXXXXXXX"></div>'
                . '<div class="vc-field"><label>Account Name</label><input name="m[' . $key . '][accounts][' . $ai . '][name]" value="' . $e($accName) . '" placeholder="Account holder name"></div>'
                . '</div>'
                . '<div class="row" style="gap:12px;margin:10px 0 12px">'
                . '<label class="vc-switch"><input type="checkbox" name="m[' . $key . '][accounts][' . $ai . '][enabled]" value="1" ' . ($accEnabled ? 'checked' : '') . '><span class="sl"></span><span class="txt" data-on="Enabled" data-off="Disabled">' . ($accEnabled ? 'Enabled' : 'Disabled') . '</span></label>'
                . '<label style="margin-left:auto;font-size:12.5px;color:var(--muted)"><input type="checkbox" name="m[' . $key . '][accounts][' . $ai . '][remove]" value="1" style="width:auto"> Remove account</label>'
                . '</div>'
                . '<table><thead><tr><th>Channel Name</th><th style="width:80px;text-align:center">On</th><th style="width:80px">Min</th><th style="width:80px">Max</th><th style="width:70px;text-align:center">Del</th></tr></thead><tbody>'
                . $chRows . '</tbody></table></div></details>';
            $ai++;
        }

        $nAcc = count($accounts);
        $accRows .= '<div style="margin-top:10px"><details class="vc-ch"><summary><span class="muted">+ Add account</span></summary><div class="body">'
            . '<div class="vc-fields">'
            . '<div class="vc-field"><label>Account Number</label><input name="m[' . $key . '][accounts][' . $nAcc . '][number]" placeholder="01XXXXXXXXX"></div>'
            . '<div class="vc-field"><label>Account Name</label><input name="m[' . $key . '][accounts][' . $nAcc . '][name]" placeholder="Account holder name"></div>'
            . '</div>'
            . '<label class="vc-switch" style="margin-top:10px"><input type="checkbox" name="m[' . $key . '][accounts][' . $nAcc . '][enabled]" value="1" checked><span class="sl"></span><span>Enabled</span></label>'
            . '</div></details></div>';

        $panels .= '<div class="card vc-sec">'
            . '<div class="vc-sec-head"><h3><span class="badge">' . $e($key) . '</span> &nbsp;' . $e($name) . '</h3>'
            . '<label class="vc-switch"><input type="checkbox" name="m[' . $key . '][enabled]" value="1" ' . ($enabled ? 'checked' : '') . '><span class="sl"></span><span class="txt" data-on="On" data-off="Off">' . ($enabled ? 'On' : 'Off') . '</span></label></div>'
            . '<div class="vc-fields" style="margin-bottom:14px">'
            . '<div class="vc-field"><label>Display Name</label><input name="m[' . $key . '][name]" value="' . $e($name) . '"></div>'
            . '<div class="vc-field"><label>Color</label><input name="m[' . $key . '][color]" value="' . $e($color) . '" placeholder="#E2136E" style="width:120px"></div>'
            . '</div>'
            . '<h4 style="margin:0 0 10px;font-size:13px;color:var(--muted)">Accounts &amp; Channels</h4>'
            . $accRows
            . '</div>';
    }

    $style = '<style>'
        . '.vc-fields{display:grid;grid-template-columns:1fr 1fr;gap:10px}'
        . '.vc-field label{display:block;font-size:11px;color:var(--muted);margin:0 0 5px;text-transform:uppercase;letter-spacing:.05em}'
        . '.vc-field input{width:100%}'
        . '.vc-sec{margin-bottom:18px}'
        . '.vc-sec-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px}'
        . '.vc-sec-head h3{margin:0;display:flex;align-items:center;font-size:15px}'
        . '.vc-ch{border:1px solid var(--line);border-radius:10px;margin-bottom:10px;background:var(--panel2);overflow:hidden}'
        . '.vc-ch>summary{list-style:none;cursor:pointer;padding:11px 13px;font-weight:600;display:flex;align-items:center;justify-content:space-between;gap:10px}'
        . '.vc-ch>summary::-webkit-details-marker{display:none}'
        . '.vc-ch[open]>summary{border-bottom:1px solid var(--line)}'
        . '.vc-ch .body{padding:12px 13px}'
        . '.vc-ch .body table{margin:0}'
        . '.vc-ch .chev{transition:transform .15s;vertical-align:middle;margin-left:6px}'
        . '.vc-ch[open] .chev{transform:rotate(180deg)}'
        . '.vc-switch{position:relative;display:inline-flex;align-items:center;gap:8px;cursor:pointer;font-size:12.5px;color:var(--muted);user-select:none}'
        . '.vc-switch input{position:absolute;opacity:0;width:0;height:0}'
        . '.vc-switch .sl{width:40px;height:22px;border-radius:999px;background:#2a3444;position:relative;transition:.15s;flex:0 0 40px}'
        . '.vc-switch .sl:before{content:"";position:absolute;top:2px;left:2px;width:18px;height:18px;border-radius:50%;background:#9aa6b6;transition:.15s}'
        . '.vc-switch input:checked+.sl{background:#22c55e}'
        . '.vc-switch input:checked+.sl:before{transform:translateX(18px);background:#fff}'
        . '.vc-savebar{position:sticky;bottom:14px;display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:12px 14px;background:rgba(18,23,34,.94);border:1px solid var(--line);border-radius:12px;backdrop-filter:blur(6px)}'
        . '</style>';

    $preview = '<script>(function(){'
        . 'document.querySelectorAll(".vc-switch input").forEach(function(cb){'
        . 'cb.addEventListener("change",function(){var t=cb.parentElement.querySelector(".txt");'
        . 'if(t)t.textContent=cb.checked?(t.getAttribute("data-on")||"On"):(t.getAttribute("data-off")||"Off");});});'
        . '})();</script>';

    $body = $ok . $style
        . '<div class="card"><h3>Payment Methods</h3><div class="desc">Manage payment accounts, channels, and min/max amounts per channel. Changes here affect the order creation API and payment page.</div></div>'
        . '<form method="post" action="' . $action . '">'
        . '<input type="hidden" name="csrf" value="' . $csrf . '"><input type="hidden" name="action" value="save_payment_methods">'
        . $panels
        . '<div class="vc-savebar"><span class="muted" style="margin-right:auto">Changes apply immediately to the payment system.</span>'
        . '<button class="btn" type="submit">Save payment methods</button></div>'
        . '</form>' . $preview;
    admin_layout($base, 'payment_methods', 'Payment Methods', $body, $_SESSION['px_user'] ?? '');
}

/* -------------------- payments: settings -------------------- */

function admin_render_payment_settings(string $base, string $notice = ''): void
{
    $ok = admin_notice_html($notice);
    $settings = payment_settings_read();
    $action = htmlspecialchars(admin_home_url($base) . '/payment_settings');
    $csrf = htmlspecialchars(admin_csrf(), ENT_QUOTES);
    $e = function ($s) { return htmlspecialchars((string) $s, ENT_QUOTES); };

    $body = $ok . '<div class="card"><h3>Payment Settings</h3>'
        . '<div class="desc">Platform name, currency, and other settings used by the payment page and API.</div>'
        . '<form method="post" action="' . $action . '">'
        . '<input type="hidden" name="csrf" value="' . $csrf . '"><input type="hidden" name="action" value="save_payment_settings">'
        . '<div class="row" style="gap:16px">'
        . '<div style="flex:1"><label>Platform Name</label><input name="platformName" value="' . $e($settings['platformName'] ?? 'VoucherCenter') . '"></div>'
        . '<div style="flex:1"><label>Brand Name</label><input name="brandName" value="' . $e($settings['brandName'] ?? '') . '"></div>'
        . '</div>'
        . '<div class="row" style="gap:16px">'
        . '<div style="flex:1"><label>Currency</label><input name="currency" value="' . $e($settings['currency'] ?? 'BDT') . '"></div>'
        . '<div style="flex:1"><label>Currency Symbol</label><input name="currencySymbol" value="' . $e($settings['currencySymbol'] ?? '') . '"></div>'
        . '<div style="flex:1"><label>Time Zone (UTC offset)</label><input type="number" name="timeZone" value="' . (int) ($settings['timeZone'] ?? 6) . '"></div>'
        . '</div>'
        . '<div class="row" style="gap:16px">'
        . '<div style="flex:1"><label>Language</label><select name="language"><option value="bn"' . (($settings['language'] ?? '') === 'bn' ? ' selected' : '') . '>Bengali</option><option value="en"' . (($settings['language'] ?? '') === 'en' ? ' selected' : '') . '>English</option></select></div>'
        . '<div style="flex:1"><label>Test Mode</label><select name="isTest"><option value="0"' . (empty($settings['isTest']) ? ' selected' : '') . '>Off (Live)</option><option value="1"' . (!empty($settings['isTest']) ? ' selected' : '') . '>On (Test)</option></select></div>'
        . '</div>'
        . '<button class="btn" type="submit">Save payment settings</button></form></div>';
    admin_layout($base, 'payment_settings', 'Payment Settings', $body, $_SESSION['px_user'] ?? '');
}

/* --------------------------- router --------------------------- */

function handleAdmin(string $base, string $sub, array $cfg): void
{
    admin_boot((string) ($cfg['cookie'] ?? 'px_sid'));
    users_seed($cfg);

    if (admin_key_match($cfg)) {
        $_SESSION['px_key'] = true;
    }
    $sub = '/' . trim($sub, '/');
    $home = admin_home_url($base);
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    $notice = '';

    if ($sub === '/logout') {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        header('Location: ' . $home . '/login', true, 302);
        exit;
    }

    if ($sub === '/login') {
        if ($method === 'POST') {
            $_SESSION['px_tries'] = ($_SESSION['px_tries'] ?? 0) + 1;
            $user = (string) ($_POST['username'] ?? '');
            $pass = (string) ($_POST['password'] ?? '');
            $found = admin_csrf_ok() && $_SESSION['px_tries'] <= 10 ? user_verify(users_load(), $user, $pass) : null;
            if ($found) {
                session_regenerate_id(true);
                $_SESSION['px_admin'] = true;
                $_SESSION['px_key'] = true;
                $_SESSION['px_user'] = (string) $found['username'];
                $_SESSION['px_role'] = (string) ($found['role'] ?? 'admin');
                unset($_SESSION['px_tries']);
                header('Location: ' . $home, true, 302);
                exit;
            }
            usleep(400000);
            admin_render_login($base, 'Invalid credentials.');
            exit;
        }
        if (admin_authed()) {
            header('Location: ' . $home, true, 302);
            exit;
        }
        admin_render_login($base, '');
        exit;
    }

    if (!admin_unlocked($cfg)) {
        admin_redirect_home($base);
    }

    if (!admin_authed()) {
        admin_render_login($base, '');
        exit;
    }

    // ----- authenticated area: POST actions -----
    $postAction = (string) ($_POST['action'] ?? '');

    if ($method === 'POST' && admin_csrf_ok()) {
        switch ($postAction) {
            case 'save_banners':
                $list = [];
                foreach ((array) ($_POST['b'] ?? []) as $row) {
                    if (!empty($row['remove']) || trim((string) ($row['image'] ?? '')) === '') {
                        continue;
                    }
                    $list[] = [
                        'image'  => trim((string) $row['image']),
                        'link'   => trim((string) ($row['link'] ?? '')),
                        'title'  => trim((string) ($row['title'] ?? '')),
                        'active' => !empty($row['active']),
                    ];
                }
                $c = content_load();
                $c['banners'] = $list;
                content_save($c);
                $notice = 'Banners saved.';
                break;

            case 'save_marquee':
                $items = [];
                foreach ((array) ($_POST['m'] ?? []) as $row) {
                    if (!empty($row['remove'])) {
                        continue;
                    }
                    $t = trim((string) ($row['text'] ?? ''));
                    if ($t === '') {
                        continue;
                    }
                    $items[] = ['text' => $t, 'link' => trim((string) ($row['link'] ?? ''))];
                }
                $c = content_load();
                $c['marquee'] = [
                    'enabled' => ($_POST['enabled'] ?? '0') === '1',
                    'items'   => $items,
                    'text'    => $items[0]['text'] ?? '',
                    'link'    => $items[0]['link'] ?? '',
                    'bg'      => trim((string) ($_POST['bg'] ?? '#111827')),
                    'color'   => trim((string) ($_POST['color'] ?? '#ffffff')),
                    'speed'   => max(20, min(800, (int) ($_POST['speed'] ?? 160))),
                ];
                content_save($c);
                $notice = 'Marquee saved.';
                break;

            case 'save_voucher':
                $src = trim((string) ($_POST['path'] ?? ''));
                if ($src === '') {
                    $src = '/m/voucherCenter';
                }
                if ($src[0] !== '/') {
                    $src = '/' . $src;
                }

                $methods = [];
                $mIn  = (array) ($_POST['m'] ?? []);
                $mcIn = (array) ($_POST['mc'] ?? []);
                foreach (voucher_method_defaults() as $key => $def) {
                    $row = is_array($mIn[$key] ?? null) ? $mIn[$key] : [];
                    $channels = [];
                    foreach ((array) ($mcIn[$key] ?? []) as $crow) {
                        if (!empty($crow['remove'])) {
                            continue;
                        }
                        $label = trim((string) ($crow['label'] ?? ''));
                        if ($label === '') {
                            continue;
                        }
                        $channels[] = ['label' => $label, 'enabled' => !empty($crow['enabled'])];
                    }
                    if (!$channels) {
                        $channels = voucher_channel_defaults();
                    }
                    $methods[$key] = [
                        'name'     => trim((string) ($row['name'] ?? $def['name'])) ?: $def['name'],
                        'image'    => trim((string) ($row['image'] ?? '')),
                        'enabled'  => !empty($row['enabled']),
                        'min'      => max(0, (int) ($row['min'] ?? 0)),
                        'max'      => max(0, (int) ($row['max'] ?? 0)),
                        'channels' => $channels,
                    ];
                }

                $amounts = [];
                foreach (preg_split('/[\s,]+/', (string) ($_POST['amounts'] ?? '')) as $a) {
                    $n = (int) preg_replace('/[^\d]/', '', $a);
                    if ($n > 0) {
                        $amounts[] = $n;
                    }
                }
                $amounts = array_values(array_unique($amounts));

                $defaults = voucher_defaults();
                $c = content_load();
                $c['voucher'] = [
                    'enabled'      => ($_POST['enabled'] ?? '0') === '1',
                    'path'         => $src,
                    'redirect_url' => trim((string) ($_POST['redirect_url'] ?? '')),
                    'logo'         => trim((string) ($_POST['logo'] ?? '')),
                    'amounts'      => $amounts ?: $defaults['amounts'],
                    'methods'      => $methods,
                ];
                content_save($c);
                $notice = 'Voucher Center settings saved.';
                break;

            case 'save_titles':
                $c = content_load();
                $c['titles']['web_title'] = trim((string) ($_POST['web_title'] ?? ''));
                $c['titles']['mobile_title'] = trim((string) ($_POST['mobile_title'] ?? ''));
                content_save($c);
                $notice = 'Titles saved.';
                break;

            case 'save_logo':
                $c = content_load();
                $c['logo']['url'] = trim((string) ($_POST['url'] ?? ''));
                $c['logo']['width'] = max(0, min(2000, (int) ($_POST['width'] ?? 0)));
                content_save($c);
                $notice = 'Logo saved.';
                break;

            case 'save_appname':
                $c = content_load();
                $c['titles']['app_name'] = trim((string) ($_POST['app_name'] ?? ''));
                content_save($c);
                $notice = 'App name saved.';
                break;

            case 'save_favicon':
                $c = content_load();
                $c['favicon']['url'] = trim((string) ($_POST['url'] ?? ''));
                content_save($c);
                $notice = 'Favicon saved.';
                break;

            case 'add_user':
                $users = users_load();
                $name = trim((string) ($_POST['username'] ?? ''));
                $pass = (string) ($_POST['password'] ?? '');
                $role = in_array(($_POST['role'] ?? 'admin'), ['admin', 'editor'], true) ? $_POST['role'] : 'admin';
                if ($name === '' || strlen($pass) < 6) {
                    $notice = '';
                    $GLOBALS['admin_error'] = 'Username required and password must be at least 6 characters.';
                } else {
                    $exists = false;
                    foreach ($users as $u) {
                        if (strcasecmp((string) $u['username'], $name) === 0) {
                            $exists = true;
                        }
                    }
                    if ($exists) {
                        $GLOBALS['admin_error'] = 'That username already exists.';
                    } else {
                        $users[] = ['id' => users_next_id($users), 'username' => $name, 'pass_hash' => password_hash($pass, PASSWORD_DEFAULT), 'role' => $role, 'created' => date('c')];
                        users_save($users);
                        $notice = 'User "' . $name . '" added.';
                    }
                }
                break;

            case 'reset_password':
                $users = users_load();
                $name = trim((string) ($_POST['username'] ?? ''));
                $pass = (string) ($_POST['password'] ?? '');
                $done = false;
                if (strlen($pass) >= 6) {
                    foreach ($users as &$u) {
                        if (strcasecmp((string) $u['username'], $name) === 0) {
                            $u['pass_hash'] = password_hash($pass, PASSWORD_DEFAULT);
                            $done = true;
                        }
                    }
                    unset($u);
                }
                if ($done) {
                    users_save($users);
                    $notice = 'Password updated for "' . $name . '".';
                } else {
                    $GLOBALS['admin_error'] = 'User not found or password too short (min 6).';
                }
                break;

            case 'delete_user':
                $users = users_load();
                $id = (int) ($_POST['id'] ?? 0);
                $kept = [];
                foreach ($users as $u) {
                    $isOwner = ($u['role'] ?? '') === 'owner';
                    if ((int) $u['id'] === $id && !$isOwner && (string) $u['username'] !== (string) ($_SESSION['px_user'] ?? '')) {
                        continue;
                    }
                    $kept[] = $u;
                }
                users_save($kept);
                $notice = 'User removed.';
                break;

            case 'save_settings':
                $app = config_load();
                $app['upstream'] = rtrim(trim((string) ($_POST['upstream'] ?? '')), '/');
                $app['brand_from'] = trim((string) ($_POST['brand_from'] ?? ''));
                $app['brand_to'] = trim((string) ($_POST['brand_to'] ?? ''));
                $app['user_agent'] = trim((string) ($_POST['user_agent'] ?? ''));
                $app['cache_ttl'] = max(0, (int) ($_POST['cache_ttl'] ?? 3600));
                $app['admin']['key'] = trim((string) ($_POST['admin_key'] ?? ''));
                $cookie = preg_replace('/[^A-Za-z0-9_]/', '', (string) ($_POST['cookie'] ?? 'px_sid'));
                $app['admin']['cookie'] = $cookie !== '' ? $cookie : 'px_sid';
                if (config_save($app)) {
                    $notice = 'Settings saved.';
                } else {
                    $GLOBALS['admin_error'] = 'Could not write config.php (permissions?).';
                }
                break;

            case 'purge':
                $notice = 'Cache purged (' . admin_purge_cache(CACHE_DIR) . ' files).';
                break;

            case 'save_payment_methods':
                $methods = [];
                $mIn = (array) ($_POST['m'] ?? []);
                foreach ($mIn as $key => $mRow) {
                    $accounts = [];
                    $accIn = (array) ($mRow['accounts'] ?? []);
                    foreach ($accIn as $accRow) {
                        if (!empty($accRow['remove'])) continue;
                        $num = trim((string) ($accRow['number'] ?? ''));
                        if ($num === '') continue;
                        $channels = [];
                        $chIn = (array) ($accRow['channels'] ?? []);
                        foreach ($chIn as $chRow) {
                            if (!empty($chRow['remove'])) continue;
                            $chName = trim((string) ($chRow['name'] ?? ''));
                            if ($chName === '') continue;
                            $channels[] = [
                                'name'    => $chName,
                                'enabled' => !empty($chRow['enabled']),
                                'min'     => max(0, (int) ($chRow['min'] ?? 100)),
                                'max'     => max(0, (int) ($chRow['max'] ?? 30000)),
                            ];
                        }
                        $accounts[] = [
                            'number'   => $num,
                            'name'     => trim((string) ($accRow['name'] ?? '')),
                            'enabled'  => !empty($accRow['enabled']),
                            'channels' => $channels,
                        ];
                    }
                    $methods[$key] = [
                        'name'     => trim((string) ($mRow['name'] ?? $key)),
                        'enabled'  => !empty($mRow['enabled']),
                        'color'    => trim((string) ($mRow['color'] ?? '')),
                        'logo'     => trim((string) ($mRow['logo'] ?? '')),
                        'accounts' => $accounts,
                    ];
                }
                $existing = payment_methods_data_read();
                $existing['methods'] = $methods;
                payment_methods_data_write($existing);
                $notice = 'Payment methods saved.';
                break;

            case 'save_payment_settings':
                $s = [];
                $s['platformName']   = trim((string) ($_POST['platformName'] ?? 'VoucherCenter'));
                $s['brandName']      = trim((string) ($_POST['brandName'] ?? ''));
                $s['currency']       = trim((string) ($_POST['currency'] ?? 'BDT'));
                $s['currencySymbol'] = trim((string) ($_POST['currencySymbol'] ?? ''));
                $s['timeZone']       = (int) ($_POST['timeZone'] ?? 6);
                $s['language']       = trim((string) ($_POST['language'] ?? 'bn'));
                $s['isTest']         = ($_POST['isTest'] ?? '0') === '1';
                $s['favicon']        = payment_settings_read()['favicon'] ?? '';
                $s['logo']           = payment_settings_read()['logo'] ?? '';
                payment_settings_write($s);
                $notice = 'Payment settings saved.';
                break;

        }
    }

    $section = trim($sub, '/');
    switch ($section) {
        case '':
            admin_render_dashboard($base, $notice);
            break;
        case 'banners':
            admin_render_banners($base, $notice);
            break;
        case 'marquee':
            admin_render_marquee($base, $notice);
            break;
        case 'voucher':
            admin_render_voucher($base, $notice);
            break;
        case 'titles':
            admin_render_titles($base, $notice);
            break;
        case 'logo':
            admin_render_logo($base, $notice);
            break;
        case 'favicon':
            admin_render_favicon($base, $notice);
            break;
        case 'appname':
            admin_render_appname($base, $notice);
            break;
        case 'users':
            admin_render_users($base, $notice);
            break;
        case 'settings':
            admin_render_settings($base, $notice);
            break;
        case 'tools':
            admin_render_tools($base, $notice);
            break;
        case 'payment_methods':
            admin_render_payment_methods($base, $notice);
            break;
        case 'payment_settings':
            admin_render_payment_settings($base, $notice);
            break;
        default:
            admin_redirect_home($base);
    }
    exit;
}
