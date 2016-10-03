<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_TA_Integration Class
 */
class WC_TA_Integration extends WC_Integration {

	public static $private_token = null;
	public static $public_token = null;
	public static $enable_self_declaration = null;
	public static $enable_vat_number = null;
	public static $show_taxamo_invoice = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id                 = 'taxamo';
		$this->method_title       = __( 'Taxamo', 'woocommerce-taxamo' );
		$this->method_description = __( sprintf( 'Taxamo handles VAT calculation and reporting on digital goods &amp; services. %sYou need a Taxamo account for this extension to work, click here to sign up.%s', '<br/><a href="' . WooCommerce_Taxamo::TAXAMO_URL . '" target="_blank">', '</a>' ), 'woocommerce-taxamo' );

		// Load admin form
		$this->init_form_fields();

		// Load settings
		$this->init_settings();

		self::$private_token = $this->get_option( 'private_token' );
		self::$public_token  = $this->get_option( 'public_token' );

		self::$enable_self_declaration = $this->get_option( 'enable_self_declaration', 'yes' );
		self::$enable_vat_number       = $this->get_option( 'enable_vat_number', 'yes' );
		self::$show_taxamo_invoice	   = $this->get_option( 'show_taxamo_invoice', 'no' );

		// Hooks
		add_action( 'woocommerce_update_options_integration_taxamo', array( $this, 'process_admin_options' ) );

		if ( empty( self::$private_token ) || empty( self::$public_token ) ) {
			add_action( 'admin_notices', array( $this, 'settings_notice' ) );
		}
	}

	/**
	 * Init integration form fields
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'private_token'           => array(
				'title'       => __( 'Private Token', 'woocommerce-taxamo' ),
				'description' => __( 'Get this token from your Taxamo account.', 'woocommerce-taxamo' ),
				'type'        => 'text'
			),
			'public_token'            => array(
				'title'       => __( 'Public Token', 'woocommerce-taxamo' ),
				'description' => __( 'Get this token from your Taxamo account.', 'woocommerce-taxamo' ),
				'type'        => 'text'
			),
			'enable_self_declaration' => array(
				'title'       => __( 'Enable Self Declaration', 'woocommerce-taxamo' ),
				'description' => __( 'Allow customers to self declare their location when location validation failed.', 'woocommerce-taxamo' ),
				'type'        => 'checkbox',
				'default'     => 'yes'
			),
			'enable_vat_number'       => array(
				'title'       => __( 'Enable VAT Number Field', 'woocommerce-taxamo' ),
				'description' => __( 'Allow customer with companies to enter a VAT Number to be exempt of VAT.', 'woocommerce-taxamo' ),
				'type'        => 'checkbox',
				'default'     => 'yes'
			),
			'show_taxamo_invoice'       => array(
				'title'       => __( 'Show Invoices', 'woocommerce-taxamo' ),
				'description' => __( sprintf( 'Allow customers to see a Taxamo VAT invoice for their orders.%s(You must have invoicing enabled in your %sTaxamo settings%s)%s', '<br/><small>', '<a href="https://dashboard.taxamo.com/merchant/app.html#/account/invoicing" target="blank">', '</a>', '</small>' ), 'woocommerce-taxamo' ),
				'type'        => 'checkbox',
				'default'     => 'no'
			)
		);
	}

	/**
	 * Settings prompt
	 */
	public function settings_notice() {
		if ( ! empty( $_GET['tab'] ) && 'integration' === $_GET['tab'] ) {
			return;
		}
		?>
		<div id="message" class="updated woocommerce-message">
			<p><?php _e( '<strong>Taxamo</strong> is almost ready &#8211; Please configure your API keys to begin fetching tax rates.', 'woocommerce-taxamo' ); ?></p>

			<p class="submit"><a
					href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=integration&section=taxamo' ); ?>"
					class="button-primary"><?php _e( 'Settings', 'woocommerce-taxamo' ); ?></a></p>
		</div>
	<?php
	}
}