<?php

declare(strict_types=1);

/**
 * Agent console: shared-password login, conversation queue, replies.
 */

require dirname(__DIR__) . '/lib/bootstrap.php';
require __DIR__ . '/partials.php';

header('X-Robots-Tag: noindex');

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
    header('Location: index.php' . $suffix, true, 303);
    exit;
}

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
// POST actions
// ---------------------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = param('action', 20);
    $returnId = param('return_id', 64);

    // ---------------------------------------------------------------- login
    if ($action === 'login') {
        if ($needsSetup) {
            set_flash('error', 'Create an admin password first.');
            redirect_to();
        }

        $wait = $limiter->retryAfter($throttleKey);
        if ($wait > 0) {
            set_flash('error', 'Too many failed attempts. Try again in ' . max(1, (int) ceil($wait / 60)) . ' minute(s).');
            redirect_to();
        }

        if (password_verify(raw_post('password', 300), $storedHash)) {
            session_regenerate_id(true);
            $_SESSION['agent'] = true;
            $_SESSION['agent_at'] = time();
            $limiter->clear($throttleKey);
            set_flash('ok', 'Signed in.');
            redirect_to();
        }

        $limiter->hit($throttleKey);
        set_flash('error', 'Incorrect password.');
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
            set_flash('error', 'Use at least ' . $minPassword . ' characters.');
            redirect_to();
        }
        if ($password !== $confirm) {
            set_flash('error', 'The two passwords do not match.');
            redirect_to();
        }
        if (!save_admin_hash($adminFile, password_hash($password, PASSWORD_DEFAULT))) {
            set_flash('error', 'Could not write ' . e($adminFile) . '. Check that the data/ directory is writable.');
            redirect_to();
        }

        session_regenerate_id(true);
        $_SESSION['agent'] = true;
        set_flash('ok', 'Admin password created. Keep it somewhere safe — it cannot be recovered.');
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
            set_flash('error', 'Your current password is not correct.');
        } elseif (mb_strlen($password) < $minPassword) {
            set_flash('error', 'Use at least ' . $minPassword . ' characters.');
        } elseif ($password !== $confirm) {
            set_flash('error', 'The two passwords do not match.');
        } elseif ($configuredHash !== '') {
            set_flash('error', 'The password is pinned in config.php (admin_password_hash); edit that file instead.');
        } elseif (save_admin_hash($adminFile, password_hash($password, PASSWORD_DEFAULT))) {
            session_regenerate_id(true);
            $_SESSION['agent'] = true;
            set_flash('ok', 'Password updated.');
        } else {
            set_flash('error', 'Could not save the new password.');
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
            set_flash('error', 'That conversation no longer exists.');
            redirect_to();
        } elseif ($message === '') {
            set_flash('error', 'Write a reply before sending.');
            redirect_to('?id=' . $id);
        } else {
            $updated = $store->addMessage($id, Store::ROLE_AGENT, $message);
            if ($updated === null) {
                set_flash('error', 'The reply could not be saved.');
                redirect_to('?id=' . $id);
            }
            $store->poll($id, 0, Store::ROLE_AGENT); // the agent is looking at it now
            redirect_to('?id=' . $id);
        }
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
                set_flash('ok', $status === Store::STATUS_CLOSED ? 'Conversation closed.' : 'Conversation reopened.');
                redirect_to('?id=' . $id);
            }
            if ($store->delete($id)) {
                set_flash('ok', 'Conversation deleted.');
            } else {
                set_flash('error', 'That conversation no longer exists.');
            }
        }
        redirect_to();
    }

    set_flash('error', 'Unknown action.');
    redirect_to();
}

// ---------------------------------------------------------------------------
// Selected conversation + list
// ---------------------------------------------------------------------------

$view = param('view', 20, true);
$filter = param('filter', 12, true);
if (!in_array($filter, ['all', 'open', 'unread'], true)) {
    $filter = 'all';
}

$selectedId = null;
$conversation = null;
$allRows = $isAgent ? $store->listConversations() : [];
$rows = match ($filter) {
    'open' => array_values(array_filter($allRows, static fn (array $r): bool => $r['status'] === 'open')),
    'unread' => array_values(array_filter($allRows, static fn (array $r): bool => (int) $r['agent_unread'] > 0)),
    default => $allRows,
};
$unreadTotal = $store->unreadCount($allRows);

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($siteName) ?> — Agent console</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body<?= $isAgent ? ' class="admin"' : '' ?>>

<?php if (!$isAgent): ?>
  <div class="login-shell">
    <div class="card login-card">
      <h1><?= $needsSetup ? 'Create admin password' : 'Agent sign in' ?></h1>
      <p class="muted">
        <?= $needsSetup
            ? 'This is the first run, so pick a password for the agent console. It is stored hashed in data/admin.json.'
            : e($siteName) . ' support console.' ?>
      </p>

      <?php if ($flash !== null): ?>
        <p class="flash <?= e($flash['type']) ?>"><?= e($flash['msg']) ?></p>
      <?php endif; ?>

      <?php if ($needsSetup): ?>
        <form method="post" autocomplete="off">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="setup">
          <label class="field" for="password">New password (min <?= $minPassword ?> characters)</label>
          <input class="text" type="password" id="password" name="password" required minlength="<?= $minPassword ?>" autofocus>
          <label class="field" for="password2">Repeat password</label>
          <input class="text" type="password" id="password2" name="password2" required minlength="<?= $minPassword ?>">
          <p><button class="btn" type="submit" style="width:100%;justify-content:center;margin-top:16px">Create and sign in</button></p>
        </form>
      <?php else: ?>
        <form method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="login">
          <label class="field" for="password">Password</label>
          <input class="text" type="password" id="password" name="password" required autofocus>
          <p><button class="btn" type="submit" style="width:100%;justify-content:center;margin-top:16px">Sign in</button></p>
        </form>
      <?php endif; ?>
    </div>
  </div>

<?php else: ?>

  <div class="admin-bar">
    <a class="brand" href="index.php"><span class="dot"><?= e(mb_strtoupper(mb_substr($siteName, 0, 1))) ?></span> Agent console</a>
    <span class="pill<?= $unreadTotal > 0 ? ' unread' : '' ?>" id="unreadPill"><?= (int) $unreadTotal ?> unread</span>
    <span class="spacer"></span>
    <a class="btn ghost small" href="index.php?view=settings">Settings</a>
    <form method="post" style="display:inline">
      <?= csrf_field() ?>
      <button class="btn ghost small" name="action" value="logout" type="submit">Sign out</button>
    </form>
  </div>

  <?php if ($flash !== null): ?>
    <p class="flash <?= e($flash['type']) ?>" style="margin:12px 18px 0"><?= e($flash['msg']) ?></p>
  <?php endif; ?>

  <?php if ($view === 'settings'): ?>
    <div class="wrap" style="max-width:720px;padding:26px 20px">
      <div class="card">
        <h2 style="font-size:19px">Change admin password</h2>
        <?php if ($configuredHash !== ''): ?>
          <p class="muted">The password is pinned by <code>admin_password_hash</code> in <code>config.php</code>. Remove that value to manage it here.</p>
        <?php else: ?>
          <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="password">
            <label class="field" for="current">Current password</label>
            <input class="text" type="password" id="current" name="current" required>
            <label class="field" for="new">New password (min <?= $minPassword ?> characters)</label>
            <input class="text" type="password" id="new" name="password" required minlength="<?= $minPassword ?>">
            <label class="field" for="new2">Repeat new password</label>
            <input class="text" type="password" id="new2" name="password2" required minlength="<?= $minPassword ?>">
            <p><button class="btn" type="submit">Update password</button></p>
          </form>
        <?php endif; ?>
      </div>

      <div class="card" style="margin-top:16px">
        <h2 style="font-size:19px">Storage</h2>
        <p class="muted">
          <?= count($allRows) ?> conversation(s). Stored as JSON files in
          <code><?= e($config['data_dir']) ?>/chats</code> — no database.
          Back up that directory to keep your history.
        </p>
      </div>
    </div>

  <?php else: ?>
    <div class="admin-layout">
      <aside class="conv-pane">
        <div class="conv-tools">
          <input type="search" id="filter" placeholder="Search name or message…" aria-label="Search conversations">
        </div>
        <div class="tabs">
          <a href="?filter=all" class="<?= $filter === 'all' ? 'active' : '' ?>">All</a>
          <a href="?filter=open" class="<?= $filter === 'open' ? 'active' : '' ?>">Open</a>
          <a href="?filter=unread" class="<?= $filter === 'unread' ? 'active' : '' ?>">Unread</a>
        </div>
        <div class="conv-list" id="convList"><?= render_list_items($rows, $selectedId) ?></div>
      </aside>

      <section class="thread"
               id="thread"
               data-id="<?= e((string) $selectedId) ?>"
               data-cursor="<?= (int) $totalMessages ?>"
               data-poll="<?= (int) $pollMs ?>">

        <?php if ($conversation === null): ?>
          <div class="thread-head"><h2>No conversation selected</h2></div>
          <div class="thread-body">
            <p class="empty">
              <?= $allRows === [] ? 'Waiting for the first visitor…' : 'Pick a conversation on the left.' ?>
            </p>
          </div>
        <?php else: ?>
          <?php
            $status = (string) ($conversation['status'] ?? Store::STATUS_OPEN);
            $visitorName = (string) ($conversation['name'] ?? '');
            $visitorEmail = (string) ($conversation['email'] ?? '');
          ?>
          <div class="thread-head">
            <div>
              <h2><?= e($visitorName !== '' ? $visitorName : 'Anonymous visitor') ?></h2>
              <div class="who">
                <?php if ($visitorEmail !== ''): ?>
                  <a href="mailto:<?= e($visitorEmail) ?>"><?= e($visitorEmail) ?></a> ·
                <?php endif; ?>
                started <?= e(date('M j, Y H:i', (int) $conversation['created_at'])) ?>
                <?php if (($conversation['page'] ?? '') !== ''): ?>
                  · from <a href="<?= e((string) $conversation['page']) ?>" rel="noreferrer noopener" target="_blank"><?= e(mb_substr((string) $conversation['page'], 0, 60)) ?></a>
                <?php endif; ?>
              </div>
            </div>
            <span class="spacer"></span>
            <span class="tag <?= e($status) ?>" id="statusTag"><?= e($status === 'open' ? 'Open' : 'Closed') ?></span>
            <form method="post" style="display:inline">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= e((string) $selectedId) ?>">
              <input type="hidden" name="status" value="<?= $status === 'open' ? 'closed' : 'open' ?>">
              <button class="btn ghost small" name="action" value="status" type="submit">
                <?= $status === 'open' ? 'Close' : 'Reopen' ?>
              </button>
            </form>
            <form method="post" style="display:inline" onsubmit="return confirm('Delete this conversation permanently?')">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= e((string) $selectedId) ?>">
              <button class="btn ghost small" name="action" value="delete" type="submit">Delete</button>
            </form>
          </div>

          <div class="thread-body" id="threadBody">
            <?= render_messages($conversation['messages'] ?? []) ?>
          </div>

          <form class="reply" method="post" id="replyForm">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="reply">
            <input type="hidden" name="id" value="<?= e((string) $selectedId) ?>">
            <textarea name="message" id="replyText" placeholder="Write a reply… (Ctrl+Enter to send)" required maxlength="<?= (int) ($config['max_message_length'] ?? 4000) ?>"></textarea>
            <div class="row">
              <button class="btn" type="submit">Send reply</button>
              <button class="btn ghost" type="button" id="canned">Insert canned reply</button>
              <span class="hint" id="replyHint"></span>
            </div>
          </form>
        <?php endif; ?>
      </section>
    </div>

    <script>
    (function () {
      'use strict';
      var thread = document.getElementById('thread');
      var body = document.getElementById('threadBody');
      var list = document.getElementById('convList');
      var pill = document.getElementById('unreadPill');
      var hint = document.getElementById('replyHint');
      var id = thread.dataset.id;
      var cursor = parseInt(thread.dataset.cursor, 10) || 0;
      var pollMs = parseInt(thread.dataset.poll, 10) || 4000;

      function atBottom() {
        if (!body) return true;
        return body.scrollHeight - body.scrollTop - body.clientHeight < 80;
      }
      function toBottom() { if (body) body.scrollTop = body.scrollHeight; }
      function note(text) { if (hint) hint.textContent = text; }

      function get(url) {
        return fetch(url, { credentials: 'same-origin' }).then(function (r) { return r.json(); });
      }

      if (body) { toBottom(); }

      // Live list refresh (new conversations, unread counts).
      setInterval(function () {
        get('api.php?what=list&id=' + encodeURIComponent(id || '')).then(function (data) {
          if (!data.ok) return;
          if (list) list.innerHTML = data.list_html;
          if (pill) {
            pill.textContent = data.unread + ' unread';
            pill.className = data.unread > 0 ? 'pill unread' : 'pill';
          }
        }).catch(function () {});
      }, 10000);

      // Live thread refresh.
      if (id) {
        setInterval(function () {
          var stick = atBottom();
          get('api.php?what=thread&id=' + encodeURIComponent(id) + '&after=' + cursor).then(function (data) {
            if (!data.ok) return;
            if (data.reset) {
              body.innerHTML = data.messages_html;
              cursor = data.cursor;
              toBottom();
              return;
            }
            if (data.messages_html) {
              body.insertAdjacentHTML('beforeend', data.messages_html);
              if (stick) toBottom();
              note('New message received');
              setTimeout(function () { note(''); }, 4000);
            }
            cursor = data.cursor;
          }).catch(function () {});
        }, pollMs);
      }

      // Ctrl+Enter sends the reply.
      var replyText = document.getElementById('replyText');
      if (replyText) {
        replyText.addEventListener('keydown', function (event) {
          if (event.key === 'Enter' && (event.ctrlKey || event.metaKey)) {
            event.preventDefault();
            document.getElementById('replyForm').submit();
          }
        });
      }

      // Canned replies, stored per browser.
      var cannedBtn = document.getElementById('canned');
      if (cannedBtn && replyText) {
        var defaults = [
          'Thanks for reaching out! Could you send us a bit more detail?',
          'Thanks — we are looking into this and will update you shortly.',
          'This should be fixed now. Please reload and try again.',
          'Sorry for the trouble! I have escalated this to our team.'
        ];
        cannedBtn.addEventListener('click', function () {
          var stored = null;
          try { stored = JSON.parse(localStorage.getItem('sc_canned') || 'null'); } catch (e) { stored = null; }
          var options = Array.isArray(stored) && stored.length ? stored : defaults;
          var choice = window.prompt('Pick a canned reply:\n' + options.map(function (t, i) { return (i + 1) + '. ' + t; }).join('\n'), '1');
          var index = parseInt(choice, 10);
          if (isNaN(index) || !options[index - 1]) return;
          replyText.value = (replyText.value ? replyText.value.replace(/\s*$/, '') + '\n\n' : '') + options[index - 1];
          replyText.focus();
        });
      }

      // Client-side filter of the sidebar.
      var filterInput = document.getElementById('filter');
      if (filterInput) {
        filterInput.addEventListener('input', function () {
          var needle = filterInput.value.toLowerCase();
          Array.prototype.forEach.call(list.querySelectorAll('.conv'), function (node) {
            node.style.display = node.textContent.toLowerCase().indexOf(needle) === -1 ? 'none' : '';
          });
        });
      }
    })();
    </script>
  <?php endif; ?>
<?php endif; ?>

</body>
</html>
