<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}

function tm_get_price_decimal_separator() {
	if (function_exists('wc_get_price_decimal_separator')){
		return wc_get_price_decimal_separator();
	}
	$separator = stripslashes( get_option( 'woocommerce_price_decimal_sep' ) );
	return $separator ? $separator : '.';
}

function tm_convert_local_numbers($input=""){
	$locale   = localeconv();
	$decimals = array( tm_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'] );

	// Remove whitespace from string
	$input = preg_replace( '/\s+/', '', $input );

	// Remove locale from string
	$input = str_replace( $decimals, '.', $input );

	// Trim invalid start/end characters
	$input = rtrim( ltrim( $input, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

	return $input;
}

function tm_needs_wc_db_update(){
	$_tm_current_woo_version=get_option( 'woocommerce_db_version' );
	$_tm_needs_wc_db_update=false;
	if (version_compare( get_option( 'woocommerce_db_version' ), '2.3', '<' ) && version_compare( get_option( 'woocommerce_version' ), '2.4', '<' ) ){
		$_tm_notice_check='_wc_needs_update';
		$_tm_needs_wc_db_update=get_option( $_tm_notice_check );
	}else{
		$_tm_notice_check='woocommerce_admin_notices';
		$_tm_needs_wc_db_update=in_array( 'update', get_option( 'woocommerce_admin_notices', array() ) );
	}
	return $_tm_needs_wc_db_update;
}

if (!tm_needs_wc_db_update() && !function_exists('wc_get_product') && version_compare( get_option( 'woocommerce_db_version' ), '2.2', '<' ) ){
	function wc_get_product( $the_product = false, $args = array() ) {
		return get_product( $the_product, $args );
	}
}
if (!function_exists('tm_woocommerce_check')){
	function tm_woocommerce_check(){
	    $active_plugins = (array) get_option( 'active_plugins', array() );
	    if ( is_multisite() ){
		   $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	    }
	    return !tm_needs_wc_db_update() && in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
	}
}
if (!function_exists('tm_woocommerce_check_only')){
	function tm_woocommerce_check_only(){
	    $active_plugins = (array) get_option( 'active_plugins', array() );
	    if ( is_multisite() ){
		   $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	    }
	    return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
	}
}
if (!function_exists('tm_woocommerce_subscriptions_check')){
	function tm_woocommerce_subscriptions_check(){
	    $active_plugins = (array) get_option( 'active_plugins', array() );
	    if ( is_multisite() ){
		   $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	    }
	    return in_array( 'woocommerce-subscriptions/woocommerce-subscriptions.php', $active_plugins ) || array_key_exists( 'woocommerce-subscriptions/woocommerce-subscriptions.php', $active_plugins );
	}
}

/* Check for require json function for PHP 4 & 5.1 */
if (!function_exists('json_decode')) {
	include_once ('json/JSON.php');
	function json_encode($data) { $json = new Services_JSON(); return( $json->encode($data) ); }
	function json_decode($data) { $json = new Services_JSON(); return( $json->decode($data) ); }
}

/* Check for require json function for PHP 4 & 5.1 */
if (!function_exists('tm_get_roles')) {
	function tm_get_roles(){
		$result = array();
		$result["@everyone"] = __('Everyone',TM_EPO_TRANSLATION);
		$result["@loggedin"] = __('Logged in users',TM_EPO_TRANSLATION);
		global $wp_roles;
		if (empty($wp_roles)){
			$all_roles = new WP_Roles();	
		}else{
			$all_roles=$wp_roles;
		}
		$roles = $all_roles->roles;		
		if ($roles) {
			foreach ($roles as $role => $details) {
				$name = translate_user_role($details['name']);
				$result[$role] = $name;
			}
		}
		return $result;
	}
}

?>