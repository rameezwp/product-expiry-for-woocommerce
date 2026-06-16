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