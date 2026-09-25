# Support Center (PHP, no database)

A small customer support center built with plain PHP:

- **Public support page** — a bare, centred stage that opens the consultant widget on load.
- **"Online Consultant" chat widget** — one `<script>` tag, embeddable on any site, no dependencies, with template replies and scripted auto-replies.
- **Agent console** at `/admin/` — shared-password login, conversation queue, live thread, replies, close/reopen, delete.
- **Attachments** — both visitors and agents can send images and video. Uploads land in `data/uploads/` and are served back through `media.php`.

There is **no database**. Conversations are stored as JSON files under `data/chats/`, each guarded by `flock()`, so you can back everything up by copying one folder.

## Requirements

- PHP 8.0 or newer with `json`, `mbstring` and `session` (all standard).
- A writable `data/` directory. Nothing else.

## Quick start

```bash
php -S 127.0.0.1:8000 -t .
# open http://127.0.0.1:8000/
```

On a shared host, upload the folder and open your domain in a browser.

**First run:** open `/admin/` and you will be asked to create the admin password.
It is stored hashed (bcrypt) in `data/admin.json` and cannot be recovered — save it
somewhere safe. You can change it later under **Settings**.

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
├── admin.json       # bcrypt hash of the admin password
├── throttle.json    # hashed failed-login counters
├── chats/
│   ├── <32 hex id>.json
│   └── …
└── uploads/
    └── <32 hex token>   # an attachment, no extension, served only via media.php
```

Each chat file holds the visitor name/email, the page the chat started from, status,
unread counters and the message list. The id is a 128-bit random token. A message that
carries an attachment also holds a `file` block (token, detected type, original name,
size); the bytes live in `data/uploads/<token>`. Deleting a conversation deletes its
attachments with it, so `uploads/` cannot accumulate orphans.

**Backing up** = copy `data/`. **Deleting history** = delete files in `data/chats/`.

### When to move off flat files

Flat files are fine for a small team and hundreds to a few thousand conversations. You
should move to SQLite (or MySQL) when you expect heavy traffic, want to keep many
thousands of chats, or need search/reporting. `lib/store.php` is the only file that
touches storage — its public methods (`create`, `get`, `poll`, `addMessage`,
`setStatus`, `listConversations`, `delete`) are what a database-backed implementation
would need to provide.

## Security notes

Already handled:

- Passwords are hashed with `password_hash()`; nothing is stored in plain text.
- All state-changing admin requests require a CSRF token; `SameSite=Lax`, `HttpOnly`
  session cookies.
- Login is throttled per IP (8 failures / 15 min by default) from `data/throttle.json`.
- Conversation ids are unguessable, validated against `^[a-f0-9]{32}$`, and never used to
  build a path directly — path traversal is not possible.
- All user content is escaped on output and limits are enforced on input.
- `api.php` rate-limits new chats and message sending per IP.
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

1. **Serve over HTTPS.** Set `'bind_chat_to_session' => true` in `config.php` if the
   widget is only ever used on the same domain as this app — that ties a conversation to
   the visitor's PHP session on top of the secret id.
2. **Protect `data/`.** Apache already is via `data/.htaccess`. On nginx add:
   ```nginx
   location ^~ /data/ { deny all; }
   location ~ ^/lib/ { deny all; }
   ```
3. Keep PHP and the OS patched, and change the admin password if it may have leaked.
4. Every visitor message is untrusted input — never paste it into a shell or SQL string.

## Layout

```
index.php          public support page (centred stage + widget, self-hosted)
widget.js          self-contained chat widget (CSS + DOM injected, no deps)
api.php            public JSON API: start / send / poll
config.php         all settings
lib/bootstrap.php  config + session + store wiring
lib/store.php      flat-file conversation store (flock, atomic writes)
lib/http.php       escaping, JSON output, CSRF, time helpers
lib/throttle.php   file-backed rate limiter
lib/upload.php     attachment validation and safe storage
media.php          serves attachments (authorised, supports video Range)
admin/index.php    agent console (login, queue, reply, settings)
admin/api.php      JSON for the console's live refresh
admin/partials.php shared HTML rendering
assets/style.css   styles for both the public page and the console
```
