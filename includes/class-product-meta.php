<?php
namespace WOOPE;

if ( ! defined( 'ABSPATH' ) ) exit;

class Product_Meta {

    public function __construct() {

        // Add product tab
        add_filter( 'woocommerce_product_data_tabs', [ $this, 'add_tab' ] );

        // Panel content (simple products)
        add_action( 'woocommerce_product_data_panels', [ $this, 'panel_content' ] );

        // Variation fields
        add_action(
            'woocommerce_variation_options_pricing',
            [ $this, 'variation_fields' ],
            10,
            3
        );

        // Save simple product
        add_action(
            'woocommerce_process_product_meta',
            [ $this, 'save_simple_product' ]
        );

        // Save variation
        add_action(
            'woocommerce_save_product_variation',
            [ $this, 'save_variation' ],
            10,
            2
        );
    }

    /* -------------------------------------------------------------
     *  PRODUCT TAB
     * ----------------------------------------------------------- */

    public function add_tab( $tabs ) {

        $tabs['woopetab'] = [
            'label'  => __( 'Product Expiry', 'product-expiry-for-woocommerce' ),
            'target' => 'woo_product_expiry',
        ];

        return $tabs;
    }

    /* -------------------------------------------------------------
     *  SIMPLE PRODUCT PANEL
     * ----------------------------------------------------------- */

    public function panel_content() { ?>

        <div id="woo_product_expiry" class="panel woocommerce_options_panel">
            <div class="options_group">

                <?php

                woocommerce_wp_text_input([
                    'id'          => 'woo_expiry_date',
                    'label'       => __( 'Expiry Date', 'product-expiry-for-woocommerce' ),
                    'type'        => 'text',
                    'desc_tip'    => true,
                    'description' => __( 'Provide the date of expiry in YYYY-MM-DD format. Expires at midnight as per site timezone.', 'product-expiry-for-woocommerce' ),
                ]);

                if ( ! defined( 'WOOPE_PRO_VERSION' ) ) {

                    echo '<div class="woope-pro-field" style="opacity:0.6;">';

                    woocommerce_wp_text_input( array(
                        'id'          => 'woo_expiry_time',
                        'label'       => __( 'Expiry Time', 'product-expiry-for-woocommerce' ),
                        'type'        => 'text',
                        'value'       => '23:59',
                        'custom_attributes' => array(
                            'readonly' => 'readonly',
                        ),
                        'description' => sprintf(
                            __( 'Set exact expiry time. Available in <strong>Pro version</strong>. <a href="%s" target="_blank">Learn more</a>', 'product-expiry-for-woocommerce' ),
                            esc_url( 'https://webcodingplace.com/product-expiry-for-woocommerce/' )
                        ),
                    ) );

                    echo '</div>';
                }
                
                // will be used in pro version to add actual time field
                do_action( 'woope_data_panels_after_expiry_date' );                

                woocommerce_wp_text_input([
                    'id'          => 'woo_expiry_note',
                    'label'       => __( 'Expiry Note (Optional)', 'product-expiry-for-woocommerce' ),
                    'type'        => 'text',
                    'desc_tip'    => true,
                    'description' => __( 'Provide text to display instead of the expiry date.', 'product-expiry-for-woocommerce' ),
                ]);

                woocommerce_wp_select([
                    'id'          => 'woo_expiry_action',
                    'label'       => __( 'Action', 'product-expiry-for-woocommerce' ),
                    'options'     => [
                        ''        => __( 'Nothing', 'product-expiry-for-woocommerce' ),
                        'draft'   => __( 'Make it Draft', 'product-expiry-for-woocommerce' ),
                        'out'     => __( 'Out of stock', 'product-expiry-for-woocommerce' ),
                        'reduce'  => __( 'Reduce stock by amount', 'product-expiry-for-woocommerce' ),
                        'expired' => __( 'Mark as Expired (badge + disable purchase)', 'product-expiry-for-woocommerce' ),
                    ],
                    'desc_tip'    => true,
                    'description' => __( 'What to do when this product expires?', 'product-expiry-for-woocommerce' ),
                ]);

                woocommerce_wp_text_input([
                    'id'                => 'woo_expiry_reduce_qty',
                    'label'             => __( 'Reduce Stock By', 'product-expiry-for-woocommerce' ),
                    'type'              => 'number',
                    'desc_tip'          => true,
                    'description'       => __( 'Used only with the "Reduce stock by amount" action: lower the managed stock quantity by this amount on expiry.', 'product-expiry-for-woocommerce' ),
                    'custom_attributes' => [ 'min' => '0', 'step' => '1' ],
                ]);

                ?>

            </div>
        </div>

    <?php }

    /* -------------------------------------------------------------
     *  VARIATION FIELDS
     * ----------------------------------------------------------- */

    public function variation_fields( $loop, $variation_data, $variation ) {

        $variation_id = $variation->ID;

        ?>

        <div class="options_group form-row form-row-full">

            <?php

            woocommerce_wp_text_input([
                'id'            => '_woope_exp_date[' . $variation_id . ']',
                'label'         => __( 'Expiry Date', 'product-expiry-for-woocommerce' ),
                'type'          => 'text',
                'wrapper_class' => 'form-row form-row-first',
                'desc_tip'      => true,
                'description'   => __( 'Provide expiry date (YYYY-MM-DD)', 'product-expiry-for-woocommerce' ),
                'value'         => get_post_meta( $variation_id, 'woo_expiry_date', true ),
            ]);

            if ( ! defined( 'WOOPE_PRO_VERSION' ) ) {

                echo '<div class="woope-pro-field" style="opacity:0.6;">';

                woocommerce_wp_text_input( array(
                    'id'            => '_woope_exp_time[' . $variation_id . ']',
                    'label'       => __( 'Expiry Time', 'product-expiry-for-woocommerce' ),
                    'type'        => 'text',
                    'value'       => '23:59',
                    'custom_attributes' => array(
                        'readonly' => 'readonly',
                    ),
                    'wrapper_class' => 'form-row form-row-last',
                    'description' => sprintf(
                        __( 'Set exact expiry time. Available in <strong>Pro version</strong>. <a href="%s" target="_blank">Learn more</a>', 'product-expiry-for-woocommerce' ),
                        esc_url( 'https://webcodingplace.com/product-expiry-for-woocommerce/' )
                    ),
                ) );

                echo '</div>';
            }
            
            // will be used in pro version to add actual time field
            do_action( 'woope_variation_fields_after_expiry_date', $loop, $variation_data, $variation );

            woocommerce_wp_text_input([
                'id'            => '_woope_exp_note[' . $variation_id . ']',
                'label'         => __( 'Expiry Note (Optional)', 'product-expiry-for-woocommerce' ),
                'type'          => 'text',
                'wrapper_class' => 'form-row form-row-first',
                'desc_tip'      => true,
                'description'   => __( 'Provide text instead of expiry date', 'product-expiry-for-woocommerce' ),
                'value'         => get_post_meta( $variation_id, 'woo_expiry_note', true ),
            ]);

            woocommerce_wp_select([
                'id'            => '_woope_exp_action[' . $variation_id . ']',
                'label'         => __( 'Action', 'product-expiry-for-woocommerce' ),
                'wrapper_class' => 'form-row form-row-last',
                'options'       => [
                    ''        => __( 'Nothing', 'product-expiry-for-woocommerce' ),
                    'draft'   => __( 'Make it Draft', 'product-expiry-for-woocommerce' ),
                    'out'     => __( 'Out of stock', 'product-expiry-for-woocommerce' ),
                    'reduce'  => __( 'Reduce stock by amount', 'product-expiry-for-woocommerce' ),
                    'expired' => __( 'Mark as Expired (badge + disable purchase)', 'product-expiry-for-woocommerce' ),
                ],
                'desc_tip'      => true,
                'description'   => __( 'What to do when this variation expires?', 'product-expiry-for-woocommerce' ),
                'value'         => get_post_meta( $variation_id, 'woo_expiry_action', true ),
            ]);

            woocommerce_wp_text_input([
                'id'                => '_woope_reduce_qty[' . $variation_id . ']',
                'label'             => __( 'Reduce Stock By', 'product-expiry-for-woocommerce' ),
                'type'              => 'number',
                'wrapper_class'     => 'form-row form-row-full',
                'desc_tip'          => true,
                'description'       => __( 'Used only with the "Reduce stock by amount" action.', 'product-expiry-for-woocommerce' ),
                'custom_attributes' => [ 'min' => '0', 'step' => '1' ],
                'value'             => get_post_meta( $variation_id, 'woo_expiry_reduce_qty', true ),
            ]);

            ?>

        </div>

        <?php
    }

    /* -------------------------------------------------------------
     *  SAVE SIMPLE PRODUCT
     * ----------------------------------------------------------- */

    public function save_simple_product( $post_id ) {

        $product = wc_get_product( $post_id );
        if ( ! $product ) return;

        $date   = isset( $_POST['woo_expiry_date'] ) ? sanitize_text_field( $_POST['woo_expiry_date'] ) : '';
        $note   = isset( $_POST['woo_expiry_note'] ) ? sanitize_text_field( $_POST['woo_expiry_note'] ) : '';
        $action = isset( $_POST['woo_expiry_action'] ) ? sanitize_text_field( $_POST['woo_expiry_action'] ) : '';

        $product->update_meta_data( 'woo_expiry_date', $date );
        $product->update_meta_data( 'woo_expiry_note', $note );
        $product->update_meta_data( 'woo_expiry_action', $action );

        if ( isset( $_POST['woo_expiry_reduce_qty'] ) ) {
            $product->update_meta_data( 'woo_expiry_reduce_qty', absint( $_POST['woo_expiry_reduce_qty'] ) );
        }

        $product->save();

        $this->handle_scheduling( $post_id, $date, $action );
    }

    /* -------------------------------------------------------------
     *  SAVE VARIATION
     * ----------------------------------------------------------- */

    public function save_variation( $variation_id, $i ) {

        $date   = $_POST['_woope_exp_date'][ $variation_id ] ?? '';
        $note   = $_POST['_woope_exp_note'][ $variation_id ] ?? '';
        $action = $_POST['_woope_exp_action'][ $variation_id ] ?? '';

        $date   = sanitize_text_field( $date );
        $note   = sanitize_text_field( $note );
        $action = sanitize_text_field( $action );

        update_post_meta( $variation_id, 'woo_expiry_date', $date );
        update_post_meta( $variation_id, 'woo_expiry_note', $note );
        update_post_meta( $variation_id, 'woo_expiry_action', $action );

        if ( isset( $_POST['_woope_reduce_qty'][ $variation_id ] ) ) {
            update_post_meta(
                $variation_id,
                'woo_expiry_reduce_qty',
                absint( $_POST['_woope_reduce_qty'][ $variation_id ] )
            );
        }

        $this->handle_scheduling( $variation_id, $date, $action );
    }

    /* -------------------------------------------------------------
     *  SCHEDULER HANDLER
     * ----------------------------------------------------------- */

    private function handle_scheduling( $post_id, $date, $action ) {

        // Clear if no date
        if ( empty( $date ) ) {
            wp_clear_scheduled_hook( 'woo_expiry_schedule_action', [ $post_id ] );
            return;
        }

        Plugin::instance()->scheduler->schedule( $post_id, $date );
    }
}