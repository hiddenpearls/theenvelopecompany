<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}

class TM_EPO_Admin_Welcome {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}


	public function admin_menus() {

		if ( empty( $_GET['page'] ) ) {
			return;
		}

		$welcome_page_name  = __( 'About WooCommerce TM Extra Product Options', 'woocommerce-tm-extra-product-options' );
		$welcome_page_title = __( 'Welcome to WooCommerce TM Extra Product Options', 'woocommerce-tm-extra-product-options' );

		if($_GET['page']=='tc-about'){
			$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'tc-about', array( $this, 'welcome_screen' ) );
		}
	}

	public function admin_head() {
		remove_submenu_page( 'index.php', 'tc-about' );

		if (!empty($_GET['page']) && $_GET['page']=='tc-about'){
		?>
		<link href='<?php echo TM_EPO_PLUGIN_URL .'/external/font-awesome/css/font-awesome.min.css'; ?>' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300italic,300,400italic,500,500italic,700,700italic,900,900italic&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic' rel='stylesheet' type='text/css'>
		<style type="text/css">
			/*<![CDATA[*/
			.notice, div.updated, div.error {
				display: none;
			}
			#wpcontent{
    			color: #2c3e50;
				font-family: 'Roboto',"Open Sans",sans-serif;
			}
			.wp-core-ui .button-primary:hover,.wp-core-ui .button-primary:focus {
			    background: none repeat scroll 0 0 #1abc9c;
			    border-color: #1abc9c;
			    -webkit-box-shadow: none;
			    box-shadow: none;
			    color: #fff;
			    text-decoration: none;
			}
			.wp-core-ui .button-primary {
			    background: none repeat scroll 0 0 #95a5a6;
			    border-color: #7f8c8d;
			    -webkit-box-shadow: none;
			    box-shadow: none;
			    color: #fff;
			    text-decoration: none;
			}
			.about-wrap h1 {
    			color: #34495e;
			}
			a {
    			color: #e67e22;
			    text-decoration: none;
			}
			a:hover,a:focus{color:#e67e22;text-decoration: underline;}

			p.tm-actions .twitter-share-button {
			    margin-left: 3px;
			    margin-top: -3px;
			    vertical-align: middle;
			}

			.about-wrap h3{
				color: #00aa00;
			}

			.tm-logo-text {
			    font-size: 3em;
			    font-weight: 100;
			    height: 110px;
			    left: 0;
			    line-height: 110px;
			    margin: 0;
			    position: absolute;
			    text-align: center;
			    top: 0;
			    vertical-align: middle;
			    width: 150px;
			}
			.tm-logo-text-e{
				color: #e67e22;
			}
			.tm-version {
			    background: #00aa00 none repeat scroll 0 0;
			    color: #fff;
			    display: block;
			    height: 40px;
			    line-height: 40px;
			    margin-top: 110px;
			}
			.tm-logo {
				background: #fff none repeat scroll 0 0;
				color: #00aa00;
				font-size: 14px;
				font-weight: 400;
				height: 110px;
				margin: 5px 0 0;
				position: absolute;
				text-align: center;
				text-rendering: optimizelegibility;
				width: 150px;
				-webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
				box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
			}
			.about-wrap .tm-logo {
				position: absolute;
				top: 0;
				<?php echo is_rtl() ? 'left' : 'right'; ?>: 0;
			}
			.about-wrap .feature-section h4{
				color: #00aa00;
			}
			.about-wrap .feature-section {
			    border: 0 none;
			    padding:0;
			}
			
			.about-new {
				background: #ecf0f1 none repeat scroll 0 0;
				margin: 20px 0;
				padding: 1px 20px 10px;
			}
			.about-warning {
				border: 1px dotted #1abc9c;
				background: #fafafa none repeat scroll 0 0;
				margin: 20px 0;
				padding: 1px 20px 10px;
			}
			.about-wrap .about-warning .feature-section h4{
				color: #1abc9c;
			}
			.feature-wrap h4 {
				line-height: 1.4;
			}


			img.tc-featured-img {
			    float: left;
			    margin: 0 5%;
			}
			.tc-feature-wrap {
			    overflow: hidden;
			    padding: 0 0 40px;
			}

			.feature-wrap {
			    background: #fff none repeat scroll 0 0;
			    margin: 20px 0 0;
			    padding: 20px;
			}
			.feature-section div {
			    -moz-box-sizing: border-box;
			    -webkit-box-sizing: border-box;
			    -ms-box-sizing: border-box;
			    -o-box-sizing: border-box;
			    box-sizing: border-box;   
			    float: left;
			    padding: 0 15px;
			    width: 33.3333%;
			}

			.about-wrap .about-description, .about-wrap .about-text,.about-wrap h3,.about-wrap p,.about-wrap .feature-section h4 {
				font-weight: 300;
			}

			/*]]>*/
		</style>
		<?php }
	}

	private function welcome_header() {

		$major_version = explode(".", TM_EPO_VERSION, 2);
		$major_version = $major_version[0];
		?>
		<h1><?php printf( __( 'Welcome to Extra Product Options %s', 'woocommerce-tm-extra-product-options' ), $major_version ); ?></h1>

		<div class="about-text woocommerce-about-text">
			<?php
				$message = __( 'Thank you for the purchase!', 'woocommerce-tm-extra-product-options' );

				printf( __( '%s If you have any questions or problems with the plugin please visit the support forum <a href="http://support.themecomplete.com">here</a>.', 'woocommerce-tm-extra-product-options' ), $message );
			?>
		</div>

		<div class="tm-logo"><span class="tm-logo-text"><span class="tm-logo-text-e">E</span><span class="tm-logo-text-po">PO</span></span><span class="tm-version"><?php printf( __( 'Version %s', 'woocommerce' ), TM_EPO_VERSION ); ?></span></div>

		<p class="tm-actions">
			<a href="<?php echo admin_url('admin.php?page=wc-settings&tab=tm_extra_product_options'); ?>" class="button button-primary"><?php _e( 'Settings', 'woocommerce-tm-extra-product-options' ); ?></a>
			<a href="<?php echo esc_url('http://epo.themecomplete.com/documentation/woocommerce-tm-extra-product-options/index.html'); ?>" class="docs button button-primary"><?php _e( 'Documentation', 'woocommerce-tm-extra-product-options' ); ?></a>
			<a href="<?php echo esc_url('http://support.themecomplete.com/'); ?>" class="docs button button-primary"><?php _e( 'Support', 'woocommerce-tm-extra-product-options' ); ?></a>
			<a href="https://twitter.com/share" class="twitter-share-button" 
			data-url="http://codecanyon.net/item/woocommerce-extra-product-options/7908619?utm_source=sharetw" 
			data-text="Check out 'WooCommerce Extra Product Options' on #EnvatoMarket by @themecomplete #codecanyon" 
			data-via="themeComplete" 
			data-size="large" 
			data-hashtags="themecomplete">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</p>


		<?php
	}

	public function welcome_screen() {
		?>

		<div class="wrap about-wrap">

			<?php $this->welcome_header(); ?>			

<h2 class="nav-tab-wrapper"></h2>






<div class="changelog">
	<div class="tc-feature-wrap">
		<div>
			<img class="tc-featured-img" src="<?php echo TM_EPO_PLUGIN_URL. '/assets/images/about-1.png' ; ?>"/>

			<h3><?php _e( 'Extend-able elements', 'woocommerce-tm-extra-product-options' ); ?></h3>

			<p><?php _e( 'Extra Product Options can now be extended by creating addon plugins. You can create you own elements or customize the default ones.', 'woocommerce-tm-extra-product-options' ); ?></p>
			<p><?php _e( 'If you are interested in developing an addon to Extra Product Options contact us for more information.', 'woocommerce-tm-extra-product-options' ); ?></p>
		</div>
	</div>

	
<div class="feature-wrap">

				

				<div class="tm-feature-wrap">
					<div class="wc-feature feature-section col three-col">
						<div>
							<h4><?php _e( 'Validation features', 'woocommerce-tm-extra-product-options' ); ?></h4>
							<p><?php _e( 'You can now have validation features applied to the options created with the builder when they support it. This way you create number or email fields and the customer will not be permitted to add the product to the cart if the fields do not conform to the selected validation.', 'woocommerce-tm-extra-product-options' ); ?></p>
						</div>
						<div>
							<h4><?php _e( 'Related image sizes for image replacements', 'woocommerce-tm-extra-product-options' ); ?></h4>
							<p><?php _e( 'When choosing an image replacement or a picture to replace the product image you can now select between the various image sizes that WordPress applies to that image.', 'woocommerce-tm-extra-product-options' ); ?></p>
						</div>
						<div class="last-feature">
							<h4><?php _e( 'Include additional Global forms', 'woocommerce-tm-extra-product-options' ); ?></h4>
							<p><?php _e( 'You can now include global forms to individual products even if they are not assigned to those forms. You can do that from the new settings tab.', 'woocommerce-tm-extra-product-options' ); ?></p>
						</div>
					</div>
				</div>
			</div>
	


</div>



















			
				<p><?php _e( 'For any problems feel free to contact us at the', 'woocommerce-tm-extra-product-options' ); ?> <a href="<?php echo esc_url('http://support.themecomplete.com/'); ?>"><?php _e( 'Support forum', 'woocommerce-tm-extra-product-options' ); ?></a>.</p>

			<hr />


		</div>
		<?php
	}

	public function welcome() {

		if ( ! get_transient( '_tm_activation_redirect' ) ) {
			return;
		}

		delete_transient( '_tm_activation_redirect' );

		if ( is_network_admin() || defined( 'IFRAME_REQUEST' ) ) {
			return;
		}

		wp_redirect( admin_url( 'index.php?page=tc-about' ) );
		exit;
	}

}

$_tm_welcome = new TM_EPO_Admin_Welcome();
?>