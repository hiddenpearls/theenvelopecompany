<?php
/**
* Instagram Widget Setup
* @since 1.9.2
*/
if(!function_exists('tt_instagram_data')){
	/**
	* Get data from instagram by scrapping user's feed
	* @since 1.9.2
	* @return array Image Data & User Stats
	*/
	function tt_instagram_data( $username, $cache_hours, $nr_images ) {
		
		$opt_name		= 'tt_insta_' . md5( $username . $nr_images );
		$instaData		= get_transient( $opt_name );
		$instaCounts	= get_transient( $opt_name . '_counts');
		
		if ( false === $instaData || false === $instaCounts) {
			$instaData		= array();
			$instaCounts	= array();
			$insta_url		= 'http://instagram.com/';
			$user_profile 	= $insta_url.$username;
			$json			= wp_remote_get( $user_profile, array( 'sslverify' => false, 'timeout'=> 60 ) );
			$user_options 	= compact('username', 'cache_hours', 'nr_images');
			update_option($opt_name, $user_options);

			if ( $json['response']['code'] == 200 ) {

				$json	= $json['body'];
				$json	= strstr( $json, '"static_root"' );
				
				// Compatibility for version of php where strstr() doesnt accept third parameter
				if ( version_compare( phpversion(), '5.3.10', '<' ) ) {
					$json = substr( $json, 0, strpos($json, '</script>' ) );
				} else {
					$json = strstr( $json, '</script>', true );
				}
				
				$json	= rtrim( $json, ';' );
				//fixing json
				$json	= ltrim( $json, '{' );
				$json 	= "{" . $json;
				
				( $results = json_decode( $json, true ) ) && json_last_error() == JSON_ERROR_NONE;
				if ( ( $results ) && is_array( $results ) ) {

					$instaCounts['followers'] = $results['entry_data']['ProfilePage'][0]['user']['followed_by']['count'];
					$instaCounts['pictures']	= $results['entry_data']['ProfilePage'][0]['user']['media']['count'];
					$instaCounts['following'] = $results['entry_data']['ProfilePage'][0]['user']['follows']['count'];

					if(!empty($results['entry_data']['ProfilePage'][0]['user']['media']['nodes'])){
						foreach( $results['entry_data']['ProfilePage'][0]['user']['media']['nodes'] as $current => $result ) {
							if( $current >= $nr_images ) 
								break;
							
							array_push( $instaData, array(
								'id'			=> $result['id'],
								'user_name'		=> $username,
								'user_url'		=> $user_profile,
								'created_time'	=> $result['date'],
								'caption'		=> !empty($result['caption']) ? $result['caption'] : '',
								'image'			=> $result['display_src'],
								'thumb'			=> $result['thumbnail_src'],
								'link'			=> 'https://instagram.com/p/' . $result['code'] . '/',
								'comments_count'=> $result['comments']['count'],
								'likes_count'	=> $result['likes']['count'],
								'width'			=> $result['dimensions']['width'],
								'height'		=> $result['dimensions']['height'],
							));
						
						} // end -> foreach

					} //end -> if has images
				
				} // end -> ( $results ) && is_array( $results ) )
			
			} // end -> $json['response']['code'] === 200 )
			
			if ( $instaData && !empty($cache_hours)) {
				set_transient( $opt_name, $instaData, $cache_hours * HOUR_IN_SECONDS );
			} // end -> true $instaData

			if ( $instaCounts && !empty($cache_hours)) {
				set_transient( $opt_name . '_counts', $instaCounts, $cache_hours * HOUR_IN_SECONDS );
			} // end -> true $instaData
		
		} // end -> false === $instaData
		
		return array($instaData,$instaCounts);
	}

} // end !function_exist

if(!function_exists('tt_instagram_generate_output')){
	/**
	* Generate instagram output
	* @since 1.9.2
	*/
	function tt_instagram_generate_output( $username, $cache_hours, $nr_images , $thumbs = true, $callback = '' ){
		if(empty($username)){
			return __("No username inserted in instagram widget","TeslaFramework");
		}
		$images = tt_instagram_data( $username, $cache_hours, $nr_images );
		if(!empty($images)) : 
			if(!empty($callback))
				call_user_func( $callback , $images , $thumbs);
			$output = '<ul class="tt-instagram-feed">';
				foreach ($images[0] as $key => $image) :
					$image_src = $thumbs ? $image['thumb'] : $image['image'];
					$output .= '<li>';
						$output .= '<a target="_blank" href="' . esc_url($image['link']) . '">';
							$output .= '<img class="tt-instagram-img" src="' . esc_attr($image_src) . '" width="' . esc_attr( $image['width'] ) . '" height="' . esc_attr( $image['height'] ) . '" alt="' . esc_attr( $image['caption'] ) . '"/>';
							$output .= '<span class="tt-instagram-caption">' . esc_html($image['caption']) . '</span>';
							$output .= '<span class="tt-instagram-likes">' . esc_html($image['likes_count']) . '</span>';
						$output .= '</a>';
					$output .= '</li>';
				endforeach;
			$output .= '</ul>';
		endif;
		return $output;
	}
}