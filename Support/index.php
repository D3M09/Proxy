<?php

declare(strict_types=1);

/**
 * Public chat panel — offline stitch clone.
 *
 * Same structure/classes as stitch_customer_support_chat_panel/code.html,
 * styled by assets/chat-panel.css (hand-compiled, no CDN) and driven by
 * assets/chat-panel.js against api.php + media.php, so visitor messages
 * arrive in the agent console at /admin/ in realtime.
 *
 * Decisions applied:
 * - Brand mapped to BBC99.bet + local avatar (no 1333bet/Elena/remote imgs).
 * - Live thread only (no hardcoded receipt/player1333 mock); welcome card +
 *   chips + composer form is shown on open, first send starts the chat.
 * - Dead UI hidden: header search/profile, mic button, bottom 3-tab nav.
 */

require __DIR__ . '/lib/bootstrap.php';

$siteName = (string) ($config['site_name'] ?? 'BBC99.bet');
$title = (string) ($config['widget_title'] ?? $siteName);
$subtitle = (string) ($config['widget_subtitle'] ?? '24/7 অনলাইন গ্রাহক পরিষেবা');
$notice = (string) ($config['widget_notice'] ?? '');
$greeting = (string) ($config['widget_greeting'] ?? '');

$baseUrl = rtrim(trim((string) ($config['base_url'] ?? '')), '/');
if ($baseUrl === '') {
    $scheme = is_https() ? 'https' : 'http';
    // safe_host() rejects a crafted Host header, so the URLs below (and the
    // JSON island that carries them) can never be poisoned by the client.
    $host = safe_host();
    $dir = rtrim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'))), '/');
    $baseUrl = $scheme . '://' . $host . $dir;
}

$quickReplies = $config['widget_quick_replies'] ?? [];
$quickReplies = is_array($quickReplies) ? array_values(array_filter($quickReplies, 'is_string')) : [];
if ($quickReplies === []) {
    $quickReplies = menu_main_labels();
}

// Main-menu grid comes from the live tree so buttons always send valid labels.
$mainLabels = menu_main_labels();
$agentLabel = MENU_AGENT;

$avatarRaw = trim((string) ($config['widget_avatar'] ?? 'assets/images/support.svg'));
$avatarUrl = '';
if ($avatarRaw !== '') {
    if (preg_match('#^(https?://|data:image/)#i', $avatarRaw) === 1) {
        $avatarUrl = $avatarRaw;
    } elseif (strpos($avatarRaw, '/') === 0) {
        $scheme = is_https() ? 'https' : 'http';
        $host = safe_host();
        $avatarUrl = $scheme . '://' . $host . $avatarRaw;
    } else {
        $avatarUrl = $baseUrl . '/' . ltrim($avatarRaw, '/');
    }
}

$bridge = [
    'api' => $baseUrl . '/api.php',
    'media' => $baseUrl . '/media.php',
    'avatar' => $avatarUrl,
    'quickReplies' => $quickReplies,
    'poll' => (int) ($config['poll_interval'] ?? 4000),
    'maxUploadMb' => (int) ($config['max_upload_mb'] ?? 25),
    'accept' => implode(',', array_keys(is_array($config['allowed_media'] ?? null) ? $config['allowed_media'] : [])),
];

$cssV = (int) @filemtime(__DIR__ . '/assets/chat-panel.css');
$jsV = (int) @filemtime(__DIR__ . '/assets/chat-panel.js');

// The public page is meant to be reached, but never framed by another site, and
// it never needs <object> or a rewritten <base>.
security_headers("'self'");
?>
<!DOCTYPE html>
<html class="dark" lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#edf0f6">
    <meta name="google" content="notranslate">
    <title><?= e($siteName) ?></title>
    <link rel="stylesheet" href="<?= e($baseUrl) ?>/assets/chat-panel.css?v=<?= $cssV ?>">
</head>

<body class="cp-body">
    <header class="cp-header cp-safe-t">
        <div class="cp-header-in">
            <div class="cp-orb" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3a7 7 0 0 0-7 7v3.5c0 .8-.4 1.6-1 2.1-.3.3-.2.9.2 1 2 .5 3.9.4 5.6-.2.7.3 1.4.4 2.2.4a7 7 0 0 0 0-14z" /><circle cx="9.5" cy="10.5" r="1" fill="currentColor" stroke="none" /><circle cx="14.5" cy="10.5" r="1" fill="currentColor" stroke="none" /><path d="M9 15h6" stroke-linecap="round" /></svg>
            </div>
            <div class="cp-brand">
                <div class="cp-brand-row">
                    <span class="cp-brand-name"><?= e($title) ?></span>
                    <span class="cp-live"><span class="cp-dot"></span><span class="cp-live-t">সক্রিয়</span></span>
                </div>
                <span class="cp-sub"><?= e($subtitle) ?></span>
            </div>
        </div>
    </header>

    <main class="cp-main">
        <div class="cp-col">
            <div class="cp-agentbar" id="cp-agentbar">
                <span class="cp-ab-photo" id="cp-ab-photo" hidden></span>
                <span class="cp-dot" id="cp-ab-dot"></span>
                <span id="cp-ab-text">Agent session • connecting…</span>
            </div>
            <div class="cp-date">
                <div class="cp-date-pill">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" stroke-linecap="round" /></svg>
                    <span id="cp-date">আজ</span>
                </div>
            </div>

            <section class="cp-sec" id="cp-welcome">
                <?php if ($notice !== '') : ?>
                    <div class="cp-notice">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l10 18H2z" stroke-linejoin="round" /><path d="M12 10v5" stroke-linecap="round" /><circle cx="12" cy="18" r="1" fill="currentColor" stroke="none" /></svg>
                        <p><?= e($notice) ?></p>
                    </div>
                <?php endif; ?>
                <div class="cp-card">
                    <div class="cp-glow-a"></div>
                    <div class="cp-glow-b"></div>
                    <div class="cp-card-in">
                        <div class="cp-agent-row">
                            <div class="cp-agent-id">
                                <div class="cp-avatar">
                                    <span class="cp-avatar-fallback" aria-hidden="true"><?= e(mb_substr($title, 0, 1, 'UTF-8')) ?></span>
                                    <?php if ($avatarUrl !== '') : ?>
                                        <img src="<?= e($avatarUrl) ?>" alt="গ্রাহক সেবা প্রতিনিধি" onerror="this.remove()">
                                    <?php endif; ?>
                                    <span class="cp-online"><i></i></span>
                                </div>
                                <div style="min-width:0">
                                    <div class="cp-agent-name">
                                        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e($title) ?> সাপোর্ট</span>
                                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 2.4 3.4-.5.9 3.3 3.3.9-.5 3.4L23.9 14l-2.4 2.4.5 3.4-3.3.9-.9 3.3-3.4-.5L12 26l-2.4-2.4-3.4.5-.9-3.3-3.3-.9.5-3.4L.1 14l2.4-2.4-.5-3.4 3.3-.9.9-3.3 3.4.5z" transform="scale(.92)" /><path d="M10.6 14.6l-2.1-2.1-1.4 1.4 3.5 3.5 7-7-1.4-1.4z" fill="#ffffff" /></svg>
                                    </div>
                                    <div class="cp-agent-sub"><?= e($title) ?> গ্রাহক সেবা প্রতিনিধি</div>
                                </div>
                            </div>
                            <div class="cp-manual">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4" /><path d="M4 21c0-4 3.6-6 8-6s8 2 8 6" stroke-linecap="round" /><path d="M16 8l2 2 3-3" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                <span>ম্যানুয়াল সাপোর্ট</span>
                            </div>
                        </div>
                        <p class="cp-greet"><?= e($greeting) ?></p>
                        <div class="cp-tree">
                            <div class="cp-tree-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11l9-8 9 8" stroke-linecap="round" stroke-linejoin="round" /><path d="M5 10v10h5v-6h4v6h5V10" stroke-linejoin="round" /></svg>
                                <span>🏠 প্রধান মেনু</span>
                            </div>
                            <div class="cp-grid">
                                <?php foreach (array_slice($mainLabels, 0, 4) as $i => $label) : ?>
                                    <button class="cp-btn<?= $i === 0 ? ' cp-active' : '' ?>" type="button" data-send="<?= e($label) ?>">
                                        <span class="cp-e"><?= e(mb_substr($label, 0, 2, 'UTF-8')) ?></span><span><?= e($label) ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <button class="cp-cta" type="button" data-send="<?= e($agentLabel) ?>">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 4h4l2 5-2.5 1.5a12 12 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z" stroke-linejoin="round" /></svg>
                                <span>📞 গ্রাহক সেবা প্রতিনিধির সাথে কথা বলুন</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="cp-chips">
                <div class="cp-chips-head">
                    <span class="cp-chips-title">দ্রুত সেবা ক্যাটাগরি</span>
                    <span class="cp-chips-hint">সোয়াইপ করুন
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    </span>
                </div>
                <div class="cp-chiprow cp-noscroll">
                    <?php foreach ($quickReplies as $i => $label) : ?>
                        <button class="cp-chip<?= $i === 0 ? ' cp-chip-on' : ' cp-chip-off' ?>" type="button" data-send="<?= e($label) ?>"><?= e($label) ?></button>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="cp-thread" id="cp-thread" aria-live="polite"></section>

            <div class="cp-typing" id="cp-typing" hidden>
                <div class="cp-tavatar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a8 8 0 0 1-8 8H4l2-3a8 8 0 1 1 15-5z" stroke-linejoin="round" /></svg>
                </div>
                <div class="cp-tbubble"><em>প্রতিনিধি টাইপ করছেন...</em><i></i><i></i><i></i></div>
            </div>

            <section class="cp-dock">
                <div class="cp-tip" id="cp-tip">
                    <div class="cp-tip-l"><span>💡</span><span>পরামর্শ: রসিদের ছবি ও বার্তা আলাদা পাঠান — সরাসরি ইমেজ যাচাই প্রক্রিয়া সম্পন্ন হবে</span></div>
                    <button class="cp-tip-x" id="cp-tip-x" type="button" aria-label="Dismiss tip">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" /></svg>
                    </button>
                </div>
                <div class="cp-shell">
                    <label class="cp-iconbtn" title="স্ক্রিনশট বা ফাইল যুক্ত করুন">
                        <input type="file" id="cp-file" hidden>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2" /><circle cx="9" cy="10" r="1.6" /><path d="M3 17l5-4 4 3 4-4 5 5" stroke-linejoin="round" /></svg>
                    </label>
                    <div class="cp-grow">
                        <textarea id="cp-input" rows="1" maxlength="4000" placeholder="বার্তা লিখুন, রসিদের ছবি বা TrxID পাঠান..." aria-label="Your message"></textarea>
                    </div>
                    <div class="cp-actions">
                        <button class="cp-send" id="cp-send" type="button" aria-label="বার্তা পাঠান">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 19V5M6 11l6-6 6 6" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php
    // A <script> block is not HTML-escaped by the parser, so "</script>" inside
    // any value would end the block early and the rest would run as markup.
    // JSON_HEX_TAG/AMP/APOS/QUOT escape <, >, &, ' and " as \uXXXX, which makes
    // that impossible while staying valid JSON.
    $bridgeJson = json_encode(
        $bridge,
        JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    );
    ?>
    <script type="application/json" id="cp-config"><?= $bridgeJson !== false ? $bridgeJson : '{}' ?></script>
    <script src="<?= e($baseUrl) ?>/assets/chat-panel.js?v=<?= $jsV ?>" async></script>
</body>

</html>
