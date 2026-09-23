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

    // Salesmartly-style public page (mirrors https://hskxtmcmhb.com/)
    // ---------------------------------------------------------------------
    // Plugin id shown on the public page. The reference site uses a Salesmartly
    // plugin id; set your own here if you have one, otherwise the page still
    // renders the same launcher shape.
    'plugin_id' => 'g1l7xa9',

    // URL of the widget JS loaded by the public page. The reference site
    // (https://hskxtmcmhb.com/) uses this value:
    'widget_js_src' => 'https://hskxtmcmhb.com/chat/widget-v2/code/install.js',

    // Optional background image URL for the public page container.
    'public_bg_image' => '',

    // ---------------------------------------------------------------------
    // Chat widget ("Online Consultant")
    // ---------------------------------------------------------------------
    'widget_title' => 'Online Consultant',
    'widget_subtitle' => 'Typically replies in a few minutes',
    'widget_greeting' => 'Hi! How can we help you today?',
    'widget_color' => '#1f6feb',
    'widget_offline_note' => 'Leave us a message and we will get back to you by email.',
    'widget_position' => 'right', // "right" or "left"

    // Poll interval (ms) used by the widget and the admin console.
    'poll_interval' => 4000,

    // Message limits.
    'max_message_length' => 4000,
    'max_messages_per_chat' => 500,

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
            'a' => 'Click the chat button in the bottom corner, leave your name and question, and an agent will reply in the same window.',
        ],
        [
            'q' => 'Do I need an account?',
            'a' => 'No. You only provide a name, and optionally an email address if you would like a copy of the conversation.',
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
