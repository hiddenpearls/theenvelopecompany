<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}

final class TM_EPO_HELPER_base {

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	function __construct( $args = array() ) {		
	}

	public function convert_to_select_options($a=array()){
		$r=array();
		foreach ($a as $key => $value) {
			$r[]=array( "text" => $value, "value"=>$key );
		}
		return $r;
	}

	public function recreate_element_ids($meta=array()){
		$meta = $builder = maybe_unserialize($meta);
		$original_meta = false;
		$parsed_meta = false;
		$invalid = false;
		if ( isset( $meta["tmfbuilder"] ) ){
			$original_meta = true;
			$builder = $meta["tmfbuilder"];
		}else{
			if ( isset($meta['element_type']) ){
				$parsed_meta = true;
			}else{
				$invalid = true;
			}
		}
		
		if ($invalid){
			return $meta;
		}
		
		if (isset($builder)){
			$new_ids = array();
			$ids = TM_EPO_HELPER()->array_contains_key($builder,"_uniqid");
			$logics = TM_EPO_HELPER()->array_contains_key($builder,"_clogic");

			foreach ($ids as $idx => $idelement) {
				foreach ($idelement as $idy => $id) {
					$new_ids[$id] = TM_EPO_HELPER()->tm_uniqid();
				}
			}
			foreach ($ids as $idx => $idelement) {
				foreach ($idelement as $idy => $id) {
					$ids[$idx][$idy] = $new_ids[$id];
				}
			}

			foreach ($logics as $lx => $logicelement) {
				foreach ($logicelement as $ly => $logic) {
					$logic = str_replace(array_keys($new_ids), array_values($new_ids), $logic);
					$logics[$lx][$ly] = $logic;
				}
			}
			
			$builder = array_merge($builder, $ids);
			$builder = array_merge($builder, $logics);

			if ($original_meta){
				$meta["tmfbuilder"] = $builder;
			}else{
				$meta = $builder;
			}
		}
		return $meta;
	}

	public function html_entity_decode($string=""){
		return html_entity_decode($string, version_compare(phpversion(), '5.4', '<') ? ENT_COMPAT : (ENT_COMPAT | ENT_HTML401) ,'UTF-8');
	}

	/* Check if current request is made via AJAX */
	public function is_ajax_request() {
		if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
			return true;
		}
			
		return false;
	}

	public function sum_array_values($input=array()){
		$r=array();

		if (is_array($input)){
			foreach ($input as $key => $value) {
				if (is_array($value)){
					foreach ($value as $key2 => $value2) {
						if(!($key2=="min" || $key2=="max")){
							continue;
						}
						$a=0;
						if (isset($r[$key2])){
							$a=$r[$key2];
						}
						if ((!$value['section_logic'] && !$value['logic'] && $key2=="min") || $key2=="max"){
							$r[$key2] = floatval($value2) + $a;
						}
					}
				}
			}			
		}
		foreach ($r as $key => $value) {
			$r[$key]=apply_filters('wc_epo_get_current_currency_price',$value,'');
		}
		return $r;
	}

	public function add_array_values($input=array(),$add=array()){
		$r=array();

		if (is_array($input) && is_array($add)){
			foreach ($input as $key => $value) {
				$a=0;
				if (isset($add[$key])){
					$a=floatval($add[$key]);
				}

				$r[$key] = floatval($value) + $a;
			}
		}

		return $r;
	}

	public function merge_price_array($a=array(),$b=array()){
		if (!is_array($a) || !is_array($b)){
			return $a;
		}

		$r=array();
		
		foreach ($b as $key => $value) {
			if($value===''){
				$r[$key]=$a[$key];
			}else{
				$r[$key]=$value;
			}
		}
		return $r;		
	}

	public function build_array($a=array(),$b=array()){
		if (!is_array($a) || !is_array($b)){
			return $a;
		}

		$r=array();

		foreach ($b as $key => $value) {
			if (is_array($value)){
				if (isset($a[$key])){
					$r[$key]=$value;
				}else{
					$r[$key]=$this->build_array($a[$key],$b[$key]);	
				}				
			}else{
				if(isset($a[$key])){
					$r[$key]=$a[$key];
				}else{
					$r[$key]=$value;
				}
			}
		}
		return $r;
	}

	/**
	 * Filters an $input array by key.
	 */
	public function array_filter_key( $input ,$what="tmcp_",$where="start") {
		if ( !is_array( $input ) || empty( $input ) ) {
			return array();
		}

		$filtered_result=array();

		if ($where=="end"){
			$what=strrev($what);
		}

		foreach ( $input as $key => $value ) {
			$k=$key;
			if ($where=="end"){
				$k=strrev($key);
			}
			if ( strpos( $k, $what ) === 0 ) {
				$filtered_result[$key] = $value;
			}
		}

		return $filtered_result;
	}

	public function array_map_deep($array, $array2, $callback){
	    $new = array();
	    if( is_array($array) && is_array($array2)){
	    	foreach ($array as $key => $val) {
		        if (is_array($val) && is_array($array2[$key])) {
		            $new[$key] = $this->array_map_deep($val, $array2[$key], $callback);
		        } else {
		            $new[$key] = call_user_func($callback, $val, $array2[$key]);
		        }
		    }
	    }else{
	    	$new = call_user_func($callback, $array, $array2);
	    }
	    return $new;

	}

	/* Post URLs to IDs function, supports custom post types - borrowed and modified from url_to_postid() in wp-includes/rewrite.php */
	public function get_url_to_postid($url){
		global $wp_rewrite;

		$url = apply_filters('tm_url_to_postid', $url);

		// First, check to see if there is a 'p=N' or 'page_id=N' to match against
		if ( preg_match('#[?&](p|page_id|attachment_id)=(\d+)#', $url, $values) )	{
			$id = absint($values[2]);
			if ( $id )
				return $id;
		}

		// Check to see if we are using rewrite rules
		$rewrite = $wp_rewrite->wp_rewrite_rules();

		// Not using rewrite rules, and 'p=N' and 'page_id=N' methods failed, so we're out of options
		if ( empty($rewrite) )
			return 0;

		// Get rid of the #anchor
		$url_split = explode('#', $url);
		$url = $url_split[0];

		// Get rid of URL ?query=string
		$url_split = explode('?', $url);
		$url = $url_split[0];

		// Add 'www.' if it is absent and should be there
		if ( false !== strpos(home_url(), '://www.') && false === strpos($url, '://www.') )
			$url = str_replace('://', '://www.', $url);

		// Strip 'www.' if it is present and shouldn't be
		if ( false === strpos(home_url(), '://www.') )
			$url = str_replace('://www.', '://', $url);

		// Strip 'index.php/' if we're not using path info permalinks
		if ( !$wp_rewrite->using_index_permalinks() )
			$url = str_replace('index.php/', '', $url);

		if ( false !== strpos($url, home_url()) ) {
			// Chop off http://domain.com
			$url = str_replace(home_url(), '', $url);
		} else {
			// Chop off /path/to/blog
			$home_path = parse_url(home_url());
			$home_path = isset( $home_path['path'] ) ? $home_path['path'] : '' ;
			$url = str_replace($home_path, '', $url);
		}

		// Trim leading and lagging slashes
		$url = trim($url, '/');

		$request = $url;
		// Look for matches.
		$request_match = $request;
		foreach ( (array)$rewrite as $match => $query) {
			// If the requesting file is the anchor of the match, prepend it
			// to the path info.
			if ( !empty($url) && ($url != $request) && (strpos($match, $url) === 0) )
				$request_match = $url . '/' . $request;

			if ( preg_match("!^$match!", $request_match, $matches) ) {
				// Got a match.
				// Trim the query of everything up to the '?'.
				$query = preg_replace("!^.+\?!", '', $query);

				// Substitute the substring matches into the query.
				$query = addslashes(WP_MatchesMapRegex::apply($query, $matches));

				// Filter out non-public query vars
				global $wp;
				parse_str($query, $query_vars);
				$query = array();
				foreach ( (array) $query_vars as $key => $value ) {
					if ( in_array($key, $wp->public_query_vars) )
						$query[$key] = $value;
				}

			// Taken from class-wp.php
			foreach ( $GLOBALS['wp_post_types'] as $post_type => $t )
				if ( $t->query_var )
					$post_type_query_vars[$t->query_var] = $post_type;

			foreach ( $wp->public_query_vars as $wpvar ) {
				if ( isset( $wp->extra_query_vars[$wpvar] ) )
					$query[$wpvar] = $wp->extra_query_vars[$wpvar];
				elseif ( isset( $_POST[$wpvar] ) )
					$query[$wpvar] = $_POST[$wpvar];
				elseif ( isset( $_GET[$wpvar] ) )
					$query[$wpvar] = $_GET[$wpvar];
				elseif ( isset( $query_vars[$wpvar] ) )
					$query[$wpvar] = $query_vars[$wpvar];

				if ( !empty( $query[$wpvar] ) ) {
					if ( ! is_array( $query[$wpvar] ) ) {
						$query[$wpvar] = (string) $query[$wpvar];
					} else {
						foreach ( $query[$wpvar] as $vkey => $v ) {
							if ( !is_object( $v ) ) {
								$query[$wpvar][$vkey] = (string) $v;
							}
						}
					}

					if ( isset($post_type_query_vars[$wpvar] ) ) {
						$query['post_type'] = $post_type_query_vars[$wpvar];
						$query['name'] = $query[$wpvar];
					}
				}
			}

				// Do the query
				$query = new WP_Query($query);
				if ( !empty($query->posts) && $query->is_singular )
					return $query->post->ID;
				else
					return 0;
			}
		}
		return 0;
	}

	public function new_meta(){
		global $wp_version;
		return version_compare( $wp_version, '4.0.1', '>' );
	}

	public function build_meta_query($relation='OR',$meta_key='',$meta_value='', $compare='!=', $exists='NOT EXISTS'){
		$meta_array=array(
					'relation' => $relation,
					array(
						'key' => $meta_key, // get only enabled global extra options
						'value' => $meta_value,
						'compare' => $compare
					),
					array(
						'key' => $meta_key,// backwards compatibility
						'value' => $meta_value,
						'compare' => $exists
					)
					);
		if($this->new_meta()){
			$meta_array=array(
					'relation' => $relation,
					array(
						'key' => $meta_key, // get only enabled global extra options
						'value' => $meta_value,
						'compare' => $compare
					),
					array(
						'key' => $meta_key,// backwards compatibility
						'compare' => $exists
					)
					);

		}
		return $meta_array;
	}

	public function tm_uniqid($prefix=""){
		return uniqid($prefix,true);
	}

	public function tm_temp_uniqid($s){
		$a=array();
		for ( $m = 0; $m < $s; $m++ ) {
			$a[]=$this->tm_uniqid();
		}
		return $a;
	}

	public function encodeURIComponent($str) {
	    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
	    return strtr(rawurlencode($str), $revert);
	}

	public function reverse_strrchr($haystack, $needle, $trail=0) {
	    return strrpos($haystack, $needle) ? substr($haystack, 0, strrpos($haystack, $needle) + $trail) : false;
	}
	
	private function _count_posts_cache_key( $type = 'post', $perm = '' ) {
		$cache_key = 'tm-posts-' . $type;
		if ( 'readable' == $perm && is_user_logged_in() ) {
			$post_type_object = get_post_type_object( $type );
			if ( $post_type_object && ! current_user_can( $post_type_object->cap->read_private_posts ) ) {
				$cache_key .= '_' . $perm . '_' . get_current_user_id();
			}
		}
		return $cache_key;
	}
	
	public function wp_count_posts( $type = 'post', $perm = '' ) {
		global $wpdb;

		if ( ! post_type_exists( $type ) )
			return new stdClass;

		$cache_key = $this->_count_posts_cache_key( $type, $perm );

		// WPML
		$_lang=TM_EPO_WPML()->get_lang();
		if( TM_EPO_WPML()->is_active() && TM_EPO_WPML()->get_lang()!='all' && $_lang==TM_EPO_WPML()->get_default_lang() ){
			$query = "SELECT p.post_status, COUNT( DISTINCT ID ) AS num_posts FROM {$wpdb->posts} p";
		}else{
			$query = "SELECT p.post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} p";	
		}
		// WPML
		if( TM_EPO_WPML()->is_active() && TM_EPO_WPML()->get_lang()!='all' ){
			if ($_lang==TM_EPO_WPML()->get_default_lang()){
				$query 	.= 	" LEFT JOIN {$wpdb->postmeta} ON (p.ID = {$wpdb->postmeta}.post_id)"
						.	" LEFT JOIN {$wpdb->postmeta} AS mt1 ON (p.ID = mt1.post_id AND mt1.meta_key = '".TM_EPO_WPML_LANG_META."')";
			}else{
				$query .= " JOIN  {$wpdb->postmeta} pm";
			}
		}
		// WPML
		if( TM_EPO_WPML()->is_active() && TM_EPO_WPML()->get_lang()!='all' && $_lang==TM_EPO_WPML()->get_default_lang() ){
			$query .= " WHERE 1=1 AND p.post_type = %s";
		}else{
			$query .= " WHERE p.post_type = %s";	
		}
		
		if ( 'readable' == $perm && is_user_logged_in() ) {
			$post_type_object = get_post_type_object($type);
			if ( ! current_user_can( $post_type_object->cap->read_private_posts ) ) {
				$query .= $wpdb->prepare( " AND (p.post_status != 'private' OR ( p.post_author = %d AND p.post_status = 'private' ))",
					get_current_user_id()
				);
			}
		}

		// WPML
		if( TM_EPO_WPML()->is_active() && TM_EPO_WPML()->get_lang()!='all' ){			
			if ($_lang==TM_EPO_WPML()->get_default_lang()){
				$query .= " AND ( ( ".$wpdb->prefix."postmeta.meta_key = '".TM_EPO_WPML_LANG_META."' AND CAST(".$wpdb->prefix."postmeta.meta_value AS CHAR) = '".TM_EPO_WPML()->get_lang()."' ) OR mt1.post_id IS NULL ) ";
			}else{
				$query .= " AND p.ID=pm.post_id AND pm.meta_key = '".TM_EPO_WPML_LANG_META."' AND pm.meta_value = '".TM_EPO_WPML()->get_lang()."'";
			}
		}

		$query .= ' GROUP BY p.post_status';

		$counts = wp_cache_get( $cache_key, 'counts' );
		if ( false === $counts ) {
			$results = (array) $wpdb->get_results( $wpdb->prepare( $query, $type ), ARRAY_A );
			$counts = array_fill_keys( get_post_stati(), 0 );

			foreach ( $results as $row )
				$counts[ $row['post_status'] ] = $row['num_posts'];

			$counts = (object) $counts;
			wp_cache_set( $cache_key, $counts, 'counts' );
		}

		return apply_filters( 'wp_count_posts', $counts, $type, $perm );
	}

	public function wc_base_currency(){
		$from_currency = get_option('woocommerce_currency');
		return $from_currency;
	}

	public function get_currencies(){
		$enabled_currencies = apply_filters('wc_aelia_cs_enabled_currencies', array($this->wc_base_currency()));
		if (class_exists('WOOCS')){
			global $WOOCS;
			$currencies=$WOOCS->get_currencies();
			if ($currencies && is_array($currencies)){
				$enabled_currencies=array();
				foreach ($currencies as $key => $value) {
					$enabled_currencies[]=$value['name'];
				}
			}
		}elseif (class_exists('WooCommerce_All_in_One_Currency_Converter_Main')){
			global $woocommerce_all_in_one_currency_converter;            
            $currency_data = $woocommerce_all_in_one_currency_converter->settings->get_currency_data();
            if ($currency_data && is_array($currency_data)){
				$enabled_currencies=array();
				foreach ($currency_data as $key => $value) {
					$enabled_currencies[]=$key;
				}
			}
		}
		return $enabled_currencies;
	}

	public function wc_aelia_cs_enabled_currencies(){
		$enabled_currencies = $this->get_currencies();
		$from_currency = $this->wc_base_currency();
		foreach ($enabled_currencies as $key => $value) {
			if($value==$from_currency){
				unset($enabled_currencies[$key]);
				break;
			}
		}
		return $enabled_currencies;
	}

	public function wc_aelia_num_enabled_currencies(){
		$enabled_currencies = $this->wc_aelia_cs_enabled_currencies();
		if (is_array($enabled_currencies)){
			return count($enabled_currencies);
		}
		return 0;
	}

	public function get_currency_price_prefix($currency=NULL){
		if ($currency==NULL){
			if ($this->wc_aelia_num_enabled_currencies()>0){
				$to_currency = tc_get_woocommerce_currency();
				return "_".$to_currency;
			}else{
				return "";
			}			
		}else{
			return (empty($currency)||$currency==$this->wc_base_currency())?"":"_".$currency; 
		}
	}

	public function formatBytes($bytes, $precision = 2) { 
	    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

	    $bytes = max($bytes, 0); 
	    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
	    $pow = min($pow, count($units) - 1); 

	    // Uncomment one of the following alternatives
	     $bytes /= pow(1024, $pow);
	    // $bytes /= (1 << (10 * $pow)); 

	    return round($bytes, $precision) . ' ' . $units[$pow]; 
	} 

	public function convert_to_right_icon($label='',$icon='tcfa-angle-right'){
		$label = str_replace("/", "", $label);
		$label .= '<i class="tm-icon tmfa tcfa '.$icon.'"></i>';
		return $label;
	}

	public function url_to_links($url='',$main_path='',$main_path_label=''){

		$param = str_replace($main_path, "", $url);
		$param = explode("/", $param);

		$html = '';
		
		$a = '<a class="tm-mn-movetodir" data-tm-dir="" href="'.esc_attr($main_path).'">'.$this->convert_to_right_icon(esc_html($main_path_label)).'</a>';
		$html .= $a;
		$todir='';
		foreach ($param as $key => $value) {
			if ($key==count($param)-1){
				$a = '<span class="tm-mn-currentdir">'.esc_html($value).'</span>';
			}else{
				$data_tm_dir = (empty($todir))?$value:$todir."/".$value;
				$a = '<a class="tm-mn-movetodir" data-tm-dir="'.esc_attr($data_tm_dir).'" href="'.esc_attr($main_path.$data_tm_dir).'">'.$this->convert_to_right_icon(esc_html($value."/")).'</a>';
				$todir=$data_tm_dir;				
			}
			$html .= $a;
		}
		
		
		return $html;//$main_path_label.$param;
	}

	public function init_filesystem(){
		if (function_exists('get_filesystem_method')){
			$access_type = get_filesystem_method();
			if($access_type === 'direct'){
				/* you can safely run request_filesystem_credentials() without any issues and don't need to worry about passing in a URL */
				$creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, array());

				/* initialize the API */
				if ( ! WP_Filesystem($creds) ) {
					/* any problems and we exit */
					return '';
				}
				return true;
			}			
		}
		return false;	
	}

	public function file_rmdir($file=''){
		if($this->init_filesystem()){
			global $wp_filesystem;
			$mn=$wp_filesystem->rmdir($file,true);
			clearstatcache();
			return $mn;
		}
		return false;
	}

	public function file_delete($file=''){
		if($this->init_filesystem()){
			global $wp_filesystem;
			$mn=$wp_filesystem->delete($file);
			clearstatcache();
			return $mn;
		}
		return false;
	}

	public function file_manager($main_path='',$todir=''){
		$html="";
		
		if($this->init_filesystem()){

			global $wp_filesystem;

			$subdir=$main_path.$todir;
			$param = wp_upload_dir();
			if ( empty( $param['subdir'] ) ) {
				$param['path']   	= $param['path'] . $subdir;
				$param['url']    	= $param['url']. $subdir;
				$param['subdir'] 	= $subdir;
				$base_url 			= $param['url'].$main_path;
			} else {
				$param['path']   	= str_replace( $param['subdir'], $subdir, $param['path'] );
				$param['url']    	= str_replace( $param['subdir'], $subdir, $param['url'] );
				$param['subdir'] 	= str_replace( $param['subdir'], $subdir, $param['subdir'] );
				$base_url 			= str_replace( $param['subdir'], $main_path, $param['url'] );
			}

			clearstatcache();
			$mn=$wp_filesystem->dirlist($param['path'], true, false);
			
			$files=array();
			$directories=array();
			if($mn){
				foreach ($mn as $key => $value) {
					if (isset($value["type"]) && isset($value["name"]) && isset($value["size"] )){
						switch (strtolower($value["type"])) {
							case 'd':
								$directories[]=array("name"=>$value["name"],"size"=>0);
								break;
								
							case 'f':
								$files[]=array("name"=>$value["name"],"size"=>$value["size"]);
								break;
						}
					}
				}
			}

			$html .='<div class="tm-mn-header"><div class="tm-mn-path">'.$this->url_to_links($param['url'],$base_url,$main_path).'</div></div>';
			$html .='<div class="tm-mn-wrap-heading tm-row nopadding nomargin">';
				$html .='<div class="tm-mn-name tm-cell col-6">'.__( 'Filename', 'woocommerce-tm-extra-product-options' ).'</div>';
				$html .='<div class="tm-mn-size tm-cell col-3">'.__( 'Size', 'woocommerce-tm-extra-product-options' ).'</div>';
				$html .='<div class="tm-mn-op tm-cell col-3">&nbsp;</div>';
			$html .='</div>';
			foreach ($directories as $key => $value) {
				$filetype = wp_check_filetype($value["name"]);
				$img = '<img class="tm-mime" src="'.esc_attr(wp_mime_type_icon($filetype['type'])).'" /> ';
				$html .='<div class="tm-mn-wrap-dir tm-row nopadding nomargin">';
					$data_tm_dir=(empty($todir))?$value["name"]:$todir."/".$value["name"];
					$html .='<div class="tm-mn-name tm-cell col-6">'.$img.'<a class="tm-mn-movetodir" data-tm-dir="'.esc_attr($data_tm_dir).'" href="'.esc_attr($param['url'].$value["name"]).'">'.esc_html($value["name"]).'</a></div>';
					$html .='<div class="tm-mn-size tm-cell col-3">&nbsp;</div>';
					$html .='<div class="tm-mn-op tm-cell col-3">'
					.'<a title="'.__( 'Delete', 'woocommerce-tm-extra-product-options' ).'" href="#" data-tm-dir="'.esc_attr($todir).'" data-tm-deldir="'.esc_attr($data_tm_dir).'" class="tm-mn-deldir"><i class="tm-icon tmfa tcfa tcfa-times"></i></a>'
					.'</div>';
				$html .='</div>';
			}
			foreach ($files as $key => $value) {
				$filetype = wp_check_filetype($value["name"]);
				$img = '<img class="tm-mime" src="'.esc_attr(wp_mime_type_icon($filetype['type'])).'" /> ';			

				$html .='<div class="tm-mn-wrap-file tm-row nopadding nomargin">';
					$data_tm_dir=$todir;
					$html .='<div class="tm-mn-name tm-cell col-6">'.$img.esc_html($value["name"]).'</div>';
					$html .='<div class="tm-mn-size tm-cell col-3">'.$this->formatBytes($value["size"],2).'</div>';
					$html .='<div class="tm-mn-op tm-cell col-3">'
					.'<a title="'.__( 'Delete', 'woocommerce-tm-extra-product-options' ).'" href="#" data-tm-dir="'.esc_attr($todir).'" data-tm-deldir="'.esc_attr($data_tm_dir).'" data-tm-delfile="'.esc_attr($value["name"]).'" class="tm-mn-delfile"><i class="tm-icon tmfa tcfa tcfa-times"></i></a>'
					.'</div>';
				$html .='</div>';
			}

		}
		return $html;
	}

	public function get_saved_order_multiple_keys($current_product_id=0){
		$this_land_epos=TM_EPO()->get_product_tm_epos( $current_product_id );
		$saved_order_multiple_keys=array();
		if (isset($this_land_epos['global']) && is_array($this_land_epos['global'])){
			foreach ( $this_land_epos['global'] as $priority=>$priorities ) {
				if (is_array($priorities)){
					foreach ( $priorities as $pid=>$field ) {
						if (isset($field['sections']) && is_array($field['sections'])){
							foreach ( $field['sections'] as $section_id=>$section ) {
								if(isset($section['elements']) && is_array($section['elements'])){
									foreach ( $section['elements'] as $element ) {
										$saved_order_multiple_keys[$element['uniqid']]=$element['label'];
										$saved_order_multiple_keys["options_".$element['uniqid']]=$element['options'];
									}								
								}
							}
						}
					}					
				}
			}
		}
		return $saved_order_multiple_keys;
	}

	public function tm_get_order_object(){
		global $thepostid, $theorder;

		if ( ! is_object( $theorder ) ) {
			$theorder = wc_get_order( $thepostid );
		}
		if (!$theorder && isset($_POST['order_id'])){
			$order_id = absint( $_POST['order_id'] );
			$order    = wc_get_order( $order_id );
			return $order;
		}elseif (!$theorder && isset($_POST['post_ID'])){
			$order_id = absint( $_POST['post_ID'] );
			$order    = wc_get_order( $order_id );
			return $order;
		}
		if (!$theorder){
			global $post;
			if ( $post ) {
		        $theorder = wc_get_order( $post->ID );
		    }
		}
		return $theorder;
	}

	public function upload_to_png($source, $target){

	   $sourceImg = @imagecreatefromstring(file_get_contents($source));
	   
	   if ($sourceImg === false){
	      return false;//Invalid image
	   }
	   $width = imagesx($sourceImg);
	   $height = imagesy($sourceImg);
	   $targetImg = imagecreatetruecolor($width, $height);
	   imagecopy($targetImg, $sourceImg, 0, 0, 0, 0, $width, $height);
	   imagedestroy($sourceImg);
	   imagepng($targetImg, $target);
	   imagedestroy($targetImg);
	   return true;
	}

	public function str_startswith($source, $prefix){
       return strncmp($source, $prefix, strlen($prefix)) == 0;
    }

    public function str_endsswith($source, $prefix){
       return $prefix === '' || (strlen($prefix) <= strlen($source) && substr_compare($source, $prefix, -strlen($prefix)) === 0);
    }

    /**
	 * Search through an array for a matching key.
	 *
	 * https://gist.github.com/steve-todorov/3671626
	 *
	 * @param array  $input_array
	 * @param string $search_value
	 * @param bool   $case_sensitive
	 *
	 * @return array
	 */
	public function array_contains_key( array $input_array, $search_value, $case_sensitive = true){
	    if($case_sensitive){
	        $preg_match = '/'.$search_value.'/';
	    }else{
	        $preg_match = '/'.$search_value.'/i';
	    }
	    $return_array = array();
	    $keys = array_keys( $input_array );
	    foreach ( $keys as $k ) {
	        if ( preg_match($preg_match, $k) ){
	            $return_array[$k] = $input_array[$k];
	        }
	    }
	    return $return_array;
	}

}


?>