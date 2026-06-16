<?php
namespace WOOPE;

if ( ! defined( 'ABSPATH' ) ) exit;

class Columns {

    public function __construct() {

        // Add column
        add_filter(
            'manage_product_posts_columns',
            [ $this, 'add_column' ]
        );

        // Column content
        add_action(
            'manage_product_posts_custom_column',
            [ $this, 'render_column' ],
            10,
            2
        );

        // Make sortable
        add_filter(
            'manage_edit-product_sortable_columns',
            [ $this, 'make_sortable' ]
        );

        // Handle sorting
        add_action(
            'pre_get_posts',
            [ $this, 'handle_sorting' ]
        );

        add_action(
            'admin_enqueue_scripts',
            [ $this, 'enqueue_quick_edit_script' ]
        );

        add_action(
            'woocommerce_product_quick_edit_start',
            [ $this, 'quick_edit_field' ]
        );

        add_action(
            'woocommerce_product_quick_edit_save',
            [ $this, 'save_quick_edit' ]
        );
    }

    /* -------------------------------------------------------------
     *  ADD COLUMN
     * ----------------------------------------------------------- */

    public function add_column( $columns ) {

        $columns['woope_tab'] = __( 'Exp', 'product-expiry-for-woocommerce' );

        return $columns;
    }

    /* -------------------------------------------------------------
     *  RENDER COLUMN
     * ----------------------------------------------------------- */

    public function render_column( $column, $post_id ) {

        if ( $column !== 'woope_tab' ) {
            return;
        }

        $date_format = get_option( 'date_format' );
        $output = '';

        // Main product expiry
        $expiry_date = get_post_meta(
            $post_id,
            'woo_expiry_date',
            true
        );

        if ( $expiry_date ) {

            $formatted = wp_date(
                $date_format,
                wc_string_to_timestamp( $expiry_date )
            );

            $output .= '<div>
                <span id="expid-' . esc_attr( $post_id ) . '"
                      data-expdate="' . esc_attr( $expiry_date ) . '">
                    ' . esc_html( $formatted ) . '
                </span>
            </div>';

        } else {

            $output .= '<div>
                <span id="expid-' . esc_attr( $post_id ) . '"
                      title="' . esc_attr__( 'No Date Set', 'product-expiry-for-woocommerce' ) . '"
                      class="dashicons dashicons-warning">
                </span>
            </div>';
        }

        // Variation breakdown
        $product = wc_get_product( $post_id );

        if ( $product && $product->is_type( 'variable' ) ) {

            $children = $product->get_children();

            if ( ! empty( $children ) ) {
                update_meta_cache( 'post', $children );
            }

            foreach ( $children as $variation_id ) {

                $var_exp = get_post_meta(
                    $variation_id,
                    'woo_expiry_date',
                    true
                );

                if ( ! $var_exp ) continue;

                $variation = wc_get_product( $variation_id );
                if ( ! $variation || ! $variation->exists() ) continue;

                $attributes = $variation->get_attributes();
                $attr_output = [];

                foreach ( $attributes as $attr_name => $attr_value ) {

                    $taxonomy = str_replace( 'attribute_', '', $attr_name );

                    $term = get_term_by(
                        'slug',
                        $attr_value,
                        $taxonomy
                    );

                    $label = wc_attribute_label( $taxonomy );

                    if ( $term && ! is_wp_error( $term ) ) {
                        $attr_output[] = $label . ': ' . $term->name;
                    } else {
                        $attr_output[] = $label . ': ' . $attr_value;
                    }
                }

                $formatted_var = wp_date(
                    $date_format,
                    wc_string_to_timestamp( $var_exp )
                );

                $output .= '<div>
                    <small>' . esc_html( implode( ', ', $attr_output ) ) . ':</small>
                    ' . esc_html( $formatted_var ) . '
                </div>';
            }
        }

        echo $output;

    }

    public function quick_edit_field() {
        ?>
        <fieldset class="woo-expiry-fields">
            <div class="inline-edit-col">

                <label>
                    <span class="title">
                        <?php _e( 'Exp Date', 'product-expiry-for-woocommerce' ); ?>
                    </span>
                    <span class="input-text-wrap">
                        <input type="text"
                               name="woo_expiry_date"
                               class="text"
                               placeholder="YYYY-MM-DD">
                    </span>
                </label>
                <br class="clear">
                <label>
                    <span class="title">
                        <?php _e( 'Exp Note', 'product-expiry-for-woocommerce' ); ?>
                    </span>
                    <span class="input-text-wrap">
                        <input type="text"
                               name="woo_expiry_note"
                               class="text">
                    </span>
                </label>
                <br class="clear">
                <label>
                    <span class="title">
                        <?php _e( 'Exp Action', 'product-expiry-for-woocommerce' ); ?>
                    </span>
                    <span class="input-text-wrap">
                        <select name="woo_expiry_action">
                            <option value="">
                                <?php _e( 'Nothing', 'product-expiry-for-woocommerce' ); ?>
                            </option>
                            <option value="draft">
                                <?php _e( 'Make it Draft', 'product-expiry-for-woocommerce' ); ?>
                            </option>
                            <option value="out">
                                <?php _e( 'Out of stock', 'product-expiry-for-woocommerce' ); ?>
                            </option>
                            <option value="expired">
                                <?php _e( 'Mark as Expired', 'product-expiry-for-woocommerce' ); ?>
                            </option>
                        </select>
                    </span>
                </label>

            </div>
        </fieldset>
        <?php
    }

    public function save_quick_edit( $product ) {

        $post_id = $product->get_id();

        $date   = sanitize_text_field( $_REQUEST['woo_expiry_date'] ?? '' );
        $note   = sanitize_text_field( $_REQUEST['woo_expiry_note'] ?? '' );
        $action = sanitize_text_field( $_REQUEST['woo_expiry_action'] ?? '' );

        update_post_meta( $post_id, 'woo_expiry_date', $date );
        update_post_meta( $post_id, 'woo_expiry_note', $note );
        update_post_meta( $post_id, 'woo_expiry_action', $action );

        /*
         * Scheduling logic
         */
        if ( ! empty( $date )  ) {

            \WOOPE\Plugin::instance()
                ->scheduler
                ->schedule( $post_id, $date );
        }

        if ( empty( $date ) ) {

            wp_clear_scheduled_hook(
                'woo_expiry_schedule_action',
                [ $post_id ]
            );
        }
    }   

    public function enqueue_quick_edit_script( $hook ) {

        if ( $hook !== 'edit.php' ) {
            return;
        }

        if ( get_current_screen()->post_type !== 'product' ) {
            return;
        }

        $script = "
            jQuery(function($){

                $('#the-list').on('click', '.editinline', function(){

                    var post_id = $(this).closest('tr').attr('id');
                    post_id = post_id.replace('post-', '');

                    var custom_field = $('#expid-' + post_id).data('expdate');

                    $('input[name=\"woo_expiry_date\"]', '.inline-edit-row')
                        .val(custom_field);
                });

            });
        ";

        wp_add_inline_script(
            'inline-edit-post',
            $script
        );
    }    

    /* -------------------------------------------------------------
     *  SORTABLE COLUMN
     * ----------------------------------------------------------- */

    public function make_sortable( $columns ) {

        $columns['woope_tab'] = 'woo_expiry_date';

        return $columns;
    }

    /* -------------------------------------------------------------
     *  HANDLE SORTING
     * ----------------------------------------------------------- */

    public function handle_sorting( $query ) {

        if (
            ! is_admin() ||
            ! $query->is_main_query() ||
            $query->get( 'post_type' ) !== 'product'
        ) {
            return;
        }

        if ( $query->get( 'orderby' ) === 'woo_expiry_date' ) {

            $query->set( 'meta_key', 'woo_expiry_date' );
            $query->set( 'orderby', 'meta_value' );
            $query->set( 'meta_type', 'DATE' );
        }
    }
}