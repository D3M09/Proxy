<?php

declare(strict_types=1);

/**
 * Support Center configuration.
 *
 * This file is plain PHP so you can edit values and (optionally) compute them
 * from environment variables.
 */
return [
    // ---------------------------------------------------------------------
    // Branding
    // ---------------------------------------------------------------------
    'site_name' => 'BBC99.bet',
    'tagline' => 'We are here to help. Start a chat and a real person replies.',
    'contact_email' => 'support@bbc99.bet',

    // Public base URL of this install, used by the landing page and the embed
    // snippet. Leave empty to auto-detect from the current request, and set it
    // explicitly once the site is behind a proxy or on a real domain.
    'base_url' => '',

    // ---------------------------------------------------------------------
    // Chat widget ("Online Consultant")
    // ---------------------------------------------------------------------
    // The public page loads this install's own widget.js, which talks to api.php
    // and the agent console at /admin/. No third party script is proxied.
    'widget_title' => 'BBC99.bet',
    'widget_subtitle' => '24/7 অনলাইন গ্রাহক পরিষেবা',
    'widget_greeting' => 'আমাদের BBC99.bet ওয়েবসাইটে আপনাকে স্বাগতম। নিচের মেনু থেকে আপনার সমস্যার বিষয় বেছে নিন, আমরা যেকোনো সময় আপনাকে সহায়তা করার জন্য অনলাইনে আছি।',

    // Scrolling notice pinned under the widget header. Leave empty to hide it.
    'widget_notice' => '⚠️【পেমেন্ট বিজ্ঞপ্তি】⚠️ বর্তমানে উত্তোলনের আবেদন বৃদ্ধি পাওয়ায় কিছু অ্যাকাউন্টে টাকা পৌঁছাতে সামান্য সময় লাগতে পারে। অনুগ্রহ করে ধৈর্য ধরে অপেক্ষা করুন। সকল উত্তোলনের অনুরোধ ধারাবাহিকভাবে প্রক্রিয়াধীন রয়েছে।',

    // Quick-reply buttons shown in an empty thread (= 🏠 প্রধান মেনু from
    // tree.txt / lib/menu.php). Clicking one sends it and opens the submenu.
    'widget_quick_replies' => [
        '🔷 জমা সমস্যা',
        '💳 প্রত্যাহারের সমস্যা',
        '❄️ অ্যাকাউন্ট প্রশ্ন',
        '🎁 সম্পর্কে প্রশ্ন ঘটনা',
    ],

    // Scripted auto-replies, keyed by the exact visitor message. These are the
    // fallback for anything outside the menu tree — lib/menu.php (tree.txt
    // flow) answers first, with submenu buttons and tutorial images.
    'auto_replies' => [
        '🔷 জমা সমস্যা' => 'জমা করতে: BBC99.bet-এ লগইন করে "ডিপোজিট" বাটনে চাপ দিন, পেমেন্ট চ্যানেল বেছে সঠিক পরিমাণ পাঠান। তারপর আপনার ইউজার আইডি, পরিমাণ ও পেমেন্টের স্ক্রিনশট/OTP এখানে পাঠান — যাচাই করে দ্রুত ব্যবস্থা নেওয়া হবে।',
        '💳 প্রত্যাহারের সমস্যা' => 'উত্তোলন করতে: "উত্তোলন" ফর্মে আপনার পেমেন্ট অ্যাকাউন্ট ও পরিমাণ ঠিকভাবে লিখুন। বর্তমানে আবেদন বেশি থাকায় কিছু পেমেন্টে সামান্য দেরি হতে পারে — সব অনুরোধ ধারাবাহিকভাবে প্রক্রিয়াধীন আছে। আপনার ইউজার আইডি পাঠালে স্ট্যাটাস দেখে জানিয়ে দেব।',
        '❄️ অ্যাকাউন্ট প্রশ্ন' => 'অ্যাকাউন্ট সাহায্য: লগইন/রেজিস্ট্রেশন বা পাসওয়ার্ড সমস্যা হলে আপনার ইউজার আইডি ও সমস্যার স্ক্রিনশট পাঠান। ছবিতে দেখানো ধাপগুলো অনুসরণ করুন — আটকে গেলে কোন ধাপে আটকেছেন লিখুন।',
        '🎁 সম্পর্কে প্রশ্ন ঘটনা' => 'বোনাস/ইভেন্ট তথ্য: চলতি অফার ও VIP শর্ত ছবিতে দেখে নিন। আপনার ইউজার আইডি পাঠালে আমরা আপনার জন্য প্রযোজ্য অফারটি দেখে জানিয়ে দেব।',
    ],

    // Tutorial images sent after the matching auto-reply text. Paths are
    // relative to the Support root; only files under assets/tutorials/ are
    // served, so nothing outside that directory can be injected here.
    'auto_reply_media' => [
        '🔷 জমা সমস্যা' => [
            'assets/tutorials/deposit-01.jpg',
            'assets/tutorials/deposit-02.jpg',
        ],
        '💳 প্রত্যাহারের সমস্যা' => [
            'assets/tutorials/withdraw-01.jpg',
            'assets/tutorials/withdraw-02.jpg',
        ],
        '❄️ অ্যাকাউন্ট প্রশ্ন' => [
            'assets/tutorials/account-01.jpg',
            'assets/tutorials/account-02.jpg',
        ],
        '🎁 সম্পর্কে প্রশ্ন ঘটনা' => [
            'assets/tutorials/promo-01.jpg',
        ],
    ],

    // Accent colour (stitch obsidian panel gradient base).
    'widget_color' => '#8083ff',
    // Public avatar shown in the widget header and next to agent messages.
    // Relative paths resolve against base_url; an absolute https:// URL works too.
    'widget_avatar' => 'assets/images/support.jpg',
    'widget_offline_note' => 'Leave us a message and we will get back to you by email.',
    'widget_position' => 'right', // "right" or "left"

    // The reference page shows the consultant panel already open on load.
    'widget_auto_open' => true,

    // "page": the chat fills the viewport, so the whole page *is* the message box
    //         (the reference consultant page). Used by index.php.
    // "bubble": the usual floating launcher in the corner. Use this for the
    //         embeddable snippet on other sites.
    'widget_mode' => 'page',

    // Poll interval (ms) used by the widget and the admin console.
    'poll_interval' => 4000,

    // Message limits.
    'max_message_length' => 4000,
    'max_messages_per_chat' => 500,

    // ---------------------------------------------------------------------
    // Attachments (images and video, sent by visitors and agents alike)
    // ---------------------------------------------------------------------
    // Largest single upload, in megabytes. PHP caps uploads separately, so raise
    // upload_max_filesize and post_max_size in php.ini to match.
    'max_upload_mb' => 25,

    // Accepted types, mapped to the extension used in the download filename. The
    // detected content type must appear here, never the browser's claim. SVG is
    // deliberately absent because it can carry script.
    'allowed_media' => [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'video/mp4' => 'mp4',
        'video/webm' => 'webm',
        'video/quicktime' => 'mov',
    ],

    // ---------------------------------------------------------------------
    // Admin access
    // ---------------------------------------------------------------------
    // Leave empty to create the admin password on first visit to /admin/
    // (it is then stored hashed in data/admin.json). Set a hash to pin it here
    // instead, e.g. from: php -r "echo password_hash('your-pass', PASSWORD_DEFAULT);"
    'admin_password_hash' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password: password

    // The login id that signs in as the owner above. This is the only account
    // that can manage the roster, and it is the only way into the console until
    // you add agent accounts under Settings. Agent accounts may not use it.
    'admin_login' => 'admin',

    // Login throttling: N failed attempts per IP inside the window (seconds).
    // Deliberately strict — a login guess is cheap to make and expensive to get
    // wrong, so this budget stays small.
    'login_max_attempts' => 8,
    'login_window' => 900,

    // ---------------------------------------------------------------------
    // Agent roster (console users)
    // ---------------------------------------------------------------------
    // Support departments. The demo roster and the per-agent "departments"
    // field use these exact names; the transfer list and the team page group
    // agents by them.
    'admin_departments' => ['জমা', 'উত্তোলন', 'অ্যাকাউন্ট'],

    // Password given to the demo agents created on the first console visit.
    // EMPTY DISABLES THE SEED: no agent accounts are created, and the only way
    // in is the owner login above. Set a password here only if you want the six
    // sample agents (kabir, rahim, tania, nasrin, sakib, mehedi) to be created
    // — they would then all share that password, which is published in the
    // README, so never leave it set on an install strangers can reach.
    'admin_demo_password' => '',

    // Quick-reply chips above the console's reply box. Clicking one appends its
    // text to the composer — the agent can still edit it before sending.
    'admin_canned_replies' => [
        'তথ্য যাচাই করা হচ্ছে, অনুগ্রহ করে ২ মিনিট অপেক্ষা করুন।',
        'অনুগ্রহ করে ট্রানজেকশনের স্ক্রিনশট বা TrxID পাঠান।',
        'আপনার অ্যাকাউন্টে টাকা সফলভাবে যোগ করা হয়েছে।',
        'আমাদের সাথে যোগাযোগ করার জন্য ধন্যবাদ।',
    ],

    // ---------------------------------------------------------------------
    // Public chat rate limits (per visitor IP)
    // ---------------------------------------------------------------------
    // Kept separate from the login limits above on purpose. A visitor tapping
    // through the menu tree sends a message per tap, so one real conversation
    // costs many requests: the chat budget has to be far larger than a login
    // budget. (Until these were split out, every limiter shared the login
    // numbers, which capped a visitor at 8 actions per 15 minutes.)
    'chat_start_max_attempts' => 20,  // new conversations…
    'chat_start_window' => 900,       // …per 15 minutes
    'chat_send_max_attempts' => 60,   // messages…
    'chat_send_window' => 30,         // …per 30 seconds
    // The window is also the penalty: a blocked visitor waits at most this long
    // before the counter resets and they can send again. Keep it short (30s) so
    // a glitch or an enthusiastic visitor is never locked out for minutes, and
    // raise max_attempts instead if you need more headroom.

    // ---------------------------------------------------------------------
    // Chat access
    // ---------------------------------------------------------------------
    // false (default): the conversation id is a 128-bit secret token held in the
    //   visitor's browser. Works when the widget is embedded on other domains.
    // true: a visitor can only read a conversation started in their own PHP
    //   session. Stricter, but only usable when the widget is served from the
    //   same domain as api.php (cookies are not sent cross-site).
    'bind_chat_to_session' => false,

    // ---------------------------------------------------------------------
    // Storage / misc
    // ---------------------------------------------------------------------
    'data_dir' => __DIR__ . '/data',
    'timezone' => 'UTC',
    'session_name' => 'supportcenter',

    // ---------------------------------------------------------------------
    // Landing page FAQ (rendered on the support center home page)
    // ---------------------------------------------------------------------
    'faq' => [
        [
            'q' => 'How do I get help?',
            'a' => 'Type a message, or tap one of the quick replies, and an agent will answer in the same window. You can send a photo or a video too.',
        ],
        [
            'q' => 'Do I need an account?',
            'a' => 'No. Just send a message — no name or email is required.',
        ],
        [
            'q' => 'Can I close the page while waiting?',
            'a' => 'Yes. Your conversation is stored on the server and the widget reconnects automatically when you come back on the same browser.',
        ],
        [
            'q' => 'Where is my data stored?',
            'a' => 'Conversations are stored as JSON files in the data/ directory on this server. No third party services are involved.',
        ],
    ],
];
