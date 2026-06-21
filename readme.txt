=== Product Expiry for WooCommerce ===
Contributors: webcodingplace
Tags: expiry date, expiration date, product expiry, perishable, countdown
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.2
Requires Plugins: woocommerce
Stable tag: 3.3
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Add expiry dates to WooCommerce products and variations, then auto set them out of stock, draft, or expired and email you when they expire.

== Description ==

**Product Expiry for WooCommerce** gives every product and variation in your store an expiry date. When that date passes, the plugin does the cleanup for you. It can move the product to draft, set it out of stock, reduce its stock, or keep it visible with an "Expired" badge and the add to cart button disabled. You can also get an email the moment something expires.

No more forgotten sale items sitting live in your catalog. No more manually hunting for last season's stock. You set the date once, and the plugin handles the rest on schedule.

It works for any store that sells things with a shelf life: food, cosmetics, and supplements, limited time offers and flash sales, seasonal lines, digital licenses and event tickets, rentals, and classified style listings.

= What it does for you =

Set an expiry date on any product or variation. You can also add a short note that shows instead of the date, like "Sold out for the season". Expiry respects your site timezone and triggers at the end of the chosen day, scheduled individually per product so nothing fires early or late.

When a product expires, you choose what happens to it:

* **Leave it alone** and just record that it expired
* **Move it to draft** so it disappears from the store
* **Set it out of stock** while keeping the page live
* **Reduce its stock** by a set amount, handled through WooCommerce so HPOS and lookup tables stay in sync
* **Mark it as expired**, which keeps the product visible, shows an "Expired" badge, and turns off add to cart

= Show the expiry date where customers can see it =

Turn the expiry date on or off for single product pages and shop and archive pages. You decide where it appears using a dropdown of common positions, or paste in a custom hook if your theme uses its own. You control the wording around the date with an {expiry_date} placeholder, set your own date format, and decide what shows after a product expires: keep the date, hide it, or swap in custom text.

The "Expired" badge is yours to style too. Change the label, pick the background color, and choose where it sits on single and archive pages. Variable products with no date of their own can roll up and display the soonest expiring variation on the parent. There is also a simple shortcode, `[expiry_date before="" after=""]`, for dropping the date anywhere.

= Keep it in orders and emails =

Show the expiry date inside order details for both you and your customer, and include it in WooCommerce order emails so the record travels with every purchase. You can also send yourself an email notification when a product expires, with custom recipients and your own subject and body using {product_name}, {expiry_date}, and {product_url} placeholders.

= Manage everything from the products list =

A sortable Expiry column shows dates right in the products table, with a per variation breakdown for variable products. Quick edit the date, note, and action without opening the product. Filter the list to find what needs attention: within 7 days, within 30 days, this month, next month, within 3 months, within 6 months, or already expired. There is also an Email Log of the last 30 days of plugin emails, showing recipient, subject, type, and whether each one sent or failed.

= Built to fit your store =

* Works with any well coded WooCommerce theme, no template editing needed
* Translation ready, with **WPML** and **Polylang** sync across translated products
* Full RTL stylesheet
* No external API calls on the frontend and no tracking
* Clean uninstall with no leftover options or cron jobs

== Pro version ==

**Product Expiry Pro for WooCommerce** runs on top of the free plugin and adds the features stores ask for most:

* **Exact expiry time** in hours and minutes, not just the date, so you can expire a flash sale at 6:00 PM sharp
* **Live countdown timer** on product pages, and optionally archives, in three styles (blocks, badge, or minimal) with an urgency threshold and optional seconds
* **Customer reminder emails** that tell buyers when something they purchased is about to expire, sent as one branded email per order and deduped so nobody gets spammed
* **Admin reminder emails** that summarize everything expiring in the next few days
* **Auto discount before expiry** that puts products on sale a set number of days out, by percentage or fixed amount, and restores the original price automatically afterward
* **Batch and lot tracking** with quantity, supplier, and lot number per batch, listed earliest expiry first, plus a recall lookup that finds orders containing a given batch
* **CSV bulk tools** to export every product with its expiry data, edit in a spreadsheet, and re import to update thousands of products at once
* **Premium dashboard** that monitors every expiring product with status and date range filters
* **Inline plugin updates** from your WordPress admin

[See everything in Pro](https://webcodingplace.com/product-expiry-for-woocommerce/?utm_source=wporg&utm_medium=readme&utm_campaign=pro)

== Useful Links ==

* [Documentation](https://kb.webcodingplace.com/docs/product-expiry-for-woocommerce/)
* [Plugin details](https://webcodingplace.com/product-expiry-for-woocommerce/)
* [Upgrade to Pro](https://webcodingplace.com/product-expiry-for-woocommerce/?utm_source=wporg&utm_medium=readme&utm_campaign=pro)

== Installation ==

1. Make sure WooCommerce is installed and active.
2. Upload the plugin files to `/wp-content/plugins/`, or install it directly from **Plugins > Add New** in your WordPress dashboard.
3. Activate the plugin.
4. Edit any product and open the new **Product Expiry** tab.
5. Set an expiry date and choose what should happen when that date passes, such as draft or out of stock.
6. Visit **Product Expiry > Settings** to control how and where the date shows on your storefront.

== Frequently Asked Questions ==

= Can I set an expiry date for each variation? =
Yes. Every variation can have its own date, action, and note, set right inside the variation. A variable product can also show the soonest expiring variation on the parent page.

= What happens when a product expires? =
Whatever you choose per product. It can be left as is, moved to draft, set out of stock, have its stock reduced by an amount, or marked as expired with a badge and add to cart turned off.

= Does expiry follow my site timezone? =
Yes. Each product expires at the end of its chosen day in your site timezone, scheduled per product so it triggers at the right moment rather than on a shared batch.

= Can I show the expiry date on the product page? =
Yes. You can show or hide it on single product and shop pages, control exactly where it appears, choose its wording with a placeholder, and set your own date format.

= Will I be notified when a product expires? =
Yes. The plugin can email you the moment a product expires. You can set custom recipients and write your own subject and body using placeholders for the product name, date, and URL.

= Can I edit expiry dates in bulk? =
You can quick edit the date, note, and action straight from the products list. For bulk updates across many products at once, the Pro version adds CSV export and import.

= Is it compatible with HPOS (High Performance Order Storage)? =
Yes. Stock and order actions run through WooCommerce CRUD methods, so HPOS and product lookup tables stay in sync.

= Does it work with WPML or Polylang? =
Yes. Expiry data syncs across translated products with both WPML and Polylang, and the markup supports translation.

= Can I expire products at a specific time, not just a date? =
Setting an exact time in hours and minutes is a Pro feature, along with a live countdown timer for product pages.

= Can I remind customers before something they bought expires? =
Yes, in the Pro version. It sends buyers one branded reminder email per order a set number of days before expiry.

= Can I automatically discount products before they expire? =
Yes, in the Pro version. You can put products on sale a chosen number of days before expiry, by percentage or fixed amount, and the original price is restored automatically.

= Will it slow down my store? =
No. There are no external API calls on the frontend and no tracking. Expiry actions run on scheduled events rather than on every page load.

= What happens to my data if I uninstall? =
The plugin removes its options and scheduled events on uninstall, so you are not left with orphaned data.

== Screenshots ==

1. Set an expiry date and choose the action on expiry
2. Expiry date shown on the single product page
3. Filter products in admin by expiry status
4. Settings screen
5. Expiry date inside an order email

== Changelog ==

= 3.3 =

* Feature Added: Expiry action "Reduce stock by amount"
* Feature Added: Expiry action "Mark as Expired" with a customizable badge
* Feature Added: Email log of recent plugin emails
* Feature Added: Filter expiry products within 7 days
* Feature Added: Filter expiry products within 30 days
* Feature Added: Control variable products when the parent has no expiry of its own
* Feature Added: WPML and Polylang support
* Feature Added: RTL support
* Bug Fixed: Filters not displaying draft products

= 3.2 =

* Bug Fixed: Variable products not displaying expiry note when display is outside of the form
* Notice: POT updated

= 3.1 =

* Feature Added: Choose display positions for single product and archive pages from a dropdown
* Feature Added: Support for custom hooks for themes that use non standard hooks
* Feature Added: SweetAlert2 integration for smoother real time settings saving
* Feature Added: Better feedback when no changes are detected on save
* Bug Fixed: Email notification not working when no action is selected

= 3.0 =

* Feature Added: New UI with more control
* Feature Added: Email subject and markup options
* Bug Fixed: Expiry date for variations not showing in order summary

= 2.9 =

* Bug Fixed: WPML breaking the date placeholder in emails and frontend

= 2.8 =

* Bug Fixed: Date not respecting the site timezone (credit: @akukameda)

= 2.7 =

* Feature Added: Sorting by expiry date
* Feature Added: 6 months filter
* Feature Added: Filter variable products
* Feature Added: Variable products expiry date in admin column
* Feature Added: Support for WooCommerce 9.8.5
* Bug Fixed: Uncaught error calling get_meta()
* Bug Fixed: Date not displaying for variable products

= 2.6 =

* Vulnerability Fixed: Issue identified by the Wordfence team

= 2.5 =

* Bug Fixed: Cron event not cleared when the date is deleted

= 2.4 =

* Feature Added: Custom expiry note to display
* Feature Added: Shortcode [expiry_date before="" after=""]
* Bug Fixed: Quick edit date always empty

= 2.3 =

* Feature Added: Variations support
* Feature Added: WPML support
* Feature Added: Set products out of stock on expiry
* Feature Added: Quick settings button

= 2.2 =

* Feature Added: Product becomes draft when the date passes
* Feature Added: Option to display the date in order details for admin and frontend

= 2.1 =

* Feature Added: Option to display the expiry date in order emails
* Bug Fixed: Frontend display settings not saving

= 2.0 =

* Feature Added: Admin filtering by expiry status
* Feature Added: Custom text markup
* Feature Added: Custom date format
* Feature Added: Email notification on expiry

= 1.5 =

* Feature Added: Russian translation

= 1.4 =

* Feature Added: Bulk edit expiration date
* Feature Added: Admin column to display dates
* Notice: POT updated

= 1.3 =

* Bug Fixed: Warning for use of undefined constant

= 1.2 =

* Feature Added: Enable or disable the expiry date on the product page
* Notice: POT updated

= 1.1 =

* Feature Added: Expiry date on the single product page

= 1.0 =

* Initial release

== Upgrade Notice ==

= 3.3 =
Adds new on expiry actions (reduce stock, mark as expired), an email log, faster 7 and 30 day filters, and WPML, Polylang, and RTL support. Safe update with no breaking changes.