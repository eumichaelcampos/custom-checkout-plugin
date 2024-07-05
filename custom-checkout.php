<?php
/*
Plugin Name: Custom Checkout
Description: Plugin para controlar as informações do checkout no WooCommerce.
Version: 1.0
Author: Michael
License: GPLv2 or later
Text Domain: custom-checkout
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'CUSTOM_CHECKOUT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CUSTOM_CHECKOUT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once CUSTOM_CHECKOUT_PLUGIN_DIR . 'includes/class-custom-checkout.php';
require_once CUSTOM_CHECKOUT_PLUGIN_DIR . 'includes/class-shortcode-handler.php';
require_once CUSTOM_CHECKOUT_PLUGIN_DIR . 'includes/class-file-upload-handler.php';

function custom_checkout_init() {
    Custom_Checkout::get_instance();
    Shortcode_Handler::get_instance();
    File_Upload_Handler::get_instance();
}
add_action( 'plugins_loaded', 'custom_checkout_init' );

function custom_checkout_load_textdomain() {
    load_plugin_textdomain( 'custom-checkout', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'custom_checkout_load_textdomain' );
