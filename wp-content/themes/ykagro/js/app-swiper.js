/* global Swiper */
// Swiper sliders (category products, gallery). Initialised per component.
$(function () {
  if (typeof Swiper === "undefined") return;

  // ── Category products slider ──────────────────────────────────────
  document.querySelectorAll(".js-products-slider").forEach(function (el) {
    new Swiper(el, {
      slidesPerView: 1.15,
      spaceBetween: 16,
      grabCursor: true,
      pagination: {
        el: el.querySelector(".swiper-pagination"),
        clickable: true,
      },
      breakpoints: {
        576: { slidesPerView: 2, spaceBetween: 24 },
        992: { slidesPerView: 3, spaceBetween: 32 },
        1200: { slidesPerView: 3, spaceBetween: 48 },
      },
    });
  });

  // ── Product detail gallery — one photo at a time, arrows over it ──
  document.querySelectorAll(".js-product-gallery").forEach(function (el) {
    var gallery = el.closest(".product-hero__gallery");

    new Swiper(el, {
      slidesPerView: 1,
      spaceBetween: 0,
      loop: true,
      grabCursor: true,
      watchOverflow: true,
      navigation: {
        prevEl: gallery ? gallery.querySelector(".js-product-gallery-prev") : null,
        nextEl: gallery ? gallery.querySelector(".js-product-gallery-next") : null,
      },
      keyboard: { enabled: true },
    });
  });

  // ── About team slider ─────────────────────────────────────────────
  document.querySelectorAll(".js-team-slider").forEach(function (el) {
    var section = el.closest(".about-team");

    function syncTeamLock(swiper) {
      if (!section || !swiper) return;
      section.classList.toggle("is-slider-locked", !!swiper.isLocked);
    }

    var swiper = new Swiper(el, {
      slidesPerView: 1.08,
      spaceBetween: 16,
      grabCursor: true,
      watchOverflow: true,
      navigation: {
        prevEl: section ? section.querySelector(".js-team-prev") : null,
        nextEl: section ? section.querySelector(".js-team-next") : null,
      },
      pagination: {
        el: el.querySelector(".swiper-pagination"),
        clickable: true,
      },
      breakpoints: {
        576: { slidesPerView: 1.5, spaceBetween: 20 },
        768: { slidesPerView: 2, spaceBetween: 20 },
        1200: { slidesPerView: 3, spaceBetween: 20 },
      },
      on: {
        init: function (sw) {
          syncTeamLock(sw);
        },
        lock: function (sw) {
          syncTeamLock(sw);
        },
        unlock: function (sw) {
          syncTeamLock(sw);
        },
        breakpoint: function (sw) {
          syncTeamLock(sw);
        },
        resize: function (sw) {
          syncTeamLock(sw);
        },
      },
    });

    syncTeamLock(swiper);
  });

  // ── About stats slider — mobile only ──────────────────────────────
  (function initAboutStatsSlider() {
    var els = Array.prototype.slice.call(document.querySelectorAll(".js-about-stats-slider"));
    if (!els.length) return;

    var mq = window.matchMedia("(max-width: 767.98px)");
    var instances = [];

    function sync() {
      els.forEach(function (el, i) {
        if (mq.matches && !instances[i]) {
          instances[i] = new Swiper(el, {
            slidesPerView: 1.08,
            spaceBetween: 16,
            grabCursor: true,
            watchOverflow: true,
            pagination: {
              el: el.querySelector(".swiper-pagination"),
              clickable: true,
            },
            breakpoints: {
              576: { slidesPerView: 1.35, spaceBetween: 20 },
            },
          });
        } else if (!mq.matches && instances[i]) {
          instances[i].destroy(true, true);
          instances[i] = null;
        }
      });
    }

    if (mq.addEventListener) {
      mq.addEventListener("change", sync);
    } else {
      mq.addListener(sync);
    }
    sync();
  })();

  // ── Articles slider — mobile only (desktop keeps the CSS grid) ─────
  (function initArticlesSlider() {
    var els = Array.prototype.slice.call(document.querySelectorAll(".js-articles-slider"));
    if (!els.length) return;

    var mq = window.matchMedia("(max-width: 991.98px)");
    var instances = [];

    function sync() {
      els.forEach(function (el, i) {
        if (mq.matches && !instances[i]) {
          instances[i] = new Swiper(el, {
            slidesPerView: 1.15,
            spaceBetween: 16,
            grabCursor: true,
            pagination: {
              el: el.querySelector(".swiper-pagination"),
              clickable: true,
            },
            breakpoints: {
              576: { slidesPerView: 2, spaceBetween: 24 }, 
            },
          });
        } else if (!mq.matches && instances[i]) {
          instances[i].destroy(true, true);
          instances[i] = null;
        }
      });
    }

    if (mq.addEventListener) {
      mq.addEventListener("change", sync);
    } else {
      mq.addListener(sync);
    }
    sync();
  })();

  // ── Benefits / comfort grids → sliders below 992 (same recipe as articles) ─
  (function initGridSliders() {
    var selectors = [".js-benefits-slider", ".js-comfort-slider"];
    var mq = window.matchMedia("(max-width: 991.98px)");

    selectors.forEach(function (sel) {
      var els = Array.prototype.slice.call(document.querySelectorAll(sel));
      if (!els.length) return;
      var instances = [];

      function sync() {
        els.forEach(function (el, i) {
          if (mq.matches && !instances[i]) {
            instances[i] = new Swiper(el, {
              slidesPerView: 1.15,
              spaceBetween: 16,
              grabCursor: true,
              pagination: {
                el: el.querySelector(".swiper-pagination"),
                clickable: true,
              },
              breakpoints: {
                576: { slidesPerView: 2, spaceBetween: 24 },
              },
            });
          } else if (!mq.matches && instances[i]) {
            instances[i].destroy(true, true);
            instances[i] = null;
          }
        });
      }

      if (mq.addEventListener) {
        mq.addEventListener("change", sync);
      } else {
        mq.addListener(sync);
      }
      sync();
    });
  })();

  // ── Directions slider (Figma 773:6195) ────────────────────────────
  // 2 tiles per view on desktop (same 860 + 40 rhythm as the listing grid),
  // 1.05 on phones so the next tile peeks.
  document.querySelectorAll(".js-directions-slider").forEach(function (el) {
    new Swiper(el, {
      slidesPerView: 1.05,
      spaceBetween: 16,
      grabCursor: true,
      pagination: {
        el: el.querySelector(".swiper-pagination"),
        clickable: true,
      },
      breakpoints: {
        768: { slidesPerView: 1.6, spaceBetween: 24 },
        // 2 visible, but it advances ONE tile at a time (so 5 tiles → 4 dots,
        // not the mockup's 3 — stepping single was the call)
        992: { slidesPerView: 2, spaceBetween: 40 },
      },
    });
  });

  // ── Gallery slider ────────────────────────────────────────────────
  document.querySelectorAll(".js-gallery-slider").forEach(function (el) {
    var section = el.closest(".js-gallery");
    var galleryFrame = 0;
    var leftMask = [
      [0, 0.0498], [0, 0.0216], [0.0208, 0], [0.0458, 0], [0.9574, 0.0729],
      [0.9812, 0.0738], [1, 0.0958], [1, 0.1226], [1, 0.8757],
      [1, 0.9025], [0.9812, 0.9245], [0.9574, 0.9255], [0.0458, 0.9983],
      [0.0208, 0.9993], [0, 0.9768], [0, 0.9485], [0, 0.0498],
    ];
    var rightMask = [
      [0, 0.1226], [0, 0.0958], [0.0188, 0.0738], [0.0426, 0.0729], [0.9542, 0],
      [0.9792, 0], [1, 0.0216], [1, 0.0498], [1, 0.9485],
      [1, 0.9768], [0.9792, 0.9993], [0.9542, 0.9983], [0.0426, 0.9255],
      [0.0188, 0.9245], [0, 0.9025], [0, 0.8757], [0, 0.1226],
    ];

    function clamp(min, max, value) {
      return Math.max(min, Math.min(max, value));
    }

    function lerp(a, b, t) {
      return a + (b - a) * t;
    }

    function smoothstep(t) {
      return t * t * (3 - 2 * t);
    }

    function mixMasks(from, to, t) {
      return from.map(function (point, index) {
        return [
          lerp(point[0], to[index][0], t),
          lerp(point[1], to[index][1], t),
        ];
      });
    }

    function maskPath(points, width, height) {
      function xy(index) {
        return (points[index][0] * width).toFixed(2) + " " + (points[index][1] * height).toFixed(2);
      }

      return "path('M " + xy(0) +
        " C " + xy(1) + ", " + xy(2) + ", " + xy(3) +
        " L " + xy(4) +
        " C " + xy(5) + ", " + xy(6) + ", " + xy(7) +
        " L " + xy(8) +
        " C " + xy(9) + ", " + xy(10) + ", " + xy(11) +
        " L " + xy(12) +
        " C " + xy(13) + ", " + xy(14) + ", " + xy(15) +
        " L " + xy(16) + " Z')";
    }

    function isGalleryMobile() {
      return window.matchMedia("(max-width: 767.98px)").matches;
    }

    function update(swiper) {
      var box = el.getBoundingClientRect();
      var mid = box.left + box.width / 2;
      var gap = galleryGap();
      var mobile = isGalleryMobile();
      swiper.slides.forEach(function (slide) {
        var media = slide.querySelector(".home-gallery__media");
        if (!media) return;

        var r = slide.getBoundingClientRect();
        var c = r.left + r.width / 2;
        var p = clamp(-2.2, 2.2, (c - mid) / Math.max(1, r.width + gap));
        var abs = Math.abs(p);

        // Mobile: no shaped clip-path on the media (it fights the static arch
        // clip-path the slide already gets from CSS). Keep it flat — CSS handles
        // the shape and corners. One centered card with peeks (see Swiper config).
        if (mobile) {
          media.style.transformOrigin = "50% 50%";
          media.style.transform = "scale(1)";
          media.style.opacity = "1";
          media.style.clipPath = "none";
          media.style.webkitClipPath = "none";
          slide.style.zIndex = String(Math.round(100 - abs * 20));
          slide.classList.toggle("home-gallery__slide--left", p < 0);
          slide.classList.toggle("home-gallery__slide--right", p >= 0);
          slide.classList.toggle("is-in", r.left < window.innerWidth && r.right > 0);
          return;
        }

        var edge = smoothstep(clamp(0, 1, (abs - 0.72) / 0.62));
        var direction = p < 0 ? -1 : 1;
        var rotate = -p * 5.5 * edge;
        var shift = direction * 11 * edge;
        var depth = -36 * edge;
        var scaleX = 1 + edge * 0.215;
        var scaleY = 1 + edge * 0.274;
        var opacity = clamp(0, 1, 1.18 - edge * 0.08);
        var origin = p < -0.08 ? "100% 50%" : p > 0.08 ? "0% 50%" : "50% 50%";

        media.style.transformOrigin = origin;
        media.style.transform = "perspective(1200px) translate3d(" +
          shift.toFixed(2) + "px, 0, " + depth.toFixed(2) + "px) rotateY(" +
          rotate.toFixed(2) + "deg) scale(" + scaleX.toFixed(3) + ", " + scaleY.toFixed(3) + ")";
        media.style.opacity = opacity.toFixed(3);
        slide.style.zIndex = String(Math.round(100 - abs * 20));
        var maskProgress = clamp(0, 1, p + 0.5);
        var path = maskPath(mixMasks(leftMask, rightMask, maskProgress), media.offsetWidth, media.offsetHeight);
        media.style.clipPath = path;
        media.style.webkitClipPath = path;
        slide.classList.toggle("home-gallery__slide--left", p < 0);
        slide.classList.toggle("home-gallery__slide--right", p >= 0);
        // reveal when on screen
        slide.classList.toggle("is-in", r.left < window.innerWidth && r.right > 0);
      });
    }

    function galleryGap() {
      if (window.matchMedia("(max-width: 767.98px)").matches) return 16;
      if (window.matchMedia("(max-width: 1199.98px)").matches) return 24;

      return clamp(16, 48, window.innerWidth * 0.025);
    }

    function galleryOffset() {
      // Mobile centers a single card (no manual offset — Swiper's centeredSlides
      // handles it), matching the reference. Desktop keeps the paired layout.
      if (isGalleryMobile()) return 0;

      var slide = el.querySelector(".home-gallery__slide");
      if (!slide) return 0;

      var slideW = slide.getBoundingClientRect().width;
      var pairOffset = (window.innerWidth - (slideW * 2 + galleryGap())) / 2;

      return Math.max(0, pairOffset);
    }

    function animateGeometry(swiper, duration) {
      if (galleryFrame) cancelAnimationFrame(galleryFrame);

      var start = performance.now();
      var limit = start + (duration || swiper.params.speed || 850) + 80;

      function tick(now) {
        update(swiper);

        if (now < limit || swiper.animating) {
          galleryFrame = requestAnimationFrame(tick);
        } else {
          galleryFrame = 0;
          update(swiper);
        }
      }

      galleryFrame = requestAnimationFrame(tick);
    }

    var sw = new Swiper(el, {
      slidesPerView: "auto",
      // Mobile: one centered card with peeks (reference). Desktop: paired layout.
      centeredSlides: isGalleryMobile(),
      loop: true,
      loopAdditionalSlides: 3,
      initialSlide: isGalleryMobile() ? 0 : 1,
      spaceBetween: galleryGap(),
      slidesOffsetBefore: galleryOffset(),
      slidesOffsetAfter: galleryOffset(),
      speed: 850,
      grabCursor: true,
      watchSlidesProgress: true,
      roundLengths: false,
      resistanceRatio: 0.72,
      slideToClickedSlide: true,
      navigation: section
        ? {
            prevEl: section.querySelector(".js-gallery-prev"),
            nextEl: section.querySelector(".js-gallery-next"),
          }
        : undefined,
      on: {
        init: function () {
          var s = this;
          requestAnimationFrame(function () { update(s); });
        },
        progress: update,
        setTranslate: update,
        slideChangeTransitionStart: function (swiper) {
          animateGeometry(swiper, swiper.params.speed);
        },
        slideChangeTransitionEnd: update,
        transitionEnd: update,
        resize: function (swiper) {
          swiper.params.spaceBetween = galleryGap();
          swiper.params.centeredSlides = isGalleryMobile();
          swiper.params.slidesOffsetBefore = galleryOffset();
          swiper.params.slidesOffsetAfter = galleryOffset();
          swiper.update();
          update(swiper);
        },
      },
    });
    void sw;
  });
});
