// Appointment form: phone mask + basic validation
$(function () {
  function getPhoneLocalDigits(value) {
    var digits = String(value || "").replace(/\D/g, "");

    if (digits.indexOf("380") === 0) {
      return digits.slice(2, 12);
    }
    if (digits.indexOf("38") === 0) {
      return digits.slice(2, 12);
    }

    return digits.slice(0, 10);
  }

  function formatPhone(value) {
    var digits = getPhoneLocalDigits(value);
    var result = "+38";

    if (!digits.length) return "";

    result += " (" + digits.slice(0, Math.min(3, digits.length));
    if (digits.length >= 3) result += ")";
    if (digits.length > 3) result += " " + digits.slice(3, Math.min(6, digits.length));
    if (digits.length > 6) result += "-" + digits.slice(6, Math.min(8, digits.length));
    if (digits.length > 8) result += "-" + digits.slice(8, 10);

    return result;
  }

  function isPhoneIncomplete($input) {
    if (!$input.hasClass("js-phone-mask") || !$.trim($input.val())) {
      return false;
    }

    return getPhoneLocalDigits($input.val()).length < 10;
  }

  function setFieldError($input, hasError) {
    $input.toggleClass("is-error", hasError);

    if (hasError) {
      $input.attr("aria-invalid", "true");
    } else {
      $input.removeAttr("aria-invalid");
    }
  }

  function validateAppointmentForm(form) {
    var valid = true;

    $(form)
      .find(".form-block__input")
      .each(function () {
        var $input = $(this);
        var val = $.trim($input.val());
        var isEmail = ($input.attr("type") === "email");
        var badEmail = isEmail && val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
        var hasError = ($input.prop("required") && !val) || isPhoneIncomplete($input) || badEmail;

        setFieldError($input, hasError);
        if (hasError) valid = false;
      });

    return valid;
  }

  $(document).on("submit", ".js-appointment-form", function (e) {
    // Contact Form 7 owns submission on this site: it validates server-side and
    // sends over AJAX. Preventing the default here would block it, and the
    // markup-only validation below cannot see CF7's required fields (it looks for
    // [required], CF7 marks them with aria-required), so it would report every
    // form as valid. Success is handled in app-cf7.js via the wpcf7mailsent event.
    if (this.classList.contains("wpcf7-form")) {
      return;
    }

    e.preventDefault();

    if (validateAppointmentForm(this)) {
      // no backend — markup only
      this.reset();
      $(this).find(".form-block__input").removeClass("is-filled is-error").removeAttr("aria-invalid");
      $(this).find(".js-file-list").prop("hidden", true).find(".js-file-name").text("");

      // show the thanks panel on success
      if (window.YKModal && window.YKModal.open) {
        window.YKModal.open("thanks");
      }
    }
  });

  $(document).on("input", ".form-block__input.is-error", function () {
    $(this).removeClass("is-error").removeAttr("aria-invalid");
  });

  // CV upload — show the chosen file as a chip (name + remove) above the field
  $(document).on("change", ".form-block__file-input", function () {
    var $form = $(this).closest("form");
    var $list = $form.find(".js-file-list");
    var file = this.files && this.files[0];
    $list.find(".js-file-name").text(file ? file.name : "");
    $list.prop("hidden", !file);
  });

  $(document).on("click", ".js-file-remove", function () {
    var $form = $(this).closest("form");
    $form.find(".form-block__file-input").val("");
    $form.find(".js-file-list").prop("hidden", true).find(".js-file-name").text("");
  });

  $(document).on("focus", ".js-phone-mask", function () {
    if (!$.trim(this.value)) {
      this.value = "+38 (";
    }
  });

  $(document).on("input", ".js-phone-mask", function () {
    this.value = formatPhone(this.value);
  });

  $(document).on("blur", ".js-phone-mask", function () {
    if (!getPhoneLocalDigits(this.value).length) {
      this.value = "";
    }
  });
});
