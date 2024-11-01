<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://vayapin.com
 * @since             1.0.0
 * @package           Vayapin
 *
 * @wordpress-plugin
 * Plugin Name:       VayaPin Connector
 * Plugin URI:        https://vayapin.com
 * Description:       This is the VayaPin Wordpress & Woocommerce plugin to integrate the use of VayaPin directly in to your Woocommerce store.
 * Version:           1.0.0
 * Author:            Victor Brøgger
 * Author URI:        https://vbroegger.dk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       vayapin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'VAYAPIN_VERSION', '1.0.0' );

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-vayapin-activator.php
 */
function vayapin_connector_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vayapin-activator.php';
	Vayapin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-vayapin-deactivator.php
 */
function vayapin_connector_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vayapin-deactivator.php';
	Vayapin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'vayapin_connector_activate' );
register_deactivation_hook( __FILE__, 'vayapin_connector_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-vayapin.php';

/**
 * Adding the Vayapin settings page to the Wordpress admin panel
 */
require_once 'framework/settings.php';
require_once 'framework/vayapin/api-methods.php';
require_once 'framework/backend-ui/admin-ui.php';
require_once 'framework/frontend-ui/checkout-ui.php';
require_once 'framework/styles-scripts/styles-scripts.php';
require_once 'framework/ajax-methods.php';
require_once 'framework/woocommerce/generate-label.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function vayapin_connector_run() {

	$plugin = new Vayapin();
	$plugin->run();

}

vayapin_connector_run();
