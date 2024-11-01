<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Dompdf\Dompdf;

/**
 * Registers a filter to modify WooCommerce order actions, adding an option to generate a Vayapin label.
 */
function vayapin_connector_print_label($actions) {
    $options = get_option('vayapin_woocommerce_options');
    if (!empty($options['label_generation'])) {
        $actions['print_vayapin_label'] = __('Generate Vayapin label', 'vayapin-woocommerce');
    }
    return $actions;
}
add_filter('woocommerce_order_actions', 'vayapin_connector_print_label');

/**
 * Hooks into the WooCommerce order action to handle Vayapin label printing.
 */
function vayapin_connector_handle_print_label($order) {
    vayapin_connector_generate_label($order->get_id());
    $vayapin_id = $order->get_meta('vayapin_id');
    vayapin_usage_request($vayapin_id, 'generate_label');
}
add_action('woocommerce_order_action_print_vayapin_label', 'vayapin_connector_handle_print_label');

/**
 * Generates a Vayapin label for the specified order.
 * 
 * @param int $order_id The ID of the order for which the label is generated.
 */
function vayapin_connector_generate_label($order_id) {
    // Get the order
    $order = wc_get_order($order_id);

    // Construct the Vayapin label URL dynamically
    $site_url = get_site_url(); // Get the site's base URL
    $vayapin_label_url = $site_url . '/vayapin-label/' . $order_id;

    // Update the order meta data with the Vayapin label information
    $order->update_meta_data('vayapin_label_generated', true);
    $order->update_meta_data('vayapin_label_url', $vayapin_label_url);

    // Save the order to persist the changes
    $order->save();
}


/**
 * Adds a custom section after the shipping address in WooCommerce admin order details, showing the Vayapin label.
 */
function vayapin_custom_content_after_order_details($order) {
    $options = get_option('vayapin_woocommerce_options');
    if (!empty($options['label_generation']) && $order->get_meta('vayapin_label_generated')) {
        $imgUrl = esc_url(plugin_dir_url(__FILE__) . '../../public/img/vayapin-icon.svg');
        echo '<div class="custom-content-section">';
        echo '<h3><img src="' . esc_url($imgUrl) . '" alt="Vayapin" class="vayapin-button-icon"> Vayapin</h3>';
        echo '<p>A Vayapin label has been generated for this order.</p>';
        echo '<a href="' . esc_url($order->get_meta('vayapin_label_url')) . '" target="_blank">See the label here</a>';
        echo '</div>';
    }
}
add_action('woocommerce_admin_order_data_after_shipping_address', 'vayapin_custom_content_after_order_details', 10, 1);


// Register a custom query var to handle Vayapin label generation.
function vayapin_register_query_var($vars) {
    $vars[] = 'vayapin_label'; // This is the name of your query var
    return $vars;
}
add_filter('query_vars', 'vayapin_register_query_var');


// Add a custom rewrite rule to WordPress to handle Vayapin label generation.
function vayapin_rewrite_rule() {
    add_rewrite_rule('^vayapin-label/([0-9]+)/?$', 'index.php?vayapin_label=$matches[1]', 'top');
}
add_action('init', 'vayapin_rewrite_rule');

// Handle the Vayapin label request.
function vayapin_handle_label_request() {
    $order_id = get_query_var('vayapin_label');
    if ($order_id) {
        
        // Prevent WordPress from processing further template redirects.
        global $wp_query;
        $wp_query->is_404 = false;
        status_header(200); // Send a HTTP 200 status code.

        $template_path = plugin_dir_path(__FILE__) . 'vayapin-label-template.php';
        include($template_path);

        exit; // Stop further processing
    }
}
add_action('template_redirect', 'vayapin_handle_label_request');

