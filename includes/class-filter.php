<?php
namespace WOOPE;

if ( ! defined( 'ABSPATH' ) ) exit;

class Filter_Admin {

    public function __construct() {

        // Dropdown in admin
        add_action(
            'restrict_manage_posts',
            [ $this, 'add_dropdown' ]
        );

        // Modify query
        add_action(
            'pre_get_posts',
            [ $this, 'modify_query' ]
        );
    }

    /* -------------------------------------------------------------
     *  DROPDOWN
     * ----------------------------------------------------------- */

    public function add_dropdown( $post_type ) {

        if ( $post_type !== 'product' ) {
            return;
        }

        $selected = $_GET['expiry_period'] ?? '';

        ?>
        <select name="expiry_period" id="filter-by-expiry">
            <option value="">
                <?php _e( 'Filter by Expiry', 'product-expiry-for-woocommerce' ); ?>
            </option>
            <option value="within_7_days" <?php selected( $selected, 'within_7_days' ); ?>>
                <?php _e( 'Expiring within 7 Days', 'product-expiry-for-woocommerce' ); ?>
            </option>
            <option value="within_30_days" <?php selected( $selected, 'within_30_days' ); ?>>
                <?php _e( 'Expiring within 30 Days', 'product-expiry-for-woocommerce' ); ?>
            </option>
            <option value="this_month" <?php selected( $selected, 'this_month' ); ?>>
                <?php _e( 'Expiring this Month', 'product-expiry-for-woocommerce' ); ?>
            </option>
            <option value="next_month" <?php selected( $selected, 'next_month' ); ?>>
                <?php _e( 'Expiring next Month', 'product-expiry-for-woocommerce' ); ?>
            </option>
            <option value="three_months" <?php selected( $selected, 'three_months' ); ?>>
                <?php _e( 'Expiring within 3 Months', 'product-expiry-for-woocommerce' ); ?>
            </option>
            <option value="six_months" <?php selected( $selected, 'six_months' ); ?>>
                <?php _e( 'Expiring within 6 Months', 'product-expiry-for-woocommerce' ); ?>
            </option>
            <option value="expired" <?php selected( $selected, 'expired' ); ?>>
                <?php _e( 'Already Expired', 'product-expiry-for-woocommerce' ); ?>
            </option>
        </select>
        <?php
    }

    /* -------------------------------------------------------------
     *  MODIFY QUERY
     * ----------------------------------------------------------- */

    public function modify_query( $query ) {

        if (
            ! is_admin() ||
            ! $query->is_main_query() ||
            $query->get( 'post_type' ) !== 'product'
        ) {
            return;
        }

        if ( empty( $_GET['expiry_period'] ) ) {
            return;
        }

        global $wpdb;

        $period = sanitize_text_field( $_GET['expiry_period'] );
        $today  = current_time( 'Y-m-d' );

        switch ( $period ) {

            case 'within_7_days':
                $start = $today;
                $end   = date( 'Y-m-d', strtotime( '+7 days', strtotime( $today ) ) );
                break;

            case 'within_30_days':
                $start = $today;
                $end   = date( 'Y-m-d', strtotime( '+30 days', strtotime( $today ) ) );
                break;

            case 'this_month':
                $start = $today;
                $end   = date( 'Y-m-t', strtotime( $today ) );
                break;

            case 'next_month':
                $start = date( 'Y-m-01', strtotime( '+1 month' ) );
                $end   = date( 'Y-m-t', strtotime( '+1 month' ) );
                break;

            case 'three_months':
                $start = $today;
                $end   = date( 'Y-m-t', strtotime( '+3 months' ) );
                break;

            case 'six_months':
                $start = $today;
                $end   = date( 'Y-m-t', strtotime( '+6 months' ) );
                break;

            case 'expired':
                $start = null;
                $end   = $today;
                break;

            default:
                return;
        }

        /*
         * Simple product matches
         */
        if ( $period === 'expired' ) {

            $simple_ids = $wpdb->get_col( $wpdb->prepare("
                SELECT pm.post_id
                FROM {$wpdb->postmeta} pm
                INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
                WHERE pm.meta_key = 'woo_expiry_date'
                AND pm.meta_value <= %s
                AND p.post_type = 'product'
                AND p.post_status IN ( 'publish', 'draft' )
            ", $end ) );

        } else {

            $simple_ids = $wpdb->get_col( $wpdb->prepare("
                SELECT pm.post_id
                FROM {$wpdb->postmeta} pm
                INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
                WHERE pm.meta_key = 'woo_expiry_date'
                AND pm.meta_value BETWEEN %s AND %s
                AND p.post_type = 'product'
                AND p.post_status = 'publish'
            ", $start, $end ) );
        }

        /*
         * Variation matches (get parent IDs)
         */
        if ( $period === 'expired' ) {

            $parent_ids = $wpdb->get_col( $wpdb->prepare("
                SELECT DISTINCT p.post_parent
                FROM {$wpdb->postmeta} pm
                INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
                WHERE pm.meta_key = 'woo_expiry_date'
                AND pm.meta_value <= %s
                AND p.post_type = 'product_variation'
                AND p.post_status IN ( 'publish', 'draft' )
            ", $end ) );

        } else {

            $parent_ids = $wpdb->get_col( $wpdb->prepare("
                SELECT DISTINCT p.post_parent
                FROM {$wpdb->postmeta} pm
                INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
                WHERE pm.meta_key = 'woo_expiry_date'
                AND pm.meta_value BETWEEN %s AND %s
                AND p.post_type = 'product_variation'
                AND p.post_status = 'publish'
            ", $start, $end ) );
        }

        /*
         * Merge
         */
        $all_ids = array_unique(
            array_merge( $simple_ids, $parent_ids )
        );

        /*
         * Apply filter
         */
        if ( ! empty( $all_ids ) ) {
            $query->set( 'post__in', $all_ids );
        } else {
            $query->set( 'post__in', [ 0 ] );
        }
    }
}