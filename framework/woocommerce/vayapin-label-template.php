<?php
// Ensure this file is being included by a WordPress function or exit
if (!defined('ABSPATH')) exit;

// Get the order ID from the URL
$order_id = get_query_var('vayapin_label');

// Ensure order ID is an integer
$order_id = intval($order_id);

// Get the order
$order = wc_get_order($order_id);

// Validate the order object
if (!$order) {
    wp_die('Order not found.'); // Safely handle cases where the order doesn't exist
}

// Get the Vayapin ID from the order
$vayapin_id = $order->get_meta('vayapin_id');

// Sanitize the Vayapin ID to ensure it's safe for use
$vayapin_id = sanitize_text_field($vayapin_id);

// Get vayapin QR code
$vayapin_qr_code = vayapin_connector_get_qr_code($vayapin_id);

// Ensure QR code URL is safe to use
$vayapin_qr_code = esc_url($vayapin_qr_code);

// Function to enqueue styles
function vayapin_enqueue_styles() {
    wp_register_style('vayapin-label', plugin_dir_url(__FILE__) . 'public/css/vayapin-label.css');
    wp_enqueue_style('vayapin-label');
    $custom_css = '
        @media print {
            .print-no-shadow {
                box-shadow: none !important;
                -webkit-print-color-adjust: exact;
            }
        }
		.body {
	display: flex;
	justify-content: center;
	align-items: center;
	height: 100vh;
	background-color: #f5f5f5;
	font-family: "Bree Serif", sans-serif;
	text-align: left;
}

.label {
	background-color: #fff;
	padding: 20px;
	border-radius: 5px;
	box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
	text-align: center;
	max-width: 300px;
}

.label>img {
	max-width: 300px;
}	
		';
    wp_add_inline_style('vayapin-label', $custom_css);
}
add_action('wp_enqueue_scripts', 'vayapin_enqueue_styles');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vayapin Label</title>
    <?php wp_head(); ?>
</head>
<body class="body">
    <div class="label">
        <h1 class="text-lg font-semibold">VayaPin Label</h1>
        <p class="text-sm mt-2">Scan the QR code and access the world's most precise address.</p>
        <img src="<?php echo esc_url($vayapin_qr_code); ?>" alt="VayaPin QR Code">
    </div>
    <?php wp_footer(); ?>
</body>
</html>
