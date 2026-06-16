=== Product Expiry for WooCommerce ===
Contributors: webcodingplace
Tags: woo, woocommerce, product, product expiry, woo notifications
Requires at least: 3.5
Tested up to: 7.0
Stable tag: 3.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Set expiration dates for WooCommerce products and variations. Automatically change their status or send notifications when they expire.

== Description ==

**Product Expiry for WooCommerce** gives every product and variation in your store an expiry date. When the date hits, the plugin can flip the product to out-of-stock, move it to draft, or fire an email notification. No manual cleanup, no forgotten sale items haunting your catalog.

Perfect for stores selling perishable goods, time-limited offers, seasonal inventory, digital licenses, event tickets, or anything with a shelf life.

### 🔗 Quick Links

[Documentation](https://kb.webcodingplace.com/docs/product-expiry-for-woocommerce/) · [Live Details](https://webcodingplace.com/product-expiry-for-woocommerce/) · [Upgrade to Pro](https://webcodingplace.com/product-expiry-for-woocommerce/?utm_source=wporg&utm_medium=readme&utm_campaign=pro)

### 🛠️ Typical Use Cases

- Food, cosmetics, supplements, and other **perishable goods** that auto-disappear after their sell-by date
- **Limited-time sales** and flash offers : pair with the Pro countdown timer for real urgency
- **Seasonal products** (holiday, summer lines) that should vanish off-season
- **Digital licenses**, event tickets, and subscription codes with a fixed validity window
- **Rental listings**, classifieds, and marketplace-style stores where items expire

### 🔑 Free Features

- Set an **expiry date** for any WooCommerce product or variation
- Automatically mark expired products as **Out of Stock** or **Draft**
- **Email notifications** to store admins when a product expires
- **Show or hide** the expiry date on single product and shop pages, with customizable markup
- Display expiry info in **order details** (frontend, admin, and order emails)
- **Sort** products in the admin list by expiry date
- **Quick edit** and **bulk edit** expiry dates from the product list
- Custom notification recipients (comma-separated emails)
- **WPML-compatible** expiry markup
- Works with both simple and variable products

### ⭐ Pro Features

Upgrade to **Product Expiry Pro** for everything above, plus:

- ⏱ **Live Countdown Timer** on product pages : day / hour / minute / second, with three display styles (blocks, badge, minimal) and automatic green → amber → red urgency colouring
- 📊 **CSV Bulk Tools** : export every product and variation with expiry data, edit in Excel, re-import to update dates, times, notes and on-expiry actions across thousands of products at once
- 🕐 **Exact expiry time** (HH:MM), not just the date
- 📧 **Pre-expiry reminder emails** : notify admins X days *before* a product expires
- ✉️ Fully customizable **email subject and body**
- 📋 **Premium dashboard** to manage every expiring product from one screen
- 🔄 **Inline plugin updates** directly from your WordPress admin

[See all Pro features →](https://webcodingplace.com/product-expiry-for-woocommerce/?utm_source=wporg&utm_medium=readme&utm_campaign=pro)

### 💚 Built Right

- Works with any well-coded WooCommerce theme : no template overrides needed
- Translation-ready; **WPML** and **Polylang** supported
- Clean uninstall : no orphaned options or cron jobs
- Lightweight: no external API calls on frontend, no tracking

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

= 3.2 =

- Bug Fixed: Variable products not displaying expiry note when display is outside of form
- Notice: POT Updated

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