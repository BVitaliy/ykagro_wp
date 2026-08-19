// Contact Form 7 → design integration.
//
// The markup shipped its own submit handler that opened the "thanks" modal
// straight away, because there was no backend. Here CF7 does the real work, so
// the modal must follow CF7's own events instead of the click.
(function () {
  "use strict";

  document.addEventListener("wpcf7mailsent", function (event) {
    var form = event.target;

    // Reset the design's own field states so the form looks untouched again.
    if (form && form.querySelectorAll) {
      form.querySelectorAll(".form-block__input").forEach(function (input) {
        input.classList.remove("is-filled", "is-error");
        input.removeAttribute("aria-invalid");
      });

      var fileList = form.querySelector(".js-file-list");

      if (fileList) {
        fileList.hidden = true;
        var name = fileList.querySelector(".js-file-name");
        if (name) name.textContent = "";
      }
    }

    if (window.YKModal && window.YKModal.open) {
      window.YKModal.open("thanks");
    }
  });

  // A failed send is not a validation problem — say so instead of staying silent.
  document.addEventListener("wpcf7mailfailed", function (event) {
    var output = event.target.querySelector(".wpcf7-response-output");
    if (output) output.setAttribute("role", "alert");
  });
})();
