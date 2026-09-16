/**
 * Portal do Aluno — install prompt + service worker (só Minha Conta / LMS).
 *
 * Detecção de app instalado: display-mode standalone / navigator.standalone (iOS).
 * Se já estiver instalado (abrir como PWA), os banners permanecem ocultos.
 */
(function () {
  'use strict';

  if (typeof scPortalAluno === 'undefined') {
    return;
  }

  var DISMISS_KEY = 'sc_portal_install_dismissed_v1';
  var deferredPrompt = null;
  var roots = document.querySelectorAll('[data-sc-portal-install]');

  function isStandalone() {
    return (
      window.matchMedia('(display-mode: standalone)').matches ||
      window.navigator.standalone === true
    );
  }

  function isIos() {
    return /iphone|ipad|ipod/i.test(window.navigator.userAgent);
  }

  function isDismissed() {
    try {
      return window.localStorage.getItem(DISMISS_KEY) === '1';
    } catch (e) {
      return false;
    }
  }

  function setDismissed() {
    try {
      window.localStorage.setItem(DISMISS_KEY, '1');
    } catch (e) {
      /* ignore */
    }
  }

  function hideAll() {
    roots.forEach(function (el) {
      el.hidden = true;
    });
  }

  function showAll() {
    if (isStandalone() || isDismissed() || !roots.length) {
      hideAll();
      return;
    }
    roots.forEach(function (el) {
      el.hidden = false;
      var iosHint = el.querySelector('[data-sc-portal-install-ios]');
      var btn = el.querySelector('[data-sc-portal-install-btn]');
      if (isIos() && iosHint) {
        iosHint.hidden = false;
        iosHint.textContent = scPortalAluno.iosHint || '';
        if (btn) {
          btn.hidden = true;
        }
      } else if (btn && !deferredPrompt) {
        // Android/desktop: botão visível; o prompt só dispara quando o browser permitir.
        btn.hidden = false;
      }
    });
  }

  window.addEventListener('beforeinstallprompt', function (e) {
    e.preventDefault();
    deferredPrompt = e;
    showAll();
  });

  window.addEventListener('appinstalled', function () {
    deferredPrompt = null;
    setDismissed();
    hideAll();
  });

  roots.forEach(function (el) {
    var btn = el.querySelector('[data-sc-portal-install-btn]');
    var dismiss = el.querySelector('[data-sc-portal-install-dismiss]');
    if (btn) {
      btn.addEventListener('click', function () {
        if (!deferredPrompt) {
          showAll();
          return;
        }
        deferredPrompt.prompt();
        deferredPrompt.userChoice.finally(function () {
          deferredPrompt = null;
        });
      });
    }
    if (dismiss) {
      dismiss.addEventListener('click', function () {
        setDismissed();
        hideAll();
      });
    }
  });

  // Mostrar se ainda não for app: iOS sempre (dica); Android também (texto + botão quando BIP chegar).
  if (!isStandalone() && !isDismissed()) {
    showAll();
  }

  if ('serviceWorker' in navigator && scPortalAluno.swUrl) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register(scPortalAluno.swUrl, { scope: '/' }).catch(function () {
        /* silencioso — PWA opcional */
      });
    });
  }
})();

/**
 * Sininho / inbox de avisos (issue #16 · Fase A).
 */
(function () {
  'use strict';

  if (typeof scPortalAluno === 'undefined' || !scPortalAluno.notices) {
    return;
  }

  var cfg = scPortalAluno.notices;
  var root = document.querySelector('[data-sc-portal-bell]');
  if (!root || !cfg.restUrl) {
    return;
  }

  var btn = root.querySelector('[data-sc-portal-bell-btn]');
  var panel = root.querySelector('[data-sc-portal-bell-panel]');
  var list = root.querySelector('[data-sc-portal-bell-list]');
  var empty = root.querySelector('[data-sc-portal-bell-empty]');
  var badge = root.querySelector('[data-sc-portal-bell-badge]');
  var open = false;

  function setBadge(n) {
    if (!badge) return;
    n = parseInt(n, 10) || 0;
    if (n < 1) {
      badge.hidden = true;
      badge.textContent = '0';
      return;
    }
    badge.hidden = false;
    badge.textContent = n > 99 ? '99+' : String(n);
  }

  function setOpen(next) {
    open = !!next;
    if (panel) panel.hidden = !open;
    if (btn) btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  function fetchJson(url, options) {
    options = options || {};
    options.headers = options.headers || {};
    options.headers['X-WP-Nonce'] = cfg.nonce;
    options.credentials = 'same-origin';
    return fetch(url, options).then(function (res) {
      if (!res.ok) throw new Error('http');
      return res.json();
    });
  }

  function render(items) {
    if (!list) return;
    list.innerHTML = '';
    if (!items || !items.length) {
      if (empty) empty.hidden = false;
      return;
    }
    if (empty) empty.hidden = true;
    items.forEach(function (item) {
      var li = document.createElement('li');
      var el = document.createElement(item.url ? 'a' : 'button');
      el.className = 'sc-portal-bell__item' + (item.is_read ? '' : ' sc-portal-bell__item--unread');
      if (item.url) {
        el.href = item.url;
      } else {
        el.type = 'button';
      }
      var title = document.createElement('span');
      title.className = 'sc-portal-bell__item-title';
      title.textContent = item.title || '';
      el.appendChild(title);
      if (item.body) {
        var body = document.createElement('span');
        body.className = 'sc-portal-bell__item-body';
        body.textContent = item.body;
        el.appendChild(body);
      }
      el.addEventListener('click', function () {
        markRead(item.id);
      });
      li.appendChild(el);
      list.appendChild(li);
    });
  }

  function markRead(id) {
    fetchJson(cfg.restUrl.replace(/\/?$/, '') + '/' + id + '/read', { method: 'POST' })
      .then(function (data) {
        if (data && typeof data.unread_count !== 'undefined') {
          setBadge(data.unread_count);
        }
        load();
      })
      .catch(function () {
        /* ignore */
      });
  }

  function load() {
    fetchJson(cfg.restUrl)
      .then(function (data) {
        render((data && data.items) || []);
        setBadge(data && data.unread_count);
      })
      .catch(function () {
        if (empty) {
          empty.hidden = false;
          empty.textContent = cfg.errorText || '';
        }
      });
  }

  if (btn) {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      setOpen(!open);
      if (open) load();
    });
  }

  document.addEventListener('click', function (e) {
    if (!open) return;
    if (root.contains(e.target)) return;
    setOpen(false);
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') setOpen(false);
  });

  // Badge inicial sem abrir o painel.
  fetchJson(cfg.restUrl.replace(/\/?$/, '') + '/unread-count')
    .then(function (data) {
      setBadge(data && data.unread_count);
    })
    .catch(function () {
      /* silencioso */
    });
})();

/**
 * Web Push opt-in (issue #16 · Fase B) — tab Conta.
 */
(function () {
  'use strict';

  if (typeof scPortalAluno === 'undefined' || !scPortalAluno.push) {
    return;
  }

  var cfg = scPortalAluno.push;
  var root = document.querySelector('[data-sc-portal-push]');
  if (!root) {
    return;
  }

  var statusEl = root.querySelector('[data-sc-portal-push-status]');
  var hintEl = root.querySelector('[data-sc-portal-push-hint]');
  var btnEnable = root.querySelector('[data-sc-portal-push-enable]');
  var btnDisable = root.querySelector('[data-sc-portal-push-disable]');
  var labels = cfg.labels || {};

  function isStandalone() {
    return (
      window.matchMedia('(display-mode: standalone)').matches ||
      window.navigator.standalone === true
    );
  }

  function isIos() {
    return /iphone|ipad|ipod/i.test(window.navigator.userAgent);
  }

  function urlBase64ToUint8Array(base64String) {
    var padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);
    for (var i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
  }

  function setStatus(text) {
    if (statusEl) statusEl.textContent = text || '';
  }

  function setHint(text) {
    if (!hintEl) return;
    if (!text) {
      hintEl.hidden = true;
      hintEl.textContent = '';
      return;
    }
    hintEl.hidden = false;
    hintEl.textContent = text;
  }

  function setBusy(busy) {
    if (btnEnable) btnEnable.disabled = !!busy;
    if (btnDisable) btnDisable.disabled = !!busy;
  }

  function showSubscribed(on) {
    if (btnEnable) btnEnable.hidden = !!on;
    if (btnDisable) btnDisable.hidden = !on;
    setStatus(on ? labels.enabled || '' : '');
  }

  function fetchJson(path, options) {
    options = options || {};
    options.headers = options.headers || {};
    options.headers['X-WP-Nonce'] = cfg.nonce;
    options.headers['Content-Type'] = 'application/json';
    options.credentials = 'same-origin';
    var base = (cfg.restUrl || '').replace(/\/?$/, '');
    return fetch(base + path, options).then(function (res) {
      if (!res.ok) throw new Error('http');
      return res.json();
    });
  }

  function unsupported() {
    root.hidden = false;
    if (btnEnable) btnEnable.hidden = true;
    if (btnDisable) btnDisable.hidden = true;
    setStatus(labels.unsupported || '');
    if (isIos() && !isStandalone()) {
      setHint(labels.iosHint || '');
    }
  }

  function initUi() {
    root.hidden = false;
    if (!cfg.ready || !cfg.vapidPublicKey) {
      unsupported();
      return;
    }
    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
      unsupported();
      return;
    }
    if (isIos() && !isStandalone()) {
      setHint(labels.iosHint || '');
    }
    if (Notification.permission === 'denied') {
      setStatus(labels.denied || '');
      if (btnEnable) btnEnable.hidden = true;
      if (btnDisable) btnDisable.hidden = true;
      return;
    }
    showSubscribed(!!cfg.subscribed);
  }

  function enable() {
    setBusy(true);
    setStatus(labels.loading || '');
    var swReady = navigator.serviceWorker.ready;
    if (scPortalAluno.swUrl) {
      swReady = navigator.serviceWorker
        .register(scPortalAluno.swUrl, { scope: '/' })
        .then(function () {
          return navigator.serviceWorker.ready;
        });
    }
    swReady
      .then(function (reg) {
        return Notification.requestPermission().then(function (permission) {
          if (permission !== 'granted') {
            throw new Error('denied');
          }
          return reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(cfg.vapidPublicKey),
          });
        });
      })
      .then(function (sub) {
        var json = sub.toJSON();
        return fetchJson('/subscribe', {
          method: 'POST',
          body: JSON.stringify({
            endpoint: json.endpoint,
            keys: json.keys,
          }),
        });
      })
      .then(function () {
        cfg.subscribed = true;
        showSubscribed(true);
      })
      .catch(function (err) {
        if (err && err.message === 'denied') {
          setStatus(labels.denied || '');
          if (btnEnable) btnEnable.hidden = true;
        } else {
          setStatus(labels.error || '');
          showSubscribed(false);
        }
      })
      .then(function () {
        setBusy(false);
      });
  }

  function disable() {
    setBusy(true);
    setStatus(labels.loading || '');
    navigator.serviceWorker.ready
      .then(function (reg) {
        return reg.pushManager.getSubscription();
      })
      .then(function (sub) {
        var endpoint = sub ? sub.endpoint : '';
        var unsubLocal = sub ? sub.unsubscribe() : Promise.resolve(true);
        return unsubLocal.then(function () {
          return fetchJson('/unsubscribe', {
            method: 'POST',
            body: JSON.stringify({ endpoint: endpoint }),
          });
        });
      })
      .then(function () {
        cfg.subscribed = false;
        showSubscribed(false);
        setStatus('');
      })
      .catch(function () {
        setStatus(labels.error || '');
      })
      .then(function () {
        setBusy(false);
      });
  }

  if (btnEnable) {
    btnEnable.addEventListener('click', function () {
      enable();
    });
  }
  if (btnDisable) {
    btnDisable.addEventListener('click', function () {
      disable();
    });
  }

  initUi();
})();
