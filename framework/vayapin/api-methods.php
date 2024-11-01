<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

const apiBaseUrl = "https://cs.vayapin.com/api/v0/";


function vayapin_connector_search($search_term)
{
    $options = get_option('vayapin_woocommerce_options');
    $api_url = apiBaseUrl . '/' . $search_term;
    $args = [
        'headers' => [
            'Authorization' => 'Bearer ' . $options['api_key'],
            'Content-Type' => 'application/json',
        ]
    ];
    $response = wp_remote_get($api_url, $args);
    $body = wp_remote_retrieve_body($response);
    $result = json_decode($body, true);
    
    $data = [];
    foreach ($result as $pin) {
        $data[] = [
            'id' => $pin['vayapin'],
            'text' => isset($pin['title']['value']) ? $pin['title']['value'] : $pin['vayapin']
        ];
    }

    return $data;
}

function vayapin_connector_fetch($id)
{
    $options = get_option('vayapin_woocommerce_options');
    $api_url = apiBaseUrl . '/pin/' . $id;
    $args = [
        'headers' => [
            'Authorization' => 'Bearer ' . $options['api_key'],
            'Content-Type' => 'application/json',
        ]
    ];
    $response = wp_remote_get($api_url, $args);
    $body = wp_remote_retrieve_body($response);
    $result = json_decode($body, true);
    return $result;
}

/**
 * Get VayaPin QR code
 */
function vayapin_connector_get_qr_code($vayapinId)
{
    $imgFormattedVayapin = strtolower(str_replace(':', '_', $vayapinId));

    $options = get_option('vayapin_woocommerce_options');
    $api_url = "https://cs.vayapin.com/r/qrc/p/{$vayapinId}/my_dl/{$imgFormattedVayapin}.png";
    $hel = vayapin_usage_request($vayapinId, 'get_qr_code');

    return $api_url;
}

/**
 * Post checkout usage request
 */
function vayapin_usage_request($vayapinId, $type)
{
    $options = get_option('vayapin_woocommerce_options');

    // Post request to the API
    $args = [
        'headers' => [
            'Authorization' => 'Bearer ' . $options['api_key'],
            'Content-Type' => 'application/json',
        ],
        'body' => wp_json_encode([
            'usage_type' => $type,
            'platform' => 'wordpress',
            'vayapin_id' => $vayapinId,
        ]),
    ];

    try {
        $url = apiBaseUrl . 'usage/track';
        $response = wp_remote_post($url, $args);
        $body = wp_remote_retrieve_body($response);
        $result = json_decode($body, true);
        return $result;
    } catch (\Exception $e) {
        error_log($e->getMessage());
    }
}
