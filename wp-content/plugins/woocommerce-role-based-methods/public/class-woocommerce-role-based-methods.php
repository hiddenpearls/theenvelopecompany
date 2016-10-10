<?php
/**
 * WooCommerce Role Based Methods
 *
 * @package   WC_Role_Methods
 * @author    Bryan Purcell <support@wpbackoffice.com>
 * @license   GPL-2.0+
 * @link      http://woothemes.com/woocommerce
 * @copyright 2014 WPBackOffice
 */

/**
 * @package WC_Role_Methods
 * @author  WPBackOffice <support@wpbackoffice.com>
 */
class WC_Role_Methods {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   2.0.0
	 *
	 * @var     string
	 */
	const VERSION = '2.0.7';

	/**
	 * @TODO - Rename "plugin-name" to the name your your plugin
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    2.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'woocommerce-role-based-methods';

	/**
	 * Instance of this class.
	 *
	 * @since    2.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     2.0.0
	 */
	private function __construct() {
		if (is_woocommerce_active()) {
			add_filter('woocommerce_available_payment_gateways',array( $this,'get_available_payment_gateways'));
			
			// General Options

			$this->options = get_option('woocommerce_role_methods_options');

			$this->shipping_options = get_option("woocommerce_shipping_roles");
			$this->payment_options = get_option("woocommerce_payment_roles");

			//Set up Shipping group options.

			$this->allowed_payment_groups = get_option("woocommerce_group_payment_roles");
			$this->allowed_shipping_groups = get_option("woocommerce_group_shipping_roles");

			//Check for 2.1

			if(function_exists('WC')) { 
				add_filter('woocommerce_package_rates',array( $this,'get_available_shipping_methods'));
			} else {
				add_filter('woocommerce_available_shipping_methods',array( $this,'get_available_shipping_methods'));
			}
		}
	}

	private function is_version_between($start_version, $end_version) {
		if(version_compare( WOOCOMMERCE_VERSION, $start_version, '>=' ) && version_compare( WOOCOMMERCE_VERSION, $end_version, '<' )) {
			return true;
		} else {
			return false;
		}
	}
	private function using_ship_role_based_groups() {
		if(isset($this->options['ship-groups-enable']) && $this->options['ship-groups-enable']== "Yes") {
			return true;
		} else {
			return false;
		}
	}

	private function using_pay_role_based_groups() {
		if(isset($this->options['pay-groups-enable']) && $this->options['pay-groups-enable']== "Yes") {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Return the slug.
	 *
	 * @since    2.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     2.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

/**
 * Get a list of all shipping methods globally available for use.
 *
 * @param array $methods all shipping methods globally available for use.
 * @return array $avail_methods only shipping methods deemed permitted by the Role-Based Plugin
 */
public function get_available_shipping_methods($methods) {
	if ( !isset( $wp_roles ) )
		$wp_roles = new WP_Roles();

	$the_roles = $wp_roles->roles;
	$current_user_roles = array();

	global $current_user;
	global $woocommerce;
	
	if ( is_user_logged_in() ) {
		$user = new WP_User( $current_user->ID );
		if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
			foreach ( $user->roles as $role ) {
				$current_user_roles[] = strtolower($the_roles[$role]['name']);
			}
		}
	} else {
		$current_user_roles[] = 'Guest';
	}

	unset($avail_methods);
	$avail_methods = array();

	foreach($methods as $method_id => $method) {
		foreach($current_user_roles as $user_role) {

			//If this method is a sub-method (ie a specific USPS service, grab the parent's method id)
			if(isset($method->method_id)) {
				$parent_method_id = $method->method_id;
			} else {
				$parent_method_id = $method_id;
			}

			if($this->check_rolea_methods($user_role, $parent_method_id)) {
				$avail_methods[$method_id] = $method;
			}
		}
	}

	return $avail_methods;
}

	/**
	 * Accept a user role and a shipping method, return true or false depending on whether it's allowed.
	 *
	 * @param string $current_user_role The current user role as-per the logged in user
	 * @param string $method_id - id of the shipping method to be checked.rr x
	 * @return bool true if allowed, false if not allowed
	 */
		public function check_rolea_methods($current_user_role, $method_id) {
			global $woocommerce;

			if(sizeof($woocommerce->shipping->shipping_methods) > 0) {
				$shipping_methods = $woocommerce->shipping->shipping_methods;
			} else {
				$shipping_methods = $woocommerce->shipping->load_shipping_methods();
			}

			$the_role = $current_user_role;

			//Check if user is in one of the allowed groups, but only if groups plugin is installed.

			$active_in_groups = false;
			if(function_exists('_groups_get_tablename') && $this->allowed_shipping_groups) {
				foreach($this->allowed_shipping_groups as $group_id => $group_allowed_methods) {
					if(Groups_User_Group::read( get_current_user_id() , $group_id ) && isset($group_allowed_methods[$method_id]) && $group_allowed_methods[$method_id] == 'on')
						$active_in_groups = true;
				}
			}

			$active_in_roles = false;

			if( ( isset( $this->shipping_options[ $the_role ][ $method_id ] ) && $this->shipping_options[ $the_role ][ $method_id ] == 'on' ) || !$this->shipping_options){
				$active_in_roles = true;
			} elseif($the_role == "Guest" && isset( $this->shipping_options[ 'Guest' ][ $method_id ] ) && $this->shipping_options[ 'Guest' ][ $method_id ] =='on'){
				$active_in_roles = true;
			} else {
				$active_in_roles = false;
			}

			//Guests aren't in groups, so only check the role settings for guests.

			if($the_role == "Guest" && $active_in_roles) {
				return true;
			}

			//Check the operator - either AND or OR

			if($this->using_ship_role_based_groups() && isset($this->options['shipping_operator']) && $this->options['shipping_operator'] == 'and') {

				if($active_in_groups && $active_in_roles) {
					return true;
				} else {
					return false;
				}
			} else {
				if($active_in_groups || $active_in_roles) {
					return true;
				} else {
					return false;
				}				
			}
		}

		public function check_rolea($current_user_role, $gateway_id) {
			global $current_user;

			//Check if user is in one of the allowed groups, but only if groups plugin is installed.
			$active_in_groups = false;

			if( function_exists('_groups_get_tablename') && $this->allowed_payment_groups ) {
				foreach($this->allowed_payment_groups as $group_id => $group_allowed_gateways) {
					if( Groups_User_Group::read( get_current_user_id() , $group_id ) && isset($group_allowed_gateways[$gateway_id]) && $group_allowed_gateways[$gateway_id] == 'on' ) {
						$active_in_groups = true;
					}
				}
			}
			
			$active_in_roles = false;
			if ( ( isset( $this->payment_options[ $current_user_role ][ $gateway_id ] ) && $this->payment_options[ $current_user_role ][ $gateway_id ] == 'on' ) || $this->payment_options == false ){
				$active_in_roles = true;
			} elseif( !is_user_logged_in() && isset($this->payment_options['Guest'][$gateway_id]) && $this->payment_options['Guest'][$gateway_id] =='on' )
				$active_in_roles = true;
			else
				$active_in_roles = false;

			//Guests aren't in groups, so only check the role settings for guests.

			if($current_user_role == "Guest" && $active_in_roles) {
				return true;
			}

			//Check the operator - either AND or OR

			if($this->using_pay_role_based_groups() && isset($this->options['payment_operator']) && $this->options['payment_operator'] == 'and') {
				if($active_in_groups && $active_in_roles) {
					return true;
				} else {
					return false;
				}
			} else {
				if($active_in_groups || $active_in_roles) {
					return true;
				} else {
					return false;
				}				
			}
		}

		/**
		 * Get a list of all gateway globally available for use.
		 *
		 * @param array $gateways loop through all enabled payment gateways, but remove any gateways that are not permitted through Role-Based Methods.
		 * @return array Array of permitted gateways
		 */
		function get_available_payment_gateways($gateways) {
			global $current_user;
			global $woocommerce;

			if ( !isset( $wp_roles ) )
			$wp_roles = new WP_Roles();

			$the_roles = $wp_roles->roles;
			$current_u = 'Guest';

			if ( is_user_logged_in() ) {
				$user = new WP_User( $current_user->ID );
				if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
					foreach ( $user->roles as $role ) {
						$current_u=strtolower($the_roles[$role]['name']);
					}
				}
			}

			$avail_gateways = array();
			foreach($gateways as $gateway) {
				if($this->check_rolea($current_u, $gateway->id)) {
					$avail_gateways[$gateway->id] = $gateway;
				}
			}

			return $avail_gateways;
		}


	}
