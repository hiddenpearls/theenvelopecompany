<?php

/**
*  Does shortcode of post type client.
*  Filters by number of posts per page.
* @return <html>
*/
function pull_clients_shortcode( $atts ) {

    $pull_posts_atts = shortcode_atts( array(
        'type' => 'client',
        'posts_per_page' => array('posts_per_page'),
    ), $atts );
        
    $options = array(
        'post_type'         =>  $pull_posts_atts[ 'type' ],
        'posts_per_page'    =>  $pull_posts_atts[ 'posts_per_page' ],
        'meta_query' => array(
            array(
                'key' => '_thumbnail_id',
                'compare' => 'EXISTS'
            ),
        )

    );
    //create the query based on the options set   
    $news_query = new WP_Query( $options );
    if ( $news_query->have_posts() ) { 
        $output .= '';
        while( $news_query->have_posts() ){
            //insert the post into the query and store it in output
            $news_query->the_post();
             //output html             
            $output .= '<div class="logo-outer"><div class="logo-container">';
            $url = wp_get_attachment_url( get_post_thumbnail_id() );
            $output .= '<img src="'.$url.'" class="img-responsive" alt="">';
            $output .= '</div></div>';
        }//while 
        //reset post data in case there are other custom queries running along.
        wp_reset_postdata();      
        } else {
            //$output .= 'Nothing found bro';
        }
    return $output;
}
add_shortcode( 'list-clients', 'pull_clients_shortcode' );
?>