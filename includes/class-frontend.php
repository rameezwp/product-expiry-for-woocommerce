<?php
namespace WOOPE;

if ( ! defined( 'ABSPATH' ) ) exit;

class Frontend {

    public function __construct() {

        // Defer hook registration until WP is ready
        add_action( 'wp', [ $this, 'register_hooks' ] );

        add_filter(
            'woocommerce_available_variation',
            [ $this, 'variation_data' ]
        );

        add_shortcode(
            'expiry_date',
            [ $this, 'shortcode' ]
        );

        add_action(
            'wp_enqueue_scripts',
            [ $this, 'enqueue_scripts' ]
        );
    }

    public function register_hooks() {

        $settings = \WOOPE\Plugin::instance()->settings;

        $single_hook  = $settings->get('single_hook') ? : 'woocommerce_before_add_to_cart_button';
        $archive_hook = $settings->get('archive_hook');

        add_action( $single_hook, [ $this, 'display_expiry_date' ] );

        if ( ! empty( $archive_hook ) ) {
            add_action( $archive_hook, [ $this, 'display_expiry_date' ] );
        }
    }

    /* -------------------------------------------------------------
     *  FRONTEND DISPLAY
     * ----------------------------------------------------------- */

    public function display_expiry_date() {

        $settings = Plugin::instance()->settings;

        if ( $settings->get('display') !== 'enable' ) {
            return;
        }

        $product = wc_get_product();
        if ( ! $product ) {
            return;
        }

        $expiry_date = $product->get_meta( 'woo_expiry_date' );
        $expiry_note = $product->get_meta( 'woo_expiry_note' );

        // If note exists → override
        if ( ! empty( $expiry_note ) ) {
            $text = $expiry_note;
        } else {
            $text = woope_format_expiry_text(
                $expiry_date,
                $product->get_id()
            );
        }

        if ($text) {
            echo '<p class="woope-notice">' . wp_kses_post( $text ) . '</p>';
        }

        // Placeholder for variation dynamic text
        if ( $product->is_type( 'variable' ) ) {
            echo '<p class="woope-variable-notice"></p>';
        }
    }

    /* -------------------------------------------------------------
     *  VARIATION DATA
     * ----------------------------------------------------------- */

    public function variation_data( $variation ) {

        if ( empty( $variation['variation_id'] ) ) {
            return $variation;
        }

        $settings = Plugin::instance()->settings;

        if ( $settings->get('display') !== 'enable' ) {
            return $variation;
        }

        $variation_id = $variation['variation_id'];

        $expiry_date = get_post_meta(
            $variation_id,
            'woo_expiry_date',
            true
        );

        $expiry_note = get_post_meta(
            $variation_id,
            'woo_expiry_note',
            true
        );

        if ( empty( $expiry_date ) ) {
            return $variation;
        }

        if ( ! empty( $expiry_note ) ) {

            $variation['woope_text'] = $expiry_note;

        } else {

            $variation['woope_text'] = woope_format_expiry_text(
                $expiry_date,
                $variation_id
            );
        }

        return $variation;
    }

    /* -------------------------------------------------------------
     *  SHORTCODE
     * ----------------------------------------------------------- */

    public function shortcode( $atts ) {

        $atts = shortcode_atts([
            'before' => '',
            'after'  => '',
        ], $atts );

        $product = wc_get_product();
        if ( ! $product ) {
            return '';
        }

        $expiry_date = $product->get_meta( 'woo_expiry_date' );
        $expiry_note = $product->get_meta( 'woo_expiry_note' );

        if ( empty( $expiry_date ) ) {
            return '';
        }

        if ( ! empty( $expiry_note ) ) {
            $text = $expiry_note;
        } else {
            $text = woope_format_expiry_text(
                $expiry_date,
                $product->get_id()
            );
        }

        return esc_html( $atts['before'] ) .
               wp_kses_post( $text ) .
               esc_html( $atts['after'] );
    }

    /* -------------------------------------------------------------
     *  SCRIPTS
     * ----------------------------------------------------------- */

    public function enqueue_scripts() {

        if ( is_product() ) {

            wp_enqueue_script(
                'woope-front',
                WOOPE_URL . 'assets/js/front.js',
                [ 'jquery' ],
                WOOPE_VERSION,
                true
            );
        }
    }
}