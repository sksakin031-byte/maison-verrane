/* Maison Verrane theme scripts. Vanilla JS, no dependencies. */
(function () {
	'use strict';
	var cfg = window.MV || {};
	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function qs(s, c) { return (c || document).querySelector(s); }
	function qsa(s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); }

	/* ---------- Header: nav, search, sticky shadow ---------- */
	var header = qs('#mv-header');
	var navBtn = qs('.mv-navtoggle');
	var nav = qs('#mv-nav');
	if (navBtn && nav) {
		navBtn.addEventListener('click', function () {
			var open = navBtn.getAttribute('aria-expanded') === 'true';
			navBtn.setAttribute('aria-expanded', String(!open));
			document.body.classList.toggle('mv-nav-open', !open);
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && document.body.classList.contains('mv-nav-open')) {
				navBtn.click();
				navBtn.focus();
			}
		});
	}
	var sBtn = qs('.mv-searchtoggle');
	var sBox = qs('#mv-search');
	if (sBtn && sBox) {
		sBtn.addEventListener('click', function () {
			var open = sBtn.getAttribute('aria-expanded') === 'true';
			sBtn.setAttribute('aria-expanded', String(!open));
			sBox.hidden = open;
			if (!open) { var i = qs('input[type="search"]', sBox); if (i) { i.focus(); } }
		});
	}
	function onScroll() { if (header) { header.classList.toggle('is-stuck', window.scrollY > 8); } }
	window.addEventListener('scroll', onScroll, { passive: true });
	onScroll();

	/* ---------- Filters toggle (mobile) ---------- */
	var fBtn = qs('.mv-filters__toggle');
	if (fBtn) {
		fBtn.addEventListener('click', function () {
			var open = fBtn.getAttribute('aria-expanded') === 'true';
			fBtn.setAttribute('aria-expanded', String(!open));
			qs('#mv-filters').classList.toggle('is-open', !open);
		});
	}

	/* ---------- Hero slider ---------- */
	var hero = qs('[data-hero]');
	if (hero) {
		var slides = qsa('.mv-slide', hero);
		var dotsBox = qs('.mv-hero__dots', hero);
		var toggle = qs('[data-hero-toggle]', hero);
		var idx = 0, timer = null, playing = !reduce;
		var dots = slides.map(function (s, i) {
			var b = document.createElement('button');
			b.type = 'button';
			b.className = 'mv-dot';
			b.setAttribute('role', 'tab');
			b.setAttribute('aria-label', 'Slide ' + (i + 1));
			b.addEventListener('click', function () { go(i); restart(); });
			dotsBox.appendChild(b);
			return b;
		});
		function go(n) {
			idx = (n + slides.length) % slides.length;
			slides.forEach(function (s, i) {
				var on = i === idx;
				s.classList.toggle('is-active', on);
				s.setAttribute('aria-hidden', on ? 'false' : 'true');
				if (on) { s.removeAttribute('inert'); } else { s.setAttribute('inert', ''); }
			});
			dots.forEach(function (d, i) { d.setAttribute('aria-selected', i === idx ? 'true' : 'false'); d.classList.toggle('is-active', i === idx); });
		}
		function stop() { if (timer) { clearInterval(timer); timer = null; } }
		function start() { stop(); if (playing && slides.length > 1) { timer = setInterval(function () { go(idx + 1); }, 7000); } }
		function restart() { start(); }
		qs('[data-hero-prev]', hero).addEventListener('click', function () { go(idx - 1); restart(); });
		qs('[data-hero-next]', hero).addEventListener('click', function () { go(idx + 1); restart(); });
		if (toggle) {
			toggle.addEventListener('click', function () {
				playing = !playing;
				toggle.setAttribute('aria-label', playing ? cfg.i18n.pause : cfg.i18n.play);
				toggle.classList.toggle('is-paused', !playing);
				start();
			});
		}
		hero.addEventListener('mouseenter', stop);
		hero.addEventListener('mouseleave', start);
		hero.addEventListener('focusin', stop);
		hero.addEventListener('focusout', start);
		go(0);
		start();
	}

	/* ---------- Wishlist ---------- */
	var KEY = 'mv_wishlist';
	function readLocal() {
		try { return (JSON.parse(localStorage.getItem(KEY) || '[]') || []).map(Number).filter(Boolean); } catch (e) { return []; }
	}
	function writeLocal(a) { try { localStorage.setItem(KEY, JSON.stringify(a)); } catch (e) { /* storage blocked */ } }
	var ids = cfg.loggedIn ? (cfg.wishlist || []).map(Number) : readLocal();

	function post(action, data) {
		var body = new URLSearchParams();
		body.append('action', action);
		body.append('nonce', cfg.nonce);
		Object.keys(data || {}).forEach(function (k) { body.append(k, data[k]); });
		return fetch(cfg.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body }).then(function (r) { return r.json(); });
	}

	function paint() {
		qsa('.mv-wish').forEach(function (b) {
			var on = ids.indexOf(Number(b.dataset.product)) !== -1;
			b.setAttribute('aria-pressed', on ? 'true' : 'false');
			var label = on ? cfg.i18n.remove : cfg.i18n.add;
			b.setAttribute('aria-label', label);
			var t = qs('.mv-wish__text', b);
			if (t) { t.textContent = label; }
		});
		qsa('.mv-wish-count').forEach(function (c) { c.textContent = ids.length; c.dataset.count = ids.length; });
	}

	function loadWishlistPage() {
		var grid = qs('#mv-wishlist-grid');
		if (!grid) { return; }
		var empty = qs('#mv-wishlist-empty');
		if (!ids.length) { grid.innerHTML = ''; empty.hidden = false; return; }
		post('mv_wishlist_cards', { ids: ids.join(',') }).then(function (res) {
			if (res && res.success && res.data.html) {
				grid.innerHTML = res.data.html;
				empty.hidden = true;
				paint();
			} else {
				grid.innerHTML = '';
				empty.hidden = false;
			}
		}).catch(function () { grid.textContent = cfg.i18n.error; });
	}

	function toggleWish(id) {
		var had = ids.indexOf(id) !== -1;
		if (cfg.loggedIn) {
			ids = had ? ids.filter(function (x) { return x !== id; }) : ids.concat(id); // optimistic
			paint();
			post('mv_wishlist_toggle', { product_id: id }).then(function (res) {
				if (res && res.success) { ids = res.data.ids.map(Number); paint(); }
			}).catch(function () { /* keep optimistic state */ });
		} else {
			ids = had ? ids.filter(function (x) { return x !== id; }) : ids.concat(id);
			writeLocal(ids);
			paint();
		}
		var grid = qs('#mv-wishlist-grid');
		if (grid && had) {
			qsa('.mv-wish[data-product="' + id + '"]', grid).forEach(function (b) {
				var li = b.closest('li');
				if (li) { li.remove(); }
			});
			if (!qs('li', grid)) { grid.innerHTML = ''; qs('#mv-wishlist-empty').hidden = false; }
		}
	}

	document.addEventListener('click', function (e) {
		var b = e.target.closest ? e.target.closest('.mv-wish') : null;
		if (!b) { return; }
		e.preventDefault();
		toggleWish(Number(b.dataset.product));
	});

	// Merge guest list into account after login.
	if (cfg.loggedIn) {
		var local = readLocal();
		if (local.length) {
			post('mv_wishlist_sync', { ids: local.join(',') }).then(function (res) {
				if (res && res.success) { ids = res.data.ids.map(Number); writeLocal([]); paint(); loadWishlistPage(); }
			}).catch(function () {});
		}
	}

	paint();
	loadWishlistPage();
	// Re-apply heart state after WooCommerce / fragments refresh the DOM.
	if (window.jQuery) { window.jQuery(document.body).on('updated_wc_div wc_fragments_refreshed', paint); }
})();
