<?php
/*
Plugin Name: WOOEXIM (Free)
Description: Advanced WooCommerce Store Products, Orders, Users, Product Categories, Coupons data Import Export with Multiple Filter, Export Management, Field Management, Scheduled Management.
Version: 2.0.0
Author: WOOEXIM.COM
Author URI: http://wooexim.com
*/
if (!defined('ABSPATH'))
    die("Can't load this file directly");

global $wpdb;

// Plugin version
if ( ! defined( 'WOOEXIM_PLUGIN_VERSION' ) ) {
	define( 'WOOEXIM_PLUGIN_VERSION', '2.0' );
}

// Plugin Folder Path
if ( ! defined( 'WOOEXIM_PLUGIN_DIR' ) ) {
	define( 'WOOEXIM_PLUGIN_DIR', WP_PLUGIN_DIR.'/'.dirname(plugin_basename( __FILE__ )) );
}

if(is_ssl()){
    define('WOOEXIM_PLUGIN_URL', str_replace('http://', 'https://', WP_PLUGIN_URL.'/wooexim'));
	define('WOOEXIM_CURRENT_PLUGIN_URL', str_replace('http://', 'https://', WP_PLUGIN_URL));
}else{
    define('WOOEXIM_PLUGIN_URL', WP_PLUGIN_URL.'/wooexim');
	define('WOOEXIM_CURRENT_PLUGIN_URL', WP_PLUGIN_URL);
}
	
global $WOOEXIM_AJAXURL;

$WOOEXIM_AJAXURL = admin_url('admin-ajax.php');
	
if ( ! defined( 'WOOEXIM_CSS_URL' ) )
	define( 'WOOEXIM_CSS_URL', WOOEXIM_PLUGIN_URL.'/css' );

if ( ! defined( 'WOOEXIM_JS_URL' ) )
	define( 'WOOEXIM_JS_URL', WOOEXIM_PLUGIN_URL.'/js' );
	
if ( ! defined( 'WOOEXIM_IMAGES_URL' ) )
	define( 'WOOEXIM_IMAGES_URL', WOOEXIM_PLUGIN_URL.'/images' );
	
if ( ! defined( 'WOOEXIM_CORE_DIR' ) )
	define( 'WOOEXIM_CORE_DIR', WOOEXIM_PLUGIN_DIR.'/core' );

if ( ! defined( 'WOOEXIM_CLASSES_DIR' ) )
	define( 'WOOEXIM_CLASSES_DIR', WOOEXIM_CORE_DIR.'/classes' );
		
if ( ! defined( 'WOOEXIM_VIEW_DIR' ) )
	define( 'WOOEXIM_VIEW_DIR', WOOEXIM_CORE_DIR.'/views' );
	
if ( ! defined( 'WOOEXIM_TEXTDOMAIN' ) )
	define( 'WOOEXIM_TEXTDOMAIN', 'wooexim' );
	
// Plugin site path
if ( ! defined( 'WOOEXIM_PLUGIN_SITE' ) ) 
{
	define( 'WOOEXIM_PLUGIN_SITE', 'http://wooexim.com' );	
}	
$wpupload_dir 	= wp_upload_dir();
$wooexim_upload_dir = $wpupload_dir['basedir'].'/wooexim';
$wooexim_upload_url = $wpupload_dir['baseurl'].'/wooexim';

define('WOOEXIM_UPLOAD_DIR', $wooexim_upload_dir);

define('WOOEXIM_UPLOAD_URL', $wooexim_upload_url);

wp_mkdir_p($wooexim_upload_dir);

global $wooexim_import_export, $wooexim_product, $wooexim_order, $wooexim_scheduled, $wooexim_user, $wooexim_auto_update, $wooexim_product_cat, $wooexim_coupon;
		
if(file_exists(WOOEXIM_CLASSES_DIR . '/wooexim_import_export.class.php'))
{
	require_once( WOOEXIM_CLASSES_DIR . '/wooexim_import_export.class.php' );

	$wooexim_import_export = new wooexim_import_export();	
}

if(file_exists(WOOEXIM_CLASSES_DIR . '/wooexim_product.class.php'))
{
	require_once( WOOEXIM_CLASSES_DIR . '/wooexim_product.class.php' );

	$wooexim_product = new wooexim_product();	
}

if(file_exists(WOOEXIM_CLASSES_DIR . '/wooexim_order.class.php'))
{
	require_once( WOOEXIM_CLASSES_DIR . '/wooexim_order.class.php' );

	$wooexim_order = new wooexim_order();	
}

if(file_exists(WOOEXIM_CLASSES_DIR . '/wooexim_scheduled.class.php'))
{
	require_once( WOOEXIM_CLASSES_DIR . '/wooexim_scheduled.class.php' );

	$wooexim_scheduled = new wooexim_scheduled();	
}

if(file_exists(WOOEXIM_CLASSES_DIR . '/wooexim_user.class.php'))
{
	require_once( WOOEXIM_CLASSES_DIR . '/wooexim_user.class.php' );

	$wooexim_user = new wooexim_user();	
}
if(file_exists(WOOEXIM_CLASSES_DIR . '/wooexim_product_cat.class.php'))
{
	require_once( WOOEXIM_CLASSES_DIR . '/wooexim_product_cat.class.php' );

	$wooexim_product_cat = new wooexim_product_cat();	
}
if(file_exists(WOOEXIM_CLASSES_DIR . '/wooexim_coupon.class.php'))
{
	require_once( WOOEXIM_CLASSES_DIR . '/wooexim_coupon.class.php' );

	$wooexim_coupon = new wooexim_coupon();	
}
?>