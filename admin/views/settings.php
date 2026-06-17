<div class="wrap woope-settings">
	<?php
        $default = array(
            'single_hook'   =>  'woocommerce_single_product_summary',
            'archive_hook'  =>  'woocommerce_after_shop_loop_item_title',
            'date_format'   =>  get_option( 'date_format' ),
            'notify_emails' =>  '',
            'display'   	=>  'enable',
            'orderdetails'   	=>  'disable',
            'show_earliest_variation' => 'disable',
            'expired_badge_text' => '',
            'expired_badge_color' => '#d63638',
            'expired_badge_single_hook' => 'woocommerce_single_product_summary',
            'expired_badge_archive_hook' => 'woocommerce_before_shop_loop_item_title',
            'markup'   =>  __( 'Expiry Date: {expiry_date}', 'product-expiry-for-woocommerce' ),
            'notify_on_expired'  => 'enable',
            'notify_before_days' => '',
            'email_subject'      => '',
            'email_body'         => '',
        );
		$savedSettings = get_option( 'woope_admin_settings', $default );

        $is_custom_single  = !empty($savedSettings['single_hook']) && !array_key_exists($savedSettings['single_hook'], $common_single_hooks);
        $is_custom_archive = !empty($savedSettings['archive_hook']) && !array_key_exists($savedSettings['archive_hook'], $common_archive_hooks);

        $badge_single_hook  = isset($savedSettings['expired_badge_single_hook']) ? $savedSettings['expired_badge_single_hook'] : 'woocommerce_single_product_summary';
        $badge_archive_hook = isset($savedSettings['expired_badge_archive_hook']) ? $savedSettings['expired_badge_archive_hook'] : 'woocommerce_before_shop_loop_item_title';
        $badge_color        = isset($savedSettings['expired_badge_color']) ? $savedSettings['expired_badge_color'] : '#d63638';
        $is_custom_badge_single  = !empty($badge_single_hook) && !array_key_exists($badge_single_hook, $common_single_hooks);
        $is_custom_badge_archive = !empty($badge_archive_hook) && !array_key_exists($badge_archive_hook, $common_archive_hooks);
	?>	
    <h1><?php _e( 'Woo Product Expiry Settings', 'product-expiry-for-woocommerce' ); ?></h1>

    <?php if ( ! defined( 'WOOPE_PRO_VERSION' ) ) : ?>
    <div class="woope-card woope-pro-promo">

        <h2 style="display:flex;align-items:center;gap:8px;border:none;padding:0;">
            ✨ <?php _e( 'New in Product Expiry Pro 1.2', 'product-expiry-for-woocommerce' ); ?>
            <span class="woope-pro-badge"><?php _e( 'PRO', 'product-expiry-for-woocommerce' ); ?></span>
        </h2>

        <div class="woope-pro-promo-grid">

            <!-- Countdown Timer -->
            <div class="woope-pro-promo-feature">
                <h3>⏱ <?php _e( 'Countdown Timer', 'product-expiry-for-woocommerce' ); ?></h3>
                <p><?php _e( 'Live day/hour/min/sec countdown on product pages. Three styles (blocks, badge, minimal), urgency colouring that shifts green → amber → red as expiry approaches, works on variable products.', 'product-expiry-for-woocommerce' ); ?></p>

                <div class="woope-promo-cd" data-deadline="<?php echo esc_attr( time() + ( 2 * DAY_IN_SECONDS ) + ( 4 * HOUR_IN_SECONDS ) + ( 30 * MINUTE_IN_SECONDS ) ); ?>">
                    <span class="woope-promo-cd-label"><?php _e( 'Expires in:', 'product-expiry-for-woocommerce' ); ?></span>
                    <span class="woope-promo-cd-block" data-unit="d"><b>02</b><i>days</i></span>
                    <span class="woope-promo-cd-block" data-unit="h"><b>04</b><i>hrs</i></span>
                    <span class="woope-promo-cd-block" data-unit="m"><b>30</b><i>min</i></span>
                    <span class="woope-promo-cd-block" data-unit="s"><b>00</b><i>sec</i></span>
                </div>
            </div>

            <!-- CSV Tools -->
            <div class="woope-pro-promo-feature">
                <h3>📊 <?php _e( 'CSV Bulk Tools', 'product-expiry-for-woocommerce' ); ?></h3>
                <p><?php _e( 'Export every product and variation with expiry data to CSV. Edit in Excel. Re-import to bulk-update dates, times, notes and on-expiry actions — no SQL, no per-product clicks.', 'product-expiry-for-woocommerce' ); ?></p>

                <div class="woope-promo-csv">
                    <code>product_id,sku,expiry_date,expiry_time,expiry_action</code>
                    <code>42,MILK-001,2026-12-31,23:59,out</code>
                    <code>43,BREAD-002,2026-11-15,18:00,draft</code>
                </div>
            </div>

        </div>

        <p style="margin:16px 0 0;">
            <a href="https://webcodingplace.com/product-expiry-for-woocommerce/?utm_source=free-plugin&utm_medium=settings&utm_campaign=v1.2-launch"
               target="_blank" rel="noopener"
               class="button button-primary">
                <?php _e( 'Upgrade to Pro', 'product-expiry-for-woocommerce' ); ?> →
            </a>
            <a class="woope-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'woope_dismiss_promo', '1' ), 'woope_dismiss_promo' ) ); ?>"
               style="margin-left:12px;color:#666;text-decoration:none;">
                <?php _e( 'Dismiss', 'product-expiry-for-woocommerce' ); ?>
            </a>
        </p>
    </div>
    <?php endif; ?>

    <form action="#" class="woope-form">

        <input type="hidden" name="action" value="woope_save_admin_settings">
        <?php wp_nonce_field('woope_save_admin_settings_nonce', 'woope_save_admin_settings_nonce'); ?>

        <!-- General Settings -->
        <div class="woope-card">
            <h2><?php _e( 'Display Settings', 'product-expiry-for-woocommerce' ); ?></h2>
            <table class="form-table">
            	<tr>
            		<th><?php _e( 'Display on Frontend', 'product-expiry-for-woocommerce' ); ?></th>
            		<td>
            			<select name="display">
            				<option value="enable" <?php echo ($savedSettings['display'] == 'enable') ? 'selected' : '' ?>><?php _e( 'Enable', 'product-expiry-for-woocommerce' ) ?></option>
            				<option value="disable" <?php echo ($savedSettings['display'] == 'disable') ? 'selected' : '' ?>><?php _e( 'Disable', 'product-expiry-for-woocommerce' ) ?></option>
            			</select>
            		</td>
            		<td>
            			<?php _e( 'Display the expiry date on single product or shop pages', 'product-expiry-for-woocommerce' ); ?>
            		</td>
            	</tr>

            	<tr>
            		<th><?php _e( 'Earliest Variation Expiry on Parent', 'product-expiry-for-woocommerce' ); ?></th>
            		<td>
            			<?php $show_earliest = isset( $savedSettings['show_earliest_variation'] ) ? $savedSettings['show_earliest_variation'] : 'disable'; ?>
            			<select name="show_earliest_variation">
            				<option value="disable" <?php selected( $show_earliest, 'disable' ); ?>><?php _e( 'Disable', 'product-expiry-for-woocommerce' ) ?></option>
            				<option value="enable" <?php selected( $show_earliest, 'enable' ); ?>><?php _e( 'Enable', 'product-expiry-for-woocommerce' ) ?></option>
            			</select>
            		</td>
            		<td>
            			<?php _e( 'For variable products without their own expiry date, show the earliest variation expiry on the parent product page. A variation\'s own date still applies once selected.', 'product-expiry-for-woocommerce' ); ?>
            		</td>
            	</tr>

            	<tr>
            		<th><?php _e( 'Expired Badge Text', 'product-expiry-for-woocommerce' ); ?></th>
            		<td>
            			<input type="text" name="expired_badge_text" value="<?php echo isset( $savedSettings['expired_badge_text'] ) ? esc_attr( $savedSettings['expired_badge_text'] ) : ''; ?>" placeholder="<?php esc_attr_e( 'Expired', 'product-expiry-for-woocommerce' ); ?>">
            		</td>
            		<td>
            			<?php _e( 'Label shown on the badge for products whose on-expiry action is "Mark as Expired". Leave blank to use the default "Expired".', 'product-expiry-for-woocommerce' ); ?>
            		</td>
            	</tr>

            	<tr>
            		<th><?php _e( 'Expired Badge Color', 'product-expiry-for-woocommerce' ); ?></th>
            		<td>
            			<input type="color" name="expired_badge_color" value="<?php echo esc_attr( $badge_color ); ?>">
            		</td>
            		<td>
            			<?php _e( 'Background color of the "Expired" badge.', 'product-expiry-for-woocommerce' ); ?>
            		</td>
            	</tr>

            	<tr>
            		<th><?php _e( 'Expired Badge Position (Single Product)', 'product-expiry-for-woocommerce' ); ?></th>
            		<td>
            			<select class="woope-hook-selector" data-target="custom_badge_single_container">
            				<?php
            				foreach ( $common_single_hooks as $hook => $label ) {
            					printf(
            						'<option value="%s" %s>%s</option>',
            						esc_attr( $hook ),
            						selected( $badge_single_hook, $hook, false ),
            						esc_html( $label )
            					);
            				}
            				?>
            				<option value="custom" <?php selected( $is_custom_badge_single, true ); ?>><?php _e( 'Custom Hook Name...', 'product-expiry-for-woocommerce' ); ?></option>
            			</select>
            			<div id="custom_badge_single_container" style="<?php echo $is_custom_badge_single ? '' : 'display:none;'; ?> margin-top:10px;">
            				<input type="text" name="expired_badge_single_hook" class="widefat"
            					value="<?php echo esc_attr( $badge_single_hook ); ?>"
            					placeholder="<?php esc_attr_e( 'Enter custom hook name', 'product-expiry-for-woocommerce' ); ?>">
            			</div>
            		</td>
            		<td>
            			<?php _e( 'Where the "Expired" badge appears on the single product page.', 'product-expiry-for-woocommerce' ); ?>
            		</td>
            	</tr>

            	<tr>
            		<th><?php _e( 'Expired Badge Position (Shop / Archive)', 'product-expiry-for-woocommerce' ); ?></th>
            		<td>
            			<select class="woope-hook-selector" data-target="custom_badge_archive_container">
            				<?php
            				foreach ( $common_archive_hooks as $hook => $label ) {
            					printf(
            						'<option value="%s" %s>%s</option>',
            						esc_attr( $hook ),
            						selected( $badge_archive_hook, $hook, false ),
            						esc_html( $label )
            					);
            				}
            				?>
            				<option value="custom" <?php selected( $is_custom_badge_archive, true ); ?>><?php _e( 'Custom Hook Name...', 'product-expiry-for-woocommerce' ); ?></option>
            			</select>
            			<div id="custom_badge_archive_container" style="<?php echo $is_custom_badge_archive ? '' : 'display:none;'; ?> margin-top:10px;">
            				<input type="text" name="expired_badge_archive_hook" class="widefat"
            					value="<?php echo esc_attr( $badge_archive_hook ); ?>"
            					placeholder="<?php esc_attr_e( 'Enter custom hook name (leave blank to hide on archives)', 'product-expiry-for-woocommerce' ); ?>">
            			</div>
            		</td>
            		<td>
            			<?php _e( 'Where the "Expired" badge appears on shop and category pages. Leave the custom field blank to hide it on archives.', 'product-expiry-for-woocommerce' ); ?>
            		</td>
            	</tr>

            	<tr>
            		<th><?php _e( 'Expiry Text Markup', 'product-expiry-for-woocommerce' ); ?></th>
            		<td>
            			<input type="text" name="markup" class="widefat" value="<?php echo esc_attr( $savedSettings['markup'] ) ?>">
            		</td>
            		<td>
            			<?php _e( 'Provide text to show on frontend. Use {expiry_date} for expiry date', 'product-expiry-for-woocommerce' ); ?>
            		</td>
            	</tr>

                <tr>
                    <th><?php _e( 'Position on single product page', 'product-expiry-for-woocommerce' ); ?></th>
                    <td>
                        <select class="woope-hook-selector" data-target="custom_single_container">
                            <?php
                            foreach ( $common_single_hooks as $hook => $label ) {
                                    printf(
                                        '<option value="%s" %s>%s</option>',
                                        esc_attr( $hook ),
                                        selected( $savedSettings['single_hook'], $hook, false ),
                                        esc_html( $label )
                                    );
                                }
                            ?>
                            <option value="custom" <?php selected($is_custom_single, true); ?>><?php _e( 'Custom Hook Name...', 'product-expiry-for-woocommerce' ); ?></option>
                        </select>
                        
                        <div id="custom_single_container" style="<?php echo $is_custom_single ? '' : 'display:none;'; ?> margin-top:10px;">
                            <input type="text" name="single_hook" class="widefat" 
                                   value="<?php echo esc_attr( $savedSettings['single_hook'] ); ?>" 
                                   placeholder="Enter custom hook name (e.g. my_theme_hook)">
                        </div>
                    </td>
                    <td>
                        <p>
                            <?php _e('Choose where the expiry date appears on the main product page', 'product-expiry-for-woocommerce') ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th><?php _e( 'Position on shop archive page', 'product-expiry-for-woocommerce' ) ?></th>
                    <td>
                        <select class="woope-hook-selector" data-target="custom_archive_container">
                            <?php
                            foreach ( $common_archive_hooks as $hook => $label ) {
                                    printf(
                                        '<option value="%s" %s>%s</option>',
                                        esc_attr( $hook ),
                                        selected( $savedSettings['archive_hook'], $hook, false ),
                                        esc_html( $label )
                                    );
                                }
                            ?>

                            <option value="custom" <?php selected($is_custom_archive, true); ?>><?php _e( 'Custom Hook Name...', 'product-expiry-for-woocommerce' ); ?></option>
                        </select>

                        <div id="custom_archive_container" style="<?php echo $is_custom_archive ? '' : 'display:none;'; ?> margin-top:10px;">
                            <input type="text" name="archive_hook" class="widefat" 
                                   value="<?php echo esc_attr( $savedSettings['archive_hook'] ); ?>" 
                                   placeholder="Enter custom hook name">
                        </div>
                    </td>
                    <td>
                        <p>
                            <?php _e("Choose where the expiry date appears on your shop's main listing and category pages.", "product-expiry-for-woocommerce") ?>
                        </p>
                    </td>
                </tr>

            	<tr>
            		<th><?php _e( 'Date Format', 'product-expiry-for-woocommerce' ) ?></th>
            		<td>
            			<input type="text" name="date_format" value="<?php echo esc_attr( $savedSettings['date_format'] ) ?>">
            		</td>
                    <td>
                        <p>
                            <?php 
                            echo sprintf(
                                __( 'Enter how the date should appear (e.g., %1$s). Leave blank to use your WordPress default. <a href="%2$s" target="_blank">View formatting guide</a>.', 'product-expiry-for-woocommerce' ),
                                '<strong>d/m/Y</strong>',
                                'https://wordpress.org/support/article/formatting-date-and-time/'
                            ); 
                            ?>
                        </p>
                    </td>
            	</tr>

            	<tr>
            		<th><?php _e( 'Display in Emails', 'product-expiry-for-woocommerce' ); ?></th>
            		<td>
            			<select name="orderdetails">
            				<option value="enable" <?php echo (isset($savedSettings['orderdetails']) && $savedSettings['orderdetails'] == 'enable') ? 'selected' : '' ?>><?php _e( 'Enable', 'product-expiry-for-woocommerce' ) ?></option>
            				<option value="disable" <?php echo (isset($savedSettings['orderdetails']) && $savedSettings['orderdetails'] == 'disable') ? 'selected' : '' ?>><?php _e( 'Disable', 'product-expiry-for-woocommerce' ) ?></option>
            			</select>
            		</td>
            		<td>
            			<?php _e( 'Display the expiry date in order details email.', 'product-expiry-for-woocommerce' ); ?>
            		</td>
            	</tr>

            	<tr>
            		<th><?php _e( 'Display in Order Details', 'product-expiry-for-woocommerce' ); ?></th>
            		<td>
            			<select name="orderdetailsadmin">
            				<option value="enable" <?php echo (isset($savedSettings['orderdetailsadmin']) && $savedSettings['orderdetailsadmin'] == 'enable') ? 'selected' : '' ?>><?php _e( 'Enable', 'product-expiry-for-woocommerce' ) ?></option>
            				<option value="disable" <?php echo (isset($savedSettings['orderdetailsadmin']) && $savedSettings['orderdetailsadmin'] == 'disable') ? 'selected' : '' ?>><?php _e( 'Disable', 'product-expiry-for-woocommerce' ) ?></option>
            			</select>
            		</td>
            		<td>
            			<?php _e( 'Display the expiry date in order details admin and front.', 'product-expiry-for-woocommerce' ); ?>
            		</td>
            	</tr>
            </table>
        </div>

        <!-- Notification Settings -->
        <div class="woope-card">
            <h2><?php _e( 'Notification Settings', 'product-expiry-for-woocommerce' ); ?></h2>
            <table class="form-table">
            	<tr>
            		<th><?php _e( 'Notification Emails', 'product-expiry-for-woocommerce' ) ?></th>
            		<td>
            			<input type="text" class="widefat" name="notify_emails" value="<?php echo esc_attr( $savedSettings['notify_emails'] ) ?>">
            		</td>
            		<td>
            			<?php _e( 'Provide comma separated email addresses for expiry notification.', 'product-expiry-for-woocommerce' ) ?>
            		</td>
            	</tr>
            	<tr>
            	    <th><?php _e( 'Send Email When Expired', 'product-expiry-for-woocommerce' ); ?></th>
            	    <td>
            	        <select name="notify_on_expired">
            	            <option value="enable" <?php isset($savedSettings['notify_on_expired']) ? selected( $savedSettings['notify_on_expired'], 'enable' ) : ''; ?>>
            	                <?php _e( 'Enable', 'product-expiry-for-woocommerce' ); ?>
            	            </option>
            	            <option value="disable" <?php isset($savedSettings['notify_on_expired']) ? selected( $savedSettings['notify_on_expired'], 'disable' ) : ''; ?>>
            	                <?php _e( 'Disable', 'product-expiry-for-woocommerce' ); ?>
            	            </option>
            	        </select>
            	    </td>
            	    <td>
            	        <?php _e( 'Send notification email when product expires.', 'product-expiry-for-woocommerce' ); ?>
            	    </td>
            	</tr>
            	<tr>
            	    <th><?php _e( 'Email Subject', 'product-expiry-for-woocommerce' ); ?></th>
            	    <td>
            	        <input type="text"
            	                   name="email_subject"
            	                   value="<?php echo isset($savedSettings['email_subject']) ?  esc_attr( $savedSettings['email_subject'] ) : ''; ?>"
            	                   class="widefat">
            	    </td>
            	    <td>
            	        <?php _e( 'Available placeholders: {product_name}, {expiry_date}, {product_url}', 'product-expiry-for-woocommerce' ); ?>
            	    </td>
            	</tr>

            	<tr>
            	    <th><?php _e( 'Email Body Template', 'product-expiry-for-woocommerce' ); ?></th>
            	    <td>
            	        <textarea name="email_body"
            	                      class="widefat"
            	                      rows="4"><?php echo isset($savedSettings['email_body']) ? esc_textarea( $savedSettings['email_body'] ) : ''; ?></textarea>
            	    </td>
            	    <td>
            	        <?php _e( 'Customize email message sent for expiry notification.', 'product-expiry-for-woocommerce' ); ?>
            	    </td>
            	</tr>
            	<tr>
            	    <th><?php _e( 'Reminder Before Expiry (Days)', 'product-expiry-for-woocommerce' ); ?></th>
            	    <td>
            	        <?php if ( ! defined( 'WOOPE_PRO_VERSION' ) ) : ?>
            	            <input type="number"
            	                   value="3"
            	                   class="small-text"
            	                   readonly
            	                   style="opacity:0.6;">
            	        <?php else : ?>
            	            <input type="number"
            	                   name="notify_before_days"
            	                   value="<?php echo isset($savedSettings['notify_before_days']) ?  esc_attr( $savedSettings['notify_before_days'] ) : ''; ?>"
            	                   class="small-text">
            	        <?php endif; ?>
            	    </td>
            	    <td>
            	        <?php
            	        if ( ! defined( 'WOOPE_PRO_VERSION' ) ) {
            	            echo sprintf(
            	                __( 'Send reminder email X days before expiry. Available in <strong>Pro version</strong>. <a href="%s" target="_blank">Learn more</a>', 'product-expiry-for-woocommerce' ),
            	                esc_url( 'https://webcodingplace.com/product-expiry-for-woocommerce/' )
            	            );
            	        } else {
            	            _e( 'Send reminder email before days product expires.', 'product-expiry-for-woocommerce' );
            	        }
            	        ?>
            	    </td>
            	</tr>
            </table>
        </div>

        <p class="submit">
            <button type="submit" class="button button-primary">
                <?php _e( 'Save Settings', 'product-expiry-for-woocommerce' ); ?>
            </button>
        </p>

    </form>
</div>