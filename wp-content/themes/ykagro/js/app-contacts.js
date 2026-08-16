// Contacts map (Figma 715:5238) — pin → info popup, in two modes:
//   • static — the frame holds a picture; pins/popups are positioned from the
//              percentage coordinates already in the markup.
//   • live   — Google Maps draws the tiles and an OverlayView keeps the same
//              pin/popup DOM anchored to real lat/lng.
// Both modes therefore share one set of markup, styles and click handling; the
// popup is always closed on load and opens on a pin click.
(function () {
  "use strict";

  var root = document.querySelector("[data-contacts-map]");
  if (!root) return;

  var frame = root.querySelector(".contacts-map__frame");
  var canvas = root.querySelector("[data-contacts-map-canvas]");
  var pins = Array.prototype.slice.call(root.querySelectorAll("[data-contacts-map-pin]"));
  var popups = Array.prototype.slice.call(root.querySelectorAll("[data-contacts-map-popup]"));
  var config = window.YKAGRO_CONTACTS_MAP || {};

  if (!frame || !pins.length) return;

  var openId = null;
  var overlays = {};

  var EDGE = 20;
  var POPUP_GAP = 12;

  function popupById(id) {
    for (var i = 0; i < popups.length; i++) {
      if (popups[i].getAttribute("data-contacts-map-popup") === id) return popups[i];
    }
    return null;
  }

  function pinById(id) {
    for (var i = 0; i < pins.length; i++) {
      if (pins[i].getAttribute("data-contacts-map-pin") === id) return pins[i];
    }
    return null;
  }

  // Static mode places the popup itself: centred over its pin and above it,
  // but clamped — and flipped below — so it never leaves the (clipped) frame.
  function placeStatic(id) {
    var pin = pinById(id);
    var popup = popupById(id);
    if (!pin || !popup) return;

    var frameBox = frame.getBoundingClientRect();
    var pinBox = pin.getBoundingClientRect();
    var popupBox = popup.getBoundingClientRect();

    var left = pinBox.left + pinBox.width / 2 - frameBox.left;
    var minLeft = popupBox.width / 2 + EDGE;
    var maxLeft = frameBox.width - popupBox.width / 2 - EDGE;
    if (maxLeft > minLeft) left = Math.min(Math.max(left, minLeft), maxLeft);

    var top = pinBox.top - frameBox.top - POPUP_GAP - popupBox.height;
    if (top < EDGE) top = pinBox.bottom - frameBox.top + POPUP_GAP;

    popup.style.left = left + "px";
    popup.style.top = top + "px";
    popup.style.transform = "translateX(-50%)";
  }

  function closeAll() {
    popups.forEach(function (p) {
      p.classList.add("is-hidden");
    });
    pins.forEach(function (p) {
      p.setAttribute("aria-expanded", "false");
    });
    openId = null;
  }

  // Live map: pan so the open pin + popup stay inside the clipped frame.
  function ensureVisible(id) {
    if (!map) return;
    var pin = pinById(id);
    var popup = popupById(id);
    if (!pin || !popup || popup.classList.contains("is-hidden")) return;

    var frameBox = frame.getBoundingClientRect();
    var pinBox = pin.getBoundingClientRect();
    var popupBox = popup.getBoundingClientRect();

    var left = Math.min(pinBox.left, popupBox.left);
    var right = Math.max(pinBox.right, popupBox.right);
    var top = Math.min(pinBox.top, popupBox.top);
    var bottom = Math.max(pinBox.bottom, popupBox.bottom);

    var pad = EDGE;
    var dx = 0;
    var dy = 0;

    // Prefer centering the pin+popup group when anything is clipped;
    // otherwise leave the map alone.
    var clipped =
      left < frameBox.left + pad ||
      right > frameBox.right - pad ||
      top < frameBox.top + pad ||
      bottom > frameBox.bottom - pad;

    if (clipped) {
      var midX = (left + right) / 2;
      var midY = (top + bottom) / 2;
      dx = midX - (frameBox.left + frameBox.width / 2);
      dy = midY - (frameBox.top + frameBox.height / 2);
    }

    if (Math.abs(dx) > 1 || Math.abs(dy) > 1) {
      map.panBy(dx, dy);
    }
  }

  function open(id) {
    closeAll();
    var popup = popupById(id);
    if (!popup) return;

    popup.classList.remove("is-hidden");
    openId = id;
    pins.forEach(function (p) {
      if (p.getAttribute("data-contacts-map-pin") === id) p.setAttribute("aria-expanded", "true");
    });
    if (overlays[id]) {
      overlays[id].draw();
      // Wait a frame so the popup has real metrics, then pan into view.
      window.requestAnimationFrame(function () {
        ensureVisible(id);
      });
    } else {
      placeStatic(id);
    }
  }

  function toggle(id) {
    if (openId === id) closeAll();
    else open(id);
  }

  // Listeners sit on the elements themselves rather than on a delegating
  // ancestor: in live mode these nodes live inside the Google Maps container,
  // which swallows bubbling DOM events before they reach the section root.
  pins.forEach(function (pin) {
    pin.addEventListener("click", function (e) {
      e.preventDefault();
      toggle(pin.getAttribute("data-contacts-map-pin"));
    });
  });

  popups.forEach(function (popup) {
    var close = popup.querySelector("[data-contacts-map-close]");
    if (close) {
      close.addEventListener("click", function (e) {
        e.preventDefault();
        closeAll();
      });
    }
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && openId) closeAll();
  });

  window.addEventListener("resize", function () {
    if (openId && !overlays[openId]) placeStatic(openId);
  });

  // Static mode — the markup plus placeStatic() is the finished map.
  if (!canvas) return;

  // ── Live mode ────────────────────────────────────────────────────────
  var MAP_STYLES = [
    { featureType: "water", elementType: "geometry", stylers: [{ color: "#e9e9e9" }, { lightness: 17 }] },
    { featureType: "landscape", elementType: "geometry", stylers: [{ color: "#f5f5f5" }, { lightness: 20 }] },
    { featureType: "road.highway", elementType: "geometry.fill", stylers: [{ color: "#ffffff" }, { lightness: 17 }] },
    { featureType: "road.arterial", elementType: "geometry", stylers: [{ color: "#ffffff" }, { lightness: 18 }] },
    { featureType: "road.local", elementType: "geometry", stylers: [{ color: "#ffffff" }, { lightness: 16 }] },
    { featureType: "poi", elementType: "labels.icon", stylers: [{ visibility: "off" }] },
    { elementType: "labels.text.fill", stylers: [{ color: "#7c7c82" }] },
    { elementType: "labels.text.stroke", stylers: [{ color: "#ffffff" }] },
    { featureType: "transit", stylers: [{ visibility: "simplified" }] },
  ];

  var PIN_H = 61;
  var booted = false;
  var map;

  // One overlay per point: it owns the pin button and its popup, so both keep
  // the markup/CSS they already have and simply follow the map.
  function makeOverlay(maps, position, pinEl, popupEl) {
    function Overlay() {}
    Overlay.prototype = Object.create(maps.OverlayView.prototype);
    Overlay.prototype.constructor = Overlay;

    Overlay.prototype.onAdd = function () {
      var panes = this.getPanes();
      panes.floatPane.appendChild(pinEl);
      pinEl.style.position = "absolute";
      pinEl.style.visibility = "visible";
      if (popupEl) {
        panes.floatPane.appendChild(popupEl);
        popupEl.style.position = "absolute";
      }
      // Let clicks/drags on our own DOM through instead of being treated as
      // map gestures (pan, zoom) by the Maps event layer.
      [pinEl, popupEl].forEach(function (el) {
        if (el && maps.OverlayView.preventMapHitsAndGesturesFrom) {
          maps.OverlayView.preventMapHitsAndGesturesFrom(el);
        }
      });
    };

    Overlay.prototype.draw = function () {
      var projection = this.getProjection();
      if (!projection) return;
      var point = projection.fromLatLngToDivPixel(position);
      if (!point) return;

      pinEl.style.left = point.x + "px";
      pinEl.style.top = point.y + "px";

      if (!popupEl) return;

      // Above the pin by default; flipped below when that would push the popup
      // past the top of the frame (which clips it).
      popupEl.style.left = point.x + "px";
      popupEl.style.top = point.y - PIN_H - POPUP_GAP + "px";
      popupEl.style.transform = "translate(-50%, -100%)";

      if (popupEl.classList.contains("is-hidden")) return;
      var frameBox = frame.getBoundingClientRect();
      if (popupEl.getBoundingClientRect().top < frameBox.top + EDGE) {
        popupEl.style.top = point.y + POPUP_GAP + "px";
        popupEl.style.transform = "translate(-50%, 0)";
      }
    };

    Overlay.prototype.onRemove = function () {
      [pinEl, popupEl].forEach(function (el) {
        if (el && el.parentNode) el.parentNode.removeChild(el);
      });
    };

    return new Overlay();
  }

  function createMap(maps) {
    var bounds = new maps.LatLngBounds();

    map = new maps.Map(canvas, {
      zoom: config.zoom || 11,
      styles: MAP_STYLES,
      disableDefaultUI: true,
      zoomControl: true,
      mapTypeControl: false,
      streetViewControl: false,
      fullscreenControl: false,
      clickableIcons: false,
      gestureHandling: "cooperative",
    });

    pins.forEach(function (pin) {
      var id = pin.getAttribute("data-contacts-map-pin");
      var position = new maps.LatLng(Number(pin.dataset.lat), Number(pin.dataset.lng));
      bounds.extend(position);

      var overlay = makeOverlay(maps, position, pin, popupById(id));
      overlay.setMap(map);
      overlays[id] = overlay;
    });

    if (!bounds.isEmpty()) {
      map.fitBounds(bounds, { top: 140, right: 100, bottom: 100, left: 100 });
    }

    maps.event.addListener(map, "idle", function () {
      if (openId && overlays[openId]) overlays[openId].draw();
    });
    window.addEventListener("resize", function () {
      if (openId && overlays[openId]) overlays[openId].draw();
    });
  }

  function showError(message) {
    closeAll();
    root.classList.add("contacts-map--failed");
    canvas.innerHTML = '<p class="contacts-map__fallback">' + message + "</p>";
  }

  function boot() {
    if (booted) return;
    booted = true;

    if (!config.apiKey) {
      showError("Додайте Google Maps API key у inc/_maps-config.php, щоб показати карту.");
      return;
    }
    if (!window.google || !window.google.maps) {
      showError("Не вдалося завантажити Google Maps. Перевірте API key і підключення.");
      return;
    }
    createMap(window.google.maps);
  }

  window.initYkagroContactsMap = boot;

  if (window.google && window.google.maps) boot();
  else if (!config.apiKey) boot();
})();
