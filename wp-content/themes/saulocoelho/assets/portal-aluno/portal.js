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
