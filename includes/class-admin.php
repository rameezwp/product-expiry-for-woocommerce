<?php
namespace WOOPE;

if ( ! defined( 'ABSPATH' ) ) exit;

class Admin {

    public function __construct() {

        // Settings submenu
        add_action(
            'admin_menu',
            [ $this, 'add_settings_page' ]
        );

        // AJAX save
        add_action(
            'wp_ajax_woope_save_admin_settings',
            [ $this, 'save_settings' ]
        );

        // Admin scripts
        add_action(
            'admin_enqueue_scripts',
            [ $this, 'enqueue_scripts' ]
        );

        // Plugin quick settings link
        add_filter(
            'plugin_action_links',
            [ $this, 'add_settings_link' ],
            10,
            2
        );

        // Loyalty upsell: record first-use time and handle persistent dismissal.
        add_action( 'admin_init', [ $this, 'maybe_record_install_time' ] );
        add_action( 'admin_init', [ $this, 'handle_promo_dismiss' ] );
    }

    /* -------------------------------------------------------------
     *  LOYALTY UPSELL (tenure-based, persistently dismissable)
     * ----------------------------------------------------------- */

    /**
     * Record the first time the plugin ran, so we can measure how long the
     * store has been using it. Set once; never overwritten.
     */
    public function maybe_record_install_time() {
        if ( ! get_option( 'woope_install_time' ) ) {
            add_option( 'woope_install_time', time() );
        }
    }

    /**
     * Current usage milestone: 'one_year', 'six_months', or '' (too new).
     */
    public function get_usage_milestone() {

        $install = (int) get_option( 'woope_install_time', 0 );
        if ( ! $install ) {
            return '';
        }

        $elapsed = time() - $install;

        if ( $elapsed >= YEAR_IN_SECONDS ) {
            return 'one_year';
        }

        if ( $elapsed >= 6 * MONTH_IN_SECONDS ) {
            return 'six_months';
        }

        return '';
    }

    /**
     * Whether to show the loyalty promo to the current user: only on the free
     * plugin, only once a milestone is reached, and not if already dismissed
     * for that milestone (so it can re-appear when the next milestone hits).
     */
    public function should_show_promo() {

        if ( defined( 'WOOPE_PRO_VERSION' ) ) {
            return false;
        }

        $milestone = $this->get_usage_milestone();
        if ( ! $milestone ) {
            return false;
        }

        $dismissed = get_user_meta( get_current_user_id(), 'woope_promo_dismissed_milestone', true );

        return $dismissed !== $milestone;
    }

    /**
     * Persist a per-user dismissal of the promo for the current milestone.
     */
    public function handle_promo_dismiss() {

        if ( empty( $_GET['woope_dismiss_promo'] ) ) {
            return;
        }

        if (
            ! isset( $_GET['_wpnonce'] ) ||
            ! wp_verify_nonce( $_GET['_wpnonce'], 'woope_dismiss_promo' ) ||
            ! current_user_can( 'manage_options' )
        ) {
            return;
        }

        $milestone = $this->get_usage_milestone();

        update_user_meta(
            get_current_user_id(),
            'woope_promo_dismissed_milestone',
            $milestone ? $milestone : 'dismissed'
        );

        wp_safe_redirect( remove_query_arg( [ 'woope_dismiss_promo', '_wpnonce' ] ) );
        exit;
    }

    /* -------------------------------------------------------------
     *  ADD SETTINGS PAGE
     * ----------------------------------------------------------- */

    public function add_settings_page() {

        add_submenu_page(
            'edit.php?post_type=product',
            __( 'Product Expiry Settings', 'product-expiry-for-woocommerce' ),
            __( 'Expiry Settings', 'product-expiry-for-woocommerce' ),
            'manage_options',
            'products_expiry_settings',
            [ $this, 'render_settings_page' ]
        );
    }

    public function render_settings_page() {

        $common_single_hooks = array(
            'woocommerce_single_product_summary' => __( 'Inside Summary (Default)', 'product-expiry-for-woocommerce' ),
            'woocommerce_before_single_product_summary' => __( 'Before Summary (Above Image)', 'product-expiry-for-woocommerce' ),
            'woocommerce_after_single_product_summary' => __( 'After Summary (Below Tabs)', 'product-expiry-for-woocommerce' ),
            'woocommerce_product_meta_start' => __( 'Product Meta Start', 'product-expiry-for-woocommerce' ),
            'woocommerce_product_meta_end' => __( 'Product Meta End', 'product-expiry-for-woocommerce' ),
            'woocommerce_before_add_to_cart_form' => __( 'Before Add to Cart Form', 'product-expiry-for-woocommerce' ),
            'woocommerce_after_add_to_cart_form' => __( 'After Add to Cart Form', 'product-expiry-for-woocommerce' ),
        );

        $common_archive_hooks = array(
            'woocommerce_after_shop_loop_item_title' => __( 'After Title (Default)', 'product-expiry-for-woocommerce' ),
            'woocommerce_before_shop_loop_item_title' => __( 'Before Title', 'product-expiry-for-woocommerce' ),
            'woocommerce_after_shop_loop_item' => __( 'After Product Link', 'product-expiry-for-woocommerce' ),
            'woocommerce_shop_loop_item_title' => __( 'Inside Title Hook', 'product-expiry-for-woocommerce' ),
        );

        include WOOPE_PATH . 'admin/views/settings.php';
    }

    /* -------------------------------------------------------------
     *  AJAX SAVE SETTINGS
     * ----------------------------------------------------------- */

    public function save_settings() {

        if (
            ! isset( $_POST['woope_save_admin_settings_nonce'] ) ||
            ! wp_verify_nonce(
                $_POST['woope_save_admin_settings_nonce'],
                'woope_save_admin_settings_nonce'
            ) ||
            ! current_user_can( 'manage_options' )
        ) {
            wp_die( __( 'Security check failed.', 'product-expiry-for-woocommerce' ) );
        }

        $settings = [
            'single_hook'        => sanitize_text_field( $_POST['single_hook'] ?? '' ),
            'archive_hook'       => sanitize_text_field( $_POST['archive_hook'] ?? '' ),
            'date_format'        => sanitize_text_field( $_POST['date_format'] ?? '' ),
            'notify_emails'      => sanitize_text_field( $_POST['notify_emails'] ?? '' ),
            'display'            => sanitize_text_field( $_POST['display'] ?? '' ),
            'orderdetails'       => sanitize_text_field( $_POST['orderdetails'] ?? '' ),
            'orderdetailsadmin'  => sanitize_text_field( $_POST['orderdetailsadmin'] ?? '' ),
            'markup'             => wp_kses_post( $_POST['markup'] ?? '' ),
            'show_earliest_variation' => sanitize_text_field( $_POST['show_earliest_variation'] ?? 'disable' ),
            'expired_date_display' => sanitize_text_field( $_POST['expired_date_display'] ?? 'show' ),
            'expired_date_custom_text' => sanitize_text_field( $_POST['expired_date_custom_text'] ?? '' ),
            'expired_badge_text' => sanitize_text_field( $_POST['expired_badge_text'] ?? '' ),
            'expired_badge_color' => $this->sanitize_badge_color( $_POST['expired_badge_color'] ?? '' ),
            'expired_badge_single_hook'  => sanitize_text_field( $_POST['expired_badge_single_hook'] ?? '' ),
            'expired_badge_archive_hook' => sanitize_text_field( $_POST['expired_badge_archive_hook'] ?? '' ),
            'notify_on_expired'  => sanitize_text_field( $_REQUEST['notify_on_expired'] ?? 'enable' ),
            'notify_before_days' => sanitize_text_field( $_REQUEST['notify_before_days'] ?? '' ),
            'email_subject'      => sanitize_text_field( $_REQUEST['email_subject'] ?? '' ),
            'email_body'         => wp_kses_post( $_REQUEST['email_body'] ?? '' ),            
        ];

        $updated = update_option(
            'woope_admin_settings',
            $settings
        );

        /*
         * Preserve WPML integration
         */
        if ( isset( $_POST['markup'] ) ) {

            do_action(
                'wpml_register_single_string',
                'product-expiry-for-woocommerce',
                'date-markup',
                sanitize_text_field( $_POST['markup'] )
            );
        }

        if ( $updated ) {
            wp_send_json_success( __( 'Settings saved successfully!', 'product-expiry-for-woocommerce' ) );
        } else {
            // Check if the option exists to see if "no changes" happened vs a real failure
            $current_val = get_option( 'woope_admin_settings' );
            
            if ( $current_val === $settings ) {
                wp_send_json_success( __( 'No changes detected, but settings are up to date.', 'product-expiry-for-woocommerce' ) );
            } else {
                wp_send_json_error( __( 'Failed to save settings. Please try again.', 'product-expiry-for-woocommerce' ) );
            }
        }
    }

    /* -------------------------------------------------------------
     *  HELPERS
     * ----------------------------------------------------------- */

    /**
     * Validate a hex colour, falling back to the default badge colour.
     */
    private function sanitize_badge_color( $color ) {

        $color = is_string( $color ) ? trim( $color ) : '';

        if ( preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $color ) ) {
            return $color;
        }

        return '#d63638';
    }

    /* -------------------------------------------------------------
     *  ADMIN SCRIPTS
     * ----------------------------------------------------------- */

    public function enqueue_scripts( $hook ) {

        global $post;

        // Product edit screen
        if ( $hook === 'post-new.php' || $hook === 'post.php' ) {

            if ( isset( $post->post_type ) && $post->post_type === 'product' ) {

                wp_enqueue_script(
                    'woope-product-meta',
                    WOOPE_URL . 'assets/js/trigger-date-picker.js',
                    [ 'wc-admin-product-meta-boxes' ],
                    WOOPE_VERSION,
                    true
                );
            }
        }

        // Settings page
        if ( $hook === 'product_page_products_expiry_settings' ) {
            wp_enqueue_style(
                'woope-admin-style',
                WOOPE_URL . 'assets/css/admin.css',
                [],
                WOOPE_VERSION
            );

            // Load admin-rtl.css automatically on RTL locales.
            wp_style_add_data( 'woope-admin-style', 'rtl', 'replace' );

            wp_enqueue_script(
                'sweetalert2',
                WOOPE_URL . 'assets/js/sweetalert2.min.js',
                [ 'jquery' ],
                '11.26.24',
                true
            );

            wp_enqueue_script(
                'woope-admin',
                WOOPE_URL . 'assets/js/admin.js',
                [ 'jquery' ],
                WOOPE_VERSION,
                true
            );

            wp_localize_script(
                'woope-admin',
                'woope_admin',
                [
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce'    => wp_create_nonce( 'woope_save_admin_settings_nonce' ),
                ]
            );
        }
    }

    /* -------------------------------------------------------------
     *  PLUGIN SETTINGS LINK
     * ----------------------------------------------------------- */

    public function add_settings_link( $links, $file ) {

        if ( strpos( $file, 'product-expiry-for-woocommerce.php' ) !== false ) {

            $settings_url = admin_url(
                'edit.php?post_type=product&page=products_expiry_settings'
            );

            $links[] = '<a href="' . esc_url( $settings_url ) . '">' .
                __( 'Settings', 'product-expiry-for-woocommerce' ) .
                '</a>';
        }

        return $links;
    }
}