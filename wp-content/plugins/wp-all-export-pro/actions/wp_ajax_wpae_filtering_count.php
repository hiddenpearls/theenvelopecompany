<?php

function pmxe_wp_ajax_wpae_filtering_count(){

	if ( ! check_ajax_referer( 'wp_all_export_secure', 'security', false )){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

	if ( ! current_user_can( PMXE_Plugin::$capabilities ) ){
		exit( json_encode(array('html' => __('Security check', 'wp_all_export_plugin'))) );
	}

	ob_start();

	$hasVariations = false;

	$input = new PMXE_Input();
	
	$post = $input->post('data', array());	

	$filter_args = array(
		'filter_rules_hierarhy' => empty($post['filter_rules_hierarhy']) ? array() : $post['filter_rules_hierarhy'],
		'product_matching_mode' => empty($post['product_matching_mode']) ? 'strict' : $post['product_matching_mode'],
		'taxonomy_to_export' => empty($post['taxonomy_to_export']) ? '' : $post['taxonomy_to_export']
	);

	$input  = new PMXE_Input();	
	$export_id = $input->get('id', 0);
	if (empty($export_id))
	{
		$export_id = ( ! empty(PMXE_Plugin::$session->update_previous)) ? PMXE_Plugin::$session->update_previous : 0;		
	} 	

	$export = new PMXE_Export_Record();
	$export->getById($export_id);		
	if ( ! $export->isEmpty() )
	{
		XmlExportEngine::$exportOptions  = $export->options + PMXE_Plugin::get_default_import_options();	
		XmlExportEngine::$exportOptions['export_only_new_stuff'] = $post['export_only_new_stuff'];
	}
	
	XmlExportEngine::$is_user_export = ( 'users' == $post['cpt'] or 'shop_customer' == $post['cpt'] ) ? true : false;
	XmlExportEngine::$is_comment_export = ( 'comments' == $post['cpt'] ) ? true : false;
	XmlExportEngine::$is_taxonomy_export = ( 'taxonomies' == $post['cpt'] ) ? true : false;
	XmlExportEngine::$post_types = array($post['cpt']);
	XmlExportEngine::$exportOptions['export_variations'] = empty($post['export_variations']) ? XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_VARIATION : $post['export_variations'];

	$filters = new XmlExportFiltering($filter_args);

	$filters->parseQuery();
				
	PMXE_Plugin::$session->set('whereclause', $filters->get('queryWhere'));
	PMXE_Plugin::$session->set('joinclause',  $filters->get('queryJoin'));
	PMXE_Plugin::$session->save_data();		

	$found_records = 0;
	$total_records = 0;

	$cpt = array($post['cpt']);

	$is_products_export = ($post['cpt'] == 'product' and class_exists('WooCommerce'));

	if ($post['export_type'] == 'advanced') 
	{
		if (XmlExportEngine::$is_user_export)
		{			
			// get total users
			$totalQuery = eval('return new WP_User_Query(array(' . PMXE_Plugin::$session->get('wp_query') . ', \'offset\' => 0, \'number\' => 10 ));');
			if ( ! empty($totalQuery->results)){
				$total_records = $totalQuery->get_total();			
			}

			ob_start();
			// get users depends on filters
			add_action('pre_user_query', 'wp_all_export_pre_user_query', 10, 1);
			$exportQuery = eval('return new WP_User_Query(array(' . PMXE_Plugin::$session->get('wp_query') . ', \'offset\' => 0, \'number\' => 10 ));');
			if ( ! empty($exportQuery->results)){
				$found_records = $exportQuery->get_total();			
			}
			remove_action('pre_user_query', 'wp_all_export_pre_user_query');			
			ob_get_clean();
		}
		elseif(XmlExportEngine::$is_comment_export)
		{			
			// get total comments
			$totalQuery = eval('return new WP_Comment_Query(array(' . PMXE_Plugin::$session->get('wp_query') . ', \'number\' => 10, \'count\' => true ));');
			$total_records = $totalQuery->get_comments();

			ob_start();
			// get comments depends on filters
			add_action('comments_clauses', 'wp_all_export_comments_clauses', 10, 1);								
			$exportQuery = eval('return new WP_Comment_Query(array(' . PMXE_Plugin::$session->get('wp_query') . '));');
			$found_records = $exportQuery->get_comments();
			remove_action('comments_clauses', 'wp_all_export_comments_clauses');			
			ob_get_clean();
		}
		else
		{			
			remove_all_actions('parse_query');
			remove_all_actions('pre_get_posts');
			remove_all_filters('posts_clauses');			
			
			// get total custom post type records
			$totalQuery = eval('return new WP_Query(array(' . PMXE_Plugin::$session->get('wp_query') . ', \'offset\' => 0, \'posts_per_page\' => 10 ));');						
			if ( ! empty($totalQuery->found_posts)){
				$total_records = $totalQuery->found_posts;			
			}

			wp_reset_postdata();

			ob_start();
			// get custom post type records depends on filters
			add_filter('posts_where', 'wp_all_export_posts_where', 10, 1);
			add_filter('posts_join', 'wp_all_export_posts_join', 10, 1);							
			
			$exportQuery = eval('return new WP_Query(array(' . PMXE_Plugin::$session->get('wp_query') . ', \'offset\' => 0, \'posts_per_page\' => 10 ));');							
			if ( ! empty($exportQuery->found_posts)){				
				$found_records = $exportQuery->found_posts;								
			}
			remove_filter('posts_join', 'wp_all_export_posts_join');			
			remove_filter('posts_where', 'wp_all_export_posts_where');
			ob_get_clean();					
		}
	}
	else
	{
		if ( 'users' == $post['cpt'] or 'shop_customer' == $post['cpt'] )
		{			
			// get total users
			$totalQuery = new WP_User_Query( array( 'orderby' => 'ID', 'order' => 'ASC', 'number' => 10 ));		
			if ( ! empty($totalQuery->results)){
				$total_records = $totalQuery->get_total();			
			}
			
			ob_start();
			// get users depends on filters
			add_action('pre_user_query', 'wp_all_export_pre_user_query', 10, 1);
			$exportQuery = new WP_User_Query( array( 'orderby' => 'ID', 'order' => 'ASC', 'number' => 10 ));		
			if ( ! empty($exportQuery->results)){
				$found_records = $exportQuery->get_total();			
			}
			remove_action('pre_user_query', 'wp_all_export_pre_user_query');			
			ob_get_clean();
		}
		elseif( 'comments' == $post['cpt'] )
		{
			// get total comments
			global $wp_version;	

			if ( version_compare($wp_version, '4.2.0', '>=') ) 
			{
				$totalQuery = new WP_Comment_Query( array( 'orderby' => 'comment_ID', 'order' => 'ASC', 'number' => 10, 'count' => true));
				$total_records = $totalQuery->get_comments();
			}
			else
			{
				$total_records = get_comments( array( 'orderby' => 'comment_ID', 'order' => 'ASC', 'number' => 10, 'count' => true));
			}

			ob_start();
			// get comments depends on filters
			add_action('comments_clauses', 'wp_all_export_comments_clauses', 10, 1);			
					
			if ( version_compare($wp_version, '4.2.0', '>=') ) 
			{
				$exportQuery = new WP_Comment_Query( array( 'orderby' => 'comment_ID', 'order' => 'ASC'));
				$found_records = count($exportQuery->get_comments());
			}
			else
			{
				$found_records = count(get_comments( array( 'orderby' => 'comment_ID', 'order' => 'ASC')));
			}
			remove_action('comments_clauses', 'wp_all_export_comments_clauses');			
			ob_get_clean();
		}
		elseif( 'taxonomies' == $post['cpt'] )
		{
			global $wp_version;

			if ( version_compare($wp_version, '4.6.0', '>=') ) {
				$totalQuery = new WP_Term_Query(array(
					'taxonomy' => $post['taxonomy_to_export'],
					'orderby' => 'name',
					'order' => 'ASC',
					'number' => 10,
					'hide_empty' => FALSE
				));
				$total_records = count($totalQuery->get_terms());

				ob_start();
				// get comments depends on filters
				add_filter('terms_clauses', 'wp_all_export_terms_clauses', 10, 3);
				$exportQuery = new WP_Term_Query(array(
					'taxonomy' => $post['taxonomy_to_export'],
					'orderby' => 'name',
					'order' => 'ASC',
					'hide_empty' => FALSE
				));
				$found_records = count($exportQuery->get_terms());
				remove_filter('terms_clauses', 'wp_all_export_terms_clauses');
				ob_get_clean();
			}
			else{
				?>
				<div class="founded_records">
					<h3><?php _e('Unable to Export', 'wp_all_export_plugin'); ?></h3>
					<h4><?php printf(__("Exporting taxonomies requires WordPress 4.6 or greater", "wp_all_export_plugin"), wp_all_export_get_cpt_name($cpt, 2, $post)); ?></h4>
				</div>
				<?php
				exit(json_encode(array('html' => ob_get_clean(), 'found_records' => 0, 'hasVariations' => $hasVariations))); die;
			}

		}
		else
		{									
			remove_all_actions('parse_query');
			remove_all_actions('pre_get_posts');			
			remove_all_filters('posts_clauses');			

			$cpt = ($is_products_export) ? array('product', 'product_variation') : array($post['cpt']);

			// get total custom post type records
			$totalQuery = new WP_Query( array( 'post_type' => $cpt, 'post_status' => 'any', 'orderby' => 'ID', 'order' => 'ASC', 'posts_per_page' => 10 ));
			if ( ! empty($totalQuery->found_posts)){
				$total_records = $totalQuery->found_posts;			
			}

			wp_reset_postdata();

			ob_start();
			// get custom post type records depends on filters			
			add_filter('posts_where', 'wp_all_export_posts_where', 10, 1);
			add_filter('posts_join', 'wp_all_export_posts_join', 10, 1);										
				
			$exportQuery = new WP_Query( array( 'post_type' => $cpt, 'post_status' => 'any', 'orderby' => 'ID', 'order' => 'ASC', 'posts_per_page' => 10 ));
			if ( ! empty($exportQuery->found_posts)) 
			{
				$found_records = $exportQuery->found_posts;	
			}

			if($is_products_export) {
				foreach($exportQuery->posts as $product) {
					if($product->post_type = 'product_variation') {
						$hasVariations = true;
					}
				}
			}

			remove_filter('posts_join', 'wp_all_export_posts_join');			
			remove_filter('posts_where', 'wp_all_export_posts_where');			
			ob_end_clean();

		}
	}

	PMXE_Plugin::$session->set('exportQuery', $exportQuery);
	PMXE_Plugin::$session->save_data();

	if ( $post['is_confirm_screen'] )
	{
		?>
				
		<?php if ($found_records > 0) :?>
			<h3><?php _e('Your export is ready to run.', 'wp_all_export_plugin'); ?></h3>							
			<h4><?php printf(__('WP All Export will export %d %s.', 'wp_all_export_plugin'), $found_records, wp_all_export_get_cpt_name($cpt, $found_records, $post)); ?></h4>
		<?php else: ?>
			<?php if (! $export->isEmpty() and $export->options['export_only_new_stuff']): ?>
				<h3><?php _e('Nothing to export.', 'wp_all_export_plugin'); ?></h3>
				<h4><?php printf(__("All %s have already been exported.", "wp_all_export_plugin"), wp_all_export_get_cpt_name($cpt, 2, $post)); ?></h4>
			<?php elseif ($total_records > 0): ?>
				<h3><?php _e('Nothing to export.', 'wp_all_export_plugin'); ?></h3>
				<h4><?php printf(__("No matching %s found for selected filter rules.", "wp_all_export_plugin"), wp_all_export_get_cpt_name($cpt, 2, $post)); ?></h4>
			<?php else: ?>
				<h3><?php _e('Nothing to export.', 'wp_all_export_plugin'); ?></h3>
				<h4><?php printf(__("There aren't any %s to export.", "wp_all_export_plugin"), wp_all_export_get_cpt_name($cpt, 2, $post)); ?></h4>
			<?php endif; ?>
		<?php endif; ?>

		<?php	
	}
	elseif( $post['is_template_screen'] )
	{
		?>
				
		<?php if ($found_records > 0) :?>
			<h3><span class="matches_count"><?php echo $found_records; ?></span> <strong><?php echo wp_all_export_get_cpt_name($cpt, $found_records, $post); ?></strong> will be exported</h3>
			<h4><?php _e("Choose data to include in the export file.", "wp_all_export_plugin"); ?></h4>
		<?php else: ?>
			<?php if (! $export->isEmpty() and $export->options['export_only_new_stuff']): ?>
				<h3><?php _e('Nothing to export.', 'wp_all_export_plugin'); ?></h3>
				<h4><?php printf(__("All %s have already been exported.", "wp_all_export_plugin"), wp_all_export_get_cpt_name($cpt, 2, $post)); ?></h4>
			<?php elseif ($total_records > 0): ?>
				<h3><?php _e('Nothing to export.', 'wp_all_export_plugin'); ?></h3>
				<h4><?php printf(__("No matching %s found for selected filter rules.", "wp_all_export_plugin"), wp_all_export_get_cpt_name($cpt, 2, $post)); ?></h4>
			<?php else: ?>
				<h3><?php _e('Nothing to export.', 'wp_all_export_plugin'); ?></h3>
				<h4><?php printf(__("There aren't any %s to export.", "wp_all_export_plugin"), wp_all_export_get_cpt_name($cpt, 2, $post)); ?></h4>
			<?php endif; ?>
		<?php endif; ?>

		<?php	
	}
	else
	{
		?>
		<div class="founded_records">			
			<?php if ($found_records > 0) :?>
				<h3><span class="matches_count"><?php echo $found_records; ?></span> <strong><?php echo wp_all_export_get_cpt_name($cpt, $found_records, $post); ?></strong> will be exported</h3>
				<h4><?php _e("Continue to configure and run your export.", "wp_all_export_plugin"); ?></h4>
			<?php elseif ($total_records > 0): ?>
				<h4 style="line-height:60px;"><?php printf(__("No matching %s found for selected filter rules.", "wp_all_export_plugin"), wp_all_export_get_cpt_name($cpt, 2, $post)); ?></h4>
			<?php else: ?>
				<h4 style="line-height:60px;"><?php printf(__("There aren't any %s to export.", "wp_all_export_plugin"), wp_all_export_get_cpt_name($cpt, 2, $post)); ?></h4>
			<?php endif; ?>
		</div>
		<?php	
	}	
	
	exit(json_encode(array('html' => ob_get_clean(), 'found_records' => $found_records, 'hasVariations' => $hasVariations))); die;

}