/* global gsap, ScrollTrigger */
// Contact collage photo parallax. Travels with the .home-contact component so
// it runs wherever the component is used (homepage + contacts page), without
// pulling in the whole homepage script. GSAP + ScrollTrigger load site-wide
// via inc/_bottom.php.
$(function () {
  if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") return;
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

  gsap.registerPlugin(ScrollTrigger);

  var offsets = {
    "home-contact__photo--tl": -74,
    "home-contact__photo--tc": 56,
    "home-contact__photo--r": -68,
    "home-contact__photo--bl": 86,
    "home-contact__photo--br": -58,
  };

  document.querySelectorAll(".home-contact").forEach(function (section) {
    // Guard against a double-init (e.g. a stale cached copy of this or an old
    // app-home.js still holding the parallax) — two scrub tweens on the same
    // photos fight and the effect looks stuck.
    if (section.dataset.contactParallax) return;
    section.dataset.contactParallax = "1";

    var photos = Array.prototype.slice.call(section.querySelectorAll(".home-contact__photo"));
    if (!photos.length) return;

    photos.forEach(function (photo, index) {
      var offset = 24;

      Object.keys(offsets).some(function (className) {
        if (photo.classList.contains(className)) {
          offset = offsets[className];
          return true;
        }
        return false;
      });

      gsap.fromTo(
        photo,
        { y: offset * -0.9 },
        {
          y: offset,
          ease: "none",
          immediateRender: false,
          scrollTrigger: {
            trigger: section,
            start: "top bottom",
            end: "bottom top",
            scrub: 1.1 + index * 0.08,
            invalidateOnRefresh: true,
            // Refresh AFTER pinned sections above (e.g. the homepage "Напрями"
            // pin) so this trigger's start/end account for the pin spacer.
            // Without this the motion plays out before the block is in view.
            refreshPriority: -1,
          },
        }
      );
    });
  });
});
