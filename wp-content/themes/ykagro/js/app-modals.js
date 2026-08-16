// Modals + standalone utility popups.
// Modal: one .js-modal wrapper (overlay + panels). Open with
// [data-modal-open="name"] → panel[data-modal="name"]. Close via overlay, X,
// Esc or any .js-modal-close.
$(function () {
  var $body = $("body");
  var $html = $("html");
  var $modalWrap = $(".js-modal");
  var closeTimer = null;
  var modalClipRaf = 0;
  var AUTO_DETAIL_COOKIE = "yk_detail_modal_opened";
  var AUTO_DETAIL_MAX_AGE = 60 * 60 * 24 * 365;

  // SVG objectBoundingBox clips scale with both width and height, so a taller
  // modal stretches the corner radii/cuts. Keep the CSS url(#...) masks, but
  // rewrite their normalized path from fixed px geometry for the active panel.
  var modalClipPaths = {
    desktop: document.querySelector("#modal-panel-clip path"),
    mobile: document.querySelector("#modal-panel-mobile-clip path"),
  };
  var hasDynamicModalClip = !!(modalClipPaths.desktop || modalClipPaths.mobile);

  function unit(value, total) {
    var result = total ? value / total : 0;
    result = Math.max(0, Math.min(1, result));
    return result.toFixed(4).replace(/0+$/, "").replace(/\.$/, "");
  }

  function modalPanelClipPath(panel) {
    var rect = panel.getBoundingClientRect();
    var w = Math.round(rect.width);
    var h = Math.round(rect.height);
    if (!w || !h) return "";

    var isMobile = window.matchMedia("(max-width: 767.98px)").matches;
    var radius = isMobile ? 20 : 24;
    var x1 = isMobile ? 22 : 24;
    var y1 = isMobile ? 32 : 88;
    var x2 = Math.max(x1 + 40, w - (isMobile ? 29 : 36));
    var y2 = isMobile ? 5 : 3;
    var rightY = isMobile ? 56 : 38;
    var leftY = isMobile ? 68 : 114;
    var leftC1Y = isMobile ? y1 + 18 : leftY - 14;
    var leftC2X = isMobile ? 8 : 10;
    var leftC2Y = isMobile ? y1 + 3 : y1 + 1;

    radius = Math.min(radius, Math.floor(w / 2), Math.floor(h / 2));
    rightY = Math.min(rightY, h - radius);
    leftY = Math.min(leftY, h - radius);

    function x(value) { return unit(value, w); }
    function y(value) { return unit(value, h); }

    return [
      "M" + x(x1) + "," + y(y1),
      "L" + x(x2) + "," + y(y2),
      "C" + x(w - 8) + "," + y(y2) + " " + x(w) + "," + y(y2 + 12) + " " + x(w) + "," + y(rightY),
      "L" + x(w) + "," + y(h - radius),
      "C" + x(w) + "," + y(h - 9) + " " + x(w - 9) + "," + y(h) + " " + x(w - radius) + "," + y(h),
      "L" + x(radius) + "," + y(h),
      "C" + x(9) + "," + y(h) + " " + x(0) + "," + y(h - 9) + " " + x(0) + "," + y(h - radius),
      "L" + x(0) + "," + y(leftY),
      "C" + x(0) + "," + y(leftC1Y) + " " + x(leftC2X) + "," + y(leftC2Y) + " " + x(x1) + "," + y(y1),
      "Z",
    ].join(" ");
  }

  function applyModalPanelClip(panel) {
    if (!hasDynamicModalClip || !panel || panel.classList.contains("modal__panel--video")) return;
    var path = modalPanelClipPath(panel);
    if (!path) return;
    var isMobile = window.matchMedia("(max-width: 767.98px)").matches;
    var target = isMobile ? modalClipPaths.mobile : modalClipPaths.desktop;
    if (target) target.setAttribute("d", path);
  }

  function refreshModalPanelClips() {
    if (!hasDynamicModalClip) return;
    $modalWrap.find(".modal__panel.is-active").each(function () {
      applyModalPanelClip(this);
    });
  }

  function requestModalPanelClipRefresh() {
    if (!hasDynamicModalClip) return;
    cancelAnimationFrame(modalClipRaf);
    modalClipRaf = requestAnimationFrame(refreshModalPanelClips);
  }

  if (hasDynamicModalClip && "ResizeObserver" in window) {
    var modalPanelObserver = new ResizeObserver(function (entries) {
      entries.forEach(function (entry) {
        applyModalPanelClip(entry.target);
      });
    });
    $modalWrap.find(".modal__panel").each(function () {
      modalPanelObserver.observe(this);
    });
  }

  $(window).on("resize orientationchange", requestModalPanelClipRefresh);

  function getCookie(name) {
    var prefix = encodeURIComponent(name) + "=";
    var items = document.cookie ? document.cookie.split("; ") : [];
    for (var i = 0; i < items.length; i++) {
      if (items[i].indexOf(prefix) === 0) return decodeURIComponent(items[i].slice(prefix.length));
    }
    return "";
  }

  function setCookie(name, value, maxAge) {
    document.cookie = [
      encodeURIComponent(name) + "=" + encodeURIComponent(value),
      "Max-Age=" + maxAge,
      "Path=/",
      "SameSite=Lax",
    ].join("; ");
  }

  // ── Scroll lock ──────────────────────────────────────────────────
  // Goal: the scrim covers the whole viewport (page + floating menu, no
  // undimmed scrollbar lane) and nothing behind it moves or shifts sideways.
  //
  // Desktop: drop the classic bar (overflow: clip, see _modals.scss) so fixed
  // inset:0 overlays reach the real viewport edge, then hand the freed width
  // back as padding-right so the page and the centred panel stay put.
  // Touch: no classic bar — pin body at the current offset instead.

  // Keys that scroll the document. Lenis preventDefaults wheel and touch while
  // stopped, but keyboard scrolling bypasses it completely.
  var SCROLL_KEYS = {
    ArrowUp: 1, ArrowDown: 1, PageUp: 1, PageDown: 1,
    Home: 1, End: 1, " ": 1, Spacebar: 1,
  };

  var lockedScrollY = 0;
  var isScrollLocked = false;

  function onLockedKeydown(e) {
    if (!SCROLL_KEYS[e.key]) return;
    // typing inside the modal must keep working — space in a text field, etc.
    if (e.target && e.target.closest && e.target.closest('input, textarea, select, [contenteditable="true"]')) {
      return;
    }
    e.preventDefault();
  }

  // Safety net if anything still moves the document (scrollbar drag, etc.).
  // When body is position:fixed (phones), window.scrollY is ~0 — chasing the
  // pre-lock Y would re-scroll the page under the modal.
  function onLockedScroll() {
    if (!isScrollLocked) return;
    var target = document.body.classList.contains("is-modal-scroll-pin") ? 0 : lockedScrollY;
    if (Math.abs(window.scrollY - target) > 1) {
      window.scrollTo(0, target);
    }
  }

  // Find an overflow scrollport under the pointer (the modal body, mostly).
  function getScrollableAncestor(el) {
    var node = el;
    while (node && node !== document.body && node !== document.documentElement) {
      if (node instanceof HTMLElement) {
        var oy = window.getComputedStyle(node).overflowY;
        if ((oy === "auto" || oy === "scroll" || oy === "overlay") && node.scrollHeight > node.clientHeight + 1) {
          return node;
        }
      }
      node = node.parentElement;
    }
    return null;
  }

  // Panels carry data-lenis-prevent so they can scroll while Lenis is stopped.
  // Block document scroll; only allow a real inner scrollport, stop at edges.
  function onLockedWheel(e) {
    if (!isScrollLocked) return;
    var scrollable = getScrollableAncestor(e.target);
    if (scrollable) {
      var dy = e.deltaY;
      var atTop = scrollable.scrollTop <= 0;
      var atBottom = scrollable.scrollTop + scrollable.clientHeight >= scrollable.scrollHeight - 1;
      if ((dy < 0 && atTop) || (dy > 0 && atBottom) || dy === 0) {
        e.preventDefault();
      }
      return;
    }
    e.preventDefault();
  }

  function onLockedTouchMove(e) {
    if (!isScrollLocked) return;
    if (getScrollableAncestor(e.target)) return;
    if (e.cancelable) e.preventDefault();
  }

  function getScrollbarComp() {
    return Math.max(0, window.innerWidth - document.documentElement.clientWidth);
  }

  // Fixed elements that must not drift when the bar is dropped (body padding
  // cannot reach them). The floating menu is centred via left:50% + translateX,
  // so the same padding-right pulls it back by half the freed width — exactly
  // the amount the wider viewport moved it. NOT the .modal wrapper: it has its
  // own symmetric padding and its panel is centred, so it simply re-centres.
  var COMP_TARGETS = ".js-menu";

  function applyScrollCompStyles(sbw) {
    document.documentElement.style.setProperty("--scrollbar-comp", sbw + "px");
    document.body.style.paddingRight = sbw + "px";
    $(COMP_TARGETS).css("padding-right", sbw + "px");
  }

  function clearScrollCompStyles() {
    document.documentElement.style.setProperty("--scrollbar-comp", "0px");
    document.body.style.paddingRight = "";
    $(COMP_TARGETS).css("padding-right", "");
  }

  // A real classic scrollbar is at least a few px wide. Anything narrower is an
  // overlay bar (phones, and mobile emulation which reports a stray 1px), so
  // there is no width to compensate — those get the body pin instead.
  var CLASSIC_BAR_MIN = 4;

  function hasClassicBar(sbw) {
    return sbw >= CLASSIC_BAR_MIN;
  }

  function needsBodyScrollPin(sbw) {
    if (hasClassicBar(sbw)) return false;
    return window.matchMedia("(hover: none), (pointer: coarse)").matches;
  }

  function lockScroll() {
    // Idempotent: switching panels (or reopening inside the close delay) calls
    // this again while already locked — re-measuring now would read sbw=0 (the
    // bar is already gone) and wipe the padding comp, shifting the page.
    if (isScrollLocked) return;

    lockedScrollY = Math.round(
      (window.lenis && typeof window.lenis.scroll === "number" ? window.lenis.scroll : window.scrollY) || 0
    );
    isScrollLocked = true;

    // Stop Lenis BEFORE any pin — otherwise it chases scrollY→0 back up.
    if (window.lenis && window.lenis.stop) window.lenis.stop();

    clearScrollCompStyles();

    // Measure while the bar is still there, THEN drop it (is-modal-open →
    // overflow: clip) and give the width back as padding so nothing reflows.
    var sbw = getScrollbarComp();
    $html.addClass("is-modal-open");
    $body.addClass("is-modal-open");

    if (hasClassicBar(sbw)) applyScrollCompStyles(sbw);

    if (needsBodyScrollPin(sbw)) {
      document.body.style.top = -lockedScrollY + "px";
      $body.addClass("is-modal-scroll-pin");
      $html.addClass("is-modal-open--pin");
    }

    window.addEventListener("keydown", onLockedKeydown, { passive: false });
    window.addEventListener("scroll", onLockedScroll, { passive: true });
    window.addEventListener("wheel", onLockedWheel, { passive: false });
    window.addEventListener("touchmove", onLockedTouchMove, { passive: false });
  }

  function unlockScroll() {
    if (!isScrollLocked) return;
    isScrollLocked = false;
    window.removeEventListener("keydown", onLockedKeydown);
    window.removeEventListener("scroll", onLockedScroll);
    window.removeEventListener("wheel", onLockedWheel);
    window.removeEventListener("touchmove", onLockedTouchMove);

    var y = lockedScrollY;

    document.body.style.top = "";
    $html.removeClass("is-modal-open is-modal-open--pin");
    $body.removeClass("is-modal-open is-modal-scroll-pin");
    clearScrollCompStyles();

    if (window.lenis) {
      if (window.lenis.start) window.lenis.start();
      if (window.lenis.resize) window.lenis.resize();
      if (typeof window.lenis.scrollTo === "function") {
        window.lenis.scrollTo(y, { immediate: true, force: true });
      } else {
        window.scrollTo(0, y);
      }
    } else {
      window.scrollTo(0, y);
    }
  }

  function openModal(name) {
    var $panel = $modalWrap.find('.modal__panel[data-modal="' + name + '"]');
    if (!$panel.length) return false;
    clearTimeout(closeTimer);
    $(".js-menu").removeClass("is-open is-ready");
    $modalWrap.find(".modal__panel").removeClass("is-active");
    $panel.addClass("is-active");
    $modalWrap.toggleClass("modal--video", name === "video");
    $modalWrap.addClass("is-open").attr("aria-hidden", "false");
    applyModalPanelClip($panel[0]);
    requestModalPanelClipRefresh();
    // the wrapper is the scrollport — start every panel from its top
    if ($modalWrap[0]) $modalWrap[0].scrollTop = 0;
    lockScroll();
    return true;
  }

  function closeModals() {
    if (!$modalWrap.hasClass("is-open")) return;
    $modalWrap.removeClass("is-open").attr("aria-hidden", "true");
    clearTimeout(closeTimer);
    // Unlock only AFTER the leave animation: restoring the scrollbar shrinks the
    // viewport by its width, and the still-visible (fading) centred panel would
    // slide left. Reopening in the meantime clears this timer (openModal).
    closeTimer = setTimeout(function () {
      $modalWrap.find(".modal__panel").removeClass("is-active");
      $modalWrap.removeClass("modal--video");
      unlockScroll();
    }, 500);
  }

  window.YKModal = { open: openModal, close: closeModals };
  // Same lock for other full-screen overlays (mobile catalog filter sheet), so
  // there is one implementation of "nothing behind me moves or shifts".
  window.YKScrollLock = { lock: lockScroll, unlock: unlockScroll };

  if (!getCookie(AUTO_DETAIL_COOKIE)) {
    window.setTimeout(function () {
      if ($modalWrap.hasClass("is-open")) return;
      if (openModal("detail")) {
        setCookie(AUTO_DETAIL_COOKIE, "1", AUTO_DETAIL_MAX_AGE);
      }
    }, 900);
  }

  $(document).on("click", "[data-modal-open]", function (e) {
    e.preventDefault();
    openModal($(this).data("modal-open"));
  });

  $(document).on("keydown", ".about-team__card[data-modal-open]", function (e) {
    if (e.key !== "Enter" && e.key !== " " && e.key !== "Spacebar") return;
    e.preventDefault();
    openModal($(this).data("modal-open"));
  });

  // Detail modal — fill title/text/(optional) CTA href from data-*
  $(document).on("click", "[data-detail-open]", function (e) {
    e.preventDefault();
    var $t = $(this);
    if ($t.data("title")) $modalWrap.find(".js-detail-title").text($t.data("title"));
    if ($t.data("text")) $modalWrap.find(".js-detail-text").text($t.data("text"));
    var href = $t.attr("data-href") || $t.data("href");
    if (href && href !== "#") $modalWrap.find(".js-detail-cta").attr("href", href);
    openModal("detail");
  });
  $(document).on("click", ".js-modal .js-modal-close", function (e) {
    e.preventDefault();
    closeModals();
  });
  $(document).on("keydown", function (e) {
    if (e.key === "Escape") closeModals();
  });

  // ── Field filled state (label float) ─────────────────────────────
  $(document).on("input change", ".form-block__input", function () {
    $(this).toggleClass("is-filled", !!$.trim($(this).val()));
  });

  // ── Standalone popups ────────────────────────────────────────────
  function showPopup(name) {
    $('.js-popup[data-popup="' + name + '"]').addClass("is-open");
  }
  function hidePopup(name) {
    $('.js-popup[data-popup="' + name + '"]').removeClass("is-open");
  }
  window.YKPopup = { show: showPopup, hide: hidePopup };

  $(document).on("click", "[data-popup-open]", function (e) {
    e.preventDefault();
    showPopup($(this).data("popup-open"));
  });
  $(document).on("click", ".js-popup-close", function (e) {
    e.preventDefault();
    $(this).closest(".js-popup").removeClass("is-open");
  });

  // Cookie popup — show until accepted (persisted in localStorage)
  if (!localStorage.getItem("yk-cookie-ok")) {
    setTimeout(function () { showPopup("cookie"); }, 1000);
  }
  $(document).on("click", '.js-popup[data-popup="cookie"] .js-popup-close', function () {
    try { localStorage.setItem("yk-cookie-ok", "1"); } catch (e) {}
  });

  // Footer SEO block: visible editor content + hidden editor content.
  $(document).on("click", ".js-seo-toggle", function (e) {
    e.preventDefault();
    var $block = $(this).closest(".js-seo-block").toggleClass("is-open");
    $(this).attr("aria-expanded", $block.hasClass("is-open") ? "true" : "false");
  });
});
