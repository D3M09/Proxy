/*!
 * Support Center chat widget ("Online Consultant").
 *
 * Self contained: injects its own CSS and DOM, no dependencies.
 *
 *   <script src="https://support.example.com/widget.js" async></script>
 *
 * Optional data-* attributes on the script tag:
 *   data-support-url  base URL of the support center (defaults to script origin)
 *   data-title        header title
 *   data-subtitle     header subtitle
 *   data-greeting     first bubble shown in the empty thread
 *   data-color        accent colour
 *   data-position     "right" (default) or "left"
 *   data-poll         polling interval in ms
 */
(function () {
  'use strict';

  if (window.__supportCenterWidget) {
    return;
  }
  window.__supportCenterWidget = true;

  var script = document.currentScript;
  var STORAGE_KEY = 'sc_chat_id';

  function attr(name, fallback) {
    if (script && script.getAttribute) {
      var value = script.getAttribute('data-' + name);
      if (value !== null && value !== '') {
        return value;
      }
    }
    return fallback;
  }

  var base = attr('support-url', '');
  if (!base && script && script.src) {
    try {
      base = new URL(script.src, location.href).origin;
    } catch (e) {
      base = '';
    }
  }
  base = base.replace(/\/+$/, '');

  var config = {
    api: base + '/api.php',
    title: attr('title', 'Online Consultant'),
    subtitle: attr('subtitle', ''),
    greeting: attr('greeting', 'Hi! How can we help you today?'),
    color: attr('color', '#1f6feb'),
    position: attr('position', 'right') === 'left' ? 'left' : 'right',
    poll: parseInt(attr('poll', '4000'), 10) || 4000
  };

  var state = {
    id: null,
    cursor: 0,
    open: false,
    unread: 0,
    sending: false,
    timer: null
  };

  var el = {};

  // ---------------------------------------------------------------------------
  // Styles
  // ---------------------------------------------------------------------------
  var css = [
    '.sc-root{--sc-accent:' + config.color + ';position:fixed;bottom:20px;z-index:2147483000;',
    'font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;',
    'font-size:14px;line-height:1.45;color:#1f2933;-webkit-font-smoothing:antialiased}',
    '.sc-root.sc-right{right:20px}.sc-root.sc-left{left:20px}',
    '.sc-root *,.sc-root *::before,.sc-root *::after{box-sizing:border-box}',
    '.sc-launcher{display:flex;align-items:center;justify-content:center;width:58px;height:58px;',
    'margin-left:auto;border:0;border-radius:50%;background:var(--sc-accent);color:#fff;cursor:pointer;',
    'box-shadow:0 6px 20px rgba(15,23,42,.28);transition:transform .15s ease}',
    '.sc-left .sc-launcher{margin-left:0;margin-right:auto}',
    '.sc-launcher:hover{transform:scale(1.06)}',
    '.sc-launcher svg{width:26px;height:26px;fill:currentColor}',
    '.sc-badge{position:absolute;top:-4px;right:-4px;min-width:20px;height:20px;padding:0 5px;',
    'border-radius:10px;background:#e5484d;color:#fff;font-size:12px;font-weight:600;',
    'display:flex;align-items:center;justify-content:center;box-shadow:0 0 0 2px #fff}',
    '.sc-badge[hidden]{display:none}',
    '.sc-panel{position:absolute;bottom:74px;width:368px;max-width:calc(100vw - 32px);height:min(560px,calc(100vh - 120px));',
    'background:#fff;border-radius:16px;overflow:hidden;display:none;flex-direction:column;',
    'box-shadow:0 18px 50px rgba(15,23,42,.28)}',
    '.sc-right .sc-panel{right:0}.sc-left .sc-panel{left:0}',
    '.sc-panel.sc-open{display:flex}',
    '.sc-head{display:flex;align-items:center;gap:10px;padding:14px 16px;background:var(--sc-accent);color:#fff}',
    '.sc-head-text{flex:1;min-width:0}',
    '.sc-head strong{display:block;font-size:15px;font-weight:600}',
    '.sc-head span{display:block;font-size:12px;opacity:.85}',
    '.sc-close{border:0;background:transparent;color:#fff;font-size:22px;line-height:1;cursor:pointer;padding:4px 6px;border-radius:8px}',
    '.sc-close:hover{background:rgba(255,255,255,.16)}',
    '.sc-body{flex:1;overflow-y:auto;padding:14px;background:#f6f8fa}',
    '.sc-msg{max-width:82%;margin:0 0 10px;padding:9px 12px;border-radius:14px;white-space:pre-wrap;',
    'overflow-wrap:anywhere;background:#fff;border:1px solid #e4e7eb;box-shadow:0 1px 2px rgba(15,23,42,.05)}',
    '.sc-msg.sc-agent{border-bottom-left-radius:4px}',
    '.sc-msg.sc-visitor{margin-left:auto;background:var(--sc-accent);border-color:transparent;color:#fff;border-bottom-right-radius:4px}',
    '.sc-msg time{display:block;margin-top:4px;font-size:11px;opacity:.6}',
    '.sc-note{margin:0 0 12px;padding:8px 12px;border-radius:10px;background:#eef2f7;color:#52606d;font-size:12.5px}',
    '.sc-form{display:flex;flex-direction:column;gap:8px}',
    '.sc-form label{font-size:12px;font-weight:600;color:#52606d}',
    '.sc-form input,.sc-composer textarea{width:100%;padding:9px 11px;border:1px solid #cfd6dd;border-radius:10px;',
    'font:inherit;color:inherit;background:#fff;resize:vertical}',
    '.sc-form input:focus,.sc-composer textarea:focus{outline:2px solid var(--sc-accent);outline-offset:-1px;border-color:var(--sc-accent)}',
    '.sc-form button,.sc-composer button{border:0;border-radius:10px;background:var(--sc-accent);color:#fff;',
    'font:inherit;font-weight:600;padding:10px 14px;cursor:pointer}',
    '.sc-form button:disabled,.sc-composer button:disabled{opacity:.6;cursor:default}',
    '.sc-composer{display:flex;gap:8px;align-items:flex-end;padding:10px;border-top:1px solid #e4e7eb;background:#fff}',
    '.sc-composer textarea{flex:1;max-height:110px;min-height:40px}',
    '.sc-error{margin:0;color:#c62a2f;font-size:12.5px}',
    '.sc-error[hidden]{display:none}',
    '.sc-hint{padding:0 12px 10px;background:#fff;color:#7b8794;font-size:11.5px;text-align:center}',
    '@media (max-width:480px){.sc-panel{width:calc(100vw - 24px);height:calc(100vh - 120px)}}'
  ].join('');

  var style = document.createElement('style');
  style.textContent = css;
  document.head.appendChild(style);

  // ---------------------------------------------------------------------------
  // Markup
  // ---------------------------------------------------------------------------
  var root = document.createElement('div');
  root.className = 'sc-root ' + (config.position === 'left' ? 'sc-left' : 'sc-right');
  root.innerHTML = [
    '<div class="sc-panel" role="dialog" aria-label="' + escapeAttr(config.title) + '" aria-modal="false">',
    '<div class="sc-head"><div class="sc-head-text"><strong></strong><span></span></div>',
    '<button type="button" class="sc-close" aria-label="Close chat">&times;</button></div>',
    '<div class="sc-body" aria-live="polite"></div>',
    '<div class="sc-hint" hidden></div>',
    '</div>',
    '<button type="button" class="sc-launcher" aria-label="Open support chat" aria-expanded="false">',
    '<span class="sc-badge" hidden>0</span>',
    '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3C6.9 3 2.8 6.5 2.8 10.8c0 2.5 1.4 4.7 3.6 6.1-.2 1.3-.8 2.6-1.8 3.6-.2.2 0 .6.3.5 2-.4 3.7-1.2 5-2.2 .7.1 1.4.2 2.1.2 5.1 0 9.2-3.5 9.2-7.8S17.1 3 12 3z"/></svg>',
    '</button>'
  ].join('');

  function escapeAttr(value) {
    return String(value).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  document.body.appendChild(root);

  el.panel = root.querySelector('.sc-panel');
  el.body = root.querySelector('.sc-body');
  el.headTitle = root.querySelector('.sc-head strong');
  el.headSubtitle = root.querySelector('.sc-head span');
  el.close = root.querySelector('.sc-close');
  el.launcher = root.querySelector('.sc-launcher');
  el.badge = root.querySelector('.sc-badge');
  el.hint = root.querySelector('.sc-hint');

  el.headTitle.textContent = config.title;
  if (config.subtitle) {
    el.headSubtitle.textContent = config.subtitle;
  } else {
    el.headSubtitle.hidden = true;
  }
  el.hint.textContent = 'Your messages are stored on ' + (base ? base.replace(/^https?:\/\//, '') : location.host) + '.';

  // ---------------------------------------------------------------------------
  // Rendering
  // ---------------------------------------------------------------------------
  function addBubble(role, text, at) {
    showGreeting(false);
    var bubble = document.createElement('div');
    bubble.className = 'sc-msg ' + (role === 'agent' ? 'sc-agent' : 'sc-visitor');
    var p = document.createElement('div');
    p.textContent = text;
    bubble.appendChild(p);
    if (at) {
      var time = document.createElement('time');
      time.dateTime = new Date(at * 1000).toISOString();
      time.textContent = new Date(at * 1000).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      bubble.appendChild(time);
    }
    el.body.appendChild(bubble);
    el.body.scrollTop = el.body.scrollHeight;
  }

  var greetingNode = null;
  function showGreeting(show) {
    if (show) {
      if (!greetingNode) {
        greetingNode = document.createElement('p');
        greetingNode.className = 'sc-note';
        greetingNode.textContent = config.greeting;
        el.body.appendChild(greetingNode);
      }
      greetingNode.hidden = false;
    } else if (greetingNode) {
      greetingNode.hidden = true;
    }
  }

  function renderStartForm() {
    el.body.textContent = '';
    greetingNode = null;
    showGreeting(true);

    var form = document.createElement('form');
    form.className = 'sc-form';
    form.innerHTML = [
      '<label for="sc-name">Your name *</label>',
      '<input id="sc-name" name="name" maxlength="80" required autocomplete="name">',
      '<label for="sc-email">Email (optional)</label>',
      '<input id="sc-email" name="email" type="email" maxlength="160" autocomplete="email">',
      '<label for="sc-first">How can we help? *</label>',
      '<textarea id="sc-first" name="message" rows="3" maxlength="4000" required></textarea>',
      '<p class="sc-error" hidden></p>',
      '<button type="submit">Start chat</button>'
    ].join('');
    el.body.appendChild(form);

    var nameInput = form.querySelector('#sc-name');
    var error = form.querySelector('.sc-error');
    var submit = form.querySelector('button');

    nameInput.value = localStorage.getItem('sc_name') || '';
    form.querySelector('#sc-email').value = localStorage.getItem('sc_email') || '';
    nameInput.focus();

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      error.hidden = true;
      submit.disabled = true;
      submit.textContent = 'Starting…';

      var payload = new URLSearchParams({
        action: 'start',
        name: form.name.value.trim(),
        email: form.email.value.trim(),
        message: form.message.value.trim(),
        page: location.href
      });

      request(payload).then(function (data) {
        localStorage.setItem('sc_name', form.name.value.trim());
        localStorage.setItem('sc_email', form.email.value.trim());
        localStorage.setItem(STORAGE_KEY, data.conversation.id);
        state.id = data.conversation.id;
        state.cursor = 0;
        el.body.textContent = '';
        greetingNode = null;
        ingest(data);
        renderComposer(data.conversation.status);
        startPolling();
      }).catch(function (err) {
        error.textContent = err.message || 'Could not start the chat.';
        error.hidden = false;
        submit.disabled = false;
        submit.textContent = 'Start chat';
      });
    });
  }

  function renderComposer(status) {
    var existing = root.querySelector('.sc-composer');
    if (status === 'closed') {
      if (existing) {
        existing.remove();
      }
      var note = root.querySelector('.sc-closed-note');
      if (!note) {
        note = document.createElement('p');
        note.className = 'sc-note sc-closed-note';
        note.textContent = 'This conversation was closed. Send a message to reopen it.';
        el.body.appendChild(note);
        el.body.scrollTop = el.body.scrollHeight;
      }
    }

    if (existing) {
      return;
    }

    var composer = document.createElement('div');
    composer.className = 'sc-composer';
    composer.innerHTML = [
      '<textarea rows="1" maxlength="4000" placeholder="Write a message…" aria-label="Your message"></textarea>',
      '<button type="button" aria-label="Send message">Send</button>'
    ].join('');
    el.panel.appendChild(composer);

    var textarea = composer.querySelector('textarea');
    var button = composer.querySelector('button');

    function send() {
      var text = textarea.value.trim();
      if (!text || state.sending) {
        return;
      }
      state.sending = true;
      button.disabled = true;

      request(new URLSearchParams({ action: 'send', id: state.id, message: text }))
        .then(function (data) {
          textarea.value = '';
          textarea.style.height = 'auto';
          var closed = root.querySelector('.sc-closed-note');
          if (closed && data.conversation.status === 'open') {
            closed.remove();
          }
          ingest(data);
        })
        .catch(function (err) {
          window.alert(err.message || 'Message could not be sent.');
        })
        .finally(function () {
          state.sending = false;
          button.disabled = false;
          textarea.focus();
        });
    }

    button.addEventListener('click', send);
    textarea.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        send();
      }
    });
    textarea.addEventListener('input', function () {
      textarea.style.height = 'auto';
      textarea.style.height = Math.min(textarea.scrollHeight, 110) + 'px';
    });
  }

  /** Append messages from an API response and advance the cursor. */
  function ingest(data) {
    (data.messages || []).forEach(function (message) {
      addBubble(message.role, message.text, message.at);
      if (message.role === 'agent' && !state.open) {
        setUnread(state.unread + 1);
      }
      state.cursor = Math.max(state.cursor, message.index + 1);
    });
    if (typeof data.total === 'number') {
      state.cursor = Math.max(state.cursor, data.total);
    }
    if (data.conversation && data.conversation.status) {
      renderComposer(data.conversation.status);
    }
  }

  function setUnread(count) {
    state.unread = count;
    el.badge.textContent = count > 9 ? '9+' : String(count);
    el.badge.hidden = count === 0;
  }

  function request(params) {
    return fetch(config.api, {
      method: 'POST',
      credentials: 'omit',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
      body: params.toString()
    }).then(parse);
  }

  function parse(response) {
    return response.json().catch(function () {
      throw new Error('Unexpected server response (' + response.status + ').');
    }).then(function (data) {
      if (!response.ok || !data.ok) {
        throw new Error(data && data.error ? data.error : 'Request failed.');
      }
      return data;
    });
  }

  // ---------------------------------------------------------------------------
  // Polling
  // ---------------------------------------------------------------------------
  function startPolling() {
    stopPolling();
    state.timer = window.setInterval(tick, state.open ? config.poll : Math.max(config.poll, 15000));
  }

  function stopPolling() {
    if (state.timer) {
      window.clearInterval(state.timer);
      state.timer = null;
    }
  }

  function tick() {
    if (!state.id) {
      return;
    }
    var url = config.api + '?action=poll&id=' + encodeURIComponent(state.id) + '&after=' + state.cursor;
    fetch(url, { credentials: 'omit' })
      .then(parse)
      .then(ingest)
      .catch(function (err) {
        // A deleted conversation just means we start over.
        if (/not found/i.test(err.message)) {
          localStorage.removeItem(STORAGE_KEY);
          state.id = null;
          state.cursor = 0;
          stopPolling();
          renderStartForm();
        }
      });
  }

  // ---------------------------------------------------------------------------
  // Open / close
  // ---------------------------------------------------------------------------
  function open() {
    state.open = true;
    el.panel.classList.add('sc-open');
    el.launcher.setAttribute('aria-expanded', 'true');
    setUnread(0);
    startPolling();
    var textarea = root.querySelector('.sc-composer textarea');
    if (textarea) {
      textarea.focus();
    }
    tick();
  }

  function close() {
    state.open = false;
    el.panel.classList.remove('sc-open');
    el.launcher.setAttribute('aria-expanded', 'false');
    startPolling();
  }

  el.launcher.addEventListener('click', function () {
    if (state.open) {
      close();
    } else {
      open();
    }
  });
  el.close.addEventListener('click', close);
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && state.open) {
      close();
    }
  });

  // ---------------------------------------------------------------------------
  // Boot: restore an existing conversation if this browser has one
  // ---------------------------------------------------------------------------
  function boot() {
    var saved = null;
    try {
      saved = localStorage.getItem(STORAGE_KEY);
    } catch (e) {
      saved = null;
    }

    if (!saved || !/^[a-f0-9]{32}$/.test(saved)) {
      renderStartForm();
      return;
    }

    state.id = saved;
    state.cursor = 0;

    fetch(config.api + '?action=poll&id=' + encodeURIComponent(saved) + '&after=0', { credentials: 'omit' })
      .then(parse)
      .then(function (data) {
        el.body.textContent = '';
        greetingNode = null;
        ingest(data);
        renderComposer(data.conversation.status);
        startPolling();
      })
      .catch(function () {
        localStorage.removeItem(STORAGE_KEY);
        state.id = null;
        renderStartForm();
      });
  }

  // Small public API so page buttons can open the chat, e.g.
  //   <button onclick="SupportCenter.open()">Chat with us</button>
  window.SupportCenter = { open: open, close: close, toggle: function () { state.open ? close() : open(); } };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
