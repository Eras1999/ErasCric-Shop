=== Payment Plugins for PayPal WooCommerce ===
Contributors: mr.clayton
Tags: paypal, paylater, venmo, credit cards
Requires at least: 4.7
Tested up to: 6.5
Requires PHP: 7.1
Stable tag: 1.0.47
Copyright: Payment Plugins
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Developed exclusively between Payment Plugins and PayPal, PayPal for WooCommerce integrates with PayPal's newest API's.
To boost conversion rates, you can offer PayPal, Pay Later, Venmo, or credit cards on your site. There are many supported features so
merchants can tweak the plugin to suit their business needs.

= Supports =
- WooCommerce Subscriptions
- WooCommerce Pre-Orders
- WooCommerce Blocks
- WooFunnels AeroCheckout and Upsell
- Integrates with [Payment Plugins for Stripe WooCommerce](https://wordpress.org/plugins/woo-stripe-payment/)
- [CheckoutWC](https://www.checkoutwc.com/payment-plugins-paypal-woocommerce/)

= Payment Plugins is an official PayPal Partner =

== Screenshots ==
1. Offer payment buttons and display Pay Later messaging on product pages
2. Offer payment buttons and display Pay Later messaging on the cart page
3. Customize the checkout page button location
4. Customize the checkout page button location
5. Express payment buttons on the checkout page
6. Easily connect your PayPal account to your WooCommerce store
7. Lots of options for customizing the Pay Later messaging

== Frequently Asked Questions ==
= How do I test the plugin? =
The plugin has a sandbox option, where you can test payments. Our documentation shows you how to setup a Sandbox account.
[Documentation](https://docs.paymentplugins.com/wc-paypal/config/#/create_sandbox_account)

= How do I connect my PayPal account? =
Our documentation has a step-by-step guide on how to connect the plugin to your PayPal account. [Documentation](https://docs.paymentplugins.com/wc-paypal/config/#/connect)

= Who is Payment Plugins =
Payment Plugins is the team behind several of the highest reviewed and installed Payment integrations for WooCommerce.

== Changelog ==
= 1.0.47 - 04/30/24 =
* Added - Integration with [Advanced Product Fields (Product Addons) for WooCommerce](https://wordpress.org/plugins/advanced-product-fields-for-woocommerce/)
= 1.0.46 - 03/18/24 =
* Fixed - Order status not being updated to processing/completed when PayPal sends payment.capture.completed event
for payments under review. [https://wordpress.org/support/topic/status-never-changed-from-on-hold-to-processing/](https://wordpress.org/support/topic/status-never-changed-from-on-hold-to-processing/)
* Added - Additional shipping address validations
= 1.0.45 - 02/29/24 =
* Updated - PHP 8.2 notice showing if debug log enabled
* Added - Improvements to Checkout Block UI integration
= 1.0.44 - 02/16/24 =
* Updated - In WebhookReceiver, for event PAYMENT.CAPTURE.COMPLETED, don't rely on WC_Order::is_paid() function. Instead, check for existence of transaction ID.
= 1.0.43 - 02/05/24 =
* Updated - On checkout block, don't override email address if it's already populated and express checkout isn't being used
* Fixed - If PayPal button was configured to render in payment method section, and PayLater messaging enabled with "Above PayPal button" set, button was not rendering
= 1.0.42 - 01/11/24 =
* Updated - references to Woofunnels changed to Funnelkit
* Fixed - PayLater message color option fixed for checkout page
= 1.0.41 - 12/19/23 =
* Added - Filter "wc_ppcp_refund_factory_from_order_refund" so refund object can be customized before its processed by PayPal API.
* Added - Debug options so merchants can control the level of debugging that's enabled
* Updated - Replaced deprecated filters used in Checkout and Cart block
= 1.0.40 - 11/23/23 =
* Added - German translation file added. [Support thread](https://wordpress.org/support/topic/german-translation-168/)
* Updated - Performance improvements on checkout page when processing a payment.
* Fixed - Prevent duplicate Pay Later message on checkout page.
* Fixed - Unsupported regular expression for older versions of Safari.
* Fixed - FunnelKit upsell order issue introduced in recent version of FunnelKit
= 1.0.39 - 10/3/23 =
* Added - When validate required fields option is enabled, validate Germanized checkboxes.
* Added - Ensure PayPal button is disabled on product page if variation isn't selected.
* Added - Support for [WooCommerce Product Add-ons](https://woocommerce.com/products/product-add-ons/) plugin. Product addons are now accounted for on the product page when using PayPal as an express payment option.
= 1.0.38 - 9/17/23 =
* Added - New feature where the Place Order button on checkout page can be used for PayPal rather than the PayPal buttons. This is a good option
for those that want the best page load speed or for German based merchants that have requirements around button location and text.
* Added - Support for automatic tracking updates in PayPal via WooCommerce - ShipStation Integration plugin. When Shipstation
updates the tracking number on the order, the PayPal plugin will send that info to PayPal.
* Fixed - Add company field to list of validated fields when Validate Checkout Fields option is enabled
* Updated - Improved support for formal locales like de_DE_formal etc
* Updated - If address validation is enabled in Advanced Settings page, display all required fields that failed validation on checkout page rather than the first failed validation.
= 1.0.37 - 8/30/23 =
* Added - Advanced Settings option where merchants can enable/disable the PayPal change shipping address option. If enabled, the customer cannot change
the shipping address in the PayPal popup.
* Updated - Improved logic for rendering express buttons on product pages rendered by the product_page shortcode
= 1.0.36 - 8/4/23 =
* Added - Filter wc_ppcp_checkout_field_validation_label so the validation text can be modified
* Added - Filter wc_ppcp_is_product_section_disabled so display of payment buttons on product page can be conditionally controlled.
* Added - Filter wc_ppcp_get_shipping_carriers
= 1.0.35 - 7/20/23 =
* Added - Scope TRACKING_SHIPMENT_READWRITE to connection parameters so tracking API can be used. Merchants will need to re-connect via the API settings page if they want this feature.
* Added - Support for WooCommerce Extra Product Options plugin. Extra product options are now accounted for on the product page when using PayPal as an express payment option.
= 1.0.34 - 6/22/23 =
* Fixed - Show correct error message when PayPal account doesn't have reference transactions enabled.
* Updated - Handle scenario where payment already captured.
= 1.0.33 - 6/6/23 =
* Updated - Germanized plugin no longer triggers change event when terms checkbox is changed. Had to adjust code to account for this scenario
and any other plugins that alter the standard WooCommerce checkbox behavior.
= 1.0.32 - 6/2/23 =
* Updated - Added margin-top to PayPal button in FunnelKit Cart
* Fixed - Null exception when processing a subscription with a free trial.
* Fixed - Don't update customer address info on cart page if PayPal button clicked to ensure the shipping option remains the same
= 1.0.31 - 5/23/23 =
* Fixed - Handle case where the order requires additional approval before it can be processed
* Fixed - Add Billing Agreement and Payer ID to subscriptions created via FunnelKit Upsell
* Added - action wc_ppcp_validate_checkout_fields so 3rd party code can add custom validations when the PayPal button is clicked on the checkout page
* Added - Support for the FunnelKit Cart plugin
= 1.0.30 - 4/19/23 =
* Fixed - PayLater Elementor widget triggering Elementor editor error under certain conditions
* Updated - Improved checkout page validation logic
* Updated - Prevent "undefined" value being assigned to order address fields that have been removed using via filter woocommerce_checkout_fields
= 1.0.29 - 4/14/23 =
* Fixed - throw Exception statement which needed to be changed for PHP version less than 7.5
= 1.0.28 - 4/14/23 =
* Added - Elementor Payment buttons widget
* Added - Elementor PayLater messaging widget
* Added - Shortcodes ppcp_product_buttons, ppcp_cart_buttons, ppcp_product_message, & ppcp_cart_message which can be used to render the PayPal payment buttons and PayLater messaging
* Added - Advanced Setting option where merchants can enable/disable checkout page field validation when the PayPal button is clicked
= 1.0.27 - 4/4/23 =
* Added - Option to control the Pay Later messaging location on product pages
* Added - PAYMENT.CAPTURE.DENIED event added
* Updated - If capture status is pending due to review or eCheck, set order status to on-hold
= 1.0.26 - 3/20/23 =
* Updated - Added additional validations for the address country code property
* Fixed - JS error in paypal-express-checkout.js under certain conditions
* Fixed - WooCommerce Blocks change which affected billing and shipping address field population
= 1.0.25 - 3/10/23 =
* Fixed - FunnelKit upsell error if upsell quantity greater than one
* Added - Added events when PayPal button is clicked or payment popup is closed/cancelled. This allows 3rd party plugins
to listen for these events
= 1.0.24 - 3/3/23 =
* Fixed - Bulk actions on order status correctly captures payments for all selected orders
* Added - Ability to add tracking info to the PayPal transaction via the order details page
= 1.0.23 - 2/20/23 =
* Added - Limit SKU number to 127 characters
* Added - Product descriptions to PayPal line items
* Fixed - Error triggered when fee added to PayPal line items
* Updated - Removed GuzzleHttp dependency. Lots of 3rd party plugins have Guzzle as a dependency and it's usually outdated so removed Guzzle to prevent any version conflicts
= 1.0.22 - 2/18/23 =
* Fixed - If refund on cancel option enabled, perform the refund for captured payments and a void for authorized payments
* Added - Include product SKU in line items so they appear on shipping label
= 1.0.21 - 2/3/23 =
* Fixed - Payment method format always using the default option
* Fixed - Don't render Pay Later html container on shop page if disabled
* Fixed - Only enqueue paylater-message-checkout.js if enabled
= 1.0.20 - 1/27/23 =
* Added - Optimized classmap which improves plugin performance
* Added - Improved logic for rendering PayPal buttons with non-standard themes
* Fixed - JS error on product page if only PayPal and Card funding options are enabled
= 1.0.19 - 1/10/23 =
* Added - Option to show Pay Later messaging on the shop/product category page
* Added - Option for hiding or displaying the popup icon that appears in the payment method section of the checkout page.
* Added - WooCommerce Checkout Block validation notice if customer clicks Place Order button before clicking PayPal button.
* Fixed - WooCommerce Blocks error when local pickup shipping selected
= 1.0.18 - 12/24/22 =
* Added - PayPal fee to FunnelKit Upsell orders
* Added - Error message for invalid currency
* Updated - If billing name or email is populated on checkout page, don't override those values when using a billing agreement.
= 1.0.17 - 12/9/22 =
* Added - Improved compatibility with Mondial Relay
* Added - Show error message if incorrect client ID has been entered in API Settings page
* Fixed - If Stripe Express section is enabled, ensure PayPal buttons have necessary css classes added
= 1.0.16 - 11/23/22 =
* Fixed - Inaccurate coupon calculation if option "Display prices in the shop" set to including tax.
* Fixed - Update queries that include transaction property to be compatible across all WooCommerce versions
* Fixed - Prevent item total mismatch error if items total exceeds breakdown item total due to PayPal decimal limitation
= 1.0.15 - 11/18/22 =
* Fixed - Patch entire purchase unit rather than individual properties. This ensures certain tax calculations and shipping configurations are always accurate.
= 1.0.14 - 11/17/22 =
* Updated - Include Purchase Unit amount in patch request made during checkout.
* Added - Reference transactions are now optional with FunnelKit/WooFunnels Upsells.
= 1.0.13 - 11/11/22 =
* Added - Dispute created and resolved webhooks.
* Added - Site locale option in the Advanced Settings tab. The plugin can use the site's locale or default to PayPal's auto detection.
* Added - Factory filters so PayPal order and purchase units can be modified
= 1.0.12 - 10.12.22 =
* Updated - Improved messaging when reference transactions aren't enabled on the Merchant PayPal business account
* Updated - Improved Express Checkout buttons compatibility with currency plugins
* Added - Support for the new WooCommerce custom order tables (HPOS)
* Added - Capture On Status option which allows merchants to capture an authorized order on either the processing or completed status. Setting can be set to manual as well
* Fixed - Don't override shipping label first and last name with billing name
= 1.0.11 - 10.1.22 =
* Fixed - WooFunnels Upsell refund not processing
* Fixed - WooFunnels Upsell amount not always accurate
* Fixed - For currencies with no decimal points, adjust rounding to prevent decimals
= 1.0.10 - 9/23/22 =
* Updated - Use refund ID instead of order ID when processing refund.
* Updated - Don't override the shipping address first, last name if already populated on checkout page
* Fixed - WooFunnels One Click upsell, upsell not being triggered
= 1.0.9 - 9/14/22 =
* Fixed - Restrict item name length to 127 characters to prevent invalid schema error
* Updated - WC tested up to: 6.9
* Updated - Improved WooCommerce Blocks UI
= 1.0.8 - 9/12/22 =
* Updated - Improved billing address validations
* Updated - Only fill the billing email address field with the paypal email address if the field is blank
* Fixed - Compatibility with WooCommerce PayPal Payments recurring payments
* Added - Option to disable the credit card button tagline
= 1.0.7 - 8/29/22 =
* Updated - Improved Payer address validation for digital products
= 1.0.6 - 8/8/22 =
* Updated - WC tested up to: 6.8
* Added - Better compatability with the WooCommerce Checkout Add-Ons plugin
* Added - Better compatability with the WooCommerce Advanced Shipping Packages plugin
= 1.0.5 =
* Added - Compatibility with the [WooCommerce PayPal Checkout Gateway](https://wordpress.org/plugins/woocommerce-gateway-paypal-express-checkout/)
 plugin to ensure subscriptions process automatically and seamlessly when merchants switch.
* Added - Compatibility with the WooCommerce PayPal AngellEYE plugin to ensure subscriptions process automatically and seamlessly when merchants switch
= 1.0.4 - 7/21/22 =
* Fixed - CheckoutWC discounted order bump error
* Updated - WC tested up to: 6.7
* Added - Populate shipping_phone value if it exists and PayPal provides customer's phone number
= 1.0.3 - 6/27/22 =
* Fixed - PayLater messaging if option "Display prices in the shop" is enabled.
* Added - PayPal option in the CheckoutBlock payment gateways section and in the Express section
* Updated - Improved autofill logic for billing and shipping address
* Updated - WC tested up to: 6.6
= 1.0.2 - 5/20/22 =
* Fixed - Malformed request error during order creation if Payer's billing address is not valid
= 1.0.1 - 5/11/22 =
* Updated - Deactivation modal on Admin plugins page
* Updated - Improved error handling for PayPal script params.
= 1.0.0 - 5/4/22 =
* Initial release