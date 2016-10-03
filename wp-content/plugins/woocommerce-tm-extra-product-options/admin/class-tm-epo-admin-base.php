<?php
// Direct access security
if ( !defined( 'TM_EPO_PLUGIN_SECURITY' ) ) {
    die();
}

final class TM_EPO_Admin_base {

    var $version        = TM_EPO_VERSION;
    var $plugin_url;

    protected static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function init(){
        return;
    }

    public function __construct() {

        $this->plugin_url = untrailingslashit( plugins_url( '/', dirname( __FILE__ ) ) );
        
        /** Add Admin tab in products **/
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'register_data_tab' ) );
        add_action( 'woocommerce_product_data_panels', array( $this, 'register_data_panels' ) );
        
        /** Load css and javascript files **/
        add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
                
        /** Remove Extra Product Options from deleted Products **/
        add_action( 'delete_post', array( $this, 'delete_post' ) );
        
        /** Remove Extra Product Options via remove button **/
        add_action( 'wp_ajax_woocommerce_tm_remove_epo' , array( $this, 'remove_price' ) );
        add_action( 'wp_ajax_woocommerce_tm_remove_epos' , array( $this, 'remove_prices' ) );
        
        /** Load Extra Product Options **/
        add_action( 'wp_ajax_woocommerce_tm_load_epos' , array( $this, 'load_prices' ) );
        
        /** Add Extra Product Options via add button **/
        add_action( 'wp_ajax_woocommerce_tm_add_epo' , array( $this, 'add_price' ) );
        
        /** Save Extra Product Options meta data **/
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_meta' ) );

        /** Duplicate TM Extra Product Options **/
        add_action( 'woocommerce_duplicate_product' , array( $this, 'duplicate_product' ) , 50, 2 );
        
        /** Show action links on the plugin screen **/
        add_filter( 'plugin_action_links_' . TM_EPO_PLUGIN_NAME_HOOK, array( $this, 'action_links' ) );

    }

    /**
     * Show action links on the plugin screen
     */
    public function action_links( $links ) {
        return array_merge( array(
            '<a href="' . admin_url( 'admin.php?page=wc-settings&tab='.TM_EPO_ADMIN_SETTINGS_ID ) . '">' . __( 'Settings', 'woocommerce-tm-extra-product-options' ) . '</a>',
            '<a href="' . esc_url( 'http://epo.themecomplete.com/documentation/woocommerce-tm-extra-product-options/index.html' ) . '">' . __( 'Docs', 'woocommerce-tm-extra-product-options' ) . '</a>',
            '<a href="' . esc_url( 'http://support.themecomplete.com/' ) . '">' . __( 'Premium Support', 'woocommerce-tm-extra-product-options' ) . '</a>',
        ), $links );
    }

    /**
     * Get a product from the database to duplicate
     *
     * This is needed since the repsective function in woocommerce is private.
     *
     * @access private
     * @param mixed   $id
     * @return WP_Post|bool
     * @todo Returning false? Need to check for it in...
     * @see duplicate_product
     */
    private function get_product_to_duplicate( $id ) {
        $id = absint( $id );
        if ( ! $id ){
            return false;
        }

        global $wpdb;

        $post = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE ID=$id" );

        if ( isset( $post->post_type ) && $post->post_type == "revision" ) {
            $id   = $post->post_parent;
            $post = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE ID=$id" );
        }
        return $post[0];
    }

    public function duplicate_product( $new_id, $post ) {
        
        $tm_meta= get_post_meta( $post_id , 'tm_meta' , true );
            
        if ( !empty($tm_meta) 
            && is_array($tm_meta) 
            && isset($tm_meta['tmfbuilder']) 
            && is_array($tm_meta['tmfbuilder'])
            ){
            update_post_meta( $new_id, 'tm_meta', TM_EPO_HELPER()->recreate_element_ids($tm_meta) );
        }
        if ( class_exists( 'WC_Admin_Duplicate_Product' ) ) {
            $dup = new WC_Admin_Duplicate_Product();
            if ( $children_products = get_children( 'post_parent='.$post->ID.'&post_type='.TM_EPO_LOCAL_POST_TYPE ) ) {

                if ( $children_products ) {
                    $new_rules_ids=array();
                    foreach ( $children_products as $child ) {
                        $new_rules_ids[]=$dup->duplicate_product( $this->get_product_to_duplicate( $child->ID ), $new_id, $child->post_status );
                    }
                    $new_rules_ids=array_filter( $new_rules_ids );

                    if ( !empty( $new_rules_ids ) ) {
                        $children_products = get_children( 'post_parent='.$post->ID.'&post_type=product_variation&order=ASC' );

                        if ( $children_products ) {

                            $old_variations_ids=array();
                            foreach ( $children_products as $child ) {
                                $old_variations_ids[$child->menu_order]=$child->ID;
                            }
                            $old_variations_ids=array_filter( $old_variations_ids );
                            $children_products = get_children( 'post_parent='.$new_id.'&post_type=product_variation&order=ASC' );

                            if ( $children_products ) {

                                $new_variations_ids=array();
                                foreach ( $children_products as $child ) {
                                    $new_variations_ids[$child->menu_order]=$child->ID;
                                }
                                $new_variations_ids=array_filter( $new_variations_ids );

                                if ( !empty( $old_variations_ids ) && !empty( $new_variations_ids ) ) {
                                    
                                    foreach ( $new_rules_ids as $rule_id ) {
                                        $_regular_price = get_post_meta( $rule_id, '_regular_price', true );
                                        /*
                                         * $key = attirbute
                                         * $k = variation
                                         * $v = price
                                         */                                       
                                        $new_regular_price=array();
                                        if (is_array($_regular_price))
                                        foreach ( $_regular_price as $key=>$value ) {
                                            if (is_array($value))
                                            foreach ( $value as $k=>$v ) {                                                
                                                if ( !isset( $new_regular_price[$key] ) ) {
                                                    $new_regular_price[$key]=array();
                                                }
                                                $_new_key=array_search($k, $old_variations_ids);
                                                if($_new_key!==FALSE && $_new_key!==NULL){
                                                    $_new_key=$new_variations_ids[$_new_key];    
                                                }
                                                if($_new_key!==FALSE && $_new_key!==NULL){
                                                    $new_regular_price[$key][$_new_key]=$v;
                                                }
                                            }
                                        }
                                        update_post_meta( $rule_id, '_regular_price', $new_regular_price );
                                    }
                                }
                            }
                        }
                    }

                }
            }
        }
    }

    public function register_data_tab( $tabs ) {
        // Adds the new tab
        $tabs['tm_extra_product_options'] = array(
            'label'  => __( 'TM Extra Product Options', 'woocommerce-tm-extra-product-options' ),
            'target' => 'tm_extra_product_options',
            'class'  => array( 'tm_epo_class', 'hide_if_grouped' )
        );
        return $tabs;
    }

    public function register_data_panels() {

        global $post, $post_id, $tm_is_ajax;
        $post_id=$post->ID;
        $tm_is_ajax=false;
        include ('views/html-tm-global-epo.php');

    }
    private function in_product(){
        $screen = get_current_screen();
        if ( in_array( $screen->id, array(  'product',  'edit-product' , 'shop_order') ) ) {
            return true;
        }
        return false;
    }
    private function in_settings_page(){
        $wc_screen_id = sanitize_title( __( 'WooCommerce', 'woocommerce' ) );
        $screen = get_current_screen();
        $wcsids=wc_get_screen_ids();
        if (is_array($wcsids) && isset($wcsids[3]) && isset($wcsids[2]) && $wcsids[2]==$wc_screen_id.'_page_wc-shipping' ){
            $wcsids=$wcsids[3];
        }
        elseif (is_array($wcsids) && isset($wcsids[2])){
            $wcsids=$wcsids[2];
        }else{
            $wcsids=$wc_screen_id.'_page_wc-settings';
        }
        if ( isset($_GET['tab']) && $_GET['tab']==TM_EPO_ADMIN_SETTINGS_ID && in_array( $screen->id, array( $wcsids ) ) ) {
            return true;
        }
        return false;
    }    
    public function register_admin_styles() {
        wp_enqueue_style( 'tm_epo_admin_css', $this->plugin_url  . '/assets/css/admin/tm-epo-admin.css' );
        if ( $this->in_product() ) {
            TM_EPO_ADMIN_GLOBAL()->register_admin_styles(1);
        }elseif( $this->in_settings_page() ) {
            TM_EPO_ADMIN_GLOBAL()->register_admin_styles(1);
        }
    }

    public function register_admin_scripts() {
        global $wp_query, $post;
        $this->register_admin_styles();
        if ( $this->in_product() ) {
            wp_register_script( 'tm_epo_admin_meta_boxes', $this->plugin_url . '/assets/js/admin/tm-epo-admin.js', array( 'jquery' ), $this->version );
            $params = array(
                'post_id'                       => isset( $post->ID ) ? $post->ID : '',
                'plugin_url'                    => $this->plugin_url,
                'ajax_url'                      => strtok(admin_url( 'admin-ajax'.'.php' ), '?'),//WPML 3.3.x fix
                'add_tm_epo_nonce'              => wp_create_nonce( "add-tm-epo" ),
                'delete_tm_epo_nonce'           => wp_create_nonce( "delete-tm-epo" ),
                'check_attributes_nonce'        => wp_create_nonce( "check_attributes" ),
                'load_tm_epo_nonce'             => wp_create_nonce( "load-tm-epo" ),
                'i18n_no_variations'            => esc_js( __( 'There are no saved variations yet.', 'woocommerce-tm-extra-product-options' ) ),
                'i18n_max_tmcp'                 => esc_js( __( 'You cannot add any more extra options.', 'woocommerce-tm-extra-product-options' ) ),
                'i18n_remove_tmcp'              => esc_js( __( 'Are you sure you want to remove this option?', 'woocommerce-tm-extra-product-options' ) ),
                'i18n_missing_tmcp'             => esc_js( __( 'Before adding Extra Product Options, add and save some attributes on the <strong>Attributes</strong> tab.', 'woocommerce-tm-extra-product-options' ) ),
                'i18n_fixed_type'               => esc_js( __( 'Fixed amount', 'woocommerce-tm-extra-product-options' ) ),
                'i18n_percent_type'             => esc_js( __( 'Percent of the orignal price', 'woocommerce-tm-extra-product-options' ) ),
                'i18n_error_title'              => __( 'Error', 'woocommerce-tm-extra-product-options' )
            );
            wp_localize_script( 'tm_epo_admin_meta_boxes', 'tm_epo_admin_meta_boxes', $params );
            wp_enqueue_script( 'tm_epo_admin_meta_boxes' );

            TM_EPO_ADMIN_GLOBAL()->register_admin_scripts(1);
            
        }elseif( $this->in_settings_page() ) {
            TM_EPO_ADMIN_GLOBAL()->register_admin_scripts(1);
        }
    }

    public function delete_post( $id ) {
        global $woocommerce, $wpdb;
        if ( ! current_user_can( 'delete_posts' ) ) {
            return;
        }
        if ( $id > 0 ) {
            $post_type = get_post_type( $id );
            switch ( $post_type ) {
            case 'product' :
                $child_product_variations = get_children( 'post_parent=' . $id . '&post_type='.TM_EPO_LOCAL_POST_TYPE );
                if ( $child_product_variations ) {
                    foreach ( $child_product_variations as $child ) {
                        wp_delete_post( $child->ID, true );
                    }
                }
                wc_delete_product_transients();
                break;
            case TM_EPO_LOCAL_POST_TYPE :
                wc_delete_product_transients();
                break;
            }
        }
    }

    public function remove_price() {
        if ( ! current_user_can( 'delete_posts' ) ) {
            return;
        }
        check_ajax_referer( 'delete-tm-epo', 'security' );
        $tmcpid = intval( $_POST['tmcpid'] );
        $tmcp = get_post( $tmcpid );
        if ( $tmcp && $tmcp->post_type == TM_EPO_LOCAL_POST_TYPE ) {
            wp_delete_post( $tmcpid );
        }
        die();
    }

    public function remove_prices() {
        if ( ! current_user_can( 'delete_posts' ) ) {
            return;
        }
        check_ajax_referer( 'delete-tm-epos', 'security' );
        $tmcpids = (array) $_POST['tmcpids'];
        foreach ( $tmcpids as $tmcpid ) {
            $tmcp = get_post( $tmcpid );
            if ( $tmcp && $tmcp->post_type == TM_EPO_LOCAL_POST_TYPE ) {
                wp_delete_post( $tmcpid );
            }
        }
        die();
    }

    public function load_prices() {
        global $post, $post_id, $tm_is_ajax;
        $tm_is_ajax=true;
        if (isset($_POST['post_id'])){
            $post_id = intval( $_POST['post_id'] );
            include 'views/html-tm-epo.php';            
        }
        die();
    }

    public function add_price() {
        check_ajax_referer( 'add-tm-epo', 'security' );
        $post_id    = intval( $_POST['post_id'] );
        $loop       = intval( $_POST['loop'] );
        $att_id     = ( $_POST['att_id'] );

        // Get Attributes
        function _tm_alter_attributes( &$item1, $key, $attributes ) {
            if (  $attributes[$item1]['is_variation'] ) {
                $item1 = "";
            }
        }
        $attributes = (array) maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );
        $_attributes = array_keys( $attributes );
        array_walk( $_attributes, '_tm_alter_attributes', $attributes );

        // $_attributes holds the number of all available attributes we can use
        $_attributes = array_diff( $_attributes, array( '' ) );

        // check if we can insert a post
        $args = array(
            'post_type'     => TM_EPO_LOCAL_POST_TYPE,
            'post_status'   => array( 'private', 'publish' ),
            'numberposts'   => -1,
            'orderby'       => 'menu_order',
            'order'         => 'asc',
            'post_parent'   => $post_id,
            'meta_query'    => array(
                array(
                    'key'       => 'tmcp_attribute',
                    'value'     => $_attributes,
                    'compare'   => 'IN'
                )
            )
        );
        $tmepos = get_posts( $args );
        if ( count( $tmepos ) >= count( $_attributes ) ) {
            die( 'max' );
        }

        // else add a new extra option
        $tmcp = array(
            'post_title'    => 'Product #' . $post_id . ' Extra Product Option',
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_parent'   => $post_id,
            'post_author'   => get_current_user_id(),
            'post_type'     => TM_EPO_LOCAL_POST_TYPE
        );
        $tmcp_id = wp_insert_post( $tmcp );
        if ( $tmcp_id ) {
            update_post_meta( $tmcp_id, 'tmcp_attribute', $att_id );
            update_post_meta( $tmcp_id, 'tmcp_attribute_is_taxonomy', $attributes[$att_id]['is_taxonomy'] );
            $tmcp_post_status               = 'publish';
            $tmcp_data                      = get_post_meta( $tmcp_id );
            $tmcp_required                  = 0;
            $tmcp_hide_price                = 0;
            $tmcp_limit                     = "";

            // Get Attributes
            $attributes = (array) maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );

            // Get parent data
            $parent_data = array(
                'id' => $post_id,
                'attributes'=> $attributes
            );

            // Get Variations
            $args = array(
                'post_type'     => 'product_variation',
                'post_status'   => array( 'private', 'publish' ),
                'numberposts'   => -1,
                'orderby'       => 'menu_order',
                'order'         => 'asc',
                'post_parent'   => $post_id
            );
            $variations = get_posts( $args );

            include 'views/html-tm-epo-admin.php';
        }
        die();
    }

    public function save_meta( $post_id ) {
        global $woocommerce, $wpdb;

        $attributes = (array) maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );
        
        if ( isset( $_POST['variable_sku'] ) || isset( $_POST['_sku'] ) ) {
            $_post_id                   = isset( $_POST['tmcp_post_id'] ) ? $_POST['tmcp_post_id'] : array();
            $tmcp_regular_price         = isset( $_POST['tmcp_regular_price'] ) ? $_POST['tmcp_regular_price'] : array();
            $tmcp_regular_price_type    = isset( $_POST['tmcp_regular_price_type'] ) ? $_POST['tmcp_regular_price_type'] : array();
            $tmcp_enabled               = isset( $_POST['tmcp_enabled'] ) ? $_POST['tmcp_enabled'] : array();
            $tmcp_required              = isset( $_POST['tmcp_required'] ) ? $_POST['tmcp_required'] : array();
            $tmcp_hide_price            = isset( $_POST['tmcp_hide_price'] ) ? $_POST['tmcp_hide_price'] : array();
            $tmcp_limit                 = isset( $_POST['tmcp_limit'] ) ? $_POST['tmcp_limit'] : array();
            $tmcp_menu_order            = isset( $_POST['tmcp_menu_order'] ) ? $_POST['tmcp_menu_order'] : array();
            $tmcp_attribute             = isset( $_POST['tmcp_attribute'] ) ? $_POST['tmcp_attribute'] : array();
            $tmcp_type                  = isset( $_POST['tmcp_type'] ) ? $_POST['tmcp_type'] : array();
            $tm_meta_cpf                = isset( $_POST['tm_meta_cpf'] ) ? $_POST['tm_meta_cpf'] : array();

            // update custom product settings
            update_post_meta( $post_id, 'tm_meta_cpf', $tm_meta_cpf );    
            
            if ( isset($_POST['tm_meta_serialized'])){
                $tm_metas = $_POST['tm_meta_serialized'];
                $tm_metas = stripslashes_deep($tm_metas);
                $tm_metas = rawurldecode($tm_metas);
                $tm_metas = nl2br($tm_metas);
                $tm_metas = json_decode($tm_metas, true);

                if($tm_metas || (is_array($tm_metas))){
                    if (!isset($_SESSION)){
                        session_start();
                    }
                    $import=false;
                    if (isset($_SESSION['import_csv'])){
                        $import=$_SESSION['import_csv'];
                    }
                    if (!empty($import)){     
                        if (!empty($_SESSION['import_override'])){
                            $tm_metas=$import;
                            unset($_SESSION['import_override']);
                        }else{
                            $tm_metas=TM_EPO_ADMIN_GLOBAL()->import_array_merge($tm_metas,$import);    
                        }                        
                        unset($_SESSION['import_csv']);
                    }
                    
                    $old_data = get_post_meta($post_id, 'tm_meta',true);

                    if ( !empty($tm_metas) && is_array($tm_metas) && isset($tm_metas['tm_meta']) && is_array($tm_metas['tm_meta'])){
                        $tm_meta=$tm_metas['tm_meta'];
                        TM_EPO_ADMIN_GLOBAL()->tm_save_meta($post_id, $tm_meta, $old_data, 'tm_meta');
                    }else{
                        TM_EPO_ADMIN_GLOBAL()->tm_save_meta($post_id, false, $old_data, 'tm_meta');
                    }
                }
            }elseif ( isset($_POST['tm_meta_serialized_wpml'])){
                $tm_metas = $_POST['tm_meta_serialized_wpml'];
                $tm_metas = stripslashes_deep($tm_metas);
                $tm_metas = rawurldecode($tm_metas);
                $tm_metas = nl2br($tm_metas);
                $tm_metas = json_decode($tm_metas, true);
                if($tm_metas){
                    
                    $old_data = get_post_meta($post_id, 'tm_meta_wpml',true);

                    if ( !empty($tm_metas) && is_array($tm_metas) && isset($tm_metas['tm_meta']) && is_array($tm_metas['tm_meta'])){
                        $tm_meta=$tm_metas['tm_meta'];
                        TM_EPO_ADMIN_GLOBAL()->tm_save_meta($post_id, $tm_meta, $old_data, 'tm_meta_wpml');
                    }else{
                        TM_EPO_ADMIN_GLOBAL()->tm_save_meta($post_id, false, $old_data, 'tm_meta_wpml');
                    }
                }                
            }

            if (!empty($_post_id )){
                global $wpdb;
                $max_loop = max( array_keys( $_post_id ) );
                for ( $i = 0; $i <= $max_loop; $i ++ ) {

                    if ( ! isset( $_post_id[ $i ] ) ){
                        continue;
                    }

                    $tmcp_id = absint( $_post_id[ $i ] );

                    // This will always be update post
                    if ( $tmcp_id ) {
                        // Enabled or disabled
                        $post_status = isset( $tmcp_enabled[ $i ] ) ? 'publish' : 'private';

                        // Generate a useful post title
                        $post_title = sprintf( __( 'TM Extra Product Option #%s of %s', 'woocommerce-tm-extra-product-options' ), absint( $tmcp_id ), esc_html( get_the_title( $post_id ) ) );

                        /*wp_update_post( wp_slash( array(
                            'ID'            => $tmcp_id,
                            'post_status'   => $post_status,
                            'post_title'    => $post_title,
                            'menu_order'    => $tmcp_menu_order[ $i ]
                            )));*/
                        $data = wp_slash( array(
                            'post_status'   => $post_status,
                            'post_title'    => $post_title,
                            'menu_order'    => $tmcp_menu_order[ $i ]
                            ));
                        $data = wp_unslash( $data );
                        $where = array( 'ID' => $tmcp_id );
                        if ( false === $wpdb->update( $wpdb->posts, $data, $where ) ) {
                            if ( $wp_error ) {
                                return new WP_Error('db_update_error', __('Could not update post in the database'), $wpdb->last_error);
                            } else {
                                return 0;
                            }
                        }
                        // Update post meta

                        // Price handling
                        $clean_prices = array();
                        $clean_prices_type = array();
                        if ( isset( $tmcp_regular_price[ $i ] ) ) {
                            foreach ( $tmcp_regular_price[ $i ] as $key=>$value ) {
                                foreach ( $value as $k=>$v ) {
                                    if ( $v !== '' ) {
                                        $clean_prices[$key][$k] = wc_format_decimal( $v );
                                    }
                                }
                            }
                        }
                        if ( isset( $tmcp_regular_price_type[ $i ] ) ) {
                            foreach ( $tmcp_regular_price_type[ $i ] as $key=>$value ) {
                                foreach ( $value as $k=>$v ) {
                                    $clean_prices_type[$key][$k] = $v;
                                }
                            }
                        }
                        
                        $regular_price = $clean_prices ;
                        $regular_price_type = $clean_prices_type;
                        update_post_meta( $tmcp_id, '_regular_price', $regular_price );
                        update_post_meta( $tmcp_id, '_regular_price_type', $regular_price_type );

                        $post_required      = isset( $tmcp_required[ $i ] ) ? 1 : '';
                        $post_hide_price    = isset( $tmcp_hide_price[ $i ] ) ? 1 : '';
                        $post_limit         = isset( $tmcp_limit[ $i ] ) ?  $tmcp_limit[ $i ] : '';
                        update_post_meta( $tmcp_id, 'tmcp_required', $post_required );
                        update_post_meta( $tmcp_id, 'tmcp_hide_price', $post_hide_price );
                        update_post_meta( $tmcp_id, 'tmcp_limit', $post_limit );
                        update_post_meta( $tmcp_id, 'tmcp_attribute', $tmcp_attribute[ $i ] );
                        update_post_meta( $tmcp_id, 'tmcp_attribute_is_taxonomy', $attributes[$tmcp_attribute[ $i ]]['is_taxonomy'] );
                        update_post_meta( $tmcp_id, 'tmcp_type', $tmcp_type[ $i ] );

                    }
                }
            }
        }
    }
}

?>