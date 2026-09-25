<?php

declare(strict_types=1);

/**
 * HTML fragments shared by admin/index.php and admin/api.php so the live
 * refresh renders exactly what a full page load renders.
 *
 * The console is Bangla-first: every label the console itself prints comes from
 * console_label(), so the server-rendered page and the JSON polls cannot drift
 * apart. Visitor content (names, messages) is never translated.
 */

/**
 * Console UI strings. The reference design keeps a few English terms
 * ("Agent Console", "Visitor Queues"), those are kept in parenthesis.
 */
function console_label(string $key): string
{
    static $labels = [
        // ------------------------------------------------------------- chrome
        'console' => 'এজেন্ট কনসোল',
        'online' => 'অনলাইন (সক্রিয় সাপোর্ট)',
        'unread' => 'অপঠিত',
        'settings' => 'সেটিংস',
        'signout' => 'সাইন আউট',
        'signin' => 'সাইন ইন',
        'queue' => 'ইনকামিং কিউ',
        'queue_short' => 'কিউ',
        'queue_hint' => 'কথোপকথন বেছে নিন',
        'search' => 'নাম বা মেসেজ খুঁজুন…',
        'theme_to_dark' => 'ডার্ক থিমে দেখুন',
        'theme_to_light' => 'লাইট থিমে দেখুন',

        // ------------------------------------------------------------- filters
        'tab_all' => 'সব',
        'tab_open' => 'চালু',
        'tab_unread' => 'অপঠিত',
        'tab_closed' => 'বন্ধ',

        // ------------------------------------------------------------- team
        'team' => 'টিম',
        'team_hint' => 'কতজন অনলাইনে, কার কত চ্যাট, কোন ডিপার্টমেন্ট',
        'online_short' => 'অনলাইনে',
        'offline' => 'অফলাইন',
        'joined' => 'যোগ দিয়েছে',
        'last_active' => 'শেষ সক্রিয়',
        'chats' => 'চ্যাট',
        'agents' => 'এজেন্ট',
        'demo' => 'ডেমো',
        'mine' => 'আমার',
        'unassigned' => 'বরাদ্দহীন',
        'assignee' => 'হ্যান্ডলিং',
        'transfer' => 'ট্রান্সফার',
        'departments' => 'ডিপার্টমেন্ট',
        'no_department' => 'কোনো ডিপার্টমেন্ট নেই',
        'team_summary' => 'মোট এজেন্ট',
        'team_online' => 'জন অনলাইন',
        'team_unassigned' => 'টি চ্যাট বরাদ্দহীন',
        'view_chats' => 'চ্যাট দেখুন',
        'agent_new' => 'নতুন এজেন্ট',
        'agent_handle' => 'লগইন আইডি',
        'agent_name' => 'নাম',
        'agent_photo' => 'প্রোফাইল ছবি (URL বা assets/…)',
        'agent_password' => 'পাসওয়ার্ড (কমপক্ষে ১০ অক্ষর)',
        'agent_password_keep' => 'নতুন পাসওয়ার্ড (খালি রাখলে অপরিবর্তিত)',
        'agent_add' => 'এজেন্ট যোগ করুন',
        'agent_save' => 'সংরক্ষণ করুন',
        'agent_delete' => 'মুছে ফেলুন',
        'agent_delete_confirm' => 'এই এজেন্টটি মুছে ফেলবেন? তার চ্যাটগুলো বরাদ্দহীন হয়ে যাবে।',
        'agent_owner_only' => 'এজেন্ট যোগ, সম্পাদনা বা মোছা শুধু মালিক অ্যাকাউন্ট থেকে করা যায়।',
        'agent_handle_taken' => 'এই লগইন আইডি আগেই ব্যবহার করা হয়েছে।',
        'agent_handle_reserved' => 'এই আইডিটি মালিক (Owner) অ্যাকাউন্টের জন্য সংরক্ষিত।',
        'agent_not_found' => 'এজেন্ট খুঁজে পাওয়া যায়নি।',
        'agent_added' => 'নতুন এজেন্ট যোগ করা হয়েছে।',
        'agent_updated' => 'এজেন্ট আপডেট হয়েছে।',
        'agent_removed' => 'এজেন্ট মুছে ফেলা হয়েছে।',
        'agent_password_set' => 'পাসওয়ার্ড পরিবর্তন হয়েছে।',
        'agent_own_password_only' => 'নিজের পাসওয়ার্ড শুধু নিজে পরিবর্তন করতে পারেন।',
        'owner' => 'মালিক (Owner)',
        'transferred_to' => 'ট্রান্সফার করা হয়েছে:',
        'transferred_cleared' => 'চ্যাটটি বরাদ্দহীন করা হয়েছে।',
        'demo_password_warning' => 'ডেমো পাসওয়ার্ড এখনো ব্যবহার হচ্ছে — লাইভে যাওয়ার আগে সব ডেমো এজেন্টের পাসওয়ার্ড পরিবর্তন করুন বা তাদের মুছে ফেলুন।',

        // -------------------------------------------------- security self-check
        'security_check' => 'নিরাপত্তা পরীক্ষা',
        'security_data_open' => 'ডাটা ফোল্ডার এই সার্ভারে সরাসরি পড়া যাচ্ছে। Built-in সার্ভার হলে <code>php -S 127.0.0.1:8000 router.php</code> দিয়ে চালান — নইলে <code>data/</code> এর সব কথোপকথন ও পাসওয়ার্ড হ্যাশ যে কেউ নামিয়ে নিতে পারবে।',
        'security_weak_owner' => 'মালিক (Owner) পাসওয়ার্ড এখনো ডিফল্ট <code>password</code> — যে কেউ ঢুকে পড়তে পারে। আগে থেকেই হ্যাশ বদলে ফেলুন।',

        // --------------------------------------------------- list + statuses
        'anonymous' => 'অনামী ভিজিটর',
        'status_open' => 'চালু',
        'status_closed' => 'বন্ধ',
        'messages' => 'বার্তা',
        'new' => 'নতুন',
        'no_conversations' => 'এখনো কোনো কথোপকথন নেই।',
        'no_selection' => 'বাঁ দিক থেকে একটি কথোপকথন বেছে নিন।',
        'waiting' => 'প্রথম ভিজিটরের অপেক্ষায়…',

        // ------------------------------------------------- roles / presence
        'visitor' => 'ভিজিটর',
        'agent' => 'এজেন্ট (আপনি)',
        'seen' => '✓✓ দেখা হয়েছে',
        'typing' => 'ভিজিটর লিখছেন…',
        'connected' => 'সংযুক্ত',

        // --------------------------------------------------- thread actions
        'close' => 'বন্ধ করুন',
        'reopen' => 'আবার চালু করুন',
        'delete' => 'ডিলিট',
        'started' => 'শুরু',
        'from' => 'থেকে',
        'confirm_delete' => 'এই কথোপকথনটি স্থায়ীভাবে মুছে ফেলবেন?',

        // --------------------------------------------------------- composer
        'canned' => 'দ্রুত উত্তর',
        'attach' => 'অ্যাটাচ',
        'insert_canned' => 'আরও দ্রুত উত্তর',
        'send' => 'উত্তর পাঠান',
        'sending' => 'পাঠানো হচ্ছে…',
        'sent' => 'পাঠানো হয়েছে',
        'write_reply' => 'একটি উত্তর লিখুন… (Ctrl+Enter দিয়ে পাঠান)',
        'attached' => 'সংযুক্ত',

        // ------------------------------------------------- visitor panel
        'visitor_info' => 'ভিজিটর তথ্য',
        'reference' => 'রেফারেন্স',
        'ip' => 'আইপি অ্যাড্রেস',
        'device' => 'ডিভাইস / ব্রাউজার',
        'page' => 'শেষ পেজ',
        'email' => 'ইমেইল',
        'copy_link' => 'লিংক কপি',
        'copied' => 'কপি হয়েছে',
        'copy_prompt' => 'লিংক কপি করুন:',
        'unknown' => '—',
        'open_file' => 'খুলুন',
        'last_message' => 'শেষ বার্তা',
        'panel_note' => 'আইপি ও ডিভাইস কথোপকথন শুরুর মুহূর্তে রেকর্ড করা হয়।',
    ];

    return $labels[$key] ?? $key;
}

/**
 * The queue filters the console understands, in tab order.
 *
 * @return list<string>
 */
function console_filters(): array
{
    // 'agent' carries an extra ?agent=<id> and is reached from the team page.
    return ['all', 'open', 'mine', 'agent', 'unread', 'unassigned', 'closed'];
}

/**
 * Apply a queue filter to conversation rows.
 *
 * Single implementation for the page render *and* the JSON polls: when the two
 * disagree, the live refresh silently undoes whatever filter is on screen.
 *
 * @param list<array<string,mixed>> $rows
 * @param string $agentId Only used by the 'agent' filter (the roster links).
 * @param string $meId The signed-in agent, used by 'mine'.
 * @return list<array<string,mixed>>
 */
function filter_rows(array $rows, string $filter, string $agentId = '', string $meId = ''): array
{
    $assignedTo = static fn (array $r, string $id): bool => (string) ($r['assignee'] ?? '') === $id;

    return match ($filter) {
        'open' => array_values(array_filter($rows, static fn (array $r): bool => (string) $r['status'] === 'open')),
        'closed' => array_values(array_filter($rows, static fn (array $r): bool => (string) $r['status'] === 'closed')),
        'unread' => array_values(array_filter($rows, static fn (array $r): bool => (int) $r['agent_unread'] > 0)),
        'mine' => $meId === '' ? [] : array_values(array_filter($rows, static fn (array $r): bool => $assignedTo($r, $meId))),
        'agent' => $agentId === '' ? [] : array_values(array_filter($rows, static fn (array $r): bool => $assignedTo($r, $agentId))),
        'unassigned' => array_values(array_filter($rows, static fn (array $r): bool => (string) ($r['assignee'] ?? '') === '')),
        default => $rows,
    };
}

/**
 * Asset URL prefix for the console: it lives one level down in /admin/ and
 * agent photos are stored relative to the web root, so this is that root.
 *
 * Derived by trimming the stylesheet path rather than by checking for a
 * leading "/": an install under a subdirectory ("/support/assets/style.css")
 * would otherwise resolve every agent photo against "/" and 404 it.
 */
function admin_asset_prefix(string $assetsPath): string
{
    $suffix = '/assets/style.css';
    if (str_ends_with($assetsPath, $suffix)) {
        $base = rtrim(substr($assetsPath, 0, -strlen($suffix)), '/');
        return $base === '' ? '/' : $base . '/';
    }
    return str_starts_with($assetsPath, '/') ? '/' : '../';
}

/** Turn a stored photo value into a URL the console can load. */
function agent_photo_url(string $photo, string $prefix): string
{
    $photo = trim($photo);
    if ($photo === '') {
        return '';
    }
    if (preg_match('#^(https?://|data:image/)#i', $photo) === 1) {
        return $photo;
    }
    // "//host/path" borrows the page's scheme instead of being read as a path.
    if (str_starts_with($photo, '//')) {
        return (is_https() ? 'https:' : 'http:') . $photo;
    }
    return $prefix . ltrim($photo, '/');
}

/**
 * The letter(s) an agent without a photo is identified by.
 *
 * One letter per word, at most two, so a long Bangla name still fits. A
 * trailing role in brackets ("মেহেদী হাসান (সুপারভাইজার)") is dropped first,
 * matching how the rest of the console labels people.
 */
function agent_initials(string $name): string
{
    $name = trim((string) preg_replace('/\([^)]*\)/u', '', $name));
    $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    if ($parts === []) {
        return '?';
    }
    $initials = mb_substr($parts[0], 0, 1);
    if (count($parts) > 1) {
        $initials .= mb_substr($parts[1], 0, 1);
    }
    return mb_strtoupper($initials, 'UTF-8');
}

/**
 * The colour and initials that stand in for an agent with no photo.
 *
 * @param array<string,mixed> $agent
 * @return array{from:string,to:string,initials:string,hash:string}
 */
function agent_avatar_parts(array $agent): array
{
    $key = trim((string) ($agent['id'] ?? ''));
    if ($key === '') {
        $key = (string) ($agent['name'] ?? '');
    }
    $hash = md5($key);
    $hue = hexdec(substr($hash, 0, 4)) % 360;

    return [
        // Two stops 32 degrees apart, so the gradient still reads as one while
        // staying inside the same colour family. At 27% lightness the worst
        // hue of the circle clears 4.5:1 against the white initials.
        'from' => sprintf('hsl(%d 72%% 27%%)', $hue),
        'to' => sprintf('hsl(%d 72%% 19%%)', ($hue + 32) % 360),
        'initials' => agent_initials((string) ($agent['name'] ?? '')),
        'hash' => $hash,
    ];
}

/**
 * The generated picture as SVG. $id must be unique within the document it is
 * embedded in, so the caller supplies it.
 *
 * @param array<string,mixed> $agent
 * @param bool $standalone True to make it a whole document, for a data: URI.
 */
function agent_avatar_svg(array $agent, string $class, string $id, bool $standalone = false): string
{
    $name = (string) ($agent['name'] ?? '');
    $parts = agent_avatar_parts($agent);
    $classAttr = $class === '' ? '' : ' class="' . e($class) . '"';
    $prefix = 'viewBox="0 0 96 96" role="img" aria-label="' . e($name) . '"';
    if ($standalone) {
        // Served as an <img> source it is a document of its own: it cannot
        // inherit the page's font or size, and it needs the SVG namespace.
        $prefix = 'xmlns="http://www.w3.org/2000/svg" width="96" height="96" '
            . 'font-family="Hind Siliguri, Noto Sans Bengali, Nirmala UI, Kalpurush, SolaimanLipi, sans-serif" '
            . $prefix;
    }

    // 34px in a 96px box keeps even two Bangla glyphs inside the circle.
    return '<svg' . $classAttr . ' ' . $prefix . '>'
        . '<defs><linearGradient id="' . e($id) . '" x1="0" y1="0" x2="1" y2="1">'
        . '<stop offset="0" stop-color="' . e($parts['from']) . '"/>'
        . '<stop offset="1" stop-color="' . e($parts['to']) . '"/>'
        . '</linearGradient></defs>'
        . '<rect width="96" height="96" rx="48" fill="url(#' . e($id) . ')"/>'
        . '<text x="48" y="48" text-anchor="middle" dominant-baseline="central"'
        . ' font-size="34" font-weight="600" fill="#fff">' . e($parts['initials']) . '</text>'
        . '</svg>';
}

/** The generated picture as a data: URI, ready to drop into an <img> source. */
function agent_avatar_fallback_uri(array $agent): string
{
    return 'data:image/svg+xml;base64,' . base64_encode(agent_avatar_svg($agent, '', 'g', true));
}

/**
 * An agent's profile picture.
 *
 * A photo the owner set — a link or a path inside this install — is shown as
 * it is. Agents without one get a generated picture: their initials on a
 * colour taken from their id. Both halves of that matter. The colour is a hue
 * across the whole circle rather than a handful of fixed gradients, and the
 * initials are what actually tells two people apart: a fixed six-colour
 * palette meant a roster of six agents rendered identical avatars, because the
 * silhouette behind them was the same shape for everyone.
 *
 * @param array<string,mixed> $agent
 */
function agent_avatar_html(array $agent, string $prefix, string $class = ''): string
{
    static $seq = 0;
    $seq++;

    $name = (string) ($agent['name'] ?? '');
    $url = agent_photo_url((string) ($agent['photo'] ?? ''), $prefix);
    $classes = trim('agent-avatar ' . $class);

    if ($url !== '') {
        // The generated picture rides along as data-avatar-fallback so a URL
        // that 404s, times out or is hotlink-blocked degrades to the initials
        // instead of a broken-image icon. The swap is made by a listener in
        // the console script — the CSP refuses inline onerror handlers.
        return '<img class="' . e($classes) . '" src="' . e($url) . '"'
            . ' data-avatar-fallback="' . e(agent_avatar_fallback_uri($agent)) . '"'
            . ' alt="' . e($name) . '" loading="lazy">';
    }

    // Sequence number first: the same agent is drawn in the topbar, the roster
    // and the team page, and a gradient id has to be unique per document.
    return agent_avatar_svg($agent, $classes, 'av' . $seq . '-' . substr(agent_avatar_parts($agent)['hash'], 0, 6));
}

/** Compact agent label for pills and queue rows. */
function agent_short_name(string $name): string
{
    $name = trim((string) preg_replace('/\([^)]*\)/u', '', $name));
    $parts = preg_split('/\s+/u', $name) ?: [];
    $short = implode(' ', array_slice($parts, 0, 2));
    return mb_strlen($short) > 20 ? mb_substr($short, 0, 19) . '…' : $short;
}

/** Relative time in Bangla; digits stay Latin, as in the reference design. */
function bn_time_ago(int $timestamp): string
{
    $delta = time() - $timestamp;
    if ($delta < 60) {
        return 'এখনই';
    }
    if ($delta < 3600) {
        return (int) floor($delta / 60) . ' মিনিট আগে';
    }
    if ($delta < 86400) {
        return (int) floor($delta / 3600) . ' ঘণ্টা আগে';
    }
    if ($delta < 604800) {
        return (int) floor($delta / 86400) . ' দিন আগে';
    }
    return date('M j, Y', $timestamp);
}

/** The sticky date chip between message groups. */
function bn_day_label(int $at): string
{
    $day = date('Y-m-d', $at);
    if ($day === date('Y-m-d')) {
        return 'আজ · ' . date('j M Y', $at);
    }
    if ($day === date('Y-m-d', time() - 86400)) {
        return 'গতকাল · ' . date('j M Y', $at);
    }
    return date('j M Y', $at);
}

/** Short public handle for a conversation, e.g. "#9f3a11c2". */
function short_ref(string $id): string
{
    return '#' . substr($id, 0, 8);
}

/** Avatar letter: the first character of the name, or "অ" for anonymous. */
function avatar_initial(string $name): string
{
    $name = trim($name);
    return $name === '' ? 'অ' : mb_strtoupper(mb_substr($name, 0, 1));
}

/** Deterministic avatar colour, so a conversation looks the same every render. */
function avatar_tone(string $id): string
{
    $tones = ['a', 'b', 'c', 'd'];
    return $tones[hexdec(substr($id, 0, 2)) % 4];
}

/**
 * "Chrome · Windows" from a User-Agent string. Plain substring sniffing: no
 * library, no database, and an empty string when we cannot tell.
 */
function format_device(string $ua): string
{
    $ua = trim($ua);
    if ($ua === '') {
        return '';
    }

    $browser = '';
    foreach ([
        'Edg/' => 'Edge',
        'OPR/' => 'Opera',
        'SamsungBrowser' => 'Samsung Internet',
        'Firefox/' => 'Firefox',
        'Chrome/' => 'Chrome',
        'Safari/' => 'Safari',
        'curl/' => 'curl',
    ] as $needle => $name) {
        if (str_contains($ua, $needle)) {
            $browser = $name;
            break;
        }
    }
    if ($browser === '' && stripos($ua, 'bot') !== false) {
        $browser = 'Bot';
    }

    // Android/iOS before Linux/macOS: their User-Agents mention both.
    $os = '';
    foreach ([
        'Windows' => 'Windows',
        'Android' => 'Android',
        'iPhone' => 'iOS',
        'iPad' => 'iOS',
        'Mac OS X' => 'macOS',
        'Linux' => 'Linux',
    ] as $needle => $name) {
        if (str_contains($ua, $needle)) {
            $os = $name;
            break;
        }
    }

    if ($browser === '') {
        return $os;
    }
    return $os === '' ? $browser : $browser . ' · ' . $os;
}

/**
 * Sidebar rows.
 *
 * @param list<array<string,mixed>> $rows
 * @param array<string,array<string,mixed>> $agents id => agent, for the
 *        "who is handling this" pill.
 */
function render_list_items(array $rows, ?string $selectedId = null, array $agents = []): string
{
    if ($rows === []) {
        return '<p class="empty">' . e(console_label('no_conversations')) . '</p>';
    }

    $html = '';
    foreach ($rows as $row) {
        $id = (string) $row['id'];
        $status = (string) $row['status'];
        $name = trim((string) $row['name']);
        $unread = (int) $row['agent_unread'];
        $assigneeId = (string) ($row['assignee'] ?? '');
        $assignee = $assigneeId !== '' ? ($agents[$assigneeId] ?? null) : null;

        $classes = ['conv'];
        if ($id === $selectedId) {
            $classes[] = 'active';
        }
        if ($status === 'closed') {
            $classes[] = 'closed';
        }

        $html .= '<a class="' . implode(' ', $classes) . '" href="?id=' . e($id) . '" data-id="' . e($id) . '">'
            . '<span class="conv-top">'
            . '<span class="conv-avatar tone-' . e(avatar_tone($id)) . '">' . e(avatar_initial($name)) . '</span>'
            . '<span class="conv-who">'
            . '<span class="conv-name">' . e($name !== '' ? $name : console_label('anonymous')) . '</span>'
            . '<span class="conv-ref">' . e(short_ref($id)) . '</span>'
            . '</span>'
            . '<span class="conv-time">' . e(bn_time_ago((int) $row['updated_at'])) . '</span>'
            . '</span>'
            . '<span class="conv-preview">' . e((string) $row['preview']) . '</span>'
            . '<span class="conv-meta">'
            . '<span class="tag ' . e($status) . '">'
            . e($status === 'open' ? console_label('status_open') : console_label('status_closed'))
            . '</span>'
            . ($assignee !== null
                ? '<span class="tag agent-tag" title="' . e((string) $assignee['name']) . '">'
                    . e(agent_short_name((string) $assignee['name'])) . '</span>'
                : '')
            . '<span class="tag">' . (int) $row['message_count'] . ' ' . e(console_label('messages')) . '</span>'
            . ($unread > 0 ? '<span class="tag count">' . $unread . ' ' . e(console_label('new')) . '</span>' : '')
            . '</span>'
            . '</a>';
    }

    return $html;
}

/**
 * Message bubbles, including any attachment.
 *
 * $prevAt is the timestamp of the message *before* the first one in $messages;
 * it keeps the day separator from repeating on every incremental poll.
 *
 * @param list<array<string,mixed>> $messages
 */
function render_messages(array $messages, string $chatId = '', int $offset = 0, ?int $prevAt = null): string
{
    $html = '';
    $prevDay = $prevAt === null ? null : date('Y-m-d', $prevAt);

    foreach (array_values($messages) as $i => $message) {
        $agent = ($message['role'] ?? '') === 'agent';
        $at = (int) ($message['at'] ?? 0);
        $text = (string) ($message['text'] ?? '');
        $file = is_array($message['file'] ?? null) ? $message['file'] : null;

        $day = date('Y-m-d', $at);
        if ($day !== $prevDay) {
            $html .= '<div class="day-sep"><span>' . e(bn_day_label($at)) . '</span></div>';
            $prevDay = $day;
        }

        // data-index is the absolute message index on the .msg wrapper: the
        // console places the Seen marker from the presence feed, and de-dupes
        // re-delivered bubbles by this attribute.
        $html .= '<div class="msg ' . ($agent ? 'agent' : 'visitor') . '" data-index="' . ($offset + $i) . '">'
            . '<div class="msg-role">'
            . '<span class="msg-who">' . e($agent ? console_label('agent') : console_label('visitor')) . '</span>'
            . '<span class="msg-sep">•</span>'
            . '<span class="msg-time">' . e(date('H:i', $at)) . '</span>'
            . '</div>'
            . '<div class="bubble">';

        if ($file !== null) {
            $html .= render_attachment($chatId, $file);
        }
        if ($text !== '') {
            $html .= '<div class="msg-text">' . e($text) . '</div>';
        }

        $html .= '<div class="msg-foot"><time datetime="' . e(date('c', $at)) . '">'
            . e(date('M j, H:i', $at)) . '</time></div>'
            . '</div>'
            . '</div>';
    }

    return $html;
}

/**
 * Image or video block with a caption card. media.php is one directory up from
 * the console.
 *
 * @param array<string,mixed> $file
 */
function render_attachment(string $chatId, array $file): string
{
    $token = (string) ($file['token'] ?? '');
    if (preg_match('/^[a-f0-9]{32}$/', $token) !== 1) {
        return '';
    }
    if (preg_match('/^[a-f0-9]{32}$/', $chatId) !== 1) {
        return '';
    }

    $url = '../media.php?id=' . rawurlencode($chatId) . '&f=' . rawurlencode($token);
    $mime = (string) ($file['mime'] ?? '');
    $name = (string) ($file['name'] ?? '');
    $label = $name !== '' ? $name : ($mime === '' ? 'attachment' : $mime);
    $size = format_bytes((int) ($file['size'] ?? 0));

    $meta = '<span class="media-meta">'
        . '<span class="media-name">' . e($label) . '</span>'
        . '<span class="media-size">· ' . e($size) . '</span>'
        . '<a class="media-open" href="' . e($url) . '" target="_blank" rel="noreferrer noopener">'
        . e(console_label('open_file')) . '</a>'
        . '</span>';

    if (media_is_video($mime)) {
        return '<div class="media media-video-card">'
            . '<video class="media-video" controls preload="metadata" src="' . e($url) . '"></video>'
            . $meta . '</div>';
    }

    return '<div class="media media-image-card">'
        . '<a class="media-thumb" href="' . e($url) . '" target="_blank" rel="noreferrer noopener">'
        . '<img class="media-img" src="' . e($url) . '" alt="' . e($label) . '" loading="lazy"></a>'
        . $meta . '</div>';
}
