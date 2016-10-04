<?php

if (!defined('ABSPATH'))
    die("Can't load this file directly");
	
class wooexim_product{

	function wooexim_product()
	{
		add_action( 'wp_ajax_wooexim_get_filter_results', array( &$this, 'wooexim_get_filter_results' ));
		
		add_action( 'wp_ajax_wooexim_remove_export_entry', array( &$this, 'wooexim_remove_export_entry' ));
		
		add_action( 'wp_ajax_wooexim_import_products', array( &$this, 'wooexim_import_products' ));
		
		add_action( 'wp_ajax_wooexim_save_product_fields', array( &$this, 'wooexim_save_product_fields' ));
		
		add_action( 'wp_ajax_save_product_scheduled', array( &$this, 'save_product_scheduled' ));
		
		add_action( 'init', array( &$this, 'update_product_attributes' ));
		
		
 	}
	
	function wooexim_get_product_category()
	{
		global $wooexim_product;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$query_args = array(
					
						'taxonomy'   => 'product_cat',
						
						'hide_empty' => false,
						
						'number' 	=> 2000,
						
						'orderby' => 'id',
							
						'order' => 'ASC'					
						
					);
		
		$product_category = get_terms('product_cat',$query_args);
		
		return $product_category;
	}
	
	function wooexim_get_product()
	{
		global $wooexim_product;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$query_args = array(
		
						'posts_per_page' 	=> 2000,
						
						'post_type'   		=> 'product',
						
						'orderby' 			=> 'post_date',
						
						'order' 			=> 'ASC',
											
					);
		$product_results = new WP_Query( $query_args );
		
		$product_list = $product_results->get_posts();
		
		return $product_list;
	}
	
	function wooexim_get_author_list()
	{
		global $wooexim_product;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$query_args = array(
							'who'		=> 'authors',
							'number'   	=> 2000,
							'fields' 	=>	array('ID','display_name','user_email'),
						);
		
		$user_query = new WP_User_Query($query_args);
		
		$user_results = $user_query->get_results();
		
		return $user_results;
		
	}
	
 	function wooexim_get_filter_results()
	{
		global $wooexim_product;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$product_list = $wooexim_product -> get_filter_product($_POST);
		
		$product_fields = $wooexim_product -> get_updated_product_fields();
		
		ob_start();
		
		?>
		
		<div class="wooexim_filter_data_container">
		
			<table class="wooexim_product_filter_data">
				<thead>
					<tr>
						<th></th>
						<?php foreach($product_fields['product_fields'] as $product_info){?>
							<th><?php echo $product_info['field_value'];?></th>
						<?php }?>
					</tr>
				</thead>
				<tbody>
					<?php 
					if(!empty($product_list)){
						$count = 1;
						$sub_count = 0;
						$temp_count = 0.1;
						foreach($product_list as $product_data){
							if($product_data['_product_type'] == 'variation')
							{
								$count--;
								$sub_count = $temp_count + $count;
								$temp_count = $temp_count + 0.1;
							}
							else
							{
								$sub_count = $count ;
								$temp_count = 0.1;
							}
							?>
							<tr>
								<td><?php echo $sub_count;?></td>
								<?php 
									foreach($product_fields['product_fields'] as $product_info){
										$output = "";
										if($product_info['field_key']=='images')
										{	
											foreach( $product_data[$product_info['field_key']] as $product_images)
											{
												 $output .= $product_images['src'].',';
											}
											$output = substr($output,0,-1);
										}
										else
										{
											$output = isset($product_data[$product_info['field_key']])?$product_data[$product_info['field_key']]:"";
										}
										?>
										<td><?php echo $output;?></td>
										<?php
										 
								}?>
							</tr>
							<?php 
							$count++;
						}
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
		
		$buffer_output = ob_get_contents();
		
		ob_end_clean();
		
		$return_value = array();
		
		$return_value['message']	= 'success';
		
		$return_value['data']		= $buffer_output ;
				
		echo json_encode($return_value );
		
		die();



	}
	function get_filter_product($wooexim_data)
	{
		global $wooexim_product;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$product_categories = isset($wooexim_data['wooexim_product_category'])?$wooexim_data['wooexim_product_category']:array();
		
		$product_ids		= isset($wooexim_data['wooexim_product_ids'])?$wooexim_data['wooexim_product_ids']:array();
		
		$author_ids 		= isset($wooexim_data['wooexim_product_author_ids'])?$wooexim_data['wooexim_product_author_ids']:array();
		
		$total_records		= isset($wooexim_data['wooexim_total_records'])?$wooexim_data['wooexim_total_records']:"";
		
		$offset_records 	= isset($wooexim_data['wooexim_offset_records'])?$wooexim_data['wooexim_offset_records']:"";
		
		$temp_start_date 	= isset($wooexim_data['wooexim_start_date'])?$wooexim_data['wooexim_start_date']:"";
		
		$temp_end_date 		= isset($wooexim_data['wooexim_end_date'])?$wooexim_data['wooexim_end_date']:"";
		
		$end_date           = "";
		
		$start_date         = ""; 
		
		if($temp_start_date!="")
		{
			$temp_start_date			= explode('-',$temp_start_date );
		
			$start_date 				= $temp_start_date[2].'-'.$temp_start_date[0].'-'.$temp_start_date[1];
		}
		if($temp_end_date!="")
		{		
			$temp_end_date				= explode('-',$temp_end_date );
			
			$end_date 					= $temp_end_date[2].'-'.$temp_end_date[0].'-'.$temp_end_date[1];
		}
		
		
		$query_args = array(
		
						'posts_per_page' 	=> -1,
						
						'post_type'   		=> 'product',
						
						'orderby' 			=> 'ID',
						
						'order' 			=> 'ASC',
						
						//'fields' 			=> 'ids',
											
					);
		
				
		if(!empty($product_categories))
		{
			$query_args['tax_query'] = array(
										array(
											'taxonomy' => 'product_cat',
											'field'    =>'id',
											'terms'    => $product_categories
										),
									);	
		}
		if(!empty($product_ids))
		{
			$query_args['post__in'] = $product_ids;
		}
		
		if($total_records!="" && $total_records>0 )
		{
			$query_args['posts_per_page'] = $total_records;
			
			if($offset_records!="" && $offset_records>=0) 
			{
				$query_args['offset'] = $offset_records;
			}
		}
		
		if($end_date!="" || $start_date!="")
		{
			$date_data = array();
			
			if($end_date!="")
			{
				$date_data['before'] =  $end_date." 23:59:59";
			}
			
			if($start_date!="")
			{
				$date_data['after'] =  $start_date." 00:00:00";
			}
			
			$date_data['inclusive'] = true;
			
			$query_args['date_query'] = array($date_data);
			
		}
		
		$product_results = new WP_Query( $query_args );
		
		$product_data_list = array();
		$product_flag = 0;
		while ($product_results->have_posts()) {
			$product_results->the_post();
			global $product;
			
			if(!is_object($product) || $product_flag>0)
			{
				if(function_exists(get_product))
				{
					$product = get_product($product_results->post->ID);
				}
				else
				{
					$product = new WC_product($product_results->post->ID);
				}
				$product_flag++;
			}
				/*wp_set_object_terms($product_results->post->ID,$product_info['_product_type'],'product_type');	*/					
			$product_data_list[] = array(
										'post_title'              => @$product->post->post_title,
										'id'                 => @(int) $product->is_type( 'variation' ) ? $product->get_variation_id() : $product->id,
										'created_at'         =>  @$product->get_post_data()->post_date_gmt ,
										'updated_at'         => @$product->get_post_data()->post_modified_gmt,
										'_product_type'               => @$product->product_type,
										'post_status'             => @$product->get_post_data()->post_status,
										'_downloadable'       => @$product->is_downloadable()?'yes':'no',
										'_virtual'            => @$product->is_virtual()?'yes':'no',
										'permalink'          => @$product->get_permalink(),
										'_sku'                => @$product->get_sku(),
										'_price'              => @$product->get_price(),
										'_regular_price'      => @$product->get_regular_price(),
										'_sale_price'         => @$product->get_sale_price() ? $product->get_sale_price() : null,
										'price_html'         => @$product->get_price_html(),
										'taxable'            => @$product->is_taxable()?'yes':'no',
										'_tax_status'         => @$product->get_tax_status(),
										'_tax_class'          => @$product->get_tax_class(),
										'_manage_stock'     => @$product->managing_stock()?'yes':'no',
										'_stock'     => @$product->get_stock_quantity(),
										'_stock_status'           => @$product->is_in_stock()?'instock':"outofstock",
										'backorders_allowed' => @$product->backorders_allowed(),
										'_backorders'        => @$product->backorders,
										'_sold_individually'  => @$product->is_sold_individually()?'yes':'no',
										'purchaseable'       => @$product->is_purchasable()?'yes':'no',
										'_featured'           => @$product->is_featured()?'yes':'no',
										'_visibility'            => @$product->visibility,
										'catalog_visibility' => @$product->visibility,
										'on_sale'            => @$product->is_on_sale()?'yes':'no',
										'_product_url'        => @$product->is_type( 'external' ) ? $wooexim_product->wooexim_get_product_url($product) : '',
										'_button_text'        => @$product->is_type( 'external' ) ? $wooexim_product->wooexim_get_button_text($product) : '',
										'_weight'             => @$product->get_weight() ? wc_format_decimal( $product->get_weight(), 2 ) : null,
										'dimensions'         => @array(
											'length' => $product->length,
											'width'  => $product->width,
											'height' => $product->height,
											'unit'   => get_option( 'woocommerce_dimension_unit' ),
										),
										'_length' 			 => @$product->length,
										'_width'  			 => @$product->width,
										'_height' 			 => @$product->height,
										'unit'   			 => @get_option( 'woocommerce_dimension_unit' ),
										'shipping_required'  => @$product->needs_shipping()?'yes':'no',
										'shipping_taxable'   => @$product->is_shipping_taxable()?'yes':'no',
										'product_shipping_class'     => @$product->get_shipping_class(),
										'shipping_class_id'  => @( 0 !== $product->get_shipping_class_id() ) ? $product->get_shipping_class_id() : null,
										'post_content'        => @do_shortcode( $product->get_post_data()->post_content ) ,
										'post_excerpt'  =>  @$product->get_post_data()->post_excerpt ,
										'comment_status'    => @$product->get_post_data()->comment_status ,
										'average_rating'     => @wc_format_decimal( $product->get_average_rating(), 2 ),
										'rating_count'       => @(int) $product->get_rating_count(),
										'related_ids'        => @$wooexim_product->wooexim_get_sku_by_id(implode(',',array_map( 'absint', array_values( $product->get_related() ) ))),
										'_upsell_ids'         => @$wooexim_product->wooexim_get_sku_by_id(implode(',',array_map( 'absint', $product->get_upsells() ))),
										'_crosssell_ids'     => @$wooexim_product->wooexim_get_sku_by_id(implode(',',array_map( 'absint', $product->get_cross_sells() ))),
										'parent_id'          => @$product->post->post_parent,
										'product_cat'         => @$wooexim_product->wooexim_get_categories_by_producy_id($product->id),//implode(',',wp_get_post_terms( $product->id, 'product_cat', array( 'fields' => 'names' ) )),
										'product_tag'         => @implode(',',wp_get_post_terms( $product->id, 'product_tag', array( 'fields' => 'names' ) )),
										'images'             => @$wooexim_product->wooexim_get_images( $product ),
										'has_post_thumbnail'            => @has_post_thumbnail( $product->id )?'yes':'no',
										'featured_src'       => @wp_get_attachment_url( get_post_thumbnail_id( $product->is_type( 'variation' ) ? $product->variation_id : $product->id ) ),
										'_product_attributes' => @$wooexim_product->wooexim_get_attributes( $product ),
										'_product_custom_fields'   => @maybe_serialize(get_post_custom($product->id)),
										'downloads'          =>  @$wooexim_product->wooexim_get_downloads( $product ),
										'_downloadable_files' => @maybe_serialize($product->get_files()),
										'_download_limit'     =>  @$product->download_limit,
										'_download_expiry'    =>  @$product->download_expiry,
							
										'_download_type'      => @$product->download_type,
										'_purchase_note'      => @do_shortcode( wp_kses_post( $product->purchase_note ) ),
										'total_sales'        => @metadata_exists( 'post', $product->id, 'total_sales' ) ? (int) get_post_meta( $product->id, 'total_sales', true ) : 0,
										'ping_status'  		 => @$product->get_post_data()->ping_status?'open':'close' ,
										'variations'         => @array(),
										'_variation_description' => "",
										'menu_order' => @$product->get_post_data()->menu_order,
										'post_parent'             => @$product->get_post_data()->post_parent,
									); 
									
				if ( $product->is_type( 'variable' ) && $product->has_child() ) {
			
   					
					foreach ( $product->get_children() as $child_id ) 
					{
	
						$variation = $product->get_child( $child_id );
						
						if ( ! $variation->exists() ) {
							continue;
						}
						$variation = $product->get_child( $child_id );
						
						if($variation->product_type == 'variation')
						{
							$product_data_list[] = array(
								'post_title'             => @$variation->post->post_title,
								'id'                => @$variation->get_variation_id(),
								'created_at'        => @$variation->get_post_data()->post_date_gmt,
								'updated_at'        => @$variation->get_post_data()->post_modified_gmt ,
								'_product_type'     => @$variation->product_type,
								'post_status'            => @$variation->get_post_data()->post_status,
								'_downloadable'      => @$variation->is_downloadable()?'yes':'no',
								'_virtual'           => @$variation->is_virtual()?'yes':'no',
								'permalink'         => @$variation->get_permalink(),
								'_sku'               => @$variation->get_sku(),
								'_price'             => @$variation->get_price(),
								'_regular_price'     => @$variation->get_regular_price(),
								'_sale_price'        => @$variation->get_sale_price() ? $variation->get_sale_price() : null,
								'price_html'        => @$variation->get_price_html(),
								'taxable'           => @$variation->is_taxable()?'yes':'no',
								'_tax_status'        => @$variation->get_tax_status(),
								'_tax_class'         => @$variation->get_tax_class(),
								'_manage_stock'    => @$variation->managing_stock()?'yes':'no',
								'_stock'    => @$variation->get_stock_quantity(),
								'_stock_status'          => @$variation->is_in_stock()?'instock':"outofstock",
								'backorders_allowed'=> @$variation->backorders_allowed()?'yes':'no',
								'_backorders'       => @$variation->backorders,
								'_sold_individually' => @$variation->is_sold_individually()?'yes':'no',
								'purchaseable'      => @$variation->is_purchasable()?'yes':'no',
								'_featured'          => @$variation->is_featured()?'yes':'no',
								'_visibility'           => @$variation->visibility,//$variation->variation_is_visible()?'yes':'no',
								'catalog_visibility'=> @$variation->visibility,
								'on_sale'           => @$variation->is_on_sale()?'yes':'no',
								'_product_url'       => @$variation->is_type( 'external' ) ? $wooexim_product->wooexim_get_product_url($variation) : '',
								'_button_text'       => @$variation->is_type( 'external' ) ? $wooexim_product->wooexim_get_button_text($variation) : '',
								'_weight'            => @$variation->get_weight() ? wc_format_decimal( $variation->get_weight(), 2 ) : null,
								'dimensions'        => @array(
									'length' => $variation->length,
									'width'  => $variation->width,
									'height' => $variation->height,
									'unit'   => get_option( 'woocommerce_dimension_unit' ),
								),
								'_length' 			=> @$variation->length,
								'_width'  			=> @$variation->width,
								'_height' 			=> @$variation->height,
								'unit'  			=> @get_option( 'woocommerce_dimension_unit' ),
								'product_shipping_class'    => @$variation->get_shipping_class(),
								'shipping_class_id' => @( 0 !== $variation->get_shipping_class_id() ) ? $variation->get_shipping_class_id() : null,
								'shipping_required' => @$variation->needs_shipping()?'yes':'no',
								'shipping_taxable'  => @$variation->is_shipping_taxable()?'yes':'no',
								'post_content'       => @do_shortcode( $variation->get_post_data()->post_content ) ,
								'post_excerpt' =>  @$variation->get_post_data()->post_excerpt ,
								'comment_status'   => @$variation->get_post_data()->comment_status ,
								'average_rating'    => @wc_format_decimal( $variation->get_average_rating(), 2 ),
								'rating_count'      => @(int) $variation->get_rating_count(),
								'related_ids'       => @$wooexim_product->wooexim_get_sku_by_id(implode(',',array_map( 'absint', array_values( $variation->get_related() ) ))),
								'_upsell_ids'        => @$wooexim_product->wooexim_get_sku_by_id(implode(',',array_map( 'absint', $variation->get_upsells() ))),
								'_crosssell_ids'    => @$wooexim_product->wooexim_get_sku_by_id(implode(',',array_map( 'absint', $variation->get_cross_sells() ))),
								'parent_id'         => @$variation->post->post_parent,
								'product_cat'        => @$wooexim_product->wooexim_get_categories_by_producy_id($variation->id),//implode(',',wp_get_post_terms( $variation->id, 'product_cat', array( 'fields' => 'names' ) )),
								'product_tag'              => @implode(',',wp_get_post_terms( $variation->id, 'product_tag', array( 'fields' => 'names' ) )),
								'images'             => @$wooexim_product->wooexim_get_images( $variation ),
								'has_post_thumbnail'            =>@has_post_thumbnail( $variation->get_variation_id() )?'yes':'no',
								'featured_src'      => @wp_get_attachment_url( get_post_thumbnail_id( $variation->is_type( 'variation' ) ? $variation->variation_id : $variation->id ) ),
								'_product_attributes'        => @$wooexim_product->wooexim_get_attributes( $variation ),
								'_product_custom_fields'   => @maybe_serialize(get_post_custom($child_id)),
								'downloads'         => @$wooexim_product->wooexim_get_downloads( $variation ),
								'_downloadable_files' => @maybe_serialize($variation->get_files()),
								'_download_limit'    =>  @$variation->download_limit,
								'_download_expiry'   =>  @$variation->download_expiry,
								'_download_type'     => @$variation->download_type,
								'_purchase_note'     => @do_shortcode( wp_kses_post( $variation->purchase_note ) ) ,
								'total_sales'       => @metadata_exists( 'post', $variation->id, 'total_sales' ) ? (int) get_post_meta( $variation->id, 'total_sales', true ) : 0,
								'ping_status'  	    => @$variation->get_post_data()->ping_status?'open':'close' ,
								'variations'        => @array(),
								'_variation_description' => @get_post_meta( $variation->id, '_variation_description', true ),
								'menu_order' => @$variation->get_post_data()->menu_order,
								'post_parent'            => @(int) $product->is_type( 'variation' ) ? $product->get_variation_id() : $product->id,
						);
					}
					
				}
				
			}
			
		}
		
		wp_reset_postdata();
		
		return $product_data_list;
	}
	function get_product_fields()
	{
		$get_product_fields = array(
					
						'product_fields' => array(
										 array(
											'field_key' => 'id',
											'field_display' => 1,
											'field_title' =>'Id',
											'field_value' =>'Id', 
										),
										array(
											'field_key' => 'post_title',
											'field_display' => 1,
											'field_title' =>'Product Name',
											'field_value' =>'Product Name',
										),
										array(
											'field_key' => 'created_at',
											'field_display' => 1,
											'field_title' =>'Created Date',
											'field_value' =>'Created Date',
										),
										array(
											'field_key' => 'post_content',
											'field_display' => 1,
											'field_title' =>'Description',
											'field_value' =>'Description',
										),
										array(
											'field_key' => '_product_type',
											'field_display' => 1,
											'field_title' =>'Product Type',
											'field_value' =>'Product Type',  
										),
										array(
											'field_key' => 'product_cat',
											'field_display' => 1,
											'field_title' =>'Categories',
											'field_value' =>'Categories', 
										),
										array(
											'field_key' => '_price',
											'field_display' => 1,
											'field_title' =>'Price',
											'field_value' =>'Price',   
										),
										array(
											'field_key' => 'post_excerpt',
											'field_display' => 1,
											'field_title' =>'Short Description',
											'field_value' =>'Short Description',
										),
										array(
											'field_key' => 'post_status',
											'field_display' => 1,
											'field_title' =>'Product Status',
											'field_value' =>'Product Status',
										),
										array(
											'field_key' => 'permalink',
											'field_display' => 1,
											'field_title' =>'Permalink',
											'field_value' =>'Permalink', 
										),
 										array(
											'field_key' => 'product_tag',
											'field_display' => 1,
											'field_title' =>'Tags',
											'field_value' =>'Tags', 
										),
										array(
											'field_key' => '_sku',
											'field_display' => 1,
											'field_title' =>'SKU', 
											'field_value' =>'SKU', 
										),
										array(
											'field_key' => '_sale_price',
											'field_display' => 1,
											'field_title' =>'Sale Price',
											'field_value' =>'Sale Price', 
										),
										array(
											'field_key' => '_visibility',
											'field_display' => 1,
											'field_title' =>'Visibility',
											'field_value' =>'Visibility', 
										),
										array(
											'field_key' => 'on_sale',
											'field_display' => 1,
											'field_title' =>'On Sale',
											'field_value' =>'On Sale', 
										),
										array(
											'field_key' => '_stock_status',
											'field_display' => 1,
											'field_title' =>'Stock Status',
											'field_value' =>'Stock Status',  
										),
										array(
											'field_key' => '_regular_price',
											'field_display' => 1,
											'field_title' =>'Regular Price',
											'field_value' =>'Regular Price',  
										),
										array(
											'field_key' => 'total_sales',
											'field_display' => 1,
											'field_title' =>'Total Sales',
											'field_value' =>'Total Sales', 
										),
										array(
											'field_key' => '_downloadable',
											'field_display' => 1,
											'field_title' =>'Downloadable',
											'field_value' =>'Downloadable',  
										),
										array(
											'field_key' => '_virtual',
											'field_display' => 1,
											'field_title' =>'Virtual', 
											'field_value' =>'Virtual',  
										),
										array(
											'field_key' => '_purchase_note',
											'field_display' => 1,

											'field_title' =>'Purchase Note',
											'field_value' =>'Purchase Note', 
										),
										array(
											'field_key' => '_weight',
											'field_display' => 1,
											'field_title' =>'Weight',
											'field_value' =>'Weight',   
										),
										array(
											'field_key' => '_length',
											'field_display' => 1,
											'field_title' =>'Length',
											'field_value' =>'Length',  
										),
										array(
											'field_key' => '_width',
											'field_display' => 1,
											'field_title' =>'Width',
											'field_value' =>'Width',  
										),
										array(
											'field_key' => '_height',
											'field_display' => 1,
											'field_title' =>'Height',
											'field_value' =>'Height',  
										),
										array(
											'field_key' => 'unit',
											'field_display' => 1,
											'field_title' =>'Unit',
											'field_value' =>'Unit',  
										),
 										array(
											'field_key' => '_sold_individually',
											'field_display' => 1,
											'field_title' =>'Sold Individually',
											'field_value' =>'Sold Individually',  
										),
										array(
											'field_key' => '_manage_stock',
											'field_display' => 1,
											'field_title' =>'Manage Stock',
											'field_value' =>'Manage Stock', 
										),
										array(
											'field_key' => '_stock',
											'field_display' => 1,
											'field_title' =>'Stock', 
											'field_value' =>'Stock',   
										),
										array(
											'field_key' => 'backorders_allowed',
											'field_display' => 1,
											'field_title' =>'Backorders Allowed',
											'field_value' =>'Backorders Allowed',  
										),
										array(
											'field_key' => '_backorders',
											'field_display' => 1,
											'field_title' =>'Backorders',
											'field_value' =>'Backorders',  
										),
										array(
											'field_key' => 'purchaseable',
											'field_display' => 1,
											'field_title' =>'Purchaseable',
											'field_value' =>'Purchaseable',  
										),
										array(
											'field_key' => '_featured',
											'field_display' => 1,
											'field_title' =>'Featured',
											'field_value' =>'Featured',  
										),
										array(
											'field_key' => 'taxable',
											'field_display' => 1,
											'field_title' =>'Is Taxable',
											'field_value' =>'Is Taxable',  
										),
										array(
											'field_key' => '_tax_status',
											'field_display' => 1,
											'field_title' =>'Tax Status',
											'field_value' =>'Tax Status',  
										),
										array(
											'field_key' => '_tax_class',
											'field_display' => 1,
											'field_title' =>'Tax Class',
											'field_value' =>'Tax Class',   
										),
										array(
											'field_key' => 'images',
											'field_display' => 1,
											'field_title' =>'Product Images',
											'field_value' =>'Product Images',  
										),
										array(
											'field_key' => 'has_post_thumbnail',
											'field_display' => 1,
											'field_title' =>'Product Image Set',
											'field_value' =>'Product Image Set',  
										),
										array(
											'field_key' => '_download_limit',
											'field_display' => 1,
											'field_title' =>'Download Limit',
											'field_value' =>'Download Limit', 
										),
										array(
											'field_key' => '_download_expiry',
											'field_display' => 1,
											'field_title' =>'Download Expiry',
											'field_value' =>'Download Expiry', 
										),
										array(
											'field_key' => '_downloadable_files',
											'field_display' => 1,
											'field_title' =>'Downloadable Files',
											'field_value' =>'Downloadable Files', 
										),
										array(
											'field_key' => '_download_type',
											'field_display' => 1,
											'field_title' =>'Download Type', 
											'field_value' =>'Download Type',
										),
										array(
											'field_key' => '_product_url',
											'field_display' => 1,
											'field_title' =>'Product URL',
											'field_value' =>'Product URL',  
										),
										array(
											'field_key' => '_button_text',
											'field_display' => 1,
											'field_title' =>'Button Text',
											'field_value' =>'Button Text',  
										),
 										array(
											'field_key' => 'shipping_required',
											'field_display' => 1,
											'field_title' =>'Shipping Required',
											'field_value' =>'Shipping Required',  
										),
										array(
											'field_key' => 'shipping_taxable',
											'field_display' => 1,
											'field_title' =>'Shipping Taxable',
											'field_value' =>'Shipping Taxable',  
										),
										array(
											'field_key' => 'product_shipping_class',
											'field_display' => 1,
											'field_title' =>'Shipping Class',
											'field_value' =>'Shipping Class',  
										),
										array(
											'field_key' => 'shipping_class_id',
											'field_display' => 1,
											'field_title' =>'Shipping Class Id',
											'field_value' =>'Shipping Class Id',  
										),
										array(
											'field_key' => 'comment_status',
											'field_display' => 1,
											'field_title' =>'Comment Status',
											'field_value' =>'Comment Status',  
										),
										array(
											'field_key' => 'average_rating',
											'field_display' => 1,
											'field_title' =>'Average Rating',
											'field_value' =>'Average Rating',  
										),
										array(
											'field_key' => 'rating_count',
											'field_display' => 1,
											'field_title' =>'Rating Count',
											'field_value' =>'Rating Count', 
										),
										array(
											'field_key' => 'related_ids',
											'field_display' => 1,
											'field_title' =>'Related Ids',
											'field_value' =>'Related Ids', 
										),
										array(
											'field_key' => '_upsell_ids',
											'field_display' => 1,
											'field_title' =>'Upsell Ids',
											'field_value' =>'Upsell Ids', 
										),
										array(
											'field_key' => '_crosssell_ids',
											'field_display' => 1,
											'field_title' =>'Cross Sell Ids',
											'field_value' =>'Cross Sell Ids', 
										),
  										array(
											'field_key' => '_product_attributes',
											'field_display' => 1,
											'field_title' =>'Attributes',
											'field_value' =>'Attributes',  
										),
										array(
											'field_key' => '_product_custom_fields',
											'field_display' => 1,
											'field_title' =>'Custom Fields',
											'field_value' =>'Custom Fields',  
										),
										array(
											'field_key' => 'post_parent',
											'field_display' => 1,
											'field_title' =>'Product Parent id',
											'field_value' =>'Product Parent id',  
										),
										array(
											'field_key' => '_variation_description',
											'field_display' => 1,
											'field_title' =>'Variation Description',
											'field_value' =>'Variation Description',  
										),
										array(
											'field_key' => 'menu_order',
											'field_display' => 1,
											'field_title' =>'Menu Order',
											'field_value' =>'Menu Order',  
										),
										array(
											'field_key' => 'comment_status',
											'field_display' => 1,
											'field_title' =>'Comment Status',
											'field_value' =>'Comment Status',  
										),
										array(
											'field_key' => 'ping_status',
											'field_display' => 1,
											'field_title' =>'Ping Status',
											'field_value' =>'Ping Status',  
										),
										
									),
								);
								
		return $get_product_fields;
	}
	function wooexim_get_downloads( $product ) 
	{
 		$downloads = array();

		if ( $product->is_downloadable() ) {

			foreach ( $product->get_files() as $file_id => $file ) {

				$downloads[] = array(
					'id'   => $file_id, // do not cast as int as this is a hash
					'name' => $file['name'],
					'file' => $file['file'],
				);
			}
		}

		return $downloads;
	}
	function wooexim_get_attributes( $product ) {

		$attributes = array();

		if ( $product->is_type( 'variation' ) ) {

			// variation attributes
			$attributes = $product->get_variation_attributes();
			
		} else {
			$attributes = $product->get_attributes();
		}
		
 		if(empty($attributes))
		{
			$attributes = "";
		}
		else
		{
			$temp_attr = array();
			
			foreach ( $attributes as $attribute )
			{
				
				if ( empty( $attribute['is_visible'] ) || ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attribute['name'] ) ) ) {
					continue;
				} 
				if ( $attribute['is_taxonomy'] ) {

					$values = wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'names' ) );
					
					$attribute['value'] =   apply_filters( 'woocommerce_attribute',implode( ', ', $values ), $attribute, $values );

				}
				$temp_attr[] = $attribute;
			}
			//$attributes = maybe_serialize($temp_attr);
			
			$new_temp_array = array();
			
			$temp_array_count = 0;
			
			foreach($temp_attr as $temp_attr_all_data)
			{
				foreach($temp_attr_all_data as $key=>$value)
				{
					$new_temp_array[$temp_array_count][$key] = @str_replace(",", "~||~",@str_replace('"', "``", $value));
				}
				$temp_array_count++;
			}
			
			$attributes = @str_replace("~||~", ",",str_replace(",", "||",json_encode($new_temp_array)));
		}
		return $attributes;
	}
	function wooexim_get_images( $product ) {

		global $wooexim_product;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$images = $attachment_ids = array();

		if ( $product->is_type( 'variation' ) ) {

			if ( has_post_thumbnail( $product->get_variation_id() ) ) {

				// Add variation image if set
				$attachment_ids[] = get_post_thumbnail_id( $product->get_variation_id() );

			} elseif ( has_post_thumbnail( $product->id ) ) {

				// Otherwise use the parent product featured image if set
				$attachment_ids[] = get_post_thumbnail_id( $product->id );
			}

		} else {

			// Add featured image
			if ( has_post_thumbnail( $product->id ) ) {
				$attachment_ids[] = get_post_thumbnail_id( $product->id );
			}

			// Add gallery images
			$attachment_ids = array_merge( $attachment_ids, $product->get_gallery_attachment_ids() );
		}

		// Build image data
		foreach ( $attachment_ids as $position => $attachment_id ) {

			$attachment_post = get_post( $attachment_id );

			if ( is_null( $attachment_post ) ) {
				continue;
			}

			$attachment = wp_get_attachment_image_src( $attachment_id, 'full' );

			if ( ! is_array( $attachment ) ) {
				continue;
			}

			$images[] = array(
				'id'         => (int) $attachment_id,
				'created_at' =>  $attachment_post->post_date_gmt ,
				'updated_at' =>  $attachment_post->post_modified_gmt ,
				'src'        => current( $attachment ),
				'title'      => get_the_title( $attachment_id ),
				'alt'        => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
				'position'   => (int) $position,
			);
		}
 
		return $images;
	}
	function get_product_export_data($wooexim_data = array())
 	{
		global $wooexim_product;
	
		$wooexim_product->wooexim_set_time_limit(0);
		
		$csv_data = "";
		
		$product_field_list = $wooexim_product -> get_updated_product_fields();
				
		$product_list_data = $wooexim_product -> get_filter_product($wooexim_data);
		
		$count = 0;
		
		foreach($product_field_list['product_fields'] as $field_data)
		{
			if($field_data['field_display']==1){
				
					$csv_data[$count][] = $field_data['field_value'];
			}
				
		}
		
		foreach($product_list_data as $product_info)
		{
			$count++;
			
			$data_result = array();
			
			foreach($product_field_list['product_fields'] as $field_data){
			
				
				if($field_data['field_display']==1){
				
					$output = "";
					
					if($field_data['field_key']=='images')
					{	
						foreach( $product_info[$field_data['field_key']] as $product_images)
						{
							 $output .= $product_images['src'].',';
						}
						$output = substr($output,0,-1);
					}
					else
					{
						$output = isset($product_info[$field_data['field_key']])?$product_info[$field_data['field_key']]:"";
					}
			
					$data_result[] = $output ;
					
				}
				
			}
			
			$csv_data[$count] = $data_result;
			
		}
			
		return $csv_data;
	}
	function wooexim_get_product_import_export_log()
	{
		global $wpdb;
		
		$results 	= $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."wooexim_export_log ORDER BY `export_log_id` DESC");
		
		return $results ;
	}
	function wooexim_remove_export_entry()
	{
		global $wpdb,$wooexim_product;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$log_id = isset($_POST['log_id'])?$_POST['log_id']:"";
		
		$file_name = isset($_POST['file_name'])?$_POST['file_name']:"";
		
		$return_value = array();
		
		$return_value['message']	= 'error';
 		
		if($log_id!="")
		{
			$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."wooexim_export_log WHERE export_log_id = %d ",$log_id));
			
			if(file_exists(WOOEXIM_UPLOAD_DIR.'/'.$file_name))
			{
				@unlink(WOOEXIM_UPLOAD_DIR.'/'.$file_name);
			}
			
			$return_value['message']	= 'success';
			
			$return_value['message_data']	= __('Successfully Deleted.',WOOEXIM_TEXTDOMAIN);
			
 		}
		
 		$return_value['data']		= '' ;
				
		echo json_encode($return_value );
		
		die();
	}
	
	function wooexim_import_products()
	{
		
		global $wooexim_product,$wooexim_import_export;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$return_value = array();
		
		$return_value['message'] = 'error';
		
		$plugin_data = $wooexim_import_export->get_wooexim_sort_order();
		if( $plugin_data['plugin_status']!='active' ){
			$return_value['message_text'] = __('Please activate plugin license.',WOOEXIM_TEXTDOMAIN);
			echo json_encode($return_value );
			die();
		}
		
		$file_url = isset($_POST['wooexim_import_file_url'])?$_POST['wooexim_import_file_url']:"";
		
		$product_field_list = $wooexim_product -> get_updated_product_fields();
		 
		$file_path = "";
		
		if(isset($_FILES['wooexim_import_file']['name']) && $_FILES['wooexim_import_file']['name']!="")
		{
			$file_name = time().'_'.$_FILES['wooexim_import_file']['name'];
			 
			if(move_uploaded_file($_FILES['wooexim_import_file']['tmp_name'],WOOEXIM_UPLOAD_DIR.'/'.$file_name ))
			{
			
				$file_path = WOOEXIM_UPLOAD_DIR.'/'.$file_name;
			}
			else
			{
			
				$return_value['message_text'] = __('File Not Uploaded. Please check file size for exceeds the limit.',WOOEXIM_TEXTDOMAIN);
				
				echo json_encode($return_value );
		
				die();
			}
			
		}
		else if($file_url!="")
		{
			$file_path = $file_url;
		}
		
		if($file_path!="")
		{
			
			$fh = @fopen($file_path, 'r' );
			
			$import_data = array();
			 
			if ( $fh !== FALSE ) { 
			
				while ( ( $new_line = fgetcsv($fh, 0) ) !== FALSE ) {
					
               		$import_data_temp[] = $new_line;
				}
				fclose( $fh );

 				unset($import_data_temp[0]);
				
				$count = 0;
				
 				foreach($import_data_temp as $data)
				{
					foreach($data as $key =>$value )
					{
						$import_data[$count][$product_field_list['product_fields'][$key]['field_key']] = $value;
						
 					}
					$count++;
				}
  				
				$return_value['message'] = 'success' ;
				
		
				
			}else{
			
 				$return_value['message_text'] = __( 'Could not open file.', WOOEXIM_TEXTDOMAIN );
			}
			if(!empty($import_data))
			{
				$product_updated_data = $wooexim_product -> wooexim_create_new_product($import_data);
				
				$return_value['data'] = @$product_updated_data['data'];
				
				$return_value['product_log'] = $wooexim_product -> set_product_import_errors($product_updated_data['product_log']);
 			}
		}
		
		echo json_encode($return_value );
		
		die();
		
	}
	function wooexim_create_new_product($product_data = array())
	{
		global $wooexim_product;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$group_ids = array();
		
		$group_child_id = array();
		
		$variable_ids = array();
		
		$imported_ids = array();
		
		$wooexim_product_log = array();
  		
		$product_create_method = isset($_POST['wooexim_product_create_method'])?$_POST['wooexim_product_create_method']:"";
		
		foreach($product_data as $product_info)
		{
 			
			$existing_product_id = "";
				
			if(isset($product_info['_sku']) && $product_info['_sku']!="" && ($product_create_method == 'update_product' || $product_create_method == 'skip_product')) {
				
				if($product_info['_product_type'] == 'variation')
				{	
					$current_post_type = 'product_variation';
				}
				else
				{
					$current_post_type = 'product';
				}
				
				$existing_post_query = array(
				
					'posts_per_page' 	=> 1,
					
					'meta_query' => array(
						array(
							'key'=>'_sku',
							'value'=> $product_info['_sku'],
							'compare' => '='
						),
						
					),
					'post_type' => $current_post_type,
					
					'fields' 	=> 'ids',
					
					);
					
				$existing_product = get_posts($existing_post_query);
				 
				if(!empty($existing_product) && $existing_product[0] !="" && $existing_product[0]>0)
				{
					
 					$existing_product_id =  $existing_product[0];
					
				}
				
				if( $product_create_method == 'skip_product' && $existing_product_id!="" && $existing_product_id>0)
				{
					$imported_ids['wooexim_product_ids'][] = $existing_product_id;
					
					$wooexim_product_log[] = sprintf( __( 'Product #%s already Exist.', WOOEXIM_TEXTDOMAIN ), $existing_product_id);
					
					continue;
				}
				 
			}
			
			if($product_info['_product_type'] == 'simple' || $product_info['_product_type'] == 'external')
			{
				//try to find a product with a matching SKU
 				
				 $product_query  = array(
				 
										'post_title'		=> $product_info['post_title'],
										
										'post_type' 		=> 'product',	
										
										'post_status'		=> $product_info['post_status'],
										
										'post_content'		=> $product_info['post_content'],
										
										'ping_status'		=> $product_info['ping_status'],
										
										'comment_status'	=> $product_info['comment_status'],
										
										'post_excerpt'		=> $product_info['post_excerpt'],
										
										'menu_order'		=> $product_info['menu_order'],
										
										);
										
				if(isset($product_info['post_parent']) && $product_info['post_parent']>0 && in_array($product_info['post_parent'],array_keys($group_ids)))
				{					
 					$product_query['post_parent'] = $group_ids[$product_info['post_parent']];
   				}
				if($existing_product_id == "")
				{
 					$new_product_id = wp_insert_post($product_query, false);
				}
				else
				{
					$product_query['ID'] = $existing_product_id;
					
					$new_product_id = wp_update_post($product_query, false);
				}
				
				if($new_product_id>0)
				{
					$wooexim_product_log[] = $wooexim_product->wooexim_set_product_attributes($product_info,$new_product_id,$existing_product_id);
					
					if(isset($product_info['post_parent']) && $product_info['post_parent']>0 && !(in_array($product_info['post_parent'],array_keys($group_ids))))
					{					
 						$group_child_id[$new_product_id] =  $product_info['post_parent'];
 						
					}
				}
			}
			else if($product_info['_product_type'] == 'grouped')
			{
				 $product_query  = array(
				 
										'post_title'		=> $product_info['post_title'],
										
										'post_type' 		=> 'product',	
										
										'post_status'		=> $product_info['post_status'],
										
										'post_content'		=> $product_info['post_content'],
										
										'ping_status'		=> $product_info['ping_status'],
										
										'comment_status'	=> $product_info['comment_status'],
										
										'post_excerpt'		=> $product_info['post_excerpt'],
										
										'menu_order'		=> $product_info['menu_order'],
										
										);
				if($existing_product_id == "")
				{
 					$new_product_id = wp_insert_post($product_query, false);
				}
				else
				{
					$product_query['ID'] = $existing_product_id;
					
					$new_product_id = wp_update_post($product_query, false);
				}
  				
				$group_ids[$product_info['id']] = $new_product_id ;
				
				if($new_product_id>0)
				{
					 
					$wooexim_product_log[] = $wooexim_product->wooexim_set_product_attributes($product_info,$new_product_id,$existing_product_id);
					
				}
			}
			else if($product_info['_product_type'] == 'variable')
			{
				$product_query  = array(
				 
										'post_title'		=> $product_info['post_title'],
										
										'post_type' 		=> 'product',	
										
										'post_status'		=> $product_info['post_status'],
										
										'post_content'		=> $product_info['post_content'],
										
										'ping_status'		=> $product_info['ping_status'],
										
										'comment_status'	=> $product_info['comment_status'],
										
										'post_excerpt'		=> $product_info['post_excerpt'],
										
										'menu_order'		=> $product_info['menu_order'],
										
										);
										
 					if($existing_product_id == "")
					{
						$new_product_id = wp_insert_post($product_query, false);
					}
					else
					{
						$product_query['ID'] = $existing_product_id;
						
						$new_product_id = wp_update_post($product_query, false);
						
						$child_args = array( 
						
							'post_parent' => $new_product_id,
							
							'post_type' => 'product_variation'
						);
						
						$child_posts = get_posts( $child_args );
						
						if (is_array($child_posts) && count($child_posts) > 0) 
						{
							foreach($child_posts as $child_post)
							{
						
								@wp_delete_post($child_post->ID, true);
						
							}
						
						}
					}
					
					$variable_ids[$product_info['id']] = $new_product_id ;
					
					if($new_product_id>0)
					{
						 
						$wooexim_product_log[] = $wooexim_product->wooexim_set_product_attributes($product_info,$new_product_id,$existing_product_id);
						
					}
					
			}
			else if($product_info['_product_type'] == 'variation')
			{	
					if($product_info['post_parent']!="" && $product_info['post_parent']>0)	
					{
						$product_query  = array(
					 
											'post_title'		=> $product_info['post_title'],
											
											'post_type' 		=> 'product_variation',	
											
											'post_status'		=> $product_info['post_status'],
											
											'post_content'		=> $product_info['post_content'],
											
											'ping_status'		=> $product_info['ping_status'],
											
											'comment_status'	=> $product_info['comment_status'],
											
											'post_excerpt'		=> $product_info['post_excerpt'],
											
											'post_parent' 		=> @$variable_ids[$product_info['post_parent']],
											
											'menu_order'		=> $product_info['menu_order'],
											
											
											);
											
 						if($existing_product_id == "")
						{
							@wp_delete_post($existing_product_id, true);	
						}
						
						$new_product_id = wp_insert_post($product_query, false);
						
						if($new_product_id>0)
						{
							 
							$wooexim_product_log[] = $wooexim_product->wooexim_set_product_attributes($product_info,$new_product_id,$existing_product_id);
							
						}
					}
				}
				$imported_ids['wooexim_product_ids'][] = $new_product_id;
			}
			
		if((!empty($group_child_id)) && (!empty($group_ids)))
		{
			foreach($group_child_id as $key=>$value)
			{
				$post_data = array('ID' => $key, 'post_parent' => $group_ids[$value]);
				
				wp_update_post($post_data);
			}
		}
		
		$product_all_data  = array();
		
		$product_all_data['data'] = $wooexim_product -> get_imported_product($imported_ids);
		
		$product_all_data['product_log'] = $wooexim_product_log;
		
		return $product_all_data;
		
	}
	function wooexim_upload_remote_image($images = "", $new_product_id = "", $has_post_thumbnail = "",$existing_product_id = "")
	{
		global $new_product_errors,$wooexim_product;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$image_list = @explode(',',$images);
		
		$new_product_errors = array();
		
		if(!empty($image_list) )
		{
			$wp_upload_dir = wp_upload_dir();

			foreach($image_list as $image_index => $image_url) {

				if($image_url!="")
				{
					$image_url = str_replace(' ', '%20', trim($image_url));
	
					$parsed_url = parse_url($image_url);
					
					$pathinfo = pathinfo($parsed_url['path']);
	
					$allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');
					
					$url_ext = @explode('.',$image_url);
					
					if(!empty($url_ext))
					{
						$image_ext = @strtolower(end($url_ext));
					}
					else
					{
						$image_ext = "";
					}
					
					if(!in_array($image_ext, $allowed_extensions)) {
					
						$new_product_errors[] = sprintf( __( 'A valid file extension wasn\'t found in %s. Extension found was %s. Allowed extensions are: %s.', WOOEXIM_TEXTDOMAIN ), $image_url, $image_ext, implode( ', ', $allowed_extensions ) );
						
						continue;
					}
	
					$dest_filename = wp_unique_filename( $wp_upload_dir['path'], $pathinfo['basename'] );
					
					$dest_path = $wp_upload_dir['path'] . '/' . $dest_filename;
					
					$dest_url = $wp_upload_dir['url'] . '/' . $dest_filename;
	
					if(ini_get('allow_url_fopen')) {
	
						if( ! @copy($image_url, $dest_path)) {
						
							$http_status = $http_response_header[0];
							
							$new_product_errors[] = sprintf( __( '%s encountered while attempting to download %s', WOOEXIM_TEXTDOMAIN ), $http_status, $image_url );
						}
	
					} elseif(function_exists('curl_init')) {
					
						$ch = curl_init($image_url);
						
						$fp = fopen($dest_path, "wb");
	
						$options = array(
						
							CURLOPT_FILE => $fp,
							
							CURLOPT_HEADER => 0,
							
							CURLOPT_FOLLOWLOCATION => 1,
							
							CURLOPT_TIMEOUT => 60);
	
						curl_setopt_array($ch, $options);
						
						curl_exec($ch);
						
						$http_status = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
						
						curl_close($ch);
						
						fclose($fp);
	
						if($http_status != 200) {
						
							unlink($dest_path);
							
							$new_product_errors[] = sprintf( __( 'HTTP status %s encountered while attempting to download %s', WOOEXIM_TEXTDOMAIN ), $http_status, $image_url );
						}
					} else {
					
						$new_product_errors[] = sprintf( __( 'Looks like %s is off and %s is not enabled. No images were imported.', WOOEXIM_TEXTDOMAIN), '<code>allow_url_fopen</code>', '<code>cURL</code>'  );
						
						break;
					}
	
					if(!file_exists($dest_path)) {
					
						$new_product_errors[] = sprintf( __( 'Couldn\'t download file %s.', WOOEXIM_TEXTDOMAIN ), $image_url );
						
						continue;
					}
	
					$new_post_image_paths[] = array(
					
						'path' => $dest_path,
						
						'source' => $image_url
					);
					}
			}

			$image_gallery_ids = array();
			
			if(!empty($new_post_image_paths))
			{
				foreach($new_post_image_paths as $image_index => $dest_path_info) {

 					if($existing_product_id != "" && $has_post_thumbnail == 'yes') {
					
						$existing_attachment_query = array(
						
							'numberposts' => 1,
							
							'meta_key' => '_import_source',
							
							'post_status' => 'inherit',
							
							'post_parent' => $existing_product_id,
							
							'meta_query' => array(
							
								array(
									'key'=>'_import_source',
									
									'value'=> $dest_path_info['source'],
									
									'compare' => '='
								)
							),
							
							'post_type' => 'attachment',
							
							'fields' 	=> 'ids',
							);
							
						$existing_attachments = get_posts($existing_attachment_query);
						
						if(!empty($existing_attachments) && isset($existing_attachments[0]) && $existing_attachments[0]!="" && $existing_attachments[0] > 0) {
						
							$new_product_errors[] = sprintf( __( 'Skipping import of duplicate image %s.', WOOEXIM_TEXTDOMAIN ), $dest_path_info['source'] );
							
							continue;
						}
					}
	
					if(!file_exists($dest_path_info['path'])) {
					
						$new_product_errors[] = sprintf( __( 'Couldn\'t find local file %s.', WOOEXIM_TEXTDOMAIN ), $dest_path_info['path'] );
						
						continue;
					}
	
					$dest_url = str_ireplace(ABSPATH, home_url('/'), $dest_path_info['path']);
					
					$path_parts = pathinfo($dest_path_info['path']);
	
					$wp_filetype = wp_check_filetype($dest_path_info['path']);
					
					$attachment = array(
					
						'guid' => $dest_url,
						
						'post_mime_type' => $wp_filetype['type'],
						
						'post_title' => preg_replace('/\.[^.]+$/', '', $path_parts['filename']),
						
						'post_content' => '',
						
						'post_status' => 'inherit'
					);
					
					$attachment_id = wp_insert_attachment( $attachment, $dest_path_info['path'], $new_product_id );
					
					require_once(ABSPATH . 'wp-admin/includes/image.php');
					
					$attach_data = wp_generate_attachment_metadata( $attachment_id, $dest_path_info['path'] );
					
					wp_update_attachment_metadata( $attachment_id, $attach_data );
	
					update_post_meta($attachment_id, '_import_source', $dest_path_info['source']);
	
					if($image_index == 0 && $has_post_thumbnail == 'yes') {
					
						update_post_meta($new_product_id, '_thumbnail_id', $attachment_id);
						
					} else {
					
						$image_gallery_ids[] = $attachment_id;
						
					}
				}
			}

			if(count($image_gallery_ids) > 0) {
			
				update_post_meta($new_product_id, '_product_image_gallery', implode(',', $image_gallery_ids));
				
			}
		}
		return $new_product_errors;
	}
	function wooexim_set_product_attributes($product_info=array(),$new_product_id=0,$existing_product_id = "")
	{
		global $wooexim_product, $wpdb;
		
		$product_log = array();
		
		$wooexim_product->wooexim_set_time_limit(0);
				
		$product_info['_product_attributes'] = isset($product_info['_product_attributes'])?$product_info['_product_attributes']:"";
		
		$product_attributes_temp = json_decode(str_replace('||', ",",maybe_unserialize($product_info['_product_attributes'])),1);
		
		$product_attributes = array();
		
		if(!empty($product_attributes_temp))
		{
			$temp_array_count = 0;
			
			foreach($product_attributes_temp as $temp_attr_all_data)
			{
				foreach($temp_attr_all_data as $key=>$value)
				{
					$product_attributes[$temp_array_count][$key] = @str_replace( "``",'"', $value);
				}
				
				$temp_array_count++;
			}
		}
		
		$product_info['_sku'] = isset($product_info['_sku'])?$product_info['_sku']:"";
		
		$product_info['_sale_price'] = isset($product_info['_sale_price'])?$product_info['_sale_price']:"";
		
		$product_info['_visibility'] = isset($product_info['_visibility'])?$product_info['_visibility']:"";
		
		$product_info['_stock_status'] = isset($product_info['_stock_status'])?$product_info['_stock_status']:"";
		
		$product_info['_price'] = isset($product_info['_price'])?$product_info['_price']:"";
		
		$product_info['_regular_price'] = isset($product_info['_regular_price'])?$product_info['_regular_price']:"";
		
		$product_info['total_sales'] = isset($product_info['total_sales'])?$product_info['total_sales']:"";
		
		$product_info['_downloadable'] = isset($product_info['_downloadable'])?$product_info['_downloadable']:"";
		
		$product_info['_virtual'] = isset($product_info['_virtual'])?$product_info['_virtual']:"";
		
		$product_info['_purchase_note'] = isset($product_info['_purchase_note'])?$product_info['_purchase_note']:"";
		
		$product_info['_weight'] = isset($product_info['_weight'])?$product_info['_weight']:"";
		
		$product_info['_length'] = isset($product_info['_length'])?$product_info['_length']:"";
		
		$product_info['_width'] = isset($product_info['_width'])?$product_info['_width']:"";
		
		$product_info['_height'] = isset($product_info['_height'])?$product_info['_height']:"";
		
		$product_info['_sold_individually'] = isset($product_info['_sold_individually'])?$product_info['_sold_individually']:"";
		
		$product_info['_manage_stock'] = isset($product_info['_manage_stock'])?$product_info['_manage_stock']:"";
		
		$product_info['_stock'] = isset($product_info['_stock'])?$product_info['_stock']:"";
		
		$product_info['_backorders'] = isset($product_info['_backorders'])?$product_info['_backorders']:"";
		
		$product_info['_featured'] = isset($product_info['_featured'])?$product_info['_featured']:"";
		
		$product_info['_tax_status'] = isset($product_info['_tax_status'])?$product_info['_tax_status']:"";
		
		$product_info['_tax_class'] = isset($product_info['_tax_class'])?$product_info['_tax_class']:"";
		
 		$product_info['_product_type'] = isset($product_info['_product_type'])?$product_info['_product_type']:"";
		
		$product_info['_crosssell_ids'] = isset($product_info['_crosssell_ids'])?$product_info['_crosssell_ids']:"";
				
		$product_info['_upsell_ids'] = isset($product_info['_upsell_ids'])?$product_info['_upsell_ids']:"";
		
		$product_info['product_tag'] = isset($product_info['product_tag'])?$product_info['product_tag']:"";
		
		$product_info['product_type'] = isset($product_info['product_type'])?$product_info['product_type']:"";
		
		$product_info['product_shipping_class'] = isset($product_info['product_shipping_class'])?$product_info['product_shipping_class']:"";
		
		$product_info['images'] = isset($product_info['images'])?$product_info['images']:"";
		
		$product_info['_download_type'] = isset($product_info['_download_type'])?$product_info['_download_type']:"";
		
		$product_info['_download_limit'] = isset($product_info['_download_limit'])?$product_info['_download_limit']:"";
		
		$product_info['_download_expiry'] = isset($product_info['_download_expiry'])?$product_info['_download_expiry']:"";
		
		$product_info['_downloadable_files'] = isset($product_info['_downloadable_files'])?maybe_unserialize($product_info['_downloadable_files']):"";
		
		$product_custom_fields = isset($product_info['_product_custom_fields'])?$product_info['_product_custom_fields']:"";
		
		$product_info['_variation_description'] = isset($product_info['_variation_description'])?$product_info['_variation_description']:"";
		
		$product_custom_fields = maybe_unserialize($product_custom_fields);
		
		$has_post_thumbnail = isset($product_info['has_post_thumbnail'])?$product_info['has_post_thumbnail']:"";
		
		$product_new_category = isset($_POST['wooexim_product_category'])?$_POST['wooexim_product_category']:array();
				
		foreach($product_custom_fields as $key => $value)
		{
			update_post_meta($new_product_id, $key, maybe_unserialize($value[0]));
		}
 			
		update_post_meta( $new_product_id, '_variation_description', wp_kses_post( $product_info['_variation_description']) );
					
		update_post_meta($new_product_id, '_sku', $product_info['_sku']);
		
		update_post_meta($new_product_id, '_sale_price', $product_info['_sale_price']);
		
		update_post_meta($new_product_id, '_visibility', $product_info['_visibility']);
		
		update_post_meta($new_product_id, '_stock_status', $product_info['_stock_status']);
		
		update_post_meta($new_product_id, '_price', $product_info['_price']);
		
		update_post_meta($new_product_id, '_regular_price', $product_info['_regular_price']);
				
		update_post_meta($new_product_id, '_downloadable', $product_info['_downloadable']);
		
 		
		
		if($product_info['_downloadable'] == 'yes' || $product_info['_downloadable'] == '1')
		{
 			
			update_post_meta($new_product_id, '_download_type',$product_info['_download_type']);
			
			update_post_meta($new_product_id, '_download_limit', $product_info['_download_limit']);
		
			update_post_meta($new_product_id, '_download_expiry', $product_info['_download_expiry']);
 			
			$product_log[] = $wooexim_product -> get_downlodable_file($new_product_id,$product_info['_downloadable_files']);
			
 		}
		
		update_post_meta($new_product_id, '_virtual', $product_info['_virtual']);
		
		update_post_meta($new_product_id, '_purchase_note', $product_info['_purchase_note']);
		
		update_post_meta($new_product_id, '_weight', $product_info['_weight']); 
		
		update_post_meta($new_product_id, '_length', $product_info['_length']);
		
		update_post_meta($new_product_id, '_width', $product_info['_width']);
		
		update_post_meta($new_product_id, '_height', $product_info['_height']);
		
		update_post_meta($new_product_id, '_sold_individually', $product_info['_sold_individually']);
		
		update_post_meta($new_product_id, '_manage_stock', $product_info['_manage_stock']);
		
		update_post_meta($new_product_id, '_stock', $product_info['_stock']);
		
		update_post_meta($new_product_id, '_backorders', $product_info['_backorders']);
		
		update_post_meta($new_product_id, '_featured', $product_info['_featured']);
		
		update_post_meta($new_product_id, '_tax_status', $product_info['_tax_status']);
		
		update_post_meta($new_product_id, '_tax_class', $product_info['_tax_class']);
		
		
		
		//if(is_array($product_attributes) && !empty($product_attributes) && $product_info['_product_type'] == 'variation')
//		{
//			foreach($product_attributes as $key=>$value)
//			{
//				update_post_meta($new_product_id, $key, $value);
//			}
//		}
		
		//update_post_meta($new_product_id, '_product_attributes',maybe_serialize($product_attributes));
		
		if(is_array($product_attributes) && !empty($product_attributes))
		{
			foreach($product_attributes as $key=>$value)
			{
				if($value['is_taxonomy'] == 1)
				{
					$attr_tax_name = $value['name'];
					
					if ( !taxonomy_exists(  $attr_tax_name ) ) {
						
						global $wp_taxonomies, $wp;
						
 						$attr_tax_name = str_replace( 'pa_', '', sanitize_title($value['name']));
						
						$attribute_taxonomy = array(
												'attribute_label'   => wc_attribute_label($attr_tax_name),
												'attribute_name'    => wc_sanitize_taxonomy_name($attr_tax_name),
												'attribute_type'    => 'select',
												'attribute_orderby' => '',
												'attribute_public'  => 0
											);
						
						$attr_tax_name =  $attribute_taxonomy['attribute_name'];
						 
						$temp = $wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute_taxonomy );

						$new_taxonomy_id = $wpdb->insert_id;
						
						do_action( 'woocommerce_attribute_added', $new_taxonomy_id , $attribute_taxonomy );
						
						flush_rewrite_rules();
						
						delete_transient( 'wc_attribute_taxonomies');
												
 						$options = explode( ',', $value['value'] );

						$taxonomy_values = array_map( 'wc_sanitize_term_text_based', $options );
						
						$taxonomy_values = array_filter( $taxonomy_values, 'strlen' );
						
						$taxonomy_data = maybe_unserialize(get_option('wooexim_taxonomy_data'));
						
						$taxonomy_data[] = array(
											'product_id' => $new_product_id,
											'attr_tax_name'=> 'pa_'.$attr_tax_name,
											'taxonomy_values'=>$taxonomy_values
										);
						$taxonomy_data = maybe_serialize($taxonomy_data);
						
						update_option('wooexim_taxonomy_data',$taxonomy_data);				
					}
					else
					{
							$options = explode( ',', $value['value'] );

							$taxonomy_values = array_map( 'wc_sanitize_term_text_based', $options );
							
							$taxonomy_values = array_filter( $taxonomy_values, 'strlen' );
							
							foreach($taxonomy_values as $term_value)
							{
								wp_insert_term( $term_value, $attr_tax_name );
							}
															
							wp_set_object_terms( $new_product_id, $taxonomy_values, $attr_tax_name );
					}
										
				}
			}
		}
		
 		if($product_info['_crosssell_ids']!="")
		{
			update_post_meta($new_product_id, '_crosssell_ids', $wooexim_product->get_ids_by_sku($product_info['_crosssell_ids']));
		}
		
 		if($product_info['_upsell_ids']!="")
		{
			update_post_meta($new_product_id, '_upsell_ids', $wooexim_product->get_ids_by_sku($product_info['_upsell_ids']));
		}
		
		if($product_info['related_ids']!="")
		{
			update_post_meta($new_product_id, 'related_ids', $wooexim_product->get_ids_by_sku($product_info['related_ids']));
		}
		
		
		if($product_info['_product_type'] == 'external')
		{
			update_post_meta($new_product_id, '_product_url',$product_info['_product_url']);
			
			update_post_meta($new_product_id, '_button_text', $product_info['_button_text']);
		}
		if(!empty($product_new_category))
		{
			
			wp_set_object_terms($new_product_id, $product_new_category, 'product_cat');
		}
		else
		{
			$product_categories_new = @json_decode($product_info['product_cat'],true);
			
			if(is_array($product_categories_new ) && isset($product_categories_new[0]['name']))
			{
				$old_categories = wp_get_post_terms( $new_product_id, 'product_cat', array("fields" => "ids") );
				
				wp_remove_object_terms( $new_product_id, $old_categories, 'product_cat' );
				
				foreach($product_categories_new as $product_current_category)
				{
					
					$product_cat = get_term_by( 'slug', $product_current_category['slug'],'product_cat'); 
				
  					$cat_id = $product_cat->term_id;
  					
					if(!$cat_id)
					{
						
						$new_categories = wp_insert_term(
													  $product_current_category['name'], 
													  'product_cat',
													  array(
														'description'=> '',
														'slug' => $product_current_category['slug']
													  )
													);
						
					
						if(!is_wp_error())
						{
							$cat_id = $new_categories['term_id'];
						}
					}
					
					if($cat_id)
					{
						wp_set_object_terms( $new_product_id, $cat_id, 'product_cat' );
					}
				}
			}
			else
			{
				wp_set_object_terms($new_product_id, explode(',',$product_categories_new), 'product_cat');
			}
		}
		
		wp_set_object_terms($new_product_id, explode(',',$product_info['product_tag']), 'product_tag');
		
		wp_set_object_terms($new_product_id,$product_info['_product_type'],'product_type');
		
		wp_set_object_terms($new_product_id, $product_info['product_shipping_class'], 'product_shipping_class' );
		
 		$product_log[] = $wooexim_product -> wooexim_upload_remote_image($product_info['images'],$new_product_id,$has_post_thumbnail,$existing_product_id);
		
		return $product_log;
	}
	function get_downlodable_file($new_product_id = 0, $downloadable_files = "")
	{
		global $wooexim_product,$download_product_errors;
		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$downloadable_files = maybe_unserialize($downloadable_files);
 		 
		$downloadable_file_list = array();
		
		$download_product_errors = array();
		
 		if(!empty($downloadable_files) )
		{
			$wp_upload_dir = wp_upload_dir();
			
			$allowed_file_types = apply_filters( 'woocommerce_downloadable_file_allowed_mime_types', get_allowed_mime_types() );

			foreach($downloadable_files as $file_hash => $file_data) {

				$file_url = str_replace(' ', '%20', trim($file_data['file']));
				
				$file_type  = wp_check_filetype( strtok( $file_url, '?' ) );

				$parsed_url = parse_url( $file_url, PHP_URL_PATH );
				
				$parsed_info_url = parse_url( $file_url );
				
				$pathinfo = pathinfo($parsed_info_url['path']);
				
				$extension  = pathinfo( $parsed_url, PATHINFO_EXTENSION );
 				if(! empty( $extension ) && ! in_array( $file_type['type'], $allowed_file_types )) {
				
 					$download_product_errors[] = sprintf( __( 'A valid file extension wasn\'t found in %s. Extension found was %s. Allowed extensions are: %s.', WOOEXIM_TEXTDOMAIN ), $file_url, $extension, implode( ',', $allowed_file_types ) );
					
 					continue;
				}

				$dest_filename = wp_unique_filename( $wp_upload_dir['path'], $pathinfo['basename'] );
				
				$dest_path = $wp_upload_dir['path'] . '/' . $dest_filename;
				
				$dest_url = $wp_upload_dir['url'] . '/' . $dest_filename;
 				if(ini_get('allow_url_fopen')) {
					if( !@copy($file_url, $dest_path)) {
						$http_status = $http_response_header[0];
						
						$download_product_errors[] = sprintf( __( '%s encountered while attempting to download %s', WOOEXIM_TEXTDOMAIN ), $http_status, $file_url );
					}

				} elseif(function_exists('curl_init')) {
				
					$ch = curl_init($file_url);
					
					$fp = fopen($dest_path, "wb");

					$options = array(
					
						CURLOPT_FILE => $fp,
						
						CURLOPT_HEADER => 0,
						
						CURLOPT_FOLLOWLOCATION => 1,
						
						CURLOPT_TIMEOUT => 60);

					curl_setopt_array($ch, $options);
					
					curl_exec($ch);
					
					$http_status = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
					
					curl_close($ch);
					
					fclose($fp);

					if($http_status != 200) {
					
						unlink($dest_path);
						
						$download_product_errors[] = sprintf( __( 'HTTP status %s encountered while attempting to download %s', WOOEXIM_TEXTDOMAIN ), $http_status, $file_url );
					}
				} else {
				
					$download_product_errors[] = sprintf( __( 'Looks like %s is off and %s is not enabled. No images were imported.', WOOEXIM_TEXTDOMAIN), '<code>allow_url_fopen</code>', '<code>cURL</code>'  );
					
 					break;
				}
 				
				if(!file_exists($dest_path)) {
				
					$download_product_errors[] = sprintf( __( 'Couldn\'t download file %s.', WOOEXIM_TEXTDOMAIN ), $file_url );
					
					continue;
				}
				$file_name = wc_clean($file_data['name']);
				
				$file_hash = md5( $dest_url );
						
				$downloadable_file_list[ $file_hash ] = array(
				
															'name' => $file_name,
															
															'file' => $dest_url
															
														);
														
														
			}
			
 			do_action( 'woocommerce_process_product_file_download_paths', $new_product_id, 0, $downloadable_file_list );
			
			update_post_meta( $new_product_id, '_downloadable_files', $downloadable_file_list );
		
		}
		return $download_product_errors;
	}
	function wooexim_get_product_url($product)
	{
		if(function_exists($product->get_product_url()))
		{
			return $product->get_product_url();
		}
		else
		{
			return get_post_meta( $product->id, '_product_url', true ) ;
		}
	}
	function wooexim_get_button_text($product)
	{
		if(function_exists($product->get_button_text()))
		{
			return $product->get_button_text();
		}
		else
		{
			return get_post_meta( $product->id, '_button_text', true ) ;
		}
	}
	function wooexim_set_time_limit($time)
	{
		$safe_mode = ini_get('safe_mode');
		
		if((!$safe_mode) || strtolower($safe_mode) == 'off')
		{
			@set_time_limit($time);
			@ini_set("memory_limit", "-1");
		}
	}
	function get_imported_product($product_ids)
	{
		global $wooexim_product;
  		
		$wooexim_product->wooexim_set_time_limit(0);
		
		$product_list = $wooexim_product -> get_filter_product($product_ids);
		
 		$product_fields = $wooexim_product -> get_updated_product_fields();
		
		ob_start();
		
		?>
		
		<div class="wooexim_filter_data_container">
			<div class="wooexim_product_imported_data_title_wrapper">
				<div class="wooexim_product_imported_data_title"><?php _e('Created/Updated Products',WOOEXIM_TEXTDOMAIN);?></div>
			</div>
			<div class="wooexim_product_filter_data_wrapper">
			<table class="wooexim_product_filter_data">
				<thead>
					<tr>
						<th></th>
						<?php foreach($product_fields['product_fields'] as $product_info){?>
							<th><?php echo $product_info['field_value'];?></th>
						<?php }?>
					</tr>
				</thead>
				<tbody>
					<?php 
					if(!empty($product_list)){
						$count = 1;
						$sub_count = 0;
						$temp_count = 0.1;
						foreach($product_list as $product_data){
							if($product_data['_product_type'] == 'variation')
							{
								$count--;
								$sub_count = $temp_count + $count;
								$temp_count = $temp_count + 0.1;
							}
							else
							{
								$sub_count = $count ;
								$temp_count = 0.1;
							}
							?>
							<tr>
								<td><?php echo $sub_count;?></td>
								<?php 
									foreach($product_fields['product_fields'] as $product_info){
 										$output = "";
										if($product_info['field_key']=='images')
										{	
											foreach( $product_data[$product_info['field_key']] as $product_images)
											{
												 $output .= $product_images['src'].',';
											}
											$output = substr($output,0,-1);
										}
										else
										{
											$output = isset($product_data[$product_info['field_key']])?$product_data[$product_info['field_key']]:"";
										}
										?>
										<td><?php echo $output;?></td>
										<?php
										 
								}?>
							</tr>
							<?php 
							$count++;
						}
					}
					?>
				</tbody>
			</table>
			</div>
		</div>
		<?php
		
		$buffer_output = ob_get_contents();
		
		ob_end_clean();
  		
 		return  $buffer_output ;
	}
	function set_product_import_errors($product_errors = array())
	{
		
		$product_all_errors =array();
		
		if(!empty($product_errors))
		{
			foreach($product_errors as $product_error)
			{
				if(is_array($product_error))
				{
					foreach($product_error as $product_sub_error)
					{
						if(is_array($product_sub_error))
						{
							foreach($product_sub_error as $product_sub1_error)
							{
								if($product_sub1_error!="" && !is_array($product_sub1_error))
								{
									$product_all_errors[] = $product_sub1_error;
								}
							}
						}
						else if($product_sub_error!="")
						{
							$product_all_errors[] = $product_sub_error;
						}
					}
				}
				else if($product_error!="")
				{
					$product_all_errors[] = $product_error;
				}
			 
			}
		}
		ob_start();
		
		
		if(!empty($product_all_errors))
		{
			foreach($product_all_errors as $wooexim_error)
			{
				?>
				<div class="wooexim_error_msg wooexim_import_error_log"><?php echo $wooexim_error;?></div>
				<?php
			}
		}
		
		$buffer_output = ob_get_contents();
		
		ob_end_clean();
		
		return $buffer_output ;
	}
	function get_new_product_fields()
	{
		global $wooexim_product;
		
		$product_fields = maybe_serialize($wooexim_product -> get_product_fields());
		
		return $product_fields;
	}
	function get_updated_product_fields()
	{
		global $wooexim_product;
		
		$old_product_fields = $wooexim_product -> get_new_product_fields();
		
		$new_fields = get_option('wooexim_product_fields',$old_product_fields);
		
		$new_fields = maybe_unserialize($new_fields);
		
		return $new_fields;
	}
	function wooexim_save_product_fields()
	{
		global $wooexim_product;
		
		$old_product_fields = $wooexim_product -> get_updated_product_fields();
		
		$new_fields = array();
		
		foreach($old_product_fields as $product_fields_key=>$product_fields_data)
		{
 			foreach($product_fields_data as $key=>$value)
			{
				$new_fields[$product_fields_key][$key]['field_key'] = $value['field_key'];
				
				$new_fields[$product_fields_key][$key]['field_display'] = $value['field_display'];
				
				$new_fields[$product_fields_key][$key]['field_title'] = $value['field_title'];
				
				$new_fields[$product_fields_key][$key]['field_value'] = isset($_POST['wooexim_'.$value['field_key'].'_field'])?$_POST['wooexim_'.$value['field_key'].'_field']:"";
				
  			}
		}
		
		$new_fields_data = maybe_serialize($new_fields);
		
		update_option('wooexim_product_fields', $new_fields_data);
		
		$return_value = array();
		
		$return_value['message']	= 'success';
		
		$return_value['message_content']	= __('Changes Saved Successfully.',WOOEXIM_TEXTDOMAIN);
			
		echo json_encode($return_value );
		
		die();
	}
	
	function save_product_scheduled()
 	{
		global $wooexim_scheduled;
		
		$general_options_data 		= $_POST;
		
		$wooexim_export_interval 			= isset($_POST['wooexim_export_interval'])?$_POST['wooexim_export_interval']:"";
		
		$return_value = array();
		
		if($wooexim_export_interval!="")
		{
		
			$scheduled_id = uniqid();
			
			$scheduled_data = $wooexim_scheduled -> get_product_scheduled_data();
			
			$scheduled_data[$scheduled_id] = $general_options_data;
			
			$scheduled_new_data = @maybe_serialize($scheduled_data);
			
			update_option('wooexim_product_scheduled_data',$scheduled_new_data);
						
			wp_schedule_event( time(), $wooexim_export_interval, 'wooexim_cron_scheduled_product_export',array($scheduled_id) );
			
			$return_value['message']		= 'success';
			
		}		
		else
		{
			$return_value['message']		= 'error';
		}
		
		echo json_encode($return_value );
		
		die();
	}	
	function update_product_attributes()
	{
		$taxonomy_data = get_option('wooexim_taxonomy_data');
		
		if($taxonomy_data!="")
		{			
			$taxonomy_data = maybe_unserialize($taxonomy_data);
			
			if(is_array($taxonomy_data) && !empty($taxonomy_data))
			{
				foreach($taxonomy_data as $new_data )
				{		
					$taxonomy_values = $new_data['taxonomy_values'];
					
					$new_product_id = $new_data['product_id'];
					
					$attr_tax_name = $new_data['attr_tax_name'];
						
					if ( taxonomy_exists(  $attr_tax_name ) ) 
					{			
						foreach($taxonomy_values as $term_value)
						{
							wp_insert_term( $term_value, $attr_tax_name );
						}
						wp_set_object_terms( $new_product_id, $taxonomy_values, $attr_tax_name );
					}
				}
			}
			update_option('wooexim_taxonomy_data',"");		
		}
	}
	function wooexim_get_sku_by_id($ids = "")
	{
		if($ids != "")
		{
			$product_ids = @explode(',',$ids);
			
			if(is_array($product_ids) && !empty($product_ids))
			{
				$sku_list = array();
				
				foreach($product_ids as $product_id)
				{
					
					$current_sku = get_post_meta($product_id, '_sku',true);
					
					if(trim($current_sku)!="")
					{
						$sku_list[] = $current_sku;
					}
				} 
				
				return @implode(',',$sku_list);
			}
			else
			{
				return "";	
			}
		}
		else
		{
			return "";	
		}
	}
	function get_ids_by_sku($sku_list = "")
	{
		if($sku_list!="")
		{
			$sku_list_all = @explode(',',$sku_list);
			
			if(is_array($sku_list_all) && !empty($sku_list_all))
			{
				$existing_post_query = array(
					
						'posts_per_page' 	=> -1,
						
						'meta_query' => array(
							array(
								'key'=>'_sku',
								'value'=> $sku_list_all,
								'compare' => 'IN'
							),
							
						),
						
						'post_type' => array( 'product' ),
						
						'fields' 	=> 'ids',
						
						);
						
						
					$existing_product = get_posts($existing_post_query);
					
					if(!empty($existing_product))
					{
						return $existing_product;
					}
			}
		}
		
		return "";
	}
	function wooexim_get_categories_by_producy_id($product_id)
	{
		$categories_list = wp_get_post_terms( $product_id, 'product_cat' );
		
		$product_categories = array();
		
		$temp_count = 0;
		
		foreach($categories_list as $category)
		{
			$product_categories[$temp_count]['name'] = $category->name;
			
			$product_categories[$temp_count]['slug'] = $category->slug;
			
			$temp_count++;
		}
		
		return json_encode($product_categories);
	}
}
?>