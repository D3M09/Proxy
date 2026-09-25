<?php

declare(strict_types=1);

/**
 * Agent console: shared-password login, conversation queue, replies.
 */

require dirname(__DIR__) . '/lib/bootstrap.php';
require __DIR__ . '/partials.php';

header('X-Robots-Tag: noindex');

// The console holds every visitor conversation plus the roster, so it is locked
// down hardest: no framing, no referrer leakage, and a CSP that permits only the
// console's own nonce-bearing script — an injected <script> cannot execute.
// style-src keeps 'unsafe-inline' because the markup carries style="…"
// attributes; script-src deliberately does not.
security_headers("'none'", [
    'default-src' => "'self'",
    'script-src' => "'nonce-" . csp_nonce() . "'",
    'style-src' => "'self' 'unsafe-inline'",
    'img-src' => "'self' data: http: https:",
    'media-src' => "'self'",
    'connect-src' => "'self'",
    'font-src' => "'self'",
    'form-action' => "'self'",
]);

$adminFile = rtrim((string) $config['data_dir'], "/\\") . DIRECTORY_SEPARATOR . 'admin.json';
$pollMs = max(2000, (int) ($config['poll_interval'] ?? 4000));

// ---------------------------------------------------------------------------
// Admin password storage (data/admin.json), unless pinned in config.php
// ---------------------------------------------------------------------------

function load_admin_hash(string $file): string
{
    if (!is_file($file)) {
        return '';
    }
    $raw = @file_get_contents($file);
    if ($raw === false) {
        return '';
    }
    $data = json_decode($raw, true);
    return is_array($data) ? (string) ($data['password_hash'] ?? '') : '';
}

function save_admin_hash(string $file, string $hash): bool
{
    $json = json_encode(['password_hash' => $hash, 'updated_at' => time()], JSON_PRETTY_PRINT);
    if ($json === false || @file_put_contents($file, $json, LOCK_EX) === false) {
        return false;
    }
    @chmod($file, 0600);
    return true;
}

/** Passwords are read raw: trimming could silently change a valid password. */
function raw_post(string $key, int $max = 200): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? mb_substr(str_replace("\0", '', $value), 0, $max) : '';
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $message];
}

function redirect_to(string $suffix = ''): never
{
    // Use the current script's absolute path so the redirect stays inside
    // /admin/ even when the console was opened as ".../admin" (no trailing
    // slash / no index.php). A relative "Location: index.php..." would then
    // resolve to ".../index.php" — the public customer view.
    $script = (string) ($_SERVER['SCRIPT_NAME'] ?? 'index.php');
    if (substr($script, -4) !== '.php') {
        $script = rtrim($script, '/') . '/index.php';
    }
    if ($script === '') {
        $script = 'index.php';
    }
    header('Location: ' . $script . $suffix, true, 303);
    exit;
}

$ownerLoginId = mb_strtolower(trim((string) ($config['admin_login'] ?? 'admin')));
$configuredHash = trim((string) ($config['admin_password_hash'] ?? ''));
$storedHash = $configuredHash !== '' ? $configuredHash : load_admin_hash($adminFile);
$needsSetup = $storedHash === '';
$isAgent = ($_SESSION['agent'] ?? false) === true;

$flash = is_array($_SESSION['flash'] ?? null) ? $_SESSION['flash'] : null;
unset($_SESSION['flash']);

$minPassword = 10;
$limiter = $throttle('login');
$throttleKey = 'login:' . client_ip();

// ---------------------------------------------------------------------------
// Agent roster: departments, the demo seed, and who is signed in right now
// ---------------------------------------------------------------------------

$departments = is_array($config['admin_departments'] ?? null)
    ? array_values(array_filter(
        array_map(static fn ($d): string => is_string($d) ? trim($d) : '', $config['admin_departments']),
        static fn (string $d): bool => $d !== ''
    ))
    : [];
$demoPassword = trim((string) ($config['admin_demo_password'] ?? ''));

// First visit: create the demo roster, so the console starts with people in it.
// seedDemo() does nothing once data/agents.json exists.
$agents->seedDemo($departments, $demoPassword);

// Departments posted from a form, limited to the configured list.
$postDepartments = static function () use ($departments): array {
    $sent = $_POST['departments'] ?? null;
    if (!is_array($sent)) {
        return [];
    }
    $sent = array_values(array_filter(
        array_map(static fn ($d): string => is_string($d) ? trim($d) : '', $sent),
        static fn (string $d): bool => $d !== ''
    ));
    return $departments === [] ? $sent : array_values(array_intersect($sent, $departments));
};

// The owner's login id is reserved for the owner: an agent account may not
// shadow it, or the two would be indistinguishable at sign-in.
$isReservedHandle = static function (string $handle) use ($ownerLoginId): bool {
    return $ownerLoginId !== '' && mb_strtolower(trim($handle)) === $ownerLoginId;
};

$me = null;          // the signed-in agent's roster row (null for the owner)
$meIsOwner = false;  // the shared owner login: may manage the roster
if ($isAgent) {
    $sessionAgentId = (string) ($_SESSION['agent_id'] ?? '');
    if ($sessionAgentId === '') {
        $meIsOwner = true;
    } else {
        $me = $agents->find($sessionAgentId);
        if ($me === null) {
            // The account was removed while this session stayed signed in.
            $_SESSION = [];
            session_destroy();
            redirect_to();
        }
        $agents->touch($sessionAgentId);
    }
}

$meId = $me === null ? '' : (string) $me['id'];
$meDepartments = $me === null ? [] : (array) $me['departments'];
$meCard = $me ?? ['id' => 'owner', 'name' => console_label('owner'), 'photo' => '', 'departments' => []];
$agentMap = $agents->byId();
$agentList = $agents->all();
$canManageAgents = $isAgent && $meIsOwner;
$onlineCount = 0;
foreach ($agentList as $agentRow) {
    if ($agents->isOnline($agentRow)) {
        $onlineCount++;
    }
}

// ---------------------------------------------------------------------------
// Security self-check
//
// A misconfigured install should be loud, not silent. Each entry is a
// console_label() key holding trusted markup (no visitor input reaches it).
// ---------------------------------------------------------------------------

/** @var list<string> $securityWarnings */
$securityWarnings = [];

if ($isAgent) {
    // The conversation store must never be fetchable as static text.
    // data/.htaccess and .htaccess are Apache-only; the built-in server needs
    // router.php, which flags itself with SC_ROUTER.
    if (PHP_SAPI === 'cli-server' && !defined('SC_ROUTER')) {
        $securityWarnings[] = 'security_data_open';
    }

    // config.php pins a bcrypt hash, so a shipped default can only be spotted by
    // trying it. Cached per hash, so the bcrypt cost is paid once per session
    // rather than on every page load.
    if ($meIsOwner && $storedHash !== '') {
        $ownerHashKey = hash('sha256', $storedHash);
        if (($_SESSION['owner_weak_hash'] ?? '') !== $ownerHashKey) {
            $ownerWeak = false;
            foreach (['password', 'admin', 'admin123', 'demo1234'] as $guess) {
                if (password_verify($guess, $storedHash)) {
                    $ownerWeak = true;
                    break;
                }
            }
            $_SESSION['owner_weak_hash'] = $ownerHashKey;
            $_SESSION['owner_weak'] = $ownerWeak;
        }
        if (($_SESSION['owner_weak'] ?? false) === true) {
            $securityWarnings[] = 'security_weak_owner';
        }
    }

    // Demo agents all share one password, and that password is in the README.
    foreach ($agentList as $agentRow) {
        if (($agentRow['demo'] ?? false) === true) {
            $securityWarnings[] = 'demo_password_warning';
            break;
        }
    }
}

// ---------------------------------------------------------------------------
// POST actions
// ---------------------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = param('action', 20);
    $returnId = param('return_id', 64);

    // ---------------------------------------------------------------- login
    if ($action === 'login') {
        if ($needsSetup) {
            set_flash('error', 'প্রথমে একটি অ্যাডমিন পাসওয়ার্ড তৈরি করুন।');
            redirect_to();
        }

        $handle = raw_post('handle', 60);
        $password = raw_post('password', 300);

        // Throttle the account as well as the IP. Per-IP alone only stops one
        // machine: a distributed attacker can keep guessing one agent's password
        // from many addresses, so each account gets its own budget too.
        $accountKey = 'login-account:' . ($handle === '' ? 'blank' : mb_strtolower($handle));
        $limitKeys = [$throttleKey, $accountKey];

        foreach ($limitKeys as $limitKey) {
            $wait = $limiter->retryAfter($limitKey);
            if ($wait > 0) {
                set_flash('error', 'অনেকবার ভুল চেষ্টা হয়েছে। ' . max(1, (int) ceil($wait / 60)) . ' মিনিট পরে আবার চেষ্টা করুন।');
                redirect_to();
            }
        }

        // One handle signs in as the owner (admin_login from config.php); any
        // other handle is looked up in the roster. The two are checked in that
        // order, so the owner id always wins and no agent can shadow it.
        $isOwnerLogin = $ownerLoginId !== '' && mb_strtolower($handle) === $ownerLoginId;
        $ownerAccount = $isOwnerLogin && $storedHash !== '' && password_verify($password, $storedHash);
        $agentAccount = ($handle === '' || $isOwnerLogin) ? null : $agents->verify($handle, $password);

        if ($ownerAccount || $agentAccount !== null) {
            session_regenerate_id(true);
            $_SESSION['agent'] = true;
            $_SESSION['agent_at'] = time();
            // An empty agent_id is what marks the session as the owner.
            $_SESSION['agent_id'] = $agentAccount === null ? '' : (string) $agentAccount['id'];
            $_SESSION['agent_name'] = $agentAccount === null ? console_label('owner') : (string) $agentAccount['name'];
            foreach ($limitKeys as $limitKey) {
                $limiter->clear($limitKey);
            }
            set_flash('ok', 'সাইন ইন সম্পন্ন হয়েছে।');
            redirect_to();
        }

        foreach ($limitKeys as $limitKey) {
            $limiter->hit($limitKey);
        }
        set_flash('error', 'নাম বা আইডি অথবা পাসওয়ার্ড সঠিক নয়।');
        redirect_to();
    }

    // ---------------------------------------------------------------- setup
    if ($action === 'setup') {
        if (!$needsSetup) {
            redirect_to();
        }
        $password = raw_post('password', 300);
        $confirm = raw_post('password2', 300);

        if (mb_strlen($password) < $minPassword) {
            set_flash('error', 'কমপক্ষে ' . $minPassword . ' অক্ষরের পাসওয়ার্ড দিন।');
            redirect_to();
        }
        if ($password !== $confirm) {
            set_flash('error', 'দুইটি পাসওয়ার্ড মিলছে না।');
            redirect_to();
        }
        if (!save_admin_hash($adminFile, password_hash($password, PASSWORD_DEFAULT))) {
            set_flash('error', $adminFile . ' ফাইলে লেখা যায়নি। data/ ফোল্ডারে লেখার অনুমতি আছে কিনা দেখুন।');
            redirect_to();
        }

        session_regenerate_id(true);
        $_SESSION['agent'] = true;
        set_flash('ok', 'অ্যাডমিন পাসওয়ার্ড তৈরি হয়েছে। নিরাপদ জায়গায় রেখে দিন — এটি পুনরুদ্ধার করা যায় না।');
        redirect_to();
    }

    // ------------------------------------------------------- password change
    if ($action === 'password') {
        if (!$isAgent) {
            redirect_to();
        }
        $current = raw_post('current', 300);
        $password = raw_post('password', 300);
        $confirm = raw_post('password2', 300);

        if (!password_verify($current, $storedHash)) {
            set_flash('error', 'বর্তমান পাসওয়ার্ডটি সঠিক নয়।');
        } elseif (mb_strlen($password) < $minPassword) {
            set_flash('error', 'কমপক্ষে ' . $minPassword . ' অক্ষরের পাসওয়ার্ড দিন।');
        } elseif ($password !== $confirm) {
            set_flash('error', 'দুইটি পাসওয়ার্ড মিলছে না।');
        } elseif ($configuredHash !== '') {
            set_flash('error', 'পাসওয়ার্ডটি config.php (admin_password_hash) এ পিন করা আছে; সেটি পরিবর্তন করুন।');
        } elseif (save_admin_hash($adminFile, password_hash($password, PASSWORD_DEFAULT))) {
            session_regenerate_id(true);
            $_SESSION['agent'] = true;
            set_flash('ok', 'পাসওয়ার্ড আপডেট হয়েছে।');
        } else {
            set_flash('error', 'নতুন পাসওয়ার্ড সংরক্ষণ করা যায়নি।');
        }
        redirect_to('?view=settings');
    }

    // --------------------------------------------------------------- logout
    if ($action === 'logout') {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();
        redirect_to();
    }

    // ---------------------------------------------------------------- reply
    if ($action === 'reply') {
        if (!$isAgent) {
            redirect_to();
        }
        $id = param('id', 64);
        $message = param('message', (int) ($config['max_message_length'] ?? 4000));

        if (preg_match('/^[a-f0-9]{32}$/', $id) !== 1) {
            set_flash('error', 'কথোপকথনটি আর নেই।');
            redirect_to();
        }

        try {
            $upload = save_upload('file', $config);
        } catch (RuntimeException $e) {
            set_flash('error', $e->getMessage());
            redirect_to('?id=' . $id);
        }

        // An attachment with no caption is a valid reply.
        if ($message === '' && $upload === null) {
            set_flash('error', 'উত্তর লিখুন অথবা ছবি/ভিডিও সংযুক্ত করুন।');
            redirect_to('?id=' . $id);
        }

        $updated = $store->addMessage($id, Store::ROLE_AGENT, $message, $upload);
        if ($updated === null) {
            set_flash('error', 'উত্তর সংরক্ষণ করা যায়নি।');
            redirect_to('?id=' . $id);
        }
        $store->poll($id, 0, Store::ROLE_AGENT); // the agent is looking at it now
        redirect_to('?id=' . $id);
    }

    // -------------------------------------------------------- status/delete
    if ($action === 'status' || $action === 'delete') {
        if (!$isAgent) {
            redirect_to();
        }
        $id = param('id', 64);
        if (preg_match('/^[a-f0-9]{32}$/', $id) === 1) {
            if ($action === 'status') {
                $status = param('status', 10) === Store::STATUS_CLOSED ? Store::STATUS_CLOSED : Store::STATUS_OPEN;
                $store->setStatus($id, $status);
                set_flash('ok', $status === Store::STATUS_CLOSED ? 'কথোপকথন বন্ধ করা হয়েছে।' : 'কথোপকথন আবার চালু করা হয়েছে।');
                redirect_to('?id=' . $id);
            }
            if ($store->delete($id)) {
                set_flash('ok', 'কথোপকথন মুছে ফেলা হয়েছে।');
            } else {
                set_flash('error', 'কথোপকথনটি আর নেই।');
            }
        }
        redirect_to();
    }

    // -------------------------------------------------------------- transfer
    if ($action === 'transfer') {
        if (!$isAgent) {
            redirect_to();
        }
        $id = param('id', 64);
        if (preg_match('/^[a-f0-9]{32}$/', $id) !== 1) {
            set_flash('error', 'কথোপকথন আর নেই।');
            redirect_to();
        }

        $target = param('agent', 12);
        if ($target === '') {
            $store->setAssignee($id, '', $meId);
            set_flash('ok', console_label('transferred_cleared'));
        } elseif (!isset($agentMap[$target])) {
            set_flash('error', console_label('agent_not_found'));
        } else {
            $store->setAssignee($id, $target, $meId);
            set_flash('ok', console_label('transferred_to') . ' ' . $agentMap[$target]['name']);
        }
        redirect_to('?id=' . $id);
    }

    // ---------------------------------------------------------- agent roster
    if (str_starts_with($action, 'agent-')) {
        if (!$isAgent) {
            redirect_to();
        }

        // Only the owner login may create, edit or remove accounts. An agent may
        // change their own password and nothing else — otherwise any agent could
        // take over the roster.
        $isOwnPassword = $action === 'agent-password' && $meId !== '' && param('id', 12) === $meId;
        if (!$canManageAgents && !$isOwnPassword) {
            set_flash('error', console_label('agent_owner_only'));
            redirect_to('?view=settings');
        }

        if ($action === 'agent-add') {
            $password = raw_post('password', 300);
            if (mb_strlen($password) < $minPassword) {
                set_flash('error', 'কমপক্ষে ' . $minPassword . ' অক্ষরের পাসওয়ার্ড দিন।');
            } elseif ($isReservedHandle(param('handle', 40))) {
                set_flash('error', console_label('agent_handle_reserved'));
            } elseif ($agents->add(param('name', 80), param('handle', 40), $password, $postDepartments(), param('photo', 300)) === null) {
                set_flash('error', console_label('agent_handle_taken'));
            } else {
                set_flash('ok', console_label('agent_added'));
            }
            redirect_to('?view=settings');
        }

        $agentId = param('id', 12);
        if ($agentId !== '' && $agents->find($agentId) === null) {
            set_flash('error', console_label('agent_not_found'));
            redirect_to('?view=settings');
        }

        if ($action === 'agent-delete') {
            if ($agents->remove($agentId)) {
                // Never leave a chat pointing at an account that no longer exists.
                $store->unassignAll($agentId);
                set_flash('ok', console_label('agent_removed'));
            } else {
                set_flash('error', console_label('agent_not_found'));
            }
            redirect_to('?view=settings');
        }

        $newPassword = raw_post('password', 300);
        if ($newPassword !== '' && mb_strlen($newPassword) < $minPassword) {
            set_flash('error', 'কমপক্ষে ' . $minPassword . ' অক্ষরের পাসওয়ার্ড দিন।');
            redirect_to('?view=settings');
        }

        $fields = ['password' => $newPassword];
        if ($action === 'agent-save') {
            $fields = [
                'name' => param('name', 80),
                'handle' => param('handle', 40),
                'photo' => param('photo', 300),
                'departments' => $postDepartments(),
                'password' => $newPassword,
            ];
        }

        if ($action === 'agent-save' && $isReservedHandle((string) ($fields['handle'] ?? ''))) {
            set_flash('error', console_label('agent_handle_reserved'));
            redirect_to('?view=settings');
        }

        if (!$agents->update($agentId, $fields)) {
            set_flash('error', console_label('agent_handle_taken'));
        } elseif ($newPassword !== '') {
            set_flash('ok', console_label('agent_password_set'));
        } else {
            set_flash('ok', console_label('agent_updated'));
        }
        redirect_to('?view=settings');
    }

    set_flash('error', 'অজানা অনুরোধ।');
    redirect_to();
}

// ---------------------------------------------------------------------------
// Selected conversation + list
// ---------------------------------------------------------------------------

$view = param('view', 20, true);
$filter = param('filter', 12, true);
if (!in_array($filter, console_filters(), true)) {
    $filter = 'all';
}
$agentFilter = param('agent', 12, true); // ?filter=agent&agent=<id>, from the roster

$selectedId = null;
$conversation = null;
$allRows = $isAgent ? $store->listConversations() : [];
// filter_rows() is shared with admin/api.php, so the live refresh cannot
// silently drop the filter that is on screen.
$rows = filter_rows($allRows, $filter, $agentFilter, $meId);
$unreadTotal = $store->unreadCount($allRows);

// Tab badges count conversations in each bucket, not messages.
$tabCounts = array_fill_keys(console_filters(), 0);
$tabCounts['all'] = count($allRows);
foreach ($allRows as $tabRow) {
    if ((string) $tabRow['status'] === 'closed') {
        $tabCounts['closed']++;
    } else {
        $tabCounts['open']++;
    }
    if ((int) $tabRow['agent_unread'] > 0) {
        $tabCounts['unread']++;
    }
    if ((string) ($tabRow['assignee'] ?? '') === '') {
        $tabCounts['unassigned']++;
    }
}
$tabCounts['mine'] = count(filter_rows($allRows, 'mine', '', $meId));
$assignedLabel = $agentFilter !== '' && isset($agentMap[$agentFilter]) ? (string) $agentMap[$agentFilter]['name'] : '';

if ($isAgent) {
    $requested = param('id', 64, true);
    if (preg_match('/^[a-f0-9]{32}$/', $requested) === 1) {
        $conversation = $store->get($requested);
        $selectedId = $conversation === null ? null : $requested;
    }
    if ($conversation === null && $rows !== []) {
        $selectedId = (string) $rows[0]['id'];
        $conversation = $store->get($selectedId);
    }
}

$totalMessages = $conversation === null ? 0 : count($conversation['messages'] ?? []);
$siteName = (string) ($config['site_name'] ?? 'Support Center');

// Visit-side metadata captured when the conversation started. Older
// conversations (or ones started before this was recorded) have none.
$visitorMeta = is_array($conversation['visitor'] ?? null) ? $conversation['visitor'] : [];
$visitorIp = trim((string) ($visitorMeta['ip'] ?? ''));
$visitorUa = trim((string) ($visitorMeta['ua'] ?? ''));
$deviceLabel = format_device($visitorUa);
$refLabel = $selectedId === null ? '' : short_ref($selectedId);
$visitorName = $conversation === null ? '' : trim((string) ($conversation['name'] ?? ''));
$visitorEmail = $conversation === null ? '' : trim((string) ($conversation['email'] ?? ''));
$visitorPage = $conversation === null ? '' : trim((string) ($conversation['page'] ?? ''));
$assigneeId = $conversation === null ? '' : (string) ($conversation['assignee'] ?? '');
$assigneeAgent = $assigneeId !== '' && isset($agentMap[$assigneeId]) ? $agentMap[$assigneeId] : null;

$cannedReplies = $config['admin_canned_replies'] ?? [];
$cannedReplies = is_array($cannedReplies)
    ? array_values(array_filter($cannedReplies, static fn ($v): bool => is_string($v) && trim($v) !== ''))
    : [];

// Absolute script path for links/forms/redirects so the console never leaks
// to the public page when opened as ".../admin" (no trailing slash).
$selfPath = (string) ($_SERVER['SCRIPT_NAME'] ?? 'index.php');
if (substr($selfPath, -4) !== '.php') {
    $selfPath = rtrim($selfPath, '/') . '/index.php';
}
if ($selfPath === '') {
    $selfPath = 'index.php';
}
// Absolute admin API endpoint for the live refresh (same reason: a relative
// "api.php" from ".../admin" would resolve to the public /api.php).
$adminApiPath = str_replace('\\', '/', (string) dirname($selfPath));
$adminApiPath = $adminApiPath === '.' || $adminApiPath === '' ? 'api.php' : rtrim($adminApiPath, '/') . '/api.php';
// Absolute stylesheet path for the same reason ("../assets/..." breaks from ".../admin").
$adminDir = str_replace('\\', '/', (string) dirname($selfPath));
$assetsBase = str_replace('\\', '/', (string) dirname($adminDir));
$assetsPath = ($adminDir === '.' || $adminDir === '' || $adminDir === '/')
    ? '../assets/style.css'
    : rtrim($assetsBase, '/') . '/assets/style.css';
if (strpos($assetsPath, '/') !== 0 && $assetsPath !== '../assets/style.css') {
    $assetsPath = '/' . ltrim($assetsPath, '/');
}
// Same reasoning for agent profile photos stored as root-relative paths.
$assetPrefix = admin_asset_prefix($assetsPath);

// Resolved before any output: the theme is an attribute on <body>, so the page
// is painted in the right colours on the first paint rather than being swapped
// afterwards by a script.
$theme = console_theme();
$themeIsDark = $theme === 'dark';
$themeToggleLabel = console_label($themeIsDark ? 'theme_to_light' : 'theme_to_dark');

/** Shared SVG bits, kept inline so the console needs no icon font. */
function icon(string $name): string
{
    $open = '<svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">';
    $close = '</svg>';
    $paths = [
        'search' => '<path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round"/>',
        'signout' => '<path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 17l5-5-5-5" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12H9" stroke-linecap="round"/>',
        // Theme toggle faces: the button shows the theme it switches *to*.
        'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M4.9 4.9l1.4 1.4m11.4 11.4l1.4 1.4M19.1 4.9l-1.4 1.4M6.3 17.7l-1.4 1.4" stroke-linecap="round"/>',
        'moon' => '<path d="M21 12.8A9 9 0 1111.2 3a7 7 0 009.8 9.8z" stroke-linecap="round" stroke-linejoin="round"/>',
    ];
    return $open . ($paths[$name] ?? '') . $close;
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<title><?= e($siteName) ?> — <?= e(console_label('console')) ?></title>
<link rel="stylesheet" href="<?= e($assetsPath) ?>">
</head>
<body class="console-page<?= $isAgent ? ' admin' : '' ?>" data-theme="<?= e($theme) ?>">

<?php if (!$isAgent): ?>
  <div class="auth-shell">
    <div class="auth-card">
      <h1><?= $needsSetup ? 'অ্যাডমিন পাসওয়ার্ড তৈরি করুন' : 'এজেন্ট সাইন ইন' ?></h1>
      <p class="muted">
        <?= $needsSetup
            ? 'প্রথম চালুতে এজেন্ট কনসোলের জন্য একটি পাসওয়ার্ড দিন। এটি হ্যাশ করে data/admin.json এ রাখা হয়।'
            : e($siteName) . ' সাপোর্ট কনসোল।' ?>
      </p>

      <?php if ($flash !== null): ?>
        <p class="flash <?= e($flash['type']) ?>" style="margin:14px 0"><?= e($flash['msg']) ?></p>
      <?php endif; ?>

      <?php if ($needsSetup): ?>
        <form method="post" action="<?= e($selfPath) ?>" autocomplete="off">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="setup">
          <label class="field" for="password">নতুন পাসওয়ার্ড (কমপক্ষে <?= $minPassword ?> অক্ষর)</label>
          <input class="text" type="password" id="password" name="password" required minlength="<?= $minPassword ?>" autofocus>
          <label class="field" for="password2">পাসওয়ার্ড আবার লিখুন</label>
          <input class="text" type="password" id="password2" name="password2" required minlength="<?= $minPassword ?>">
          <button class="btn" type="submit">তৈরি করে সাইন ইন করুন</button>
        </form>
      <?php else: ?>
        <form method="post" action="<?= e($selfPath) ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="login">
          <label class="field" for="handle">লগইন আইডি</label>
          <input class="text" type="text" id="handle" name="handle" autocomplete="username" required autofocus>
          <label class="field" for="password">পাসওয়ার্ড</label>
          <input class="text" type="password" id="password" name="password" required autocomplete="current-password">
          <button class="btn" type="submit">সাইন ইন করুন</button>
          <p class="muted" style="margin:12px 0 0">
            মালিক (Owner): <b><?= e($ownerLoginId) ?></b>। এজেন্ট অ্যাকাউন্ট সেটিংস থেকে যোগ করা যায় —
            তারা নিজের আইডি দিয়ে সাইন ইন করবে।
          </p>
        </form>
      <?php endif; ?>
    </div>
  </div>

<?php else: ?>

  <header class="topbar">
    <div class="topbar-brand">
      <a class="brand-badge" href="<?= e($selfPath) ?>" title="<?= e($siteName) ?>"><?= e(mb_strtoupper(mb_substr($siteName, 0, 1))) ?><span class="status-dot" aria-hidden="true"></span></a>
      <div class="brand-text">
        <div class="brand-row">
          <h1><?= e(console_label('console')) ?></h1>
          <span class="pill<?= $unreadTotal > 0 ? ' unread' : '' ?>" id="unreadPill"><span id="unreadCount"><?= (int) $unreadTotal ?></span> <span class="pill-word wide-only"><?= e(console_label('unread')) ?></span></span>
        </div>
        <p class="brand-note">
          <span class="status-dot sm pulse" aria-hidden="true"></span>
          <?= $agentList === [] ? e(console_label('online')) : (int) $onlineCount . ' ' . e(console_label('team_online')) ?>
        </p>
      </div>
    </div>

    <div class="topbar-search">
      <?= icon('search') ?>
      <input type="search" id="filterTop" placeholder="<?= e(console_label('search')) ?>" aria-label="<?= e(console_label('search')) ?>">
      <span class="kbd" aria-hidden="true">⌘K</span>
    </div>

    <div class="topbar-actions">
      <button class="btn ghost small" id="queueBtn" type="button" aria-controls="queueSheet" aria-expanded="false">
        <span class="wide-only"><?= e(console_label('queue')) ?></span>
        <span class="narrow-only"><?= e(console_label('queue_short')) ?></span>
      </button>
      <a class="btn ghost small team-btn<?= $view === 'agents' ? ' active' : '' ?>" href="<?= e($selfPath) ?>?view=agents"><?= e(console_label('team')) ?></a>
      <a class="btn ghost small" href="<?= e($selfPath) ?>?view=settings"><?= e(console_label('settings')) ?></a>
      <?php /* Both glyphs ship in the button; CSS shows the one for the theme
               it would switch to, so the click only flips the attribute. */ ?>
      <button class="btn ghost small theme-btn" id="themeBtn" type="button"
              data-theme-cookie="<?= e(SC_THEME_COOKIE) ?>"
              data-to-dark="<?= e(console_label('theme_to_dark')) ?>"
              data-to-light="<?= e(console_label('theme_to_light')) ?>"
              title="<?= e($themeToggleLabel) ?>" aria-label="<?= e($themeToggleLabel) ?>">
        <span class="theme-ico sun" aria-hidden="true"><?= icon('sun') ?></span>
        <span class="theme-ico moon" aria-hidden="true"><?= icon('moon') ?></span>
      </button>
      <form method="post" action="<?= e($selfPath) ?>">
        <?= csrf_field() ?>
        <button class="btn ghost small danger signout-btn" name="action" value="logout" type="submit" title="<?= e(console_label('signout')) ?>" aria-label="<?= e(console_label('signout')) ?>">
          <?= icon('signout') ?>
          <span class="btn-text"><?= e(console_label('signout')) ?></span>
        </button>
      </form>
      <a class="topbar-me" href="<?= e($selfPath) ?>?view=agents" title="<?= e((string) $meCard['name']) ?>">
        <?= agent_avatar_html($meCard, $assetPrefix, 'sm') ?>
        <span class="topbar-me-text">
          <span class="topbar-me-name"><?= e(agent_short_name((string) $meCard['name'])) ?></span>
          <span class="topbar-me-dept"><?= e($meDepartments === []
              ? ($meIsOwner ? console_label('owner') : console_label('no_department'))
              : implode(' · ', $meDepartments)) ?></span>
        </span>
      </a>
    </div>
  </header>

  <?php if ($flash !== null): ?>
    <p class="flash <?= e($flash['type']) ?>"><?= e($flash['msg']) ?></p>
  <?php endif; ?>

  <?php if ($securityWarnings !== []): ?>
    <div class="security-note" role="status">
      <strong><?= e(console_label('security_check')) ?></strong>
      <ul>
        <?php foreach ($securityWarnings as $warningKey): ?>
          <li><?= console_label($warningKey) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($view === 'settings'): ?>
    <div class="settings-shell">
      <?php if ($isAgent && !$meIsOwner): ?>
        <div class="card">
          <h2>নিজের পাসওয়ার্ড পরিবর্তন</h2>
          <p class="muted">
            <?= e((string) $meCard['name']) ?><?= ($me['handle'] ?? '') === '' ? '' : ' (' . e((string) $me['handle']) . ')' ?> — নতুন পাসওয়ার্ড সঙ্গে সঙ্গে কার্যকর হবে।
          </p>
          <form method="post" action="<?= e($selfPath) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="agent-password">
            <input type="hidden" name="id" value="<?= e($meId) ?>">
            <label class="field" for="ownpass">নতুন পাসওয়ার্ড (কমপক্ষে <?= $minPassword ?> অক্ষর)</label>
            <input class="text" type="password" id="ownpass" name="password" required minlength="<?= $minPassword ?>" autocomplete="new-password">
            <p style="margin-top:16px"><button class="btn" type="submit">পাসওয়ার্ড আপডেট করুন</button></p>
          </form>
        </div>
      <?php endif; ?>

      <?php if ($meIsOwner): ?>
      <div class="card">
        <h2>অ্যাডমিন পাসওয়ার্ড পরিবর্তন</h2>
        <?php if ($configuredHash !== ''): ?>
          <p class="muted">পাসওয়ার্ডটি <code>config.php</code> এর <code>admin_password_hash</code> দিয়ে পিন করা আছে। এখান থেকে পরিবর্তন করতে চাইলে সেই মানটি সরিয়ে দিন।</p>
        <?php else: ?>
          <form method="post" action="<?= e($selfPath) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="password">
            <label class="field" for="current">বর্তমান পাসওয়ার্ড</label>
            <input class="text" type="password" id="current" name="current" required>
            <label class="field" for="new">নতুন পাসওয়ার্ড (কমপক্ষে <?= $minPassword ?> অক্ষর)</label>
            <input class="text" type="password" id="new" name="password" required minlength="<?= $minPassword ?>">
            <label class="field" for="new2">নতুন পাসওয়ার্ড আবার লিখুন</label>
            <input class="text" type="password" id="new2" name="password2" required minlength="<?= $minPassword ?>">
            <p style="margin-top:16px"><button class="btn" type="submit">পাসওয়ার্ড আপডেট করুন</button></p>
          </form>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <div class="card">
        <h2>স্টোরেজ</h2>
        <p class="muted">
          মোট <?= count($allRows) ?> টি কথোপকথন। প্রতিটি কথোপকথন <code><?= e($config['data_dir']) ?>/chats</code> এ JSON ফাইল হিসেবে রাখা হয় — কোনো ডেটাবেস নেই।
          ইতিহাস রাখতে ওই ফোল্ডারটি ব্যাকআপ করুন।
        </p>
      </div>

      <div class="card">
        <h2><?= e(console_label('team')) ?> <span class="pill"><?= count($agentList) ?></span></h2>
        <?php if (!$canManageAgents): ?>
          <p class="muted"><?= e(console_label('agent_owner_only')) ?></p>
        <?php else: ?>
          <?php if ($demoPassword !== '' && array_filter($agentList, static fn (array $a): bool => (bool) $a['demo']) !== []): ?>
            <p class="flash error" style="margin:0 0 12px"><?= e(console_label('demo_password_warning')) ?></p>
          <?php endif; ?>

          <div class="agent-rows">
            <?php foreach ($agentList as $agentRow): ?>
              <form class="agent-row" method="post" action="<?= e($selfPath) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= e((string) $agentRow['id']) ?>">
                <div class="agent-row-head">
                  <?= agent_avatar_html($agentRow, $assetPrefix, 'lg') ?>
                  <div>
                    <h3><?= e((string) $agentRow['name']) ?></h3>
                    <p class="muted" style="margin:2px 0 0">
                      <code><?= e((string) $agentRow['handle']) ?></code>
                      <?php if ($agents->isOnline($agentRow)): ?>
                        · <span class="tag online"><?= e(console_label('online_short')) ?></span>
                      <?php endif; ?>
                      <?php if ((bool) $agentRow['demo']): ?>
                        · <span class="tag demo"><?= e(console_label('demo')) ?></span>
                      <?php endif; ?>
                    </p>
                  </div>
                  <button class="btn ghost small danger push" name="action" value="agent-delete" type="submit"
                          data-confirm="<?= e(console_label('agent_delete_confirm')) ?>"><?= e(console_label('agent_delete')) ?></button>
                </div>
                <div class="agent-grid">
                  <div>
                    <label for="agent-name-<?= e((string) $agentRow['id']) ?>"><?= e(console_label('agent_name')) ?></label>
                    <input class="text" type="text" id="agent-name-<?= e((string) $agentRow['id']) ?>" name="name" value="<?= e((string) $agentRow['name']) ?>" required>
                  </div>
                  <div>
                    <label for="agent-handle-<?= e((string) $agentRow['id']) ?>"><?= e(console_label('agent_handle')) ?></label>
                    <input class="text" type="text" id="agent-handle-<?= e((string) $agentRow['id']) ?>" name="handle" value="<?= e((string) $agentRow['handle']) ?>">
                  </div>
                  <div>
                    <label for="agent-photo-<?= e((string) $agentRow['id']) ?>"><?= e(console_label('agent_photo')) ?></label>
                    <input class="text" type="text" id="agent-photo-<?= e((string) $agentRow['id']) ?>" name="photo" value="<?= e((string) $agentRow['photo']) ?>" placeholder="assets/images/…">
                  </div>
                  <div>
                    <label for="agent-pass-<?= e((string) $agentRow['id']) ?>"><?= e(console_label('agent_password_keep')) ?></label>
                    <input class="text" type="password" id="agent-pass-<?= e((string) $agentRow['id']) ?>" name="password" minlength="<?= $minPassword ?>" autocomplete="new-password">
                  </div>
                </div>
                <div class="agent-actions">
                  <div class="dept-cards">
                    <?php foreach ($departments as $department): ?>
                      <label>
                        <input type="checkbox" name="departments[]" value="<?= e($department) ?>"<?= in_array($department, (array) $agentRow['departments'], true) ? ' checked' : '' ?>>
                        <?= e($department) ?>
                      </label>
                    <?php endforeach; ?>
                  </div>
                  <button class="btn small push" name="action" value="agent-save" type="submit"><?= e(console_label('agent_save')) ?></button>
                </div>
              </form>
            <?php endforeach; ?>
          </div>

          <form class="agent-row" method="post" action="<?= e($selfPath) ?>" style="margin-top:14px">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="agent-add">
            <div class="agent-row-head"><h3><?= e(console_label('agent_new')) ?></h3></div>
            <div class="agent-grid">
              <div>
                <label for="new-name"><?= e(console_label('agent_name')) ?></label>
                <input class="text" type="text" id="new-name" name="name" required>
              </div>
              <div>
                <label for="new-handle"><?= e(console_label('agent_handle')) ?></label>
                <input class="text" type="text" id="new-handle" name="handle" placeholder="kabir">
              </div>
              <div>
                <label for="new-photo"><?= e(console_label('agent_photo')) ?></label>
                <input class="text" type="text" id="new-photo" name="photo">
              </div>
              <div>
                <label for="new-pass"><?= e(console_label('agent_password')) ?></label>
                <input class="text" type="password" id="new-pass" name="password" required minlength="<?= $minPassword ?>" autocomplete="new-password">
              </div>
            </div>
            <div class="agent-actions">
              <div class="dept-cards">
                <?php foreach ($departments as $department): ?>
                  <label><input type="checkbox" name="departments[]" value="<?= e($department) ?>"><?= e($department) ?></label>
                <?php endforeach; ?>
              </div>
              <button class="btn small push" type="submit"><?= e(console_label('agent_add')) ?></button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>

  <?php elseif ($view === 'agents'): ?>
    <?php
      $unassignedCount = (int) $tabCounts['unassigned'];
      $perAgent = [];
      foreach ($allRows as $statRow) {
          $statAssignee = (string) ($statRow['assignee'] ?? '');
          if ($statAssignee === '') {
              continue;
          }
          $perAgent[$statAssignee]['total'] = ($perAgent[$statAssignee]['total'] ?? 0) + 1;
          $bucket = (string) $statRow['status'] === 'closed' ? 'closed' : 'open';
          $perAgent[$statAssignee][$bucket] = ($perAgent[$statAssignee][$bucket] ?? 0) + 1;
      }
    ?>
    <div class="settings-shell roster-shell">
      <p class="roster-summary">
        <?= e(console_label('team_summary')) ?> <b><?= count($agentList) ?></b> <?= e(console_label('agents')) ?>
        · <b><?= (int) $onlineCount ?></b> <?= e(console_label('team_online')) ?>
        · <b><?= $unassignedCount ?></b> <?= e(console_label('team_unassigned')) ?>
      </p>

      <?php if ($agentList === []): ?>
        <div class="card"><p class="muted"><?= e(console_label('agent_owner_only')) ?></p></div>
      <?php else: ?>
        <ul class="roster">
          <?php foreach ($agentList as $agentRow): ?>
            <?php
              $agentId = (string) $agentRow['id'];
              $stats = $perAgent[$agentId] ?? ['total' => 0, 'open' => 0, 'closed' => 0];
              $isOnline = $agents->isOnline($agentRow);
              $lastSeen = (int) $agentRow['last_seen'];
              $deptLabel = (array) $agentRow['departments'] === []
                  ? console_label('no_department')
                  : implode(', ', (array) $agentRow['departments']);
            ?>
            <li class="roster-row<?= $isOnline ? ' online' : '' ?>">
              <?= agent_avatar_html($agentRow, $assetPrefix, 'lg') ?>
              <div class="roster-body">
                <p class="roster-name">
                  <span><?= e((string) $agentRow['name']) ?></span>
                  <span class="roster-handle"><?= e((string) $agentRow['handle']) ?></span>
                  <?php if ($isOnline): ?>
                    <span class="tag online"><?= e(console_label('online_short')) ?></span>
                  <?php else: ?>
                    <span class="tag"><?= e(console_label('offline')) ?></span>
                  <?php endif; ?>
                  <?php if ((bool) $agentRow['demo']): ?>
                    <span class="tag demo"><?= e(console_label('demo')) ?></span>
                  <?php endif; ?>
                  <?php if ($agentId === $meId): ?>
                    <span class="tag agent-tag"><?= e(console_label('mine')) ?></span>
                  <?php endif; ?>
                </p>
                <p class="roster-line"><?= e(console_label('departments')) ?>: <b><?= e($deptLabel) ?></b></p>
                <p class="roster-line">
                  <?= e(console_label('chats')) ?>: <b><?= (int) $stats['total'] ?></b>
                  (<?= e(console_label('status_open')) ?> <?= (int) ($stats['open'] ?? 0) ?> · <?= e(console_label('status_closed')) ?> <?= (int) ($stats['closed'] ?? 0) ?>)
                  · <?= e(console_label('last_active')) ?>: <b><?= $lastSeen > 0 ? e(bn_time_ago($lastSeen)) : e(console_label('unknown')) ?></b>
                  · <?= e(console_label('joined')) ?>: <?= e((int) $agentRow['created_at'] > 0 ? date('M j, Y', (int) $agentRow['created_at']) : console_label('unknown')) ?>
                </p>
                <p class="roster-actions">
                  <a href="?filter=agent&amp;agent=<?= e($agentId) ?>"><?= e(console_label('view_chats')) ?> (<?= (int) $stats['total'] ?>)</a>
                  <?php if ($unassignedCount > 0): ?>
                    <a href="?filter=unassigned"><?= e(console_label('unassigned')) ?> (<?= $unassignedCount ?>)</a>
                  <?php endif; ?>
                </p>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

  <?php else: ?>
    <?php
      $tabDefs = [
          'all' => console_label('tab_all'),
          'open' => console_label('tab_open'),
      ];
      if ($meId !== '') {
          $tabDefs['mine'] = console_label('mine');
      }
      $tabDefs['unread'] = console_label('tab_unread');
      $tabDefs['closed'] = console_label('tab_closed');
      $status = $conversation === null ? '' : (string) ($conversation['status'] ?? Store::STATUS_OPEN);
      $statusTag = $status === 'open' ? console_label('status_open') : console_label('status_closed');
      $closeLabel = $status === 'open' ? console_label('close') : console_label('reopen');
      $nextStatus = $status === 'open' ? 'closed' : 'open';
    ?>

    <main class="console">
      <aside class="inbox" id="inbox">
        <div class="inbox-tools">
          <div class="field-search">
            <?= icon('search') ?>
            <input type="search" id="filter" placeholder="<?= e(console_label('search')) ?>" aria-label="<?= e(console_label('search')) ?>">
          </div>
        </div>
        <nav class="tabs" aria-label="<?= e(console_label('queue')) ?>">
          <?php foreach ($tabDefs as $tabKey => $tabLabel): ?>
            <a class="tab<?= $filter === $tabKey ? ' active' : '' ?><?= $tabKey === 'unread' ? ' unread' : '' ?>" href="?filter=<?= e($tabKey) ?>">
              <?php if ($tabKey === 'unread'): ?><span class="status-dot sm" aria-hidden="true"></span><?php endif; ?>
              <span><?= e($tabLabel) ?></span>
              <span class="n">(<?= (int) $tabCounts[$tabKey] ?>)</span>
            </a>
          <?php endforeach; ?>
        </nav>
        <?php if ($filter === 'agent' || $filter === 'unassigned'): ?>
          <div class="tabs">
            <a class="tab active" href="?filter=all">
              <span><?= e($filter === 'agent'
                  ? ($assignedLabel !== '' ? $assignedLabel : console_label('assignee'))
                  : console_label('unassigned')) ?></span>
              <span class="n">(<?= count($rows) ?>)</span>
              <span aria-hidden="true">×</span>
            </a>
          </div>
        <?php endif; ?>
        <div class="conv-list" id="convList" data-conv-list><?= render_list_items($rows, $selectedId, $agentMap) ?></div>
      </aside>

      <section class="thread"
               id="thread"
               data-id="<?= e((string) $selectedId) ?>"
               data-cursor="<?= (int) $totalMessages ?>"
               data-poll="<?= (int) $pollMs ?>"
               data-api="<?= e($adminApiPath) ?>"
               data-csrf="<?= e(csrf_token()) ?>"
               data-filter="<?= e($filter) ?>"
               data-agent="<?= e($agentFilter) ?>"
               data-unread-label="<?= e(console_label('unread')) ?>">

        <?php if ($conversation === null): ?>
          <div class="thread-head">
            <div class="thread-title"><h2><?= e(console_label('no_selection')) ?></h2></div>
          </div>
          <div class="thread-body">
            <div class="thread-empty"><?= e($allRows === [] ? console_label('waiting') : console_label('no_selection')) ?></div>
          </div>
        <?php else: ?>
          <div class="thread-head">
            <div>
              <div class="thread-title">
                <h2><?= e($visitorName !== '' ? $visitorName : console_label('anonymous')) ?></h2>
                <span class="tag <?= e($status) ?>" id="statusTag"><?= e($statusTag) ?></span>
              </div>
              <p class="thread-sub">
                <span><?= e(console_label('started')) ?> <?= e(date('M j, Y H:i', (int) $conversation['created_at'])) ?></span>
                <?php if ($visitorPage !== ''): ?>
                  <span aria-hidden="true">·</span>
                  <span><?= e(console_label('from')) ?></span>
                  <a href="<?= e($visitorPage) ?>" rel="noreferrer noopener" target="_blank"><?= e(mb_substr($visitorPage, 0, 60)) ?></a>
                <?php endif; ?>
              </p>
            </div>
            <div class="thread-actions">
              <?php if ($agentList !== []): ?>
                <form class="transfer" method="post" action="<?= e($selfPath) ?>">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="transfer">
                  <input type="hidden" name="id" value="<?= e((string) $selectedId) ?>">
                  <label class="sr-only" for="transferAgent"><?= e(console_label('transfer')) ?></label>
                  <select name="agent" id="transferAgent">
                    <option value=""><?= e(console_label('unassigned')) ?></option>
                    <?php foreach ($agentList as $optionAgent): ?>
                      <option value="<?= e((string) $optionAgent['id']) ?>"<?= $assigneeId === (string) $optionAgent['id'] ? ' selected' : '' ?>>
                        <?= e((string) $optionAgent['name']) ?><?= (array) $optionAgent['departments'] === [] ? '' : ' · ' . e(implode(', ', (array) $optionAgent['departments'])) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button class="btn ghost small" type="submit"><?= e(console_label('transfer')) ?></button>
                </form>
              <?php endif; ?>
              <form method="post" action="<?= e($selfPath) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= e((string) $selectedId) ?>">
                <input type="hidden" name="status" value="<?= e($nextStatus) ?>">
                <button class="btn ghost small" name="action" value="status" type="submit"><?= e($closeLabel) ?></button>
              </form>
              <form method="post" action="<?= e($selfPath) ?>" data-confirm="<?= e(console_label('confirm_delete')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= e((string) $selectedId) ?>">
                <button class="btn ghost small danger" name="action" value="delete" type="submit"><?= e(console_label('delete')) ?></button>
              </form>
            </div>
          </div>

          <div class="meta-strip">
            <span><?= e(console_label('reference')) ?> <b class="mono"><?= e($refLabel) ?></b></span>
            <span><?= e(console_label('ip')) ?> <b class="mono"><?= e($visitorIp !== '' ? $visitorIp : console_label('unknown')) ?></b></span>
            <span><?= e(console_label('device')) ?> <b><?= e($deviceLabel !== '' ? $deviceLabel : console_label('unknown')) ?></b></span>
          </div>

          <div class="thread-body" id="threadBody">
            <?= render_messages($conversation['messages'] ?? [], (string) $selectedId) ?>
          </div>

          <?php if ($cannedReplies !== []): ?>
            <section class="chips" data-purpose="quick-canned-replies">
              <span class="chips-label"><?= e(console_label('canned')) ?>:</span>
              <?php foreach ($cannedReplies as $cannedReply): ?>
                <button class="chip" type="button" data-canned="<?= e($cannedReply) ?>" title="<?= e($cannedReply) ?>">⚡ <?= e($cannedReply) ?></button>
              <?php endforeach; ?>
            </section>
          <?php endif; ?>

          <form class="composer" method="post" action="<?= e($selfPath) ?>" id="replyForm" enctype="multipart/form-data"
                data-max-mb="<?= (int) ($config['max_upload_mb'] ?? 25) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="reply">
            <input type="hidden" name="id" value="<?= e((string) $selectedId) ?>">

            <div class="typing-row" id="typingRow">
              <span class="typing-dots" aria-hidden="true"><i></i><i></i><i></i></span>
              <span class="hint" id="typingHint"></span>
            </div>

            <div class="composer-box">
              <textarea name="message" id="replyText" rows="3"
                        placeholder="<?= e(console_label('write_reply')) ?>"
                        maxlength="<?= (int) ($config['max_message_length'] ?? 4000) ?>"></textarea>
              <div class="composer-tools">
                <div class="tool-left">
                  <label class="attach" title="<?= e(console_label('attach')) ?>">
                    <input type="file" name="file" id="replyFile" hidden
                           accept="<?= e(implode(',', array_keys($config['allowed_media'] ?? []))) ?>">
                    <span aria-hidden="true">📎</span><?= e(console_label('attach')) ?>
                  </label>
                  <span class="attach-name" id="attachName"></span>
                  <span class="tool-sep" aria-hidden="true"></span>
                  <button class="tool-btn" type="button" id="canned">⚡ <?= e(console_label('insert_canned')) ?></button>
                </div>
                <div class="tool-right">
                  <span class="hint" id="replyHint"></span>
                  <span class="conn"><span class="status-dot sm" aria-hidden="true"></span><?= e(console_label('connected')) ?></span>
                  <button class="btn" type="submit">
                    <span id="sendLabel"><?= e(console_label('send')) ?></span>
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                      <path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </form>
        <?php endif; ?>
      </section>

      <aside class="visitor-panel" id="visitorPanel">
        <?php if ($conversation !== null): ?>
          <div class="panel-profile">
            <span class="panel-avatar"><?= e(avatar_initial($visitorName)) ?></span>
            <div>
              <h3><?= e($visitorName !== '' ? $visitorName : console_label('anonymous')) ?></h3>
              <p><?= e(console_label('last_message')) ?> <?= e(bn_time_ago((int) $conversation['updated_at'])) ?></p>
            </div>
          </div>
          <div class="panel-actions">
            <button class="btn ghost small" type="button" id="copyLink" data-url="<?= e($visitorPage) ?>"<?= $visitorPage === '' ? ' disabled' : '' ?>><?= e(console_label('copy_link')) ?></button>
            <form method="post" action="<?= e($selfPath) ?>">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= e((string) $selectedId) ?>">
              <input type="hidden" name="status" value="<?= e($nextStatus) ?>">
              <button class="btn ghost small" name="action" value="status" type="submit"><?= e($closeLabel) ?></button>
            </form>
          </div>
          <div class="panel-body">
            <h4><?= e(console_label('visitor_info')) ?></h4>
            <dl class="panel-list">
              <div><dt><?= e(console_label('reference')) ?></dt><dd class="mono"><?= e($refLabel) ?></dd></div>
              <div><dt><?= e(console_label('started')) ?></dt><dd><?= e(date('M j, Y H:i', (int) $conversation['created_at'])) ?></dd></div>
              <div>
                <dt><?= e(console_label('page')) ?></dt>
                <dd>
                  <?php if ($visitorPage !== ''): ?>
                    <a href="<?= e($visitorPage) ?>" rel="noreferrer noopener" target="_blank" title="<?= e($visitorPage) ?>"><?= e($visitorPage) ?></a>
                  <?php else: ?>
                    <?= e(console_label('unknown')) ?>
                  <?php endif; ?>
                </dd>
              </div>
              <div><dt><?= e(console_label('ip')) ?></dt><dd class="mono"><?= e($visitorIp !== '' ? $visitorIp : console_label('unknown')) ?></dd></div>
              <div><dt><?= e(console_label('device')) ?></dt><dd title="<?= e($visitorUa) ?>"><?= e($deviceLabel !== '' ? $deviceLabel : console_label('unknown')) ?></dd></div>
              <div><dt><?= e(console_label('messages')) ?></dt><dd><?= (int) $totalMessages ?></dd></div>
              <div><dt><?= e(console_label('unread')) ?></dt><dd id="panelUnread"><?= (int) ($conversation['agent_unread'] ?? 0) ?></dd></div>
              <div>
                <dt><?= e(console_label('assignee')) ?></dt>
                <dd>
                  <?php if ($assigneeAgent !== null): ?>
                    <?= e(agent_short_name((string) $assigneeAgent['name'])) ?>
                    <?= (array) $assigneeAgent['departments'] === [] ? '' : ' · ' . e(implode(', ', (array) $assigneeAgent['departments'])) ?>
                  <?php else: ?>
                    <?= e(console_label('unassigned')) ?>
                  <?php endif; ?>
                </dd>
              </div>
              <?php if ($visitorEmail !== ''): ?>
                <div><dt><?= e(console_label('email')) ?></dt><dd><a href="mailto:<?= e($visitorEmail) ?>"><?= e($visitorEmail) ?></a></dd></div>
              <?php endif; ?>
            </dl>
            <p class="panel-note"><?= e(console_label('panel_note')) ?></p>
          </div>
        <?php endif; ?>
      </aside>
    </main>

    <div class="sheet" id="queueSheet" role="dialog" aria-modal="true" aria-label="<?= e(console_label('queue')) ?>">
      <div class="sheet-backdrop" data-sheet-close></div>
      <div class="sheet-panel">
        <div class="sheet-head">
          <span class="status-dot sm" aria-hidden="true"></span>
          <h2><?= e(console_label('queue')) ?></h2>
          <span class="pill"><?= count($allRows) ?></span>
          <button class="sheet-close" type="button" data-sheet-close aria-label="<?= e(console_label('signout')) ?>">×</button>
        </div>
        <div class="inbox-tools">
          <div class="field-search">
            <input type="search" id="filterSheet" placeholder="<?= e(console_label('search')) ?>" aria-label="<?= e(console_label('search')) ?>">
          </div>
        </div>
        <div class="sheet-body conv-list" data-conv-list><?= render_list_items($rows, $selectedId, $agentMap) ?></div>
      </div>
    </div>

    <script nonce="<?= e(csp_nonce()) ?>">
    (function () {
      'use strict';

      // The confirm listener, the theme toggle and the agent photo fallback
      // are page-independent, so they live in one script further down that
      // every console view gets. This block is only reached by the inbox.

      var thread = document.getElementById('thread');
      var body = document.getElementById('threadBody');
      var pill = document.getElementById('unreadPill');
      var hint = document.getElementById('replyHint');
      var typingHint = document.getElementById('typingHint');
      var typingRow = document.getElementById('typingRow');
      var attachName = document.getElementById('attachName');
      var panelUnread = document.getElementById('panelUnread');
      var csrf = (thread && thread.dataset.csrf) || '';
      var apiBase = (thread && thread.dataset.api) || 'api.php';
      var id = thread ? thread.dataset.id : '';
      var cursor = thread ? (parseInt(thread.dataset.cursor, 10) || 0) : 0;
      var pollMs = thread ? (parseInt(thread.dataset.poll, 10) || 4000) : 4000;
      // The queue filter is part of every list request: the server re-renders the
      // same slice the page showed, so polling never widens it.
      var listFilter = (thread && thread.dataset.filter) || 'all';
      var listAgent = (thread && thread.dataset.agent) || '';
      var listQuery = '&filter=' + encodeURIComponent(listFilter) + '&agent=' + encodeURIComponent(listAgent);

      var replyForm = document.getElementById('replyForm');
      var replyText = document.getElementById('replyText');
      var replyFile = document.getElementById('replyFile');
      var sendLabel = document.getElementById('sendLabel');
      var maxMb = replyForm ? (parseInt(replyForm.dataset.maxMb, 10) || 25) : 25;

      function atBottom() {
        if (!body) return true;
        return body.scrollHeight - body.scrollTop - body.clientHeight < 80;
      }
      function toBottom() { if (body) body.scrollTop = body.scrollHeight; }
      function note(text) { if (hint) hint.textContent = text; }

      function get(url) {
        return fetch(url, { credentials: 'same-origin' }).then(function (r) { return r.json(); });
      }

      function showTyping(on) {
        if (typingHint) typingHint.textContent = on ? 'ভিজিটর লিখছেন…' : '';
        if (typingRow) typingRow.classList.toggle('on', !!on);
      }

      // The pill is the console-wide unread total; it starts from what the
      // server rendered, so an unchanged total does not re-render the queue.
      var unreadCount = document.getElementById('unreadCount');
      var unreadLabel = (thread && thread.dataset.unreadLabel) || '';
      var knownUnread = unreadCount ? (parseInt(unreadCount.textContent, 10) || 0) : 0;

      function setUnread(count) {
        knownUnread = count;
        if (unreadCount) unreadCount.textContent = count;
        if (pill) {
          // The label word is rendered by the server; on phones only the
          // number stays visible, so mirror the full wording for screen readers.
          pill.setAttribute('aria-label', count + ' ' + unreadLabel);
          pill.classList.toggle('unread', count > 0);
        }
      }

      function setPanelUnread(count) {
        if (panelUnread) panelUnread.textContent = count;
      }

      // Every list container (desktop inbox + mobile queue sheet) shows the
      // same rows, so a single payload updates all of them.
      function setList(html) {
        if (!html) return;
        Array.prototype.forEach.call(document.querySelectorAll('[data-conv-list]'), function (pane) {
          pane.innerHTML = html;
        });
        applyFilter();
      }

      // The visitor's read receipt: mark the last agent bubble they have seen.
      function markSeen(visitorRead) {
        if (!body) return;
        var msgs = body.querySelectorAll('.msg.agent');
        Array.prototype.forEach.call(msgs, function (msg) {
          var stale = msg.querySelector('.seen');
          if (stale) stale.remove();
        });

        if (!msgs.length || visitorRead <= 0) return;
        var last = msgs[msgs.length - 1];
        var index = parseInt(last.dataset.index, 10);
        if (isNaN(index) || visitorRead <= index) return;

        var mark = document.createElement('span');
        mark.className = 'seen';
        mark.textContent = '✓✓ দেখা হয়েছে';
        var foot = last.querySelector('.msg-foot') || last;
        foot.appendChild(mark);
      }

      if (body) { toBottom(); }

      // Append bubble HTML without duplicating indexes already on screen
      // (a poll started before our own send can otherwise re-deliver it).
      function appendHtml(html, stick) {
        if (!body || !html) return;
        var tmp = document.createElement('div');
        tmp.innerHTML = html;
        var nodes = tmp.querySelectorAll('.msg[data-index]');
        var added = false;
        Array.prototype.forEach.call(nodes, function (node) {
          var idx = node.getAttribute('data-index');
          if (idx !== null && body.querySelector('.msg[data-index="' + idx + '"]')) return;
          body.appendChild(node);
          added = true;
        });
        if (added && stick !== false) toBottom();
      }

      function applyReplyMeta(data) {
        if (!data) return;
        if (typeof data.cursor === 'number') cursor = data.cursor;
        if (data.status) {
          var tag = document.getElementById('statusTag');
          if (tag) {
            tag.textContent = data.status === 'open' ? 'চালু' : 'বন্ধ';
            tag.className = 'tag ' + data.status;
          }
        }
        setList(data.list_html);
        if (typeof data.unread === 'number') setUnread(data.unread);
        if (typeof data.agent_unread === 'number') setPanelUnread(data.agent_unread);
        if (data.presence) {
          markSeen(data.presence.read ? data.presence.read.visitor : 0);
          showTyping(!!(data.presence.typing && data.presence.typing.visitor));
        }
      }

      // ------------------------------------------------------------- sending
      var sending = false;

      function setSending(on) {
        sending = on;
        var btn = replyForm ? replyForm.querySelector('button[type="submit"]') : null;
        if (btn) btn.disabled = on;
        if (sendLabel) sendLabel.textContent = on ? 'পাঠানো হচ্ছে…' : 'উত্তর পাঠান';
        if (replyText) replyText.disabled = on;
      }

      var lastTypingPing = 0;
      function pingTyping() {
        var now = Date.now();
        if (!id || now - lastTypingPing < 2000) return;
        lastTypingPing = now;

        fetch(apiBase, {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
          body: new URLSearchParams({ what: 'typing', id: id, csrf: csrf })
        }).catch(function () {});
      }

      if (replyForm) {
        // Attachment picker: show the chosen filename and enforce the size limit.
        if (replyFile) {
          replyFile.addEventListener('change', function () {
            var file = replyFile.files && replyFile.files[0];
            if (!file) { if (attachName) attachName.textContent = ''; return; }
            if (file.size > maxMb * 1024 * 1024) {
              window.alert('ফাইলটি ' + maxMb + ' MB সীমার চেয়ে বড়।');
              replyFile.value = '';
              if (attachName) attachName.textContent = '';
              return;
            }
            if (attachName) attachName.textContent = 'সংযুক্ত: ' + file.name;
          });
        }

        // No-reload send: POST to admin/api.php and append the new bubbles.
        // The plain form POST to index.php stays as a no-JS fallback.
        replyForm.addEventListener('submit', function (event) {
          event.preventDefault();
          if (sending || !id) return;
          var text = replyText ? replyText.value.trim() : '';
          var hasFile = !!(replyFile && replyFile.files && replyFile.files[0]);
          if (!text && !hasFile) {
            note('উত্তর লিখুন অথবা ছবি/ভিডিও সংযুক্ত করুন।');
            if (replyText) replyText.focus();
            return;
          }
          if (hasFile && replyFile.files[0].size > maxMb * 1024 * 1024) {
            window.alert('ফাইলটি ' + maxMb + ' MB সীমার চেয়ে বড়।');
            return;
          }
          setSending(true);
          note('পাঠানো হচ্ছে…');

          var form = new FormData();
          form.append('what', 'reply');
          form.append('id', id);
          form.append('csrf', csrf);
          form.append('message', replyText ? replyText.value : '');
          form.append('filter', listFilter);
          form.append('agent', listAgent);
          if (hasFile) form.append('file', replyFile.files[0]);

          fetch(apiBase, { method: 'POST', credentials: 'same-origin', body: form })
            .then(function (r) {
              return r.text().then(function (text) {
                var data = null;
                try { data = JSON.parse(text); } catch (e) { data = null; }
                if (!data) throw new Error(text || 'সিকিউরিটি টোকেন মেয়াদোত্তীর্ণ। পেজটি রিলোড করে আবার চেষ্টা করুন।');
                if (!r.ok || !data.ok) throw new Error(data.error || 'উত্তর পাঠানো যায়নি।');
                return data;
              });
            })
            .then(function (data) {
              appendHtml(data.messages_html, true);
              applyReplyMeta(data);
              if (replyText) { replyText.value = ''; replyText.focus(); }
              if (replyFile) replyFile.value = '';
              if (attachName) attachName.textContent = '';
              note('পাঠানো হয়েছে');
              setTimeout(function () { note(''); }, 2500);
            })
            .catch(function (err) {
              if (/not signed in/i.test(err.message || '')) { location.reload(); return; }
              note(err.message || 'উত্তর পাঠানো যায়নি।');
              window.alert(err.message || 'উত্তর পাঠানো যায়নি।');
            })
            .finally(function () { setSending(false); });
        });
      }

      if (replyText) {
        replyText.addEventListener('keydown', function (event) {
          if (event.key === 'Enter' && (event.ctrlKey || event.metaKey)) {
            event.preventDefault();
            if (replyForm) {
              if (typeof replyForm.requestSubmit === 'function') replyForm.requestSubmit();
              else replyForm.dispatchEvent(new Event('submit', { cancelable: true }));
            }
          }
        });

        replyText.addEventListener('input', pingTyping);
      }

      // ------------------------------------------------- canned reply chips
      function insertCanned(text) {
        if (!replyText || !text) return;
        var current = replyText.value.replace(/\s*$/, '');
        replyText.value = current === '' ? text : current + '\n\n' + text;
        replyText.focus();
        pingTyping();
      }

      var chips = document.querySelectorAll('[data-canned]');
      Array.prototype.forEach.call(chips, function (chip) {
        chip.addEventListener('click', function () {
          insertCanned(chip.getAttribute('data-canned') || '');
        });
      });

      // The toolbar button opens the full list (per-browser extras included).
      var cannedBtn = document.getElementById('canned');
      if (cannedBtn && replyText) {
        var defaults = [];
        Array.prototype.forEach.call(chips, function (chip) {
          var text = chip.getAttribute('data-canned') || '';
          if (text !== '') defaults.push(text);
        });
        if (!defaults.length) {
          defaults = [
            'Thanks for reaching out! Could you send us a bit more detail?',
            'Thanks — we are looking into this and will update you shortly.',
            'This should be fixed now. Please reload and try again.',
            'Sorry for the trouble! I have escalated this to our team.'
          ];
        }
        cannedBtn.addEventListener('click', function () {
          var stored = null;
          try { stored = JSON.parse(localStorage.getItem('sc_canned') || 'null'); } catch (e) { stored = null; }
          var options = Array.isArray(stored) && stored.length ? stored : defaults;
          var choice = window.prompt('দ্রুত উত্তর বেছে নিন:\n' + options.map(function (t, i) { return (i + 1) + '. ' + t; }).join('\n'), '1');
          var index = parseInt(choice, 10);
          if (isNaN(index) || !options[index - 1]) return;
          insertCanned(options[index - 1]);
        });
      }

      // --------------------------------------------------------- copy link
      var copyBtn = document.getElementById('copyLink');
      if (copyBtn) {
        var copyLabel = copyBtn.textContent;
        copyBtn.addEventListener('click', function () {
          var url = copyBtn.getAttribute('data-url') || '';
          if (url === '') return;
          var done = function () {
            copyBtn.textContent = 'কপি হয়েছে';
            setTimeout(function () { copyBtn.textContent = copyLabel; }, 1600);
          };
          if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(done).catch(function () { window.prompt('লিংক কপি করুন:', url); });
          } else {
            window.prompt('লিংক কপি করুন:', url);
          }
        });
      }

      // ------------------------------------------------- search + queue sheet
      // Ordered by preference for Ctrl+K: the top bar on wide screens, the
      // inbox next, the queue sheet on phones (both follow the same query).
      var filterInputs = [];
      ['filterTop', 'filter', 'filterSheet'].forEach(function (key) {
        var el = document.getElementById(key);
        if (el) filterInputs.push(el);
      });
      var query = '';

      function applyFilter() {
        Array.prototype.forEach.call(document.querySelectorAll('[data-conv-list] .conv'), function (node) {
          var text = node.textContent.toLowerCase();
          node.style.display = query === '' || text.indexOf(query) !== -1 ? '' : 'none';
        });
      }

      filterInputs.forEach(function (input) {
        input.addEventListener('input', function () {
          query = input.value.toLowerCase().trim();
          if (query === '') {
            filterInputs.forEach(function (other) { if (other !== input) other.value = ''; });
          }
          applyFilter();
        });
      });

      var sheet = document.getElementById('queueSheet');
      var queueBtn = document.getElementById('queueBtn');
      var sheetSearch = document.getElementById('filterSheet');

      function setSheet(open) {
        if (!sheet) return;
        sheet.classList.toggle('open', open);
        if (queueBtn) queueBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
      }

      if (queueBtn) {
        queueBtn.addEventListener('click', function () {
          setSheet(!sheet.classList.contains('open'));
        });
      }
      if (sheet) {
        sheet.addEventListener('click', function (event) {
          var target = event.target;
          if (target && target.closest && (target.closest('[data-sheet-close]') || target.closest('.conv'))) {
            setSheet(false);
          }
        });
      }

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') setSheet(false);
        if ((event.ctrlKey || event.metaKey) && (event.key === 'k' || event.key === 'K')) {
          // Whichever search box is actually on screen; on phones the queue
          // sheet is closed, so open it and put the caret in its search box.
          var target = null;
          filterInputs.forEach(function (input) {
            if (target === null && input.offsetParent !== null) target = input;
          });
          if (target === null && sheetSearch) {
            setSheet(true);
            target = sheetSearch;
          }
          if (target) {
            event.preventDefault();
            target.focus();
            if (target.select) target.select();
          }
        }
      });

      // ------------------------------------------------------------ polling
      // Live list refresh (new conversations, unread counts).
      setInterval(function () {
        get(apiBase + '?what=list&id=' + encodeURIComponent(id || '') + listQuery).then(function (data) {
          if (!data.ok) return;
          setList(data.list_html);
          if (typeof data.unread === 'number') setUnread(data.unread);
        }).catch(function () {});
      }, 10000);

      // Live thread refresh: messages plus typing/read presence.
      function pollThread() {
        if (!id) return;
        var stick = atBottom();
        get(apiBase + '?what=thread&id=' + encodeURIComponent(id) + '&after=' + cursor + listQuery).then(function (data) {
          if (!data.ok) return;
          if (data.reset) {
            body.innerHTML = data.messages_html;
            cursor = data.cursor;
            toBottom();
          } else if (data.messages_html) {
            appendHtml(data.messages_html, stick);
            note('নতুন বার্তা এসেছে');
            setTimeout(function () { note(''); }, 4000);
          }
          cursor = data.cursor;

          if (typeof data.agent_unread === 'number') setPanelUnread(data.agent_unread);
          if (typeof data.unread === 'number') {
            // Reading a thread clears its counter server-side: refresh the queue
            // only when the total moved, so polling stays quiet otherwise.
            if (data.unread !== knownUnread) setList(data.list_html);
            setUnread(data.unread);
          }

          if (data.presence) {
            markSeen(data.presence.read ? data.presence.read.visitor : 0);
            showTyping(!!(data.presence.typing && data.presence.typing.visitor));
          }
        }).catch(function () {});
      }

      if (id) {
        pollThread();
        setInterval(pollThread, pollMs);
      }
    })();
    </script>
  <?php endif; ?>

  <?php /*
   * Handlers that belong to the console chrome rather than to one view, kept
   * outside the branch above so the settings and team pages get them too:
   * without this, the roster's Delete button asks for a confirmation that
   * nothing implements, and the theme button is inert.
   */ ?>
  <script nonce="<?= e(csp_nonce()) ?>">
  (function () {
    'use strict';

    // Destructive forms ask for confirmation here, not with an inline
    // onsubmit/onclick: the page's CSP refuses to run inline handlers.
    // One delegated listener covers both data-confirm on the form and on the
    // button that submitted it (event.submitter).
    document.addEventListener('submit', function (event) {
      var form = event.target;
      var button = event.submitter || null;
      var message = (button && button.getAttribute && button.getAttribute('data-confirm'))
        || (form && form.getAttribute && form.getAttribute('data-confirm'));
      if (message && !window.confirm(message)) {
        event.preventDefault();
      }
    }, true);

    // ----------------------------------------------------------- theme toggle
    // Light/dark is a single attribute on <body>; the stylesheet does the
    // rest. The choice is written to a cookie so the server can put the
    // attribute back on the next page load — which is also why the toggle
    // needs no round trip and no reload. Only the two literals "dark" and
    // "light" are ever stored, and only the tooltip/label swap below.
    var themeBtn = document.getElementById('themeBtn');
    if (themeBtn) {
      themeBtn.addEventListener('click', function () {
        var next = document.body.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
        document.body.setAttribute('data-theme', next);

        var cookie = themeBtn.getAttribute('data-theme-cookie') || 'sc_theme';
        var secure = location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = cookie + '=' + next + '; path=/; max-age=31536000; SameSite=Lax' + secure;

        // The button names the theme it switches to, so it is relabelled too:
        // now that we are on `next`, offer the other one.
        var label = themeBtn.getAttribute(next === 'light' ? 'data-to-dark' : 'data-to-light') || '';
        if (label !== '') {
          themeBtn.setAttribute('title', label);
          themeBtn.setAttribute('aria-label', label);
        }
      });
    }

    // ------------------------------------------------- agent photo fallback
    // An agent's own picture is shown as it was set, and the URL may point at
    // a host that is slow, hotlink-blocked or simply wrong. Rather than leave
    // a broken-image icon in the roster, the generated initials picture is
    // carried on the tag as a data: URI and swapped in here. The listener is
    // on the capture phase because an <img> error does not bubble, and an
    // inline onerror would be refused by the page's CSP.
    document.addEventListener('error', function (event) {
      var el = event.target;
      if (!el || el.tagName !== 'IMG' || !el.classList || !el.classList.contains('agent-avatar')) {
        return;
      }
      var fallback = el.getAttribute('data-avatar-fallback') || '';
      if (fallback === '') {
        return;
      }
      // Removed first: a fallback that also failed must not loop.
      el.removeAttribute('data-avatar-fallback');
      el.src = fallback;
    }, true);
  })();
  </script>
<?php endif; ?>

</body>
</html>
