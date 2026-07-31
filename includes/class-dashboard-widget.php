<?php
namespace WOOPE;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Dashboard_Widget
 *
 * A lightweight "Product Expiry Overview" widget on the WordPress dashboard.
 * Shows at-a-glance counts of products that are already expired, expiring
 * within 7 days, and expiring within 30 days, each linking straight to the
 * matching pre-filtered products list (via the existing Filter_Admin dropdown).
 *
 * Read-only and admin-facing: it only reports counts already visible on the
 * products screen, so it introduces no new data exposure. Counts are cached in
 * a transient (cleared on product save / expiry) to keep the dashboard fast on
 * large catalogs. Nothing in the existing hook / meta / option contract changes.
 */
class Dashboard_Widget {

    const TRANSIENT = 'woope_expiry_overview_counts';

    public function __construct() {

        add_action( 'wp_dashboard_setup', [ $this, 'register_widget' ] );

        // Keep the cached counts fresh after any product/expiry change.
        add_action( 'woocommerce_process_product_meta',   [ $this, 'clear_cache' ] );
        add_action( 'woocommerce_save_product_variation',  [ $this, 'clear_cache' ] );
        add_action( 'woocommerce_product_quick_edit_save', [ $this, 'clear_cache' ] );
        add_action( 'woo_expiry_schedule_action',          [ $this, 'clear_cache' ] );
    }

    /* -------------------------------------------------------------
     *  REGISTRATION
     * ----------------------------------------------------------- */

    public function register_widget() {

        // Visible to anyone who manages products (matches the products list
        // and its expiry filter). Aggregate counts only — no settings data.
        if ( ! current_user_can( 'edit_products' ) ) {
            return;
        }

        wp_add_dashboard_widget(
            'woope_expiry_overview',
            __( 'Product Expiry Overview', 'product-expiry-for-woocommerce' ),
            [ $this, 'render_widget' ]
        );
    }

    /* -------------------------------------------------------------
     *  RENDER
     * ----------------------------------------------------------- */

    public function render_widget() {

        $counts = $this->get_counts();

        $rows = [
            'expired' => [
                'label'  => __( 'Already expired', 'product-expiry-for-woocommerce' ),
                'count'  => $counts['expired'],
                'period' => 'expired',
                'accent' => '#d63638',
            ],
            'within_7' => [
                'label'  => __( 'Expiring within 7 days', 'product-expiry-for-woocommerce' ),
                'count'  => $counts['within_7'],
                'period' => 'within_7_days',
                'accent' => '#dba617',
            ],
            'within_30' => [
                'label'  => __( 'Expiring within 30 days', 'product-expiry-for-woocommerce' ),
                'count'  => $counts['within_30'],
                'period' => 'within_30_days',
                'accent' => '#2271b1',
            ],
        ];
        ?>
        <style>
            .woope-overview{margin:0;}
            .woope-overview li{display:flex;align-items:center;justify-content:space-between;
                padding:10px 0;border-bottom:1px solid #f0f0f1;margin:0;}
            .woope-overview li:last-child{border-bottom:0;}
            .woope-overview a{display:flex;align-items:center;justify-content:space-between;
                width:100%;text-decoration:none;color:inherit;}
            .woope-overview .woope-ov-label{display:flex;align-items:center;gap:8px;}
            .woope-overview .woope-ov-dot{width:10px;height:10px;border-radius:50%;flex:0 0 10px;}
            .woope-overview .woope-ov-count{font-weight:600;font-size:15px;min-width:32px;text-align:right;}
            .woope-overview-foot{margin:12px 0 0;color:#646970;}
        </style>
        <ul class="woope-overview">
            <?php foreach ( $rows as $row ) :
                $url = admin_url( 'edit.php?post_type=product&expiry_period=' . $row['period'] );
            ?>
                <li>
                    <a href="<?php echo esc_url( $url ); ?>">
                        <span class="woope-ov-label">
                            <span class="woope-ov-dot" style="background:<?php echo esc_attr( $row['accent'] ); ?>;"></span>
                            <?php echo esc_html( $row['label'] ); ?>
                        </span>
                        <span class="woope-ov-count"><?php echo esc_html( number_format_i18n( $row['count'] ) ); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="woope-overview-foot">
            <?php
            printf(
                /* translators: %s: link to the Product Expiry settings screen. */
                esc_html__( 'Manage how expiry behaves in %s.', 'product-expiry-for-woocommerce' ),
                '<a href="' . esc_url( admin_url( 'admin.php?page=' . WOOPE_MENU_SLUG ) ) . '">' .
                    esc_html__( 'Product Expiry settings', 'product-expiry-for-woocommerce' ) . '</a>'
            );
            ?>
        </p>
        <?php
    }

    /* -------------------------------------------------------------
     *  COUNTS
     * ----------------------------------------------------------- */

    /**
     * Return the three overview counts, cached for an hour.
     *
     * @return array{expired:int,within_7:int,within_30:int}
     */
    private function get_counts() {

        $cached = get_transient( self::TRANSIENT );

        if ( is_array( $cached ) &&
             isset( $cached['expired'], $cached['within_7'], $cached['within_30'] ) ) {
            return $cached;
        }

        $today = current_time( 'Y-m-d' );

        $counts = [
            'expired'   => $this->count_expired( $today ),
            'within_7'  => $this->count_between( $today, gmdate( 'Y-m-d', strtotime( $today . ' +7 days' ) ) ),
            'within_30' => $this->count_between( $today, gmdate( 'Y-m-d', strtotime( $today . ' +30 days' ) ) ),
        ];

        set_transient( self::TRANSIENT, $counts, HOUR_IN_SECONDS );

        return $counts;
    }

    /**
     * Count distinct products (simple + variable parents) with an expiry date
     * on or before $today. Mirrors the "Already Expired" admin filter, minus
     * the empty-date rows that a plain string comparison would otherwise match.
     */
    private function count_expired( $today ) {

        global $wpdb;

        $sql = "
            SELECT COUNT(*) FROM (
                SELECT p.ID AS id
                FROM {$wpdb->postmeta} pm
                INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
                WHERE pm.meta_key = 'woo_expiry_date'
                  AND pm.meta_value <> ''
                  AND pm.meta_value <= %s
                  AND p.post_type = 'product'
                  AND p.post_status IN ( 'publish', 'draft' )
                UNION
                SELECT p.post_parent AS id
                FROM {$wpdb->postmeta} pm
                INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
                WHERE pm.meta_key = 'woo_expiry_date'
                  AND pm.meta_value <> ''
                  AND pm.meta_value <= %s
                  AND p.post_type = 'product_variation'
                  AND p.post_status IN ( 'publish', 'draft' )
            ) t
        ";

        return (int) $wpdb->get_var( $wpdb->prepare( $sql, $today, $today ) );
    }

    /**
     * Count distinct published products (simple + variable parents) whose
     * expiry date falls between $start and $end (inclusive). Mirrors the
     * "Expiring within N days" admin filters.
     */
    private function count_between( $start, $end ) {

        global $wpdb;

        $sql = "
            SELECT COUNT(*) FROM (
                SELECT p.ID AS id
                FROM {$wpdb->postmeta} pm
                INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
                WHERE pm.meta_key = 'woo_expiry_date'
                  AND pm.meta_value BETWEEN %s AND %s
                  AND p.post_type = 'product'
                  AND p.post_status = 'publish'
                UNION
                SELECT p.post_parent AS id
                FROM {$wpdb->postmeta} pm
                INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
                WHERE pm.meta_key = 'woo_expiry_date'
                  AND pm.meta_value BETWEEN %s AND %s
                  AND p.post_type = 'product_variation'
                  AND p.post_status = 'publish'
            ) t
        ";

        return (int) $wpdb->get_var( $wpdb->prepare( $sql, $start, $end, $start, $end ) );
    }

    /* -------------------------------------------------------------
     *  CACHE
     * ----------------------------------------------------------- */

    public function clear_cache() {
        delete_transient( self::TRANSIENT );
    }
}
