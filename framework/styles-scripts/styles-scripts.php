<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function vayapin_connector_enqueue_select2()
{
    // Enqueue Country select CSS
    wp_enqueue_style(
        'vayapin-country-select-css',
        plugin_dir_url(dirname(__DIR__)) . 'public/css/countrySelect.css',
        [], // Dependencies
        '4.0.13' // Version
    );

    wp_enqueue_script(
        'vayapin-country-select-js',
        plugin_dir_url(dirname(__DIR__)) . 'public/js/countrySelect.js',
        ['jquery', 'select2-js'],
        '1.0.0',
        true
    );

    // Enqueue Select2 CSS locally
    wp_enqueue_style(
        'select2-css',
        plugin_dir_url(dirname(__DIR__)) . 'public/css/vendor/select2.min.css',
        [], // Dependencies
        '4.0.13' // Version
    );

    // Enqueue Select2 JS locally
    wp_enqueue_script(
        'select2-js',
        plugin_dir_url(dirname(__Dir__)) . 'public/js/vendor/select2.min.js',
        ['jquery'], // Dependencies
        '4.0.13', // Version
        true // In footer
    );

    // Enqueue and localize custom JS for initializing Select2 on desired elements
    wp_enqueue_script(
        'vayapin-select2-init',
        plugin_dir_url(dirname(__DIR__)) . 'public/js/vayapin-select2.js',
        ['jquery', 'select2-js'],
        '1.0.0',
        true
    );

    // Correctly localize the script after it's enqueued
    wp_localize_script(
        'vayapin-select2-init',
        'vayapinSelect2',
        [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('vayapin_ajax_nonce'), // Generate nonce here
            'actions' => [
                'search' => 'vayapin_select2_search',
                'fillFields' => 'vayapin_checkout_fill',
            ]
        ]
    );
}

add_action('wp_enqueue_scripts', 'vayapin_connector_enqueue_select2');
add_action('admin_enqueue_scripts', 'vayapin_connector_enqueue_select2');

function vayapin_connector_enqueue_checkout($order_id)
{
    wp_enqueue_script(
        'vayapin-checkout',
        plugin_dir_url(dirname(__DIR__)) . 'public/js/vayapin-checkout.js',
        ['jquery'],
        '1.0.0',
        true
    );

    // Pass the Order ID, the AJAX URL, and the nonce to the script
    wp_localize_script(
        'vayapin-checkout',
        'vayapinCheckout',
        [
            'ajaxUrl' => esc_url(admin_url('admin-ajax.php')),
            'nonce' => wp_create_nonce('vayapin_ajax_nonce'), // Generate a separate nonce for checkout
            'order_id' => esc_js($order_id),
            'actions' => [
                'saveVayapinToOrder' => 'vayapin_save_to_order',
            ]
        ]
    );    
}

// Enqueue custom script on the WooCommerce thank you page
add_action('woocommerce_thankyou', 'vayapin_connector_enqueue_checkout');
