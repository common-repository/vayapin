(function ($) {
  "use strict";

  var allowClose = true; // Flag to control the closing of the dropdown

  $(window).load(function () {
    // When VayaPin checkout button is clicked
    $(".vayapin-checkout-button").on("click", function (e) {
      e.stopPropagation();
      $(".vayapin-overlay").fadeIn(100);
      $(".vayapin-checkout-dropdown").fadeIn(100);

			// Focus on the search input
			$(".vayapin-country-select2").select2("open");
    });

    $(".vayapin-checkout-dropdown-close").on("click", function (e) {
      $(".vayapin-overlay").fadeOut(100);
      $(".vayapin-checkout-dropdown").fadeOut(100);
    });

    // Close Vayapin dropdown on Esc key press
    $(document).on("keydown", function (e) {
      if (e.key === "Escape" || e.keyCode === 27) {
        // Check if the pressed key is Esc
        if ($(".vayapin-checkout-dropdown").is(":visible")) {
          $(".vayapin-overlay").fadeOut(100);
          $(".vayapin-checkout-dropdown").fadeOut(100);
        }
      }
    });

    // Modify the outside click event handler to respect the allowClose flag
    $(document).on("click", function (e) {
      var $dropdown = $(".vayapin-checkout-dropdown");
      if (
        allowClose &&
        $dropdown.is(":visible") &&
        !$dropdown.is(e.target) &&
        $dropdown.has(e.target).length === 0
      ) {
        $(".vayapin-overlay").fadeOut(100);
        $dropdown.fadeOut(100);
      }
    });

    // Event handler for when any Select2 dropdown is opened
    $(document).on("select2:open", function (e) {
      allowClose = false; // Prevent closing the vayapin dropdown
    });

    // Event handler for when any Select2 dropdown is closed
    $(document).on("select2:close", function (e) {
      allowClose = true; // Allow closing the vayapin dropdown
    });

    // Prevent closing on click inside the dropdown
    $(".vayapin-checkout-dropdown").on("click", function (e) {
      e.stopPropagation();
    });
  });

})(jQuery);
