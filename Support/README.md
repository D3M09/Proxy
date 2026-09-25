# Support Center (PHP, no database)

A small customer support center built with plain PHP:

- **Public support page** — a bare, centred stage that opens the consultant widget on load.
- **"Online Consultant" chat widget** — one `<script>` tag, embeddable on any site, no dependencies, with template replies and scripted auto-replies.
- **Agent console** at `/admin/` — an owner login plus per-agent logins, a conversation queue, live thread, replies, close/reopen, transfer to another agent, delete.
- **Hardened by default** — nonce-based CSP, security headers, session-fixation and login-throttle protection, and a dev-server router that keeps `data/` off the web. See [Security notes](#security-notes).
- **Team roster** — every agent has a name, a login id, an optional profile photo and one or more **departments** (Dps). Each agent sees who is on the team, who is online, and how many chats everybody is handling.
- **Attachments** — both visitors and agents can send images and video. Uploads land in `data/uploads/` and are served back through `media.php`.

There is **no database**. Conversations are stored as JSON files under `data/chats/`, each guarded by `flock()`, so you can back everything up by copying one folder.

## Requirements

- PHP 8.0 or newer with `json`, `mbstring` and `session` (all standard).
- A writable `data/` directory. Nothing else.

## Quick start

```bash
php -S 127.0.0.1:8000 router.php
# open http://127.0.0.1:8000/
```

Use `router.php`, not `-t .`. It is the front controller for the built-in server and
refuses to serve `data/`, `lib/` and `config.php`. **Without it the built-in server
hands out every conversation and both password-hash files as plain text** — `.htaccess`
is an Apache-only mechanism and PHP's own server ignores it completely.

On a shared host, upload the folder and open your domain in a browser. Apache is covered
by `.htaccess` and `data/.htaccess`; nginx needs the snippet under
[Protect `data/`](#what-you-should-do).

**First run:** open `/admin/` and sign in with **`admin`** / **`password`**. No account
exists yet — the owner login comes from `admin_login` + `admin_password_hash` in
`config.php`, and no agent accounts are created until you add them under **Settings**.

> Change that password before anyone else can reach the install. The shipped hash *is*
the word `password`, and the console will keep warning you until you replace it:
>
> ```bash
> php -r "echo password_hash('your-new-password', PASSWORD_DEFAULT), PHP_EOL;"
> ```
>
> Paste the result over `admin_password_hash` in `config.php`. Leave it empty instead and
the first visit to `/admin/` asks you to create one, storing the hash in
`data/admin.json`.

Then open the home page, click the chat button, and send a test message. It shows up
in `/admin/` within a few seconds.

## Embedding the widget on another site

```html
<script src="https://support.example.com/widget.js" async></script>
```

The widget derives its API URL from the script's own origin, so no configuration is
required. Optional attributes:

| Attribute | Default | Purpose |
| --- | --- | --- |
| `data-support-url` | script origin | Base URL of this support center |
| `data-title` | `Online Consultant` | Header brand |
| `data-subtitle` | — | Small line under the brand |
| `data-notice` | — | Scrolling notice under the header (empty hides it) |
| `data-greeting` | `Hi! How can we help you today?` | Bubble shown in an empty thread |
| `data-quick-replies` | — | JSON array of quick-reply button labels |
| `data-color` | `#1762f6` | Accent colour |
| `data-position` | `right` | `right` or `left` |
| `data-poll` | `4000` | Polling interval in ms |
| `data-auto-open` | — | `1` opens the panel on load |

From your own JavaScript:

```js
SupportCenter.open();    // e.g. on a "Chat with us" button
SupportCenter.close();
```

## Configuration

Everything lives in `config.php`: branding, widget text and colours, the template replies
and their scripted answers, polling interval, message limits, timezone, session name,
login throttling, and the optional pinned admin password hash.

`widget_mode` picks the widget layout:

- `page` (the default) — the chat fills the viewport, so the whole page *is* the message
  box, as on the reference consultant page.
- `bubble` — the usual floating launcher in the corner. Use this for the embeddable
  snippet on other sites.

### Template replies and scripted auto-replies

`widget_quick_replies` renders a button group in an empty thread. Clicking one sends its
label immediately: the visitor does **not** have to give a name or an email, and the
console labels such conversations *Anonymous visitor*.

`auto_replies` maps an exact visitor message to an agent answer. The template labels are
the keys, so clicking a template gets an answer with no agent online — the same way the
reference widget behaves. The check runs in `api.php`, so the scripted answer is stored
in the conversation and appears in the console too. Set it to `[]` to switch auto-reply
off.

Set `base_url` once you are on a real domain so the embed snippet and absolute links are
correct behind a proxy:

```php
'base_url' => 'https://support.example.com',
```

## How conversations are stored

```
data/
├── .htaccess        # denies web access on Apache
├── admin.json       # bcrypt hash of the owner password
├── agents.json      # the team roster (see below)
├── throttle.json    # hashed failed-login counters
├── chats/
│   ├── <32 hex id>.json
│   └── …
├── presence/        # typing + read marks, one tiny file per conversation
└── uploads/
    └── <32 hex token>   # an attachment, no extension, served only via media.php
```

`data/.htaccess` denies this whole tree to Apache, and `router.php` denies it to the
built-in server. On anything else (nginx, or Apache with `AllowOverride None`) you must
add the rules under [What you should do](#what-you-should-do) — or point `data_dir`
somewhere outside the document root, which needs no server configuration at all.

Each chat file holds the visitor name/email, the page the chat started from, the visitor
IP and User-Agent (see [the console's visitor panel](#the-agent-console)), status, unread
counters and the message list. The id is a 128-bit random token. A message that carries
an attachment also holds a `file` block (token, detected type, original name, size); the
bytes live in `data/uploads/<token>`. Deleting a conversation deletes its attachments
with it, so `uploads/` cannot accumulate orphans.

**Backing up** = copy `data/`. **Deleting history** = delete files in `data/chats/`.

### When to move off flat files

Flat files are fine for a small team and hundreds to a few thousand conversations. You
should move to SQLite (or MySQL) when you expect heavy traffic, want to keep many
thousands of chats, or need search/reporting. `lib/store.php` is the only file that
touches storage — its public methods (`create`, `get`, `poll`, `addMessage`,
`setStatus`, `listConversations`, `delete`) are what a database-backed implementation
would need to provide.

## The agent console

`/admin/` is a Bangla-first console with an inbox, a thread and a visitor panel:

- **Inbox** (left) — search, filter tabs (*সব / চালু / অপঠিত / বন্ধ*) with live counts, and
  the conversation queue. Rows show an avatar, the reference id, a preview, the status,
  the message count and an unread badge.
- **Thread** (centre) — the transcript with date separators, role labels, attachment
  cards, the visitor's read receipt (`✓✓ দেখা হয়েছে`), a typing indicator, quick-reply
  chips, and the reply box. Messages, presence and the queue refresh over `admin/api.php`
  without a page reload; the plain form POST remains as a no-JS fallback.
- **Visitor panel** (right, from 1280px up) — reference id, start time, the page the chat
  started from, IP address and browser/platform, message and unread counts. Below 1280px
  the same three values appear in a compact strip under the thread header, and on phones
  the queue lives in a bottom sheet.

The visitor's **IP address and User-Agent are captured once, when the conversation
starts** (`client_ip()` / `client_user_agent()` in `lib/http.php`), stored in the chat
file's `visitor` block and rendered only inside the password-protected console. They are
never echoed back to the visitor. Conversations created before this was recorded simply
show `—`. The browser/platform label is plain substring sniffing (`format_device()` in
`admin/partials.php`) — no third-party service, and no geolocation.

### Agents, departments and transfers

The console supports two kinds of login, with one password box:

| Login | What you type | What you get |
| --- | --- | --- |
| **Owner** | the id in `admin_login` (`admin` by default) | the `admin_password_hash` from `config.php` — full access, including the roster |
| **Agent** | the agent's own login id | that agent's own password — the queue, plus their own password form |

Only the owner id can manage the roster, and the roster refuses to hand that id to an agent
account, so the two can never be confused at sign-in. Every other login id is looked up in
`data/agents.json`.

Agents are stored in `data/agents.json` (a flat file like the conversations, `flock()`-guarded,
`0600`). Each record holds an id, a login id, a display name, an optional photo URL, a bcrypt
password hash, and a list of **departments**. `admin_departments` in `config.php` is the
vocabulary those lists are drawn from.

- **Transferring** — the thread header has a *ট্রান্সফার* picker listing every agent with their
departments. Transfers are deliberately *quiet*: they record `assignee`, `assignee_at` and
`assignee_by` on the conversation **without** touching `updated_at` (so the queue does not
reshuffle under whoever is working) and without posting anything the visitor can see. Setting
the picker back to *বরাদ্দহীন* releases the chat.
- **Queue filters** — the *সব / চালু / অপঠিত / বন্ধ* tabs work as before; `?filter=mine` narrows to
the signed-in agent, `?filter=unassigned` to released chats, and a roster link opens
`?filter=agent&agent=<id>`. The filter travels with every poll and reply, so the live refresh
never silently widens it.
- **Team page** (`?view=agents`) — the list of everyone, with photo, name, login id, departments,
chats handled (open / closed), last-seen and join date. Each row links into that agent's queue.
- **Managing the roster** — owners get a roster editor under **Settings**: add an agent, edit any
name/handle/photo/departments, set any password, remove an agent. A signed-in agent sees only
their own password form. Deleting an agent releases (but does not delete) their conversations.

Departments are display/labelling metadata: they describe what an agent covers and appear in the
transfer picker and the roster. They are not a hard access rule — every signed-in agent can still
read every conversation, exactly as with the old shared password.

#### Demo team (off by default)

**A fresh install creates no agent accounts** — `admin_demo_password` is empty, so
`seedDemo()` does nothing and the owner login is the only way in. Set it to a password if you
want six sample agents created on the first console visit, so you can try the roster, the
departments and the transfer picker immediately:

| Login id | Name | Departments |
| --- | --- | --- |
| `kabir` | কবির হোসেন | জমা |
| `rahim` | রহিম আহমেদ | উত্তোলন |
| `tania` | তানিয়া আক্তার | অ্যাকাউন্ট |
| `nasrin` | নাসরিন সুলতানা | জমা, উত্তোলন |
| `sakib` | সাকিব রহমান | অ্যাকাউন্ট, জমা |
| `mehedi` | মেহেদী হাসান (সুপারভাইজার) | জমা, উত্তোলন, অ্যাকাউন্ট |

They are seeded **once** — if `data/agents.json` already exists nothing is re-added, so deleting
them sticks. Every one of them shares the single `admin_demo_password`, and that password is
published right here: leaving the seed on for an install strangers can reach is the same as
having no password at all. The console shows a permanent warning while any demo account
remains, and warns again if the owner password is still a well-known default.

Quick-reply chips above the composer come from `admin_canned_replies` in `config.php`;
clicking one appends its text to the reply box (the agent can still edit it). The
*আরও দ্রুত উত্তর* button keeps the older per-browser list in `localStorage`.

The console's own labels live in `console_label()` in `admin/partials.php`, so the
server-rendered page and the JSON polls always print the same strings.

## Security notes

Already handled:

- Passwords are hashed with `password_hash()`; nothing is stored in plain text.
- All state-changing admin requests require a CSRF token; `SameSite=Lax`, `HttpOnly`
  session cookies, `session.use_strict_mode` on, and the session id is regenerated on login
  (so a planted session id is useless).
- Login is throttled per IP **and per account** (8 failures / 15 min by default) from
  `data/throttle.json`, so a distributed attacker cannot grind one password from many
  addresses. An unknown login id still pays for a bcrypt verify, so response timing does not
  reveal which ids exist.
- Every response carries `X-Content-Type-Options: nosniff`, `Referrer-Policy: no-referrer`,
  `X-Frame-Options`, a `Permissions-Policy`, and a `Content-Security-Policy`. The console's
  CSP uses a per-request **nonce** and no `'unsafe-inline'` for scripts, so an injected
  `<script>` will not run even if one is ever reflected into the page.
- `data/`, `lib/` and `config.php` are refused at the web layer (`router.php` on the built-in
  server, `.htaccess` + `data/.htaccess` on Apache), not only by PHP.
- The `Host` header is validated before it is used in a URL or echoed into the page, and the
  public page's JSON island is escaped with `JSON_HEX_TAG`/`JSON_HEX_QUOT`, so a crafted host
  cannot break out of an attribute or a `<script>` block.
- Visitor IP and User-Agent are stored per conversation for the agent console and are
  never sent back to the browser; drop the `visitor` block from `admin/partials.php` and
  the `create()` call in `api.php` if you would rather not record them at all.
- Conversation ids are unguessable, validated against `^[a-f0-9]{32}$`, and never used to
  build a path directly — path traversal is not possible.
- All user content is escaped on output and limits are enforced on input.
- `api.php` rate-limits new chats and message sending **per visitor IP, with a separate
  budget each** (`chat_start_*` and `chat_send_*` in `config.php` — 20 new chats / 15 min and
  60 messages / 30 s by default). They do not share a counter with each other or with the
  login limiter, because one tap through the menu tree costs a message and a real
  conversation must not be capped by how many chats the visitor has opened.
  The send window doubles as the penalty: it is a *fixed* window anchored at the visitor's
  first message, so a blocked visitor waits at most `chat_send_window` seconds before the
  counter resets and they can send again. Keeping that number small matters more than it
  looks — a long window means a long lockout. Blocked requests never increment the counter,
  so retrying while blocked cannot extend the block.
  Note both limits are per IP, so visitors behind one NAT or carrier address share the budget.
- Uploads are checked against the file's **real** content type (`finfo`), never the type
  the browser claims, and only types listed in `allowed_media` are kept. SVG is excluded
  because it can carry script.
- Stored attachments have no filename extension and sit in `data/uploads/`, below the
  `data/.htaccess` deny rule, so nothing a visitor sends can be executed or fetched
  directly. Every download goes out through `media.php` with a fixed `Content-Type` and
  `X-Content-Type-Options: nosniff`.
- `media.php` requires either an agent session or the conversation id, and the token must
  actually be referenced by that conversation — a guessed token alone gets a 404.

What you should do:

1. **Change the owner password.** `admin` / `password` is a starting point, not a password.
   The console nags until `admin_password_hash` no longer matches a known default.
2. **Serve over HTTPS.** Set `'bind_chat_to_session' => true` in `config.php` if the
   widget is only ever used on the same domain as this app — that ties a conversation to
   the visitor's PHP session on top of the secret id.
3. **Protect `data/`.** Apache is covered by `.htaccess` and `data/.htaccess` (unless the
   host sets `AllowOverride None`, in which case neither is read — check that, or move
   `data_dir` outside the document root). nginx needs:
   ```nginx
   location ^~ /data/ { deny all; }
   location ~ ^/lib/ { deny all; }
   location = /config.php { deny all; }
   location ~ /\. { deny all; }
   ```
   The most robust option on any server is to keep the data outside the web root:
   ```php
   'data_dir' => '/var/lib/support-center/data',
   ```
4. **Never run the built-in server without `router.php`**, and never leave a dev server
   listening on a public interface.
5. **Tune the chat limits** if your visitors are heavy menu users or share an IP: the
   numbers are `chat_send_max_attempts` / `chat_send_window` in `config.php`.
6. Keep PHP and the OS patched, and change the admin password if it may have leaked.
7. Every visitor message is untrusted input — never paste it into a shell or SQL string.

## Layout ```
index.php          public support page (centred stage + widget, self-hosted)
widget.js          self-contained chat widget (CSS + DOM injected, no deps)
api.php            public JSON API: start / send / poll
router.php         front controller for `php -S` — refuses data/, lib/, config.php
.htaccess          Apache deny rules + hardening headers
config.php         all settings
lib/bootstrap.php  config + session + store wiring
lib/store.php      flat-file conversation store (flock, atomic writes)
lib/agents.php     flat-file team roster (roster, departments, per-agent passwords)
lib/http.php       escaping, JSON output, CSRF, time helpers
lib/throttle.php   file-backed rate limiter
lib/upload.php     attachment validation and safe storage
media.php          serves attachments (authorised, supports video Range)
admin/index.php    agent console (login, queue, reply, settings)
admin/api.php      JSON for the console's live refresh
admin/partials.php shared HTML rendering
assets/style.css   styles for both the public page and the console
```
