<?php
namespace WOOPE;

if ( ! defined( 'ABSPATH' ) ) exit;

class Order {

    public function __construct() {

        // Display in order email
        add_action(
            'woocommerce_order_item_meta_start',
            [ $this, 'display_in_email' ],
            10,
            3
        );

        // Add meta during checkout (for admin order display)
        add_action(
            'woocommerce_checkout_create_order_line_item',
            [ $this, 'add_to_order_item_meta' ],
            10,
            4
        );
    }

    /* -------------------------------------------------------------
     *  ORDER EMAIL DISPLAY
     * ----------------------------------------------------------- */

    public function display_in_email( $item_id, $item, $order ) {

        if ( is_wc_endpoint_url() ) {
            return;
        }

        if ( ! $item->is_type( 'line_item' ) ) {
            return;
        }

        $settings = Plugin::instance()->settings;

        if ( $settings->get('orderdetails') !== 'enable' ) {
            return;
        }

        $variation_id = $item->get_variation_id();
        $product_id   = $variation_id ? $variation_id : $item->get_product_id();

        $expiry_date = get_post_meta(
            $product_id,
            'woo_expiry_date',
            true
        );

        $expiry_note = get_post_meta(
            $product_id,
            'woo_expiry_note',
            true
        );

        if ( empty( $expiry_date ) ) {
            return;
        }

        if ( ! empty( $expiry_note ) ) {
            $text = $expiry_note;
        } else {
            $text = woope_format_expiry_text(
                $expiry_date,
                $product_id
            );
        }

        echo '<div class="woope-notice">' .
             wp_kses_post( $text ) .
             '</div>';
    }

    /* -------------------------------------------------------------
     *  ORDER ADMIN META
     * ----------------------------------------------------------- */

    public function add_to_order_item_meta( $item, $cart_item_key, $values, $order ) {

        $settings = Plugin::instance()->settings;

        if ( $settings->get('orderdetailsadmin') !== 'enable' ) {
            return;
        }

        // ✅ Variation support
        $variation_id = $item->get_variation_id();
        $product_id   = $variation_id ? $variation_id : $item->get_product_id();

        $expiry_date = get_post_meta(
            $product_id,
            'woo_expiry_date',
            true
        );

        $expiry_note = get_post_meta(
            $product_id,
            'woo_expiry_note',
            true
        );

        if ( empty( $expiry_date ) ) {
            return;
        }

        if ( ! empty( $expiry_note ) ) {
            $text = $expiry_note;
        } else {
            $text = woope_format_expiry_text(
                $expiry_date,
                $product_id
            );
        }

        $item->update_meta_data( 'exp', $text );
    }
}