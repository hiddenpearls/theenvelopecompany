<?php
/*
 * Settings class.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (class_exists('WC_Settings_Page')){

	TM_EPO_ADMIN_GLOBAL()->tm_load_scripts();

	class TM_EPO_ADMIN_SETTINGS extends WC_Settings_Page {
		
		var $other_settings=0;
		var $settings_options=array();
		var $settings_array=array();

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->id    			= TM_EPO_ADMIN_SETTINGS_ID;
			$this->label 			= __('Extra Product Options', 'woocommerce-tm-extra-product-options');
			$this->tab_count 		= 0;
			$this->settings_options = array(
				"general" 	=> __( 'General', 'woocommerce-tm-extra-product-options' ),
				"display" 	=> __( 'Display', 'woocommerce-tm-extra-product-options' ),
				"cart" 		=> __( 'Cart', 'woocommerce-tm-extra-product-options' ),
				"string" 	=> __( 'Strings', 'woocommerce-tm-extra-product-options' ),
				"style" 	=> __( 'Style', 'woocommerce-tm-extra-product-options' ),
				"global" 	=> __( 'Global', 'woocommerce-tm-extra-product-options' ),
				"other" 	=> "other",
				"license" 	=> __( 'License', 'woocommerce-tm-extra-product-options' ),
				"upload" 	=> __('Upload manager', 'woocommerce-tm-extra-product-options')
				);

			foreach ($this->settings_options as $key => $value) {
				$this->settings_array[$key] = $this->get_setting_array($key,$value);
			}

			add_filter( 'woocommerce_settings_tabs_array', 							array( $this, 'add_settings_page' ), 20 );
			add_action( 'woocommerce_settings_' . $this->id, 						array( $this, 'output' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, 					array( $this, 'save' ) );

			add_action( 'woocommerce_admin_field_tm_tabs_header', 					array( $this, 'tm_tabs_header_setting' ) );
			add_action( 'woocommerce_admin_field_tm_title', 						array( $this, 'tm_title_setting' ) );
			add_action( 'woocommerce_admin_field_tm_html', 							array( $this, 'tm_html_setting' ) );
			add_action( 'woocommerce_admin_field_tm_sectionend', 					array( $this, 'tm_sectionend_setting' ) );

			add_action( 'tm_woocommerce_settings_' . 'epo_page_options' , 			array( $this, 'tm_settings_hook' ) );
			add_action( 'tm_woocommerce_settings_' . 'epo_page_options' . '_end', 	array( $this, 'tm_settings_hook_end' ) );
			
			add_action( 'woocommerce_settings_' . $this->id, 						array( $this, 'tm_settings_hook_all_end' ) );
		}

		public function tm_echo_header($counter=0,$label="") {
			echo '<div class="tm-box">'
				. '<h4 class="tab-header '.($counter == 1?'open':'closed').'" data-id="tmsettings'.$counter.'-tab">'
				. $label
				. '<span class="tcfa tm-arrow2 tcfa-angle-down2"></span></h4>'
				. '</div>';
		}

		public function tm_title_setting($value) {
			if ( ! empty( $value['id'] ) ) {
				do_action( 'tm_woocommerce_settings_' . sanitize_title( $value['id'] ) );
			}
			if ( ! empty( $value['title'] ) ) {
				echo '<h3 class="tm-section-title">' . esc_html( $value['title'] ) . '</h3>';
			}
			if ( ! empty( $value['desc'] ) ) {
				echo wpautop( wptexturize( wp_kses_post( $value['desc'] ) ) );
			}
			echo '<table class="form-table">'. "\n\n";
		}

		public function tm_html_setting($value) {
			if ( ! isset( $value['id'] ) ) {
				$value['id'] = '';
			}
			if ( ! isset( $value['title'] ) ) {
				$value['title'] = isset( $value['name'] ) ? $value['name'] : '';
			}

			if ( ! empty( $value['id'] ) ) {
				do_action( 'tm_woocommerce_settings_' . sanitize_title( $value['id'] ) );
			}?>
			<tr valign="top">
						<td colspan="2" class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
							<?php 
								if ( ! empty( $value['html'] ) ) {
									echo  $value['html'] ;
								} 
							?>
						</td>
					</tr>
			<?php
		}

		public function tm_sectionend_setting($value) {
			echo '</table>';
			if ( ! empty( $value['id'] ) ) {
				do_action( 'tm_woocommerce_settings_' . sanitize_title( $value['id'] ) . '_end' );
			}
		}

		public function tm_tabs_header_setting() {
			
			echo '<div class="tm-settings-wrap tm_wrapper">';
			
				echo '<div class="header"><h3>'.__( 'Extra Product Options Settings', 'woocommerce-tm-extra-product-options' ).'</h3></div>';
					
				echo '<div class="transition tm-tabs">';
						
					echo '<div class="transition tm-tab-headers tmsettings-tab">';

					$counter = 1;
					foreach ($this->settings_options as $key => $label) {
						if ($key=="other"){
							$_other_settings = $this->get_other_settings_headers();
							foreach ($_other_settings as $h_key => $h_label) {
								$this->tm_echo_header($counter,$h_label);
								$counter++;
							}
						}else{
							$this->tm_echo_header($counter,$label);
							$counter++;							
						}
					}
				
					echo '</div>';
			
		}

		public function tm_settings_hook() {
			$this->tab_count++;
			echo '<div class="transition tm-tab tmsettings'.$this->tab_count.'-tab">';
		}

		public function tm_settings_hook_end() {
			echo '</div>';
		}

		public function tm_settings_hook_all_end() {
			echo '</div></div>'; // close .transition.tm-tabs , .tm-settings-wrap
		}

		public function get_other_settings_headers(){
			$headers=array();
			return apply_filters('tm_epo_settings_headers',$headers);
		}

		public function get_other_settings(){
			$settings=array();
			return apply_filters('tm_epo_settings_settings',$settings);
		}

		public function get_setting_array($setting,$label){
			$method="_get_setting_".$setting;
			return $this->$method($setting,$label);
		}

		private function _get_setting_general($setting,$label){
			return array(
					array( 
						'type' 	=> 'tm_title',				
						'id' 	=> 'epo_page_options',
						'title' => $label
						),
					array(
							'title' 	=> __( 'Enable front-end for roles', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Select the roles that will have access to the extra options.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_roles_enabled',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> '@everyone',
							'type' 		=> 'multiselect',
							'options' 	=> tc_get_roles(),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Disable front-end for roles', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Select the roles that will not have access to the extra options.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_roles_disabled',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> '',
							'type' 		=> 'multiselect',
							'options' 	=> tc_get_roles(),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Final total box', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Select when to show the final total box', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_final_total_box',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'normal',
							'type' 		=> 'select',
							'options' 	=> array(
								'normal' 				=> __( 'Show Both Final and Options total box', 'woocommerce-tm-extra-product-options' ),
								'final' 				=> __( 'Show only Final total', 'woocommerce-tm-extra-product-options' ),
								'hideoptionsifzero' 	=> __( 'Show Final total and hide Options total if zero', 'woocommerce-tm-extra-product-options' ),
								'hideifoptionsiszero' 	=> __( 'Hide Final total box if Options total is zero', 'woocommerce-tm-extra-product-options' ),
								'hide' 					=> __( 'Hide Final total box', 'woocommerce-tm-extra-product-options' ),
								'pxq' 					=> __( 'Always show only Final total (Price x Quantity)', 'woocommerce-tm-extra-product-options' ),
								'disable' 				=> __( 'Disable', 'woocommerce-tm-extra-product-options' ),
							),
							'desc_tip'	=>  false,
						),		
					array(
							'title' 	=> __( 'Enable Final total box for all products', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Check to enable Final total box even when product has no extra options', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_enable_final_total_box_all',
							'default' 	=> 'no',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Strip html from emails', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Check to strip the html tags from emails', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_strip_html_from_emails',
							'default' 	=> 'yes',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Disable lazy load images', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Check to disable lazy loading images.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_no_lazy_load',
							'default' 	=> 'yes',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),				
					array(
							'title' 	=> __( 'Enable plugin for WooCommerce shortcodes', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enabling this will load the plugin files to all WordPress pages. Use with caution.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_enable_shortcodes',
							'default' 	=> 'no',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),				
					array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),
			);
		}

		private function _get_setting_display($setting,$label){
			return array(
					array(
						'type' 	=> 'tm_title', 
						'id' 	=> 'epo_page_options',
						'title' => $label
					),
					array(
							'title' 	=> __( 'Display', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'This controls how your fields are displayed on the front-end.<br />If you choose "Show using action hooks" you have to manually write the code to your theme or plugin to display the fields and the placement settings below will not work. <br />If you use Composite Products extension you must leave this setting to "Normal" otherwise the extra options cannot be displayed on the composite product bundles.<br />See more at the documentation.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_display',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'normal',
							'type' 		=> 'select',
							'options' 	=> array(
								'normal' => __( 'Normal', 'woocommerce-tm-extra-product-options' ),
								'action' => __( 'Show using action hooks', 'woocommerce-tm-extra-product-options' ),
							),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Extra Options placement', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Select where you want the extra options to appear.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_options_placement',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'woocommerce_before_add_to_cart_button',
							'type' 		=> 'select',
							'options' 	=> array(
								'woocommerce_before_add_to_cart_button' 		=> __( 'Before add to cart button', 'woocommerce-tm-extra-product-options' ),
								'woocommerce_after_add_to_cart_button' 			=> __( 'After add to cart button', 'woocommerce-tm-extra-product-options' ),
								
								'woocommerce_before_add_to_cart_form' 			=> __( 'Before cart form', 'woocommerce-tm-extra-product-options' ),
								'woocommerce_after_add_to_cart_form' 			=> __( 'After cart form', 'woocommerce-tm-extra-product-options' ),
								
								'woocommerce_before_single_product' 			=> __( 'Before product', 'woocommerce-tm-extra-product-options' ),
								'woocommerce_after_single_product' 				=> __( 'After product', 'woocommerce-tm-extra-product-options' ),
								
								'woocommerce_before_single_product_summary' 	=> __( 'Before product summary', 'woocommerce-tm-extra-product-options' ),
								'woocommerce_after_single_product_summary' 		=> __( 'After product summary', 'woocommerce-tm-extra-product-options' ),
								
								'woocommerce_product_thumbnails' 				=> __( 'After product image', 'woocommerce-tm-extra-product-options' ),

								'custom' 										=> __( 'Custom hook', 'woocommerce-tm-extra-product-options' ),
							),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Extra Options placement custom hook', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '',
							'id' 		=> 'tm_epo_options_placement_custom_hook',
							'default'	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Totals box placement', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Select where you want the Totals box to appear.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_totals_box_placement',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'woocommerce_before_add_to_cart_button',
							'type' 		=> 'select',
							'options' 	=> array(
								'woocommerce_before_add_to_cart_button' 		=> __( 'Before add to cart button', 'woocommerce-tm-extra-product-options' ),
								'woocommerce_after_add_to_cart_button' 			=> __( 'After add to cart button', 'woocommerce-tm-extra-product-options' ),
								
								'woocommerce_before_add_to_cart_form' 			=> __( 'Before cart form', 'woocommerce-tm-extra-product-options' ),
								'woocommerce_after_add_to_cart_form' 			=> __( 'After cart form', 'woocommerce-tm-extra-product-options' ),
								
								'woocommerce_before_single_product' 			=> __( 'Before product', 'woocommerce-tm-extra-product-options' ),
								'woocommerce_after_single_product' 				=> __( 'After product', 'woocommerce-tm-extra-product-options' ),
								
								'woocommerce_before_single_product_summary' 	=> __( 'Before product summary', 'woocommerce-tm-extra-product-options' ),
								'woocommerce_after_single_product_summary' 		=> __( 'After product summary', 'woocommerce-tm-extra-product-options' ),
								
								'woocommerce_product_thumbnails' 				=> __( 'After product image', 'woocommerce-tm-extra-product-options' ),

								'custom' 										=> __( 'Custom hook', 'woocommerce-tm-extra-product-options' ),
							),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Totals box placement custom hook', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '',
							'id' 		=> 'tm_epo_totals_box_placement_custom_hook',
							'default'	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Floating Totals box', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'This will enable a floating box to display your totals box.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_floating_totals_box',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'disable',
							'type' 		=> 'select',
							'options' 	=> array(
								'disable' 		=> __( 'Disable', 'woocommerce-tm-extra-product-options' ),
								'bottom right' 	=> __( 'Bottom right', 'woocommerce-tm-extra-product-options' ),
								'bottom left' 	=> __( 'Bottom left', 'woocommerce-tm-extra-product-options' ),
								'top right' 	=> __( 'Top right', 'woocommerce-tm-extra-product-options' ),
								'top left' 		=> __( 'Top left', 'woocommerce-tm-extra-product-options' ),
							),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Force Select Options', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'This changes the add to cart button to display select options when the product has extra product options.<br />Enabling this will remove the ajax functionality.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_force_select_options',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'normal',
							'type' 		=> 'select',
							'options' 	=> array(
								'normal' 	=> __( 'Disable', 'woocommerce-tm-extra-product-options' ),
								'display' 	=> __( 'Enable', 'woocommerce-tm-extra-product-options' ),
							),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Enable extra options in shop and category view', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Check to enable the display of extra options on the shop page and category view. This setting is theme dependent and some aspect may not work as expected.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_enable_in_shop',
							'default' 	=> 'no',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Remove Free price label', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Check to remove Free price label when product has extra options', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_remove_free_price_label',
							'default' 	=> 'no',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array(
							'title'		=> __( 'Hide uploaded file path', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Check to hide the uploaded file path from users.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_hide_upload_file_path',
							'default' 	=> 'yes',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Show quantity selector only for elements with a value', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Check show quantity selector only for elements with a value.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_show_only_active_quantities',
							'default' 	=> 'yes',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Hide add-to-cart button until an option is chosen', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Check this to show the add to cart button only when at least one option is filled.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_hide_add_cart_button',
							'default' 	=> 'no',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Auto hide price if zero', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Check this to globally hide the price display if it is zero.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_auto_hide_price_if_zero',
							'default' 	=> 'no',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					(TM_EPO_WPML()->is_active())
					?
					array(
							'title' 	=> __( 'Use translated values when possible on admin Order', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Please note that if the options on the Order change or get deleted you will get wrong results by enabling this!', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_wpml_order_translate',
							'default' 	=> 'no',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						)
					:array(),
					array(
							'title' 	=> __( 'Include option pricing in product price', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Check this to include the pricing of the options to the product price.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_include_possible_option_pricing',
							'default' 	=> 'no',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Use the "From" string on displayed product prices', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Check this to alter the price display of a product when it has extra options with prices.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_use_from_on_price',
							'default' 	=> 'no',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),
			);
		}

		private function _get_setting_cart($setting,$label){
			return array(
					array(  
						'type' 	=> 'tm_title', 				
						'id' 	=> 'epo_page_options',
						'title' => $label 
						),
					array(
							'title' 	=> __( 'Clear cart button', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enables or disables the clear cart button', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_clear_cart_button',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'normal',
							'type' 		=> 'select',
							'options' 	=> array(
								'normal' 	=> __( 'Hide', 'woocommerce-tm-extra-product-options' ),
								'show' 		=> __( 'Show', 'woocommerce-tm-extra-product-options' )
								
							),
							'desc_tip'	=>  false,
						),	
					array(
							'title' 	=> __( 'Cart Field Display', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Select how to display your fields in the cart', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_cart_field_display',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'normal',
							'type' 		=> 'select',
							'options' 	=> array(
								'normal' 	=> __( 'Normal display', 'woocommerce-tm-extra-product-options' ),
								'link' 		=> __( 'Display a pop-up link', 'woocommerce-tm-extra-product-options' ),
								'advanced' 	=> __( 'Advanced display', 'woocommerce-tm-extra-product-options' )
							),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Hide extra options in cart', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enables or disables the display of options in the cart.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_hide_options_in_cart',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'normal',
							'type' 		=> 'select',
							'options' 	=> array(
								'normal' 	=> __( 'Show', 'woocommerce-tm-extra-product-options' ),
								'hide' 		=> __( 'Hide', 'woocommerce-tm-extra-product-options' )
								
							),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Hide extra options prices in cart', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enables or disables the display of prices of options in the cart.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_hide_options_prices_in_cart',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'normal',
							'type' 		=> 'select',
							'options' 	=> array(
								'normal' 	=> __( 'Show', 'woocommerce-tm-extra-product-options' ),
								'hide' 		=> __( 'Hide', 'woocommerce-tm-extra-product-options' )
								
							),
							'desc_tip'	=>  false,
						),
					version_compare( get_option( 'woocommerce_db_version' ), '2.3', '<' )?
					array():
					array(
							'title' 	=> __( 'Prevent negative priced products', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Prevent adding to the cart negative priced products.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_no_negative_priced_products',
							'default' 	=> 'no',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Show image replacement in cart and checkout', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enabling this will show the images of elements that have an image replacement.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_show_image_replacement',
							'default' 	=> 'no',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),

			);			
		}

		private function _get_setting_string($setting,$label){
			return array(
					array(  
						'type' 	=> 'tm_title', 				
						'id' 	=> 'epo_page_options',
						'title' => $label 
						),
					array(
							'title' 	=> __( 'Cart field/value separator', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter the field/value separator for the cart.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_separator_cart_text',
							'default'	=> ':',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Final total text', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter the Final total text or leave blank for default.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_final_total_text',
							'default'	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),

					array(
							'title' 	=> __( 'Options total text', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter the Options total text or leave blank for default.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_options_total_text',
							'default'	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),

					(tc_woocommerce_subscriptions_check())?
					array(
							'title' 	=> __( 'Subscription fee text', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter the Subscription fee text or leave blank for default.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_subscription_fee_text',
							'default'	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						):
					array(),

					array(
							'title'	 	=> __( 'Free Price text replacement', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter a text to replace the Free price label when product has extra options.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_replacement_free_price_text',
							'default' 	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Reset Options text replacement', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter a text to replace the Reset options text when using custom variations.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_reset_variation_text',
							'default' 	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Edit Options text replacement', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter a text to replace the Edit options text on the cart.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_edit_options_text',
							'default' 	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title'	 	=> __( 'Additional Options text replacement', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter a text to replace the Additional options text when using the pop up setting on the cart.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_additional_options_text',
							'default' 	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Close button text replacement', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter a text to replace the Close button text when using the pop up setting on the cart.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_close_button_text',
							'default' 	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Calendar close button text replacement', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter a text to replace the Close button text on the calendar.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_closetext',
							'default' 	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Calendar today button text replacement', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter a text to replace the Today button text on the calendar.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_currenttext',
							'default' 	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Slider previous text', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter a text to replace the previous button text for slider.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_slider_prev_text',
							'default' 	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Slider next text', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter a text to replace the next button text for slider.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_slider_next_text',
							'default' 	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Force Select options text', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter a text to replace the add to cart button text when using the Force select option.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_force_select_text',
							'default' 	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Empty cart text', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter a text to replace the empty cart button text.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_empty_cart_text',
							'default' 	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'This field is required text', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter a text indicate that a field is required.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_this_field_is_required_text',
							'default' 	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Characters remaining text', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter a text to replace the Characters remaining when using maximum characters on a text field or a textarea.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_characters_remaining_text',
							'default' 	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),

					array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),
			);			
		}

		private function _get_setting_style($setting,$label){
			return array(
					array(  
						'type' 	=> 'tm_title', 				
						'id' 	=> 'epo_page_options',
						'title' => $label 
						),
					
					array(
							'title' 	=> __( 'Enable checkbox and radio styles', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enables or disables extra styling for checkboxes and radio buttons.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_css_styles',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> '',
							'type' 		=> 'select',
							'options' 	=> array(
								'' 			=> __( 'Disable', 'woocommerce-tm-extra-product-options' ),
								'on' 		=> __( 'Enable', 'woocommerce-tm-extra-product-options' )
								
							),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Style', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Select a style.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_css_styles_style',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'round',
							'type' 		=> 'select',
							'options' 	=> array(
								'round' 	=> __( 'Round', 'woocommerce-tm-extra-product-options' ),
								'square' 	=> __( 'Square', 'woocommerce-tm-extra-product-options' )
								
							),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Select item border type', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Select a style for the selected border when using image replacements or swatches.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_css_selected_border',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> '',
							'type' 		=> 'select',
							'options' 	=> array(
								'' 			=> __( 'Default', 'woocommerce-tm-extra-product-options' ),
								'square' 	=> __( 'Square', 'woocommerce-tm-extra-product-options' ),
								'round' 	=> __( 'Round', 'woocommerce-tm-extra-product-options' ),
								'shadow' 	=> __( 'Shadow', 'woocommerce-tm-extra-product-options' ),
								'thinline' 	=> __( 'Thin line', 'woocommerce-tm-extra-product-options' ),								
							),
							'desc_tip'	=>  false,
						),
			
					array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),

			);			
		}

		private function _get_setting_global($setting,$label){
			return array(
					array(  
						'type' 	=> 'tm_title', 				
						'id' 	=> 'epo_page_options',
						'title' => $label 
						),
					array(
							'title' 	=> __( 'Enable validation', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Check to enable validation feature for builder elements', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_global_enable_validation',
							'default' 	=> 'yes',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Prevent options from being sent to emails', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Check to disable options from being sent to emails.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_global_prevent_options_from_emails',
							'default' 	=> 'no',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Override product price', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'This will globally override the product price with the price from the options if the total options price is greater then zero.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_global_override_product_price',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> '',
							'type' 		=> 'select',
							'options' 	=> array(
								'' 		=> __( 'Use setting on each product', 'woocommerce-tm-extra-product-options' ),
								'no' 	=> __( 'No', 'woocommerce-tm-extra-product-options' ),
								'yes' 	=> __( 'Yes', 'woocommerce-tm-extra-product-options' ),
							),					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Reset option values after the product is added to the cart', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '',
							'id' 		=> 'tm_epo_global_reset_options_after_add',
							'default' 	=> 'no',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Input decimal separator', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( ' Choose how to determine the decimal separator for user inputs', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_global_input_decimal_separator',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> '',
							'type' 		=> 'select',
							'options' 	=> array(
								'' 			=> __( 'Use WooCommerce value', 'woocommerce-tm-extra-product-options' ),
								'browser' 	=> __( 'Determine by browser local', 'woocommerce-tm-extra-product-options' ),
								
							),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Displayed decimal separator', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( ' Choose which decimal separator to display on currency prices', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_global_displayed_decimal_separator',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> '',
							'type' 		=> 'select',
							'options' 	=> array(
								'' 			=> __( 'Use WooCommerce value', 'woocommerce-tm-extra-product-options' ),
								'browser' 	=> __( 'Determine by browser local', 'woocommerce-tm-extra-product-options' ),
								
							),
							'desc_tip'	=>  false,
						),

					array(
							'title' 	=> __( 'Radio button undo button', 'woocommerce-tm-extra-product-options' ),
							//'desc' 		=> '<span>'.__( '', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_global_radio_undo_button',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> '',
							'type' 		=> 'select',
							'options' 	=> array(
								'' 			=> __( 'Use field value', 'woocommerce-tm-extra-product-options' ),
								'enable' 	=> __( 'Enable', 'woocommerce-tm-extra-product-options' ),
								'disable' 	=> __( 'Disable', 'woocommerce-tm-extra-product-options' )
								
							),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Required state indicator', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Enter a string to indicate the required state of a field.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_global_required_indicator',
							'default'	=> '*',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Required state indicator position', 'woocommerce-tm-extra-product-options' ),
							//'desc' 		=> '<span>'.__( '', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_global_required_indicator_position',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'left',
							'type' 		=> 'select',
							'options' 	=> array(
								'left' 		=> __( 'Left of the label', 'woocommerce-tm-extra-product-options' ),
								'right' 	=> __( 'Right of the label', 'woocommerce-tm-extra-product-options' )
								
							),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Include tax string suffix on totals box', 'woocommerce-tm-extra-product-options' ),
							'id' 		=> 'tm_epo_global_tax_string_suffix',
							'default' 	=> 'no',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Load generated styles inline', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'This will prevent some load flickering but it will produce invalid html.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_global_load_generated_styles_inline',
							'default' 	=> 'yes',
							'type' 		=> 'checkbox',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Datepicker theme', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Select the theme for the datepicker.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_global_datepicker_theme',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> '',
							'type' 		=> 'select',
							'options' 	=> array(
								''			=> __( 'Use field value', 'woocommerce-tm-extra-product-options' ),
								'epo' 		=> __( 'Epo White', 'woocommerce-tm-extra-product-options' ),
								'epo-black' => __( 'Epo Black', 'woocommerce-tm-extra-product-options' ),
							),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Datepicker size', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Select the size of the datepicker.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_global_datepicker_size',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> '',
							'type' 		=> 'select',
							'options' 	=> array(
								''			=> __( 'Use field value', 'woocommerce-tm-extra-product-options' ),
								'small' 	=> __( 'Small', 'woocommerce-tm-extra-product-options' ),
								'medium' 	=> __( 'Medium', 'woocommerce-tm-extra-product-options' ),
								'large' 	=> __( 'Large', 'woocommerce-tm-extra-product-options' ),
							),
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Datepicker position', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Select the position of the datepicker.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_global_datepicker_position',
							'class'		=> 'chosen_select',
							'css' 		=> 'min-width:300px;',
							'default'	=> '',
							'type' 		=> 'select',
							'options' 	=> array(
								''			=> __( 'Use field value', 'woocommerce-tm-extra-product-options' ),
								'normal' 	=> __( 'Normal', 'woocommerce-tm-extra-product-options' ),
								'top' 		=> __( 'Top of screen', 'woocommerce-tm-extra-product-options' ),
								'bottom' 	=> __( 'Bottom of screen', 'woocommerce-tm-extra-product-options' ),
							),
							'desc_tip'	=>  false,
						),							
					array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),

			);			
		}

		private function _get_setting_other($setting,$label){
			$settings = array();
			$other = $this->get_other_settings();
			foreach ($other as $key => $setting) {
				$settings = array_merge($settings,$setting);
			}
			return $settings;
		}

		private function _get_setting_license($setting,$label){
			$_license_settings=(!defined('TM_DISABLE_LICENSE'))?
				array(				
					array( 
						'type' 	=> 'tm_title', 
						'id' 	=> 'epo_page_options',
						'title' => $label 
						),
					array(
							'title' 	=> __( 'Username', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Your Envato username.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_envato_username',
							'default'	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
							
							//'custom_attributes'=>(TM_EPO_LICENSE()->get_license())?array('disabled'=>'disabled'):""
						),
					array(
							'title' 	=> __( 'Envato API Key', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'You can find your API key by visiting your Account page then clicking the My Settings tab. At the bottom of the page you’ll find your account’s API key and a button to regenerate it as needed.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_envato_apikey',
							'default'	=> '',
							'type' 		=> 'password',					
							'desc_tip'	=>  false,
						),
					array(
							'title' 	=> __( 'Purchase code', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span><p>'.__( 'Please enter your <strong>CodeCanyon WooCommerce Extra Product Options purchase code</strong>.', 'woocommerce-tm-extra-product-options' ).'</p><p>'.__( 'To access your Purchase Code for an item:', 'woocommerce-tm-extra-product-options' ).'</p>'
											.'<ol>'
											.'<li>'.__('Log into your Marketplace account', 'woocommerce-tm-extra-product-options' ).'</li>'
											.'<li>'.__('From your account dropdown links, select "Downloads"', 'woocommerce-tm-extra-product-options' ).'</li>'
											.'<li>'.__('Click the "Download" button that corresponds with your purchase', 'woocommerce-tm-extra-product-options' ).'</li>'
											.'<li>'.__('Select the "License certificate &amp; purchase code" download link. Your Purchase Code will be displayed within the License Certificate.', 'woocommerce-tm-extra-product-options' ).'</li>'
											.'</ol>'
											.'<p><img alt="Purchase Code Location" src="'.TM_EPO_PLUGIN_URL.'/assets/images/download_button.gif" title="Purchase Code Location" style="vertical-align: middle;"></p>'
											.'<span class="tm-license-button">'
											
											.'<a href="#" class="'.(TM_EPO_LICENSE()->get_license()?"":"tm-hidden ").'tm-button button button-primary button-large tm-deactivate-license" id="tm_deactivate_license">'.__('Deactivate License', 'woocommerce-tm-extra-product-options' ).'</a>'
											.'<a href="#" class="'.(TM_EPO_LICENSE()->get_license()?"tm-hidden ":"").'tm-button button button-primary button-large tm-activate-license" id="tm_activate_license">'.__('Activate License', 'woocommerce-tm-extra-product-options' ).'</a>'
											
											.'</span>'
											.'<span class="tm-license-result">'
											.((TM_EPO_LICENSE()->get_license())?
											"<span class='activated'><p>".__("License activated.",'woocommerce-tm-extra-product-options')."</p></span>"
											:""
											)
											.'</span>'
											.'</span>',
							'id' 		=> 'tm_epo_envato_purchasecode',
							'default' 	=> '',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
							//'custom_attributes'=>(TM_EPO_LICENSE()->get_license())?array('disabled'=>'disabled'):""
						),
					array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),		
				):array();
			
			return $_license_settings;
		}

		private function _get_setting_upload($setting,$label){
			$html=TM_EPO_HELPER()->file_manager(TM_EPO()->upload_dir,'');

			$_upload_settings=
				array(				
					array( 
						'type' 	=> 'tm_title', 
						'id' 	=> 'epo_page_options',
						'title' => $label 
						),
					array(
							'title' 	=> __( 'Upload folder', 'woocommerce-tm-extra-product-options' ),
							'desc' 		=> '<span>'.__( 'Changing this will only affect future uploads.', 'woocommerce-tm-extra-product-options' ).'</span>',
							'id' 		=> 'tm_epo_upload_folder',
							'default'	=> 'extra_product_options',
							'type' 		=> 'text',					
							'desc_tip'	=>  false,
							
							//'custom_attributes'=>(TM_EPO_LICENSE()->get_license())?array('disabled'=>'disabled'):""
						),
					array( 
						'type' 	=> 'tm_html', 
						'id' 	=> 'epo_page_options_html',
						'title' => __( 'File manager', 'woocommerce-tm-extra-product-options' ),
						'html' 	=> $html 
						),
					array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),		
				);
			
			return $_upload_settings;
		}

		/**
		 * Get settings array
		 *
		 * @return array
		 */
		public function get_settings() {

			$settings = array();
			$settings = array_merge($settings, array(array( 'type' => 'tm_tabs_header' )) );

			foreach ($this->settings_array as $key => $value) {
				$settings = array_merge($settings, $value );
			}

			return apply_filters( 'tm_' . $this->id . '_settings', 
				$settings
			); // End pages settings
		}
	}

}
?>