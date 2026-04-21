=== Product Expiry for WooCommerce ===
Contributors: webcodingplace
Tags: woo, woocommerce, product, product expiry, woo notifications
Requires at least: 3.5
Tested up to: 6.9
Stable tag: 3.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Set expiration dates for WooCommerce products and variations. Automatically change their status or send notifications when they expire.

== Description ==

**Product Expiry for WooCommerce** lets you set an expiry date and time for your products and variations. You can automatically mark them as out of stock, move them to draft, or trigger email notifications when they reach their expiration point.

Whether you're managing limited-time offers, perishable goods, or seasonal items, this plugin gives you complete control over product visibility based on expiry logic.

### Quick Links

[Documentation](https://kb.webcodingplace.com/docs/product-expiry-for-woocommerce/).
[More Details](https://webcodingplace.com/product-expiry-for-woocommerce/).

### 🔑 Key Features:
- Set expiration date & time for any WooCommerce product or variation
- Automatically make products **Out of Stock** or **Draft** on expiry
- Receive email **notifications** before a product expires
- **Show or hide** the expiry date on product pages (with custom formatting)
- Display expiry info on the **Order Details** page and in **Order Emails**
- Sort products in the admin panel by expiry date
- **Quick edit** or **bulk update** expiry dates from the product list
- Customize email recipient for expiry alerts

### 🛠️ Use Cases:
- Automatically hide expired food, cosmetics, or perishable goods
- Manage expiring digital products or promotional offers
- Keep store content fresh without manual cleanup

This plugin is lightweight, easy to use, and integrates directly into your WooCommerce workflow — no extra setup required.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory or install through the WordPress plugin dashboard.
2. Activate the plugin.
3. Edit a product in WooCommerce, and you’ll see a new **"Product Expiry"** panel.
4. Set your desired expiry date and choose the action on expiry (e.g., draft or out-of-stock).

== Frequently Asked Questions ==

= Can I set expiry dates for individual variations? =
Yes, the plugin supports setting expiration dates at the variation level.

= What happens when a product expires? =
Depending on your settings, it will either be moved to Draft status or marked as Out of Stock automatically.

= Can I display the expiry date on the product page? =
Yes. You can choose to show or hide the expiry date on the front end and even control where it appears and how it looks.

= Will I be notified when a product expires? =
Yes, the plugin can send an email notification when a product expires. You can also specify a custom recipient email address.

= Can I edit expiry dates in bulk? =
Yes. You can bulk-edit or quick-edit expiry dates from the product listing page in your admin dashboard.


== Screenshots ==

1. Provide Expiry Date
2. Single Product Page
3. Filter by Expiry Dates
4. Settings
5. Expire date in order emails

== Changelog ==

= 3.1 =

- Feature Added: Enhanced Hook Selection UI! You can now choose display positions for Single Product and Archive pages via a user-friendly dropdown
- Feature Added: Added support for "Custom Hooks." If your theme uses non-standard hooks, you can still enter them manually.
- Feature Added: Modernized Admin experience with SweetAlert2 integration for smoother, real-time settings saving.
- Feature Added: Optimized AJAX saving logic to provide better feedback when "no changes" are detected.
- Bug Fixed: Email notification is not working when no action is selected.

= 3.0 =

- Feature Added: New UI with more control
- Feature Added: Email Subject and Markup options
- Bug Fixed: Expiry date for variations does not show in order summary

= 2.9 =

- Bug Fixed: WPML breaks %date% placeholder in emails and frontend

= 2.8 =

- Bug Fixed: Date is not respecting the site's timezone settings. (credits: @akukameda)

= 2.7 =

- Feature Added: Sorting by expiry date
- Feature Added: 6 Months filter
- Feature Added: Filter variable products
- Feature Added: Variable products expiry date in admin column
- Feature Added: Support with WooCommerce 9.8.5
- Bug Fixed: Uncaught Error: Call to a member function get_meta()
- Bug Fixed: Date is not displaying for variable products

= 2.6 =

- Vulnerability Fixed: issue identified by Wordfence team is fixed

= 2.5 =

- Bug Fixed: Cron event does not get cleared when date is deleted

= 2.4 =

- Feature Added: Provide custom expiry note to display
- Feature Added: Shortcode added [expiry_date before="" after=""]
- Bug Fixed: Quick edit date is always empty

= 2.3 =

- Feature Added: Variations support
- Feature Added: WPML Support
- Feature Added: Make products out of stock
- Feature Added: Quick settings button

= 2.2 =

- Feature Added: Product becomes draft when the date pass (+1 Day)
- Feature Added: Option to display date in order details (Admin + Front)

= 2.1 =

- Feature Added: Option to display expiry date in order emails
- Bug Fixed: Display on frontend not saving settings

= 2.0 =

- Feature Added: Admin filtering by expiry status
- Feature Added: Custom text markup
- Feature Added: Custom date format
- Feature Added: Email notification on expiry

= 1.5 =

- Feature Added: Russian Translation Added

= 1.4 =

- Feature Added: Bulk edit expiration date
- Feature Added: Admin column added to display dates
- Notice: POT updated

= 1.3 =

- Bug Fixed: Warning Use of undefined constant

= 1.2 =

- Ability to enable/disable display of expire date on the product page
- POT file updated

= 1.1 =

- Expiry date added in the single product page

= 1.0 =

- Initial Release