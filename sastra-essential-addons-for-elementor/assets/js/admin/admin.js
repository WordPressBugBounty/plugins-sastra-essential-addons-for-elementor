"use strict";

(function ($) {
  $(function () {
    var $clearCache = $(".tmpcoderjs-clear-cache"),
      $tmpcoderMenu = $("#toplevel_page_spexo-addons .toplevel_page_spexo-addons .wp-menu-name"),
      menuText = $tmpcoderMenu.text();
    $tmpcoderMenu.text(menuText.replace(/\s/, ""));
    $clearCache.on("click", "a", function (e) {
      e.preventDefault();
      var type = "all",
        $m = $(e.delegateTarget);
      if ($m.hasClass("tmpcoder-clear-page-cache")) {
        type = "page";
      }
      $m.addClass("tmpcoder-clear-cache--init");
      $.post(SpexoAdmin.ajax_url, {
        action: "tmpcoder_clear_cache",
        type: type,
        nonce: SpexoAdmin.nonce,
        post_id: SpexoAdmin.post_id
      }).done(function (res) {
        $m.removeClass("tmpcoder-clear-cache--init").addClass("tmpcoder-clear-cache--done");
      });
    });
  });
})(jQuery);