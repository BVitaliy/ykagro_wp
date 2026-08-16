// Mobile catalog filter sheet (Figma 1234:6213) — full-screen category + sort
// picker that replaces the tab rail and the sort dropdown on phones.
//
// The radios inside the sheet are the state; "Застосувати" turns them into a
// plain navigation (<category>?sort=…&q=…), i.e. exactly the GET the desktop
// form submits, so the backend needs nothing extra. "Скасувати" restores the
// state the sheet had when it was opened.
$(function () {
  var $sheet = $(".js-catalog-filter");
  if (!$sheet.length) return;

  // Portal to <body>: inside <main> the sheet sits in a z-index: 1 stacking
  // context (see _scroll-line.scss), so it would paint under the floating menu.
  $sheet.appendTo(document.body);

  var $open = $(".js-catalog-filter-open");
  var $cats = $sheet.find(".js-catalog-filter-cat");
  var $sorts = $sheet.find(".js-catalog-filter-sort");
  var $close = $sheet.find(".js-catalog-filter-close");
  var desktop = window.matchMedia("(min-width: 768px)");
  var opened = null; // radio values captured on open, for Скасувати

  function readState() {
    return {
      cat: $cats.filter(":checked").val() || "",
      sort: $sorts.filter(":checked").val() || "",
    };
  }

  function applyState(state) {
    if (!state) return;
    $cats.each(function () {
      this.checked = this.value === state.cat;
    });
    $sorts.each(function () {
      this.checked = this.value === state.sort;
    });
  }

  function lock() {
    if (window.YKScrollLock) window.YKScrollLock.lock();
    else if (window.lenis && window.lenis.stop) window.lenis.stop();
  }

  function unlock() {
    if (window.YKScrollLock) window.YKScrollLock.unlock();
    else if (window.lenis && window.lenis.start) window.lenis.start();
  }

  function openSheet() {
    if ($sheet.hasClass("is-open")) return;
    opened = readState();
    // the floating menu must not stay expanded under the sheet
    $(".js-menu").removeClass("is-open is-ready");
    $sheet.addClass("is-open").attr("aria-hidden", "false");
    $open.attr("aria-expanded", "true");
    $sheet.find(".catalog-filter__inner")[0].scrollTop = 0;
    lock();
    if ($close.length) $close[0].focus({ preventScroll: true });
  }

  // revert: true for Скасувати / X / Esc, false when the choice was applied
  function closeSheet(revert) {
    if (!$sheet.hasClass("is-open")) return;
    if (revert) applyState(opened);
    $sheet.removeClass("is-open").attr("aria-hidden", "true");
    $open.attr("aria-expanded", "false");
    unlock();
    if ($open.length) $open[0].focus({ preventScroll: true });
  }

  function applyFilters() {
    var state = readState();
    var url = new URL(state.cat || window.location.href, window.location.href);

    if (state.sort) url.searchParams.set("sort", state.sort);
    else url.searchParams.delete("sort");

    // keep whatever is typed in the search field — it is part of the same GET
    var q = $(".catalog__search-input").val() || "";
    if (q) url.searchParams.set("q", q);
    else url.searchParams.delete("q");

    closeSheet(false);
    window.location.href = url.toString();
  }

  $open.on("click", function (e) {
    e.preventDefault();
    openSheet();
  });

  $sheet.on("click", ".js-catalog-filter-close, .js-catalog-filter-cancel", function (e) {
    e.preventDefault();
    closeSheet(true);
  });

  $sheet.on("click", ".js-catalog-filter-apply", function (e) {
    e.preventDefault();
    applyFilters();
  });

  $(document).on("keydown", function (e) {
    if (e.key === "Escape") closeSheet(true);
  });

  // Rotating to a tablet/desktop width brings the inline tabs + select back
  function onBreakpoint(e) {
    if (e.matches) closeSheet(true);
  }
  if (desktop.addEventListener) desktop.addEventListener("change", onBreakpoint);
  else if (desktop.addListener) desktop.addListener(onBreakpoint);
});
