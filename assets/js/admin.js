/* GELPAZ IMMO — Scripts du back-office */
(function () {
  'use strict';
  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));
  const csrf = (window.ADMIN && window.ADMIN.csrf) || '';
  const post = (url, data) => {
    const fd = data instanceof FormData ? data : new FormData();
    if (!(data instanceof FormData) && data) Object.entries(data).forEach(([k, v]) => Array.isArray(v) ? v.forEach((x) => fd.append(k + '[]', x)) : fd.append(k, v));
    fd.append('_token', csrf);
    return fetch(url, { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } }).then((r) => r.json());
  };

  /* Menu latéral mobile */
  $$('[data-toggle-sidebar]').forEach((b) => b.addEventListener('click', () => document.body.classList.toggle('sidebar-open')));

  /* Confirmations */
  document.addEventListener('submit', (e) => {
    const f = e.target;
    if (f.dataset.confirm && !window.confirm(f.dataset.confirm)) e.preventDefault();
  });
  $$('[data-confirm-click]').forEach((b) => b.addEventListener('click', (e) => { if (!window.confirm(b.dataset.confirmClick)) e.preventDefault(); }));
  $$('[data-confirm-bulk]').forEach((b) => b.addEventListener('click', (e) => {
    const form = b.closest('form');
    const n = $$('input[name="ids[]"]:checked', form).length;
    if (!n) { e.preventDefault(); alert('Sélectionnez au moins un élément.'); return; }
    if ($('select[name="action"]', form).value === 'delete' && !confirm('Supprimer ' + n + ' élément(s) ?')) e.preventDefault();
  }));
  $$('[data-check-all]').forEach((c) => c.addEventListener('change', () => {
    $$('input[name="ids[]"]', c.closest('form')).forEach((x) => { x.checked = c.checked; });
  }));

  /* Interrupteurs (publication, vedette) */
  $$('.adm-switch[data-toggle-url]').forEach((sw) => sw.addEventListener('click', async () => {
    sw.disabled = true;
    try {
      const r = await post(sw.dataset.toggleUrl, { field: sw.dataset.field });
      if (r.ok) { sw.classList.toggle('is-on', !!r.value); sw.setAttribute('aria-pressed', r.value ? 'true' : 'false'); }
      else alert(r.message || 'Action impossible.');
    } catch (e) { alert('Erreur réseau.'); }
    sw.disabled = false;
  }));

  /* Slug automatique */
  const slugify = (s) => s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/[’']/g, '-').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
  $$('[data-slug-from]').forEach((slug) => {
    const src = $('[name="' + slug.dataset.slugFrom + '"]', slug.form);
    if (!src) return;
    let touched = slug.value !== '';
    slug.addEventListener('input', () => { touched = slug.value !== ''; });
    src.addEventListener('input', () => { if (!touched) slug.value = slugify(src.value); });
  });

  /* Aperçu des images */
  $$('input[type=file][data-preview]').forEach((input) => input.addEventListener('change', () => {
    const file = input.files && input.files[0];
    const box = input.closest('.adm-image-field').querySelector('.adm-image-preview');
    if (!file || !box) return;
    const url = URL.createObjectURL(file);
    box.innerHTML = '<img src="' + url + '" alt="">';
  }));
  $$('input[type=file][data-count-files]').forEach((input) => input.addEventListener('change', () => {
    const zone = input.closest('.adm-dropzone');
    const n = input.files.length;
    if (zone) $('strong', zone).textContent = n ? n + ' photo(s) sélectionnée(s) — elles seront ajoutées à l’enregistrement' : 'Ajouter des photos';
  }));
  $$('.adm-dropzone').forEach((zone) => {
    const input = $('input[type=file]', zone);
    ['dragenter', 'dragover'].forEach((ev) => zone.addEventListener(ev, (e) => { e.preventDefault(); zone.classList.add('is-over'); }));
    ['dragleave', 'drop'].forEach((ev) => zone.addEventListener(ev, () => zone.classList.remove('is-over')));
    zone.addEventListener('drop', (e) => { e.preventDefault(); if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; input.dispatchEvent(new Event('change')); } });
  });

  /* Glisser-déposer : galerie */
  $$('[data-gallery]').forEach((gallery) => {
    const orderInput = $('[data-gallery-order]', gallery.parentElement);
    let dragged = null;
    const sync = () => { if (orderInput) orderInput.value = $$('.adm-gallery-item', gallery).map((i) => i.dataset.id).join(','); };
    gallery.addEventListener('dragstart', (e) => { dragged = e.target.closest('.adm-gallery-item'); if (dragged) { dragged.classList.add('is-dragging'); e.dataTransfer.effectAllowed = 'move'; } });
    gallery.addEventListener('dragend', () => { if (dragged) dragged.classList.remove('is-dragging'); $$('.is-over', gallery).forEach((x) => x.classList.remove('is-over')); dragged = null; sync(); });
    gallery.addEventListener('dragover', (e) => {
      e.preventDefault();
      const over = e.target.closest('.adm-gallery-item');
      if (!over || !dragged || over === dragged) return;
      const rect = over.getBoundingClientRect();
      const after = (e.clientX - rect.left) > rect.width / 2;
      over.parentNode.insertBefore(dragged, after ? over.nextSibling : over);
    });
  });

  /* Glisser-déposer : ordre des lignes d'un tableau */
  $$('table[data-sortable]').forEach((table) => {
    const tbody = $('tbody', table);
    let dragged = null;
    $$('.adm-handle', table).forEach((h) => {
      h.addEventListener('dragstart', (e) => { dragged = h.closest('tr'); dragged.classList.add('is-dragging'); e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain', dragged.dataset.id); });
    });
    tbody.addEventListener('dragover', (e) => {
      e.preventDefault();
      const over = e.target.closest('tr');
      if (!over || !dragged || over === dragged) return;
      const rect = over.getBoundingClientRect();
      over.parentNode.insertBefore(dragged, (e.clientY - rect.top) > rect.height / 2 ? over.nextSibling : over);
    });
    tbody.addEventListener('drop', async (e) => {
      e.preventDefault();
      if (!dragged) return;
      dragged.classList.remove('is-dragging');
      const ids = $$('tr', tbody).map((tr) => tr.dataset.id);
      dragged = null;
      try { await post(table.dataset.sortable, { ids, offset: table.dataset.offset || 0 }); } catch (err) { alert('Impossible d’enregistrer l’ordre.'); }
    });
    tbody.addEventListener('dragend', () => { if (dragged) dragged.classList.remove('is-dragging'); dragged = null; });
  });

  /* Plans : ajout de lignes */
  let planIndex = 0;
  $$('[data-add-plan]').forEach((btn) => btn.addEventListener('click', () => {
    const card = btn.closest('.adm-card');
    const tpl = $('[data-plan-template]', card);
    const list = $('[data-plans]', card);
    list.insertAdjacentHTML('beforeend', tpl.innerHTML.replace(/__i__/g, String(planIndex++)));
  }));

  /* Éditeur de texte riche */
  $$('[data-editor]').forEach((ed) => {
    const area = $('.adm-editor-area', ed);
    const source = $('.adm-editor-source', ed);
    const sync = () => { if (!ed.classList.contains('is-source')) source.value = area.innerHTML.trim(); };
    area.addEventListener('input', sync);
    area.addEventListener('blur', sync);
    area.addEventListener('paste', (e) => {
      // Collage en texte brut pour éviter les styles parasites
      const html = e.clipboardData && e.clipboardData.getData('text/html');
      if (html) {
        e.preventDefault();
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        $$('*', tmp).forEach((n) => { n.removeAttribute('style'); n.removeAttribute('class'); });
        $$('script,style,meta,link', tmp).forEach((n) => n.remove());
        document.execCommand('insertHTML', false, tmp.innerHTML);
      }
    });
    $$('.adm-editor-toolbar button', ed).forEach((b) => b.addEventListener('click', async () => {
      const cmd = b.dataset.cmd;
      if (cmd === 'source') {
        if (ed.classList.contains('is-source')) { area.innerHTML = source.value; ed.classList.remove('is-source'); }
        else { source.value = area.innerHTML.trim(); ed.classList.add('is-source'); }
        b.classList.toggle('is-active');
        return;
      }
      area.focus();
      if (cmd === 'createLink') {
        const u = prompt('Adresse du lien (https://…) :', 'https://');
        if (u) document.execCommand('createLink', false, u);
      } else if (cmd === 'insertImage') {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = async () => {
          if (!input.files[0]) return;
          const fd = new FormData();
          fd.append('image', input.files[0]);
          try {
            const r = await post((window.ADMIN.base || '') + '/admin/upload-editeur', fd);
            if (r.ok) { area.focus(); document.execCommand('insertImage', false, r.url); sync(); } else alert(r.message || 'Échec de l’envoi.');
          } catch (e) { alert('Erreur réseau.'); }
        };
        input.click();
      } else if (cmd === 'formatBlock') {
        document.execCommand('formatBlock', false, b.dataset.value);
      } else {
        document.execCommand(cmd, false, null);
      }
      sync();
    }));
    ed.closest('form').addEventListener('submit', () => {
      if (ed.classList.contains('is-source')) { area.innerHTML = source.value; }
      source.value = area.innerHTML.trim();
    });
  });

  /* Alerte de modifications non enregistrées */
  $$('form[data-unsaved]').forEach((form) => {
    let dirty = false;
    form.addEventListener('input', () => { dirty = true; });
    form.addEventListener('change', () => { dirty = true; });
    form.addEventListener('submit', () => { dirty = false; });
    window.addEventListener('beforeunload', (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
  });

  /* Import des images de l'ancien site */
  $$('[data-import-start]').forEach((btn) => btn.addEventListener('click', async () => {
    const card = btn.closest('.adm-card');
    const box = $('[data-import-progress]', card);
    const bar = $('.adm-progress-bar span', card);
    const status = $('[data-import-status]', card);
    const errors = $('[data-import-errors]', card);
    const total = parseInt(btn.dataset.total, 10) || 1;
    let done = 0;
    const skip = [];
    box.hidden = false;
    btn.disabled = true;
    errors.innerHTML = '';
    status.textContent = 'Importation en cours… ne fermez pas cette page.';
    for (;;) {
      let r;
      try { r = await post(btn.dataset.importStart, { skip }); } catch (e) { status.textContent = 'Erreur réseau : relancez l’importation.'; btn.disabled = false; return; }
      if (!r.ok) { status.textContent = r.message || 'Erreur.'; btn.disabled = false; return; }
      done += r.done;
      (r.errors || []).forEach((er) => { skip.push(er.key); const li = document.createElement('li'); li.textContent = er.url.split('/').pop() + ' — ' + er.error; errors.appendChild(li); });
      bar.style.width = Math.min(100, Math.round(((done + skip.length) / total) * 100)) + '%';
      status.textContent = done + ' image(s) importée(s)' + (skip.length ? ', ' + skip.length + ' échec(s)' : '') + ' — ' + r.remaining + ' restante(s)';
      if (r.remaining <= 0 || (r.done === 0 && (!r.errors || !r.errors.length))) break;
    }
    status.textContent = 'Terminé : ' + done + ' image(s) importée(s)' + (skip.length ? ', ' + skip.length + ' échec(s) (images introuvables sur l’ancien site).' : '.');
    bar.style.width = '100%';
  }));
})();
