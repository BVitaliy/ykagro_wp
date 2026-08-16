/* global Swiper */
// Article page: in-content slider + TOC built from h2 headings.
$(function () {
  var root = document.querySelector("[data-article-page]");
  if (!root) return;

  root.querySelectorAll(".js-article-slider").forEach(function (slider) {
    if (typeof Swiper === "undefined" || slider.swiper) return;

    var swiper = new Swiper(slider, {
      slidesPerView: 1,
      autoHeight: true,
      speed: 600,
      grabCursor: true,
      pagination: {
        el: slider.querySelector(".swiper-pagination"),
        clickable: true,
      },
    });

    slider.querySelectorAll("img").forEach(function (img) {
      if (img.complete) return;
      img.addEventListener(
        "load",
        function () {
          swiper.update();
          swiper.updateAutoHeight(250);
        },
        { once: true }
      );
    });
  });

  // YouTube embeds capture wheel/touch and fight Lenis. A shield (::after)
  // keeps the iframe inert until click; then autoplay starts in the same
  // gesture so one tap is enough. Shield returns on mouseleave / scroll,
  // but not while hovered or right after the click.
  root.querySelectorAll(".article-content__video").forEach(function (wrap) {
    var iframe = wrap.querySelector("iframe");
    if (!iframe) return;

    var started = false;
    var lockUntil = 0;

    function activate() {
      wrap.classList.add("is-interactive");
      lockUntil = Date.now() + 1000;

      if (started) return;
      started = true;

      var raw = iframe.getAttribute("src") || iframe.src || "";
      if (!raw || /[?&]autoplay=1(?:&|$)/.test(raw)) return;

      try {
        var url = new URL(raw, window.location.href);
        url.searchParams.set("autoplay", "1");
        iframe.src = url.toString();
      } catch (err) {
        iframe.src = raw + (raw.indexOf("?") >= 0 ? "&" : "?") + "autoplay=1";
      }
    }

    function deactivate() {
      if (Date.now() < lockUntil) return;
      if (wrap.matches(":hover")) return;
      wrap.classList.remove("is-interactive");
    }

    wrap.addEventListener("click", activate);
    wrap.addEventListener("mouseleave", deactivate);

    if (window.lenis && typeof window.lenis.on === "function") {
      window.lenis.on("scroll", deactivate);
    }
  });

  var toc = root.querySelector("[data-article-toc]");
  if (!toc) return;

  var content = root.querySelector(".article-content");
  var toggle = toc.querySelector("[data-article-toc-toggle]");
  var currentLabel = toc.querySelector("[data-article-toc-current]");
  var list = toc.querySelector(".article-toc__list");
  var sections = content ? Array.prototype.slice.call(content.querySelectorAll("h2")) : [];
  // While a TOC click-scroll is in progress, ignore scroll-spy updates so the
  // active item doesn't flicker back to whichever heading is still in view.
  var scrollLock = false;
  var unlockTimer = null;

  function uniqueHeadingId(index) {
    var id = "article-section-" + (index + 1);
    var suffix = 2;

    while (document.getElementById(id)) {
      id = "article-section-" + (index + 1) + "-" + suffix;
      suffix += 1;
    }

    return id;
  }

  if (!sections.length || !list) {
    toc.hidden = true;
    return;
  }

  list.innerHTML = "";

  var links = sections.map(function (section, index) {
    if (!section.id) section.id = uniqueHeadingId(index);

    var item = document.createElement("li");
    var link = document.createElement("a");
    link.className = "article-toc__link" + (index === 0 ? " is-active" : "");
    link.href = "#" + section.id;
    link.textContent = section.textContent.replace(/\s+/g, " ").trim();
    item.appendChild(link);
    list.appendChild(item);

    return link;
  });

  if (currentLabel && links[0]) {
    currentLabel.textContent = links[0].textContent;
  }

  function measureTocTop() {
    var gap = window.matchMedia("(max-width: 991px)").matches ? 12 : 20;
    toc.style.setProperty("--article-toc-top", gap + "px");
  }

  function setOpen(open) {
    toc.classList.toggle("is-open", open);
    if (toggle) toggle.setAttribute("aria-expanded", open ? "true" : "false");
  }

  function setActive(id) {
    var active = null;

    links.forEach(function (link) {
      var isActive = link.getAttribute("href") === "#" + id;
      link.classList.toggle("is-active", isActive);
      if (isActive) active = link;
    });

    if (active && currentLabel) {
      currentLabel.textContent = active.textContent.replace(/\s+/g, " ").trim();
    }
  }

  function getScrollOffset() {
    measureTocTop();
    var tocHeight = window.matchMedia("(max-width: 991px)").matches && toggle ? toggle.offsetHeight + 14 : 0;
    var top = parseFloat(getComputedStyle(toc).getPropertyValue("--article-toc-top")) || 0;

    return -(top + tocHeight);
  }

  function unlockSpy() {
    scrollLock = false;
    if (unlockTimer) {
      window.clearTimeout(unlockTimer);
      unlockTimer = null;
    }
  }

  function lockSpy(ms) {
    scrollLock = true;
    if (unlockTimer) window.clearTimeout(unlockTimer);
    // Safety unlock if onComplete never fires (interrupted scroll, etc.)
    unlockTimer = window.setTimeout(unlockSpy, ms || 1600);
  }

  function scrollToSection(target, onDone) {
    var offset = getScrollOffset();
    var done = function () {
      unlockSpy();
      if (typeof onDone === "function") onDone();
    };

    lockSpy(2000);

    if (window.lenis && typeof window.lenis.scrollTo === "function") {
      window.lenis.scrollTo(target, {
        offset: offset,
        onComplete: done,
      });
      return;
    }

    var top = target.getBoundingClientRect().top + window.pageYOffset + offset;
    window.scrollTo({ top: top, behavior: "smooth" });

    var start = performance.now();
    function check() {
      var dist = Math.abs(target.getBoundingClientRect().top + offset);
      if (dist < 3 || performance.now() - start > 1800) {
        done();
        return;
      }
      window.requestAnimationFrame(check);
    }
    window.requestAnimationFrame(check);
  }

  if (toggle) {
    toggle.addEventListener("click", function () {
      setOpen(!toc.classList.contains("is-open"));
    });
  }

  links.forEach(function (link) {
    link.addEventListener("click", function (event) {
      var href = link.getAttribute("href");
      var target = href && href.charAt(0) === "#" ? document.getElementById(href.slice(1)) : null;
      if (!target) return;

      event.preventDefault();
      setActive(target.id);
      setOpen(false);
      scrollToSection(target);
    });
  });

  // Scroll-spy: keep the last heading that crossed the upper third of the
  // viewport. Skip updates while a click-scroll is animating.
  if ("IntersectionObserver" in window && sections.length) {
    var visible = {};
    var lastActiveId = links[0] ? links[0].getAttribute("href").slice(1) : null;

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            visible[entry.target.id] = true;
          } else {
            delete visible[entry.target.id];
          }
        });

        if (scrollLock) return;

        var bestId = null;
        // Prefer the last heading in document order that is still in the band.
        sections.forEach(function (section) {
          if (visible[section.id]) bestId = section.id;
        });

        if (bestId && bestId !== lastActiveId) {
          lastActiveId = bestId;
          setActive(bestId);
        }
      },
      {
        rootMargin: "-25% 0px -55% 0px",
        threshold: 0,
      }
    );

    sections.forEach(function (section) {
      observer.observe(section);
    });
  }

  measureTocTop();
  window.addEventListener("resize", measureTocTop, { passive: true });
});
