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

        // Variable products: when the parent has no expiry of its own, optionally
        // roll up the earliest variation expiry as a summary. Opt-in and off by
        // default, so existing stores see no change. A variation's own date still
        // governs that variation once selected (variation_data() + front.js).
        if (
            empty( $expiry_date ) &&
            empty( $expiry_note ) &&
            $product->is_type( 'variable' ) &&
            $settings->get( 'show_earliest_variation' ) === 'enable'
        ) {
            $expiry_date = woope_get_earliest_variation_expiry( $product );
        }

        // If note exists → override
        if ( ! empty( $expiry_note ) ) {
            $text = $expiry_note;
        } else {
            $text = woope_format_expiry_text(
                $expiry_date,
                $product->get_id()
            );
        }

        // Apply the "after expiry" display rule (show / hide / custom text).
        $text = $this->apply_expiry_visibility( $text, $product->get_id(), $expiry_date );

        if ($text) {
            echo '<p class="woope-notice">' . wp_kses_post( $text ) . '</p>';
        }

        // Placeholder for variation dynamic text
        if ( $product->is_type( 'variable' ) ) {
            echo '<p class="woope-variable-notice"></p>';
        }
    }

    /**
     * Apply the "After Expiry" display setting to already-prepared expiry text.
     *
     * Returns the text unchanged unless a real date has passed, in which case
     * it is kept (show), blanked (hide), or replaced with custom text. Default
     * is "show", so existing stores see no change.
     *
     * @param string $text    Prepared expiry text (formatted date or note).
     * @param int    $post_id Product/variation ID.
     * @param string $date    Raw Y-m-d expiry date.
     * @return string
     */
    private function apply_expiry_visibility( $text, $post_id, $date ) {

        if ( empty( $date ) || ! woope_expiry_has_passed( $post_id, $date ) ) {
            return $text;
        }

        $settings = Plugin::instance()->settings;
        $mode     = $settings->get( 'expired_date_display' );

        if ( $mode === 'hide' ) {
            return '';
        }

        if ( $mode === 'custom' ) {
            $custom = $settings->get( 'expired_date_custom_text' );
            return ( $custom !== null && $custom !== '' ) ? $custom : $text;
        }

        // 'show' (default) — leave unchanged.
        return $text;
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

        // Apply the "after expiry" display rule for the selected variation.
        $variation['woope_text'] = $this->apply_expiry_visibility(
            $variation['woope_text'],
            $variation_id,
            $expiry_date
        );

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

        // Apply the "after expiry" display rule.
        $text = $this->apply_expiry_visibility( $text, $product->get_id(), $expiry_date );

        if ( $text === '' ) {
            return '';
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