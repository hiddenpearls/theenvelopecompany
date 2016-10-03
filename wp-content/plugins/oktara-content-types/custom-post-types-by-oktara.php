<?php
    //register custom post type samples
    function register_samples() {
        $labels = array(
            'name'               => _x( 'Samples', 'post type general name' ),
            'singular_name'      => _x( 'Sample', 'post type singular name' ),
            'add_new'            => _x( 'Add New', 'sample' ),
            'add_new_item'       => __( 'Add New ' ),
            'edit_item'          => __( 'Edit Sample' ),
            'new_item'           => __( 'New Sample' ),
            'all_items'          => __( 'All' ),
            'view_item'          => __( 'See Sample' ),
            'search_items'       => __( 'Search Sample' ),
            'not_found'          => __( 'No posts found' ),
            'not_found_in_trash' => __( 'No posts found in the Trash' ), 
            'parent_item_colon'  => '',
            'menu_name'          => 'Samples'
        );
        $args = array(
            'labels'        => $labels,
            'description'   => 'Samples',
            'public'        => true,
            'menu_position' => 8,
            'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
            'taxonomies'    => array(''),
            'has_archive'   => false,
        );
        register_post_type( 'sample', $args ); 
    }
    add_action( 'init', 'register_samples' );
   
?>