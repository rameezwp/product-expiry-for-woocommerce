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