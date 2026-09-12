(function () {
  var form = document.getElementById('sc-palestra-form');
  if (!form || typeof scPalestra === 'undefined') {
    return;
  }

  var statusEl = document.getElementById('sc-palestra-status');
  var submitBtn = document.getElementById('sc-palestra-submit');
  var thanks = document.getElementById('sc-palestra-thanks');
  var downloads = document.getElementById('sc-palestra-downloads');
  var mailNote = document.getElementById('sc-palestra-mail-note');

  function setStatus(message, isError) {
    if (!statusEl) {
      return;
    }
    statusEl.textContent = message || '';
    statusEl.classList.toggle('text-red-400', !!isError);
    statusEl.classList.toggle('text-slate-400', !isError);
  }

  function renderDownloads(items) {
    if (!downloads) {
      return;
    }
    downloads.innerHTML = '';
    (items || []).forEach(function (item) {
      var link = document.createElement('a');
      link.href = item.url;
      link.textContent = item.label || 'Baixar';
      link.setAttribute('download', item.label || '');
      link.className =
        'inline-flex items-center justify-center w-full h-14 rounded-xl bg-primary text-background-dark font-black uppercase tracking-widest text-sm hover:bg-primary-light transition-colors';
      downloads.appendChild(link);
    });
  }

  form.addEventListener('submit', function (event) {
    event.preventDefault();
    setStatus('');

    var body = new FormData(form);
    body.set('action', 'sc_palestra_submit');
    body.set('security', scPalestra.nonce);

    if (submitBtn) {
      submitBtn.disabled = true;
    }

    fetch(scPalestra.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: body
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (json) {
        if (!json || !json.success) {
          var msg =
            json && json.data && json.data.message
              ? json.data.message
              : scPalestra.i18n.generic;
          setStatus(msg, true);
          if (submitBtn) {
            submitBtn.disabled = false;
          }
          return;
        }

        form.classList.add('hidden');
        if (thanks) {
          thanks.classList.remove('hidden');
        }
        renderDownloads(json.data.downloads || []);
        if (mailNote) {
          if (json.data.emailed) {
            mailNote.textContent =
              'Enviamos uma cópia para ' + (json.data.email || 'o seu e-mail') + '.';
          } else {
            mailNote.textContent =
              'O download está liberado abaixo. Se o e-mail não chegar, use os botões para baixar agora.';
          }
        }
      })
      .catch(function () {
        setStatus(scPalestra.i18n.generic, true);
        if (submitBtn) {
          submitBtn.disabled = false;
        }
      });
  });
})();
