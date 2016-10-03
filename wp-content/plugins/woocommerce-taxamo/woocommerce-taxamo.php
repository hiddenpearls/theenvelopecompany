<?php
/*
	Plugin Name: WooCommerce Taxamo
	Plugin URI: http://www.woothemes.com/
	Description: Use Taxamo services in your WooCommerce shop.
	Version: 1.2.4
	Author: WooThemes / Barry Kooij
	Author URI: http://www.woothemes.com/
	License: GPL v3

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . '/woo-includes/woo-functions.php' );
}

// Enable updates
woothemes_queue_update( plugin_basename( __FILE__ ), 'f4234d8df5202ab6d8162c4c0e50cb3c', '583608' );

/**
 * Class WooCommerce_Taxamo
 *
 * @since 1.0.0
 */
class WooCommerce_Taxamo {

	const VERSION = '1.2.4';

	const TAXAMO_URL = 'http://wthms.co/taxamo';

	/**
	 * Get the plugin file
	 *
	 * @static
	 * @since  1.0.0
	 * @access public
	 *
	 * @return String
	 */
	public static function get_plugin_file() {
		return __FILE__;
	}

	/**
	 * A static method that will setup the autoloader
	 *
	 * @static
	 * @since  1.0.0
	 * @access private
	 */
	private static function setup_autoloader() {
		require_once( plugin_dir_path( self::get_plugin_file() ) . '/classes/class-wc-ta-autoloader.php' );
		$autoloader = new WC_TA_Autoloader();
		spl_autoload_register( array( $autoloader, 'load' ) );
	}

	/**
	 * Constructor
	 */
	public function __construct() {

		// Check if WC is activated
		if ( ! WC_Dependencies::woocommerce_active_check() ) {
			add_action( 'admin_notices', array( $this, 'notice_activate_wc' ) );
		} else if ( version_compare( WC_VERSION, '2.2.9', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'notice_version_wc' ) );
		} else {
			$this->init();
		}
	}

	/**
	 * Display the notice
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function notice_activate_wc() {
		?>
		<div class="error">
			<p><?php printf( __( 'Please install and activate %sWooCommerce%s in order for the WooCommerce Taxamo extension to work!', 'woocommerce-taxamo' ), '<a href="' . admin_url( 'plugin-install.php?tab=search&s=WooCommerce&plugin-search-input=Search+Plugins' ) . '">', '</a>' ); ?></p>
		</div>
	<?php
	}

	/**
	 * Display the notice
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function notice_version_wc() {
		?>
		<div class="error">
			<p><?php _e( 'Please update WooCommerce to <strong>version 2.2.9 or higher</strong> in order for the WooCommerce Taxamo extension to work!', 'woocommerce-taxamo' ); ?></p>
		</div>
	<?php
	}

	/**
	 * Init the plugin
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 */
	private function init() {

		// Load plugin textdomain
		load_plugin_textdomain( 'woocommerce-taxamo', false, plugin_dir_path( self::get_plugin_file() ) . 'languages/' );

		// Setup the autoloader
		self::setup_autoloader();

		// The VAT number Field
		$vat_number_field = new WC_TA_Vat_Number_Field();
		$vat_number_field->setup();

		// Setup the Checkout VAT stuff
		$checkout_vat = new WC_TA_Checkout_Vat();
		$checkout_vat->setup();

		// Setup Taxamo manager
		$taxamo_manager = new WC_TA_Taxamo_Manager();
		$taxamo_manager->setup();

		// Setup View Order invoice link
		$view_order_invoice_link = new WC_TA_View_Order();
		$view_order_invoice_link->setup();

		// Admin only classes
		if ( is_admin() ) {

			// The admin E-Book Field
			$admin_ebook = new WC_TA_Admin_Ebook();
			$admin_ebook->setup();

			// The admin EU Exempt Field
			$admin_euexempt = new WC_TA_Admin_EUexempt();
			$admin_euexempt->setup();

			// Setup the reports
			$reports = new WC_TA_Reports();
			$reports->setup();

			// Filter plugin links
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_links' ) );
		}

		// Add Taxamo integration fields
		add_filter( 'woocommerce_integrations', array( $this, 'load_integration' ) );

		// Enqueue scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Subscriptions compatibility
		if ( class_exists( 'WC_Subscriptions' ) ) {
			$sc = new WC_TA_Subscriptions_Compatibility;
			$sc->setup_hooks();
		}

	}

	/**
	 * Plugin page links
	 */
	public function plugin_links( $links ) {

		$plugin_links = array(
			'<a href="' . self::TAXAMO_URL . '" target="_blank">' . __( 'Sign Up', 'woocommerce-taxamo' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Define integration
	 *
	 * @param  array $integrations
	 *
	 * @return array
	 */
	public function load_integration( $integrations ) {
		$integrations[] = 'WC_TA_Integration';

		return $integrations;
	}

	/**
	 * Enqueue the VAT field scripts
	 */
	public function enqueue_scripts() {
		if ( is_checkout() ) {

			wp_enqueue_script(
				'wc_ta_checkout_js',
				plugins_url( '/assets/js/checkout' . ( ( ! SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', WooCommerce_Taxamo::get_plugin_file() ),
				array( 'jquery' )
			);

			$wc_countries = new WC_Countries();

			wp_localize_script( 'wc_ta_checkout_js', 'wc_taxamo', array(
				'eu_countries' => json_encode( $wc_countries->get_european_union_countries( 'eu_vat' ) )
			) );

			wp_enqueue_style(
				'wc_af_post_shop_order_css',
				plugins_url( '/assets/css/woocommerce-taxamo.css', WooCommerce_Taxamo::get_plugin_file() ),
				array(),
				'1.0'
			);

		}
	}
}

// The 'main' function
function __woocommerce_taxamo_main() {
	new WooCommerce_Taxamo();
}

// Create object - Plugin init
add_action( 'plugins_loaded', '__woocommerce_taxamo_main' );
