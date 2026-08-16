/* global gsap, ScrollTrigger, SplitType, lightGallery */
// Homepage scroll animations: pinned "Напрями діяльності" cards, (later)
// scroll-drawn vector line and clip-path reveals.
$(function () {
  // ── Gallery lightbox (lightgallery loaded only on the homepage) ───
  (function initHomeGalleryLightbox() {
    if (typeof lightGallery !== "function") return;

    document.querySelectorAll(".js-gallery-slider").forEach(function (el) {
      var sw = el.swiper;
      var galleryItems = Array.prototype.slice
        .call(el.querySelectorAll(".home-gallery__slide[data-photo-index]"))
        .reduce(function (items, slide) {
          var index = parseInt(slide.getAttribute("data-photo-index"), 10);
          var link = slide.querySelector(".home-gallery__media");
          var src = link && (link.getAttribute("data-lg-src") || link.getAttribute("href"));

          if (!src || !Number.isFinite(index) || items[index]) return items;

          items[index] = {
            src: src,
            thumb: src,
          };
          return items;
        }, [])
        .filter(Boolean);

      if (!galleryItems.length) return;

      var lightbox = lightGallery(el, {
        dynamic: true,
        dynamicEl: galleryItems,
        addClass: "home-gallery-lightbox",
        loop: true,
        hideScrollbar: true,
        resetScrollPosition: false,
        download: false,
        counter: true,
        closable: true,
        escKey: true,
        swipeToClose: true,
        mobileSettings: {
          controls: true,
          showCloseIcon: true,
          download: false,
        },
      });

      if (window.YKLightbox && typeof window.YKLightbox.bind === "function") {
        window.YKLightbox.bind(el, "home-gallery-lightbox");
      }

      el.addEventListener("click", function (event) {
        var link = event.target.closest(".home-gallery__media");
        if (!link || (sw && !sw.allowClick)) return;

        var slide = link.closest(".home-gallery__slide[data-photo-index]");
        var index = slide ? parseInt(slide.getAttribute("data-photo-index"), 10) : 0;
        if (!Number.isFinite(index)) index = 0;

        event.preventDefault();
        lightbox.openGallery(index);
      });
    });
  })();

  // ── Stats counters: run once when a stat item is fully visible ─────
  (function initStatsCounters() {
    var items = Array.prototype.slice.call(document.querySelectorAll(".home-stats__item, .about-stats__card"));
    if (!items.length) return;

    function getCounter(item) {
      return item ? item.querySelector(".stat__num, .about-stats__num") : null;
    }

    function splitNumber(text) {
      var match = String(text).trim().match(/-?\d+(?:[.,]\d+)?/);
      if (!match) return null;

      var raw = match[0];
      var value = parseFloat(raw.replace(",", "."));
      if (!isFinite(value)) return null;

      return {
        value: value,
        decimals: raw.indexOf(".") > -1 || raw.indexOf(",") > -1 ? raw.split(/[.,]/)[1].length : 0,
        prefix: text.slice(0, match.index),
        suffix: text.slice(match.index + raw.length),
      };
    }

    function formatValue(value, decimals) {
      return value.toLocaleString("uk-UA", {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
      });
    }

    function animateCounter(num) {
      if (!num || num.dataset.counted === "true") return;

      var data = splitNumber(num.dataset.targetText || num.textContent);
      if (!data) return;

      num.dataset.counted = "true";
      var duration = 1200;
      var startTime = null;

      function tick(time) {
        if (startTime === null) startTime = time;
        var progress = Math.min((time - startTime) / duration, 1);
        var eased = 1 - Math.pow(1 - progress, 3);
        var current = data.value * eased;

        num.textContent = data.prefix + formatValue(current, data.decimals) + data.suffix;

        if (progress < 1) {
          window.requestAnimationFrame(tick);
        } else {
          num.textContent = data.prefix + formatValue(data.value, data.decimals) + data.suffix;
        }
      }

      window.requestAnimationFrame(tick);
    }

    items.forEach(function (item) {
      var num = getCounter(item);
      if (!num) return;

      num.dataset.targetText = num.textContent.trim();
      var data = splitNumber(num.dataset.targetText);
      if (data) {
        num.textContent = data.prefix + formatValue(0, data.decimals) + data.suffix;
      }
    });

    if ("IntersectionObserver" in window) {
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting || entry.intersectionRatio < 1) return;

          animateCounter(getCounter(entry.target));
          observer.unobserve(entry.target);
        });
      }, { threshold: 1 });

      items.forEach(function (item) {
        observer.observe(item);
      });
    } else {
      items.forEach(function (item) {
        animateCounter(getCounter(item));
      });
    }
  })();

  if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") return;
  gsap.registerPlugin(ScrollTrigger);

  // Nothing homepage-specific on this page — skip ST listeners entirely.
  if (
    !document.querySelector(
      ".js-directions, .home-intro, .home-contact, .home-stats, .news-block__media, .js-statement-fill"
    )
  ) {
    return;
  }

  (function refreshOnViewportHeightChange() {
    var refreshTimer = null;
    var lastH = 0;
    var lastW = 0;

    // Skip ScrollTrigger.refresh on tiny mobile chrome (URL bar) height shifts —
    // those mid-scroll refreshes cause visible hitching. Still refresh on real
    // resizes / orientation (width change or large height delta).
    window.addEventListener("app:viewport-height-change", function (e) {
      var detail = e.detail || {};
      var h = detail.height || 0;
      var w = detail.width || 0;
      var heightDelta = lastH ? Math.abs(h - lastH) : 999;
      var widthDelta = lastW ? Math.abs(w - lastW) : 999;
      lastH = h || lastH;
      lastW = w || lastW;

      if (heightDelta < 80 && widthDelta < 2) return;

      if (refreshTimer) window.clearTimeout(refreshTimer);
      refreshTimer = window.setTimeout(function () {
        refreshTimer = null;
        ScrollTrigger.refresh();
      }, 200);
    });
  })();

  // ── Directions ────────────────────────────────────────────────────
  // Desktop (≥lg): pinned window + cards scrub through (existing).
  // Mobile (<lg): PEK services pattern — pin only the bg (100lvh, survives
  // browser chrome), title/lead stay in flow, cards rise once from below,
  // then exit parallax on the bg as the section leaves.
  document.querySelectorAll(".js-directions").forEach(function (section) {
    var pin = section.querySelector(".js-directions-pin");
    var bg = section.querySelector(".js-directions-bg") || section.querySelector(".home-directions__bg");
    var windowEl = section.querySelector(".js-directions-window");
    var track = section.querySelector(".js-directions-track");
    var cards = Array.prototype.slice.call(section.querySelectorAll(".direction-card"));
    if (!pin || !windowEl || !track || !cards.length) return;

    var isMobileStack = window.matchMedia("(max-width: 991.98px)").matches;

    if (isMobileStack) {
      section.classList.add("is-mobile-stack");
      if (!bg) return;

      ScrollTrigger.create({
        trigger: section,
        start: "top top",
        // Hold bg through the exit zone so the next section rides over a
        // lagging full-viewport image (same as PEK services mobile).
        end: "bottom top",
        pin: bg,
        pinSpacing: false,
        invalidateOnRefresh: true,
      });

      // Move the oversized <picture> inside the clipped bg (not the pinned
      // container) — same as PEK services mobile exit.
      var bgVisual = bg.querySelector("picture") || bg.querySelector("img") || bg;
      gsap.timeline({
        defaults: { ease: "none" },
        scrollTrigger: {
          trigger: section,
          start: "bottom bottom",
          end: "bottom top",
          scrub: true,
          invalidateOnRefresh: true,
          onEnter: function () { section.classList.add("is-exiting"); },
          onEnterBack: function () { section.classList.add("is-exiting"); },
          onLeave: function () { section.classList.remove("is-exiting"); },
          onLeaveBack: function () { section.classList.remove("is-exiting"); },
        },
      }).fromTo(
        bgVisual,
        { y: 0 },
        {
          y: function () { return window.innerHeight * 0.64; },
          duration: 1,
          immediateRender: false,
        },
        0
      );

      cards.forEach(function (card, i) {
        gsap.fromTo(
          card,
          { y: 76, autoAlpha: 0 },
          {
            y: 0,
            autoAlpha: 1,
            duration: 0.9,
            delay: i * 0.06,
            ease: "power3.out",
            scrollTrigger: {
              trigger: card,
              start: "top 88%",
              once: true,
            },
          }
        );
      });
      return;
    }

    section.classList.add("is-pinned");

    var cachedMetrics = null;
    var cardLayout = null;
    var setCardY = cards.map(function (card) { return gsap.quickSetter(card, "y", "px"); });
    var setCardScale = cards.map(function (card) { return gsap.quickSetter(card, "scale"); });
    var lastCardY = cards.map(function () { return NaN; });
    var lastCardScale = cards.map(function () { return NaN; });

    gsap.set(cards, { y: 72, scale: 0.96, transformOrigin: "50% 50%", pointerEvents: "auto" });

    function pinGap() {
      // the var may compute to "1rem"/"16px" — convert to px explicitly
      var raw = String(getComputedStyle(section).getPropertyValue("--pin-gap")).trim();
      var value = parseFloat(raw);
      if (!Number.isFinite(value)) return 16;
      if (raw.indexOf("rem") !== -1) {
        var rootFs = parseFloat(getComputedStyle(document.documentElement).fontSize) || 16;
        return value * rootFs;
      }
      if (raw.indexOf("em") !== -1) {
        var fs = parseFloat(getComputedStyle(section).fontSize) || 16;
        return value * fs;
      }
      return value; // px or unitless
    }

    function invalidateLayout() {
      cachedMetrics = null;
      cardLayout = null;
    }

    function metrics() {
      if (cachedMetrics) return cachedMetrics;

      var pinH = pin.clientHeight;
      var trackH = track.offsetHeight;
      var pad = Math.max(24, Math.min(48, pinH * 0.06));
      var startY = pinH + pad;
      var endY = -trackH - pad;

      cachedMetrics = {
        pinH: pinH,
        trackH: trackH,
        pad: pad,
        startY: startY,
        endY: endY,
        dist: Math.max(1, startY - endY),
      };
      return cachedMetrics;
    }

    function ensureCardLayout() {
      if (cardLayout) return cardLayout;
      cardLayout = cards.map(function (card) {
        return { top: card.offsetTop, height: card.offsetHeight };
      });
      return cardLayout;
    }

    function updateCards(progress) {
      var data = metrics();
      var currentY = data.startY + (data.endY - data.startY) * progress;
      var layouts = ensureCardLayout();
      // One layout read per frame (was 2×N getBoundingClientRect in cardState)
      var windowTop = windowEl.getBoundingClientRect().top - pin.getBoundingClientRect().top;
      var enterZone = Math.max(160, data.pinH * 0.28);
      var exitZone = Math.max(130, data.pinH * 0.22);

      for (var i = 0; i < cards.length; i++) {
        var layout = layouts[i];
        var top = windowTop + layout.top + currentY;
        var bottom = top + layout.height;
        var y = 0;
        var scale = 1;

        if (top > data.pinH - enterZone) {
          var entering = Math.max(0, Math.min(1, (data.pinH - top) / enterZone));
          y = (1 - entering) * 72;
          scale = 0.96 + entering * 0.04;
        } else if (bottom < exitZone) {
          var leaving = Math.max(0, Math.min(1, bottom / exitZone));
          y = -(1 - leaving) * 56;
          scale = 0.96 + leaving * 0.04;
        }

        if (lastCardY[i] !== y) {
          lastCardY[i] = y;
          setCardY[i](y);
        }
        if (lastCardScale[i] !== scale) {
          lastCardScale[i] = scale;
          setCardScale[i](scale);
        }
      }
    }

    gsap.set(track, { y: function () { return metrics().startY; } });

    var tween = gsap.fromTo(
      track,
      { y: function () { return metrics().startY; } },
      { y: function () { return metrics().endY; }, ease: "none", immediateRender: true }
    );

    ScrollTrigger.create({
      trigger: pin,
      start: function () { return "top top+=" + pinGap(); },
      end: function () { return "+=" + metrics().dist; },
      pin: true,
      pinSpacing: true,
      anticipatePin: 1,
      scrub: 0.8,
      invalidateOnRefresh: true,
      animation: tween,
      onRefreshInit: invalidateLayout,
      onRefresh: function (self) {
        updateCards(self.progress);
      },
      onUpdate: function (self) {
        updateCards(self.progress);
      },
    });
  });

  // ── Intro: layered photo/card parallax ────────────────────────────
  (function initIntroParallax() {
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    // Under lg the photos are small and sit *behind* the text card, so the
    // desktop travel just slides them under it and hides what is on them.
    // Function values + invalidateOnRefresh → the amplitude follows resizes.
    function amp(desktop, mobile) {
      return function () {
        return window.matchMedia("(max-width: 991.98px)").matches ? mobile : desktop;
      };
    }

    document.querySelectorAll(".home-intro").forEach(function (section) {
      var left = section.querySelector(".home-intro__media--left");
      var right = section.querySelector(".home-intro__media--right");
      var card = section.querySelector(".home-intro__card");

      if (left) {
        gsap.fromTo(left, { y: amp(-78, -22) }, {
          y: amp(78, 22),
          ease: "none",
          immediateRender: false,
          scrollTrigger: {
            trigger: section,
            start: "top bottom",
            end: "bottom top",
            scrub: 1.05,
            invalidateOnRefresh: true,
          },
        });
      }

      if (right) {
        gsap.fromTo(right, { y: amp(92, 26) }, {
          y: amp(-92, -26),
          ease: "none",
          immediateRender: false,
          scrollTrigger: {
            trigger: section,
            start: "top bottom",
            end: "bottom top",
            scrub: 1.05,
            invalidateOnRefresh: true,
          },
        });
      }

      if (card) {
        gsap.fromTo(card, { "--intro-card-parallax": amp("36px", "10px") }, {
          "--intro-card-parallax": amp("-36px", "-10px"),
          ease: "none",
          immediateRender: false,
          scrollTrigger: {
            trigger: section,
            start: "top bottom",
            end: "bottom top",
            scrub: 1.15,
            invalidateOnRefresh: true,
          },
        });
      }
    });
  })();

  // ── Quote intro hen: pop-in + small playful idle motion ───────────
  (function initIntroQuoteHen() {
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    document.querySelectorAll(".home-intro__hen").forEach(function (hen) {
      var section = hen.closest(".home-intro");
      var card = hen.closest(".home-intro__card");
      var svg = hen.querySelector("svg");
      if (!section || !svg) return;

      var quoteMark = card ? card.querySelector(".home-intro__quote-mark") : null;
      var redParts = Array.prototype.filter.call(svg.querySelectorAll("path"), function (path) {
        return (path.getAttribute("fill") || "").toLowerCase() === "#db0000";
      });
      var beakParts = Array.prototype.filter.call(svg.querySelectorAll("path"), function (path) {
        return (path.getAttribute("fill") || "").toLowerCase() === "#ffa600";
      });
      var idle = null;

      gsap.set(hen, { transformOrigin: "52% 100%", force3D: true });
      gsap.set(svg, { transformOrigin: "50% 100%", force3D: true });
      gsap.set(redParts, { transformOrigin: "50% 70%" });
      gsap.set(beakParts, { transformOrigin: "0% 50%" });

      function startIdle() {
        if (idle) return;

        idle = gsap.timeline({ repeat: -1, repeatDelay: 1.15 });
        idle
          .to(hen, { y: -8, rotation: -3.5, duration: 0.26, ease: "sine.out" })
          .to(hen, { y: 0, rotation: 1.5, duration: 0.42, ease: "bounce.out" })
          .to(svg, { scaleY: 0.96, scaleX: 1.035, duration: 0.12, ease: "sine.inOut" }, "-=0.18")
          .to(svg, { scaleY: 1, scaleX: 1, duration: 0.2, ease: "sine.out" })
          .to(redParts, { rotation: 7, y: -1, duration: 0.14, ease: "sine.out" }, "-=0.18")
          .to(redParts, { rotation: 0, y: 0, duration: 0.24, ease: "sine.in" })
          .to(beakParts, { scaleX: 1.12, rotation: 2, duration: 0.13, ease: "power2.out" }, "+=0.34")
          .to(beakParts, { scaleX: 1, rotation: 0, duration: 0.2, ease: "power2.in" })
          .to(hen, { y: -4, rotation: -1.5, duration: 0.2, ease: "sine.out" }, "+=0.54")
          .to(hen, { y: 0, rotation: 0, duration: 0.3, ease: "sine.inOut" });
      }

      function playAccent() {
        if (idle) idle.pause(0);

        gsap
          .timeline({
            onComplete: function () {
              if (idle) idle.play(0);
            },
          })
          .to(hen, { y: -16, rotation: -7, scale: 1.04, duration: 0.2, ease: "power2.out" })
          .to(hen, { y: 0, rotation: 0, scale: 1, duration: 0.48, ease: "bounce.out" })
          .to(redParts, { rotation: 9, y: -1, duration: 0.12, ease: "sine.out" }, 0.02)
          .to(redParts, { rotation: 0, y: 0, duration: 0.24, ease: "sine.in" }, 0.18);
      }

      gsap.fromTo(
        hen,
        { autoAlpha: 0, y: 34, x: 16, rotation: -13, scale: 0.72 },
        {
          autoAlpha: 1,
          y: 0,
          x: 0,
          rotation: 0,
          scale: 1,
          duration: 0.9,
          ease: "elastic.out(1, 0.58)",
          scrollTrigger: {
            trigger: section,
            start: "top 76%",
            once: true,
          },
          onComplete: startIdle,
        }
      );

      if (quoteMark) {
        gsap.fromTo(
          quoteMark,
          { autoAlpha: 0, scale: 0.86, rotation: -7 },
          {
            autoAlpha: 1,
            scale: 1,
            rotation: 0,
            duration: 0.62,
            ease: "back.out(2)",
            scrollTrigger: {
              trigger: section,
              start: "top 76%",
              once: true,
            },
          }
        );
      }

      hen.addEventListener("mouseenter", playAccent);
      hen.addEventListener("touchstart", playAccent, { passive: true });
    });
  })();

  // ── Contact collage photo parallax lives in app-contact-parallax.js ─
  // (shared by the homepage and the contacts page, so it travels with the
  // .home-contact component instead of this homepage-only script).

  // ── Media parallax: backgrounds and news photos ──────────────────
  (function initMediaParallax() {
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    function addParallax(target, trigger, amount, scrub) {
      if (!target || !trigger) return;

      gsap.fromTo(
        target,
        { y: -amount, scale: 1.35 },
        {
          y: amount,
          scale: 1.35,
          ease: "none",
          immediateRender: false,
          scrollTrigger: {
            trigger: trigger,
            start: "top bottom",
            end: "bottom top",
            scrub: scrub || 1.2,
            invalidateOnRefresh: true,
          },
        }
      );
    }

    function addFramedParallax(section, fallbackAmount) {
      if (!section) return;

      var target = section.querySelector("[data-parallax-target]");

      if (!target) return;

      var mqMobile = window.matchMedia("(max-width: 767.98px)");
      var baseIntensity = parseFloat(section.getAttribute("data-parallax-intensity")) || fallbackAmount || 120;

      function intensity() {
        return baseIntensity * (mqMobile.matches ? 0.6 : 1);
      }

      gsap.fromTo(
        target,
        {
          y: function () {
            return -intensity() / 2;
          },
        },
        {
          y: function () {
            return intensity() / 2;
          },
          ease: "none",
          immediateRender: false,
          scrollTrigger: {
            trigger: section.closest(".news-block") || section,
            start: "top bottom",
            end: "bottom top",
            scrub: 1.05,
            invalidateOnRefresh: true,
            onToggle: function (self) {
              target.style.willChange = self.isActive ? "transform" : "auto";
            },
          },
        }
      );
    }

    // Mobile directions uses its own pinned-bg + exit parallax (PEK pattern).
    if (!window.matchMedia("(max-width: 991.98px)").matches) {
      document.querySelectorAll(".home-directions__bg").forEach(function (bg) {
        addParallax(bg.querySelector("img"), bg.closest(".home-directions") || bg, 120, 1.05);
      });
    }

    document.querySelectorAll(".home-stats__bg").forEach(function (bg) {
      addParallax(bg.querySelector("img"), bg.closest(".home-stats") || bg, 104, 1.2);
    });

    document.querySelectorAll(".news-block__media").forEach(function (media) {
      if (media.closest(".about-story")) return;
      addFramedParallax(media, 120);
    });
  })();

  // ── Statement text: Releaf-style scroll fill ─────────────────────
  (function initStatementFill() {
    if (
      typeof SplitType === "undefined" ||
      window.matchMedia("(prefers-reduced-motion: reduce)").matches
    ) {
      return;
    }

    document.querySelectorAll(".js-statement-fill").forEach(function (el) {
      var section = el.closest(".home-statement") || el;
      var split = new SplitType(el, {
        types: "lines",
        lineClass: "home-statement__line",
      });
      if (!split.lines || !split.lines.length) return;

      el.classList.add("is-fill-ready");
      split.lines.forEach(function (line) {
        line.style.setProperty("--line-fill", "0%");
      });

      function paintLines(progress) {
        var count = split.lines.length;
        // One line at a time: split the scroll into equal, non-overlapping
        // segments so each line fills fully before the next one starts.
        var segment = 1 / count;

        split.lines.forEach(function (line, index) {
          var start = index * segment;
          var lineProgress = gsap.utils.clamp(0, 1, (progress - start) / segment);
          var next = (lineProgress * 100).toFixed(2) + "%";
          if (line.style.getPropertyValue("--line-fill") !== next) {
            line.style.setProperty("--line-fill", next);
          }
        });
      }

      ScrollTrigger.create({
        trigger: section,
        start: "top 82%",
        end: "bottom 30%",
        scrub: 0.7,
        invalidateOnRefresh: true,
        onRefresh: function (self) {
          paintLines(self.progress);
        },
        onUpdate: function (self) {
          paintLines(self.progress);
        },
      });
    });
  })();

  // recalc once fonts/images settle
  window.addEventListener("load", function () {
    ScrollTrigger.refresh();
  });
});
