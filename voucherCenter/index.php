<?php
/**
 * Serves the voucher-center page with payment-methods data from the
 * admin-managed payment-methods.json and settings.json.
 */

require_once __DIR__ . '/../Proxy/admin/store.php';
require_once __DIR__ . '/../Proxy/admin/includes/functions.php';

$settings = payment_settings_read();
$contentCfg = content_load();
$brandTo = (string) ($settings['brandName'] ?? $contentCfg['titles']['web_title'] ?? $contentCfg['titles']['app_name'] ?? '');

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

$vcBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/voucherCenter/index.php')), '/');
if ($vcBase === '' || $vcBase === '.') {
    $vcBase = '/voucherCenter';
}

$html = str_replace('/Vcentere/', $vcBase . '/', $html);

$legacyBrands = ['BigAceWin'];
if ($brandTo !== '') {
    foreach (array_unique($legacyBrands) as $from) {
        if ($from === '' || strcasecmp($from, $brandTo) === 0) continue;
        $html = preg_replace('/(?<![\w.\/-])' . preg_quote($from, '/') . '(?!\.[a-z])/i', $brandTo, $html);
    }
}

$platformName = $contentCfg['titles']['web_title'] ?? $contentCfg['titles']['app_name'] ?? $settings['platformName'] ?? 'VoucherCenter';
$tEsc = htmlspecialchars($platformName, ENT_QUOTES);
$html = preg_replace('/<title>.*?<\/title>/is', '<title>' . $tEsc . '</title>', $html, 1);

$favicon = trim((string) ($settings['favicon'] ?? $contentCfg['favicon']['url'] ?? ''));
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

$pmData = payment_methods_data_read();
$methodsIn = $pmData['methods'] ?? [];
$amountsRaw = $pmData['amounts'] ?? [];

$methodImages = [
    'NAGAD'   => $vcBase . '/NAGAD/BN_2_20240312230148421.png',
    'BKASH'   => $vcBase . '/BKASH/BN_2_20240312225413337.png',
    'BKASHSM' => $vcBase . '/BKASHSM/BN_1_20260711012519510.png',
    'NAGADSM' => $vcBase . '/NAGADSM/BN_1_20260711012544019.png',
    'USDT'    => $vcBase . '/USDT/786_CN_1.png',
    'ROCKET'  => $vcBase . '/ROCKET/BN_2_20240312230029166.png',
];

$images = [];
$names = [];
$methodCfg = [];
$channelsByMethod = [];

foreach ($methodsIn as $key => $m) {
    $name = trim((string) ($m['name'] ?? '')) !== '' ? (string) $m['name'] : $key;
    $image = $methodImages[$key] ?? '';
    $enabled = (bool) ($m['enabled'] ?? true);
    $color = trim((string) ($m['color'] ?? ''));
    $images[$key] = $image;
    $names[$key] = $name;
    $methodCfg[$key] = [
        'enabled' => $enabled,
        'color'   => $color,
    ];

    $clean = [];
    foreach (($m['accounts'] ?? []) as $acc) {
        if (!($acc['enabled'] ?? true)) continue;
        foreach (($acc['channels'] ?? []) as $ch) {
            if (!($ch['enabled'] ?? true)) continue;
            $label = trim((string) ($ch['name'] ?? ''));
            if ($label === '') continue;
            $min = (int) ($ch['min'] ?? 100);
            $max = (int) ($ch['max'] ?? 30000);
            $exists = false;
            foreach ($clean as $c) {
                if ($c['label'] === $label) { $exists = true; break; }
            }
            if (!$exists) {
                $clean[] = ['label' => $label, 'enabled' => true, 'min' => $min, 'max' => $max];
            }
        }
    }
    if (!$clean) {
        $clean = [['label' => 'Personal', 'enabled' => true, 'min' => 100, 'max' => 30000]];
    }
    $channelsByMethod[$key] = $clean;
}

$html = preg_replace_callback(
    '/var paymentImages = \{[\s\S]*?\};/',
    function () use ($images, $jenc) {
        return 'var paymentImages = ' . $jenc($images) . ';';
    },
    $html, 1
);

$html = preg_replace_callback(
    '/var names = \{[^}]*\};/',
    function () use ($names, $jenc) {
        return 'var names = ' . $jenc($names) . ';';
    },
    $html, 1
);

$html = preg_replace_callback(
    '/try \{ adminConfig = JSON\.parse\(localStorage\.getItem\(\'voucherPaymentConfig\'\)\) \|\| \{\}; \} catch \(e\) \{ adminConfig = \{\}; \}/',
    function () use ($methodCfg, $jenc) {
        return 'adminConfig = ' . $jenc($methodCfg) . ';';
    },
    $html, 1
);

$rocketName = str_replace(['\\', "'"], ['\\\\', "\\'"], $names['ROCKET'] ?? 'Rocket');
$rocketHtml = '<div class="deposit-icon-bg"><div class="deposit-img-new"><img alt="' . $rocketName . '"></div></div>'
    . '<div class="desc-content"><div class="desc-info"><div class="vcn-list-text"><p>' . $rocketName . '</p></div></div></div>';
$html = preg_replace_callback(
    '/rocket\.innerHTML = \'[^\']*\';/',
    function () use ($rocketHtml) {
        return "rocket.innerHTML = '" . $rocketHtml . "';";
    },
    $html, 1
);

$amounts = [];
foreach ($amountsRaw as $a) {
    $n = (int) preg_replace('/[^\d]/', '', (string) $a);
    if ($n > 0) $amounts[] = $n;
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
    $html, 1
);

$firstKey = array_key_first($channelsByMethod) ?: 'BKASH';
$chItems = '';
$firstEnabled = true;
foreach (($channelsByMethod[$firstKey] ?? []) as $ch) {
    $enabled = $ch['enabled'];
    $cls = ($enabled && $firstEnabled) ? 'ck ' : ' ';
    if ($enabled) $firstEnabled = false;
    $style = $enabled ? '' : ' style="display:none"';
    $chItems .= '<li class="' . $cls . '"' . $style . '><span class="method-list-info">'
        . htmlspecialchars($ch['label'], ENT_QUOTES) . '</span></li>';
}
$html = preg_replace_callback(
    '/(<div class="\s*vc-v2-method-list\s*">\s*<ul>)[\s\S]*?(<\/ul>)/',
    function ($m) use ($chItems) {
        return $m[1] . $chItems . $m[2];
    },
    $html, 1
);

$chScript = '<script>(function(){var CH=' . $jenc($channelsByMethod) . ';'
    . 'function keyOf(li){for(var k in CH){if(li&&li.classList&&li.classList.contains(k))return k;}return null;}'
    . 'function tpl(){return document.querySelector("svg.deposit-list-ck");}'
    . 'function mark(li,on){var ex=li.querySelector("svg.deposit-list-ck");if(ex&&ex.parentNode)ex.parentNode.removeChild(ex);if(on){var t=tpl();if(t)li.insertBefore(t.cloneNode(true),li.firstChild);}}'
    . 'function render(key){var list=CH[key];if(!list||!list.length)return;var ul=document.querySelector(".vc-v2-method-list ul");if(!ul)return;ul.innerHTML="";var first=null;'
    . 'list.forEach(function(ch){var li=document.createElement("li");var sp=document.createElement("span");sp.className="method-list-info";sp.textContent=ch.label||"";li.appendChild(sp);if(ch.enabled===false)li.style.display="none";ul.appendChild(li);'
    . 'if(ch.enabled!==false&&!first)first=li;'
    . 'li.addEventListener("click",function(){var all=ul.querySelectorAll("li");for(var i=0;i<all.length;i++){var on=all[i]===li;all[i].classList.toggle("selected",on);all[i].classList.toggle("ck",on);mark(all[i],on);}window.selectedPaymentChannel=ch.label;});'
    . '});if(first)first.click();}'
    . 'function getActiveMethod(){var sel=document.querySelector("li.change-item-animate.selected");if(sel){var k=keyOf(sel);if(k)return k;}var ck=document.querySelector("li.change-item-animate.ck");if(ck){var k2=keyOf(ck);if(k2)return k2;}var ms=document.querySelectorAll("li.change-item-animate");if(ms.length){var k3=keyOf(ms[0]);if(k3)return k3;}return null;}'
    . 'function boot(){var ms=document.querySelectorAll("li.change-item-animate");for(var i=0;i<ms.length;i++){(function(li){li.addEventListener("click",function(){var k=keyOf(li);if(k)render(k);});})(ms[i]);}var k=getActiveMethod();if(k)render(k);}'
    . 'if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",function(){setTimeout(boot,0);});else setTimeout(boot,0);'
    . '})();</script>';
$html = preg_replace('/<\/body>/i', $chScript . '</body>', $html, 1);

$apiCreateOrder = '/api/createOrder.php';
$paymentUrl = $vcBase . '/payment.php';
$nextJs = '(function(){'
    . 'var m=window.selectedPaymentMethod,a=window.selectedDepositAmount;'
    . 'var c=window.selectedPaymentChannel;'
    . 'if(!c){var chEl=document.querySelector(".vc-v2-method-list li.selected span.method-list-info, .vc-v2-method-list li.ck span.method-list-info");if(chEl)c=chEl.textContent.trim();}'
    . 'if(!c)c="Personal";'
    . 'if(!m||!a){alert("Please select method and amount");return;}'
    . 'var btn=document.querySelector(".vc-v2-submit");'
    . 'if(btn){btn.disabled=true;btn.textContent="Processing...";}'
    . 'fetch("' . $apiCreateOrder . '",{method:"POST",headers:{"Content-Type":"application/json"},'
    . 'body:JSON.stringify({method:m,amount:Number(a.replace(/,/g,"")),channel:c})})'
    . '.then(function(r){return r.json()})'
    . '.then(function(d){if(d.success&&d.trackingNumber){window.location.href="' . $paymentUrl . '?tracking="+d.trackingNumber;}'
    . 'else{alert(d.error||"Failed to create order");if(btn){btn.disabled=false;btn.textContent="\u09AA\u09B0\u09AC\u09B0\u09CD\u09A4\u09C0";}}})'
    . '.catch(function(){alert("Network error. Try again.");if(btn){btn.disabled=false;btn.textContent="\u09AA\u09B0\u09AC\u09B0\u09CD\u09A4\u09C0";}});'
    . '})();';
$html = preg_replace(
    '/window\.location\.href\s*=\s*\x27[^\x27]+\x27\s*\+\s*query\.toString\(\);/',
    $nextJs . ';',
    $html, 1
);

$html = preg_replace('/\sdata-savepage-href="[^"]*"/i', '', $html);
$html = preg_replace('/<meta\s+name="savepage-[^"]*"[^>]*>/i', '', $html);
$html = preg_replace('/<meta\s+name="savepage-from"[^>]*>/i', '', $html);

$logo = trim((string) ($settings['logo'] ?? $contentCfg['logo']['url'] ?? ''));
if ($logo !== '') {
    $logoJson = $jenc($logo);
    $logoScript = '<script>(function(){var L=' . $logoJson . ';if(!L)return;'
        . 'function s(){var im=document.getElementsByTagName("img");for(var i=0;i<im.length;i++){'
        . 'var v=(im[i].getAttribute("src")||"")+" "+(im[i].className||"");'
        . 'if(/logo/i.test(v)&&im[i].src!==L){im[i].src=L;}}}'
        . 'if(document.readyState!=="loading")s();else document.addEventListener("DOMContentLoaded",s);'
        . 'setInterval(s,1500);})();</script>';
    $html = preg_replace('/<\/body>/i', $logoScript . '</body>', $html, 1);
}

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');
echo $html;
