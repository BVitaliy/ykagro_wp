// FAQ accordion — one item open at a time
$(function () {
  $(document).on("click", ".js-faq-item .faq-item__q", function () {
    var $item = $(this).closest(".js-faq-item");
    var willOpen = !$item.hasClass("is-open");

    $item.siblings(".js-faq-item").removeClass("is-open")
      .find(".faq-item__q").attr("aria-expanded", "false");

    $item.toggleClass("is-open", willOpen);
    $(this).attr("aria-expanded", willOpen);
  });
});
