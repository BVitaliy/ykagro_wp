/* global Swiper */
// Production steps — fullpage slider. One wheel notch / one swipe moves exactly
// one panel, like a fullpage library, built on the Swiper the project already
// ships (vertical, slidesPerView 1, mousewheel).
//
// The section is `position: sticky`, so the pin itself is the browser's job and
// cannot stutter. What this file owns is *who gets the wheel*: Lenis drives the
// page, so while the section is engaged we hand the wheel to Swiper with
// `data-lenis-prevent` and take it back only for the gesture that leaves.
//
// Engagement is an explicit state, NOT something re-derived from the section's
// position on every event: a single leaked wheel event used to nudge the page a
// few pixels, which made a position-based check flip to "not covering" and threw
// the user out of the middle of the slider.
//
// Runs on every screen size — the user asked for the same pinned behaviour on
// phones, where the gesture is a swipe (Swiper `followFinger: false`, so one
// swipe always commits to exactly one panel).
(function () {
  var section = document.querySelector(".js-production-steps");
  if (!section) return;
  if (typeof Swiper === "undefined") return;

  var el = section.querySelector(".js-production-slider");
  var slides = section.querySelectorAll(".production-steps__panel");
  if (!el || slides.length < 2) return;

  var OUT = 0;
  var ENGAGED = 1;

  var swiper = null;
  var state = OUT;
  var animating = false; // a slide transition is still running
  var locked = false;
  var suppressUntil = 0; // don't re-capture right after an edge release
  var releaseDir = 0; // +1 left downwards, -1 left upwards
  var lastY = window.scrollY;

  // ── Lenis hand-off ─────────────────────────────────────────────────
  // `now` skips the one-tick delay: use it when re-taking the wheel while the
  // section is already framed, so that gesture moves a panel straight away.
  function setLock(on, now) {
    if (on === locked) return;
    locked = on;
    if (on) el.setAttribute("data-lenis-prevent", "");
    else el.removeAttribute("data-lenis-prevent");
    // touch: also stop the browser's own scrolling, otherwise a vertical drag
    // pans the page *and* the slider at the same time (Lenis is prevented here,
    // so it isn't calling preventDefault for us any more)
    section.classList.toggle("is-locked", on);

    if (!swiper) return;

    applyTouch(on);

    // Swiper only listens to the wheel while the section owns it, so scrolling
    // past on the way in or out never skips a panel. Enabling is deferred by a
    // tick by default: the gesture that pulls the section into frame should not
    // also advance it — it costs one gesture to take the wheel, then one each.
    if (!swiper.mousewheel) return;
    if (!on) {
      swiper.mousewheel.disable();
    } else if (now) {
      swiper.mousewheel.enable();
    } else {
      window.setTimeout(function () {
        if (locked && swiper && swiper.mousewheel) swiper.mousewheel.enable();
      }, 0);
    }
  }

  function engage(now) {
    if (state === ENGAGED) return;
    state = ENGAGED;
    suppressUntil = 0;
    releaseDir = 0;
    setLock(true, now);
  }

  function release(goingDown) {
    if (state !== ENGAGED) return;
    state = OUT;
    suppressUntil = Date.now() + 1000;
    releaseDir = goingDown ? 1 : -1;
    setLock(false);
    // Park at the end of the sticky range we're heading for, so the very next
    // scroll leaves the section instead of grinding through leftover range.
    // Invisible: the stage is pinned right across the range.
    park(goingDown ? 1 : 0);
  }

  // fraction 0 = section top at the viewport top, 1 = section bottom at the
  // viewport bottom. Everything in between renders identically.
  function park(fraction) {
    var lenis = window.lenis;
    if (!lenis || typeof lenis.scrollTo !== "function") return;
    var slack = section.offsetHeight - window.innerHeight;
    lenis.scrollTo(section, {
      offset: Math.max(0, slack) * fraction,
      immediate: true,
      force: true,
      lock: true,
    });
  }

  // Is the section filling the viewport right now?
  function covering(r, vh) {
    return r.top <= 0 && r.bottom >= vh;
  }

  // ── Input ──────────────────────────────────────────────────────────
  // Capture phase on window runs before Lenis' own listener, so the attribute is
  // already up to date by the time Lenis reads composedPath().
  //
  // While engaged this does nothing at all unless the gesture is the one that
  // leaves — that is what makes a hard flick on panel 2 or 3 stay put.
  function onGesture(goingDown) {
    if (!swiper) return;

    if (state === ENGAGED) {
      // the edge panel has to finish animating and be fully on screen first
      if (animating) return;
      if (goingDown && swiper.isEnd) release(true);
      else if (!goingDown && swiper.isBeginning) release(false);
      return;
    }

    if (state === OUT) recapture(goingDown);
  }

  // Leaving upwards from the first panel (or downwards from the last) drops the
  // lock while the section is still framed. Without this, a gesture straight
  // back into the slider would sail through it — the section still covers the
  // screen but `suppressUntil` is blocking the normal capture.
  function recapture(goingDown) {
    var r = section.getBoundingClientRect();
    if (!covering(r, window.innerHeight)) return;
    if (goingDown ? !swiper.isEnd : !swiper.isBeginning) engage(true);
  }

  function onWheel(e) {
    if (e.deltaY) onGesture(e.deltaY > 0);
  }

  // Touch has to be gated as well as the wheel: otherwise a swipe with the
  // finger resting on the section keeps driving the slider while the page
  // scrolls past it, and the panels look like they move on their own.
  //
  // `touching` matters because Swiper checks `allowTouchMove` in touchmove, not
  // touchstart — flipping it on mid-gesture would let the rest of the gesture
  // through, so the very swipe that engages the section would also advance it
  // and the first panel would never be seen.
  var touching = false;
  var touchY = 0;

  function applyTouch(on) {
    if (swiper) swiper.allowTouchMove = on && !touching;
  }

  function onTouchStart(e) {
    touching = true;
    touchY = e.touches && e.touches[0] ? e.touches[0].clientY : 0;
  }

  function onTouchMove(e) {
    if (!e.touches || !e.touches[0]) return;
    var dy = touchY - e.touches[0].clientY; // finger up == content down
    if (Math.abs(dy) < 4) return;
    onGesture(dy > 0);
  }

  function onTouchEnd() {
    touching = false;
    applyTouch(locked); // a fresh gesture may drive the slider again
  }

  function onScroll() {
    if (!swiper) return;

    var r = section.getBoundingClientRect();
    var vh = window.innerHeight;
    var y = window.scrollY;
    var dir = y - lastY;
    lastY = y;

    if (state === ENGAGED) {
      // Only ever leave on purpose. The one exception is a jump from outside
      // (in-page anchor, browser restore) that lands clear of the section.
      if (!covering(r, vh)) {
        state = OUT;
        setLock(false);
      } else if (Math.abs(r.top) > 1) {
        park(0); // keep the scroll parked at the top of the range
      }
      return;
    }

    if (Date.now() < suppressUntil) {
      // The suppression exists so leaving doesn't get undone straight away. The
      // moment the user turns round, it has served its purpose.
      if (releaseDir && dir && Math.sign(dir) !== releaseDir) suppressUntil = 0;
      else return;
    }

    // Take the wheel exactly when the section has arrived — no pulling the user
    // in from half a screen away. Parking to the top of the range is invisible
    // because the stage is already pinned and filling the viewport.
    if (dir && covering(r, vh)) {
      park(0);
      engage();
    }
  }

  // ── Build / destroy ────────────────────────────────────────────────
  function build() {
    if (swiper) return;

    // Parallax attrs must exist before Swiper init so the module picks them up.
    el.querySelectorAll(".production-steps__banner").forEach(function (banner) {
      banner.setAttribute("data-swiper-parallax-y", "28%");
    });

    swiper = new Swiper(el, {
      direction: "vertical",
      slidesPerView: 1,
      spaceBetween: 0,
      speed: 1000,
      grabCursor: false,
      // Image moves slower than the panel → parallax wipe between steps.
      parallax: true,
      mousewheel: {
        enabled: false, // setLock() turns it on once the section is engaged
        forceToAxis: true,
        thresholdDelta: 8,
        thresholdTime: 150,
      },
      keyboard: { enabled: true, onlyInViewport: true },
      // One swipe = one panel: the slide only commits on release, so a drag can
      // never land between two panels. Swiper's default asks a slow swipe to
      // cover half the panel before it counts — far too much for a full-screen
      // slide, so any deliberate 15% drag commits.
      followFinger: false,
      threshold: 6,
      longSwipesRatio: 0.15,
      longSwipesMs: 200,
      resistanceRatio: 0,
      allowTouchMove: false, // setLock() turns it on once the section is engaged
      a11y: {
        prevSlideMessage: "Попередній етап",
        nextSlideMessage: "Наступний етап",
      },
      on: {
        transitionStart: function () {
          animating = true;
        },
        transitionEnd: function () {
          animating = false;
        },
      },
    });

    section.classList.add("is-slider");

    window.addEventListener("wheel", onWheel, { capture: true, passive: true });
    window.addEventListener("touchstart", onTouchStart, { capture: true, passive: true });
    window.addEventListener("touchmove", onTouchMove, { capture: true, passive: true });
    window.addEventListener("touchend", onTouchEnd, { capture: true, passive: true });
    window.addEventListener("touchcancel", onTouchEnd, { capture: true, passive: true });
    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll);
    onScroll();
  }

  build();
})();
