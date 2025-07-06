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
const VERSION     = '0.1.11';
const PLUGIN_FILE = __FILE__;


function njbc_enqueue_assets() {
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_enqueue_style( 'njbc-style', $plugin_url . 'build/style.css', [], VERSION );
    wp_enqueue_script( 'njbc-script', $plugin_url . 'build/main.js', [], VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'njbc_enqueue_assets' );

function my_custom_css_plugin_enqueue_scripts() {
    wp_enqueue_style(
        'my-custom-css',
        plugins_url( 'build/style.css', __FILE__ ),
        array(),
        filemtime( plugin_dir_path( __FILE__ ) . 'build/style.css' )
    );
}
add_action( 'enqueue_block_editor_assets', 'my_custom_css_plugin_enqueue_scripts' );

// Include the main plugin class.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-njb-customizations.php';

// Initialize the plugin.
function run_njb_customizations() {
    $plugin = new NJB_Customizations();
    $plugin->run();
}
add_action( 'plugins_loaded', 'run_njb_customizations' );

add_action( 'acf/init', 'set_acf_settings' );
function set_acf_settings() {
    acf_update_setting( 'enable_shortcode', true );
}
add_filter('acf/settings/remove_wp_meta_box', '__return_false');