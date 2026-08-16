/* global gsap, ScrollTrigger */
// Decorative scroll-drawn line — works on any page that includes
// inc/_scroll-line.php (.js-scroll-line + .js-scroll-line-measure).
//
// Short pages: keep the path’s natural scale and crop the SVG just below
// the footer start (no giant paint layer, no vertical squash).
// Tall pages: stretch to fill main as before.
$(function () {
  if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") return;

  var line = document.querySelector(".js-scroll-line");
  var lineMeasure = document.querySelector(".js-scroll-line-measure");
  if (!line || !lineMeasure || typeof lineMeasure.getTotalLength !== "function") return;

  gsap.registerPlugin(ScrollTrigger);

  var len = lineMeasure.getTotalLength();
  if (!len || !isFinite(len)) return;

  var lineSvg = line.closest("svg");
  var lineWrap = lineSvg && lineSvg.closest(".scroll-line");
  if (!lineSvg || !lineWrap) return;

  // Hide immediately (before sampling) so the full path never flashes
  gsap.set(line, { strokeDasharray: len, strokeDashoffset: len });

  var VB_W = 1838;
  var VB_H_FULL = 12153;
  var FOOTER_BLEED = 120; // px past footer top — slight underlap
  var vbH = VB_H_FULL;
  var maxDrawLen = len;

  // Sample once against the full path (length ↔ monotonic max-y)
  var SAMPLES = 240;
  var ys = new Array(SAMPLES + 1);
  var maxY = -Infinity;
  for (var i = 0; i <= SAMPLES; i++) {
    var pt = lineMeasure.getPointAtLength((len * i) / SAMPLES);
    maxY = Math.max(maxY, pt.y);
    ys[i] = maxY;
  }

  function lengthAtY(targetY) {
    if (targetY <= ys[0]) return 0;
    if (targetY >= ys[SAMPLES]) return len;
    var lo = 0, hi = SAMPLES;
    while (hi - lo > 1) {
      var mid = (lo + hi) >> 1;
      if (ys[mid] <= targetY) lo = mid; else hi = mid;
    }
    var span = ys[hi] - ys[lo];
    var t = span > 0 ? (targetY - ys[lo]) / span : 0;
    return ((lo + t) / SAMPLES) * len;
  }

  // Resolve CSS lengths (px / rem / em) to pixels. parseFloat("-10rem") === -10
  // would silently treat rem as px and make offset tweaks look like they do nothing.
  function cssLengthToPx(value, el) {
    var raw = String(value || "").trim();
    if (!raw) return 0;
    if (raw.endsWith("px")) return parseFloat(raw) || 0;

    var probe = document.createElement("div");
    probe.style.cssText = "position:absolute;visibility:hidden;pointer-events:none;margin-top:" + raw;
    (el || document.body).appendChild(probe);
    var px = parseFloat(getComputedStyle(probe).marginTop) || 0;
    probe.parentNode.removeChild(probe);
    return px;
  }

  function syncStartFromAnchor() {
    var main = lineWrap.closest("main");
    var anchor = document.querySelector("[data-scroll-line-after]");
    if (!main || !anchor) return;

    var mainRect = main.getBoundingClientRect();
    var anchorRect = anchor.getBoundingClientRect();
    // Start at the bottom of the anchor block, then apply an optional offset
    // (CSS --scroll-line-after-offset). Negative = higher into the block.
    var offset = cssLengthToPx(
      getComputedStyle(main).getPropertyValue("--scroll-line-after-offset"),
      main
    );
    if (!offset) {
      offset = parseFloat(anchor.getAttribute("data-scroll-line-offset") || "0") || 0;
    }

    var start = Math.max(0, Math.round(anchorRect.bottom - mainRect.top + offset));
    main.style.setProperty("--scroll-line-start", start + "px");
  }

  function sizeToPage() {
    syncStartFromAnchor();

    var footer = document.querySelector(".footer");
    var width = lineSvg.getBoundingClientRect().width;
    if (!width) {
      width = parseFloat(getComputedStyle(lineSvg).width) || 0;
    }
    if (!width) return;

    var naturalH = width * (VB_H_FULL / VB_W);
    var wrapRect = lineWrap.getBoundingClientRect();
    var absTop = wrapRect.top + window.pageYOffset;
    var footerTop = footer
      ? footer.getBoundingClientRect().top + window.pageYOffset
      : absTop + naturalH;
    var available = Math.max(0, footerTop - absTop + FOOTER_BLEED);

    if (available > 0 && available < naturalH - 1) {
      // Short page — natural scale, crop just under footer
      var cropH = available;
      vbH = VB_W * (cropH / width);
      maxDrawLen = lengthAtY(vbH);

      lineWrap.style.bottom = "auto";
      lineWrap.style.height = cropH + "px";
      lineSvg.style.height = cropH + "px";
      lineSvg.style.minHeight = "0";
      lineSvg.setAttribute("viewBox", "0 0 " + VB_W + " " + vbH.toFixed(2));
      lineSvg.setAttribute("preserveAspectRatio", "none");
    } else {
      // Tall page — stretch with main
      vbH = VB_H_FULL;
      maxDrawLen = len;

      lineWrap.style.bottom = "";
      lineWrap.style.height = "";
      lineSvg.style.height = "";
      lineSvg.style.minHeight = "";
      lineSvg.setAttribute("viewBox", "0 0 " + VB_W + " " + VB_H_FULL);
      lineSvg.setAttribute("preserveAspectRatio", "none");
    }
  }

  var ANCHOR = 0.38;
  var SMOOTH = 2.4;
  var targetLen = 0;
  var drawnLen = 0;
  var lineDirty = true;
  var ticking = false;
  var measureRaf = 0;
  var lineInView = true;
  var setDashOffset = gsap.quickSetter(line, "strokeDashoffset");

  function measureTarget() {
    measureRaf = 0;
    if (!lineInView) return;
    var rect = lineSvg.getBoundingClientRect();
    if (!rect.height || !vbH) return;
    var scaleY = rect.height / vbH;
    var targetY = (window.innerHeight * ANCHOR - rect.top) / scaleY;
    var next = Math.min(maxDrawLen, lengthAtY(targetY));
    if (Math.abs(next - targetLen) < 0.5 && !lineDirty) return;
    targetLen = next;
    lineDirty = true;
    ensureTicker();
  }

  function updateTarget() {
    if (!lineInView || measureRaf) return;
    measureRaf = window.requestAnimationFrame(measureTarget);
  }

  function onTick(time, deltaMS) {
    if (!lineInView) {
      gsap.ticker.remove(onTick);
      ticking = false;
      return;
    }
    if (!lineDirty && Math.abs(targetLen - drawnLen) < 0.5) {
      gsap.ticker.remove(onTick);
      ticking = false;
      return;
    }

    var diff = targetLen - drawnLen;
    if (Math.abs(diff) < 0.5) {
      if (drawnLen !== targetLen) {
        drawnLen = targetLen;
        setDashOffset(len - drawnLen);
      }
      lineDirty = false;
      gsap.ticker.remove(onTick);
      ticking = false;
      return;
    }
    var t = 1 - Math.exp(-(deltaMS / 1000) * SMOOTH);
    drawnLen += diff * t;
    setDashOffset(len - drawnLen);
  }

  function ensureTicker() {
    if (!lineInView || ticking) return;
    ticking = true;
    gsap.ticker.add(onTick);
  }

  function refreshSize() {
    sizeToPage();
    updateTarget();
  }

  sizeToPage();

  ScrollTrigger.create({
    start: 0,
    end: "max",
    onUpdate: updateTarget,
    onRefresh: refreshSize,
  });
  updateTarget();

  // Pause draw work while the line box is off-screen (e.g. past footer).
  if ("IntersectionObserver" in window) {
    var lineIO = new IntersectionObserver(
      function (entries) {
        lineInView = entries.some(function (entry) {
          return entry.isIntersecting;
        });
        if (lineInView) {
          updateTarget();
        } else if (ticking) {
          gsap.ticker.remove(onTick);
          ticking = false;
        }
      },
      { root: null, rootMargin: "15% 0px", threshold: 0 }
    );
    lineIO.observe(lineWrap);
  }

  var resizeTimer = null;
  window.addEventListener(
    "resize",
    function () {
      if (resizeTimer) window.clearTimeout(resizeTimer);
      resizeTimer = window.setTimeout(function () {
        resizeTimer = null;
        refreshSize();
        ScrollTrigger.refresh();
      }, 120);
    },
    { passive: true }
  );

  window.addEventListener("load", function () {
    refreshSize();
    ScrollTrigger.refresh();
  });
});
