<?php

define('THEME_NAME', 'hudson');
define('THEME_PRETTY_NAME', 'Hudson');


/* * ******************************************************************************************** */
/*  Hudson Theme Setup */
/* * ******************************************************************************************** */

function tesla_theme_setup() {
    /*
     *
     * Translations can be added to the /languages/ directory.
     */
    load_theme_textdomain('hudson', get_template_directory() . '/languages');

    // Adds RSS feed links to <head> for posts and comments.
    add_theme_support('automatic-feed-links');

    // Adds support for WooCommerce
    add_theme_support('woocommerce');

    // This theme uses wp_nav_menu() in one location.
    register_nav_menu('primary', __('Primary Menu', 'hudson'));

    // This theme uses wp_nav_menu() in one other location. :)
    register_nav_menu('categories_menu', __('Categories Menu', 'hudson'));

    //add support for shortcodes in widgets
    add_filter('widget_text', 'do_shortcode');

    // This theme uses wp_nav_menu() in one location.
    // This theme uses a custom image size for featured images, displayed on "standard" posts.
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(624, 9999); // Unlimited height, soft crop
    // Removes admin bar at top
    //add_filter('show_admin_bar', '__return_false');
}

add_action('after_setup_theme', 'tesla_theme_setup');

/**
 * Manage dependencies with the TGM Plugin Activation class
 */
add_action('tgmpa_register', 'hudson_register_required_plugins');

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function hudson_register_required_plugins() {

    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
        array(
            'name' => 'WooCommerce (excelling eCommerce)',
            'slug' => 'woocommerce',
            'required' => TRUE,
            'version' => '2.0.13'
        ),
    );

    // Change this to your theme text domain, used for internationalising strings
    $theme_text_domain = 'hudson';

    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'domain' => $theme_text_domain, // Text domain - likely want to be the same as your theme.
        'default_path' => '', // Default absolute path to pre-packaged plugins
        'parent_menu_slug' => 'themes.php', // Default parent menu slug
        'parent_url_slug' => 'themes.php', // Default parent URL slug
        'menu' => 'install-required-plugins', // Menu slug
        'has_notices' => true, // Show admin notices or not
        'is_automatic' => false, // Automatically activate plugins after installation or not
        'message' => '', // Message to output right before the plugins table
        'strings' => array(
            'page_title' => __('Install Required Plugins', $theme_text_domain),
            'menu_title' => __('Install Plugins', $theme_text_domain),
            'installing' => __('Installing Plugin: %s', $theme_text_domain), // %1$s = plugin name
            'oops' => __('Something went wrong with the plugin API.', $theme_text_domain),
            'notice_can_install_required' => _n_noop('This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.'), // %1$s = plugin name(s)
            'notice_can_install_recommended' => _n_noop('This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.'), // %1$s = plugin name(s)
            'notice_cannot_install' => _n_noop('Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.'), // %1$s = plugin name(s)
            'notice_can_activate_required' => _n_noop('The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.'), // %1$s = plugin name(s)
            'notice_can_activate_recommended' => _n_noop('The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.'), // %1$s = plugin name(s)
            'notice_cannot_activate' => _n_noop('Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.'), // %1$s = plugin name(s)
            'notice_ask_to_update' => _n_noop('The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.'), // %1$s = plugin name(s)
            'notice_cannot_update' => _n_noop('Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.'), // %1$s = plugin name(s)
            'install_link' => _n_noop('Begin installing plugin', 'Begin installing plugins'),
            'activate_link' => _n_noop('Activate installed plugin', 'Activate installed plugins'),
            'return' => __('Return to Required Plugins Installer', $theme_text_domain),
            'plugin_activated' => __('Plugin activated successfully.', $theme_text_domain),
            'complete' => __('All plugins installed and activated successfully. %s', $theme_text_domain), // %1$s = dashboard link
            'nag_type' => 'updated' // Determines admin notice type - can only be 'updated' or 'error'
        )
    );

    tgmpa($plugins, $config);
}

/**
 * Registers our footer widget area.
 *
 */
function tesla_widgets_init() {

    register_sidebar(array(
        'name' => __('Sidebar Blog', 'hudson'),
        'id' => 'sidebar-1',
        'description' => __('Blog sidebar', 'hudson'),
        'before_widget' => '<div id="%1$s" class="side_element widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<div class="side_element_title widgettitle">',
        'after_title' => '</div>',
    ));

    register_sidebar(array(
        'name' => __('Footer Column 1', 'hudson'),
        'id' => 'sidebar-footer-1',
        'description' => __('First column in footer', 'hudson'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title widgettitle">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'name' => __('Footer Column 2', 'hudson'),
        'id' => 'sidebar-footer-2',
        'description' => __('Second column in footer', 'hudson'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title widgettitle">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'name' => __('Footer Column 3', 'hudson'),
        'id' => 'sidebar-footer-3',
        'description' => __('Third column in footer', 'hudson'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title widgettitle">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'name' => __('Footer Column 4', 'hudson'),
        'id' => 'sidebar-footer-4',
        'description' => __('Fourth column in footer', 'hudson'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title widgettitle">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'name' => __('Sidebar Contact Page', 'hudson'),
        'id' => 'sidebar-contact-page',
        'description' => __('Contact Page sidebar', 'hudson'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<div class="side_element_title widgettitle">',
        'after_title' => '</div>',
    ));

    register_sidebar(array(
        'name' => __('Sidebar Shop', 'hudson'),
        'id' => 'sidebar-shop',
        'description' => __('Shop Page sidebar', 'hudson'),
        'before_widget' => '<div id="%1$s" class="filter widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>',
    ));
}

add_action('widgets_init', 'tesla_widgets_init');

add_filter('woocommerce_breadcrumb_defaults','tt_woocommerce_breadcrumb_defaults');
function tt_woocommerce_breadcrumb_defaults(){
    return array(
        'delimiter' => ' &#47; ',
        'wrap_before' => '<div class="woo-path" itemprop="breadcrumb">',
        'wrap_after' => '</div>',
        'before' => '',
        'after' => '',
        'home' => _x('Home', 'breadcrumb', 'hudson'),
    );
}