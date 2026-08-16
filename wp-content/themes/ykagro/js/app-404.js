/* global gsap */
// 404 motion: line stroke-draw + rooster run-in from left, then idle
$(function () {
  "use strict";

  if (typeof gsap === "undefined") return;
  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  // Match SCSS maxMedia(lg) — no sack idle below this
  var isMobileLayout = window.matchMedia("(max-width: 991px)").matches;

  // ── Both decorative lines ──────────────────────────────────────────
  // Paths are much taller than the viewBox; only a mid-slice is on screen.
  // Measure that slice per path — a shared 0.4 offset only fit the left line.
  document.querySelectorAll(".error-404__line path").forEach(function (path, i) {
    if (typeof path.getTotalLength !== "function") return;
    var len = path.getTotalLength();
    if (!len || !isFinite(len)) return;

    var svg = path.ownerSVGElement;
    var vb = svg && svg.viewBox && svg.viewBox.baseVal;
    var y0 = vb ? vb.y : 0;
    var y1 = vb ? vb.y + vb.height : 1744;

    var visStart = null;
    var visEnd = null;
    var steps = 240;
    for (var s = 0; s <= steps; s++) {
      var pt = path.getPointAtLength((len * s) / steps);
      if (pt.y >= y0 && pt.y <= y1) {
        if (visStart === null) visStart = (len * s) / steps;
        visEnd = (len * s) / steps;
      }
    }

    // Fallback if sampling misses (shouldn’t): previous mid-path guess
    if (visStart === null || visEnd === null || visEnd <= visStart) {
      visStart = len * 0.4;
      visEnd = len;
    }

    // dasharray=len: offset len → hidden, offset 0 → full. Reveal the crop.
    var pad = Math.max(len * 0.02, 40);
    var from = Math.min(len, len - visStart + pad);
    var to = Math.max(0, len - visEnd - pad);

    // opacity:1 reveals the path now that the dash offset hides it (CSS pre-hid
    // it via html.ta-js … { opacity:0 } to avoid any pre-draw flash).
    gsap.set(path, { strokeDasharray: len, strokeDashoffset: from, opacity: 1 });

    if (reduce) {
      gsap.set(path, { strokeDashoffset: to });
      return;
    }

    gsap.to(path, {
      strokeDashoffset: to,
      duration: 2.8,
      delay: 0.12 + i * 0.4,
      ease: "power2.out",
    });
  });

  if (reduce) return;

  var root = document.querySelector(".error-404__media");
  if (!root) return;

  var roosterWrap = root.querySelector(".error-404__rooster");
  var rooster = root.querySelector("#error-404-illu__rooster");
  var bag = root.querySelector("#error-404-illu__bag");
  var legR = root.querySelector("#error-404-illu__leg-r");
  var legL = root.querySelector("#error-404-illu__leg-l");
  var shadow = root.querySelector("#error-404-illu__shadow");
  var tail = root.querySelector("#error-404-illu__tail");
  var sack = root.querySelector(".error-404__sack");
  if (!rooster || !roosterWrap) return;

  gsap.set(rooster, { transformOrigin: "55% 85%", force3D: true });
  if (bag) gsap.set(bag, { transformOrigin: "50% 90%", force3D: true });
  // Hips — swing each leg from the top
  if (legR) gsap.set(legR, { transformOrigin: "50% 8%", force3D: true });
  if (legL) gsap.set(legL, { transformOrigin: "50% 8%", force3D: true });
  if (shadow) gsap.set(shadow, { transformOrigin: "50% 50%", force3D: true });
  if (tail) gsap.set(tail, { transformOrigin: "88% 70%", force3D: true });
  // Whole sack + grain as one piece (desktop idle only)
  if (sack && !isMobileLayout) {
    gsap.set(sack, { transformOrigin: "78% 92%", force3D: true });
  }

  function startIdle() {
    gsap.to(rooster, {
      y: -4,
      rotation: -1.5,
      duration: 2.4,
      ease: "sine.inOut",
      yoyo: true,
      repeat: -1,
    });

    // No bag/leg idle — legs stay planted after the run

    if (shadow) {
      gsap.to(shadow, {
        scaleX: 0.94,
        scaleY: 0.97,
        opacity: 0.88,
        duration: 2.4,
        ease: "sine.inOut",
        yoyo: true,
        repeat: -1,
      });
    }

    if (tail) {
      gsap.to(tail, {
        rotation: -6,
        duration: 2.1,
        ease: "sine.inOut",
        yoyo: true,
        repeat: -1,
      });
    }

    if (sack && !isMobileLayout) {
      gsap.to(sack, {
        rotation: 0.45,
        scaleX: 1.006,
        scaleY: 0.994,
        duration: 3.8,
        ease: "sine.inOut",
        yoyo: true,
        repeat: -1,
      });
    }

    startBlinks();
  }

  // ── Entrance: run in from the left, then settle into idle ──────────
  var runFrom = -(window.innerWidth + roosterWrap.offsetWidth * 0.35);
  var runDur = 1.5;
  // Slow strides, but gait must end before the ease-out “already there” coast
  var stride = 0.2;
  var gaitEnd = runDur * 0.72;
  var strideSteps = Math.max(4, Math.floor(gaitEnd / stride)) + 2;
  if (strideSteps % 2 !== 0) strideSteps += 1;

  gsap.set(roosterWrap, { x: runFrom, force3D: true, visibility: "visible" });
  roosterWrap.classList.add("is-run-ready");
  // Above text / header while running (media stacking context)
  gsap.set(root, { zIndex: 20 });
  // Shadow starts tiny under a running bird, grows as he arrives
  if (shadow) {
    gsap.set(shadow, {
      scaleX: 0.28,
      scaleY: 0.34,
      opacity: 0.45,
    });
  }

  var enter = gsap.timeline({
    delay: 0.2,
    onComplete: function () {
      gsap.set(rooster, { y: 0, rotation: 0 });
      if (bag) gsap.set(bag, { rotation: 0, y: 0 });
      if (legR) gsap.set(legR, { rotation: 0 });
      if (legL) gsap.set(legL, { rotation: 0 });
      if (shadow) gsap.set(shadow, { scaleX: 1, scaleY: 1, opacity: 1 });
      gsap.set(root, { zIndex: 0 });
      startIdle();
    },
  });

  // Dash across — power1 keeps motion visible longer (less early “parked” look)
  enter.to(
    roosterWrap,
    {
      x: 0,
      duration: runDur,
      ease: "power1.out",
    },
    0
  );

  // Shadow rides along (same SVG) — grow small → normal over the run
  if (shadow) {
    enter.to(
      shadow,
      {
        scaleX: 1,
        scaleY: 1,
        duration: runDur,
        ease: "power1.out",
      },
      0
    );
    // Dimmer when airborne (hop pulse) — only during gait
    for (var si = 0; si < strideSteps; si++) {
      enter.to(
        shadow,
        {
          opacity: si % 2 === 0 ? 0.28 : 0.45,
          duration: stride,
          ease: "sine.inOut",
        },
        si * stride
      );
    }
    enter.to(
      shadow,
      {
        opacity: 1,
        duration: 0.22,
        ease: "power2.out",
      },
      strideSteps * stride
    );
  }

  // Body hops during gait only, then hold planted
  for (var hi = 0; hi < strideSteps - 1; hi++) {
    enter.to(
      rooster,
      {
        y: hi % 2 === 0 ? -16 : 0,
        duration: stride,
        ease: "sine.inOut",
      },
      hi * stride
    );
  }
  enter.to(
    rooster,
    { y: 0, duration: stride, ease: "power2.out" },
    (strideSteps - 1) * stride
  );

  // Slight lean while running, ease upright at the end
  enter.fromTo(
    rooster,
    { rotation: -8 },
    { rotation: 0, duration: runDur, ease: "power1.out" },
    0
  );

  // Alternating legs — last step plants at 0; no steps after arrival coast
  if (legR && legL) {
    gsap.set([legR, legL], { rotation: 0 });
    var plantAt = (strideSteps - 1) * stride;
    for (var li = 0; li < strideSteps - 1; li++) {
      var forward = li % 2 === 0;
      enter.to(
        legR,
        {
          rotation: forward ? 28 : -28,
          duration: stride,
          ease: "sine.inOut",
        },
        li * stride
      );
      enter.to(
        legL,
        {
          rotation: forward ? -28 : 28,
          duration: stride,
          ease: "sine.inOut",
        },
        li * stride
      );
    }
    enter.to(legR, { rotation: 0, duration: stride, ease: "power2.out" }, plantAt);
    enter.to(legL, { rotation: 0, duration: stride, ease: "power2.out" }, plantAt);
  } else if (bag) {
    for (var bi = 0; bi < strideSteps - 1; bi++) {
      enter.to(
        bag,
        {
          rotation: bi % 2 === 0 ? 10 : -10,
          y: bi % 2 === 0 ? -3 : 0,
          duration: stride,
          ease: "sine.inOut",
        },
        bi * stride
      );
    }
    enter.to(
      bag,
      { rotation: 0, y: 0, duration: stride, ease: "power2.out" },
      (strideSteps - 1) * stride
    );
  }

  // Soft settle bounce on landing
  enter.to(
    roosterWrap,
    {
      y: -10,
      duration: 0.18,
      ease: "power2.out",
      yoyo: true,
      repeat: 1,
    },
    runDur - 0.2
  );

  // Eyes do blink (nictitating membrane + eyelid) — soft cartoon blink
  var eyes = [
    root.querySelector("#error-404-illu__eye-r"),
    root.querySelector("#error-404-illu__eye-l"),
  ].filter(Boolean);

  function startBlinks() {
    if (!eyes.length) return;

    gsap.set(eyes, { transformOrigin: "50% 50%", force3D: true });

    function scheduleBlink() {
      gsap.delayedCall(2.2 + Math.random() * 3.2, function () {
        var tl = gsap.timeline({
          onComplete: scheduleBlink,
        });
        tl.to(eyes, {
          scaleY: 0.08,
          duration: 0.055,
          ease: "power2.in",
          stagger: 0.02,
        }).to(eyes, {
          scaleY: 1,
          duration: 0.09,
          ease: "power2.out",
          stagger: 0.02,
        });

        // Occasional double-blink
        if (Math.random() < 0.28) {
          tl.to(eyes, {
            scaleY: 0.08,
            duration: 0.05,
            ease: "power2.in",
            stagger: 0.015,
            delay: 0.12,
          }).to(eyes, {
            scaleY: 1,
            duration: 0.08,
            ease: "power2.out",
            stagger: 0.015,
          });
        }
      });
    }

    scheduleBlink();
  }
});
