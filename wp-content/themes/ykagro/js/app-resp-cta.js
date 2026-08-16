/* global gsap, ScrollTrigger */
// Responsibility CTA hen(s). Each .resp-cta panel gets: a pop-in on scroll, a
// "walk in place" waddle (foot-to-foot rock + step bob), a beating heart and an
// occasional hop. Panels marked .resp-cta--anim use the split-wing artwork
// (cta-hen-anim.svg) and additionally flap their wings. All gated by
// reduced-motion.
$(function () {
  "use strict";

  if (typeof gsap === "undefined") return;
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

  function initCta(section) {
    var art = section.querySelector(".resp-cta__art");
    if (!art) return;
    var hen = art.querySelector("svg");
    if (!hen) return;

    // Heart path (red fill) — matched case-insensitively so re-optimisation
    // can't break it.
    var heart = null;
    Array.prototype.forEach.call(art.querySelectorAll("path"), function (p) {
      if ((p.getAttribute("fill") || "").toLowerCase() === "#db0000") heart = p;
    });

    var wingL = art.querySelector("#cta-hen-wing-l");
    var wingR = art.querySelector("#cta-hen-wing-r");

    gsap.set(art, { transformOrigin: "50% 100%", force3D: true });
    // Pivot on the feet so the rock reads like weight shifting between steps.
    gsap.set(hen, { transformOrigin: "50% 100%", force3D: true });
    // Pre-hide until entrance (hen is below the fold, so no flash on load).
    gsap.set(art, { autoAlpha: 0, scale: 0.6, y: 60, rotation: -12 });

    function startIdle() {
      // "walk in place": foot-to-foot rock + step bob (double rate → up on each step)
      gsap.to(hen, { rotation: 2.4, duration: 0.5, ease: "sine.inOut", yoyo: true, repeat: -1 });
      gsap.to(hen, { y: -5, duration: 0.25, ease: "sine.out", yoyo: true, repeat: -1 });

      // wings flap (only the split-wing artwork has these groups)
      if (wingL) {
        gsap.set(wingL, { transformOrigin: "98% 28%" }); // shoulder joint
        gsap.to(wingL, { rotation: -22, duration: 0.42, ease: "sine.inOut", yoyo: true, repeat: -1 });
      }
      if (wingR) {
        gsap.set(wingR, { transformOrigin: "2% 12%" });
        gsap.to(wingR, { rotation: 22, duration: 0.42, ease: "sine.inOut", yoyo: true, repeat: -1 });
      }

      // heartbeat — quick double thump, then rest
      if (heart) {
        gsap.set(heart, { transformOrigin: "50% 50%" });
        gsap
          .timeline({ repeat: -1, repeatDelay: 0.9 })
          .to(heart, { scale: 1.22, duration: 0.16, ease: "power2.out" })
          .to(heart, { scale: 1, duration: 0.16, ease: "power2.in" })
          .to(heart, { scale: 1.16, duration: 0.14, ease: "power2.out" })
          .to(heart, { scale: 1, duration: 0.2, ease: "power2.in" });
      }

      // occasional playful hop on the wrapper (composes with the svg motion)
      (function scheduleHop() {
        gsap.delayedCall(3.5 + Math.random() * 3, function () {
          gsap
            .timeline({ onComplete: scheduleHop })
            .to(art, { y: -30, rotation: 3, duration: 0.22, ease: "power2.out" })
            .to(art, { y: 0, rotation: 0, duration: 0.55, ease: "bounce.out" });
        });
      })();
    }

    function playEntrance() {
      gsap.to(art, {
        autoAlpha: 1,
        scale: 1,
        y: 0,
        rotation: 0,
        duration: 0.9,
        ease: "back.out(1.7)",
        onComplete: startIdle,
      });
    }

    if (typeof ScrollTrigger !== "undefined") {
      ScrollTrigger.create({ trigger: section, start: "top 85%", once: true, onEnter: playEntrance });
    } else {
      playEntrance();
    }
  }

  Array.prototype.forEach.call(document.querySelectorAll(".resp-cta"), initCta);
});
