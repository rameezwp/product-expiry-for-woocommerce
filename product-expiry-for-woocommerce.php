<?php
/*
 * Plugin Name: Product Expiry for WooCommerce
 * Plugin URI: https://webcodingplace.com/product-expiry-for-woocommerce/
 * Description: Provide expiry date for your products and get notified before expire
 * Version: 3.5
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
define( 'WOOPE_VERSION', '3.5' );

// Shared top-level admin menu slug (Pro attaches its pages to this).
define( 'WOOPE_MENU_SLUG', 'product-expiry' );

/*
 * Upgrade promo — shown in the Pro upsell banner on the settings page.
 * TODO: replace WOOPE_PROMO_CODE / WOOPE_PROMO_URL with the real coupon
 * code and checkout URL before release.
 */
if ( ! defined( 'WOOPE_PROMO_CODE' ) ) {
    define( 'WOOPE_PROMO_CODE', 'SAVE50' );      // placeholder — confirm real code
}
if ( ! defined( 'WOOPE_PROMO_DISCOUNT' ) ) {
    define( 'WOOPE_PROMO_DISCOUNT', '50%' );
}
if ( ! defined( 'WOOPE_PROMO_URL' ) ) {
    define( 'WOOPE_PROMO_URL', 'https://webcodingplace.com/product-expiry-for-woocommerce/?utm_source=free-plugin&utm_medium=settings&utm_campaign=coupon50' );
}

require_once WOOPE_PATH . 'includes/class-plugin.php';

function woope_init_plugin() {
    return \WOOPE\Plugin::instance();
}

add_action( 'plugins_loaded', 'woope_init_plugin' );

