(function ($) {
  "use strict";

  $(window).load(function () {
    /**
     * Initialize select2
     */
    $(".vayapin-select2").select2({
      width: "100%",
      ajax: {
        url: vayapinSelect2.ajaxUrl,
        dataType: "json",
        delay: 250,
        method: "POST",
        data: function (params) {
          return {
            action: vayapinSelect2.actions.search,
            search: params.term,
            nonce: vayapinSelect2.nonce,
          };
        },
        processResults: function (data) {
          localStorage.setItem("vayapin-search", data.data[0].id);
          return {
            results: data.data,
          };
        },
        cache: true,
      },
      minimumInputLength: 3,
      placeholder: "Search for a VayaPin",
    });

    // Country selector
    $("#country_selector").countrySelect({
			defaultCountry: "dk"
		});

    // Set country in local storage
    $("#country_selector").on("change", function (e) {
      const country = $("#country_selector").countrySelect(
        "getSelectedCountryData"
      );
      localStorage.setItem("vayapin-country", country);
    });

		// Set the vaya pin in local storage
    $("#vayapin_input").on("input", function (e) {
			var value = $(this).val();
			$(this).val(value.toUpperCase());
      localStorage.setItem("vayapin-search", value);
    });

    /**
     * When VayaPin fill fields button is clicked
     */
    $("#vayapin-fields-button").on("click", function (e) {
      e.preventDefault(); // Prevent default action

			const countryData = $("#country_selector").countrySelect(
        "getSelectedCountryData"
      );

			const vayapin = localStorage.getItem("vayapin-search");
		
			// Make vayapin string
			const vayapinString = countryData.iso2.toUpperCase() + ':' + vayapin.toUpperCase();

			// Save the full vyapin string to local storage
			localStorage.setItem('vayapin-search', vayapinString);

      $.ajax({
        url: vayapinSelect2.ajaxUrl,
        type: "POST",
        data: {
          action: vayapinSelect2.actions.fillFields,
          id: vayapinString,
          nonce: vayapinSelect2.nonce,
        },
        success: function (response) {
          // remove the overlay
          $(".vayapin-overlay").fadeOut(100);

          // remove the dropdown
          $(".vayapin-checkout-dropdown").fadeOut(100);

					// Remove error text
					$('.vayapin-error-text').fadeOut(100);

					// Clear input of vayapin input field
					$("#vayapin_input").val('');

          // fill the fields
          fillOutFields(response.data);
        },
				error: function (xhr, status, error) {
					$('.vayapin-error-text').fadeIn(100);
					
					// Clear input of vayapin input field
					$("#vayapin_input").val('');
				}
      });
    });
  });

  /**
   * Fill out fields in the checkout form
   */
  function fillOutFields(data) {
    $("#billing_company").val(data.company);
    $("#billing_email").val(data.email);
    $("#billing_phone").val(data.phone);
    $("#billing_address_1").val(data.address_1);
    $("#billing_address_2").val(data.address_2);
    $("#billing_city").val(data.city);
    $("#billing_state").val(data.state);
    $("#billing_postcode").val(data.postcode);
    $("#billing_country").val(data.country).trigger("change");
  }
})(jQuery);
