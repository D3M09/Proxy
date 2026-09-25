/*!
 * Support Center chat widget ("Online Consultant").
 *
 * Self contained: injects its own CSS and DOM, no dependencies. Talks to this
 * install's api.php, so everything the visitor types lands in the agent console
 * at /admin/ in realtime.
 *
 *   <script src="https://support.example.com/widget.js" async></script>
 *
 * Optional data-* attributes on the script tag:
 *   data-support-url    base URL of the support center (defaults to script origin)
  *   data-title          header brand, e.g. "BBC99.bet"
  *   data-subtitle       small line under the brand
  *   data-notice         scrolling notice under the header (empty = hidden)
  *   data-greeting       first bubble shown in an empty thread
  *   data-avatar         image URL for the header + agent bubbles
  *                       (defaults to <support-url>/assets/images/support.svg)
 *   data-quick-replies  JSON array of quick-reply button labels
 *   data-color          accent colour
 *   data-position       "right" (default) or "left" *   data-poll          polling interval in ms
 *   data-auto-open      "1" to open the panel on load
 *   data-mode           "bubble" (default, float bottom-right) or "page"
 *                       (fill the viewport — the consultant page layout)
 *   data-max-upload-mb  largest attachment the client will try to upload
 *   data-accept         file picker filter for the attach button
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

  function parseList(raw) {
    if (!raw) {
      return [];
    }
    try {
      var list = JSON.parse(raw);
      if (!Array.isArray(list)) {
        return [];
      }
      return list.filter(function (item) { return typeof item === 'string' && item !== ''; });
    } catch (e) {
      return [];
    }
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
    title: attr('title', 'BBC99.bet'),
    subtitle: attr('subtitle', ''),
    notice: attr('notice', ''),
    greeting: attr('greeting', 'Hi! How can we help you today?'),
    avatar: attr('avatar', base ? base + '/assets/images/support.svg' : 'assets/images/support.svg'),
    quickReplies: parseList(attr('quick-replies', '')),
    color: attr('color', '#1762f6'),
    position: attr('position', 'right') === 'left' ? 'left' : 'right',
    poll: parseInt(attr('poll', '4000'), 10) || 4000,
    autoOpen: attr('auto-open', '') === '1',
    mode: attr('mode', 'bubble') === 'page' ? 'page' : 'bubble',
    media: base + '/media.php',
    maxUploadMb: parseInt(attr('max-upload-mb', '25'), 10) || 25,
    accept: attr('accept', 'image/*,video/*')
  };

  // Page mode fills the viewport and can never be closed — the chat *is* the page.
  var pageMode = config.mode === 'page';
  if (pageMode) {
    config.autoOpen = true;
  }

  var brandInitial = (config.title || '?').trim().charAt(0).toUpperCase();

  // Public avatar image (assets/images/support.svg by default). Only http(s),
  // root/relative URLs and data:image are accepted — anything else falls back
  // to the brand initial so a data-avatar attribute cannot inject script.
  function safeAvatarUrl(url) {
    if (!url || typeof url !== 'string') {
      return '';
    }
    var value = url.trim();
    if (/^(https?:\/\/|\/)/i.test(value)) {
      return /^javascript:/i.test(value) ? '' : value;
    }
    if (/^data:image\//i.test(value)) {
      return value;
    }
    if (/^[a-z0-9_\-\/.]+\.(svg|png|jpe?g|gif|webp)(\?.*)?$/i.test(value)) {
      return value;
    }
    return '';
  }

  var avatarUrl = safeAvatarUrl(config.avatar);

  /** Fill an avatar circle with the image, falling back to the initial. */
  function fillAvatar(span) {
    span.textContent = '';
    if (avatarUrl) {
      var img = document.createElement('img');
      img.src = avatarUrl;
      img.alt = '';
      img.setAttribute('aria-hidden', 'true');
      img.addEventListener('error', function () {
        span.textContent = brandInitial;
      });
      span.appendChild(img);
    } else {
      span.textContent = brandInitial;
    }
  }

  var state = {
    id: null,
    cursor: 0,
    open: false,
    unread: 0,
    sending: false,
    timer: null,
    send: null,
    sendFile: null,
    agentRead: 0
  };

  if (pageMode) {
    state.open = true;
  }

  var el = {};

  // ---------------------------------------------------------------------------
  // Styles — Arco-style tokens, matching the reference consultant panel
  // ---------------------------------------------------------------------------
  var css = [
    '.sc-root{--sc-accent:' + config.color + ';position:fixed;bottom:20px;z-index:2147483000;',
    'font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Noto Sans Bengali",Helvetica,Arial,sans-serif;',
    'font-size:14px;line-height:1.5;color:#1d2129;-webkit-font-smoothing:antialiased}',
    '.sc-root.sc-right{right:20px}.sc-root.sc-left{left:20px}',
    '.sc-root *,.sc-root *::before,.sc-root *::after{box-sizing:border-box}',

    // launcher
    '.sc-launcher{display:flex;align-items:center;justify-content:center;width:56px;height:56px;margin-left:auto;',
    'border:0;border-radius:50%;background:var(--sc-accent);color:#fff;cursor:pointer;',
    'box-shadow:0 6px 16px rgba(23,98,246,.36);transition:transform .15s ease}',
    '.sc-left .sc-launcher{margin-left:0;margin-right:auto}',
    '.sc-launcher:hover{transform:scale(1.05)}',
    '.sc-launcher svg{width:26px;height:26px;fill:currentColor}',
    '.sc-badge{position:absolute;top:-4px;right:-4px;min-width:20px;height:20px;padding:0 5px;',
    'border-radius:10px;background:#ed4014;color:#fff;font-size:12px;font-weight:600;',
    'display:flex;align-items:center;justify-content:center;box-shadow:0 0 0 2px #fff}',
    '.sc-badge[hidden]{display:none}',

    // panel
    '.sc-panel{position:absolute;bottom:72px;width:380px;max-width:calc(100vw - 32px);height:min(620px,calc(100vh - 110px));',
    'background:#fff;border-radius:12px;overflow:hidden;display:none;flex-direction:column;',
    'box-shadow:0 12px 40px rgba(0,0,0,.16)}',
    '.sc-right .sc-panel{right:0}.sc-left .sc-panel{left:0}',
    '.sc-panel.sc-open{display:flex}',

    // ---------------------------------------------------------------------
    // Full-page mode: the chat *is* the page. 100dvh tracks the mobile
    // browser chrome, and every band aligns to one readable 760px column so
    // the layout holds from a 320px phone up to an ultrawide display.
    // ---------------------------------------------------------------------
    '.sc-root.sc-page{top:0;right:0;bottom:0;left:0;width:100%;height:100vh;height:100dvh;',
    'font-size:15px;overscroll-behavior-y:contain}',
    '.sc-root.sc-page .sc-launcher{display:none}',
    '.sc-root.sc-page .sc-panel{position:absolute;top:0;right:0;bottom:0;left:0;width:100%;height:100%;',
    'max-width:none;border-radius:0;box-shadow:none;display:flex}',
    '.sc-root.sc-page .sc-head{padding:12px max(16px,calc((100% - 760px)/2))}',
    '.sc-root.sc-page .sc-body{padding:18px max(16px,calc((100% - 760px)/2))}',
    '.sc-root.sc-page .sc-composer{padding:10px max(16px,calc((100% - 760px)/2)) max(10px,env(safe-area-inset-bottom))}',
    '.sc-root.sc-page .sc-hint{padding:0 max(16px,calc((100% - 760px)/2)) 10px}',
    '.sc-root.sc-page .sc-msg{max-width:min(560px,78%)}',
    '.sc-root.sc-page .sc-quick button{padding-top:11px;padding-bottom:11px}',
    '.sc-root.sc-page .sc-head,.sc-root.sc-page .sc-notice,.sc-root.sc-page .sc-composer{flex:0 0 auto}',

    // header
    '.sc-head{display:flex;align-items:center;gap:10px;padding:12px 14px;background:#fff;border-bottom:1px solid #e5e6eb}',
    '.sc-avatar{width:38px;height:38px;border-radius:50%;background:var(--sc-accent);color:#fff;flex:0 0 auto;',
    'display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;overflow:hidden;flex-shrink:0}',
    '.sc-avatar img{width:100%;height:100%;object-fit:cover;display:block;border-radius:50%}',
    '.sc-head-text{flex:1;min-width:0}',
    '.sc-head strong{display:block;font-size:16px;font-weight:600;color:#1d2129;overflow:hidden;',
    'text-overflow:ellipsis;white-space:nowrap}',
    '.sc-head span{display:block;font-size:12.5px;color:#86909c}',
    '.sc-close{border:0;background:transparent;color:#86909c;font-size:22px;line-height:1;cursor:pointer;',
    'padding:4px 7px;border-radius:6px}',
    '.sc-close:hover{background:#f2f3f5;color:#4e5969}',

    // Scrolling notice (marquee). The track holds exactly two copies of the text
    // and travels one copy width, so the loop is seamless with no restart jump.
    '.sc-notice{overflow:hidden;background:#fff7e8;border-bottom:1px solid #ffe4bf;',
    'color:#ff7d00;font-size:12.5px;padding:7px 0}',
    '.sc-notice[hidden]{display:none}',
    '.sc-notice-track{display:flex;width:max-content;will-change:transform;',
    'animation:sc-marquee 34s linear infinite}',
    '.sc-notice-copy{display:block;white-space:nowrap;padding-right:70px}',
    '@keyframes sc-marquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}',

    // thread
    '.sc-body{flex:1;overflow-y:auto;padding:14px;background:#f7f8fa}',
    '.sc-row{display:flex;gap:8px;align-items:flex-end;margin:0 0 12px}',
    '.sc-row.sc-visitor{flex-direction:row-reverse}',
    '.sc-bubble-avatar{width:28px;height:28px;border-radius:50%;background:var(--sc-accent);color:#fff;flex:0 0 auto;',
    'display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;overflow:hidden;flex-shrink:0}',
    '.sc-bubble-avatar img{width:100%;height:100%;object-fit:cover;display:block;border-radius:50%}',
    '.sc-msg{max-width:80%;padding:9px 12px;border-radius:10px;white-space:pre-wrap;overflow-wrap:anywhere;',
    'background:#fff;border:1px solid #e5e6eb;color:#1d2129;font-size:13.5px}',
    '.sc-row.sc-visitor .sc-msg{background:var(--sc-accent);border-color:transparent;color:#fff}',
    '.sc-msg time{display:block;margin-top:4px;font-size:11px;opacity:.6}',

    // welcome
    '.sc-note{margin:0 0 12px;padding:9px 12px;border-radius:10px;background:#fff;border:1px solid #e5e6eb;',
    'color:#4e5969;font-size:13px}',
    '.sc-note[hidden]{display:none}',

    // quick replies
    '.sc-quick{display:flex;flex-wrap:wrap;gap:8px;margin:0 0 12px}',
    '.sc-quick button{flex:1 1 calc(50% - 4px);border:1px solid #e5e6eb;background:#fff;border-radius:8px;',
    'padding:9px 10px;font:inherit;font-size:13px;color:#1d2129;cursor:pointer;text-align:center;',
    'transition:border-color .15s ease,color .15s ease,background .15s ease}',
    '.sc-quick button:hover{border-color:var(--sc-accent);color:var(--sc-accent);background:#f5f8ff}',
    '.sc-quick button:disabled{opacity:.55;cursor:default}',

    // form + composer
    '.sc-composer textarea{width:100%;padding:9px 11px;border:1px solid #e5e6eb;border-radius:8px;',
    'font:inherit;color:inherit;background:#fff;resize:vertical}',
    '.sc-composer textarea:focus{outline:2px solid var(--sc-accent);outline-offset:-1px;border-color:var(--sc-accent)}',
    '.sc-composer button{border:0;border-radius:8px;background:var(--sc-accent);color:#fff;',
    'font:inherit;font-weight:600;padding:10px 14px;cursor:pointer}',
    '.sc-composer button:disabled{opacity:.6;cursor:default}',
    '.sc-composer{display:flex;gap:8px;align-items:flex-end;padding:10px;border-top:1px solid #e5e6eb;background:#fff}',
    '.sc-composer textarea{flex:1;max-height:110px;min-height:40px}',
    '.sc-attach{display:flex;align-items:center;justify-content:center;flex:0 0 auto;width:38px;height:38px;',
    'border:1px solid #e5e6eb;border-radius:8px;background:#fff;color:#4e5969;cursor:pointer}',
    '.sc-attach:hover{border-color:var(--sc-accent);color:var(--sc-accent)}',
    '.sc-attach svg{width:20px;height:20px;fill:currentColor}',
    '.sc-media{display:block;margin:0 0 6px}',
    '.sc-media a{display:block;line-height:0}',
    '.sc-media-el{display:block;max-width:100%;max-height:280px;border-radius:8px;background:#000}',

    // typing indicator + Seen marker
    '.sc-typing{align-items:flex-end}',
    '.sc-typing-dots{display:inline-flex;gap:4px;align-items:center;padding:11px 12px;background:#fff;',
    'border:1px solid #e5e6eb;border-radius:10px}',
    '.sc-typing-dots i{width:6px;height:6px;border-radius:50%;background:#86909c;',
    'animation:sc-blink 1.2s infinite ease-in-out}',
    '.sc-typing-dots i:nth-child(2){animation-delay:.2s}',
    '.sc-typing-dots i:nth-child(3){animation-delay:.4s}',
    '@keyframes sc-blink{0%,80%,100%{opacity:.25}40%{opacity:1}}',
    '.sc-seen{display:block;margin-top:4px;font-size:11px;text-align:right;opacity:.85}',
    '.sc-error{margin:0;color:#ed4014;font-size:12.5px}',
    '.sc-error[hidden]{display:none}',
    '.sc-hint{padding:0 12px 10px;background:#fff;color:#86909c;font-size:11.5px;text-align:center}',
    '.sc-hint[hidden]{display:none}',
    '@media (max-width:480px){.sc-root:not(.sc-page) .sc-panel{width:calc(100vw - 24px);height:calc(100vh - 120px)}}',

    // ---------------------------------------------------------------- phones
    '@media (max-width:520px){',
    '.sc-root.sc-page .sc-head{padding:10px 12px;gap:8px}',
    '.sc-root.sc-page .sc-avatar{width:34px;height:34px;font-size:14px}',
    '.sc-root.sc-page .sc-head strong{font-size:15px}',
    '.sc-root.sc-page .sc-head span{font-size:11.5px}',
    '.sc-root.sc-page .sc-body{padding:12px}',
    '.sc-root.sc-page .sc-msg{max-width:86%}',
    '.sc-root.sc-page .sc-composer{padding:8px 10px max(8px,env(safe-area-inset-bottom))}',
    '.sc-root.sc-page .sc-quick button{flex:1 1 100%;padding:12px 10px}',
    '.sc-root.sc-page .sc-notice{font-size:12px;padding:6px 0}',
    '.sc-composer textarea{min-height:44px}',
    '.sc-composer button{min-height:44px}',
    '.sc-body{padding-bottom:calc(12px + env(safe-area-inset-bottom))}',
    '}',

    // short landscape phones: hand the height back to the conversation
    '@media (max-height:520px) and (orientation:landscape){',
    '.sc-root.sc-page .sc-head{padding:6px 12px}',
    '.sc-root.sc-page .sc-avatar{display:none}',
    '.sc-root.sc-page .sc-notice{font-size:11.5px;padding:5px 0}',
    '.sc-root.sc-page .sc-body{padding-top:10px;padding-bottom:10px}',
    '}',

    // large displays
    '@media (min-width:1200px){',
    '.sc-root.sc-page .sc-body{padding-top:26px;padding-bottom:26px}',
    '.sc-root.sc-page .sc-msg{max-width:min(600px,72%)}',
    '}'
  ].join('');

  var style = document.createElement('style');
  style.textContent = css;
  document.head.appendChild(style);

  // ---------------------------------------------------------------------------
  // Markup
  // ---------------------------------------------------------------------------
  function escapeAttr(value) {
    return String(value).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  var root = document.createElement('div');
  root.className = 'sc-root ' + (config.position === 'left' ? 'sc-left' : 'sc-right') + (pageMode ? ' sc-page' : '');
  root.innerHTML = [
    '<div class="sc-panel' + (pageMode ? ' sc-open' : '') + '" role="dialog" aria-label="' + escapeAttr(config.title) + '" aria-modal="false">',
    '<div class="sc-head">',
    '<span class="sc-avatar" aria-hidden="true"></span>',
    '<div class="sc-head-text"><strong></strong><span></span></div>',
    '<button type="button" class="sc-close" aria-label="Close chat">&times;</button>',
    '</div>',
    '<div class="sc-notice" hidden><div class="sc-notice-track"></div></div>',
    '<div class="sc-body" aria-live="polite"></div>',
    '<div class="sc-hint" hidden></div>',
    '</div>',
    '<button type="button" class="sc-launcher" aria-label="Open support chat" aria-expanded="false">',
    '<span class="sc-badge" hidden>0</span>',
    '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3C6.9 3 2.8 6.5 2.8 10.8c0 2.5 1.4 4.7 3.6 6.1-.2 1.3-.8 2.6-1.8 3.6-.2.2 0 .6.3.5 2-.4 3.7-1.2 5-2.2 .7.1 1.4.2 2.1.2 5.1 0 9.2-3.5 9.2-7.8S17.1 3 12 3z"/></svg>',
    '</button>'
  ].join('');

  document.body.appendChild(root);

  el.panel = root.querySelector('.sc-panel');
  el.body = root.querySelector('.sc-body');
  el.headTitle = root.querySelector('.sc-head-text strong');
  el.headSubtitle = root.querySelector('.sc-head-text span');
  el.close = root.querySelector('.sc-close');
  el.notice = root.querySelector('.sc-notice');
  el.noticeTrack = root.querySelector('.sc-notice-track');
  el.launcher = root.querySelector('.sc-launcher');
  el.badge = root.querySelector('.sc-badge');
  el.hint = root.querySelector('.sc-hint');

  fillAvatar(root.querySelector('.sc-avatar'));
  el.headTitle.textContent = config.title;
  if (config.subtitle) {
    el.headSubtitle.textContent = config.subtitle;
  } else {
    el.headSubtitle.hidden = true;
  }
  function addNoticeCopies(count) {
    for (var i = 0; i < count; i++) {
      var span = document.createElement('span');
      span.className = 'sc-notice-copy';
      span.textContent = config.notice;
      el.noticeTrack.appendChild(span);
    }
  }

  /**
   * Keep the marquee seamless. Translating -50% only loops without a gap while
   * the track is at least twice the visible width, so copies are added in pairs
   * (an even count keeps the two halves identical).
   */
  function fillMarquee() {
    if (!config.notice) {
      return;
    }
    for (var guard = 0; guard < 24; guard++) {
      if (el.noticeTrack.scrollWidth >= el.notice.clientWidth * 2) {
        return;
      }
      addNoticeCopies(2);
    }
  }

  if (config.notice) {
    el.notice.hidden = false;
    addNoticeCopies(2);
    fillMarquee();
    window.addEventListener('resize', fillMarquee);
  }
  el.hint.textContent = 'Your messages are stored on ' + (base ? base.replace(/^https?:\/\//, '') : location.host) + '.';
  el.hint.hidden = true;

  // ---------------------------------------------------------------------------
  // Rendering
  // ---------------------------------------------------------------------------
  function timeLabel(at) {
    return new Date(at * 1000).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  /** An image or video block pointing at media.php for this conversation. */
  function buildMedia(chatId, file) {
    var url = config.media + '?id=' + encodeURIComponent(chatId) + '&f=' + encodeURIComponent(file.token);
    var wrap = document.createElement('div');
    wrap.className = 'sc-media';

    if (String(file.mime || '').indexOf('video/') === 0) {
      var video = document.createElement('video');
      video.className = 'sc-media-el';
      video.controls = true;
      video.preload = 'metadata';
      video.src = url;
      wrap.appendChild(video);
      return wrap;
    }

    var link = document.createElement('a');
    link.href = url;
    link.target = '_blank';
    link.rel = 'noreferrer noopener';
    var img = document.createElement('img');
    img.className = 'sc-media-el';
    img.src = url;
    img.alt = file.name || 'attachment';
    img.loading = 'lazy';
    link.appendChild(img);
    wrap.appendChild(link);
    return wrap;
  }

  function addBubble(role, text, at, file, index, menu) {
    showWelcome(false);
    var agent = role === 'agent';

    var row = document.createElement('div');
    row.className = 'sc-row ' + (agent ? 'sc-agent' : 'sc-visitor');
    if (typeof index === 'number') {
      row.dataset.index = String(index);
    }

    if (agent) {
      var avatar = document.createElement('span');
      avatar.className = 'sc-bubble-avatar';
      avatar.setAttribute('aria-hidden', 'true');
      fillAvatar(avatar);
      row.appendChild(avatar);
    }

    var bubble = document.createElement('div');
    bubble.className = 'sc-msg';

    if (file && file.token && state.id) {
      bubble.appendChild(buildMedia(state.id, file));
    }
    if (text) {
      var p = document.createElement('div');
      p.textContent = text;
      bubble.appendChild(p);
    }
    if (at) {
      var time = document.createElement('time');
      time.dateTime = new Date(at * 1000).toISOString();
      time.textContent = timeLabel(at);
      bubble.appendChild(time);
    }
    row.appendChild(bubble);
    el.body.appendChild(row);
    if (agent && Array.isArray(menu) && menu.length) {
      addMenuOptions(menu);
    }
    el.body.scrollTop = el.body.scrollHeight;
  }

  /**
   * Tappable follow-up options under an agent message (menu-tree flow from
   * tree.txt). Tapping sends the label like a quick reply; the group locks
   * after one tap so a double click cannot send twice.
   */
  function addMenuOptions(options) {
    var labels = options.filter(function (label) { return typeof label === 'string' && label !== ''; });
    if (!labels.length) {
      return;
    }
    var group = document.createElement('div');
    group.className = 'sc-quick';
    labels.forEach(function (label) {
      var button = document.createElement('button');
      button.type = 'button';
      button.textContent = label;
      button.addEventListener('click', function () {
        var btns = group.querySelectorAll('button');
        Array.prototype.forEach.call(btns, function (b) { b.disabled = true; });
        pickQuickReply(label);
      });
      group.appendChild(button);
    });
    el.body.appendChild(group);
    el.body.scrollTop = el.body.scrollHeight;
  }

  var welcomeNode = null;

  function buildWelcome() {
    welcomeNode = document.createElement('div');

    var greeting = document.createElement('p');
    greeting.className = 'sc-note';
    greeting.textContent = config.greeting;
    welcomeNode.appendChild(greeting);

    if (config.quickReplies.length) {
      var group = document.createElement('div');
      group.className = 'sc-quick';
      config.quickReplies.forEach(function (label) {
        var button = document.createElement('button');
        button.type = 'button';
        button.textContent = label;
        button.addEventListener('click', function () { pickQuickReply(label); });
        group.appendChild(button);
      });
      welcomeNode.appendChild(group);
    }

    el.body.appendChild(welcomeNode);
    return welcomeNode;
  }

  function showWelcome(show) {
    if (show) {
      if (!welcomeNode) {
        buildWelcome();
      }
      welcomeNode.hidden = false;
    } else if (welcomeNode) {
      welcomeNode.hidden = true;
    }
  }

  /**
   * A template reply sends immediately. No visitor details are required: if the
   * conversation has not started yet, sending the template starts one.
   */
  function pickQuickReply(label) {
    if (typeof state.send === 'function') {
      state.send(label);
    }
  }

  /** Remember the conversation the server just created and clear the welcome. */
  function afterStart(data) {
    localStorage.setItem(STORAGE_KEY, data.conversation.id);
    state.id = data.conversation.id;
    state.cursor = 0;
    el.body.textContent = '';
    welcomeNode = null;
    return data;
  }

  /**
   * Start a conversation from the visitor's first message. There are no visitor
   * detail fields: the message (or an attachment) is enough, and the console
   * labels it "Anonymous visitor".
   */
  function startConversation(message) {
    return request(new URLSearchParams({
      action: 'start',
      message: message,
      page: location.href
    })).then(afterStart);
  }

  /**
   * Empty thread: the greeting, the template replies and a composer that is
   * usable straight away. There are no name/email fields — a template reply or
   * a typed message opens the conversation on its own.
   */
  function renderWelcome() {
    el.body.textContent = '';
    welcomeNode = null;
    showWelcome(true);

    renderComposer('open');
  }

  function renderComposer(status) {
    var existing = root.querySelector('.sc-composer');
    if (status === 'closed') {
      if (existing) {
        existing.remove();
        state.send = null;
      }
      var note = root.querySelector('.sc-closed-note');
      if (!note) {
        note = document.createElement('p');
        note.className = 'sc-note sc-closed-note';
        note.textContent = 'এই কথোপকথনটি বন্ধ করা হয়েছে। আবার শুরু করতে একটি বার্তা পাঠান।';
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
      '<label class="sc-attach" title="Attach an image or video">',
      '<input type="file" accept="' + escapeAttr(config.accept) + '" hidden>',
      '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.5 5.5v10a4.5 4.5 0 0 1-9 0v-10a2.75 2.75 0 0 1 5.5 0v10a1.25 1.25 0 0 1-2.5 0v-9H9v9a2.75 2.75 0 0 0 5.5 0v-10a4.25 4.25 0 0 0-8.5 0v10a6 6 0 0 0 12 0v-10z"/></svg>',
      '</label>',
      '<textarea rows="1" maxlength="4000" placeholder="একটি বার্তা লিখুন…" aria-label="Your message"></textarea>',
      '<button type="button" aria-label="Send message">পাঠান</button>'
    ].join('');
    el.panel.appendChild(composer);

    var textarea = composer.querySelector('textarea');
    var button = composer.querySelector('button');
    var picker = composer.querySelector('input[type="file"]');

    function send(text) {
      var value = typeof text === 'string' ? text : textarea.value.trim();
      if (!value || state.sending) {
        return;
      }
      state.sending = true;
      button.disabled = true;

      // No conversation yet? Sending the message starts one.
      var call = state.id
        ? request(new URLSearchParams({ action: 'send', id: state.id, message: value }))
        : startConversation(value);

      call
        .then(function (data) {
          textarea.value = '';
          textarea.style.height = 'auto';
          var closed = root.querySelector('.sc-closed-note');
          if (closed && data.conversation.status === 'open') {
            closed.remove();
          }
          ingest(data);
          startPolling();
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

    /**
     * Upload an attachment. With no conversation yet this opens one, so a
     * visitor can start by sending a photo or a video.
     */
    function sendFile(file) {
      if (!file || state.sending) {
        return;
      }
      if (file.size > config.maxUploadMb * 1024 * 1024) {
        window.alert('That file is larger than the ' + config.maxUploadMb + ' MB limit.');
        return;
      }

      state.sending = true;
      button.disabled = true;

      var caption = textarea.value.trim();
      var form = new FormData();
      form.append('file', file, file.name);
      if (caption) {
        form.append('message', caption);
      }

      var call;
      if (state.id) {
        form.append('action', 'send');
        form.append('id', state.id);
        call = request(form);
      } else {
        form.append('action', 'start');
        form.append('page', location.href);
        call = request(form).then(afterStart);
      }

      call
        .then(function (data) {
          textarea.value = '';
          textarea.style.height = 'auto';
          var closed = root.querySelector('.sc-closed-note');
          if (closed && data.conversation.status === 'open') {
            closed.remove();
          }
          ingest(data);
          startPolling();
        })
        .catch(function (err) {
          window.alert(err.message || 'The file could not be sent.');
        })
        .finally(function () {
          state.sending = false;
          button.disabled = false;
        });
    }

    state.send = send;
    state.sendFile = sendFile;

    if (picker) {
      picker.addEventListener('change', function () {
        var file = picker.files && picker.files[0];
        if (file) {
          sendFile(file);
        }
        // Reset so picking the same file again still fires a change event.
        picker.value = '';
      });
    }

    button.addEventListener('click', function () { send(); });
    textarea.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        send();
      }
    });
    var lastTypingPing = 0;

    textarea.addEventListener('input', function () {
      textarea.style.height = 'auto';
      textarea.style.height = Math.min(textarea.scrollHeight, 110) + 'px';

      // Tell the console someone is composing, at most once every 2 seconds.
      var now = Date.now();
      if (state.id && now - lastTypingPing > 2000) {
        lastTypingPing = now;
        request(new URLSearchParams({ action: 'typing', id: state.id })).catch(function () {});
      }
    });
  }

  var typingNode = null;

  /** Show or hide the "agent is typing" bubble, keeping it last in the thread. */
  function setAgentTyping(on) {
    if (on) {
      if (!typingNode) {
        typingNode = document.createElement('div');
        typingNode.className = 'sc-row sc-typing';

        var avatar = document.createElement('span');
        avatar.className = 'sc-bubble-avatar';
        avatar.setAttribute('aria-hidden', 'true');
        fillAvatar(avatar);

        var dots = document.createElement('span');
        dots.className = 'sc-typing-dots';
        dots.setAttribute('aria-label', 'typing');
        dots.innerHTML = '<i></i><i></i><i></i>';

        typingNode.appendChild(avatar);
        typingNode.appendChild(dots);
      }
      if (el.body.lastChild !== typingNode) {
        el.body.appendChild(typingNode);
        el.body.scrollTop = el.body.scrollHeight;
      }
    } else if (typingNode && typingNode.parentNode) {
      typingNode.parentNode.removeChild(typingNode);
    }
  }

  /** Mark the visitor's latest message as read once the agent has seen it. */
  function renderSeen() {
    var rows = el.body.querySelectorAll('.sc-row.sc-visitor');

    Array.prototype.forEach.call(rows, function (row) {
      var stale = row.querySelector('.sc-seen');
      if (stale) {
        stale.remove();
      }
    });

    if (!rows.length || state.agentRead <= 0) {
      return;
    }

    var last = rows[rows.length - 1];
    var index = parseInt(last.dataset.index, 10);
    var target = last.querySelector('.sc-msg');
    if (!target || isNaN(index) || state.agentRead <= index) {
      return;
    }

    var marker = document.createElement('span');
    marker.className = 'sc-seen';
    marker.textContent = '\u2713\u2713 পঠিত';
    target.appendChild(marker);
  }

  /** Apply the typing/read block the API returns with every message payload. */
  function applyPresence(presence) {
    if (!presence) {
      return;
    }
    if (presence.read && typeof presence.read.agent === 'number' && presence.read.agent !== state.agentRead) {
      state.agentRead = presence.read.agent;
      renderSeen();
    }
    setAgentTyping(!!(presence.typing && presence.typing.agent));
  }

  /** Append messages from an API response and advance the cursor. */
  function ingest(data) {
    (data.messages || []).forEach(function (message) {
      addBubble(message.role, message.text, message.at, message.file, message.index, message.menu);
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
    applyPresence(data.presence);
  }

  function setUnread(count) {
    state.unread = count;
    el.badge.textContent = count > 9 ? '9+' : String(count);
    el.badge.hidden = count === 0;
  }

  /**
   * POST to the API. Takes urlencoded params for text, or a FormData body for
   * attachments — letting fetch set Content-Type keeps the multipart boundary
   * correct.
   */
  function request(body) {
    return fetch(config.api, {
      method: 'POST',
      credentials: 'omit',
      body: body
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
          renderWelcome();
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
    if (pageMode) {
      return;
    }
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
    if (event.key === 'Escape' && state.open && !pageMode) {
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
      renderWelcome();
      if (config.autoOpen) {
        open();
      }
      return;
    }

    state.id = saved;
    state.cursor = 0;

    fetch(config.api + '?action=poll&id=' + encodeURIComponent(saved) + '&after=0', { credentials: 'omit' })
      .then(parse)
      .then(function (data) {
        el.body.textContent = '';
        welcomeNode = null;
        ingest(data);
        renderComposer(data.conversation.status);
        startPolling();
        if (config.autoOpen) {
          open();
        }
      })
      .catch(function () {
        localStorage.removeItem(STORAGE_KEY);
        state.id = null;
        renderWelcome();
        if (config.autoOpen) {
          open();
        }
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
