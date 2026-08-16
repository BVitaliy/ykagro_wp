// Floating menu (Figma 684:685). Toggle the expanding panel, open straight to
// the search field, close on overlay / Esc / link click.
$(function () {
  var $menu = $(".js-menu");
  if (!$menu.length) return;

  var $input = $menu.find(".js-menu-search-input");
  var closeTimer = null;
  var readyFrame = null;
  // The items start rising together with the panel — .is-ready lands on the
  // next frame, so the reveal and the expansion run as one move instead of
  // one after the other. The stagger itself (CSS) makes it read slowly.
  var STEP_MS = 50; // keep in sync with the stagger step in _menu.scss

  // Stagger delays come from --menu-i / --menu-n on each link, so the
  // animation stays correct no matter how many items the nav has. The count
  // also goes on the root: the search field waits for the whole nav (the
  // reveal runs bottom-up, and the field sits above the links).
  function syncLinkStagger() {
    var $links = $menu.find(".menu__link");
    var n = $links.length || 1;
    $menu[0].style.setProperty("--menu-n", String(n));
    $links.each(function (i) {
      this.style.setProperty("--menu-i", String(i));
      this.style.setProperty("--menu-n", String(n));
    });
    return n;
  }

  syncLinkStagger();

  function openMenu(focusSearch) {
    clearTimeout(closeTimer);
    if (readyFrame) cancelAnimationFrame(readyFrame);
    var n = syncLinkStagger();
    $menu.removeClass("is-closing is-ready").addClass("is-open");
    if (window.lenis && window.lenis.stop) window.lenis.stop();
    // Next frame, so the browser has the closed state to transition from.
    readyFrame = requestAnimationFrame(function () {
      readyFrame = requestAnimationFrame(function () {
        readyFrame = null;
        if ($menu.hasClass("is-open")) $menu.addClass("is-ready");
      });
    });
    if (focusSearch) {
      // the field is the last thing to arrive (see the stagger above)
      setTimeout(function () {
        $input.trigger("focus");
      }, n * STEP_MS + 120);
    }
  }

  function closeMenu() {
    if (!$menu.hasClass("is-open")) return;

    if (readyFrame) cancelAnimationFrame(readyFrame);
    readyFrame = null;
    $menu.addClass("is-closing").removeClass("is-open is-ready");
    if (window.lenis && window.lenis.start) window.lenis.start();
    closeTimer = setTimeout(function () {
      $menu.removeClass("is-closing");
    }, 400);
  }

  function toggleMenu() {
    if ($menu.hasClass("is-open")) {
      closeMenu();
    } else {
      openMenu(false);
    }
  }

  $menu.find(".js-menu-toggle").on("click", toggleMenu);
  $menu.find(".js-menu-search-toggle").on("click", function () {
    openMenu(true);
  });
  $menu.find(".js-menu-close").on("click", closeMenu);
  $menu.on("click", ".menu__link", closeMenu);

  $(document).on("keydown", function (e) {
    if (e.key === "Escape" && $menu.hasClass("is-open")) {
      closeMenu();
    }
  });
});
