<?php

declare(strict_types=1);

/**
 * Menu tree for the public chat widget, transcribed from tree.txt and
 * rebranded from 1333bet to BBC99.bet.
 *
 * Matching is exact (trimmed) on the option label — tapping a button sends
 * its label back, and api.php resolves it with menu_find(). The old decorated
 * quick-reply labels are kept as aliases so in-flight conversations keep
 * working.
 *
 * Node shape:
 *   label    option text the visitor sends (button caption)
 *   text     agent reply shown when the node is opened
 *   media    list of assets/tutorials/* images sent after the text
 *   children nested submenu nodes (buttons shown after the text)
 *   action   'main'  -> show the main menu (🔰 button)
 *            'agent' -> hand over to a human agent (📞 button)
 */

const MENU_BACK = '🔰 প্রধান মেনুতে ফিরে যান';
const MENU_AGENT = '📞 গ্রাহক সেবা';

const MENU_TRANSFER_TEXT = 'আপনাকে গ্রাহক সেবায় সংযুক্ত করা হচ্ছে — অনুগ্রহ করে অপেক্ষা করুন, একজন কর্মী শীঘ্রই উত্তর দেবেন।';

/** Legacy decorated labels from before the tree.txt flow (kept as aliases). */
function menu_aliases(): array
{
    return [
        '🔷 জমা সমস্যা 🔷' => '🔷 জমা সমস্যা',
        '💳প্রত্যাহারের সমস্যা 💳' => '💳 প্রত্যাহারের সমস্যা',
        '❄️ অ্যাকাউন্ট প্রশ্ন ❄️' => '❄️ অ্যাকাউন্ট প্রশ্ন',
        '🎁 সম্পর্কে প্রশ্ন ঘটনা 🎁' => '🎁 সম্পর্কে প্রশ্ন ঘটনা',
        '🎁 বোনাস ও অফার' => '🎁 সম্পর্কে প্রশ্ন ঘটনা',
    ];
}

function menu_leaf(string $label, string $text, array $media = []): array
{
    return ['label' => $label, 'text' => $text, 'media' => $media, 'children' => []];
}

function menu_transfer(string $label): array
{
    return menu_leaf($label, MENU_TRANSFER_TEXT);
}

/** The full tree. Main-menu entries are the top-level children. */
function menu_tree(): array
{
    return [
        'label' => '🏠 প্রধান মেনু',
        'text' => '🏠 প্রধান মেনু — সাহায্যের বিষয় বেছে নিন:',
        'media' => [],
        'is_main' => true,
        'children' => [
            // --------------------------------------------------- deposit
            [
                'label' => '🔷 জমা সমস্যা',
                'text' => 'জমা সংক্রান্ত সাহায্য — নিচ থেকে বেছে নিন:',
                'media' => [],
                'children' => [
                    [
                        'label' => '☑️ জমা আসেনি',
                        'text' => 'জমা না এলে অনুগ্রহ করে: ① পেমেন্টের রসিদ পাঠান ② সঠিক ইউজারনেমের স্ক্রিনশট পাঠান। তথ্য পেলে যাচাই করে দ্রুত ব্যবস্থা নেওয়া হবে।',
                        'media' => [],
                        'children' => [
                            menu_leaf('পেমেন্টের রসিদ পাঠান', 'আপনার পেমেন্টের রসিদের ছবি (স্ক্রিনশট) এখানে পাঠান। সাথে আপনার ইউজারনেম লিখুন।'),
                            menu_leaf('সঠিক ইউজারনেমের স্ক্রিনশট পাঠান', 'সঠিক ইউজারনেম দেখা যায় এমন একটি স্ক্রিনশট পাঠান।'),
                            menu_transfer('গ্রাহক সেবায় স্থানান্তর'),
                        ],
                    ],
                    [
                        'label' => '⚙️ কিভাবে একটি আমানত করা',
                        'text' => 'BBC99.bet-এ জমা: ① "ডিপোজিট" বাটনে চাপ দিন ② পেমেন্ট চ্যানেল বেছে সঠিক পরিমাণ পাঠান ③ রসিদ সংরক্ষণ করুন। পয়েন্ট না এলে গ্রাহক সেবায় যোগাযোগ করুন।',
                        'media' => ['assets/tutorials/deposit-01.jpg', 'assets/tutorials/deposit-02.jpg'],
                        'children' => [],
                    ],
                    menu_transfer('☑️ অন্যান্য সমস্যা'),
                ],
            ],
            // --------------------------------------------------- withdraw
            [
                'label' => '💳 প্রত্যাহারের সমস্যা',
                'text' => 'উত্তোলন সংক্রান্ত সাহায্য — নিচ থেকে বেছে নিন:',
                'media' => [],
                'children' => [
                    [
                        'label' => '💳 আমার প্রত্যাহার আসেনি',
                        'text' => 'প্রত্যাহার না এলে সঠিক ইউজারনেম ও ব্যাংক তথ্য দিন — আমরা স্ট্যাটাস দেখছি। বর্তমানে আবেদন বেশি থাকায় কিছু পেমেন্টে সামান্য দেরি হতে পারে।',
                        'media' => [],
                        'children' => [
                            menu_leaf('সঠিক ইউজারনেম প্রদান', 'আপনার সঠিক ইউজারনেমটি লিখে পাঠান।'),
                            menu_leaf('ব্যাংক তথ্য প্রদান', 'আপনার ব্যাংক/পেমেন্ট অ্যাকাউন্ট তথ্য পাঠান।'),
                        ],
                    ],
                    menu_transfer('💳 আমি প্রত্যাহার করতে পারি না'),
                    [
                        'label' => '⚙️ কিভাবে প্রত্যাহার করতে হবে',
                        'text' => 'উত্তোলন: ফর্মে আপনার পেমেন্ট অ্যাকাউন্ট ও পরিমাণ ঠিকভাবে লিখে জমা দিন। ছবির নির্দেশাবলী অনুসরণ করুন।',
                        'media' => ['assets/tutorials/withdraw-01.jpg', 'assets/tutorials/withdraw-02.jpg'],
                        'children' => [],
                    ],
                    [
                        'label' => '💳 প্রত্যাহার ব্যর্থ হয়েছে',
                        'text' => 'ব্যর্থ উত্তোলনের জন্য অ্যাকাউন্ট আইডি নম্বর ও সদস্য অ্যাকাউন্ট নম্বর পাঠান।',
                        'media' => [],
                        'children' => [
                            menu_leaf('অ্যাকাউন্ট আইডি নম্বর', 'আপনার অ্যাকাউন্ট আইডি নম্বরটি লিখে পাঠান।'),
                            menu_leaf('সদস্য অ্যাকাউন্ট নম্বর', 'আপনার সদস্য অ্যাকাউন্ট নম্বরটি লিখে পাঠান।'),
                        ],
                    ],
                    [
                        'label' => '💳 অন্যান্য সমস্যা',
                        'text' => 'অন্যান্য উত্তোলন সমস্যার জন্য অ্যাকাউন্ট আইডি নম্বর ও সদস্য অ্যাকাউন্ট নম্বর পাঠান।',
                        'media' => [],
                        'children' => [
                            menu_leaf('অ্যাকাউন্ট আইডি নম্বর', 'আপনার অ্যাকাউন্ট আইডি নম্বরটি লিখে পাঠান।'),
                            menu_leaf('সদস্য অ্যাকাউন্ট নম্বর', 'আপনার সদস্য অ্যাকাউন্ট নম্বরটি লিখে পাঠান।'),
                        ],
                    ],
                ],
            ],
            // --------------------------------------------------- account
            [
                'label' => '❄️ অ্যাকাউন্ট প্রশ্ন',
                'text' => 'অ্যাকাউন্ট সংক্রান্ত সাহায্য — নিচ থেকে বেছে নিন:',
                'media' => [],
                'children' => [
                    [
                        'label' => '❄️ লগইন পাসওয়ার্ড ভুলে গেছেন',
                        'text' => 'যাচাইয়ের জন্য পাঠান: ① লগইন অ্যাকাউন্টের নাম ② সংযুক্ত ই-ওয়ালেটের ছবি ③ সর্বশেষ জমার রসিদ।',
                        'media' => [],
                        'children' => [
                            menu_leaf('লগইন অ্যাকাউন্টের নাম', 'আপনার লগইন অ্যাকাউন্টের নাম লিখে পাঠান।'),
                            [
                                'label' => 'সংযুক্ত ই-ওয়ালেটের ছবি',
                                'text' => 'ই-ওয়ালেটের ছবি পাঠান — মালিকের সম্পূর্ণ নাম, অ্যাকাউন্ট নম্বর ও বর্তমান সময় দেখা যেতে হবে।',
                                'media' => [],
                                'children' => [
                                    menu_leaf('অ্যাকাউন্ট মালিকের সম্পূর্ণ নাম', 'অ্যাকাউন্ট মালিকের সম্পূর্ণ নাম লিখে পাঠান।'),
                                    menu_leaf('অ্যাকাউন্ট নম্বর', 'আপনার অ্যাকাউন্ট নম্বরটি লিখে পাঠান।'),
                                    menu_leaf('বর্তমান সময়', 'বর্তমান সময় দেখা যায় এমন একটি স্ক্রিনশট পাঠান।'),
                                ],
                            ],
                            menu_leaf('সর্বশেষ জমা করা অর্থের লেনদেনের রসিদ', 'সর্বশেষ জমার লেনদেনের রসিদের ছবি পাঠান।'),
                        ],
                    ],
                    [
                        'label' => '⚜️ নতুন টাকা উত্তোলনের পাসওয়ার্ড প্রদান',
                        'text' => 'উত্তোলন পাসওয়ার্ডের জন্য পাঠান: ① লগইন অ্যাকাউন্টের নাম ② সংযুক্ত ই-ওয়ালেটের ছবি ③ সর্বশেষ জমার রসিদ।',
                        'media' => [],
                        'children' => [
                            menu_leaf('লগইন অ্যাকাউন্টের নাম', 'আপনার লগইন অ্যাকাউন্টের নাম লিখে পাঠান।'),
                            menu_leaf('সংযুক্ত ই-ওয়ালেটের ছবি', 'ই-ওয়ালেটের ছবি পাঠান — মালিকের সম্পূর্ণ নাম, অ্যাকাউন্ট নম্বর ও বর্তমান সময় দেখা যেতে হবে।'),
                            menu_leaf('সর্বশেষ জমা করা অর্থের লেনদেনের রসিদ', 'সর্বশেষ জমার লেনদেনের রসিদের ছবি পাঠান।'),
                        ],
                    ],
                    [
                        'label' => '⚙️ একটি গেম অ্যাকাউন্ট নিবন্ধন করুন',
                        'text' => 'ছবিতে দেওয়া নির্দেশাবলী অনুসরণ করে আপনার গেম অ্যাকাউন্ট নিবন্ধন করুন।',
                        'media' => ['assets/tutorials/account-01.jpg', 'assets/tutorials/account-02.jpg'],
                        'children' => [],
                    ],
                    menu_leaf('❄️ এখনও খেলা সমস্যা', 'খেলার সমস্যার বিস্তারিত লিখে পাঠান (কোন গেম, কী হয়) — গ্রাহক সেবা দেখছে।'),
                    menu_transfer('❄️ গ্রাহক সেবা'),
                ],
            ],
            // --------------------------------------------------- events
            [
                'label' => '🎁 সম্পর্কে প্রশ্ন ঘটনা',
                'text' => 'ইভেন্ট ও বোনাস — কোনটি সম্পর্কে জানতে চান?',
                'media' => [],
                'children' => [
                    menu_leaf('✔️ বন্ধুকে আমন্ত্রণ জানান বোনাস', 'বন্ধু আমন্ত্রণ বোনাস: আপনার লিংকে যোগ দেওয়া বন্ধুর টার্নওভারের উপর আয়। আমন্ত্রণ সংখ্যা বাড়লে বোনাস বাড়ে।'),
                    menu_leaf('✔️ ভিআইপি সদস্য সুবিধা', 'সদস্য বোনাস ও আপগ্রেড সুবিধা ছবিতে দেখে নিন। আপনার ইউজারনেম পাঠালে বর্তমান VIP স্তর জানিয়ে দেব।', ['assets/tutorials/promo-01.jpg']),
                    menu_leaf('✔️ এখনই APP ডাউনলোড করুন', 'APP অফার: ডিপোজিট, টার্নওভার ও পুরস্কারের শর্ত প্রযোজ্য। APP থেকে অংশ নিন।'),
                    menu_leaf('✔️ ক্ষতি পুনরুদ্ধার', 'ক্ষতি পুনরুদ্ধার অফারের শর্ত ও হার জানতে আপনার ইউজারনেম পাঠান — উপযুক্ততা দেখে জানাব।'),
                    menu_leaf('✔️ গোল্ডেন এগ স্ম্যাশিং', 'গোল্ডেন এগ: শর্ত পূরণে প্রতিদিন সুযোগ। আপনার ইউজারনেম পাঠালে আজকের সুযোগ জানিয়ে দেব।'),
                    menu_leaf('🧧 লাল খাম', 'লাল খাম ইভেন্টের সময় ও শর্ত জানতে গ্রাহক সেবায় আপনার ইউজারনেম পাঠান।'),
                    menu_leaf('✔️ iPhone Duo জিততে দৈনিক ডিপোজিট ড্র', 'দৈনিক ডিপোজিট ড্র: প্রতিদিনের জমায় একটি করে ড্র সুযোগ। শর্ত প্রযোজ্য।'),
                    menu_leaf('✔️ প্রতিদিন স্লট বাজির বোনাস', 'দৈনিক স্লট বাজি বোনাস: টার্নওভার শর্ত পূরণে প্রতিদিন দাবি করুন।'),
                    menu_leaf('✔️ সদস্য কার্নিভাল দিবস', 'কার্নিভাল দিবসের বিশেষ পুরস্কার ও শর্ত ইভেন্ট পেজে দেখুন।'),
                    menu_leaf('✔️ রহস্য বোনাস', 'রহস্য বোনাস এলোমেলোভাবে দেওয়া হয় — অ্যাকাউন্ট সক্রিয় রাখুন।'),
                    menu_leaf('✔️ 3% পর্যন্ত ছাড়', '৩% পর্যন্ত ছাড়: প্রযোজ্য চ্যানেলে জমায় স্বয়ংক্রিয়। শর্ত প্রযোজ্য।'),
                    menu_leaf('✔️ চ্যানেলে অংশীদারত্বের', 'চ্যানেল অংশীদারত্ব: বিস্তারিত শর্তের জন্য গ্রাহক সেবায় যোগাযোগ করুন।'),
                    menu_leaf('✔️ 1.1.1.1 ডাউনলোড করুন', '1.1.1.1 APP ডাউনলোড করে সংযোগ স্থিতিশীল রাখুন।'),
                ],
            ],
            // ------------------------------------------- global options
            ['label' => MENU_AGENT, 'text' => MENU_TRANSFER_TEXT, 'media' => [], 'children' => [], 'action' => 'agent'],
            ['label' => MENU_BACK, 'text' => '', 'media' => [], 'children' => [], 'action' => 'main'],
        ],
    ];
}

/** Depth-first exact-match lookup (aliases resolved first). */
function menu_find(string $message): ?array
{
    $needle = trim($message);
    if ($needle === '') {
        return null;
    }
    $aliases = menu_aliases();
    if (isset($aliases[$needle])) {
        $needle = $aliases[$needle];
    }
    return menu_search(menu_tree(), $needle);
}

function menu_search(array $node, string $needle): ?array
{
    if (trim((string) ($node['label'] ?? '')) === $needle) {
        return $node;
    }
    foreach ($node['children'] ?? [] as $child) {
        if (!is_array($child)) {
            continue;
        }
        $found = menu_search($child, $needle);
        if ($found !== null) {
            return $found;
        }
    }
    return null;
}

/** Labels of the main menu (the four categories). */
function menu_main_labels(): array
{
    $out = [];
    foreach (menu_tree()['children'] ?? [] as $child) {
        $label = (string) ($child['label'] ?? '');
        if ($label === '' || $label === MENU_AGENT || $label === MENU_BACK) {
            continue;
        }
        $out[] = $label;
    }
    return $out;
}

/**
 * Button labels to show after a node: its children, plus the 🔁 common
 * options (📞 + 🔰) for every node except the main menu itself.
 *
 * @return list<string>
 */
function menu_options_for(array $node): array
{
    if (($node['action'] ?? '') === 'main') {
        return menu_main_labels();
    }
    if (!empty($node['is_main'])) {
        $out = [];
        foreach ($node['children'] ?? [] as $child) {
            $label = (string) ($child['label'] ?? '');
            if ($label === '' || $label === MENU_AGENT || $label === MENU_BACK) {
                continue;
            }
            $out[] = $label;
        }
        return $out;
    }
    $out = [];
    foreach ($node['children'] ?? [] as $child) {
        $label = (string) ($child['label'] ?? '');
        if ($label !== '') {
            $out[] = $label;
        }
    }
    $out[] = MENU_AGENT;
    $out[] = MENU_BACK;
    return $out;
}

/** Reply text for a node (the 🔰 action reuses the main menu text). */
function menu_text_for(array $node): string
{
    if (($node['action'] ?? '') === 'main') {
        return (string) (menu_tree()['text'] ?? '');
    }
    return (string) ($node['text'] ?? '');
}

/** Tutorial images for a node. */
function menu_media_for(array $node): array
{
    $media = $node['media'] ?? [];
    return is_array($media) ? array_values(array_filter($media, 'is_string')) : [];
}
