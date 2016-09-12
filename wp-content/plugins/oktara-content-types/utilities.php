<?php
  //Custom excerpt and content lengths for queried posts
    function excerpt($limit) {
      $excerpt = explode(' ', get_the_excerpt(), $limit);
      if (count($excerpt)>=$limit) {
        array_pop($excerpt);
        $excerpt = implode(" ",$excerpt).'...';
      } else {
        $excerpt = implode(" ",$excerpt);
      }
        $excerpt = preg_replace('`[[^]]*]`','',$excerpt);
      return $excerpt;
    }

    function content($limit) {
      $content = explode(' ', get_the_content(), $limit);
      if (count($content)>=$limit) {
        array_pop($content);
        $content = implode(" ",$content).'...';
      } else {
        $content = implode(" ",$content);
      }
      $content = preg_replace('/[.+]/','', $content);
      $content = apply_filters('the_content', $content);
      $content = str_replace(']]>', ']]&gt;', $content);
      return $content;
    }

    //post views count
    function wpb_set_post_views($postID) {
      $count_key = 'wpb_post_views_count';
      $count = get_post_meta($postID, $count_key, true);
      if($count==''){
          $count = 0;
          delete_post_meta($postID, $count_key);
          add_post_meta($postID, $count_key, '0');
      }else{
          $count++;
          update_post_meta($postID, $count_key, $count);
      }
  }
  //To keep the count accurate, lets get rid of prefetching
  remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
  function wpb_track_post_views ($post_id) {
      if ( !is_single() ) return;
      if ( empty ( $post_id) ) {
          global $post;
          $post_id = $post->ID;    
      }
      wpb_set_post_views($post_id);
  }
  add_action( 'wp_head', 'wpb_track_post_views');
  //display post view count
  function wpb_get_post_views($postID){
    $count_key = 'wpb_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 Vistas";
    }
    return $count.' Vistas';
}

?>