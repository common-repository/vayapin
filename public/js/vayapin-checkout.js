jQuery(document).ready(function($) {
	// Retrieve your data from local storage
	var vayapinId = localStorage.getItem('vayapin-search');

	console.log('Vayapin ID:', vayapinId);

	// Make sure the data is not null or undefined
	if (vayapinId) {
			$.ajax({
					url: vayapinCheckout.ajaxUrl,
					type: 'POST',
					data: {
							action: vayapinCheckout.actions.saveVayapinToOrder,
							vayapin: vayapinId,
							order_id: vayapinCheckout.order_id,
							nonce: vayapinCheckout.nonce
					},
					success: function(response) {
							console.log('Data saved successfully:', response);
					},
					error: function(error) {
							console.log('Error saving data:', error);
					}
			});
	}
});
