<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$default = array(
    'single_hook'   =>  'woocommerce_single_product_summary',
    'archive_hook'  =>  'woocommerce_after_shop_loop_item_title',
    'date_format'   =>  get_option( 'date_format' ),
    'notify_emails' =>  '',
    'display'       =>  'enable',
    'orderdetails'  =>  'disable',
    'orderdetailsadmin' => 'disable',
    'show_earliest_variation'    => 'disable',
    'expired_date_display'       => 'show',
    'expired_date_custom_text'   => '',
    'expired_badge_text'         => '',
    'expired_badge_color'        => '#d63638',
    'expired_badge_single_hook'  => 'woocommerce_single_product_summary',
    'expired_badge_archive_hook' => 'woocommerce_before_shop_loop_item_title',
    'markup'        =>  __( 'Expiry Date: {expiry_date}', 'product-expiry-for-woocommerce' ),
    'notify_on_expired'  => 'enable',
    'notify_before_days' => '',
    'email_subject'      => '',
    'email_body'         => '',
);

$savedSettings = wp_parse_args( get_option( 'woope_admin_settings', array() ), $default );

$is_custom_single  = ! empty( $savedSettings['single_hook'] )  && ! array_key_exists( $savedSettings['single_hook'], $common_single_hooks );
$is_custom_archive = ! empty( $savedSettings['archive_hook'] ) && ! array_key_exists( $savedSettings['archive_hook'], $common_archive_hooks );

$badge_single_hook  = $savedSettings['expired_badge_single_hook'];
$badge_archive_hook = $savedSettings['expired_badge_archive_hook'];
$badge_color        = $savedSettings['expired_badge_color'];
$is_custom_badge_single  = ! empty( $badge_single_hook )  && ! array_key_exists( $badge_single_hook, $common_single_hooks );
$is_custom_badge_archive = ! empty( $badge_archive_hook ) && ! array_key_exists( $badge_archive_hook, $common_archive_hooks );

$expired_date_display     = $savedSettings['expired_date_display'];
$expired_date_custom_text = $savedSettings['expired_date_custom_text'];

/**
 * Helper: render a "position" hook picker (select + custom-hook input).
 */
$render_hook_picker = function( $field_name, $current, $hooks, $is_custom, $target_id ) {
    ?>
    <select class="woope-hook-selector" data-target="<?php echo esc_attr( $target_id ); ?>">
        <?php foreach ( $hooks as $hook => $label ) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr( $hook ),
                selected( $current, $hook, false ),
                esc_html( $label )
            );
        } ?>
        <option value="custom" <?php selected( $is_custom, true ); ?>><?php esc_html_e( 'Custom Hook Name…', 'product-expiry-for-woocommerce' ); ?></option>
    </select>
    <div id="<?php echo esc_attr( $target_id ); ?>" class="woope-custom-hook" style="<?php echo $is_custom ? '' : 'display:none;'; ?>">
        <input type="text" name="<?php echo esc_attr( $field_name ); ?>" class="widefat"
               value="<?php echo esc_attr( $current ); ?>"
               placeholder="<?php esc_attr_e( 'Enter custom hook name', 'product-expiry-for-woocommerce' ); ?>">
    </div>
    <?php
};
?>
<div class="wrap woope-settings">

    <!-- ============ HERO HEADER ============ -->
    <div class="woope-hero">
        <div class="woope-hero-main">
            <h1><?php esc_html_e( 'Product Expiry', 'product-expiry-for-woocommerce' ); ?>
                <span class="woope-version">v<?php echo esc_html( defined( 'WOOPE_VERSION' ) ? WOOPE_VERSION : '' ); ?></span>
            </h1>
            <p><?php esc_html_e( 'Set expiry dates for products and variations, choose how they behave when they expire, and notify the right people at the right time.', 'product-expiry-for-woocommerce' ); ?></p>
        </div>
        <div class="woope-hero-links">
            <a href="https://kb.webcodingplace.com/docs/product-expiry-for-woocommerce/" target="_blank" rel="noopener" class="button"><?php esc_html_e( 'Documentation', 'product-expiry-for-woocommerce' ); ?></a>
        </div>
    </div>

    <!-- ============ QUICK NAV ============ -->
    <nav class="woope-nav">
        <a href="#woope-display"><?php esc_html_e( 'Frontend Display', 'product-expiry-for-woocommerce' ); ?></a>
        <a href="#woope-expired"><?php esc_html_e( 'Expired Products', 'product-expiry-for-woocommerce' ); ?></a>
        <a href="#woope-orders"><?php esc_html_e( 'Orders & Emails', 'product-expiry-for-woocommerce' ); ?></a>
        <a href="#woope-notifications"><?php esc_html_e( 'Notifications', 'product-expiry-for-woocommerce' ); ?></a>
    </nav>

    <div class="woope-body<?php echo defined( 'WOOPE_PRO_VERSION' ) ? '' : ' has-aside'; ?>">

    <div class="woope-content">

    <form action="#" class="woope-form">

        <input type="hidden" name="action" value="woope_save_admin_settings">
        <?php wp_nonce_field( 'woope_save_admin_settings_nonce', 'woope_save_admin_settings_nonce' ); ?>

        <!-- ================= FRONTEND DISPLAY ================= -->
        <div class="woope-card" id="woope-display">
            <h2 class="woope-card-head"><span class="woope-card-icon">🛍️</span><?php esc_html_e( 'Frontend Display', 'product-expiry-for-woocommerce' ); ?></h2>
            <p class="woope-card-desc"><?php esc_html_e( 'Control whether and where the expiry date appears to shoppers, and how it is written.', 'product-expiry-for-woocommerce' ); ?></p>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show on Storefront', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <select name="display">
                            <option value="enable"  <?php selected( $savedSettings['display'], 'enable' ); ?>><?php esc_html_e( 'Enable', 'product-expiry-for-woocommerce' ); ?></option>
                            <option value="disable" <?php selected( $savedSettings['display'], 'disable' ); ?>><?php esc_html_e( 'Disable', 'product-expiry-for-woocommerce' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'Display the expiry date on single product and shop/archive pages.', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Expiry Text', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <input type="text" name="markup" class="widefat" value="<?php echo esc_attr( $savedSettings['markup'] ); ?>">
                        <p class="description"><?php esc_html_e( 'Text shown on the frontend. Use {expiry_date} where the date should appear.', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Date Format', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <input type="text" name="date_format" value="<?php echo esc_attr( $savedSettings['date_format'] ); ?>">
                        <p class="description"><?php echo wp_kses_post( sprintf( __( 'How the date is formatted, e.g. %1$s. <a href="%2$s" target="_blank">Formatting guide</a>.', 'product-expiry-for-woocommerce' ), '<code>d/m/Y</code>', 'https://wordpress.org/support/article/formatting-date-and-time/' ) ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Position — Single Product', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <?php $render_hook_picker( 'single_hook', $savedSettings['single_hook'], $common_single_hooks, $is_custom_single, 'custom_single_container' ); ?>
                        <p class="description"><?php esc_html_e( 'Where the expiry date appears on the single product page.', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Position — Shop / Archive', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <?php $render_hook_picker( 'archive_hook', $savedSettings['archive_hook'], $common_archive_hooks, $is_custom_archive, 'custom_archive_container' ); ?>
                        <p class="description"><?php esc_html_e( 'Where the expiry date appears on shop and category listings.', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Earliest Variation on Parent', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <select name="show_earliest_variation">
                            <option value="disable" <?php selected( $savedSettings['show_earliest_variation'], 'disable' ); ?>><?php esc_html_e( 'Disable', 'product-expiry-for-woocommerce' ); ?></option>
                            <option value="enable"  <?php selected( $savedSettings['show_earliest_variation'], 'enable' ); ?>><?php esc_html_e( 'Enable', 'product-expiry-for-woocommerce' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'For variable products without their own date, show the soonest variation expiry on the parent. The variation’s own date still applies once selected.', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- ================= EXPIRED PRODUCTS ================= -->
        <div class="woope-card" id="woope-expired">
            <h2 class="woope-card-head"><span class="woope-card-icon">⏳</span><?php esc_html_e( 'Expired Products', 'product-expiry-for-woocommerce' ); ?></h2>
            <p class="woope-card-desc"><?php esc_html_e( 'What shoppers see after a product passes its expiry date, including the optional “Expired” badge used by the Mark as Expired action.', 'product-expiry-for-woocommerce' ); ?></p>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e( 'After Expiry, Show Date As', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <select name="expired_date_display" id="woope_expired_date_display">
                            <option value="show"   <?php selected( $expired_date_display, 'show' ); ?>><?php esc_html_e( 'Keep showing the date', 'product-expiry-for-woocommerce' ); ?></option>
                            <option value="hide"   <?php selected( $expired_date_display, 'hide' ); ?>><?php esc_html_e( 'Hide the date', 'product-expiry-for-woocommerce' ); ?></option>
                            <option value="custom" <?php selected( $expired_date_display, 'custom' ); ?>><?php esc_html_e( 'Show custom text', 'product-expiry-for-woocommerce' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'Once the expiry date/time has passed, choose what appears on single product and shop/archive pages. Applies to both.', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr id="woope_expired_custom_row" style="<?php echo $expired_date_display === 'custom' ? '' : 'display:none;'; ?>">
                    <th scope="row"><?php esc_html_e( 'Custom Expired Text', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <input type="text" name="expired_date_custom_text" class="widefat" value="<?php echo esc_attr( $expired_date_custom_text ); ?>" placeholder="<?php esc_attr_e( 'e.g. This product has expired', 'product-expiry-for-woocommerce' ); ?>">
                        <p class="description"><?php esc_html_e( 'Shown in place of the date after expiry when “Show custom text” is selected.', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Expired Badge Text', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <input type="text" name="expired_badge_text" value="<?php echo esc_attr( $savedSettings['expired_badge_text'] ); ?>" placeholder="<?php esc_attr_e( 'Expired', 'product-expiry-for-woocommerce' ); ?>">
                        <p class="description"><?php esc_html_e( 'Badge label for products whose on-expiry action is “Mark as Expired”. Blank uses “Expired”.', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Expired Badge Color', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <input type="color" name="expired_badge_color" value="<?php echo esc_attr( $badge_color ); ?>">
                        <p class="description"><?php esc_html_e( 'Background colour of the “Expired” badge.', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Badge Position — Single Product', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <?php $render_hook_picker( 'expired_badge_single_hook', $badge_single_hook, $common_single_hooks, $is_custom_badge_single, 'custom_badge_single_container' ); ?>
                        <p class="description"><?php esc_html_e( 'Where the “Expired” badge appears on the single product page.', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Badge Position — Shop / Archive', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <?php $render_hook_picker( 'expired_badge_archive_hook', $badge_archive_hook, $common_archive_hooks, $is_custom_badge_archive, 'custom_badge_archive_container' ); ?>
                        <p class="description"><?php esc_html_e( 'Where the badge appears on shop/category pages. Pick “Custom” and leave blank to hide it on archives.', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- ================= ORDERS & EMAILS ================= -->
        <div class="woope-card" id="woope-orders">
            <h2 class="woope-card-head"><span class="woope-card-icon">🧾</span><?php esc_html_e( 'Orders & Emails', 'product-expiry-for-woocommerce' ); ?></h2>
            <p class="woope-card-desc"><?php esc_html_e( 'Show expiry information inside order details and order emails.', 'product-expiry-for-woocommerce' ); ?></p>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show in Order Emails', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <select name="orderdetails">
                            <option value="enable"  <?php selected( $savedSettings['orderdetails'], 'enable' ); ?>><?php esc_html_e( 'Enable', 'product-expiry-for-woocommerce' ); ?></option>
                            <option value="disable" <?php selected( $savedSettings['orderdetails'], 'disable' ); ?>><?php esc_html_e( 'Disable', 'product-expiry-for-woocommerce' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'Include the expiry date in WooCommerce order emails.', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show in Order Details', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <select name="orderdetailsadmin">
                            <option value="enable"  <?php selected( $savedSettings['orderdetailsadmin'], 'enable' ); ?>><?php esc_html_e( 'Enable', 'product-expiry-for-woocommerce' ); ?></option>
                            <option value="disable" <?php selected( $savedSettings['orderdetailsadmin'], 'disable' ); ?>><?php esc_html_e( 'Disable', 'product-expiry-for-woocommerce' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'Include the expiry date in order details (admin and customer view).', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- ================= NOTIFICATIONS ================= -->
        <div class="woope-card" id="woope-notifications">
            <h2 class="woope-card-head"><span class="woope-card-icon">🔔</span><?php esc_html_e( 'Expiry Notifications', 'product-expiry-for-woocommerce' ); ?></h2>
            <p class="woope-card-desc"><?php esc_html_e( 'Email store staff when products expire, and customise the message.', 'product-expiry-for-woocommerce' ); ?></p>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Notification Emails', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <input type="text" class="widefat" name="notify_emails" value="<?php echo esc_attr( $savedSettings['notify_emails'] ); ?>">
                        <p class="description"><?php esc_html_e( 'Comma-separated email addresses to notify when a product expires.', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Send Email on Expiry', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <select name="notify_on_expired">
                            <option value="enable"  <?php selected( $savedSettings['notify_on_expired'], 'enable' ); ?>><?php esc_html_e( 'Enable', 'product-expiry-for-woocommerce' ); ?></option>
                            <option value="disable" <?php selected( $savedSettings['notify_on_expired'], 'disable' ); ?>><?php esc_html_e( 'Disable', 'product-expiry-for-woocommerce' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'Send a notification email at the moment a product expires.', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Email Subject', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <input type="text" name="email_subject" class="widefat" value="<?php echo esc_attr( $savedSettings['email_subject'] ); ?>">
                        <p class="description"><?php esc_html_e( 'Placeholders: {product_name}, {expiry_date}, {product_url}', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Email Body', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <textarea name="email_body" class="widefat" rows="4"><?php echo esc_textarea( $savedSettings['email_body'] ); ?></textarea>
                        <p class="description"><?php esc_html_e( 'The message sent on expiry. Same placeholders as the subject.', 'product-expiry-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php esc_html_e( 'Reminder Before Expiry (Days)', 'product-expiry-for-woocommerce' ); ?>
                        <?php if ( ! defined( 'WOOPE_PRO_VERSION' ) ) : ?><span class="woope-pro-badge"><?php esc_html_e( 'PRO', 'product-expiry-for-woocommerce' ); ?></span><?php endif; ?>
                    </th>
                    <td>
                        <?php if ( ! defined( 'WOOPE_PRO_VERSION' ) ) : ?>
                            <input type="number" value="3" class="small-text" readonly style="opacity:0.6;">
                            <p class="description"><?php echo wp_kses_post( sprintf( __( 'Send reminder emails X days before expiry. Available in the <strong>Pro version</strong>. <a href="%s" target="_blank">Learn more</a>.', 'product-expiry-for-woocommerce' ), esc_url( 'https://webcodingplace.com/product-expiry-for-woocommerce/' ) ) ); ?></p>
                        <?php else : ?>
                            <input type="number" name="notify_before_days" value="<?php echo esc_attr( $savedSettings['notify_before_days'] ); ?>" class="small-text">
                            <p class="description"><?php esc_html_e( 'Send an admin reminder this many days before products expire.', 'product-expiry-for-woocommerce' ); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- ================= SAVE BAR ================= -->
        <div class="woope-save-bar">
            <button type="submit" class="button button-primary button-hero"><?php esc_html_e( 'Save Settings', 'product-expiry-for-woocommerce' ); ?></button>
        </div>

    </form>

    </div><!-- /.woope-content -->

    <?php if ( ! defined( 'WOOPE_PRO_VERSION' ) ) : ?>
    <!-- ================= UPGRADE BANNER (always visible) ================= -->
    <aside class="woope-aside">
        <div class="woope-upsell">
            <div class="woope-upsell-head">
                <span class="woope-card-icon">✨</span>
                <h2><?php esc_html_e( 'Product Expiry Pro', 'product-expiry-for-woocommerce' ); ?></h2>
            </div>
            <p class="woope-upsell-sub"><?php esc_html_e( 'Unlock the full toolkit:', 'product-expiry-for-woocommerce' ); ?></p>
            <ul class="woope-upsell-list">
                <li><?php esc_html_e( 'Live countdown timers', 'product-expiry-for-woocommerce' ); ?></li>
                <li><?php esc_html_e( 'Exact expiry time (HH:MM)', 'product-expiry-for-woocommerce' ); ?></li>
                <li><?php esc_html_e( 'Customer pre-expiry reminders', 'product-expiry-for-woocommerce' ); ?></li>
                <li><?php esc_html_e( 'Auto-discount before expiry', 'product-expiry-for-woocommerce' ); ?></li>
                <li><?php esc_html_e( 'Batch / lot tracking with FEFO', 'product-expiry-for-woocommerce' ); ?></li>
                <li><?php esc_html_e( 'CSV bulk import / export', 'product-expiry-for-woocommerce' ); ?></li>
                <li><?php esc_html_e( 'Premium expiry dashboard', 'product-expiry-for-woocommerce' ); ?></li>
            </ul>
            <a href="https://webcodingplace.com/product-expiry-for-woocommerce/?utm_source=free-plugin&utm_medium=settings&utm_campaign=upgrade" target="_blank" rel="noopener" class="button button-primary button-hero woope-upsell-btn"><?php esc_html_e( 'Upgrade to Pro', 'product-expiry-for-woocommerce' ); ?> →</a>
            <a class="woope-upsell-link" href="https://webcodingplace.com/product-expiry-for-woocommerce/?utm_source=free-plugin&utm_medium=settings&utm_campaign=learn-more" target="_blank" rel="noopener"><?php esc_html_e( 'See all features', 'product-expiry-for-woocommerce' ); ?></a>
        </div>
    </aside>
    <?php endif; ?>

    </div><!-- /.woope-body -->
</div>
