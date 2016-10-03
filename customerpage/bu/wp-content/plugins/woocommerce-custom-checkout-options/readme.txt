=== Plugin Name ===
Contributors: terrytsang
Plugin Name: WooCommerce Custom Checkout Options
Plugin URI:  http://terrytsang.com/shop/shop/woocommerce-custom-checkout-options/
Tags: woocommerce, custom fields, checkout, e-commerce
Requires at least: 2.7
Tested up to: 3.5.1
Stable tag: 1.0.8
Version: 1.0.8

== Description ==

A premium WooCommerce plugin that aims to implement customization for entire checkout process. 

In WooCommerce Settings Panel, there will be a new tab called 'Custom Checkout' where you can:
1. Enabled / Disabled the checkout fields (default / custom field)
2. Change Required settings for all the fields
3. Change Field Label Name and Placeholder
1. Enabled / Disabled the checkout fields (default / custom field)
2. Change Required settings for all the fields
3. Change Field Label Name and Placeholder
4. Add/Delete Custom Fields for the checkout form
5. There are 8 types of input available now (Text, Password, Textarea, Date, Country, State, Select, Checkbox)
6. Change Position (Billing, Shipping, Account, Order) for each field
7. Change Sort Order for each field
8. Update CSS for field class (built in class css : form-row-first, form-row-last, form-row-wide)

If you need to translate your own language, do use POEdit and open ‘custom-checkout-options.pot’ file and save the file as custom-checkout-options-[language code].po, then put that into languages folder for this plugin.
For example : custom-checkout-options-zh_CN.po is for chinese translation in wordpress language.

IMPORTANT NOTES
1. This plugin requires the WooCommerce Extension.
2. If you select "Select" Type, do set "Select Options" column field in string with comma separate format (For example: [None],General,Feedback,Request)
3. If you select "Checkbox" Type, do uncheck "Required" column field if the field is not compulsory

* All the custom checkout fields will be displayed at customer received email and view order details at My Account.
* Demo custom checkout form will be shown on my checkout page, do add anything and click checkout. Thank you.


== Installation ==

1. Upload the entire *woocommerce-custom-checkout-options* folder to the */wp-content/plugins/* directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce Settings panel at left sidebar menu and update the options at Tab *Custom Checkout* there.
4. That's it. You're ready to go and cheers!

== Screenshots ==

1. [screenhot-1.png] Screenshot Admin WooCommerce Settings Tab - Custom Checkout
2. [screenhot-2.png] Screenshot Admin Custom Checkout Tab - Add New Field and Options
3. [screenhot-3.png] Screenshot Checkout Page - Validation and Show DatePicker 
4. [screenhot-4.png] Screenshot Checkout Page - Show Select and Checkbox Field
5. [screenhot-5.png] Screenshot Admin - New Order Email
6. [screenhot-6.png] Screenshot Admin WooCommerce Orders
7. [screenhot-7.png] Screenshot Customer View Order Detail
8. [screenhot-8.png] Screenshot Customer Received Email
9. [screenhot-9.png] Screenshot Admin Add New Field Types (Time Picker & Color Picker)
10. [screenhot-10.png] Screenshot Checkout Page - Time Picker Field
11. [screenhot-11.png] Screenshot Checkout Page - Color Picker Field

== Changelog ==

= 1.0.8 =
* Localization for the plugin
* Add 2 new type of fields : Time picker and Color Picker, where you can use these for reservation and other purpose.

= 1.0.7 =
* Add "Custom Order Data" section at order edit page
* Fixed bugs

= 1.0.6 =
* Change order meta saving method
* Add clear div for fields other than form-row-first

= 1.0.5 =

* Add Sort Order for all checkout fields
* Add Remove function for all new custom fields
* Fixed datepicker bugs

= 1.0.4 =

* Add 2 new field types : Select and Checkbox

= 1.0.3 =

* Add custom order data to customer My Account order details page
* Add custom order data to customer order email
* Change jquery script that populate datepicker field

= 1.0.2 =

* Add dependent validation on selected country
* Fixed State/County default required setting and label name

= 1.0.1 =

* Fixed jquery date script to populate calendar on date custom field

= 1.0.0 =

* Initial Release
* Flexible customization on all woocommerce default checkout fields
* Able to add new custom fields with types (Text, Password, Textarea, Date, Country, State)

