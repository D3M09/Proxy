/* Public chat panel bridge — stitch look, live backend.
 * Talks only to this install's api.php / media.php (same contract as
 * widget.js): POST start|send|typing, GET poll, presence{typing,read}.
 * No CDN, no external requests. Icons are inline SVG. */
(function () {
  'use strict';
  if (window.__cpPanel) return;
  window.__cpPanel = true;

  var cfgEl = document.getElementById('cp-config');
  var cfg = {};
  try { cfg = JSON.parse(cfgEl ? cfgEl.textContent : '{}'); } catch (e) { cfg = {}; }

  var API = cfg.api || 'api.php';
  var MEDIA = cfg.media || 'media.php';
  var QUICK = Array.isArray(cfg.quickReplies) ? cfg.quickReplies : [];
  var AVATAR = cfg.avatar || 'assets/images/support.svg';
  var POLL = parseInt(cfg.poll, 10) || 4000;
  var MAXMB = parseInt(cfg.maxUploadMb, 10) || 25;
  var ACCEPT = cfg.accept || 'image/*,video/*';
  var KEY = 'sc_chat_id';

  var thread = document.getElementById('cp-thread');
  var typingBox = document.getElementById('cp-typing');
  var agentbar = document.getElementById('cp-agentbar');
  var abPhoto = document.getElementById('cp-ab-photo');
  var abText = document.getElementById('cp-ab-text');
  var input = document.getElementById('cp-input');
  var sendBtn = document.getElementById('cp-send');
  var attach = document.getElementById('cp-file');
  var tip = document.getElementById('cp-tip');
  var tipX = document.getElementById('cp-tip-x');
  var dateEl = document.getElementById('cp-date');
  var welcome = document.getElementById('cp-welcome');

  var state = { id: null, cursor: 0, sending: false, timer: null, agentRead: 0, agent: null };

  var SVG = {
    done: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12l4 4L11 9l-1-1-4 5-3-3zM11 12l4 4 7-9-1-1-6 8-3-3z" fill="currentColor" stroke="none"/></svg>',
    check: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.5 2.5 4.5-5.5"/></svg>',
    verified: '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l2.4 2.4 3.4-.5.9 3.3 3.3.9-.5 3.4L23.9 14l-2.4 2.4.5 3.4-3.3.9-.9 3.3-3.4-.5L12 26l-2.4-2.4-3.4.5-.9-3.3-3.3-.9.5-3.4L.1 14l2.4-2.4-.5-3.4 3.3-.9.9-3.3 3.4.5z" transform="scale(.92)"/><path d="M10.6 14.6l-2.1-2.1-1.4 1.4 3.5 3.5 7-7-1.4-1.4z" fill="#ffffff"/></svg>'
  };

  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }
  function timeLabel(at) {
    try {
      return new Date(at * 1000).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    } catch (e) { return ''; }
  }
  function todayLabel() {
    try {
      var d = new Date();
      var time = d.toLocaleTimeString('bn-BD', { hour: 'numeric', minute: '2-digit' });
      return 'আজ, ' + time;
    } catch (e) { return 'আজ'; }
  }
  if (dateEl) dateEl.textContent = todayLabel();

  function avatarImg(cls, alt) {
    if (/^(https?:\/\/|\/|data:image\/|[a-z0-9_\-\/.]+\.(svg|png|jpe?g|gif|webp)(\?.*)?$)/i.test(AVATAR || '')) {
      return '<img class="' + cls + '" src="' + esc(AVATAR) + '" alt="' + esc(alt || '') + '" loading="lazy" onerror="this.style.display=\'none\'">';
    }
    return '';
  }

  /** Resolve an agent photo the same safe way as the brand avatar. */
  function resolvePhoto(p) {
    if (!p || typeof p !== 'string') return '';
    var v = p.trim();
    if (/^(https?:\/\/|\/)/i.test(v)) return /^javascript:/i.test(v) ? '' : v;
    if (/^data:image\//i.test(v)) return v;
    if (/^[a-z0-9_\-\/.]+\.(svg|png|jpe?g|gif|webp)(\?.*)?$/i.test(v)) {
      return (typeof BASE !== 'undefined' && BASE ? BASE : '') + '/' + v.replace(/^\/+/, '');
    }
    return '';
  }
  var BASE = (function () {
    var m = String(API || '').match(/^(https?:\/\/[^?#]+)\/api\.php/i);
    if (m) return m[1];
    try { return location.origin; } catch (e) { return ''; }
  })();

  /** Agent session bar: who joined the chat. Null = still connecting. */
  function setAgent(agent) {
    var prev = state.agent && state.agent.name;
    var next = agent && agent.name ? agent : null;
    state.agent = next;
    if (!agentbar) return;
    if (!next) {
      agentbar.classList.remove('cp-joined');
      if (abPhoto) { abPhoto.hidden = true; abPhoto.textContent = ''; }
      if (abText) abText.textContent = 'Agent session • connecting…';
      return;
    }
    agentbar.classList.add('cp-joined');
    var url = resolvePhoto(next.photo);
    if (abPhoto) {
      abPhoto.hidden = false;
      abPhoto.textContent = '';
      if (url) {
        var img = document.createElement('img');
        img.src = url;
        img.alt = '';
        img.loading = 'lazy';
        img.addEventListener('error', function () { img.remove(); });
        abPhoto.appendChild(img);
        var fb = document.createElement('span');
        fb.textContent = next.name.trim().charAt(0).toUpperCase();
        abPhoto.appendChild(fb);
      } else {
        abPhoto.textContent = next.name.trim().charAt(0).toUpperCase();
      }
    }
    if (abText) {
      abText.textContent = 'Agent session • ' + next.name +
        (next.online === false ? ' (away)' : ' joined');
    }
    // Announce a new join inside the thread, once per handover.
    if (prev && prev !== next.name && thread && thread.children.length) {
      var note = document.createElement('div');
      note.className = 'cp-date';
      note.innerHTML = '<div class="cp-date-pill"><span>🎧 ' + esc(next.name) +
        ' joined the chat</span></div>';
      thread.appendChild(note);
      scrollMain();
    }
  }

  /* ---------------- API ---------------- */
  function request(body) { return fetch(API, { method: 'POST', credentials: 'omit', body: body }).then(parse); }
  function parse(res) {
    return res.json().catch(function () { throw new Error('Unexpected server response (' + res.status + ').'); })
      .then(function (data) {
        if (!res.ok || !data.ok) throw new Error((data && data.error) ? data.error : 'Request failed.');
        return data;
      });
  }

  /* ---------------- rendering ---------------- */
  function mediaNode(file) {
    var url = MEDIA + '?id=' + encodeURIComponent(state.id) + '&f=' + encodeURIComponent(file.token);
    if (String(file.mime || '').indexOf('video/') === 0) {
      return '<div class="cp-shot"><video src="' + esc(url) + '" controls preload="metadata"></video></div>';
    }
    return '<div class="cp-shot"><a href="' + esc(url) + '" target="_blank" rel="noreferrer noopener">' +
      '<img src="' + esc(url) + '" alt="' + esc(file.name || 'attachment') + '" loading="lazy"></a></div>';
  }

  function addVisitor(text, at, file, index) {
    var col = document.createElement('div');
    col.className = 'cp-vcol';
    if (typeof index === 'number') col.dataset.index = String(index);
    var inner = '<div class="cp-vcol-inner">' +
      '<div class="cp-vmeta"><span>আপনি</span><span>•</span><span>' + esc(timeLabel(at)) + '</span></div>';
    if (file && file.token && state.id) {
      inner += '<div class="cp-receipt">' + mediaNode(file) +
        '<div class="cp-receipt-foot"><span class="lft">' + SVG.check + 'সংযুক্তি</span></div></div>';
    }
    if (text) {
      inner += '<div class="cp-vbubble"><p>' + esc(text).replace(/\n/g, '<br>') + '</p></div>';
    }
    inner += '<div class="cp-seen">' + SVG.done + '<span>✓ পাঠানো হয়েছে</span></div></div>';
    col.innerHTML = inner;
    thread.appendChild(col);
    thread.parentNode.scrollTop = thread.parentNode.scrollHeight;
    scrollMain();
  }

  function addAgent(text, at, file, index, menu) {
    var wrap = document.createElement('div');
    wrap.className = 'cp-acol';
    var who = (state.agent && state.agent.name) ? state.agent.name : 'সাপোর্ট প্রতিনিধি';
    var head = '<div class="cp-ameta">' + avatarImg('', 'agent') +
      '<span class="who">' + esc(who) + '</span><span class="when">' + esc(timeLabel(at)) + '</span></div>';
    var body = '<div class="cp-abubble">';
    if (file && file.token && state.id) body += mediaNode(file);
    if (text) body += '<p>' + esc(text).replace(/\n/g, '<br>') + '</p>';
    else if (!file) body += '<p></p>';
    body += '</div>';
    wrap.innerHTML = head + body;
    thread.appendChild(wrap);
    if (Array.isArray(menu) && menu.length) addMenuRow(menu);
    scrollMain();
  }

  function addMenuRow(options) {
    var labels = options.filter(function (l) { return typeof l === 'string' && l !== ''; });
    if (!labels.length) return;
    var row = document.createElement('div');
    row.className = 'cp-menurow';
    labels.forEach(function (label) {
      var b = document.createElement('button');
      b.type = 'button';
      b.className = 'cp-pill';
      b.textContent = label;
      b.addEventListener('click', function () {
        var btns = row.querySelectorAll('button');
        Array.prototype.forEach.call(btns, function (x) { x.disabled = true; });
        sendText(label);
      });
      row.appendChild(b);
    });
    thread.appendChild(row);
    scrollMain();
  }

  function setAgentTyping(on) {
    if (!typingBox) return;
    typingBox.hidden = !on;
    if (on) scrollMain();
  }

  function renderSeen() {
    var rows = thread.querySelectorAll('.cp-vcol');
    Array.prototype.forEach.call(rows, function (r) {
      var s = r.querySelector('.cp-seen span:last-child');
      if (s) s.textContent = '✓ পাঠানো হয়েছে';
    });
    if (!rows.length || !(state.agentRead > 0)) return;
    var last = rows[rows.length - 1];
    var idx = parseInt(last.dataset.index, 10);
    if (isNaN(idx) || !(state.agentRead > idx)) return;
    var s = last.querySelector('.cp-seen span:last-child');
    if (s) s.textContent = '✓✓ পঠিত';
  }

  function applyPresence(p) {
    if (!p) return;
    if (p.read && typeof p.read.agent === 'number' && p.read.agent !== state.agentRead) {
      state.agentRead = p.read.agent;
      renderSeen();
    }
    setAgentTyping(!!(p.typing && p.typing.agent));
  }

  function ingest(data) {
    if ('agent' in data) setAgent(data.agent);
    (data.messages || []).forEach(function (m) {
      hideWelcome();
      if (m.role === 'agent') addAgent(m.text, m.at, m.file, m.index, m.menu);
      else addVisitor(m.text, m.at, m.file, m.index);
      state.cursor = Math.max(state.cursor, m.index + 1);
    });
    if (typeof data.total === 'number') state.cursor = Math.max(state.cursor, data.total);
    if (data.conversation && data.conversation.status === 'closed') showClosed();
    else hideClosed();
    applyPresence(data.presence);
  }

  function hideWelcome() { if (welcome) welcome.style.display = 'none'; }

  function showClosed() {
    if (thread.querySelector('.cp-closed')) return;
    var p = document.createElement('p');
    p.className = 'cp-closed';
    p.textContent = 'এই কথোপকথনটি বন্ধ করা হয়েছে। আবার শুরু করতে একটি বার্তা পাঠান।';
    thread.appendChild(p);
  }
  function hideClosed() {
    var n = thread.querySelector('.cp-closed');
    if (n) n.remove();
  }

  function scrollMain() {
    try {
      var sc = document.scrollingElement || document.documentElement;
      sc.scrollTop = sc.scrollHeight;
    } catch (e) {}
  }

  /* ---------------- send ---------------- */
  function afterStart(data) {
    try { localStorage.setItem(KEY, data.conversation.id); } catch (e) {}
    state.id = data.conversation.id;
    state.cursor = 0;
    thread.textContent = '';
    setAgent(null);
    return data;
  }
  function startConversation(message) {
    return request(new URLSearchParams({ action: 'start', message: message, page: location.href })).then(afterStart);
  }

  function sendText(text) {
    var value = typeof text === 'string' ? text : (input.value || '').trim();
    if (!value || state.sending) return;
    state.sending = true;
    sendBtn.disabled = true;
    var call = state.id
      ? request(new URLSearchParams({ action: 'send', id: state.id, message: value }))
      : startConversation(value);
    call.then(function (data) {
      input.value = '';
      input.style.height = 'auto';
      sendBtn.classList.remove('cp-big');
      ingest(data);
      startPolling();
    }).catch(function (err) {
      window.alert(err.message || 'Message could not be sent.');
    }).finally(function () {
      state.sending = false;
      sendBtn.disabled = false;
      input.focus();
    });
  }

  function sendFile(file) {
    if (!file || state.sending) return;
    if (file.size > MAXMB * 1024 * 1024) {
      window.alert('That file is larger than the ' + MAXMB + ' MB limit.');
      return;
    }
    state.sending = true;
    sendBtn.disabled = true;
    var caption = (input.value || '').trim();
    var form = new FormData();
    form.append('file', file, file.name);
    if (caption) form.append('message', caption);
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
    call.then(function (data) {
      input.value = '';
      input.style.height = 'auto';
      ingest(data);
      startPolling();
    }).catch(function (err) {
      window.alert(err.message || 'The file could not be sent.');
    }).finally(function () {
      state.sending = false;
      sendBtn.disabled = false;
    });
  }

  /* ---------------- polling ---------------- */
  function startPolling() {
    stopPolling();
    state.timer = window.setInterval(tick, POLL);
  }
  function stopPolling() {
    if (state.timer) { window.clearInterval(state.timer); state.timer = null; }
  }
  function tick() {
    if (!state.id) return;
    fetch(API + '?action=poll&id=' + encodeURIComponent(state.id) + '&after=' + state.cursor, { credentials: 'omit' })
      .then(parse).then(ingest)
      .catch(function (err) {
        if (/not found/i.test(err.message || '')) {
          try { localStorage.removeItem(KEY); } catch (e) {}
          state.id = null;
          state.cursor = 0;
          stopPolling();
        }
      });
  }

  /* ---------------- wiring ---------------- */
  if (tipX && tip) tipX.addEventListener('click', function () { tip.hidden = true; });
  if (attach) {
    attach.setAttribute('accept', ACCEPT);
    attach.addEventListener('change', function () {
      var f = attach.files && attach.files[0];
      if (f) sendFile(f);
      attach.value = '';
    });
  }
  if (sendBtn) sendBtn.addEventListener('click', function () { sendText(); });
  if (input) {
    input.addEventListener('keydown', function (ev) {
      if (ev.key === 'Enter' && !ev.shiftKey) { ev.preventDefault(); sendText(); }
    });
    var lastPing = 0;
    input.addEventListener('input', function () {
      input.style.height = 'auto';
      input.style.height = Math.min(input.scrollHeight, 96) + 'px';
      if (input.value.trim().length > 0) sendBtn.classList.add('cp-big');
      else sendBtn.classList.remove('cp-big');
      var now = Date.now();
      if (state.id && now - lastPing > 2000) {
        lastPing = now;
        request(new URLSearchParams({ action: 'typing', id: state.id })).catch(function () {});
      }
    });
  }
  document.addEventListener('click', function (ev) {
    var t = ev.target && ev.target.closest ? ev.target.closest('[data-send]') : null;
    if (t) sendText(t.getAttribute('data-send'));
  });

  /* ---------------- boot ---------------- */
  function boot() {
    var saved = null;
    try { saved = localStorage.getItem(KEY); } catch (e) { saved = null; }
    if (!saved || !/^[a-f0-9]{32}$/.test(saved)) { startPolling(); return; }
    state.id = saved;
    state.cursor = 0;
    fetch(API + '?action=poll&id=' + encodeURIComponent(saved) + '&after=0', { credentials: 'omit' })
      .then(parse)
      .then(function (data) {
        thread.textContent = '';
        ingest(data);
        startPolling();
      })
      .catch(function () {
        try { localStorage.removeItem(KEY); } catch (e) {}
        state.id = null;
        startPolling();
      });
  }
  window.SupportCenter = window.SupportCenter || {};
  window.SupportCenter.send = sendText;
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
  else boot();
})();
