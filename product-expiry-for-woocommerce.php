<?php
/*
 * Plugin Name: Product Expiry for WooCommerce
 * Plugin URI: https://webcodingplace.com/product-expiry-for-woocommerce/
 * Description: Provide expiry date for your products and get notified before expire
 * Version: 3.2
 * Author: WebCodingPlace
 * Author URI: https://webcodingplace.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: product-expiry-for-woocommerce
 * Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WOOPE_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOOPE_URL', plugin_dir_url( __FILE__ ) );
define( 'WOOPE_VERSION', '3.2' );

require_once WOOPE_PATH . 'includes/class-plugin.php';

function woope_init_plugin() {
    return \WOOPE\Plugin::instance();
}

add_action( 'plugins_loaded', 'woope_init_plugin' );

