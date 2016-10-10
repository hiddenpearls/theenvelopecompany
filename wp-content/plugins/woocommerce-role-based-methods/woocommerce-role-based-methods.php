<?php
/*
Plugin Name:       WooCommerce Role Based Methods
Plugin URI:        http://woothemes.com/woocommerce
Description:       This plugin provides an interface for role-based control over WooCommerce payment and shipping methods.
Version:           2.0.9
Author: WPBackOffice
Author URI: http://www.wpbackoffice.com
*/

/*  Copyright 2015  WPBackOffice  (email : support@wpbackoffice.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 *
 * @category            Plugin
 * @copyright           Copyright © 2015 WPBackOffice
 * @author              WPBackOffice
 * @package             RoleMethods
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'ea88b3bd7b5c2de3924b6291ff598710', '18732' );

/*----------------------------------------------------------------------------*
 * Internationalization
 *----------------------------------------------------------------------------*/

load_plugin_textdomain( 'woocommerce-role-based-methods', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

/*----------------------------------------------------------------------------*
 * Activation Hook
 *----------------------------------------------------------------------------*/

add_action('admin_notices', 'checkrolegroups_admin_notice');

function checkrolegroups_admin_notice() {
    global $current_user ;
    $options = get_option('woocommerce_role_methods_options');
        $user_id = $current_user->ID;
        /* Check that the user hasn't already clicked to ignore the message */
    if ( ! get_user_meta($user_id, 'checkrolegroups_ignore_notice') && (!isset($options['ship-groups-enable']) && !isset($options['pay-groups-enable'])) && function_exists('_groups_get_tablename') && $options)  {
        echo '<div class="updated"><p>'; 
        printf(__('Please verify your Groups settings in WooCommerce -> Settings -> Role Based Methods | <a href="%1$s">Hide Notice</a>'), '?checkrolegroups_ignore=0');
        echo "</p></div>";
    }
}

add_action('admin_init', 'checkrolegroups_admin_notice_nag_ignore');

function checkrolegroups_admin_notice_nag_ignore() {
    global $current_user;
        $user_id = $current_user->ID;
        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET['checkrolegroups_ignore']) && '0' == $_GET['checkrolegroups_ignore'] ) {
            add_user_meta($user_id, 'checkrolegroups_ignore_notice', 'true', true);
    }
}



/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-woocommerce-role-based-methods.php' );

add_action( 'plugins_loaded', array( 'WC_Role_Methods', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 * The code below is intended to to give the lightest footprint possible.
 */

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-woocommerce-role-based-methods-admin.php' );
	add_action( 'plugins_loaded', array( 'WC_Role_Methods_Admin', 'get_instance' ) );

}