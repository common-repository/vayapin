<?php

/**
 * Adds a settings link to the plugin's action links on the WordPress plugins page.
 *
 * @param array $links An array of plugin action links.
 * @return array Modified array of plugin action links including the new settings link.
 */
function vayapin_add_settings_link($links) {
    $settingsLink = '<a href="options-general.php?page=vayapin-woocommerce">' . __('Settings', 'vayapin-woocommerce') . '</a>';
    array_unshift($links, $settingsLink); // Insert the settings link at the beginning of the links array.

    return $links;
}

// Hook into the 'plugin_action_links_' filter for the Vayapin WooCommerce plugin.
add_filter('plugin_action_links_vayapin-woocommerce/vayapin-woocommerce.php', 'vayapin_add_settings_link');
