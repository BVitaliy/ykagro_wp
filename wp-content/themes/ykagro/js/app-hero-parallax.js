/* global gsap, ScrollTrigger */
// Inner-page hero banner parallax. The banner media is already taller than its
// frame in CSS (_page-hero.scss, ±--hero-parallax-room) — this only drifts it
// down while the hero scrolls away, so the photo/video lags behind the page.
// Nothing here changes layout, so the banner never re-crops after load.
// Travels with the .page-hero component — include it on any page that renders
// one. GSAP + ScrollTrigger load site-wide via inc/_bottom.php.
//
// The about page hero is excluded: its banner is already driven by the
// about-scene scrub (app-about.js) and a second transform would fight it.
$(function () {
  if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") return;
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

  gsap.registerPlugin(ScrollTrigger);

  document.querySelectorAll(".page-hero").forEach(function (hero) {
    if (hero.classList.contains("about-scene__hero")) return;
    if (hero.dataset.heroParallax) return;

    var banner = hero.querySelector(":scope > .banner");
    if (!banner) return;

    var media = banner.querySelector(":scope > picture") || banner.querySelector(":scope > video");
    if (!media) return;

    hero.dataset.heroParallax = "1";

    // The travel is whatever CSS actually gave us — the used value of the
    // media's negative `top` (--hero-parallax-room). Read exactly (not via
    // rounded offsets) so the media never uncovers a sliver at the top edge,
    // and re-read on every ScrollTrigger refresh so it follows resizes.
    function room() {
      var top = parseFloat(window.getComputedStyle(media).top);
      return top < 0 ? -top : 0;
    }

    gsap.fromTo(
      media,
      { y: 0 },
      {
        y: room,
        ease: "none",
        immediateRender: false,
        scrollTrigger: {
          trigger: hero,
          // The hero opens the page, so the motion starts at the very first
          // scroll and finishes as the section leaves the viewport.
          start: "top top",
          end: "bottom top",
          scrub: 0.9,
          invalidateOnRefresh: true,
        },
      }
    );
  });
});
