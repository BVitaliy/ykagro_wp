/* global gsap, ScrollTrigger */
// About page — hero scroll scene (waabi.ai pattern).
//
// The stage is sticky (CSS); this file only scrubs the animation over the
// section's scroll distance:
//   · a hold while the hero is still full-screen,
//   · the .page-hero banner shrinks onto the small centred photo below,
//   · logo / breadcrumbs / title ride up and off the screen on their own,
//   · the surrounding photos keep drifting at their own speeds.
//
// Both boxes are MEASURED — the start from the hero's padding box (i.e. from
// _page-hero.scss, so the frame is identical to every other page hero) and the
// end from .js-about-scene-target. Nothing here parses a token, and everything
// re-measures on refresh, so the scene stays exact at any width.
$(function () {
  // ── Video band → full clip in the shared modal ────────────────────
  // The panel is filled on open and emptied on close, so the heavy file is
  // never fetched (preload="none" + no src) until someone asks for it.
  (function initVideoModal() {
    var trigger = document.querySelector(".js-video-open");
    var wrap = document.querySelector(".js-modal");
    var video = wrap ? wrap.querySelector(".js-modal-video") : null;
    if (!trigger || !wrap || !video || !window.YKModal) return;

    trigger.addEventListener("click", function () {
      var src = trigger.getAttribute("data-video-src");
      var poster = trigger.getAttribute("data-video-poster");

      if (poster) video.setAttribute("poster", poster);
      if (src && video.getAttribute("src") !== src) {
        video.setAttribute("src", src);
        video.load();
      }

      window.YKModal.open("video");

      var played = video.play();
      if (played && typeof played.catch === "function") played.catch(function () {});
    });

    // The modal can be closed from the X, the scrim or Esc — watch the state
    // instead of duplicating those handlers.
    new MutationObserver(function () {
      if (wrap.classList.contains("is-open")) return;
      if (video.paused) return;
      video.pause();
      video.currentTime = 0;
    }).observe(wrap, { attributes: true, attributeFilter: ["class"] });
  })();

  // ── Story timeline: periods swap the photo and the copy ───────────
  (function initStory() {
    var root = document.querySelector(".js-about-story");
    if (!root) return;

    var periods = Array.prototype.slice.call(root.querySelectorAll("[data-story-period]"));
    var slides = Array.prototype.slice.call(root.querySelectorAll("[data-story-slide]"));
    var imgs = Array.prototype.slice.call(root.querySelectorAll("[data-story-img]"));
    var periodTrack = root.querySelector(".about-story__periods-track");
    if (periods.length < 2) return;

    var hasGsap = typeof gsap !== "undefined";
    var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    var current = parseInt(root.getAttribute("data-story-active"), 10) || 0;
    var running = null;

    function pieces(index) {
      return slides[index]
        ? Array.prototype.slice.call(slides[index].querySelectorAll("[data-story-piece]"))
        : [];
    }

    function syncPeriodIndicator(index) {
      if (!periodTrack || !periods[index]) return;

      window.requestAnimationFrame(function () {
        var btn = periods[index];
        periodTrack.style.setProperty("--story-indicator-x", btn.offsetLeft + "px");
        periodTrack.style.setProperty("--story-indicator-w", btn.offsetWidth + "px");
      });
    }

    function settle(index) {
      periods.forEach(function (btn, i) {
        btn.classList.toggle("is-active", i === index);
        btn.setAttribute("aria-selected", i === index ? "true" : "false");
      });
      slides.forEach(function (el, i) {
        el.classList.toggle("is-active", i === index);
      });
      imgs.forEach(function (el, i) {
        el.classList.toggle("is-active", i === index);
        el.style.zIndex = i === index ? "1" : "";
      });
      if (hasGsap) {
        gsap.set(root.querySelectorAll("[data-story-piece]"), { clearProps: "all" });
        gsap.set(imgs, { clearProps: "clipPath" });
        imgs.forEach(function (el) {
          var img = el.querySelector("img");
          if (img) gsap.set(img, { clearProps: "transform" });
        });
      }
      syncPeriodIndicator(index);
    }

    // Keep the active period in view inside this horizontal rail only.
    function reveal(btn, smooth) {
      var rail = root.querySelector(".about-story__periods");
      if (!rail || !btn || rail.scrollWidth <= rail.clientWidth) return;

      var railRect = rail.getBoundingClientRect();
      var btnRect = btn.getBoundingClientRect();
      var inner = rail.querySelector(".container");
      var gutter = inner ? parseFloat(window.getComputedStyle(inner).paddingLeft) || 0 : 0;
      var index = periods.indexOf(btn);
      var delta;

      if (index === 0) {
        delta = btnRect.left - (railRect.left + gutter);
      } else if (index === periods.length - 1) {
        delta = btnRect.right - (railRect.right - gutter);
      } else {
        delta = btnRect.left + btnRect.width / 2 - (railRect.left + railRect.width / 2);
      }

      if (Math.abs(delta) < 1) return;

      var maxScroll = Math.max(0, rail.scrollWidth - rail.clientWidth);
      var next = Math.max(0, Math.min(maxScroll, rail.scrollLeft + delta));
      rail.scrollTo({ left: next, behavior: smooth ? "smooth" : "auto" });
    }

    function goTo(index) {
      if (index === current || index < 0 || index >= periods.length) return;

      var previous = current;
      current = index;

      periods.forEach(function (btn, i) {
        btn.classList.toggle("is-active", i === index);
        btn.setAttribute("aria-selected", i === index ? "true" : "false");
      });
      syncPeriodIndicator(index);

      if (!hasGsap || reduce) {
        settle(index);
        return;
      }

      // A quick re-click mid-transition: drop the running timeline and settle
      // on its target first, so the new one starts from a clean state.
      if (running) {
        running.kill();
        running = null;
        settle(previous);
      }

      var inImg = imgs[index];
      var outPieces = pieces(previous);
      var inPieces = pieces(index);

      if (outPieces.length) {
        gsap.set(outPieces, { autoAlpha: 0, y: -16 });
      }
      slides[previous].classList.remove("is-active");
      gsap.set(slides[previous], { autoAlpha: 0 });
      slides[index].classList.add("is-active");
      gsap.set(slides[index], { autoAlpha: 1 });
      gsap.set(inPieces, { autoAlpha: 0, y: 30 });

      running = gsap.timeline({
        onComplete: function () {
          running = null;
          settle(index);
        },
      });

      // Photo wipes in over the previous one, settling out of a slight zoom.
      if (inImg) {
        inImg.classList.add("is-active");
        inImg.style.zIndex = "2";
        running.fromTo(
          inImg,
          { clipPath: "inset(0% 0% 100% 0%)" },
          { clipPath: "inset(0% 0% 0% 0%)", duration: 0.8, ease: "power2.inOut" },
          0
        );

        var inImgEl = inImg.querySelector("img");
        if (inImgEl) {
          running.fromTo(inImgEl, { scale: 1.07 }, { scale: 1, duration: 0.9, ease: "power2.out" }, 0);
        }
      }

      if (inPieces.length) {
        running.to(inPieces, { autoAlpha: 1, y: 0, duration: 0.5, ease: "power2.out", stagger: 0.08 }, 0.12);
      }
    }

    periods.forEach(function (btn) {
      btn.addEventListener("click", function () {
        goTo(parseInt(btn.getAttribute("data-story-period"), 10));
        reveal(btn, true);
      });
    });

    reveal(periods[current], false);
    syncPeriodIndicator(current);

    window.addEventListener("resize", function () {
      syncPeriodIndicator(current);
    }, { passive: true });

    // Touch: swipe the body sideways to step through the periods.
    (function initSwipe() {
      var body = root.querySelector(".about-story__body");
      if (!body) return;

      var mq = window.matchMedia("(max-width: 991.98px)");
      var startX = 0;
      var startY = 0;
      var tracking = false;
      var fired = false;

      body.addEventListener("touchstart", function (e) {
        if (!mq.matches) return;
        tracking = true;
        fired = false;
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
      }, { passive: true });

      body.addEventListener("touchmove", function (e) {
        if (!tracking || fired) return;
        var dx = e.touches[0].clientX - startX;
        var dy = e.touches[0].clientY - startY;
        // clearly horizontal only — vertical stays page scroll
        if (Math.abs(dx) < 45 || Math.abs(dx) < Math.abs(dy) * 1.3) return;
        fired = true;
        var next = current + (dx < 0 ? 1 : -1);
        if (next >= 0 && next < periods.length) {
          goTo(next);
          reveal(periods[next]);
        }
      }, { passive: true });

      body.addEventListener("touchend", function () {
        tracking = false;
      }, { passive: true });
    })();
  })();

  // ── Hero scroll scene ─────────────────────────────────────────────
  var section = document.querySelector(".js-about-scene");
  if (!section) return;

  // Keep __inner hidden (CSS) until first scroll, or 1s after full load —
  // whichever comes first. Runs even when the scrubbed scene is skipped
  // (no GSAP / reduced motion), so the composition is never stuck invisible.
  (function revealInner() {
    var done = false;

    function ready() {
      if (done) return;
      done = true;
      section.classList.add("is-inner-ready");
      window.removeEventListener("scroll", ready, true);
      window.removeEventListener("wheel", ready, true);
      window.removeEventListener("touchmove", ready, true);
    }

    if (window.scrollY > 0) {
      ready();
    } else {
      window.addEventListener("scroll", ready, { capture: true, passive: true });
      window.addEventListener("wheel", ready, { capture: true, passive: true });
      window.addEventListener("touchmove", ready, { capture: true, passive: true });
    }

    function armFallback() {
      setTimeout(ready, 1000);
    }

    if (document.readyState === "complete") armFallback();
    else window.addEventListener("load", armFallback, { once: true });
  })();

  if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") return;
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

  var stage = section.querySelector(".about-scene__stage");
  var hero = section.querySelector(".about-scene__hero");
  var frame = section.querySelector(".js-about-scene-frame");
  var frameVisual = frame ? frame.querySelector("picture, video") : null;
  var inner = section.querySelector(".js-about-scene-inner");
  var target = section.querySelector(".js-about-scene-target");
  var lift = section.querySelector(".js-about-scene-lift");
  var heroTop = section.querySelector(".js-about-scene-top");
  var photos = Array.prototype.slice.call(section.querySelectorAll(".about-scene__photo"));
  if (!stage || !hero || !frame || !target) return;

  gsap.registerPlugin(ScrollTrigger);
  section.classList.add("is-scene");

  // Start = the hero's content box (padding comes back from getComputedStyle in
  // px, whatever unit the token was written in).
  function startBox() {
    var s = stage.getBoundingClientRect();
    var h = hero.getBoundingClientRect();
    var cs = getComputedStyle(hero);
    var pl = parseFloat(cs.paddingLeft) || 0;
    var pr = parseFloat(cs.paddingRight) || 0;
    var pt = parseFloat(cs.paddingTop) || 0;
    var pb = parseFloat(cs.paddingBottom) || 0;

    return {
      left: h.left - s.left + pl,
      top: h.top - s.top + pt,
      width: h.width - pl - pr,
      height: h.height - pt - pb,
    };
  }

  // End = where the target sits once the composition has finished rising, so
  // the current shift of .about-scene__inner is taken back out of the reading —
  // both tweens land on the same frame, and the banner lands exactly on it.
  function endBox() {
    var s = stage.getBoundingClientRect();
    var t = target.getBoundingClientRect();
    var shift = inner ? parseFloat(gsap.getProperty(inner, "y")) || 0 : 0;

    return { left: t.left - s.left, top: t.top - s.top - shift, width: t.width, height: t.height };
  }

  // The stage lets content paint past its bottom edge (overflow-clip-margin),
  // so the start has to clear that too or a card would peek in under the hero.
  function clipMargin() {
    var value = parseFloat(getComputedStyle(stage).overflowClipMargin);

    return isNaN(value) ? 0 : value;
  }

  // ── Photo parallax figures (shared with the tweens below) ─────────
  // Each photo crosses y = 0 exactly when the scene settles, so it starts this
  // far on the other side. The composition has to clear the largest of those
  // leads as well, otherwise the photo pulled up hardest peeks in at the top.
  var PHOTO_TRAVEL = 68;

  // Phones lay the photos out as two tidy rows inside the frame (_about.scss),
  // so they get half the drift — the full 68px would push the bottom row out of
  // the stage and cut it, which is exactly what that layout is there to avoid.
  function photoTravel() {
    return isMobileScene() ? PHOTO_TRAVEL * 0.4 : PHOTO_TRAVEL;
  }

  function settleProgress() {
    var vh = window.innerHeight;
    var h = section.offsetHeight;
    var settleScroll = (h - vh) * (MOVE_AT + MOVE_FOR);

    return (settleScroll + vh) / (h + vh);
  }

  function photoLead(speed) {
    var p = settleProgress();

    return photoTravel() * speed * (p / (1 - p));
  }

  function maxPhotoLead() {
    return photos.reduce(function (max, photo) {
      return Math.max(max, photoLead(parseFloat(photo.getAttribute("data-scene-speed")) || 1));
    }, 0);
  }

  // How far below its resting place the composition starts: its top edge is put
  // on the bottom of the stage's clip box, with room for the photo leads.
  function innerShift() {
    var s = stage.getBoundingClientRect();
    var r = inner.getBoundingClientRect();
    var current = parseFloat(gsap.getProperty(inner, "y")) || 0;

    return s.bottom + clipMargin() + maxPhotoLead() - (r.top - current);
  }

  var tl = gsap.timeline({
    defaults: { ease: "none" },
    scrollTrigger: {
      trigger: section,
      start: "top top",
      end: "bottom bottom",
      scrub: 0.6,
      invalidateOnRefresh: true,
    },
  });

  // The whole move is one snap rather than a linear crawl: nothing happens for
  // the first quarter of the scroll, then the banner collapses decisively and
  // eases into its place. The composition rises on the exact same curve, so the
  // two stay locked to each other all the way, not only at the end.
  var MOVE_AT = 0.28;
  var MOVE_FOR = 0.42;
  var MOVE_EASE = "power3.inOut";
  var FRAME_VISUAL_START_SCALE = 1.16;

  // Fixes the timeline at 1.0 long, so the positions above are read as a share
  // of the scroll. Without it the timeline would only be as long as its last
  // tween and everything would stretch to fill the scroll — the hold included.
  tl.to({}, { duration: 1 }, 0);

  function isMobileScene() {
    return window.matchMedia("(max-width: 767.98px)").matches;
  }

  // The hold: the hero stays full-screen while its copy accelerates away.
  // Mobile keeps the same immediate start as desktop, but spreads the travel
  // over more scroll so each wheel/touch step moves the title less.
  if (lift) {
    tl.to(
      lift,
      {
        y: function () { return -stage.getBoundingClientRect().height; },
        ease: "power2.in",
        duration: isMobileScene() ? 0.42 : 0.32,
      },
      0
    );
  }

  if (heroTop) {
    tl.to(
      heroTop,
      {
        y: function () { return -Math.round(stage.getBoundingClientRect().height * 0.34); },
        autoAlpha: 0,
        ease: MOVE_EASE,
        duration: isMobileScene() ? 0.34 : 0.3,
        force3D: false,
      },
      MOVE_AT
    );
  }

  if (inner) {
    tl.fromTo(
      inner,
      { y: function () { return Math.round(innerShift()); } },
      {
        y: 0,
        ease: MOVE_EASE,
        duration: MOVE_FOR,
        // Keep copy on the main thread — force3D layers blur type on mobile.
        force3D: false,
      },
      MOVE_AT
    );
  }

  tl.fromTo(
    frame,
    {
      left: function () { return startBox().left; },
      top: function () { return startBox().top; },
      width: function () { return startBox().width; },
      height: function () { return startBox().height; },
    },
    {
      left: function () { return endBox().left; },
      top: function () { return endBox().top; },
      width: function () { return endBox().width; },
      height: function () { return endBox().height; },
      ease: MOVE_EASE,
      duration: MOVE_FOR,
    },
    MOVE_AT
  );

  if (frameVisual) {
    tl.fromTo(
      frameVisual,
      {
        scale: FRAME_VISUAL_START_SCALE,
      },
      {
        scale: 1,
        transformOrigin: "center center",
        ease: MOVE_EASE,
        duration: MOVE_FOR,
      },
      MOVE_AT
    );
  }

  // Photo parallax — same shape as the contact collage (app-contact-parallax):
  // one tween per photo on its OWN trigger spanning the whole section, not the
  // scene timeline. That way they keep drifting while the block scrolls out and
  // its elements are still on screen, instead of freezing when the scene ends.
  //
  // The travel is much longer than the collage's because this trigger spans a
  // 4-screen-tall section — the same offsets over that distance crawl. To keep
  // the composition true to the design where it matters, each photo crosses
  // y = 0 exactly at the moment the scene settles: everything before that is
  // the photos catching up to the rising composition, everything after is the
  // drift as the block leaves.
  photos.forEach(function (photo, index) {
    var speed = parseFloat(photo.getAttribute("data-scene-speed")) || 1;
    // alternate the direction so neighbouring photos pull apart
    var dir = index % 2 ? 1 : -1;

    gsap.fromTo(
      photo,
      {
        y: function () {
          return -dir * photoLead(speed);
        },
      },
      {
        y: function () {
          return dir * photoTravel() * speed;
        },
        ease: "none",
        immediateRender: false,
        scrollTrigger: {
          trigger: section,
          start: "top bottom",
          end: "bottom top",
          scrub: 0.9 + index * 0.07,
          invalidateOnRefresh: true,
        },
      }
    );
  });
});
