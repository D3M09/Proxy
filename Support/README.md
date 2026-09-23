# Support Center (PHP, no database)

A small customer support center built with plain PHP:

- **Public support page** with FAQ and a call-to-action that opens the chat.
- **"Online Consultant" chat widget** — one `<script>` tag, embeddable on any site, no dependencies.
- **Agent console** at `/admin/` — shared-password login, conversation queue, live thread, replies, close/reopen, delete.

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
| `data-title` | `Online Consultant` | Header title |
| `data-subtitle` | — | Small line under the title |
| `data-greeting` | `Hi! How can we help you today?` | Bubble shown in an empty thread |
| `data-color` | `#1f6feb` | Accent colour |
| `data-position` | `right` | `right` or `left` |
| `data-poll` | `4000` | Polling interval in ms |

From your own JavaScript:

```js
SupportCenter.open();    // e.g. on a "Chat with us" button
SupportCenter.close();
```

## Configuration

Everything lives in `config.php`: branding, widget text and colours, polling interval,
message limits, FAQ entries, timezone, session name, login throttling, and the optional
pinned admin password hash.

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
└── chats/
    ├── <32 hex id>.json
    └── …
```

Each chat file holds the visitor name/email, the page the chat started from, status,
unread counters and the message list. The id is a 128-bit random token.

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
index.php          public support page (FAQ + widget embed snippet)
widget.js          self-contained chat widget (CSS + DOM injected, no deps)
api.php            public JSON API: start / send / poll
config.php         all settings
lib/bootstrap.php  config + session + store wiring
lib/store.php      flat-file conversation store (flock, atomic writes)
lib/http.php       escaping, JSON output, CSRF, time helpers
lib/throttle.php   file-backed rate limiter
admin/index.php    agent console (login, queue, reply, settings)
admin/api.php      JSON for the console's live refresh
admin/partials.php shared HTML rendering
assets/style.css   styles for both the public page and the console
```
