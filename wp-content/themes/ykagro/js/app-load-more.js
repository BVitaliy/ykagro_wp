/* global ScrollTrigger */
// "Показати більше" — appends the next page of a listing instead of navigating.
//
// The button stays a real link to page N+1, so without JS (or if the request
// fails) it still works as plain navigation. With JS it fetches that same URL
// and lifts the cards out of the response: the server has already applied the
// category, ?q and ?sort, so there is no query logic to duplicate here and
// nothing that can drift out of sync with the template.
//
// The pager below is replaced by the one from the response, which is how the
// button learns the next URL — and how it disappears on the last page.
(function () {
  "use strict";

  var BUSY_CLASS = "is-loading";
  var busy = false;

  function refreshScroll() {
    // Appending cards changes the page height: the scroll-drawn line and every
    // pinned scene measure it, so they have to be re-read.
    if (typeof ScrollTrigger !== "undefined" && ScrollTrigger.refresh) {
      ScrollTrigger.refresh();
    }
  }

  function move(from, to) {
    while (from.firstChild) {
      to.appendChild(from.firstChild);
    }
  }

  document.addEventListener("click", function (event) {
    if (!event.target.closest) return;

    var button = event.target.closest("[data-load-more]");
    if (!button) return;

    var grid = document.querySelector(button.getAttribute("data-load-more"));
    var block = button.closest(".pagination-block");

    // Nothing to append into — leave it a normal link.
    if (!grid || !block) return;

    event.preventDefault();

    if (busy) return;
    busy = true;
    block.classList.add(BUSY_CLASS);
    button.setAttribute("aria-busy", "true");

    fetch(button.href, { credentials: "same-origin" })
      .then(function (response) {
        if (!response.ok) throw new Error(response.status);
        return response.text();
      })
      .then(function (html) {
        var doc = new DOMParser().parseFromString(html, "text/html");
        var nextGrid = doc.querySelector(button.getAttribute("data-load-more"));
        var nextBlock = doc.querySelector(".pagination-block");

        if (!nextGrid) throw new Error("no grid");

        var first = nextGrid.firstElementChild;
        move(nextGrid, grid);

        // Swap in the response's own pager: it carries the link to the page
        // after this one, and on the last page it comes without a button.
        if (nextBlock) {
          block.replaceWith(nextBlock);
          // Focus was on a node that no longer exists — hand it to the new
          // button so keyboard users keep their place in the list.
          var nextButton = nextBlock.querySelector("[data-load-more]");
          if (nextButton && document.activeElement === document.body) {
            nextButton.focus();
          }
        } else {
          block.remove();
        }

        if (first) {
          first.setAttribute("tabindex", "-1");
        }

        refreshScroll();
      })
      .catch(function () {
        // Fall back to what the link would have done on its own.
        window.location.href = button.href;
      })
      .finally(function () {
        busy = false;
        block.classList.remove(BUSY_CLASS);
        button.removeAttribute("aria-busy");
      });
  });
})();
