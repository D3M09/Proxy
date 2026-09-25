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
    'site_name' => 'Support Center',
    'tagline' => 'We are here to help. Start a chat and a real person replies.',
    'contact_email' => 'support@example.com',

    // Public base URL of this install, used by the landing page and the embed
    // snippet. Leave empty to auto-detect from the current request, and set it
    // explicitly once the site is behind a proxy or on a real domain.
    'base_url' => '',

    // ---------------------------------------------------------------------
    // Chat widget ("Online Consultant")
    // ---------------------------------------------------------------------
    // The public page loads this install's own widget.js, which talks to api.php
    // and the agent console at /admin/. No third party script is proxied.
    'widget_title' => '1333betws',
    'widget_subtitle' => '24/7 অনলাইন গ্রাহক পরিষেবা',
    'widget_greeting' => 'আমাদের 1333bet ওয়েবসাইটে আপনাকে স্বাগতম। যদি আপনার সাহায্যের প্রয়োজন হয়, তাহলে কেবল এই বার্তার উত্তর দিন এবং আমরা যেকোনো সময় আপনাকে সহায়তা করার জন্য অনলাইনে থাকব।',

    // Scrolling notice pinned under the widget header. Leave empty to hide it.
    'widget_notice' => '⚠️【পেমেন্ট বিজ্ঞপ্তি】⚠️ বর্তমানে উত্তোলনের আবেদন বৃদ্ধি পাওয়ায় কিছু অ্যাকাউন্টে টাকা পৌঁছাতে সামান্য সময় লাগতে পারে। অনুগ্রহ করে ধৈর্য ধরে অপেক্ষা করুন। সকল উত্তোলনের অনুরোধ ধারাবাহিকভাবে প্রক্রিয়াধীন রয়েছে।',

    // Quick-reply buttons shown in an empty thread. Clicking one sends it.
    'widget_quick_replies' => [
        '🔷 জমা সমস্যা 🔷',
        '💳প্রত্যাহারের সমস্যা 💳',
        '❄️ অ্যাকাউন্ট প্রশ্ন ❄️',
        '🎁 সম্পর্কে প্রশ্ন ঘটনা 🎁',
    ],

    // Scripted auto-replies, keyed by the exact visitor message. The template
    // labels above are the keys, so clicking a template gets an answer straight
    // away with no agent online — the same behaviour as the reference widget.
    //
    // NOTE: the reference capture only exposes the template buttons, not the
    // answers they produce, so the reply texts below are placeholders. Edit them
    // to match your real scripts, or empty the array to turn auto-reply off.
    'auto_replies' => [
        '🔷 জমা সমস্যা 🔷' => 'আপনার জমা সংক্রান্ত সমস্যার জন্য আমরা দুঃখিত। অনুগ্রহ করে আপনার ইউজার আইডি ও জমার পরিমাণ জানান — যাচাই করে দ্রুত ব্যবস্থা নেওয়া হবে।',
        '💳প্রত্যাহারের সমস্যা 💳' => 'আপনার উত্তোলনের অনুরোধ ধারাবাহিকভাবে প্রক্রিয়া করা হচ্ছে। অনুগ্রহ করে ধৈর্য ধরে অপেক্ষা করুন, অথবা আপনার ইউজার আইডি জানালে আমরা স্ট্যাটাস দেখে জানিয়ে দেব।',
        '❄️ অ্যাকাউন্ট প্রশ্ন ❄️' => 'আপনার অ্যাকাউন্ট সংক্রান্ত যেকোনো প্রশ্নে সহায়তা করতে আমরা প্রস্তুত। অনুগ্রহ করে সমস্যাটি বিস্তারিত লিখুন।',
        '🎁 সম্পর্কে প্রশ্ন ঘটনা 🎁' => 'আপনার প্রশ্নটি আমাদের কাছে পৌঁছেছে। বিস্তারিত জানালে আমাদের টিম দ্রুত উত্তর দেবে।',
    ],

    // Accent colour (Arco blue, as used by the reference widget).
    'widget_color' => '#1762f6',
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

    // Login throttling: N failed attempts per IP inside the window (seconds).
    'login_max_attempts' => 8,
    'login_window' => 900,

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
