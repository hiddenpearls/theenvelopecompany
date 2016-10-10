<?php
/**
 * WooCommerce Role Based Methods
 *
 * @package   WC_Role_Methods
 * @author    Bryan Purcell <support@wpbackoffice.com>
 * @license   GPL-2.0+
 * @link      http://woothemes.com/woocommerce
 * @copyright 2015 WPBackOffice
 */

/**
 * @package WC_Role_Methods_Admin
 * @author  WPBackOffice <support@wpbackoffice.com>
 */

class WC_Role_Methods_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    2.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since     2.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     2.0.0
	 */
	private function __construct() {

		$plugin = WC_Role_Methods::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_roles_admin_menu' ) );
		add_action( 'admin_init', array($this, 'add_role_methods_save_flash'));
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_scripts' ));

		/* Add sanitaion case for role option */

		add_filter( 'sanitize_option_woocommerce_payment_roles', array($this, 'sanitize_role_options'), 10, 2);
		add_filter( 'sanitize_option_woocommerce_shipping_roles', array($this, 'sanitize_role_options'), 10, 2);

		/* Add sanitaion case for group option */

		add_filter( 'sanitize_option_woocommerce_group_payment_roles', array($this, 'sanitize_group_options'), 10, 2);
		add_filter( 'sanitize_option_woocommerce_group_shipping_roles', array($this, 'sanitize_group_options'), 10, 2);

		/* Add sanitaion case for general plugin options */

		add_filter( 'sanitize_option_woocommerce_role_methods_options', array($this, 'sanitize_general_settings'), 10, 2);


	}

	/**
	 * Clear any existing shipping quote transients, because the shipping
	 * when the shipping policies changes
	 * 
	 * @since     2.0.5
	 */

	private function clear_shipping_transients() {
		global $wpdb;
		$sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '%transient_timeout_wc_ship%' OR option_name LIKE '%transient_wc_ship%'";
		$wpdb->query($sql);
	}

	/**
	 * Return true if the mollie gateway plugin is activated, false otherwise.
	 *
	 * @since     2.0.3
	 *
	 * @return    boolean
	 */

	private function has_mollie() {
		if(class_exists('WC_Mollie')) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Return the methods' correct title
	 *
	 * @since     2.0.3
	 *
	 * @return    string
	 */
	public function get_col_title($method) {
		if(isset($method->title)) {
			$title = $method->title;
		} elseif(isset($method->description)) {
			$title = $method->description;
		} else {
			$title = 'Unknown Method';
		}
		return $title;
	}

	/* Display Settings Updated Flash When Role Method Settings are updated */

	public function add_role_methods_save_flash() {
		if(isset($_POST['settings-updated']) && $_POST['settings-updated']) {
			add_action('admin_notices', array($this, 'save_role_methods_admin_notice'));
		}
	}

	/* Flash Updated Alert*/

	public function save_role_methods_admin_notice(){
		include_once( 'views/flash-updated.php' );
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
	 * Register and enqueue role-methods specific scripts.
	 *
	 * @since     2.0.0
	 *
	 * @return    null Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), WC_Role_Methods::VERSION );
			wp_enqueue_script( $this->plugin_slug .'-admin-scripts', plugins_url( 'assets/js/main.js', __FILE__ ), array(), WC_Role_Methods::VERSION );
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since     2.0.0
	 */
	public function add_roles_admin_menu() {
		$this->plugin_screen_hook_suffix = add_submenu_page('woocommerce', __( 'Role Based Methods', 'woocommerce-role-based-methods-settings' ), __( 'Role Based Methods', 'woocommerce-role-based-methods' ), 'manage_woocommerce', 'woocommerce-role-based-methods-settings', array($this, 'display_woocommerce_role_based_methods_settings' ));
	}

	/**
	 * Print single Shipping row with all methods, for a specific roll.
	 *
	 * @param string $roll Role row to print.
	 * @param array $shipping_methods
	 * @param array $methodarray stored settings for the role based methods plugin
	 */

	function print_shipping_row($roll,  $shipping_methods,  $methodarray) {
		echo "<tr><td><p><b>" . ucfirst($roll) . "</b></p></td>";

		foreach($shipping_methods as $s):

			if ( ( isset( $methodarray[ $roll ][ $s->id ] ) && $methodarray[ $roll ][ $s->id ] == 'on' ) || $methodarray == false )
				$checked=' checked ';
			else
				$checked='';

			echo "<td><input type='checkbox' name='shipping_is_enabled[$roll][$s->id]' $checked/></td>";
			endforeach;

			echo "</tr>";
		}

	/**
	 * Get the title for the method. Title is a user supplied version, method_title is used as a fallback
	 *
	 * @param obj $method Method
	 */

		function get_shipping_method_title($method) {
			if($method->title) {
				return $method->title;
			} elseif($method->method_title) {
				return $method->method_title;
			} else {
				return "";
			}
		}


	/**
	 * Print single Shipping row with all methods, for a specific group.
	 *
	 * @param string $roll Role row to print.
	 * @param array $shipping_methods
	 * @param array $methodarray stored settings for the role based methods plugin
	 */

	function print_shipping_group_row($group,  $shipping_methods,  $group_methodarray) {
		echo "<tr><td><p><b>" . ucfirst($group->name) . "</b></p></td>";

		foreach($shipping_methods as $s):

			if ( ( isset( $group_methodarray[ $group->group_id ][ $s->id ] ) && $group_methodarray[ $group->group_id ][ $s->id ] == 'on' ) || $group_methodarray == false )
				$checked=' checked ';
			else
				$checked='';

			echo "<td><input type='checkbox' name='group_shipping_is_enabled[$group->group_id][$s->id]' $checked/></td>";
			endforeach;

			echo "</tr>";
		}

	/**
	 * Print single Gateway row with all gateways for a specific $roll.
	 *
	 * @param string $roll Role identity row to print.
	 * @param array $shipping_methods
	 * @param array $methodarray
	 */
	function print_gateway_row($roll, $payment_methods, $gatewayarray) {

		echo "<tr><td><p><b>" . ucfirst($roll) . "</b></p></td>";
		foreach($payment_methods as $g):

			if ( ( isset( $gatewayarray[ $roll ][ $g->id ] ) && $gatewayarray[ $roll ][ $g->id ] == 'on' ) || $gatewayarray == false )
				$checked=' checked ';
			else
				$checked='';

			echo "<td><input type='checkbox' name='payment_is_enabled[$roll][$g->id]' $checked/></td>";

			endforeach;
			echo "</tr>";
		}

	/**
	 * Print single Gateway row with all gateways for a specific group.
	 *
	 * @param string $roll Role identity row to print.
	 * @param array $shipping_methods
	 * @param array $methodarray
	 */
	function print_group_gateway_row($group, $payment_methods, $group_gatewayarray) {
		echo "<tr><td><p><b>" . $group->name . "</b></p></td>";
		foreach($payment_methods as $g):

			if ( ( isset( $group_gatewayarray[ $group->group_id ][ $g->id ] ) && $group_gatewayarray[ $group->group_id ][ $g->id ] == 'on' ) || $group_gatewayarray == false )
				$checked=' checked ';
			else
				$checked='';

			echo "<td><input type='checkbox' name='group_payment_is_enabled[$group->group_id][$g->id]' $checked/></td>";

			endforeach;
			echo "</tr>";
		}

	/**
	 * Render the settings page for the shipping methods page
	 *
	 * @since     2.0.3
	 */

	/* https://gist.github.com/esthezia/5804445 */
	public function display_woocommerce_payment_page() {
		global $woocommerce;
		global $wp_roles;

		// Clean post variable
		$posted = $this->clean_multidimensional_array($_POST);

		// Check the user capabilities
		if ( !current_user_can( 'manage_woocommerce' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'woocommerce-payment_shipping' ) );
		}

		if ( isset( $posted['payment_is_enabled'] ) ) {
			update_option( 'woocommerce_payment_roles', $posted['payment_is_enabled'] );
		}

		if ( isset( $posted['group_payment_is_enabled'] ) ) {
			update_option( 'woocommerce_group_payment_roles', $posted['group_payment_is_enabled'] );
		}

		if ( isset( $posted['woocommerce_role_methods_options'] ) && is_array($posted['woocommerce_role_methods_options'])) {
			$current_options = get_option('woocommerce_role_methods_options');
			foreach($posted['woocommerce_role_methods_options'] as $option => $value) {
				$current_options[wc_clean($option)] = wc_clean($value);
			}

			if(!isset($posted['woocommerce_role_methods_options']['pay-groups-enable'])) {
				$current_options['pay-groups-enable'] = 'No';
			} else {
				$current_options['pay-groups-enable'] = 'Yes';				
			}

			update_option( 'woocommerce_role_methods_options', $current_options );
		}

		$payment_methods = $this->get_all_payment_methods();

		$gatewayarray = get_option('woocommerce_payment_roles');
		$group_gatewayarray = get_option('woocommerce_group_payment_roles');

		$groups = $this->get_all_groups();
		$options = get_option('woocommerce_role_methods_options');

		include_once( 'views/admin-payment.php' );
	}

	public function get_all_payment_methods() {
		global $woocommerce;
		$payment_methods_loaded = $woocommerce->payment_gateways->payment_gateways;

		$payment_methods = array();
		foreach($payment_methods_loaded as $method){
			if ($method->enabled == 'yes') {
				if($method->title == 'Mollie' && $this->has_mollie()) {
					$instance = $GLOBALS['wc_mollie'];
					$gateway = $instance->get_gateway();
					$methods = $gateway->get_methods();
					foreach($methods as $method) {
						$method->id = 'mollie_' . $method->id;
						$method->description = $method->description . ' (Mollie)';
						$payment_methods[] = $method;					
					}
				} else {
					$payment_methods[] = $method;	
				}
			}
		}
		return $payment_methods;
	}

	public function get_all_payment_method_slugs() {
		$payment_methods = $this->get_all_payment_methods();
		$slugs = array();
		foreach($payment_methods as $method){
			$slugs[] = $method->id;
		}
		return $slugs;
	}

	/**
	 * Render the settings page for the payment methods page.
	 *
	 * @since     2.0.0
	 */
	public function display_woocommerce_shipping_page() {

		global $woocommerce;
		global $wp_roles;

		$posted = $this->clean_multidimensional_array($_POST);

		// Check the user capabilities
		if ( !current_user_can( 'manage_woocommerce' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'woocommerce-payment_shipping' ) );
		}

		if ( isset( $posted['shipping_is_enabled'] ) ) {
			$uploaded_shipping_options = $posted['shipping_is_enabled'];
			$current_shipping_options = get_option('woocommerce_shipping_roles');
			if($uploaded_shipping_options != $current_shipping_options) {
				update_option( 'woocommerce_shipping_roles', $posted['shipping_is_enabled'] );
				$this->clear_shipping_transients();				
			}
		}

		if ( isset( $posted['group_shipping_is_enabled'] ) ) {
			update_option( 'woocommerce_group_shipping_roles', $posted['group_shipping_is_enabled'] );
		}

		if ( isset( $posted['woocommerce_role_methods_options'] ) && is_array($posted['woocommerce_role_methods_options'])) {
			$current_options = get_option('woocommerce_role_methods_options');
			foreach($posted['woocommerce_role_methods_options'] as $option => $value) {
				$current_options[wc_clean($option)] = wc_clean($value); //Sanitize the key and value
			}

			if(!isset($posted['woocommerce_role_methods_options']['ship-groups-enable'])) {
				$current_options['ship-groups-enable'] = 'No';
			} else {
				$current_options['ship-groups-enable'] = 'Yes';				
			}

			update_option( 'woocommerce_role_methods_options', $current_options );
		}


		$shipping_methods = $this->get_all_shipping_methods();

		$methodarray = get_option('woocommerce_shipping_roles');
		$group_methodarray = get_option('woocommerce_group_shipping_roles');

		$groups = $this->get_all_groups();
		$options = get_option('woocommerce_role_methods_options');
		include_once( 'views/admin-shipping.php' );
	}

	public function get_all_shipping_methods() {
		global $woocommerce;
		if(sizeof($woocommerce->shipping->shipping_methods) > 0){
			$shipping_methods = $woocommerce->shipping->shipping_methods;
		} else {
			$shipping_methods = $woocommerce->shipping->load_shipping_methods();
		}

		return $shipping_methods;
	}

	public function get_site_roles() {
		global $wp_roles;
		$site_roles = $wp_roles->get_names();
		return array_keys($site_roles);
	}

	/**
	 * Validate posted roles options
	 *
	 * @since     2.0.6
	 */

	public function sanitize_role_options( $updated_value, $option_name) {
		$validation_passed = true;

		if(!isset($updated_value) || !is_array($updated_value)) {
			$validation_passed = false;
		}

		//Now loop through the array to make sure everything is valid.

		foreach($updated_value as $role => $role_settings) {

			// Validate the key (payment method) first.
			if(!is_string($role)) {
				$validation_passed = false;
			}

			//Next, validate the value, 

			if(is_array($role_settings)) {
				foreach($role_settings as $method => $setting) {
					if(!is_string($method)) {
						$validation_passed = false;
					}

					if($setting != 'on') {
						$validation_passed = false;
					}
				}
			} else {
				$validation_passed = false;
			}
		}
		//If everything checks out, return the new value.
		if($validation_passed) {
			return $updated_value;
		} else {
			$original_option_value = get_option( $option_name );
			return $original_option_value;
		}
	}

	/**
	 * Validate posted groups options
	 *
	 * @since     2.0.6
	 */

	public function sanitize_group_options( $updated_value, $option_name) {
		$validation_passed = true;

		if(!isset($updated_value) || !is_array($updated_value)) {
			$validation_passed = false;
		}

		//Now loop through the array to make sure everything is valid.

		foreach($updated_value as $group => $group_settings) {

			// Validate the key (payment method) first.
			if(!is_int($group)) {
				$validation_passed = false;
			}

			//Next, validate the value, 

			if(is_array($group_settings)) {
				foreach($group_settings as $method => $setting) {
					if(!is_string($method)) {
						$validation_passed = false;
					}

					if($setting != 'on') {
						$validation_passed = false;
					}
				}
			} else {
				$validation_passed = false;
			}
		}
		//If everything checks out, return the new value.
		if($validation_passed) {
			return $updated_value;
		} else {
			$original_option_value = get_option( $option_name );
			return $original_option_value;
		}
	}

	/**
	 * Verify all the roles and methods exist and the options array is in good shape before saving the option
	 *
	 * @since     2.0.6
	 */

	public function sanitize_general_settings( $updated_value, $option_name ) {
		$validation_passed = true;

		if(!isset($updated_value) || !is_array($updated_value)) {
			$validation_passed = false;
		}

		//Now loop through the array to make sure everything is valid.

		foreach($updated_value as $setting_label => $setting_value) {

			// Validate the key (payment method) first.
			if(!is_string($setting_label) || !is_string($setting_value)) {
				$validation_passed = false;
			}
		}

		//If everything checks out, return the new value.
		if($validation_passed) {
			return $updated_value;
		} else {
			$original_option_value = get_option( $option_name );
			return $original_option_value;
		}
	}

	public function display_woocommerce_role_based_methods_settings() {
		
		include_once( 'views/admin-settings-tabs.php' );
		
		if((isset($_GET['tab']) && $_GET['tab'] == 'pay-role-gateways') || !isset($_GET['tab'])) {
			$this->display_woocommerce_payment_page();

		} elseif(isset($_GET['tab']) && $_GET['tab'] == 'ship-role-methods') {
			$this->display_woocommerce_shipping_page();
		}
	}

	function get_all_groups() {
		global $wpdb;
		if( function_exists('_groups_get_tablename')){
			$groups_table = _groups_get_tablename( 'group' );
			return $wpdb->get_results( "SELECT * FROM $groups_table ORDER BY name" );
		} else {
			return false;
		}
	}

	/* Based on https://gist.github.com/esthezia/5804445 */

	private function clean_multidimensional_array ($data = array()) {
		if (!is_array($data) || !count($data)) {
			return array();
		}
		foreach ($data as $k => $v) {
			if (!is_array($v) && !is_object($v)) {
				$data[$k] = wc_clean($v);
			}
			if (is_array($v)) {
				$data[$k] = $this->clean_multidimensional_array($v);
			}
		}
		return $data;
	}

}
