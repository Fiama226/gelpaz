/* ==========================================================================
   GELPAZ IMMO — Scripts du site (vanilla JS + Swiper)
   ========================================================================== */
(function () {
  'use strict';

  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.documentElement.classList.remove('no-js');

  /* ------------------------------------------------------------ Préchargeur */
  const preloader = $('#preloader');
  const hidePreloader = () => preloader && preloader.classList.add('is-hidden');
  if (preloader) {
    if (sessionStorage.getItem('gz_seen')) { preloader.style.transition = 'opacity .3s'; }
    window.addEventListener('load', () => setTimeout(hidePreloader, sessionStorage.getItem('gz_seen') ? 50 : 450));
    setTimeout(hidePreloader, 2500);
    sessionStorage.setItem('gz_seen', '1');
  }

  /* ---------------------------------------------- Images : repli + chargement */
  document.addEventListener('error', (e) => {
    const img = e.target;
    if (img.tagName === 'IMG' && img.dataset.fallback && !img.dataset.failed) {
      img.dataset.failed = '1';
      img.src = img.dataset.fallback;
    }
  }, true);
  $$('img[data-fallback]').forEach((img) => {
    if (img.complete && img.naturalWidth === 0 && img.getAttribute('src') && !img.dataset.failed) { img.dataset.failed = '1'; img.src = img.dataset.fallback; }
  });
  const markLoaded = (img) => img.classList.add('is-loaded');
  $$('img[loading="lazy"]').forEach((img) => {
    if (img.complete && img.naturalWidth) markLoaded(img); else img.addEventListener('load', () => markLoaded(img), { once: true });
  });

  /* ------------------------------------------------------- En-tête collant */
  const header = $('#siteHeader');
  const backTop = $('#backToTop');
  const ring = backTop ? $('circle', backTop) : null;
  const onScroll = () => {
    const y = window.scrollY;
    if (header) header.classList.toggle('is-sticky', y > 140);
    if (backTop) {
      backTop.classList.toggle('is-visible', y > 500);
      const h = document.documentElement.scrollHeight - window.innerHeight;
      if (ring && h > 0) ring.style.strokeDashoffset = String(138.2 - (138.2 * y) / h);
    }
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
  if (backTop) backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' }));

  /* ---------------------------------------------------------- Menu mobile */
  const offcanvas = $('#offcanvas');
  const toggle = $('.menu-toggle');
  const setMenu = (open) => {
    if (!offcanvas) return;
    offcanvas.classList.toggle('is-open', open);
    offcanvas.setAttribute('aria-hidden', open ? 'false' : 'true');
    document.body.classList.toggle('no-scroll', open);
    if (toggle) toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) { const c = $('.offcanvas-close', offcanvas); c && c.focus(); }
  };
  if (toggle) toggle.addEventListener('click', () => setMenu(true));
  $$('[data-close-menu]').forEach((el) => el.addEventListener('click', () => setMenu(false)));
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { setMenu(false); closeLightbox(); closeVideo(); } });
  $$('.mobile-sub-toggle').forEach((btn) => {
    btn.addEventListener('click', () => {
      const sub = btn.nextElementSibling;
      const open = btn.getAttribute('aria-expanded') !== 'true';
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      sub.style.maxHeight = open ? sub.scrollHeight + 'px' : '0px';
    });
  });

  /* ------------------------------------------- Langues (Google Traduction) */
  const getLang = () => {
    const m = document.cookie.match(/(?:^|;\s*)googtrans=\/[a-z-]+\/([a-z-]+)/i);
    return m ? m[1] : 'fr';
  };
  const setLangCookie = (lang) => {
    const host = location.hostname;
    const parts = host.split('.');
    const domains = ['', host];
    if (parts.length > 1) domains.push('.' + parts.slice(-2).join('.'));
    domains.forEach((d) => {
      const dom = d ? '; domain=' + d : '';
      document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/' + dom;
      if (lang !== 'fr') document.cookie = 'googtrans=/fr/' + lang + '; path=/' + dom;
    });
  };
  const currentLang = getLang();
  $$('.lang-switch button').forEach((b) => {
    b.classList.toggle('is-active', b.dataset.lang === currentLang);
    b.addEventListener('click', () => {
      if (b.dataset.lang === getLang()) return;
      setLangCookie(b.dataset.lang);
      location.reload();
    });
  });
  if (currentLang !== 'fr') {
    document.documentElement.setAttribute('lang', currentLang);
    window.googleTranslateElementInit = function () {
      /* global google */
      new google.translate.TranslateElement({ pageLanguage: 'fr', includedLanguages: 'en,fr,it', autoDisplay: false }, 'google_translate_element');
    };
    const s = document.createElement('script');
    s.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    s.async = true;
    document.body.appendChild(s);
  }

  /* -------------------------------------------- Texte animé lettre par lettre */
  const splitText = (el) => {
    if (el.dataset.splitDone) return;
    el.dataset.splitDone = '1';
    const walk = (node) => {
      Array.from(node.childNodes).forEach((child) => {
        if (child.nodeType === 3) {
          const frag = document.createDocumentFragment();
          child.textContent.split(/(\s+)/).forEach((word) => {
            if (!word) return;
            if (/^\s+$/.test(word)) { frag.appendChild(document.createTextNode(' ')); return; }
            const w = document.createElement('span');
            w.className = 'split-word';
            Array.from(word).forEach((ch) => {
              const c = document.createElement('span');
              c.className = 'split-char';
              c.textContent = ch;
              w.appendChild(c);
            });
            frag.appendChild(w);
          });
          child.replaceWith(frag);
        } else if (child.nodeType === 1) {
          walk(child);
        }
      });
    };
    walk(el);
    $$('.split-char', el).forEach((c, i) => { c.style.transitionDelay = Math.min(i * 22, 1400) + 'ms'; });
  };
  const splitEls = $$('[data-split]');
  if (!reduceMotion && currentLang === 'fr') splitEls.forEach(splitText);

  /* --------------------------------------------- Apparition au défilement */
  const revealEls = $$('[data-reveal]');
  const counters = $$('[data-count]');
  const animateCount = (el) => {
    if (el.dataset.counted) return;
    el.dataset.counted = '1';
    const target = parseFloat(el.dataset.count) || 0;
    if (reduceMotion) { el.textContent = String(target); return; }
    const start = performance.now();
    const dur = 1800;
    const step = (t) => {
      const p = Math.min(1, (t - start) / dur);
      const eased = 1 - Math.pow(1 - p, 3);
      el.textContent = String(Math.round(target * eased));
      if (p < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  };
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        if (el.hasAttribute('data-reveal')) {
          const d = parseInt(el.dataset.delay || '0', 10);
          setTimeout(() => el.classList.add('is-revealed'), d);
        }
        if (el.hasAttribute('data-split')) el.classList.add('is-split-revealed');
        if (el.hasAttribute('data-count')) animateCount(el);
        io.unobserve(el);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    revealEls.forEach((el) => io.observe(el));
    splitEls.forEach((el) => io.observe(el));
    counters.forEach((el) => io.observe(el));
  } else {
    revealEls.forEach((el) => el.classList.add('is-revealed'));
    splitEls.forEach((el) => el.classList.add('is-split-revealed'));
    counters.forEach(animateCount);
  }

  /* ------------------------------------------------------------- Carrousels */
  const initSwipers = () => {
    if (typeof window.Swiper === 'undefined') return;
    const S = window.Swiper;

    const hero = $('.hero-slider');
    if (hero) {
      const total = $$('.swiper-slide', hero).length;
      const cur = $('.hero-current');
      new S(hero, {
        effect: 'fade', fadeEffect: { crossFade: true }, speed: 1200, loop: total > 1,
        autoplay: total > 1 ? { delay: 6500, disableOnInteraction: false } : false,
        navigation: { nextEl: '.hero-next', prevEl: '.hero-prev' },
        pagination: { el: '.hero-pagination', clickable: true },
        on: { slideChange(sw) { if (cur) cur.textContent = String(sw.realIndex + 1).padStart(2, '0'); } },
      });
    }

    const cats = $('.categories-slider');
    if (cats) {
      new S(cats, {
        slidesPerView: 1.15, spaceBetween: 20, speed: 800, grabCursor: true,
        navigation: { nextEl: '.cat-next', prevEl: '.cat-prev' },
        breakpoints: { 576: { slidesPerView: 2 }, 992: { slidesPerView: 3, spaceBetween: 24 }, 1200: { slidesPerView: 4, spaceBetween: 26 } },
      });
    }

    $$('.card-slider').forEach((el) => {
      new S(el, {
        speed: 600, loop: true, nested: true,
        navigation: { nextEl: $('.card-next', el), prevEl: $('.card-prev', el) },
        pagination: { el: $('.card-dots', el), clickable: true },
      });
    });

    const testi = $('.testimonials-slider');
    if (testi) {
      new S(testi, {
        slidesPerView: 1, spaceBetween: 26, speed: 900, loop: $$('.swiper-slide', testi).length > 2, autoHeight: false,
        autoplay: { delay: 7000, disableOnInteraction: true },
        navigation: { nextEl: '.testi-next', prevEl: '.testi-prev' },
        pagination: { el: '.testi-pagination', clickable: true },
        breakpoints: { 992: { slidesPerView: 1.25 } },
      });
    }

    const news = $('.news-slider');
    if (news) {
      new S(news, {
        slidesPerView: 1.08, spaceBetween: 22, speed: 800, grabCursor: true,
        navigation: { nextEl: '.news-next', prevEl: '.news-prev' },
        breakpoints: { 640: { slidesPerView: 2 }, 1100: { slidesPerView: 3, spaceBetween: 28 } },
      });
    }

    const similar = $('.similar-slider');
    if (similar) {
      new S(similar, {
        slidesPerView: 1.05, spaceBetween: 24, speed: 800,
        navigation: { nextEl: '.similar-next', prevEl: '.similar-prev' },
        breakpoints: { 680: { slidesPerView: 2 }, 1100: { slidesPerView: 3, spaceBetween: 28 } },
      });
    }

    const gMain = $('.gallery-main .swiper');
    if (gMain) {
      const thumbsEl = $('.gallery-thumbs');
      const thumbs = thumbsEl ? new S(thumbsEl, {
        slidesPerView: 3.5, spaceBetween: 12, watchSlidesProgress: true, freeMode: true,
        breakpoints: { 640: { slidesPerView: 5 }, 1000: { slidesPerView: 6 } },
      }) : null;
      const count = $('.gallery-current');
      new S(gMain, {
        speed: 700, loop: false, keyboard: { enabled: true },
        navigation: { nextEl: '.gallery-next', prevEl: '.gallery-prev' },
        thumbs: thumbs ? { swiper: thumbs } : undefined,
        on: { slideChange(sw) { if (count) count.textContent = String(sw.activeIndex + 1); } },
      });
    }
  };
  if (document.readyState === 'complete') initSwipers(); else window.addEventListener('load', initSwipers);

  /* ------------------------------------------------- Filtres (accueil) */
  $$('.filter-tabs').forEach((tabs) => {
    const grid = tabs.closest('section').querySelector('.filterable');
    if (!grid) return;
    $$('.filter-tab', tabs).forEach((tab) => {
      tab.addEventListener('click', () => {
        $$('.filter-tab', tabs).forEach((t) => { t.classList.remove('is-active'); t.setAttribute('aria-selected', 'false'); });
        tab.classList.add('is-active');
        tab.setAttribute('aria-selected', 'true');
        const f = tab.dataset.filter;
        $$('.property-card', grid).forEach((card, i) => {
          const show = f === '*' || card.dataset.cat === f;
          card.classList.toggle('is-hidden', !show);
          card.classList.remove('is-entering');
          if (show) { void card.offsetWidth; card.style.animationDelay = (i % 6) * 60 + 'ms'; card.classList.add('is-entering'); }
        });
      });
    });
  });

  /* ----------------------------------------------- Panneaux de services */
  $$('.service-panels').forEach((wrap) => {
    const panels = $$('.service-panel', wrap);
    panels.forEach((p) => {
      const activate = () => { panels.forEach((x) => x.classList.toggle('is-active', x === p)); };
      p.addEventListener('mouseenter', activate);
      p.addEventListener('focus', activate);
    });
  });

  /* ------------------------------------------------------------ Accordéon */
  const setAccordion = (item, open) => {
    const panel = $('.accordion-panel', item);
    const btn = $('.accordion-btn', item);
    item.classList.toggle('is-open', open);
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) {
      panel.style.height = panel.scrollHeight + 'px';
      panel.addEventListener('transitionend', function te() { if (item.classList.contains('is-open')) panel.style.height = 'auto'; panel.removeEventListener('transitionend', te); });
    } else {
      panel.style.height = panel.scrollHeight + 'px';
      requestAnimationFrame(() => { panel.style.height = '0px'; });
    }
  };
  $$('.accordion').forEach((acc) => {
    $$('.accordion-item', acc).forEach((item) => {
      const btn = $('.accordion-btn', item);
      if (item.classList.contains('is-open')) $('.accordion-panel', item).style.height = 'auto';
      btn.addEventListener('click', () => {
        const open = !item.classList.contains('is-open');
        if (open && acc.dataset.single !== undefined) $$('.accordion-item.is-open', acc).forEach((o) => o !== item && setAccordion(o, false));
        setAccordion(item, open);
      });
    });
  });
  $$('[data-faq-filter]').forEach((btn) => {
    btn.addEventListener('click', () => {
      $$('[data-faq-filter]').forEach((b) => b.classList.toggle('is-active', b === btn));
      const cat = btn.dataset.faqFilter;
      $$('.accordion-item[data-cat]').forEach((it) => it.classList.toggle('is-hidden', cat !== '*' && it.dataset.cat !== cat));
    });
  });

  /* ---------------------------------------------------- Onglets des plans */
  $$('.plan-tabs').forEach((tabs) => {
    const wrap = tabs.parentElement;
    $$('.plan-tab', tabs).forEach((t, i) => {
      t.addEventListener('click', () => {
        $$('.plan-tab', tabs).forEach((x) => x.classList.toggle('is-active', x === t));
        $$('.plan-pane', wrap).forEach((p, j) => p.classList.toggle('is-active', j === i));
      });
    });
  });

  /* -------------------------------------------------------- Visionneuse */
  const lb = $('#lightbox');
  let lbItems = [];
  let lbIndex = 0;
  const lbShow = (i) => {
    if (!lb || !lbItems.length) return;
    lbIndex = (i + lbItems.length) % lbItems.length;
    const it = lbItems[lbIndex];
    const img = $('img', lb);
    img.src = it.src;
    img.alt = it.caption || '';
    if (/^https?:/i.test(it.src)) img.referrerPolicy = 'no-referrer';
    $('figcaption', lb).textContent = it.caption || '';
    $('.lightbox-counter', lb).textContent = (lbIndex + 1) + ' / ' + lbItems.length;
    $('.lightbox-prev', lb).hidden = lbItems.length < 2;
    $('.lightbox-next', lb).hidden = lbItems.length < 2;
  };
  function closeLightbox() { if (lb && !lb.hidden) { lb.hidden = true; document.body.classList.remove('no-scroll'); } }
  document.addEventListener('click', (e) => {
    const a = e.target.closest('[data-lightbox]');
    if (!a || !lb) return;
    e.preventDefault();
    const group = a.dataset.lightbox;
    const els = $$('[data-lightbox="' + group + '"]').filter((x, idx, arr) => arr.findIndex((y) => (y.getAttribute('href') || y.dataset.src) === (x.getAttribute('href') || x.dataset.src)) === idx);
    lbItems = els.map((x) => ({ src: x.getAttribute('href') || x.dataset.src, caption: x.dataset.caption || '' }));
    const src = a.getAttribute('href') || a.dataset.src;
    lb.hidden = false;
    document.body.classList.add('no-scroll');
    lbShow(Math.max(0, lbItems.findIndex((x) => x.src === src)));
  });
  if (lb) {
    $('.lightbox-close', lb).addEventListener('click', closeLightbox);
    $('.lightbox-prev', lb).addEventListener('click', () => lbShow(lbIndex - 1));
    $('.lightbox-next', lb).addEventListener('click', () => lbShow(lbIndex + 1));
    lb.addEventListener('click', (e) => { if (e.target === lb) closeLightbox(); });
    document.addEventListener('keydown', (e) => {
      if (lb.hidden) return;
      if (e.key === 'ArrowLeft') lbShow(lbIndex - 1);
      if (e.key === 'ArrowRight') lbShow(lbIndex + 1);
    });
    let x0 = null;
    lb.addEventListener('touchstart', (e) => { x0 = e.touches[0].clientX; }, { passive: true });
    lb.addEventListener('touchend', (e) => { if (x0 === null) return; const dx = e.changedTouches[0].clientX - x0; if (Math.abs(dx) > 50) lbShow(lbIndex + (dx < 0 ? 1 : -1)); x0 = null; });
  }

  /* ------------------------------------------------------------- Vidéo */
  const vm = $('#videoModal');
  const ytId = (url) => { const m = String(url).match(/(?:youtu\.be\/|v=|embed\/|shorts\/)([\w-]{11})/); return m ? m[1] : null; };
  function closeVideo() { if (vm && !vm.hidden) { vm.hidden = true; $('.video-frame', vm).innerHTML = ''; document.body.classList.remove('no-scroll'); } }
  $$('[data-video]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const id = ytId(btn.dataset.video);
      if (!id || !vm) { window.open(btn.dataset.video, '_blank', 'noopener'); return; }
      $('.video-frame', vm).innerHTML = '<iframe src="https://www.youtube-nocookie.com/embed/' + id + '?autoplay=1&rel=0" title="Vidéo GELPAZ IMMO" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>';
      vm.hidden = false;
      document.body.classList.add('no-scroll');
    });
  });
  if (vm) {
    $('.video-modal-close', vm).addEventListener('click', closeVideo);
    vm.addEventListener('click', (e) => { if (e.target === vm) closeVideo(); });
  }

  /* ------------------------------------------------------------ Favoris */
  const FKEY = 'gelpaz_favoris';
  const getFavs = () => { try { return JSON.parse(localStorage.getItem(FKEY) || '[]'); } catch (e) { return []; } };
  const syncFavs = () => {
    const favs = getFavs();
    $$('[data-fav]').forEach((b) => {
      const on = favs.includes(b.dataset.fav);
      b.classList.toggle('is-fav', on);
      b.setAttribute('aria-pressed', on ? 'true' : 'false');
      b.setAttribute('aria-label', on ? 'Retirer des favoris' : 'Ajouter aux favoris');
    });
  };
  document.addEventListener('click', (e) => {
    const b = e.target.closest('[data-fav]');
    if (!b) return;
    e.preventDefault();
    let favs = getFavs();
    const k = b.dataset.fav;
    favs = favs.includes(k) ? favs.filter((x) => x !== k) : favs.concat(k);
    localStorage.setItem(FKEY, JSON.stringify(favs));
    syncFavs();
  });
  syncFavs();

  /* ------------------------------------------------- Partage / copie de lien */
  $$('[data-copy]').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const text = btn.dataset.copy || location.href;
      try { await navigator.clipboard.writeText(text); } catch (e) { const t = document.createElement('textarea'); t.value = text; document.body.appendChild(t); t.select(); document.execCommand('copy'); t.remove(); }
      const old = btn.getAttribute('aria-label');
      btn.setAttribute('aria-label', 'Lien copié !');
      btn.classList.add('is-copied');
      setTimeout(() => { btn.setAttribute('aria-label', old || 'Copier le lien'); btn.classList.remove('is-copied'); }, 2000);
    });
  });
  $$('[data-print]').forEach((b) => b.addEventListener('click', () => window.print()));
  $$('[data-native-share]').forEach((b) => {
    if (!navigator.share) { b.hidden = true; return; }
    b.addEventListener('click', () => navigator.share({ title: document.title, url: location.href }).catch(() => {}));
  });

  /* ------------------------------------- Formulaires : objet => type, villa */
  $$('[data-subject-select]').forEach((sel) => {
    const form = sel.closest('form');
    const typeField = form && $('[data-type-field]', form);
    const sync = () => { if (typeField) typeField.value = sel.selectedOptions[0] && sel.selectedOptions[0].dataset.type ? sel.selectedOptions[0].dataset.type : 'contact'; };
    sel.addEventListener('change', sync);
    sync();
  });
  $$('[data-select-villa]').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      const val = btn.dataset.selectVilla;
      const radio = $$('input[name="villa_type"]').find((r) => r.value === val);
      if (radio) { radio.checked = true; }
      const target = $('#formulaire-souscription');
      if (target) { e.preventDefault(); target.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' }); }
    });
  });

  /* ------------------------------------------------- Réponse aux commentaires */
  $$('[data-reply]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const form = $('#comment-form');
      if (!form) return;
      $('input[name="parent_id"]', form).value = btn.dataset.reply;
      const info = $('.replying-to', form);
      $('.replying-name', info).textContent = btn.dataset.name;
      info.classList.add('is-visible');
      form.scrollIntoView({ behavior: 'smooth', block: 'center' });
      const ta = $('textarea', form); ta && ta.focus({ preventScroll: true });
    });
  });
  $$('[data-cancel-reply]').forEach((btn) => btn.addEventListener('click', () => {
    const form = btn.closest('form');
    $('input[name="parent_id"]', form).value = '';
    $('.replying-to', form).classList.remove('is-visible');
  }));

  /* ------------------------------------------------------ Formulaires AJAX */
  $$('form[data-ajax-form]').forEach((form) => {
    form.addEventListener('submit', async (e) => {
      if (!window.fetch || !window.FormData) return;
      e.preventDefault();
      const btn = $('button[type="submit"]', form);
      const msg = $('.form-message', form);
      $$('.field', form).forEach((f) => f.classList.remove('has-error'));
      $$('.field-error', form).forEach((f) => { f.textContent = ''; });
      if (msg) { msg.className = 'form-message'; msg.textContent = ''; }
      if (btn) btn.classList.add('is-loading');
      try {
        const res = await fetch(form.action, {
          method: 'POST', body: new FormData(form), credentials: 'same-origin',
          headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
        });
        const data = await res.json().catch(() => ({ ok: false, message: 'Réponse inattendue du serveur. Merci de réessayer.' }));
        if (msg) { msg.textContent = data.message || ''; msg.classList.add(data.ok ? 'is-success' : 'is-error'); }
        if (data.ok) {
          form.reset();
          $$('[data-subject-select]', form).forEach((s) => s.dispatchEvent(new Event('change')));
          const ts = $('input[name="_ts"]', form);
          if (ts) ts.dataset.used = '1';
        } else if (data.errors) {
          Object.entries(data.errors).forEach(([name, text]) => {
            const err = $('[data-error-for="' + name + '"]', form);
            if (err) { err.textContent = text; const f = err.closest('.field'); f && f.classList.add('has-error'); }
          });
          const first = $('.has-error input, .has-error select, .has-error textarea', form);
          first && first.focus();
        }
      } catch (err) {
        if (msg) { msg.textContent = 'Connexion impossible. Vérifiez votre réseau ou appelez-nous directement.'; msg.classList.add('is-error'); }
      } finally {
        if (btn) btn.classList.remove('is-loading');
      }
    });
  });

  /* -------------------------------------------------- Ancre avec décalage */
  $$('a[href^="#"]:not([href="#"])').forEach((a) => {
    a.addEventListener('click', (e) => {
      const t = document.getElementById(a.getAttribute('href').slice(1));
      if (!t) return;
      e.preventDefault();
      window.scrollTo({ top: t.getBoundingClientRect().top + window.scrollY - 110, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
  });
})();
