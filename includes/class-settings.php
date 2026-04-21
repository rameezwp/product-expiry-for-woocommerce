<?php
namespace WOOPE;

if ( ! defined( 'ABSPATH' ) ) exit;

class Settings {

    private $settings = null;

    public function get( $key = null ) {

        if ( $this->settings === null ) {

            $defaults = [
                'date_format'        => get_option('date_format'),
                'display'            => 'enable',
                'orderdetails'       => 'disable',
                'orderdetailsadmin'  => 'disable',
                'markup'             => __( 'Expiry Date: %date%', 'product-expiry-for-woocommerce' ),
            ];

            $this->settings = wp_parse_args(
                get_option( 'woope_admin_settings', [] ),
                $defaults
            );
        }

        if ( $key ) {
            return $this->settings[$key] ?? null;
        }

        return $this->settings;
    }
}