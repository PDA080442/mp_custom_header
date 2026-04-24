(function ($) {
  $(function () {
    if ($.fn.wpColorPicker) {
      $(".mp-color").wpColorPicker();
    }

    $(".mp-upload-logo").on("click", function (e) {
      e.preventDefault();
      var $input = $(this).prev(".mp-logo-url");
      var frame = wp.media({
        title: "Выберите логотип",
        button: { text: "Использовать" },
        multiple: false,
      });
      frame.on("select", function () {
        var attachment = frame.state().get("selection").first().toJSON();
        if (attachment && attachment.url) $input.val(attachment.url);
      });
      frame.open();
    });
  });
})(jQuery);
