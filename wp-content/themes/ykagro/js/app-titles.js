/* global gsap, ScrollTrigger, SplitType */
// Title reveal — Les Grandes Serres "great-title" style:
// lines as overflow masks, characters rise with skewY stagger.
// Candidates are pre-hidden by CSS (html.ta-js … :not(.ta-ready)); each gets
// .ta-ready here so nothing flashes. Opt out with data-title-anim="off".
$(function () {
  var canAnimate =
    typeof gsap !== "undefined" &&
    typeof ScrollTrigger !== "undefined" &&
    typeof SplitType !== "undefined" &&
    !window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  var titles = Array.prototype.slice.call(
    document.querySelectorAll("main h1, main h2")
  );

  function afterFontsReady(cb) {
    var done = false;
    function run() {
      if (done) return;
      done = true;
      requestAnimationFrame(function () {
        requestAnimationFrame(cb);
      });
    }
    if (document.fonts && document.fonts.ready) {
      document.fonts.ready.then(run, run);
    } else {
      run();
    }
  }

  function afterFirstPaint(cb) {
    requestAnimationFrame(function () {
      requestAnimationFrame(cb);
    });
  }

  // Above-the-fold heroes (home hero + inner-page hero title panel) play right
  // after first paint instead of waiting for a scroll trigger.
  function isHeroTitle(el) {
    return !!(el.closest(".home-hero") || el.closest(".page-hero") || el.closest(".about-scene__hero"));
  }

  function animateTitle(el) {
    var isHero = isHeroTitle(el);
    el.setAttribute("data-title-anim", "");
    ScrollTrigger.create({
      trigger: el,
      start: isHero ? "top 100%" : "top 80%",
      once: true,
      onEnter: function () {
        var split = new SplitType(el, {
          types: "lines,chars",
          lineClass: "ta-line",
          charClass: "ta-char",
        });
        if (!split.chars || !split.chars.length) {
          el.classList.add("ta-ready");
          el.removeAttribute("data-title-anim");
          return;
        }

        // Serres: TweenMax.set(chars, { y: 100, skewY: 45 }) then
        // staggerTo({ y: 0, skewY: 0 }, 0.4, Quint.easeOut, 0.015)
        gsap.set(split.chars, {
          yPercent: 110,
          skewY: 45,
          force3D: true,
        });
        el.classList.add("ta-ready");
        gsap.to(split.chars, {
          yPercent: 0,
          skewY: 0,
          duration: 0.4,
          ease: "power4.out",
          stagger: 0.015,
          delay: isHero ? 0.12 : 0,
          force3D: true,
          onComplete: function () {
            // Keep chars split — revert() snaps kerning back and looks like a jump.
            gsap.set(split.chars, { clearProps: "willChange" });
            el.removeAttribute("data-title-anim");
          },
        });
      },
    });
  }

  var heroAnimated = [];
  var animated = [];
  titles.forEach(function (el) {
    var excluded =
      !canAnimate ||
      el.closest(".home-statement") ||
      el.closest(".article-content") ||
      el.closest(".doc-page__content") || // WYSIWYG body headings stay static; the .doc-page__title h1 still animates
      el.closest(".article-author") ||
      el.closest(".article-toc") ||
      el.dataset.titleAnim === "off";
    if (excluded) {
      el.classList.add("ta-ready");
      return;
    }
    if (isHeroTitle(el)) {
      heroAnimated.push(el);
      return;
    }
    animated.push(el);
  });

  if (!canAnimate) return;

  afterFirstPaint(function () {
    heroAnimated.forEach(animateTitle);
  });

  afterFontsReady(function () {
    animated.forEach(animateTitle);
    if (ScrollTrigger.refresh) ScrollTrigger.refresh();
  });
});
