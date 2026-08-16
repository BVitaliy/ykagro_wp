/* global lightGallery */
// Product hero gallery lightbox. Uses dynamic items so Swiper loop clones do
// not duplicate photos inside lightGallery.
(function () {
  "use strict";

  if (typeof lightGallery !== "function") return;

  var galleries = document.querySelectorAll(".js-product-hero-lightbox");

  if (!galleries.length) return;

  Array.prototype.forEach.call(galleries, function (gallery) {
    if (gallery.lgInited) return;

    var items = Array.prototype.slice
      .call(gallery.querySelectorAll(".product-hero__lightbox[data-photo-index]"))
      .reduce(function (acc, link) {
        var index = parseInt(link.getAttribute("data-photo-index"), 10);
        var img = link.querySelector("img");
        var src = link.getAttribute("data-lg-src") || link.getAttribute("href");

        if (!src || !Number.isFinite(index) || acc[index]) return acc;

        acc[index] = {
          src: src,
          thumb: link.getAttribute("data-thumb") || src,
          alt: img ? img.getAttribute("alt") || "" : "",
        };

        return acc;
      }, [])
      .filter(Boolean);

    if (!items.length) return;

    gallery.lgInited = true;

    var lightbox = lightGallery(gallery, {
      dynamic: true,
      dynamicEl: items,
      addClass: "home-gallery-lightbox",
      hideScrollbar: true,
      resetScrollPosition: false,
      download: false,
      counter: true,
      closable: true,
      escKey: true,
      swipeToClose: true,
      getCaptionFromTitleOrAlt: false,
      zoomFromOrigin: false,
      startClass: "lg-start-zoom",
      startAnimationDuration: 500,
      backdropDuration: 300,
      speed: 400,
      mobileSettings: {
        controls: true,
        showCloseIcon: true,
        download: false,
      },
    });

    if (window.YKLightbox && typeof window.YKLightbox.bind === "function") {
      window.YKLightbox.bind(gallery, "home-gallery-lightbox");
    }

    gallery.addEventListener("click", function (event) {
      var link = event.target.closest(".product-hero__lightbox");

      if (!link || !gallery.contains(link)) return;

      event.preventDefault();

      var index = parseInt(link.getAttribute("data-photo-index"), 10);

      lightbox.openGallery(Number.isFinite(index) ? index : 0);
    });
  });
})();
