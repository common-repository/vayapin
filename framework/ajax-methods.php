<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handles the search request for VayaPin and returns results in JSON format.
 */
function vayapin_connector_select2_search_callback()
{
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'vayapin_ajax_nonce')) {
        wp_send_json_error('Nonce verification failed!', 403);
        return;
    }

    $search_term = isset($_POST['search']) ? sanitize_text_field(wp_unslash($_POST['search'])) : '';

    // Validate search term (example: ensure it's not empty and within expected length)
    if (empty($search_term) || strlen($search_term) > 255) {
        wp_send_json_error('Invalid search term.', 400);
        return;
    }

    $data = vayapin_connector_search($search_term);

    // Return the search results as a JSON success response.
    wp_send_json_success($data);
}

add_action('wp_ajax_vayapin_select2_search', 'vayapin_connector_select2_search_callback');
add_action('wp_ajax_nopriv_vayapin_select2_search', 'vayapin_connector_select2_search_callback');

/**
 * Custom sanitization function for VayaPin IDs.
 *
 * @param string $vayapin_id The VayaPin ID to sanitize.
 * @return string The sanitized VayaPin ID.
 */
function sanitize_vayapin_id($vayapin_id) {
    // Remove any unwanted characters while preserving colons
    return preg_replace('/[^a-zA-Z0-9-:]/', '', sanitize_text_field($vayapin_id));
}

/**
 * Fills out the checkout fields based on VayaPin data.
 */
function vayapin_connector_checkout_fill_callback() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'vayapin_ajax_nonce')) {
        wp_send_json_error('Nonce verification failed!', 403);
        return;
    }

    // Sanitize and validate Vayapin ID
    $vayapinId = isset($_POST['id']) ? sanitize_vayapin_id(wp_unslash($_POST['id'])) : '';

    // Validate Vayapin ID (ensure it's not empty and matches expected format)
    if (empty($vayapinId) || !preg_match('/^[a-zA-Z0-9-:]+$/', $vayapinId)) {
        wp_send_json_error('Invalid VayaPin ID.', 400);
        return;
    }

    // Fetch Vayapin data
    $vayapin = vayapin_connector_fetch($vayapinId);

    if ($vayapin === null) {
        wp_send_json_error('VayaPin not found!', 404);
        return;
    }

    // Sanitize output data
    $data = [
        'company'    => sanitize_text_field($vayapin['data']['title']['value'] ?? ''),
        'email'      => sanitize_email($vayapin['data']['email']['value'] ?? ''),
        'country'    => sanitize_text_field(strtoupper($vayapin['data']['country'] ?? '')),
        'city'       => sanitize_text_field($vayapin['data']['address_city']['value'] ?? ''),
        'state'      => sanitize_text_field($vayapin['data']['address_state']['value'] ?? ''),
        'postcode'   => sanitize_text_field($vayapin['data']['address_zip']['value'] ?? ''),
        'phone'      => sanitize_text_field($vayapin['data']['phone']['value'] ?? ''),
        'address_1'  => sanitize_text_field($vayapin['data']['address_line_1']['value'] ?? ''),
        'address_2'  => sanitize_text_field($vayapin['data']['address_line_2']['value'] ?? ''),
    ];

    // Post a request to the usage API
    $usage = vayapin_usage_request($vayapinId, 'checkout');

    // Send the data back as a JSON success response to populate checkout fields.
    wp_send_json_success($data);
}

add_action('wp_ajax_vayapin_checkout_fill', 'vayapin_connector_checkout_fill_callback');
add_action('wp_ajax_nopriv_vayapin_checkout_fill', 'vayapin_connector_checkout_fill_callback');


// Register AJAX actions for filling out checkout fields.
add_action('wp_ajax_vayapin_checkout_fill', 'vayapin_connector_checkout_fill_callback');
add_action('wp_ajax_nopriv_vayapin_checkout_fill', 'vayapin_connector_checkout_fill_callback');

/**
 * Saves VayaPin data to the order.
 */
function vayapin_connector_save_to_order_callback() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'vayapin_ajax_nonce')) {
        wp_send_json_error('Nonce verification failed!', 403);
        return;
    }

    // Sanitize and validate the order ID
    $order_id = isset($_POST['order_id']) ? intval(sanitize_text_field(wp_unslash($_POST['order_id']))) : 0;
    if ($order_id <= 0) {
        wp_send_json_error('Invalid order ID.', 400);
        return;
    }

    // Sanitize and validate Vayapin ID
    $vayapinId = isset($_POST['vayapin']) ? sanitize_vayapin_id(wp_unslash($_POST['vayapin'])) : '';
    if (empty($vayapinId) || !preg_match('/^[a-zA-Z0-9-:]+$/', $vayapinId)) {
        wp_send_json_error('Invalid VayaPin ID.', 400);
        return;
    }

    // Get the order
    $order = wc_get_order($order_id);
    if (!$order) {
        wp_send_json_error('Order not found.', 404);
        return;
    }

    // Save the Vayapin ID to the order
    $order->update_meta_data('vayapin_id', $vayapinId);
    $order->save();

    wp_send_json_success('VayaPin ID saved successfully.');
}

add_action('wp_ajax_vayapin_save_to_order', 'vayapin_connector_save_to_order_callback');
add_action('wp_ajax_nopriv_vayapin_save_to_order', 'vayapin_connector_save_to_order_callback');

