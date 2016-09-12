<?php
    //register custom post type clients
    function register_clients() {
        $labels = array(
            'name'               => _x( 'Clients', 'post type general name' ),
            'singular_name'      => _x( 'Client', 'post type singular name' ),
            'add_new'            => _x( 'Add New', 'client' ),
            'add_new_item'       => __( 'Add New ' ),
            'edit_item'          => __( 'Edit Client' ),
            'new_item'           => __( 'New Client' ),
            'all_items'          => __( 'All' ),
            'view_item'          => __( 'See Client' ),
            'search_items'       => __( 'Search Client' ),
            'not_found'          => __( 'No posts found' ),
            'not_found_in_trash' => __( 'No posts found in the Trash' ), 
            'parent_item_colon'  => '',
            'menu_name'          => 'Clients'
        );
        $args = array(
            'labels'        => $labels,
            'description'   => 'LaneTerravelers clients',
            'public'        => true,
            'menu_position' => 8,
            'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
            'taxonomies'    => array(''),
            'has_archive'   => false,
        );
        register_post_type( 'client', $args ); 
    }
    add_action( 'init', 'register_clients' );
   
   //register custom post type bios
    function register_bios() {
        $labels = array(
            'name'               => _x( 'Bios', 'post type general name' ),
            'singular_name'      => _x( 'Bio', 'post type singular name' ),
            'add_new'            => _x( 'Add New', 'bio' ),
            'add_new_item'       => __( 'Add New ' ),
            'edit_item'          => __( 'Edit Bio' ),
            'new_item'           => __( 'New Bio' ),
            'all_items'          => __( 'All' ),
            'view_item'          => __( 'See Bio' ),
            'search_items'       => __( 'Search Bio' ),
            'not_found'          => __( 'No posts found' ),
            'not_found_in_trash' => __( 'No posts found in the Trash' ), 
            'parent_item_colon'  => '',
            'menu_name'          => 'Bios'
        );
        $args = array(
            'labels'        => $labels,
            'description'   => 'LaneTerravelers employees',
            'public'        => true,
            'menu_position' => 8,
            'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
            'taxonomies'    => array(''),
            'has_archive'   => false,
        );
        register_post_type( 'bio', $args ); 
    }
    add_action( 'init', 'register_bios' );

    //register custom post type careers
    function register_careers() {
        $labels = array(
            'name'               => _x( 'careers', 'post type general name' ),
            'singular_name'      => _x( 'career', 'post type singular name' ),
            'add_new'            => _x( 'Add New', 'career' ),
            'add_new_item'       => __( 'Add New ' ),
            'edit_item'          => __( 'Edit career' ),
            'new_item'           => __( 'New career' ),
            'all_items'          => __( 'All' ),
            'view_item'          => __( 'See career' ),
            'search_items'       => __( 'Search career' ),
            'not_found'          => __( 'No posts found' ),
            'not_found_in_trash' => __( 'No posts found in the Trash' ), 
            'parent_item_colon'  => '',
            'menu_name'          => 'Careers'
        );
        $args = array(
            'labels'        => $labels,
            'description'   => 'LaneTerravelers job opportunities',
            'public'        => true,
            'menu_position' => 8,
            'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
            'taxonomies'    => array(''),
            'has_archive'   => false,
        );
        register_post_type( 'career', $args ); 
    }
    add_action( 'init', 'register_careers' );

    //register custom post type offices
    function register_offices() {
        $labels = array(
            'name'               => _x( 'offices', 'post type general name' ),
            'singular_name'      => _x( 'office', 'post type singular name' ),
            'add_new'            => _x( 'Add New', 'office' ),
            'add_new_item'       => __( 'Add New ' ),
            'edit_item'          => __( 'Edit office' ),
            'new_item'           => __( 'New office' ),
            'all_items'          => __( 'All' ),
            'view_item'          => __( 'See office' ),
            'search_items'       => __( 'Search office' ),
            'not_found'          => __( 'No posts found' ),
            'not_found_in_trash' => __( 'No posts found in the Trash' ), 
            'parent_item_colon'  => '',
            'menu_name'          => 'Offices'
        );
        $args = array(
            'labels'        => $labels,
            'description'   => 'LaneTerravelers employees',
            'public'        => true,
            'menu_position' => 8,
            'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
            'taxonomies'    => array(''),
            'has_archive'   => false,
        );
        register_post_type( 'office', $args ); 
    }
    add_action( 'init', 'register_offices' );

    //register custom post type press
    function register_press() {
        $labels = array(
            'name'               => _x( 'press', 'post type general name' ),
            'singular_name'      => _x( 'press', 'post type singular name' ),
            'add_new'            => _x( 'Add New', 'press' ),
            'add_new_item'       => __( 'Add New ' ),
            'edit_item'          => __( 'Edit press' ),
            'new_item'           => __( 'New press' ),
            'all_items'          => __( 'All' ),
            'view_item'          => __( 'See press' ),
            'search_items'       => __( 'Search press' ),
            'not_found'          => __( 'No posts found' ),
            'not_found_in_trash' => __( 'No posts found in the Trash' ), 
            'parent_item_colon'  => '',
            'menu_name'          => 'Press'
        );
        $args = array(
            'labels'        => $labels,
            'description'   => 'LaneTerravelers employees',
            'public'        => true,
            'menu_position' => 8,
            'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
            'taxonomies'    => array(''),
            'has_archive'   => false,
        );
        register_post_type( 'press', $args ); 
    }
    add_action( 'init', 'register_press' );

        //register custom post type case_study
    function register_case_study() {
        $labels = array(
            'name'               => _x( 'case studies', 'post type general name' ),
            'singular_name'      => _x( 'case study', 'post type singular name' ),
            'add_new'            => _x( 'Add New', 'case study' ),
            'add_new_item'       => __( 'Add New ' ),
            'edit_item'          => __( 'Edit case study' ),
            'new_item'           => __( 'New case study' ),
            'all_items'          => __( 'All' ),
            'view_item'          => __( 'See case study' ),
            'search_items'       => __( 'Search case study' ),
            'not_found'          => __( 'No posts found' ),
            'not_found_in_trash' => __( 'No posts found in the Trash' ), 
            'parent_item_colon'  => '',
            'menu_name'          => 'Case Studies'
        );
        $args = array(
            'labels'        => $labels,
            'description'   => 'LaneTerravelers employees',
            'public'        => true,
            'menu_position' => 8,
            'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
            'taxonomies'    => array(''),
            'rewrite' => array( 'slug' => 'work', 'with_front' => false ),
            'has_archive'   => false,
        );
        register_post_type( 'case_study', $args ); 
    }
    add_action( 'init', 'register_case_study' );
?>