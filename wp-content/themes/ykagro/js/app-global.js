/* global Lenis, gsap, ScrollTrigger */
// Global UI: smooth scroll (Lenis + GSAP ticker), autoplay video priming.
if ("scrollRestoration" in window.history) {
  window.history.scrollRestoration = "manual";
}
if (!window.location.hash) {
  window.scrollTo(0, 0);
}

(function syncViewportHeightUnit() {
  var raf = null;
  var lastHeight = 0;
  var lastWidth = 0;

  function update() {
    if (raf) window.cancelAnimationFrame(raf);
    raf = window.requestAnimationFrame(function () {
      raf = null;
      var viewport = window.visualViewport;
      var doc = document.documentElement;
      var width = Math.round((viewport && viewport.width) || window.innerWidth || doc.clientWidth || 0);
      var height = Math.round(Math.max(
        (viewport && viewport.height) || 0,
        window.innerHeight || 0,
        doc.clientHeight || 0
      ));

      if (!height) return;

      var heightDelta = lastHeight ? Math.abs(height - lastHeight) : 999;
      var widthDelta = lastWidth ? Math.abs(width - lastWidth) : 999;

      // Ignore sub-pixel noise
      if (heightDelta < 2 && widthDelta < 2) return;

      // Mobile browser chrome (URL bar) shifts height by ~40–70px while scrolling.
      // Updating --app-vh / firing refresh on those micro-shifts resizes the
      // pinned directions block mid-scroll and feels like hitching. Only commit
      // real resizes / orientation changes.
      var significant = !lastHeight || widthDelta >= 2 || heightDelta >= 80;
      if (!significant) return;

      lastHeight = height;
      lastWidth = width || lastWidth;
      document.documentElement.style.setProperty("--app-vh", height + "px");
      window.dispatchEvent(new CustomEvent("app:viewport-height-change", { detail: { height: height, width: lastWidth } }));
    });
  }

  update();
  window.addEventListener("resize", update, { passive: true });
  window.addEventListener("orientationchange", update, { passive: true });

  if (window.visualViewport) {
    window.visualViewport.addEventListener("resize", update, { passive: true });
  }
})();

(function initLightboxLock() {
  var lockedScrollY = 0;
  var isLocked = false;
  var resizeHandler = null;
  var CLASSIC_BAR_MIN = 4;

  function getScrollbarComp() {
    return Math.max(0, window.innerWidth - document.documentElement.clientWidth);
  }

  function hasClassicBar(sbw) {
    return sbw >= CLASSIC_BAR_MIN;
  }

  function needsBodyScrollPin(sbw) {
    if (hasClassicBar(sbw)) return false;
    return window.matchMedia("(hover: none), (pointer: coarse)").matches;
  }

  function applyScrollCompStyles(sbw) {
    document.documentElement.style.setProperty("--scrollbar-comp", sbw + "px");
    document.body.style.paddingRight = sbw + "px";
  }

  function clearScrollCompStyles() {
    document.documentElement.style.setProperty("--scrollbar-comp", "0px");
    document.body.style.paddingRight = "";
  }

  function lock() {
    if (isLocked) return;

    lockedScrollY = Math.round(
      (window.lenis && typeof window.lenis.scroll === "number" ? window.lenis.scroll : window.scrollY) || 0
    );
    isLocked = true;

    if (window.lenis && window.lenis.stop) window.lenis.stop();

    clearScrollCompStyles();

    var sbw = getScrollbarComp();
    document.documentElement.classList.add("is-lg-open");
    document.body.classList.add("is-lg-open");

    if (hasClassicBar(sbw)) applyScrollCompStyles(sbw);

    if (needsBodyScrollPin(sbw)) {
      document.body.style.top = -lockedScrollY + "px";
      document.body.classList.add("is-lg-scroll-pin");
      document.documentElement.classList.add("is-lg-open--pin");
    }
  }

  function unlock() {
    if (!isLocked) return;
    isLocked = false;

    var y = lockedScrollY;
    document.body.style.top = "";
    document.documentElement.classList.remove("is-lg-open", "is-lg-open--pin");
    document.body.classList.remove("is-lg-open", "is-lg-scroll-pin");
    clearScrollCompStyles();

    if (window.lenis) {
      if (window.lenis.start) window.lenis.start();
      if (window.lenis.resize) window.lenis.resize();
      if (typeof window.lenis.scrollTo === "function") {
        window.lenis.scrollTo(y, { immediate: true, force: true });
        return;
      }
    }

    window.scrollTo(0, y);
  }

  function syncToolbarInset(outer) {
    var toolbar = outer && outer.querySelector(".lg-toolbar");
    if (!toolbar || !outer) return;
    outer.style.setProperty("--lg-media-inset", toolbar.offsetHeight + "px");
  }

  function bind(el, className) {
    var lightboxClass = className || "home-gallery-lightbox";

    el.addEventListener("lgBeforeOpen", function () {
      lock();
    });

    el.addEventListener("lgAfterOpen", function () {
      var outer = document.querySelector(".lg-outer");
      if (!outer) return;

      outer.classList.add(lightboxClass);
      syncToolbarInset(outer);

      resizeHandler = function () {
        syncToolbarInset(outer);
      };
      window.addEventListener("resize", resizeHandler);
    });

    el.addEventListener("lgAfterClose", function () {
      if (resizeHandler) {
        window.removeEventListener("resize", resizeHandler);
        resizeHandler = null;
      }
      unlock();
    });
  }

  window.YKLightbox = {
    lock: lock,
    unlock: unlock,
    syncToolbarInset: syncToolbarInset,
    bind: bind,
  };
})();

$(function () {
  // ── Button text hover: split label into chars (Crazy Asia style) ──
  (function initButtonTextHover() {
    document.querySelectorAll(".btn").forEach(function (btn) {
      if (btn.querySelector(".btn__char")) return;

      var existing = btn.querySelector(".btn__text");
      var label = "";

      if (existing) {
        label = (existing.getAttribute("data-text") || existing.textContent || "")
          .replace(/\s+/g, " ")
          .trim();
      } else {
        Array.prototype.slice.call(btn.childNodes).forEach(function (node) {
          if (node.nodeType !== Node.TEXT_NODE) return;
          label += node.textContent;
        });
        label = label.replace(/\s+/g, " ").trim();
      }

      if (!label) return;

      var text = document.createElement("span");
      text.className = "btn__text";

      Array.prototype.forEach.call(label, function (ch, index) {
        var char = document.createElement("span");
        char.className = "btn__char";
        char.setAttribute("data-char", ch === " " ? "\u00A0" : ch);
        char.style.setProperty("--char-index", String(index));
        char.textContent = ch === " " ? "\u00A0" : ch;
        text.appendChild(char);
      });

      if (existing) {
        existing.parentNode.replaceChild(text, existing);
        return;
      }

      Array.prototype.slice.call(btn.childNodes).forEach(function (node) {
        if (node.nodeType !== Node.TEXT_NODE) return;
        if (!node.textContent.replace(/\s+/g, " ").trim()) {
          node.parentNode.removeChild(node);
          return;
        }
        node.parentNode.replaceChild(text, node);
      });
    });
  })();

  // ── Smooth scroll (Lenis drives ScrollTrigger via the GSAP ticker so pinned
  // / scrub scenes stay in sync on touch too) ─────────────────────────
  // Lenis runs on every device (touch included) — no native-scroll fallback and
  // no QA switch: pinned/scrub scenes need Lenis to drive ScrollTrigger, and a
  // half-native mode was only ever a debugging aid.
  var isTouch = window.matchMedia("(hover: none), (pointer: coarse), (max-width: 767.98px)").matches;

  if (typeof Lenis !== "undefined" && !(window.lenis && typeof window.lenis.raf === "function")) {
    var lenis = new Lenis({
      // Touch: higher multiplier + syncTouchLerp = less “sticky” swipe than a
      // low-lerp Lenis takeover. Desktop wheel feel stays as before.
      lerp: isTouch ? 0.12 : 0.08,
      smoothWheel: true,
      wheelMultiplier: 1,
      touchMultiplier: isTouch ? 1.6 : 1,
      syncTouch: true,
      syncTouchLerp: isTouch ? 0.18 : 0.08,
      touchInertiaExponent: isTouch ? 1.45 : 1.7,
    });
    window.lenis = lenis;

    if (typeof gsap !== "undefined" && typeof ScrollTrigger !== "undefined") {
      lenis.on("scroll", ScrollTrigger.update);
      gsap.ticker.add(function (time) {
        lenis.raf(time * 1000);
      });
      gsap.ticker.lagSmoothing(0);
    } else {
      var raf = function (time) {
        lenis.raf(time);
        window.requestAnimationFrame(raf);
      };
      window.requestAnimationFrame(raf);
    }

    if (!window.location.hash && typeof lenis.scrollTo === "function") {
      lenis.scrollTo(0, { immediate: true, force: true });
    }
  }

  window.addEventListener("load", function () {
    if (window.location.hash) return;
    if (window.lenis && typeof window.lenis.scrollTo === "function") {
      window.lenis.scrollTo(0, { immediate: true, force: true });
    }
    if (typeof ScrollTrigger !== "undefined" && ScrollTrigger.refresh) {
      ScrollTrigger.refresh();
    }
  });

  // ── In-page anchor links (#id) ───────────────────────────────────
  // Lenis owns the scroll position, so the browser's native hash jump fights
  // it: the page jumps, then Lenis animates back to its own stored value.
  // Route every same-page anchor through lenis.scrollTo instead. The target's
  // CSS scroll-margin-top is honoured, so gaps stay declarative.
  (function initAnchorLinks() {
    function findTarget(hash) {
      if (!hash || hash.length < 2) return null;
      var id = hash.slice(1);
      try {
        id = decodeURIComponent(id);
      } catch (err) { /* keep raw id */ }
      var target = document.getElementById(id);
      if (target) return target;
      var byName = document.getElementsByName(id);
      return byName.length ? byName[0] : null;
    }

    function offsetFor(target) {
      var margin = parseFloat(getComputedStyle(target).scrollMarginTop);
      return isNaN(margin) ? 0 : -margin;
    }

    function scrollToTarget(target, immediate) {
      var offset = offsetFor(target);

      if (window.lenis && typeof window.lenis.scrollTo === "function") {
        if (window.lenis.isStopped && typeof window.lenis.start === "function") {
          window.lenis.start();
        }
        window.lenis.scrollTo(target, { offset: offset, immediate: !!immediate });
        return;
      }

      var top = target.getBoundingClientRect().top + window.pageYOffset + offset;
      window.scrollTo({ top: top, behavior: immediate ? "auto" : "smooth" });
    }

    document.addEventListener("click", function (event) {
      // Modules with their own anchor logic (article TOC) already preventDefault.
      if (event.defaultPrevented) return;
      if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

      var el = event.target;
      var link = el && el.closest ? el.closest("a[href]") : null;
      if (!link || link.target === "_blank") return;

      var href = link.getAttribute("href") || "";
      if (href.charAt(0) !== "#") return;

      var target = findTarget(href);
      if (!target) return;

      event.preventDefault();
      if (window.history && window.history.pushState) {
        window.history.pushState(null, "", href);
      }
      scrollToTarget(target, false);
    });

    // Landing straight on /page.php#id: re-apply the position once layout is
    // settled (lazy images / ScrollTrigger refresh move the target otherwise).
    if (window.location.hash) {
      window.addEventListener("load", function () {
        var target = findTarget(window.location.hash);
        if (target) scrollToTarget(target, true);
      });
    }
  })();

  // ── Dismissible promo card ───────────────────────────────────────
  $(document).on("click", ".js-product-card-close", function (e) {
    e.preventDefault();
    $(this).closest(".js-product-card").addClass("is-hidden");
  });

  // ── Autoplay videos: force play on load + as each enters the viewport ─
  (function primeAutoplayVideos() {
    var videos = Array.prototype.slice.call(document.querySelectorAll("video[autoplay]"));
    if (!videos.length) return;

    var mqMobile = window.matchMedia("(max-width: 767.98px)");

    function syncResponsiveVideo(v) {
      var mobilePoster = v.getAttribute("data-poster-mobile");
      var desktopPoster = v.getAttribute("data-poster-desktop");
      var nextPoster = mqMobile.matches ? mobilePoster : desktopPoster;

      if (nextPoster && v.getAttribute("poster") !== nextPoster) {
        v.setAttribute("poster", nextPoster);
      }

      var sources = Array.prototype.slice.call(v.querySelectorAll("source[data-src]"));
      if (!sources.length) return;

      var nextSource = sources.find(function (source) {
        var media = source.getAttribute("data-media");
        return !media || window.matchMedia(media).matches;
      });
      var changed = false;

      sources.forEach(function (source) {
        var src = source.getAttribute("data-src");
        if (source === nextSource && src && source.getAttribute("src") !== src) {
          source.setAttribute("src", src);
          changed = true;
        } else if (source !== nextSource && source.hasAttribute("src")) {
          source.removeAttribute("src");
          changed = true;
        }
      });

      if (changed) v.load();
    }

    function play(v) {
      syncResponsiveVideo(v);
      v.muted = true;
      v.playsInline = true;
      var p = v.play();
      if (p && typeof p.catch === "function") p.catch(function () {});
    }

    videos.forEach(function (v) {
      play(v);
      v.addEventListener("canplay", function () { play(v); }, { once: true });
    });

    if ("IntersectionObserver" in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) play(entry.target);
        });
      }, { rootMargin: "200px 0px" });
      videos.forEach(function (v) { io.observe(v); });
    }

    document.addEventListener("visibilitychange", function () {
      if (!document.hidden) videos.forEach(play);
    });

    if (mqMobile.addEventListener) {
      mqMobile.addEventListener("change", function () {
        videos.forEach(play);
      });
    }
  })();
});
