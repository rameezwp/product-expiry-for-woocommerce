<?php
namespace WOOPE;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Expired_Status
 *
 * Implements the "Mark as Expired" on-expiry action: an alternative to
 * Draft / Out of stock that keeps the product visible but shows an
 * "Expired" badge and disables add-to-cart.
 *
 * Entirely opt-in per product (the merchant must pick the new action), so
 * existing products using any other action are unchanged. All behaviour is
 * computed dynamically via woope_is_product_expired(); no destructive data
 * changes are made.
 */
class Expired_Status {

    public function __construct() {

        // Disable purchasing once expired.
        add_filter( 'woocommerce_is_purchasable', [ $this, 'filter_is_purchasable' ], 10, 2 );
        add_filter( 'woocommerce_variation_is_purchasable', [ $this, 'filter_variation_is_purchasable' ], 10, 2 );

        // Replace availability text on the single product page.
        add_filter( 'woocommerce_get_availability', [ $this, 'filter_availability' ], 10, 2 );

        // Badges (deferred until the WP context is ready, like the free Frontend class).
        add_action( 'wp', [ $this, 'register_badge_hooks' ] );

        // Badge styles.
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
    }

    /* -------------------------------------------------------------
     *  PURCHASABILITY
     * ----------------------------------------------------------- */

    public function filter_is_purchasable( $purchasable, $product ) {
        return woope_is_product_expired( $product ) ? false : $purchasable;
    }

    public function filter_variation_is_purchasable( $purchasable, $variation ) {
        return woope_is_product_expired( $variation ) ? false : $purchasable;
    }

    public function filter_availability( $availability, $product ) {

        if ( woope_is_product_expired( $product ) ) {
            $availability['availability'] = $this->badge_text();
            $availability['class']        = 'woope-expired';
        }

        return $availability;
    }

    /* -------------------------------------------------------------
     *  BADGES
     * ----------------------------------------------------------- */

    public function register_badge_hooks() {

        $settings = Plugin::instance()->settings;

        // Single product badge — position configurable in settings.
        $single_hook = $settings->get( 'expired_badge_single_hook' );
        $single_hook = $single_hook ? $single_hook : 'woocommerce_single_product_summary';
        add_action( $single_hook, [ $this, 'single_badge' ] );

        // Shop / archive loop badge — position configurable (empty = hide on archives).
        $archive_hook = $settings->get( 'expired_badge_archive_hook' );
        if ( ! empty( $archive_hook ) ) {
            add_action( $archive_hook, [ $this, 'loop_badge' ] );
        }
    }

    public function loop_badge() {

        global $product;

        if ( woope_is_product_expired( $product ) ) {
            echo '<span class="woope-expired-badge">' . esc_html( $this->badge_text() ) . '</span>';
        }
    }

    public function single_badge() {

        global $product;

        if ( woope_is_product_expired( $product ) ) {
            echo '<p class="woope-expired-badge woope-expired-badge--single">' . esc_html( $this->badge_text() ) . '</p>';
        }
    }

    /* -------------------------------------------------------------
     *  HELPERS
     * ----------------------------------------------------------- */

    private function badge_text() {

        $text = Plugin::instance()->settings->get( 'expired_badge_text' );

        if ( $text === null || $text === '' ) {
            $text = __( 'Expired', 'product-expiry-for-woocommerce' );
        }

        return $text;
    }

    public function enqueue_styles() {

        if (
            ! function_exists( 'is_shop' ) ||
            ! ( is_shop() || is_product() || is_product_category() || is_product_tag() )
        ) {
            return;
        }

        wp_enqueue_style(
            'woope-front-style',
            WOOPE_URL . 'assets/css/front.css',
            [],
            WOOPE_VERSION
        );

        // Apply the configurable badge colour, if a valid hex is set.
        $color = Plugin::instance()->settings->get( 'expired_badge_color' );

        if ( $color && preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $color ) ) {
            wp_add_inline_style(
                'woope-front-style',
                '.woope-expired-badge{background:' . $color . ';}'
            );
        }
    }
}
