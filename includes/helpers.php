<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function woope_format_expiry_text( $date, $product_id = null ) {

    if ( ! $date ) {
        return '';
    }

    $settings = \WOOPE\Plugin::instance()->settings;

    $format = $settings->get( 'date_format' );
    $timestamp = wc_string_to_timestamp( $date );
    $formatted_date = wp_date( $format, $timestamp );

    /*
     * IMPORTANT:
     * WPML does not support %date%
     * So we temporarily convert to {expiry_date}
     */

    $markup = $settings->get( 'markup' );

    // Replace %date% with temporary placeholder
    $markup = str_replace( '%date%', '{expiry_date}', $markup );

    // WPML translate the string safely
    $markup = apply_filters(
        'wpml_translate_single_string',
        $markup,
        'product-expiry-for-woocommerce',
        'date-markup'
    );

    // Replace placeholder with real formatted date
    $markup = str_replace( '{expiry_date}', $formatted_date, $markup );

    /*
     * Allow developers / Pro version to modify final markup
     */
    return apply_filters(
        'product_expiry_text_markup',
        $markup,
        $product_id,
        $formatted_date
    );
}

/**
 * Earliest expiry date among a variable product's variations.
 *
 * Returns the soonest (chronologically earliest) child expiry date as a
 * raw Y-m-d string, or '' when no variation has a date. Expiry dates are
 * stored as Y-m-d, so a plain string comparison is also a date comparison.
 *
 * Precedence note: a variation's own date always governs that variation.
 * This helper only produces a parent-level summary for variable products
 * that have no expiry date of their own.
 *
 * @param int|\WC_Product $product Product ID or object.
 * @return string Y-m-d date, or '' if none.
 */
function woope_get_earliest_variation_expiry( $product ) {

    if ( is_numeric( $product ) ) {
        $product = wc_get_product( $product );
    }

    if ( ! $product || ! is_a( $product, 'WC_Product' ) || ! $product->is_type( 'variable' ) ) {
        return '';
    }

    $children = $product->get_children();
    if ( empty( $children ) ) {
        return '';
    }

    // Warm the meta cache so the loop below does not fire one query per child.
    update_meta_cache( 'post', $children );

    $earliest = '';

    foreach ( $children as $child_id ) {

        $date = get_post_meta( $child_id, 'woo_expiry_date', true );
        if ( empty( $date ) ) {
            continue;
        }

        if ( $earliest === '' || strcmp( $date, $earliest ) < 0 ) {
            $earliest = $date;
        }
    }

    return apply_filters( 'woope_earliest_variation_expiry', $earliest, $product );
}

/**
 * Resolve the final expiry Unix timestamp for a product/variation.
 *
 * Respects Pro's time-aware resolver via the woope_resolve_expiry_timestamp
 * filter; otherwise mirrors the free scheduler's end-of-day, site-timezone
 * fallback. Single source of truth for "when does this expire".
 *
 * @param int         $post_id Product/variation ID.
 * @param string|null $date    Optional Y-m-d date; read from meta when null.
 * @return int|null Unix timestamp, or null when no date is set.
 */
function woope_get_expiry_timestamp( $post_id, $date = null ) {

    if ( $date === null ) {
        $date = get_post_meta( $post_id, 'woo_expiry_date', true );
    }

    if ( empty( $date ) ) {
        return null;
    }

    $timestamp = apply_filters( 'woope_resolve_expiry_timestamp', null, $date, $post_id );

    if ( $timestamp === null ) {
        $tz_string = get_option( 'timezone_string' );
        $tz_string = $tz_string ? $tz_string : 'UTC';
        $datetime  = new \DateTime( $date, new \DateTimeZone( $tz_string ) );
        $datetime->setTime( 23, 59, 59 );
        $timestamp = $datetime->getTimestamp();
    }

    return $timestamp;
}

/**
 * Whether a product's expiry date/time has already passed.
 *
 * Independent of the on-expiry action — purely a "is now past expiry" check.
 *
 * @param int         $post_id Product/variation ID.
 * @param string|null $date    Optional Y-m-d date; read from meta when null.
 * @return bool
 */
function woope_expiry_has_passed( $post_id, $date = null ) {

    $timestamp = woope_get_expiry_timestamp( $post_id, $date );

    return ( $timestamp !== null && $timestamp <= time() );
}

/**
 * Whether a product/variation is currently "expired" under the new
 * "Mark as Expired" on-expiry action.
 *
 * Computed dynamically from the resolved expiry timestamp so it stays
 * correct even if the scheduled cron event was missed. Returns false for
 * every other action value, so products using Nothing/Draft/Out of stock
 * are completely unaffected.
 *
 * @param int|\WC_Product $product Product ID or object.
 * @return bool
 */
function woope_is_product_expired( $product ) {

    if ( is_numeric( $product ) ) {
        $product = wc_get_product( $product );
    }

    if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
        return false;
    }

    $id = $product->get_id();

    if ( get_post_meta( $id, 'woo_expiry_action', true ) !== 'expired' ) {
        return false;
    }

    return woope_expiry_has_passed( $id );
}