<?php
namespace WOOPE;

if ( ! defined( 'ABSPATH' ) ) exit;

class Scheduler {

    public function __construct() {
        add_action( 'woo_expiry_schedule_action', [ $this, 'execute' ], 10, 1 );
        $this->register_notification_handler();
    }

    public function schedule( $post_id, $date ) {

        if ( empty( $date ) ) {
            return;
        }

        $timestamp = $this->resolve_expiry_timestamp( $date, $post_id );

        wp_clear_scheduled_hook(
            'woo_expiry_schedule_action',
            [ $post_id ]
        );

        wp_schedule_single_event(
            $timestamp,
            'woo_expiry_schedule_action',
            [ $post_id ]
        );

        // Keep the cached "expired" flag in step with the (re)scheduled date.
        // execute() flips it back to 'yes' when the event actually fires.
        update_post_meta(
            $post_id,
            'woo_expiry_expired_flag',
            $timestamp > time() ? 'no' : 'yes'
        );
    }

    public function execute( $post_id ) {

        $action = get_post_meta( $post_id, 'woo_expiry_action', true );

        if ( $action === 'draft' ) {

            wp_update_post([
                'ID' => $post_id,
                'post_status' => 'draft'
            ]);
        }

        if ( $action === 'out' ) {

            update_post_meta( $post_id, '_stock', 0 );
            update_post_meta( $post_id, '_stock_status', 'outofstock' );
            wp_set_post_terms( $post_id, 'outofstock', 'product_visibility', true );
        }

        if ( $action === 'reduce' ) {

            // Lower the managed stock by the configured amount using WC CRUD so
            // the product meta lookup table (and HPOS) stay in sync.
            $product = wc_get_product( $post_id );

            if ( $product && $product->managing_stock() ) {

                $qty = (int) get_post_meta( $post_id, 'woo_expiry_reduce_qty', true );

                if ( $qty > 0 ) {
                    $current = (int) $product->get_stock_quantity();
                    $product->set_stock_quantity( max( 0, $current - $qty ) );
                    $product->save();
                }
            }
        }

        if ( $action === 'expired' ) {

            // Keep the product published and visible; the badge + disabled
            // add-to-cart are applied dynamically by woope_is_product_expired().
            // The flag is a cache for queries/integrations, not load-bearing.
            update_post_meta( $post_id, 'woo_expiry_expired_flag', 'yes' );
        }

        /*
         * EMAIL NOTIFICATION
         */
        $settings = \WOOPE\Plugin::instance()->settings->get();

        if ( ! empty( $settings['notify_emails'] ) ) {

            do_action(
                'woope_handle_expiry_notification',
                $post_id,
                $action
            );
        }

        /*
         * Keep extension hook
         */
        do_action( 'woope_after_expiry_triggered', $post_id );
    }

    public function register_notification_handler() {

        add_action(
            'woope_handle_expiry_notification',
            [ $this, 'send_expiry_email' ],
            10,
            2
        );
    }

    public function send_expiry_email( $post_id, $action ) {

        $settings = \WOOPE\Plugin::instance()->settings->get();

        if (
            empty( $settings['notify_emails'] ) ||
            ( isset( $settings['notify_on_expired'] ) &&
              $settings['notify_on_expired'] === 'disable' )
        ) {
            return;
        }

        $date = get_post_meta( $post_id, 'woo_expiry_date', true );

        $site_title  = get_bloginfo();
        $admin_email = apply_filters(
            'woope_admin_email',
            get_bloginfo( 'admin_email' )
        );

        $from_title = apply_filters(
            'woope_email_sender_title',
            $site_title
        );

        $from_email = apply_filters(
            'woope_email_sender_email',
            $admin_email
        );

        $headers = [
            "From: {$from_title} <{$from_email}>",
            "Content-Type: text/html",
            "MIME-Version: 1.0",
        ];

        $headers = apply_filters(
            'woope_email_headers',
            $headers
        );

        $formatted_date = woope_format_expiry_text(
            $date,
            $post_id
        );

        $subject_template = ! empty( $settings['email_subject'] )
            ? $settings['email_subject']
            : 'Product {product_name} expired';

        $body_template = ! empty( $settings['email_body'] )
            ? $settings['email_body']
            : 'Product {product_name} expired on {expiry_date}.';

        $replacements = [
            '{product_name}' => get_the_title( $post_id ),
            '{expiry_date}'  => $formatted_date,
            '{product_url}'  => get_permalink( $post_id ),
        ];

        $subject = str_replace(
            array_keys( $replacements ),
            array_values( $replacements ),
            $subject_template
        );

        $message = str_replace(
            array_keys( $replacements ),
            array_values( $replacements ),
            $body_template
        );

        $sent = wp_mail(
            $settings['notify_emails'],
            $subject,
            $message,
            $headers
        );

        woope_log_email(
            $settings['notify_emails'],
            $subject,
            __( 'On-expiry (admin)', 'product-expiry-for-woocommerce' ),
            $sent ? 'sent' : 'failed'
        );
    }

    private function resolve_expiry_timestamp( $date_string, $post_id ) {

        /*
         * Allow Pro version to override entire timestamp logic
         */
        $custom = apply_filters(
            'woope_resolve_expiry_timestamp',
            null,
            $date_string,
            $post_id
        );

        if ( $custom !== null ) {
            return $custom;
        }

        /*
         * FREE VERSION:
         * Expire at 11:59:59 PM site timezone
         */

        $tz_string = get_option( 'timezone_string' );
        $tz_string = $tz_string ? $tz_string : 'UTC';

        $timezone = new \DateTimeZone( $tz_string );

        $datetime = new \DateTime( $date_string, $timezone );

        // Set to end of day
        $datetime->setTime( 23, 59, 59 );

        return $datetime->getTimestamp();
    }
}