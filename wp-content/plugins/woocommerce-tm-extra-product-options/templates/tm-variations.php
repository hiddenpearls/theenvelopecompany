<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}

$tm_product=false;
// Ensure $product is valid and that variations are customized.
if ( !empty($tm_product_id)){
	$tm_product = wc_get_product($tm_product_id);

}

if ( 
	!empty($tm_product) && 
	is_object($tm_product) && 
	method_exists($tm_product, 'get_available_variations') 
	&& !empty($builder) 
	&& isset($builder['variations_options']) 
	){
	$variations_options=$builder['variations_options'];

	$available_variations = $tm_product->get_available_variations();
	$attributes = $tm_product->get_variation_attributes();
	$selected_attributes = $tm_product->get_variation_default_attributes();
	
	$variations_builder_element_start_args_class="";
	if (isset($variations_builder_element_start_args["class"])){
		$variations_builder_element_start_args_class = $variations_builder_element_start_args["class"];
	}
	if ( ! empty( $available_variations ) ) {
		
		$loop = 0;

		foreach ( $attributes as $name => $options ){
			
			$loop++;
			// name - wc_attribute_label( $name );
			// id - sanitize_title($name);
			// select box name 'attribute_'.sanitize_title( $name );
			// select box id sanitize_title( $name ); 

			$att_id = sanitize_title( $name );
			if ( is_array( $options ) ) {

				$variations_display_as = "select";
				if (isset($variations_options[$att_id]) && !empty($variations_options[$att_id]['variations_display_as'])){
					$variations_display_as = $variations_options[$att_id]['variations_display_as'];
				}
				
				$options_array = array();
				$default_value = "";
				$imagesp = array();
				$images = array();
				$color = array();
				$changes_product_image = "";
				if (isset($variations_options[$att_id]) && !empty($variations_options[$att_id]['variations_changes_product_image'])){
					$changes_product_image = $variations_options[$att_id]['variations_changes_product_image'];
				}
				$variations_label = "";
				if (isset($variations_options[$att_id]) && !empty($variations_options[$att_id]['variations_label'])){
					$variations_label = $variations_options[$att_id]['variations_label'];
				}
				$variations_class = "";
				if (isset($variations_options[$att_id]) && !empty($variations_options[$att_id]['variations_class'])){
					$variations_class = $variations_options[$att_id]['variations_class'];
				}
				$variations_items_per_row = "";
				if (isset($variations_options[$att_id]) && !empty($variations_options[$att_id]['variations_items_per_row'])){
					$variations_items_per_row = $variations_options[$att_id]['variations_items_per_row'];
				}
				$variations_item_width = "";
				if (isset($variations_options[$att_id]) && !empty($variations_options[$att_id]['variations_item_width'])){
					$variations_item_width = $variations_options[$att_id]['variations_item_width'];
				}
				$variations_item_height = "";
				if (isset($variations_options[$att_id]) && !empty($variations_options[$att_id]['variations_item_height'])){
					$variations_item_height = $variations_options[$att_id]['variations_item_height'];
				}
				$variations_show_name = "";
				if (isset($variations_options[$att_id]) && !empty($variations_options[$att_id]['variations_show_name'])){
					$variations_show_name = $variations_options[$att_id]['variations_show_name'];
				}
				$variations_show_reset_button="";
				if (isset($variations_options[$att_id]) && !empty($variations_options[$att_id]['variations_show_reset_button'])){
					$variations_show_reset_button = $variations_options[$att_id]['variations_show_reset_button'];
				}

				if ( isset( $_REQUEST[ 'attribute_' . $att_id ] ) ) {
					$selected_value = $_REQUEST[ 'attribute_' . $att_id ];
				} elseif ( isset( $selected_attributes[ $att_id ] ) ) {
					$selected_value = $selected_attributes[ $att_id ];
				} else {
					$selected_value = '';
				}

				// Get terms if this is a taxonomy - ordered
				if ( taxonomy_exists( $att_id ) ) {

					if (function_exists('wc_get_product_terms')){
						$terms = wc_get_product_terms( $tm_product_id, $att_id, array( 'fields' => 'all' ) );
					}else{
						$orderby = wc_attribute_orderby( $att_id );

						switch ( $orderby ) {
						case 'name' :
							$args = array( 'orderby' => 'name', 'hide_empty' => false, 'menu_order' => false );
							break;
						case 'id' :
							$args = array( 'orderby' => 'id', 'order' => 'ASC', 'menu_order' => false, 'hide_empty' => false );
							break;
						case 'menu_order' :
							$args = array( 'menu_order' => 'ASC', 'hide_empty' => false );
							break;
						}

						$terms = get_terms( $att_id, $args );
					}

					$flipped_haystack = array_flip($options);
					$_index = 0;
					foreach ( $terms as $term ) {
						if ( !isset($flipped_haystack[$term->slug]) ){
							continue;
						} 
						
						$options_array[esc_attr( $term->slug )] = apply_filters( 'woocommerce_variation_option_name', $term->name );
						if (sanitize_title( $selected_value ) == sanitize_title( $term->slug )){
							$default_value = $_index;
						}
						if(isset($variations_options[$att_id]) && isset($variations_options[$att_id]['variations_imagep'])){
							if(!empty($variations_options[$att_id]['variations_imagep'][$term->slug])){
								$imagesp[$_index] = $variations_options[$att_id]['variations_imagep'][$term->slug];
							}else{
								$imagesp[$_index] = apply_filters( 'woocommerce_tm_epo_variation_product_image', "", $term->slug, $options, $available_variations);
							}							
						}
						if(isset($variations_options[$att_id]) && isset($variations_options[$att_id]['variations_image'])){
							if (!empty($variations_options[$att_id]['variations_image'][$term->slug])){
								$images[$_index] = $variations_options[$att_id]['variations_image'][$term->slug];
							}else{
								$images[$_index] = apply_filters( 'woocommerce_tm_epo_variation_image', "", $term->slug, $options, $available_variations);
							}							
						}
						if(isset($variations_options[$att_id]) && isset($variations_options[$att_id]['variations_color']) && isset($variations_options[$att_id]['variations_color'][$term->slug])){
							$color[$_index] = $variations_options[$att_id]['variations_color'][$term->slug];
						}
						
						$_index++;
					}
				} else {

					$_index = 0;
					foreach ( $options as $option ) {
						$option = TM_EPO_HELPER()->html_entity_decode($option);
						if ( version_compare( get_option( 'woocommerce_db_version' ), '2.4', '<' )  ){
							$options_array[sanitize_title( $option )] = apply_filters( 'woocommerce_variation_option_name', $option );
						}else{
							$options_array[ $option ] = apply_filters( 'woocommerce_variation_option_name', $option );
						}
						
						if (sanitize_title( $selected_value ) == sanitize_title( $option )){
							$default_value = $_index;
						}
						if(isset($variations_options[$att_id]) && isset($variations_options[$att_id]['variations_imagep'])){
							if(!empty($variations_options[$att_id]['variations_imagep'][$option])){
								$imagesp[$_index] = $variations_options[$att_id]['variations_imagep'][$option];
							}else{
								$imagesp[$_index] = apply_filters( 'woocommerce_tm_epo_variation_product_image', "", $option, $options, $available_variations);
							}
							
						}
						if(isset($variations_options[$att_id]) && isset($variations_options[$att_id]['variations_image'])){
							if(!empty($variations_options[$att_id]['variations_image'][$option])){
								$images[$_index] = $variations_options[$att_id]['variations_image'][$option];
							}else{
								$images[$_index] = apply_filters( 'woocommerce_tm_epo_variation_image', "", $option, $options, $available_variations);
							}							
						}
						if(isset($variations_options[$att_id]) 
							&& isset($variations_options[$att_id]['variations_color']) 
							&& isset($variations_options[$att_id]['variations_color'][$option])){
							$color[$_index] = $variations_options[$att_id]['variations_color'][$option];
						}

						$_index++;
					}

				}

				switch ($variations_display_as) {
					case 'select':
						if (class_exists('TM_EPO_FIELDS_select')){

							$element_display 	= new TM_EPO_FIELDS_select();

							$fake_element = array(
								"use_url" 				=> "",
								"textbeforeprice" 		=> "",
								"textafterprice" 		=> "",
								"hide_amount" 			=> "hidden",
								"changes_product_image" => $changes_product_image,
								"placeholder" 			=> __( 'Choose an option', 'woocommerce' ),
								"default_value" 		=> $default_value,
								"default_value_override"=> true,
								"imagesp" 				=> $imagesp,
								"containter_css_id" 	=> "variation-element-",
								"options" 				=> $options_array
							);

							$display = $element_display->display_field($fake_element, array(
								'name_inc' 			=> 'tm_attribute_'.$att_id.$form_prefix, 
								'element_counter' 	=> "", 
								'tabindex' 			=> "", 
								'form_prefix' 		=> $form_prefix, 
								'field_counter' 	=> $field_counter) 
							);
							
							if (is_array($display)){
								
								$field_args = array(
									"name" 				=> 'tm_attribute_'.$att_id.$form_prefix,
									"id" 				=> 'tm_attribute_id_'.$att_id.$form_prefix,
									"class" 			=> $variations_class." tm-epo-variation-element",
									'tabindex' 			=> "",
									'amount' 			=> "",
									'element_data_attr' => array("data-tm-for-variation"=>$att_id)
								);

								$field_args=array_merge($field_args,$display);
								
								if(!empty($variations_class)){
									$variations_builder_element_start_args["class"] = $variations_builder_element_start_args_class." ".$variations_class;
								}								
								$variations_builder_element_start_args["required"] = 1;
								$variations_builder_element_start_args["title"] = !empty($variations_label)?$variations_label:wc_attribute_label( $name );
								$variations_builder_element_start_args["class_id"] = "tm-variation-ul-".$variations_display_as." variation-element-".$loop.$form_prefix;
								$variations_builder_element_start_args["tm_undo_button"]="";
								$variations_builder_element_start_args["tm_product_id"]=isset($tm_product_id)?$tm_product_id:0;
								wc_get_template(
									'tm-builder-element-start.php',
									$variations_builder_element_start_args ,
									$tm__namespace,
									$tm_template_path
								);

								wc_get_template(
									'tm-select.php',
									$field_args ,
									$tm__namespace,
									$tm_template_path
								);

								wc_get_template(
									'tm-builder-element-end.php',
									$variations_builder_element_end_args,
									$tm__namespace,
									$tm_template_path
								);
							}
						}
						break;
					
					case 'radio':
					case 'image':
					case 'color':
						if ($variations_display_as=="color"){
							$images = array();
						}
						if (class_exists('TM_EPO_FIELDS_radio') && !empty($options_array)){

							$element_display = new TM_EPO_FIELDS_radio();

							if(!empty($variations_class)){
								$variations_builder_element_start_args["class"] = $variations_builder_element_start_args_class." ".$variations_class;
							}								
							$variations_builder_element_start_args["required"] = 1;
							$variations_builder_element_start_args["title"] = !empty($variations_label)?$variations_label:wc_attribute_label( $name );

							$variations_builder_element_start_args["class_id"] = "tm-variation-ul-".$variations_display_as." variation-element-".$loop.$form_prefix;
							if (!empty($variations_show_reset_button)){
								$variations_builder_element_start_args["tm_undo_button"]='<span data-tm-for-variation="'.$att_id.'" class="tm-epo-reset-variation"><i class="tcfa tcfa-undo"></i></span>';
							}else{
								$variations_builder_element_start_args["tm_undo_button"]="";
							}
							$variations_builder_element_start_args["tm_product_id"]=isset($tm_product_id)?$tm_product_id:0;
							wc_get_template(
								'tm-builder-element-start.php',
								$variations_builder_element_start_args ,
								$tm__namespace,
								$tm_template_path
							);

							$v_field_counter = 0;

							$fake_element = array(
								"default_value" 		=> $default_value,
								"default_value_override"=> true,
								"class" 				=> $variations_class." tm-epo-variation-element",
								"textbeforeprice" 		=> "",
								"textafterprice" 		=> "",
								"hide_amount" 			=> "hidden",
								"use_images" 			=> ($variations_display_as=="image" || $variations_display_as=="color")?"images":"",
								"use_url" 				=> "",
								"images" 				=> $images,
								"imagesp" 				=> $imagesp,
								"url" 					=> array(),
								"limit" 				=> "",
								"items_per_row" 		=> $variations_items_per_row,
								"item_width" 			=> $variations_item_width,
								"item_height" 			=> $variations_item_height,
								"show_label" 			=> $variations_show_name,
								"exactlimit" 			=> "",
								"minimumlimit" 			=> "",
								"swatchmode" 			=> "",
								"changes_product_image" => $changes_product_image,
								"containter_css_id" 	=> "variation-element-"
							);

							$element_display->display_field_pre($fake_element, array(
											'element_counter' 	=> $loop, 
											'tabindex' 			=> $v_field_counter, 
											'form_prefix' 		=> $form_prefix, 
											'field_counter'		=> $v_field_counter,
											'product_id'=>isset($tm_product_id)?$tm_product_id:0) );

							foreach ($options_array as $value => $label) {

								if (isset($color[$v_field_counter])){
									$fake_element["color"] = $color[$v_field_counter];
								}else{
									unset($fake_element["color"]);
								}

								$display = $element_display->display_field($fake_element, array(
									'name_inc' 			=> 'tm_attribute_'.$att_id."_".$loop.$form_prefix, 
									'value' 			=> $value, 
									'label' 			=> $label, 
									'element_counter' 	=> $loop, 
									'tabindex' 			=> $v_field_counter, 
									'form_prefix' 		=> $form_prefix, 
									'field_counter' 	=> $v_field_counter) 
								);

								if (is_array($display)){
								
									$field_args = array(
										'tm_element_settings' 	=> $fake_element,
										'id'    			=> 'tm_attribute_id_'.$att_id."_".$loop."_".$v_field_counter."_".intval($v_field_counter+$loop).$form_prefix,//doesn't actually gets that value
										'name'    			=> 'tm_attribute_'.$att_id."_".$loop.$form_prefix,
										'class'   			=> $variations_class." tm-epo-variation-element",								
										'tabindex'  		=> $v_field_counter,
										'rules'   			=> '',
										'original_rules' 	=> '',
										'rules_type' 		=> '',
										'tabindex' 			=> "",
										'amount' 			=> "",
										'element_data_attr' => array("data-tm-for-variation"=>$att_id),
										'border_type' 		=> TM_EPO()->tm_epo_css_selected_border
									);

									$field_args=array_merge($field_args,$display);

									wc_get_template(
										'tm-radio.php',
										$field_args ,
										$tm__namespace,
										$tm_template_path
									);
								}

								$v_field_counter++;
							}

							wc_get_template(
								'tm-builder-element-end.php',
								$variations_builder_element_end_args,
								$tm__namespace,
								$tm_template_path
							);
						}
						break;
				}

			}

			if ( sizeof( $attributes ) == $loop ){
				echo '<a class="reset_variations" href="#reset">' . ((!empty(TM_EPO()->tm_epo_reset_variation_text))?TM_EPO()->tm_epo_reset_variation_text:__( 'Reset options', 'woocommerce-tm-extra-product-options' )) . '</a>';
			}								
		}
	}

}
do_action( 'tm_after_styled_variations' , $tm_product);