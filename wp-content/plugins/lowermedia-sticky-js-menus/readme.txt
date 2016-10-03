=== LowerMedia Sticky.js Menus ===
Contributors: hawkeye126
Donate link: http://lowermedia.net/
Tags: js, sticky.js, multisite, navigation, headers, jquery, menu
Requires at least: 3.0.1
Tested up to: 4.1
Stable tag: 3.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Sticky Headers, Menus, Widgets, Anything!  WordPress plugin that integrates sticky.js and makes your primary navigation menu and/or header sticky (will 'stick' to top of screen when rolled over).  You can actually make html object sticky via plugin settings!

== Description ==

Sticky Headers, Menus, Widgets, Anything!  WordPress plugin that integrates sticky.js and makes your primary navigation menu and/or header sticky (will 'stick' to top of screen when rolled over).  You can actually make html object sticky via plugin settings! 

Activate and make your primary menu sticky!  
Sticky means having your navigation always visible, the nav fixes itself to the top of the page.  

This plugin uses the <a href='http://stickyjs.com'>Sticky.js</a> script, props and credit for creating that go to 
<a href="http://anthonygarand.com">Anthony Garand</a>, Thanks Anthony!   


<a href='http://lowermedia.net'>LowerMedia.Net</a>
<a href='http://petelower.com'>Dev'd by Pete</a>


More info:

This plugin is designed to work out of the box with a large number of popular themes if not all
the menu container and then manipulating the HTML tag w/ said class by way of JS

Plugins tested to work with this theme work a tad bit differently.  Instead of 
adding a class it uses custom js files that have the main navigational selectors 
already defined.  JS manipulates the menus by using the already defined tags. 

This plugin has been tested on a growing number of themes including: (will work with all themes with slight settings configuration)
   twentyfourteen,
   twentythirteen, 
   twentytwelve, 
   twentyeleven, 
   responsive, 
   wp-foundation, 
   required-foundation, 
   neuro, 
   Swtor_NeozOne_Wp, 
   lowermedia_one_page_theme, 
   expound, 
   customizr, 
   sixteen, 
   destro, 
   swift basic

   *Some CSS edits may be required


== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `LowerMedia_sticky-js-menus.zip` in the WordPress dashboard upload plugin section or unzip the file and upload the directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Make sure you have a menu defined under appearance -> menus


Popular Themes Default Target List:

   attitude = #access
   bushwick = #site-navigation
   destro = #menu
   expound = #site-navigation
   Isabelle = .nav
   lowermedia_one_page_theme
   neuro = #navigation_menu
   one-page = .header_wrapper
   required-foundation = #access
   responsive = main-nav
   spacious = #header-text-nav-container
   sixteen = #site-navigation
   spun = .site-navigation
   Swtor_NeozOne_Wp = art-nav
   twentythirteen = #navbar
   twentytwelve = #site-navigation
   twentyeleven = nav#access
   twentyten = #access
   virtue = #topbar
   wp-foundation = .top-nav


== Frequently Asked Questions ==

= Can I make Widgets sticky? =

Yes, you can make anything sticky! Set the widget's (or whatever else's) ID or Class in the primary or additional sticky object box setting on the under Settings -> Sticky.js Menus

= I am using one of the themes this plugin was tested to work on but it's not working. =

Please make sure your child theme has the same header navigation HTML syntax as the parent theme, this plugin is made to work with the latest iteration of the parent theme.

= My menu does not stick to the top of the page, there is some space between the menu and top of the page. =

Some theme styles or template styles may have overwritten the default styles, the site owner may have to tweak their own css to for ideal display.

== Changelog ==

= 1.0 =
*Plugin Launched

= 2.0 =
*Moving all js into two files instead of having individual files for specific themes
*Optimize and shorten code
*Increase number of themes tested with and supporting out of the box

= 2.0.1 =
*Fix jumpiness issue
*Add to list of supported browsers
*Optimization
*Bushwik theme support
*TwentyTwelve as base theme
*Documentation
*Name Correction
*Attitude theme support
*Destro Theme support
*Sixteen theme support
*Expound theme support
*LowerMedia One Page theme support
*Neuro theme support

= 2.0.2 =
*Add ability to target default menu
*Fix 'jumping around of menu in rare cases'
*Syntax correction

= 2.0.3 =
*Add improvements for Isabella and Spacious theme
*Documentation

= 3.0.0 =
*WILL NOW SUPPORT ALL THEMES WITH MINOR SETTINGS CONFIGURATION
*SECURITY UPDATE: Block direct access to PHP file
*FEATURE: Admin options area
*FEATURE: Now works without setting primary menu
*FEATURE: Option to set target div or nav (by class or id) to designate sticky.js target
*FEATURE: Option to disable stickyness at certain body width (hide on mobile)
*FEATURE: Option to target additional HTML tag (div, nav, header, etc) by class or id

= 3.1.0 =
*Namespacing with Classes
*PHP/JS code optimization
*Move all JS to one file, and out of js directory
*Reduce requests by 1
*Minify JS

== Upgrade Notice ==

Coming when needed

== Screenshots ==

Coming when needed