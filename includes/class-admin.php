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
            'expired_badge_text' => sanitize_text_field( $_POST['expired_badge_text'] ?? '' ),
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