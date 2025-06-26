<?php
/**
 * Plugin Name:     Njb Customizations
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     njb-customizations
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Njb_Customizations
 */

// Your code starts here.

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'NJB_PLUGIN_FILE' ) ) {
	define( 'NJB_PLUGIN_FILE', __FILE__ );
}

// Define constants.
const VERSION     = '0.1.0';
const PLUGIN_FILE = __FILE__;

// Include the main plugin class.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-njb-customizations.php';

// Initialize the plugin.
function run_njb_customizations() {
    $plugin = new NJB_Customizations();
    $plugin->run();
}
add_action( 'plugins_loaded', 'run_njb_customizations' );