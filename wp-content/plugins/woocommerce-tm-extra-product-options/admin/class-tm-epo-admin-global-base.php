<?php
// Direct access security
if ( !defined( 'TM_EPO_PLUGIN_SECURITY' ) ) {
    die();
}

/**
 * Global EPO Administration class
 */
final class TM_EPO_ADMIN_Global_base {

    var $version        = TM_EPO_VERSION;
    var $plugin_url;
    var $tm_list_table;

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

        $this->plugin_url       = untrailingslashit( plugins_url( '/', dirname( __FILE__ ) ) );

        /**
         *  Add menu action
         */
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );

        /**
         *  Pre-render actions
         */
        add_action( 'admin_init', array( $this, 'tm_admin_init' ), 9 );
        add_action( 'plugins_loaded', array( $this, 'tm_init' ), 100 );

        /**
         *  Save custom screen options.
         */
        add_filter( 'set-screen-option', array( $this, 'tm_set_option' ), 10, 3);

        /**
         *  Add the plugin to WooCommerce screen ids so that
         *  we can load the generic WooCommerce files
         */
        add_filter( 'woocommerce_screen_ids', array( $this, 'woocommerce_screen_ids' ) );

        /**
         *  Add list columns
         */
        add_filter( 'manage_'.TM_EPO_GLOBAL_POST_TYPE.'_posts_columns' , array( $this, 'tm_list_columns' ) );
        add_action( 'manage_'.TM_EPO_GLOBAL_POST_TYPE.'_posts_custom_column' , array( $this, 'tm_list_column' ), 10, 2 );

        /**
         *  Export a form.
         */
        add_action( 'wp_ajax_tm_export', array( $this, 'export' ) );

        /* Variations check */
        add_action( 'wp_ajax_woocommerce_tm_variations_check' , array( $this, 'tm_variations_check' ) );
        add_action( 'wp_ajax_nopriv_woocommerce_tm_variations_check' , array( $this, 'tm_variations_check' ) );
        add_action( 'wp_ajax_woocommerce_tm_get_variations_array' , array( $this, 'tm_get_variations_array' ) );
        add_action( 'wp_ajax_nopriv_woocommerce_tm_get_variations_array' , array( $this, 'tm_get_variations_array' ) );

        /* File manager */
        add_action( 'wp_ajax_tm_mn_movetodir' , array( $this, 'tm_mn_movetodir' ) );
        add_action( 'wp_ajax_tm_mn_deldir' , array( $this, 'tm_mn_deldir' ) );
        add_action( 'wp_ajax_tm_mn_delfile' , array( $this, 'tm_mn_delfile' ) );

    }

    /* File manager */
    public function tm_mn_deldir(){

        if (isset($_POST["tmdir"])){
            $subdir=TM_EPO()->upload_dir.$_POST["tmdir"];
            $param = wp_upload_dir();
            if ( empty( $param['subdir'] ) ) {
                $param['path']      = $param['path'] . $subdir;
                $param['url']       = $param['url']. $subdir;
                $param['subdir']    = $subdir;
            } else {
                $param['path']      = str_replace( $param['subdir'], $subdir, $param['path'] );
                $param['url']       = str_replace( $param['subdir'], $subdir, $param['url'] );
                $param['subdir']    = str_replace( $param['subdir'], $subdir, $param['subdir'] );
            }
            $html=TM_EPO_HELPER()->file_rmdir($param['path']);
        }

        $this->tm_mn_movetodir();
    }

    public function tm_mn_delfile(){

        if (isset($_POST["tmfile"]) && isset($_POST["tmdir"])){
            $subdir=TM_EPO()->upload_dir.$_POST["tmdir"];
            $param = wp_upload_dir();
            if ( empty( $param['subdir'] ) ) {
                $param['path']      = $param['path'] . $subdir;
                $param['url']       = $param['url']. $subdir;
                $param['subdir']    = $subdir;
            } else {
                $param['path']      = str_replace( $param['subdir'], $subdir, $param['path'] );
                $param['url']       = str_replace( $param['subdir'], $subdir, $param['url'] );
                $param['subdir']    = str_replace( $param['subdir'], $subdir, $param['subdir'] );
            }
            $html=TM_EPO_HELPER()->file_delete($param['path']."/".$_POST["tmfile"]);
        }

        $this->tm_mn_movetodir();
    }

    public function tm_mn_movetodir(){      

        check_ajax_referer( 'settings-nonce', 'security' );
        
        $html='';
        if (isset($_POST["dir"])){
            $html=TM_EPO_HELPER()->file_manager(TM_EPO()->upload_dir,$_POST["dir"]);
        }
        if ($html){
            echo json_encode( 
                array( 
                    'result' => $html
                ) 
            );
        }else{
            echo json_encode( 
                array( 
                    'error' => 1,
                    'message' =>__( 'File manager is not supported on your server.', 'woocommerce-tm-extra-product-options' )
                ) 
            );            
        }
        die();
    }

    /* Variations check */
    public function tm_variations_check() {
        global $post, $post_id, $tm_is_ajax;
        $tm_is_ajax=true;
        if (isset($_POST['post_id'])){
            $post_id = intval( $_POST['post_id'] );
            $builder = get_post_meta( $post_id , 'tm_meta', true );
            $meta = array();
            if (isset($builder['tmfbuilder']) && isset($builder['tmfbuilder']['variations_options'])){
                $meta = $builder['tmfbuilder']['variations_options'];
            }
            echo TM_EPO_BUILDER()->builder_sub_variations_options($meta,$post_id);
        }
        die();
    }

    public function get_available_variations($product) {
        $available_variations = array();

        foreach ( $product->get_children() as $child_id ) {
            $variation = $product->get_child( $child_id );

            // Hide out of stock variations if 'Hide out of stock items from the catalog' is checked
            if ( empty( $variation->variation_id ) || ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && ! $variation->is_in_stock() ) ) {
                continue;
            }

            // Filter 'woocommerce_hide_invisible_variations' to optionally hide invisible variations (disabled variations and variations with empty price)
            if ( apply_filters( 'woocommerce_hide_invisible_variations', false, $product->id, $variation ) && ! $variation->variation_is_visible() ) {
                continue;
            }

            $available_variations[] = $this->get_available_variation( $variation,$product );
        }

        return $available_variations;
    }

    public function get_available_variation( $variation,$product ) {
        if ( is_numeric( $variation ) ) {
            $variation = $product->get_child( $variation );
        }

/*        if ( has_post_thumbnail( $variation->get_variation_id() ) ) {
            $attachment_id   = get_post_thumbnail_id( $variation->get_variation_id() );
            $attachment      = wp_get_attachment_image_src( $attachment_id, 'shop_single' );
            $full_attachment = wp_get_attachment_image_src( $attachment_id, 'full' );
            $image           = $attachment ? current( $attachment ) : '';
            $image_link      = $full_attachment ? current( $full_attachment ) : '';
            $image_title     = get_the_title( $attachment_id );
            $image_alt       = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
        } else {
            $image = $image_link = $image_title = $image_alt = '';
        }

        $availability      = $variation->get_availability();
        $availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . wp_kses_post( $availability['availability'] ) . '</p>';
        $availability_html = apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $variation );*/

        return apply_filters( 'tc_epo_woocommerce_available_variation', array(
            'variation_id'          => $variation->variation_id,
            'attributes'            => $variation->get_variation_attributes(),
            /*'variation_is_visible'  => $variation->variation_is_visible(),
            'variation_is_active'   => $variation->variation_is_active(),
            'is_purchasable'        => $variation->is_purchasable(),
            'display_price'         => $variation->get_display_price(),
            'display_regular_price' => $variation->get_display_price( $variation->get_regular_price() ),
            'image_src'             => $image,
            'image_link'            => $image_link,
            'image_title'           => $image_title,
            'image_alt'             => $image_alt,
            'price_html'            => apply_filters( 'woocommerce_show_variation_price', $variation->get_price() === "" || $this->get_variation_price( 'min' ) !== $this->get_variation_price( 'max' ), $this, $variation ) ? '<span class="price">' . $variation->get_price_html() . '</span>' : '',
            'availability_html'     => $availability_html,
            'sku'                   => $variation->get_sku(),
            'weight'                => $variation->get_weight() . ' ' . esc_attr( get_option('woocommerce_weight_unit' ) ),
            'dimensions'            => $variation->get_dimensions(),
            'min_qty'               => 1,
            'max_qty'               => $variation->backorders_allowed() ? '' : $variation->get_stock_quantity(),
            'backorders_allowed'    => $variation->backorders_allowed(),*/
            'is_in_stock'           => $variation->is_in_stock(),/*
            'is_downloadable'       => $variation->is_downloadable() ,
            'is_virtual'            => $variation->is_virtual(),
            'is_sold_individually'  => $variation->is_sold_individually() ? 'yes' : 'no',
            'variation_description' => $variation->get_variation_description(),*/
        ), $product, $variation );
    }

    public function tm_get_variations_array() {
        $variations=array();
        $attributes=array();
        if (isset($_POST['post_id'])){
            if (class_exists('Woocommerce_Waitlist')){
                remove_filter( 'woocommerce_get_availability', array( Woocommerce_Waitlist::get_instance(), 'wew_check_product_availability' ), 2, 2 );
                remove_filter( 'woocommerce_get_availability', array( Woocommerce_Waitlist::get_instance(), 'wew_check_product_availability' ) );
            }

            $product = wc_get_product( $_POST['post_id'] );
            
            if($product && is_object($product) && method_exists($product, 'get_available_variations')){
                $variations = $this->get_available_variations($product);
                $attributes = $product->get_variation_attributes();
            }

        }
        echo json_encode(array('variations'=>$variations,'attributes'=>$attributes)); die();
     }

    /**
     * Export a form.
     */
    public function export(){

        $csv = new TM_EPO_ADMIN_CSV();
        $csv->export('metaserialized');
        
    }

    /**
     * Import a form.
     */
    public function import(){
        
        $csv = new TM_EPO_ADMIN_CSV();
        $csv->import();
        
    }

    /**
     * Download a form.
     */
    public function download(){
        
        $csv = new TM_EPO_ADMIN_CSV();
        $csv->download();
        
    }

    /**
     * Extra row actions.
     */
    public function row_actions( $actions, $post ){

        // Get the post type object
        $post_type = get_post_type_object( $post->post_type );
        
        $can_do_clone=true;
        //disable wpml cloning on translated forms
        if(TM_EPO_WPML()->is_active()){
            $ppid=absint(get_post_meta($post->ID,TM_EPO_WPML_PARENT_POSTID,true));
            if (!empty($ppid) && $ppid!=$post->ID){
                $can_do_clone=false;
            }
        }

        if( $can_do_clone ){
            // Clone a form
            $nonce = wp_create_nonce( 'tmclone_form_nonce_'.$post->ID ); 
            $actions['tm_clone_form'] = '<a class="tm-clone-form" rel="'.$nonce.'" href="'.admin_url( "edit.php?post_type=product&amp;page=".TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK."&amp;action=clone&amp;post=".$post->ID."&amp;_wpnonce=".$nonce ).'">'.__( 'Clone form', 'woocommerce-tm-extra-product-options' ).'</a>';
        }

        // Export a form
        $nonce = wp_create_nonce( 'tmexport_form_nonce_'.$post->ID ); 
        $actions['tm_export_form'] = '<a class="tm-export-form" rel="'.$nonce.'" href="'.admin_url( "edit.php?post_type=product&amp;page=".TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK."&amp;action=export&amp;post=".$post->ID."&amp;_wpnonce=".$nonce ).'">'.__( 'Export form', 'woocommerce-tm-extra-product-options' ).'</a>';
        ksort($actions);
        return $actions;
    }

    /**
     * Add menus
     */
    public function admin_menu() {
        $page_hook = add_submenu_page( 'edit.php?post_type=product', __( 'TM Global Extra Product Options', 'woocommerce-tm-extra-product-options' ), __( 'TM Global Extra Product Options', 'woocommerce-tm-extra-product-options' ), 'manage_woocommerce', TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK, array( $this, 'admin_screen' ) );
        
        /*
         *  Restrict loading scripts and functions unless we are on the plugin page
         */
        add_action( 'load-' . $page_hook, array( $this, 'tm_load_admin' ) );
    }

    public function tm_load_scripts(){
        /**
         *  Load css and javascript files
         */
        add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

    }

    /**
     * Loads plugin functionality
     */
    public function tm_load_admin(){

        $this->tm_load_scripts();

        /**
         *  Custom action to populate the filter select box.
         */
        add_action( 'tm_restrict_manage_posts', array( $this, 'tm_restrict_manage_posts' ) );

        /**
         *  Add screen option
         */        
        $this->tm_add_option();

        /**
         *  Add meta boxes
         */        
        $this->tm_add_metaboxes();

        /**
         *  Extra row actions
         */
        add_filter( 'post_row_actions', array( $this,'row_actions'), 10, 2 );
        add_filter( 'page_row_actions', array( $this,'row_actions'), 10, 2 );

    }

    /**
     * Add list columns
     */
    public function tm_list_columns($columns){
        $new_columns                = array();
        $new_columns['cb']          = isset($columns['cb'])?$columns['cb']:'<input type="checkbox" />';
        $new_columns['title']       = isset($columns['title'])?$columns['title']:__('Title','woocommerce-tm-extra-product-options');
        if (TM_EPO_WPML()->is_active()){
            $flags='';
            foreach (TM_EPO_WPML()->get_active_languages() as $key => $value) {
                if($key!=TM_EPO_WPML()->get_lang()){
                    $flags .= TM_EPO_WPML()->get_flag($key);
                }
            }
            $new_columns['tm_icl_translations'] = '<span class="tm-icl-space">&nbsp;</span>'.$flags;
        }        
        $new_columns['priority']    = __( 'Priority','woocommerce-tm-extra-product-options' );
        $new_columns['product_cat'] = __( 'Categories' , 'woocommerce-tm-extra-product-options');
        $new_columns['product_ids'] = __( 'Products' , 'woocommerce-tm-extra-product-options');

        unset($columns['cb']);
        unset($columns['title']);
        return array_merge( $new_columns, $columns );
    }
    
    public function tm_list_column( $column,  $post_id ){
        switch ( $column ) {

            case 'tm_icl_translations':
                $main_post_id=0;                
                $tm_meta_lang= get_post_meta( $post_id , TM_EPO_WPML_LANG_META , true );
                if (empty($tm_meta_lang)){
                    $tm_meta_lang=TM_EPO_WPML()->get_default_lang();
                    $main_post_id=$post_id;
                }
                if (empty($main_post_id)){
                    $main_post_id=get_post_meta( $post_id , TM_EPO_WPML_PARENT_POSTID , true );
                }
                echo TM_EPO_WPML()->get_flag($tm_meta_lang);
                foreach (TM_EPO_WPML()->get_active_languages() as $key => $value) {
                    if($key!=$tm_meta_lang || TM_EPO_WPML()->get_lang()=='all'){
                        
                        if ($key==TM_EPO_WPML()->get_default_lang()){
                            $query = new WP_Query( 
                            array(
                                'post_type'     => TM_EPO_GLOBAL_POST_TYPE,
                                'post_status'   => array( 'publish' ), 
                                'numberposts'   => -1,
                                'orderby'       => 'date',
                                'order'         => 'asc',
                                'p'             => $main_post_id
                            ));
                        }else{

                            $meta_query=TM_EPO_HELPER()->build_meta_query('AND',TM_EPO_WPML_LANG_META,$key,'=', 'EXISTS');
                            $meta_query[] =  array(
                                'key' => TM_EPO_WPML_PARENT_POSTID, 
                                'value' => $main_post_id,
                                'compare' => '='
                            );
                            /*if ($key==TM_EPO_WPML()->get_default_lang()){
                                $meta_query=TM_EPO_HELPER()->build_meta_query('OR',TM_EPO_WPML_LANG_META,$tm_meta_lang,'=', 'NOT EXISTS');
                            }*/
                            $query = new WP_Query( 
                            array(
                                'post_type'     => TM_EPO_GLOBAL_POST_TYPE,
                                'post_status'   => array( 'publish' ), 
                                'numberposts'   => -1,
                                'orderby'       => 'date',
                                'order'         => 'asc',
                                'meta_query'    => $meta_query
                            ));               

                        }

                        if ( !empty($query->posts)  ){
                            echo TM_EPO_WPML()->edit_lang_link($query->post->ID,$key,$value,$main_post_id);
                        }elseif(empty($query->posts)){
                            echo TM_EPO_WPML()->add_lang_link($main_post_id,$key,$value);
                        }
                    }                    
                }
                break;

            case 'product_cat' :
                $tm_meta= get_post_meta( $post_id , 'tm_meta_disable_categories' , true );
                if ($tm_meta){
                    echo '<span class="tm_color_pomegranate">'.__('Disabled','woocommerce-tm-extra-product-options').'</span>';
                }else{
                    $terms = get_the_term_list( $post_id , 'product_cat' , '' , ' , ' , '' );
                    if ( is_string( $terms ) ){
                        echo $terms;
                    }                    
                }
                break;

            case 'priority' :
                $post_id = TM_EPO_WPML()->get_original_id($post_id, TM_EPO_GLOBAL_POST_TYPE);
                $tm_meta= get_post_meta( $post_id , 'tm_meta' , true );
                if (is_array($tm_meta)){
                    if (is_array($tm_meta['priority'])){
                        $tm_meta['priority']=$tm_meta['priority'][0];
                    }
                    echo $tm_meta['priority'];
                }
                break;

            case 'product_ids' :
                $tm_meta= get_post_meta( $post_id , 'tm_meta_product_ids' , true );

                if (!empty($tm_meta)){
                    if (is_array($tm_meta)){
                        if (count($tm_meta)==1 && !empty($tm_meta[0])){
                                $title=get_the_title( $tm_meta[0] );
                                $tm_meta[0]='<a title="'.esc_attr($title).'" href="'.admin_url( 'post.php?action=edit&post='.$tm_meta[0] ).'">'.$title.'</a>';
                        }else{
                            foreach ($tm_meta as $key => $value) {
                                if(!empty($value)){
                                    $title=get_the_title( $value );
                                    $tm_meta[$key]='<a class="tm-tooltip" title="'.esc_attr($title).'" href="'.admin_url( 'post.php?action=edit&post='.$value ).'">'.$value.'</a>';
                                }
                            }
                        }                        
                        echo implode(" , ", $tm_meta);        
                    }else{
                        echo '';
                    }
                }                
                break;

        }
        
    }

    /**
     * Handle meta boxes
     */
    public function tm_add_metaboxes(){
        // only continue if we are are on add/edit screen
        if (!$this->tm_list_table || !$this->tm_list_table->current_action()){
            return;
        }

        add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );
        
        // WPML meta box
        TM_EPO_WPML()->add_meta_box();

        // Publish meta box
        add_meta_box("submitdiv", _( 'Publish' ), array( $this, 'tm_post_submit_meta_box' ), null, "side", "core");
        
        // Taxonomies meta box
        if ($this->tm_list_table){
            TM_EPO_WPML()->remove_term_filters();
            foreach ( get_object_taxonomies( $this->tm_list_table->screen->post_type ) as $tax_name ) {
                $taxonomy = get_taxonomy( $tax_name );
                if ( ! $taxonomy->show_ui ){
                    continue;
                }
                if (!property_exists($taxonomy,'meta_box_cb') || false === $taxonomy->meta_box_cb ){
                    if ( $taxonomy->hierarchical ){
                        $taxonomy->meta_box_cb = 'post_categories_meta_box';
                    }else{
                        $taxonomy->meta_box_cb = 'post_tags_meta_box';
                    }
                }
                $label = $taxonomy->labels->name;
                if ( ! is_taxonomy_hierarchical( $tax_name ) ){
                    $tax_meta_box_id = 'tagsdiv-' . $tax_name;
                }else{
                    $tax_meta_box_id = $tax_name . 'div';
                }
                add_meta_box( $tax_meta_box_id, $label, $taxonomy->meta_box_cb, null, 'side', 'core', array( 'taxonomy' => $tax_name ) );
            }
            TM_EPO_WPML()->restore_term_filters();
        }

        // Products meta box
        add_meta_box("tm_product_search", __( 'Products', 'woocommerce-tm-extra-product-options' ), array( $this, 'tm_product_search_meta_box' ), null, "side", "core");

        // Roles meta box
        add_meta_box("tm_product_roles", __( 'Roles', 'woocommerce-tm-extra-product-options' ), array( $this, 'tm_product_roles_meta_box' ), null, "side", "core");

        // Description meta box
        add_meta_box("postexcerpt", __('Description', 'woocommerce-tm-extra-product-options'), array( $this, 'tm_description_meta_box' ), null, "normal", "core");
        
        // Price rules meta box
        add_meta_box("tmformfieldsbuilder", __('Extra Product Options Form Builder', 'woocommerce-tm-extra-product-options'), array( $this, 'tm_form_fields_builder_meta_box' ), null, "normal", "core");
    }

    // Description meta box
    public function tm_description_meta_box($post){
        $settings = array(
            'textarea_name' => 'excerpt',
            'quicktags'     => array( 'buttons' => 'em,strong,link' ),
            'tinymce'       => array(
                'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
                'theme_advanced_buttons2' => '',
            ),
            'editor_css'    => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>'
        );

        wp_editor( htmlspecialchars_decode( $post->post_excerpt ), 'excerpt', apply_filters( 'woocommerce_product_short_description_editor_settings', $settings ) );
        echo '<p>'.esc_attr__('The description will appear under the title.', 'woocommerce-tm-extra-product-options' ).'</p>';
    }
    
    public function tm_product_roles_meta_box($post){
        $disabled='';
        if (!TM_EPO_WPML()->is_original_product($post->ID,$post->post_type)){
            $disabled = 'disabled="disabled" ';
        }
        $meta=$post->tm_meta;
        $enabled_roles = isset($meta['enabled_roles'])?$meta['enabled_roles']:'';
        $disabled_roles = isset($meta['disabled_roles'])?$meta['disabled_roles']:'';

        if (!is_array($enabled_roles)){
            $enabled_roles=array($enabled_roles);
        }
        if (!is_array($disabled_roles)){
            $disabled_roles=array($disabled_roles);
        }
        $enabled_options = '';
        $disabled_options = '';
        $roles = tc_get_roles();
        foreach ( $roles as $option_key => $option_text ) {
            $enabled_options .= '<option value="' . esc_attr( $option_key ) . '" '. selected( in_array( $option_key, $enabled_roles ), 1, false ) . '>' . esc_attr( $option_text ) .'</option>';
            $disabled_options .= '<option value="' . esc_attr( $option_key ) . '" '. selected( in_array( $option_key, $disabled_roles ), 1, false ) . '>' . esc_attr( $option_text ) .'</option>';
        }

        echo '<div class="message0x0 tc-clearfix">'.
                '<div class="message2x1">'.
                    '<label for="tm_enabled_options"><span>'.__( 'Enabled roles for this form', 'woocommerce-tm-extra-product-options' ).'</span></label>'.
                '</div>'.
                '<div class="message2x2">'.
                    '<select id="tm_enabled_options" name="tm_meta_enabled_roles[]" class="multiselect wc-enhanced-select" multiple="multiple">'.
                        $enabled_options.
                    '</select>'.
                '</div>'.
            '</div>';
        echo '<div class="message0x0 tc-clearfix">'.
                '<div class="message2x1">'.
                    '<label for="tm_disabled_options"><span>'.__( 'Disabled roles for this form', 'woocommerce-tm-extra-product-options' ).'</span></label>'.
                '</div>'.
                '<div class="message2x2">'.
                    '<select id="tm_disabled_options" name="tm_meta_disabled_roles[]" class="multiselect wc-enhanced-select" multiple="multiple">'.
                        $disabled_options.
                    '</select>'.
                '</div>'.
            '</div>';
    }
    public function tm_product_search_meta_box($post){
        $disabled='';
        if (!TM_EPO_WPML()->is_original_product($post->ID,$post->post_type)){
            $disabled = 'disabled="disabled" ';
        }
        $meta=$post->tm_meta;?>
        <h3 id="tc_disabled_categories" class="hidden"><label for="tm_meta_disable_categories"><?php _e( 'Disable categories', 'woocommerce-tm-extra-product-options' ); ?> 
            <input <?php echo $disabled;?>type="checkbox" value="1" id="tm_meta_disable_categories" name="tm_meta_disable_categories" class="meta-disable-categories" <?php checked($meta['disable_categories'] , 1); ?>/>                                                  
        </label></h3>
        <label for="tm_product_ids"><?php _e( 'Select the Product(s) to apply the options', 'woocommerce-tm-extra-product-options' ); ?></label>
        <?php if(version_compare( get_option( 'woocommerce_db_version' ), '2.3', '<' )){?>
                <select <?php echo $disabled;?>id="tm_product_ids" 
                    name="tm_meta_product_ids[]" 
                    class="ajax_chosen_select_tm_product_ids" 
                    multiple="multiple" 
                    data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce-tm-extra-product-options' ); ?>">
                    <?php 
                        $_ids = isset($meta['product_ids'])?$meta['product_ids']:null;
                        $product_ids = ! empty( $_ids ) ? array_map( 'absint',  $_ids ) : null;
                        if ( $product_ids ) {
                            foreach ( $product_ids as $product_id ) {

                                $product = wc_get_product( $product_id );

                                if ( $product ){
                                    echo '<option value="' . esc_attr( $product_id ) . '" selected="selected">' . esc_html( $product->get_formatted_name() ) . '</option>';
                                }
                                    
                            }
                        }
                    ?>
                </select><?php
            }elseif(version_compare( get_option( 'woocommerce_db_version' ), '2.3', '>=' )){?>
                <input <?php echo $disabled;?>type="<?php if(empty($disabled)){echo'hidden';}else{echo'text';}?>" class="ajax_selectit_select_tm_product_ids wc-product-search" style="width: 50%;" 
                    id="tm_product_ids" name="tm_meta_product_ids" 
                    data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce-tm-extra-product-options' );?>" 
                    data-action="woocommerce_json_search_products" 
                    data-multiple="true" 
                    data-selected="<?php
                    $_ids = isset($meta['product_ids'])?$meta['product_ids']:null;
                    $product_ids = array_filter( array_map( 'absint', (array) $_ids ) );
                    $json_ids    = array();

                    foreach ( $product_ids as $product_id ) {
                        $product = wc_get_product( $product_id );
                        if($product){
                            $json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
                        }
                    }

                    echo esc_attr( json_encode( $json_ids ) );
                ?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" />
        <?php }
    }

    // Price rules meta box
    public function tm_form_fields_builder_meta_box($post){
        //do_action( 'tm_before_price_rules' );
        ?>
        <div id="tmformfieldsbuilderwrap" class="tm_wrapper">
        <?php 
        $wpml_is_original_product=true;
        $id_for_meta = $post->ID;
        if(TM_EPO_WPML()->is_active()){

            $wpml_is_original_product=TM_EPO_WPML()->is_original_product($post->ID,$post->post_type);
            if (!$wpml_is_original_product){
                $id_for_meta = floatval(TM_EPO_WPML()->get_original_id( $post->ID, $post->post_type ));
            }

        }
        if ($wpml_is_original_product){
            echo "<div class='builder_selector'>"
            . '<div class="tm-row">'
            . '<div class="tm-cell col-6">'
            . '<a id="builder_add_section" class="builder_add_section tm-button-dotted bsbb" href="#"><i class="tcfa tcfa-plus-square"></i> '.__("Add section",'woocommerce-tm-extra-product-options').'</a>'
            . '<a id="builder_add_variation" class="builder_add_variation tm-button-dotted tm-hidden bsbb" href="#"><i class="tcfa tcfa-bullseye"></i> '.__("Style variations",'woocommerce-tm-extra-product-options').'</a>'
            . '</div>'
            //. '<div class="tm-ajax-info cell col-2">'
            //. '</div>'
            . '<div class="tm-cell col-6">'
            . '<a id="builder_fullsize_close" class="tm-button button button-primary button-large builder_fullsize_close" href="#">'.__("Close",'woocommerce-tm-extra-product-options').'</a>'
            . '<a id="builder_fullsize" class="tm-button button button-primary button-large builder_fullsize tc-clearfix" href="#">'.__("Fullsize",'woocommerce-tm-extra-product-options').'</a>'
            . '<a id="builder_export" class="tm-button button button-primary button-large builder-export tc-clearfix" href="#">'.__("Export CSV",'woocommerce-tm-extra-product-options').'</a>'
            . '<a id="builder_import" class="tm-button button button-primary button-large builder-import tc-clearfix" href="#">'.__("Import CSV",'woocommerce-tm-extra-product-options').'</a>'
            . '<input id="builder_import_file" name="builder_import_file" type="file" class="builder-import-file" />'
            . '</div>'
            . '</div>'
            . "</div>";
        }
        echo TM_EPO_BUILDER()->print_elements(0,$wpml_is_original_product)
            . "<div class='builder_layout'>"
            . TM_EPO_BUILDER()->print_saved_elements(0,$id_for_meta,$post->ID,$wpml_is_original_product)
            . "</div>";
        if ($wpml_is_original_product){
            echo "<div class='builder-add-section-action'>"
                . "<div class='tm-add-section-action'><a title='".__("Add element in a new section",'woocommerce-tm-extra-product-options')."' class='builder_add_section_and_element tmfa tcfa tcfa-plus'></a></div>"
                . "</div>";
        }
        ?>
        </div>
    <?php
    }
    
    // Publish meta box
    public function tm_post_submit_meta_box($post){
        $meta=$post->tm_meta;
        ?>
        <div class="submitbox" id="submitpost">
            <div id="minor-publishing">
                <div style="display:none;">
                <?php submit_button( __( 'Save', 'woocommerce-tm-extra-product-options' ), 'button', 'save' ); ?>
                </div>
                <div id="minor-publishing-actions">
                    <div id="save-action">
                        <span class="spinner"></span>
                    </div>                                              
                    <div class="clear"></div>
                </div>
                <div id="misc-publishing-actions">
                    <div class="misc-pub-section misc-pub-priority" id="priority">
                        <?php if (TM_EPO_WPML()->is_original_product($post->ID,$post->post_type)){ ?>
                        <?php echo esc_attr__( 'Priority','woocommerce-tm-extra-product-options' ); ?>: 
                        <input type="number" value="<?php echo (int) $meta['priority']; ?>" maxlength="3" id="tm_meta_priority" name="tm_meta[priority]" class="meta-priority" min="1" step="1" />
                        <?php } ?>
                    </div>                          
                </div>
                <div class="clear"></div>
            </div>
            <div id="major-publishing-actions">
                <div id="delete-action">
                    <?php
                    if ( current_user_can( "delete_post", $post->ID ) ) {
                        if ( !EMPTY_TRASH_DAYS ){
                            $delete_text = __('Delete Permanently', 'woocommerce-tm-extra-product-options');
                        }else{
                            $delete_text = __('Move to Trash', 'woocommerce-tm-extra-product-options');
                        }
                        ?>
                        <a class="submitdelete deletion" href="<?php echo get_delete_post_link($post->ID); ?>"><?php echo $delete_text; ?></a><?php
                    } ?>
                </div>
                <div id="publishing-action">
                    <span class="spinner"></span>
                    <?php
                    if ( !in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ) {
                        if ( $meta['can_publish'] ) : ?>
                    <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Publish') ?>" />
                    <?php submit_button( __( 'Publish', 'woocommerce-tm-extra-product-options' ), 'primary button-large', 'publish', false, array( 'accesskey' => 'p' ) ); ?>
                        <?php   
                        else : ?>
                    <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Submit for Review') ?>" />
                    <?php submit_button( __( 'Submit for Review', 'woocommerce-tm-extra-product-options' ), 'primary button-large', 'publish', false, array( 'accesskey' => 'p' ) ); ?>
                    <?php
                        endif;
                    } else { ?>
                        <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Update') ?>" />
                        <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php esc_attr_e('Update') ?>" />
                    <?php
                    } ?>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php        
    }

    /**
     *  Pre-render actions
     */
    public function tm_init(){
        if (!isset($_GET['page']) || ($_GET['page'] != TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK)){
            return;
        }
        /* remove cforms plugin tinymce buttons */
        remove_action('init', 'cforms_addbuttons');
    }
    /**
     *  Pre-render actions
     */
    public function tm_admin_init(){
        /**
         *  Custom filters for the edit and delete links.
         */
        add_filter( 'get_edit_post_link', array( $this, 'tm_get_edit_post_link' ),10,3 );
        add_filter( 'get_delete_post_link', array( $this, 'tm_get_delete_post_link' ),10,3 );

        /*
         *  Check if we are on the plugin page
         */
        if (!isset($_GET['page']) || ($_GET['page'] != TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK)){
            return;
        }

        // remove annoying messages that mess up the interface 
        remove_all_actions('admin_notices');

        // WPML: set correct language according to post
        TM_EPO_WPML()->set_post_lang();
        
        // save meta data
        add_action( 'save_post', array( $this, 'tm_save_postdata' ), 1, 2);

        //global $typenow;
        //if ( ! $typenow ){
            //wp_die( __( 'Invalid post type', 'woocommerce-tm-extra-product-options' ) );
        //}
        if (!class_exists('WP_List_Table')){
            wp_die( __( 'Something went wrong with WordPress.' , 'woocommerce-tm-extra-product-options') );
        }

        global $bulk_counts,$bulk_messages,$general_messages;
              
        $post_type = 'product';
        $post_type_object = get_post_type_object( $post_type );
        if ( ! $post_type_object ){
            wp_die( __( 'WooCommerce is not enabled!' , 'woocommerce-tm-extra-product-options') );
        }
        if ( ! current_user_can( $post_type_object->cap->edit_posts ) ){
            wp_die( __( 'Cheatin&#8217; uh?' , 'woocommerce-tm-extra-product-options') );
        }
        
        $this->tm_list_table    = $this->get_wp_list_table('TM_EPO_ADMIN_Global_List_Table');
        $post_type              = $this->tm_list_table->screen->post_type;
        $pagenum                = $this->tm_list_table->get_pagenum();
        $parent_file            = "edit.php?post_type=product&page=".TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
        $submenu_file           = "edit.php?post_type=product&page=".TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
        $post_new_file          = "edit.php?post_type=product&page=".TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK."&action=add";
        $doaction               = $this->tm_list_table->current_action();
        $sendback               = remove_query_arg( array('trashed', 'untrashed', 'deleted', 'locked', 'ids'), wp_get_referer() );
        if ( ! $sendback ){
            $sendback = admin_url( $parent_file );
        }
        $sendback = add_query_arg( 'paged', $pagenum, $sendback );

        $sendback = esc_url_raw($sendback);

        /**
         * Bulk actions
         */
        if ( $doaction && isset($_REQUEST['tm_bulk'])) {           
            check_admin_referer('bulk-posts');
            
            if ( 'delete_all' == $doaction ) {
                $post_status = preg_replace('/[^a-z0-9_-]+/i', '', $_REQUEST['post_status']);
                if ( get_post_status_object($post_status) ){ // Check if the post status exists first
                    global $wpdb;
                    $post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type=%s AND post_status = %s", $post_type, $post_status ) );
                }
                $doaction = 'delete';
            } elseif ( isset( $_REQUEST['ids'] ) ) {
                $post_ids = explode( ',', $_REQUEST['ids'] );
            } elseif ( !empty( $_REQUEST['post'] ) ) {
                $post_ids = array_map('intval', $_REQUEST['post']);
            }
            if ( !isset( $post_ids ) ) {
                wp_redirect( $sendback );
                exit;
            }

            switch ( $doaction ) {
            case 'trash':
                $trashed = $locked = 0;

                foreach( (array) $post_ids as $post_id ) {
                    if ( !current_user_can( 'delete_post', $post_id) ){
                        wp_die( __('You are not allowed to move this item to the Trash.', 'woocommerce-tm-extra-product-options') );
                    }
                    if ( wp_check_post_lock( $post_id ) ) {
                        $locked++;
                        continue;
                    }

                    if ( !wp_trash_post($post_id) ){
                        wp_die( __('Error in moving to Trash.', 'woocommerce-tm-extra-product-options') );
                    }

                    $trashed++;
                }

                $sendback = add_query_arg( array('from_bulk' => 1,'trashed' => $trashed, 'ids' => join(',', $post_ids), 'locked' => $locked ), $sendback );
                break;
            case 'untrash':
                $untrashed = 0;
                foreach( (array) $post_ids as $post_id ) {
                    if ( !current_user_can( 'delete_post', $post_id) ){
                        wp_die( __('You are not allowed to restore this item from the Trash.', 'woocommerce-tm-extra-product-options') );
                    }

                    if ( !wp_untrash_post($post_id) ){
                        wp_die( __('Error in restoring from Trash.', 'woocommerce-tm-extra-product-options') );
                    }

                    $untrashed++;
                }
                $sendback = add_query_arg( array('from_bulk' => 1, 'untrashed' => $untrashed), $sendback );
                break;
            case 'delete':
                $deleted = 0;
                foreach( (array) $post_ids as $post_id ) {
                    $post_del = get_post($post_id);

                    if ( !current_user_can( 'delete_post', $post_id ) ){
                        wp_die( __('You are not allowed to delete this item.', 'woocommerce-tm-extra-product-options') );
                    }

                    if ( $post_del->post_type == 'attachment' ) {
                        if ( ! wp_delete_attachment($post_id) ){
                            wp_die( __('Error in deleting.', 'woocommerce-tm-extra-product-options') );
                        }
                    } else {
                        if ( !wp_delete_post($post_id) ){
                            wp_die( __('Error in deleting.', 'woocommerce-tm-extra-product-options') );
                        }
                    }
                    $deleted++;
                }
                $sendback = add_query_arg( array('from_bulk' => 1, 'deleted' => $deleted), $sendback ) ;
                 
                break;
            case 'edit':
                if ( isset($_REQUEST['bulk_edit']) ) {
                    
                    $done = bulk_edit_posts($_REQUEST);

                    if ( is_array($done) ) {
                        $done['updated'] = count( $done['updated'] );
                        $done['skipped'] = count( $done['skipped'] );
                        $done['locked'] = count( $done['locked'] );
                        $sendback = add_query_arg( $done, $sendback );
                    }
                }
                break;
            }

            $sendback = remove_query_arg( array('action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view'), $sendback );
            $sendback = esc_url_raw($sendback);
            wp_redirect($sendback);
            exit();
        }

        /**
         * Single actions
         */
        elseif ( $doaction && !isset($_REQUEST['tm_bulk'])) { 

            if ( isset( $_GET['post'] ) ){
                $post_id = $post_ID = (int) $_GET['post'];
            }
            elseif ( isset( $_POST['post_ID'] ) ){
                $post_id = $post_ID = (int) $_POST['post_ID'];
            }
            elseif ( isset( $_REQUEST['ids'] ) ){
                $post_id = $post_ID = (int) $_REQUEST['ids'];
            }else{
                $post_id = $post_ID = 0;
            }
            global $post;
            $post = $post_type = $post_type_object = null;

            if ( $post_id ){
                $post = get_post( $post_id );
            }
            if ( $post ) {
                $post_type = $post->post_type;
                if ($post_type!=TM_EPO_GLOBAL_POST_TYPE){
                    $edit_link = admin_url( 'post.php?action=edit&post='.$post_id );
                    wp_redirect($edit_link);
                    exit();
                }
                $post_type_object = get_post_type_object( $post_type );
            }

            switch ( $doaction ) {
            case 'export':
                $this->tm_export_form_action($post_id);
                $_redirect = remove_query_arg( array('action','post','_wp_http_referer', '_wpnonce'), wp_unslash($_SERVER['REQUEST_URI']) );
                $_redirect = add_query_arg( 'message', 21,  $_redirect );
                $_redirect = esc_url_raw($_redirect);
                wp_redirect( $_redirect );
                break;
            case 'clone':

                $this->tm_clone_form_action($post_id);
                $_redirect = remove_query_arg( array('action','post','_wp_http_referer', '_wpnonce'), wp_unslash($_SERVER['REQUEST_URI']) );
                $_redirect = esc_url_raw($_redirect);
                wp_redirect( $_redirect );
                 exit;

                break;
            case 'trash':
                check_admin_referer('trash-post_' . $post_id);

                if ( ! $post ){
                    wp_die( __( 'The item you are trying to move to the Trash no longer exists.', 'woocommerce-tm-extra-product-options' ) );
                }

                if ( ! $post_type_object ){
                    wp_die( __( 'Unknown post type.' , 'woocommerce-tm-extra-product-options') );
                }

                if ( ! current_user_can( 'delete_post', $post_id ) ){
                    wp_die( __( 'You are not allowed to move this item to the Trash.', 'woocommerce-tm-extra-product-options' ) );
                }

                if ( $user_id = wp_check_post_lock( $post_id ) ) {
                    $user = get_userdata( $user_id );
                    wp_die( sprintf( __( 'You cannot move this item to the Trash. %s is currently editing.' , 'woocommerce-tm-extra-product-options'), $user->display_name ) );
                }

                if ( ! wp_trash_post( $post_id ) ){
                    wp_die( __( 'Error in moving to Trash.' , 'woocommerce-tm-extra-product-options') );
                }

                wp_redirect( esc_url_raw( add_query_arg( array('trashed' => 1, 'ids' => $post_id), $sendback ) ) );
                exit();
                break;

            case 'untrash':
                check_admin_referer('untrash-post_' . $post_id);

                if ( ! $post ){
                    wp_die( __( 'The item you are trying to restore from the Trash no longer exists.', 'woocommerce-tm-extra-product-options' ) );
                }

                if ( ! $post_type_object ){
                    wp_die( __( 'Unknown post type.', 'woocommerce-tm-extra-product-options' ) );
                }

                if ( ! current_user_can( 'delete_post', $post_id ) ){
                    wp_die( __( 'You are not allowed to move this item out of the Trash.', 'woocommerce-tm-extra-product-options' ) );
                }

                if ( ! wp_untrash_post( $post_id ) ){
                    wp_die( __( 'Error in restoring from Trash.', 'woocommerce-tm-extra-product-options' ) );
                }

                wp_redirect( esc_url_raw( add_query_arg('untrashed', 1, $sendback) ) );
                exit();
                break;

            case 'delete':
                check_admin_referer('delete-post_' . $post_id);

                if ( ! $post ){
                    wp_die( __( 'This item has already been deleted.', 'woocommerce-tm-extra-product-options' ) );
                }

                if ( ! $post_type_object ){
                    wp_die( __( 'Unknown post type.' , 'woocommerce-tm-extra-product-options') );
                }

                if ( ! current_user_can( 'delete_post', $post_id ) ){
                    wp_die( __( 'You are not allowed to delete this item.', 'woocommerce-tm-extra-product-options' ) );
                }

                $force = ! EMPTY_TRASH_DAYS;
                if ( $post->post_type == 'attachment' ) {
                    $force = ( $force || ! MEDIA_TRASH );
                    if ( ! wp_delete_attachment( $post_id, $force ) ){
                        wp_die( __( 'Error in deleting.', 'woocommerce-tm-extra-product-options' ) );
                    }
                } else {
                    if ( ! wp_delete_post( $post_id, $force ) ){
                        wp_die( __( 'Error in deleting.', 'woocommerce-tm-extra-product-options' ) );
                    }
                }

                wp_redirect( esc_url_raw( add_query_arg('deleted', 1, $sendback) ) );
                exit();
                break;
            case 'editpost':
                check_admin_referer('update-post_' . $post_id);

                $post_id = edit_post();

                // Session cookie flag that the post was saved
                if ( isset( $_COOKIE['wp-saving-post-' . $post_id] ) ){
                    setcookie( 'wp-saving-post-' . $post_id, 'saved' );
                }

                $this->redirect_post($post_id);

                exit();
                break;
            case 'edit':
               if ( empty( $post_id ) ) {
                    wp_redirect( admin_url($parent_file) );
                    exit();
                }

                if ( ! $post ){
                    wp_die( __( 'You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?', 'woocommerce-tm-extra-product-options' ) );
                }
                if ( ! $post_type_object ){
                    wp_die( __( 'Unknown post type.' , 'woocommerce-tm-extra-product-options') );
                }
                if ( ! current_user_can( 'edit_post', $post_id ) ){
                    wp_die( __( 'You are not allowed to edit this item.' , 'woocommerce-tm-extra-product-options') );
                }

                if ( 'trash' == $post->post_status ){
                    wp_die( __( 'You can&#8217;t edit this item because it is in the Trash. Please restore it and try again.' , 'woocommerce-tm-extra-product-options') );
                }
                break;
            case 'add':
                $post_type = $this->tm_list_table->screen->post_type;
                $post_type_object = get_post_type_object( $post_type );
                if ( ! current_user_can( $post_type_object->cap->edit_posts ) || ! current_user_can( $post_type_object->cap->create_posts ) ){
                    wp_die( __( 'Cheatin&#8217; uh?' , 'woocommerce-tm-extra-product-options') );
                }

                break;

            case 'import':
                $this->import();
                break;
            case 'download':
                $this->download();
                break;
            }
        } elseif ( ! empty($_REQUEST['_wp_http_referer']) ) {
            wp_redirect( 
                esc_url_raw( 
                    remove_query_arg( 
                        array('_wp_http_referer', '_wpnonce'), 
                        wp_unslash($_SERVER['REQUEST_URI']) 
                    ) 
                ) 
            );
             exit;
        }
        
        /**
         * We get here if we are in the list view.
         */

        $bulk_counts = array(
            'updated'   => isset( $_REQUEST['updated'] )   ? absint( $_REQUEST['updated'] )   : 0,
            'locked'    => isset( $_REQUEST['locked'] )    ? absint( $_REQUEST['locked'] )    : 0,
            'deleted'   => isset( $_REQUEST['deleted'] )   ? absint( $_REQUEST['deleted'] )   : 0,
            'trashed'   => isset( $_REQUEST['trashed'] )   ? absint( $_REQUEST['trashed'] )   : 0,
            'untrashed' => isset( $_REQUEST['untrashed'] ) ? absint( $_REQUEST['untrashed'] ) : 0,
        );

        $bulk_messages = array();
        $bulk_messages[$post_type] = array(
            'updated'   => _n( '%s post updated.', '%s posts updated.', $bulk_counts['updated'] ),
            'locked'    => _n( '%s post not updated, somebody is editing it.', '%s posts not updated, somebody is editing them.', $bulk_counts['locked'] ),
            'deleted'   => _n( '%s post permanently deleted.', '%s posts permanently deleted.', $bulk_counts['deleted'] ),
            'trashed'   => _n( '%s post moved to the Trash.', '%s posts moved to the Trash.', $bulk_counts['trashed'] ),
            'untrashed' => _n( '%s post restored from the Trash.', '%s posts restored from the Trash.', $bulk_counts['untrashed'] ),
        );
        $bulk_counts = array_filter( $bulk_counts );

        $general_messages = array();
        $general_messages[$post_type] = array(
            21   => __('The selected form does not contain any sections','woocommerce-tm-extra-product-options'),
        );
        $general_messages = array_filter( $general_messages );

    }

    private function redirect_post($post_id = '') {
        $edit_post_link=admin_url( "edit.php?post_type=product&page=".TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK."&action=edit&post=$post_id" );
        if ( isset($_POST['save']) || isset($_POST['publish']) ) {
            $status = get_post_status( $post_id );

            if ( isset( $_POST['publish'] ) ) {
                switch ( $status ) {
                    case 'pending':
                        $message = 8;
                        break;
                    case 'future':
                        $message = 9;
                        break;
                    default:
                        $message = 6;
                }
            } else {
                $message = 'draft' == $status ? 10 : 1;
            }

            $location = add_query_arg( 'message', $message, $edit_post_link );

        } else {
            $location = add_query_arg( 'message', 4, $edit_post_link );
        }
        $location = esc_url_raw($location);

        wp_redirect( apply_filters( 'redirect_post_location', $location, $post_id ) );
        
        exit;
    }

    public function tm_get_delete_post_link($url, $post_id, $foce) {
        // check we're in the right place, otherwise return
        if ( !(
               (isset($_GET['page']) && $_GET['page'] == TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK) 
            || (isset($_POST['screen']) && $_POST['screen'] == TM_EPO_GLOBAL_POST_TYPE)
            )) {
            return $url;
        }
        $vars = array();
        $decoded_url = str_replace("&amp;", "&", $url);
        $decoded_url = str_replace("?", "&", $decoded_url);
        wp_parse_str($decoded_url,$vars);
        if (isset($vars['action']) && isset($vars['_wpnonce'])){
            if($vars['action']=='delete'  ){
                $url=admin_url( "edit.php?post_type=product&amp;page=".TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK."&amp;action=delete&amp;post=$post_id&amp;_wpnonce=".$vars['_wpnonce'] );
            }
            if($vars['action']=='trash'  ){
                $url=admin_url( "edit.php?post_type=product&amp;page=".TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK."&amp;action=trash&amp;post=$post_id&amp;_wpnonce=".$vars['_wpnonce'] );
            }

        }
        return $url;

    }
    public function tm_get_edit_post_link($url, $post_id, $context) {
        // check we're in the right place, otherwise return
        if ( !(
               (isset($_GET['page']) && $_GET['page'] == TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK) 
            || (isset($_POST['screen']) && $_POST['screen'] == TM_EPO_GLOBAL_POST_TYPE)
            )) {
            return $url;
        }
        $vars = array();
        $decoded_url = str_replace("&amp;", "&", $url);
        $decoded_url = str_replace("?", "&", $decoded_url);
        wp_parse_str($decoded_url,$vars);
        if (isset($vars['action'])){
            if($vars['action']=='edit'  ){
                $url=admin_url( "edit.php?post_type=product&amp;page=".TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK."&amp;action=edit&amp;post=$post_id" );
            }
        }
        return $url;
    }

    /**
     *  Populate the filter select box.
     */
    public function tm_restrict_manage_posts() {      
        // check we're in the right place, otherwise return
        if (!isset($_GET['page']) || ($_GET['page']!=TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK)){
            return;
        }        

        global $typenow, $wp_query;

        $output ='';

        $customPostTaxonomies = get_object_taxonomies(TM_EPO_GLOBAL_POST_TYPE);
        $show_option_all = apply_filters( 'list_cats', __('Select a category', 'woocommerce-tm-extra-product-options') );

        if(count($customPostTaxonomies) > 0){
             foreach($customPostTaxonomies as $tax){
                $output .= "<select name='$tax' id='dropdown_$tax'>\n";

                $selected = (isset($wp_query->query[$tax]) && $wp_query->query[$tax]=='') ? '' : 0;
                $selected = ( '' === $selected ) ? " selected='selected'" : '';
                $output .= "\t<option value=''$selected>$show_option_all</option>\n";

                $terms = get_terms( $tax, 'orderby=name&hide_empty=0' );
                foreach ( $terms as $term ) {
                    $selected = (isset($wp_query->query[$tax]) && $wp_query->query[$tax]==$term->slug) ? $term->slug : '';
                    $selected = (  $term->slug === $selected ) ? " selected='selected'" : '';
                    $output .= "\t<option class='level-0' value='".$term->slug."'$selected>".$term->name."</option>\n";
                }
                $output .= "</select>\n";
             }
        }

        echo $output;
    }

    public function tm_add_option() {
        // only continue if we are are on list screen
        if ($this->tm_list_table && $this->tm_list_table->current_action()){
            return;
        }
        $option = 'per_page';
 
        $args = array(
            'label'     => __('Extra Product Options', 'woocommerce-tm-extra-product-options'),
            'default'   => 20,
            'option'    => 'tm_per_page'
        );
        add_screen_option( $option, $args );        
    }

    public function tm_set_option($status, $option, $value) {
        if ( 'tm_per_page' == $option ){
            return $value;
        } 
        return $status;
    }

    /**
     * Adds our custom screen id to WooCommerce so that we can load needed WooCommerce files.
     *
     */
    public function woocommerce_screen_ids( $screen_ids ) {
        $screen_ids[] = 'product_page_'.TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
        return $screen_ids;
    }

    /**
     * Enqueue plugin css and dequeue unwanted woocommerce css styles
     */
    public function register_admin_styles($override=0) {
        if (empty($override) || $override!=1){
            $screen = get_current_screen();
            if($screen->id != 'product_page_'.TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK){
                return;
            }
            //wp_dequeue_style( 'woocommerce_admin_menu_styles' );            
            wp_dequeue_style( 'jquery-ui-style' );
            wp_dequeue_style( 'wp-color-picker' );
            wp_dequeue_style( 'woocommerce_admin_dashboard_styles' );

        }
        wp_enqueue_style( 'tc-font-awesome', $this->plugin_url .'/external/font-awesome/css/font-awesome.min.css', false, '4.5', 'screen' );
        wp_enqueue_style( 'tm_global_epo_animate_css', $this->plugin_url  . '/assets/css/animate.css' );
        wp_enqueue_style( 'tm_global_epo_admin_css', $this->plugin_url  . '/assets/css/admin/tm-global-epo-admin.css' );
        
        wp_enqueue_style( 'tm_global_epo_admin_font', 'https://fonts.googleapis.com/css?family=Roboto:400,100,300,700,900,400italic,700italic' );
    }

    /**
     * Enqueue plugin scripts and dequeue unwanted woocommerce scripts
     */
    public function register_admin_scripts($override=0) {
        global $wp_query, $post;
        $this->register_admin_styles($override);
        if (empty($override) || $override!=1){
            $screen = get_current_screen();
            if($screen->id !='product_page_'.TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK){
                return;
            }
            wp_dequeue_script( 'woocommerce_admin' );
            wp_dequeue_script( 'iris' );
        }
        
        // Dequeue DHVC Woocommerce products choosen scripts.
        wp_dequeue_script( 'dhvc-woo-admin' );

        wp_register_script( 'tm-modernizr', $this->plugin_url. '/assets/js/modernizr.js', array(   ), '2.8.3' );
        wp_enqueue_style( 'tm-spectrum', $this->plugin_url. '/assets/css/tm-spectrum.css', false, '1.7.1', 'screen' );
        wp_register_script( 'tm-scripts', $this->plugin_url . '/assets/js/tm-scripts.js', '', TM_EPO_VERSION );
        
        wp_register_script( 'tm_iframe_transport', $this->plugin_url. '/external/jquery.fileupload/js/jquery.iframe-transport.js', array('jquery'), '1.8.2');
        wp_register_script( 'tm_fileupload', $this->plugin_url. '/external/jquery.fileupload/js/jquery.fileupload.js', 
            array('jquery',
                'jquery-ui-widget',
                'tm_iframe_transport'), '5.41.0');

        wp_register_script( 'tm_global_epo_admin' , $this->plugin_url . '/assets/js/admin/tm-global-epo-admin.js', 
            array( 'jquery',
                'jquery-ui-droppable',
                'jquery-ui-sortable',
                'jquery-ui-tabs',
                'json2',
                'tm-scripts',
                'tm-modernizr', 
                'tm_fileupload' 
                ), $this->version );
        $import_url = "edit.php?post_type=product&page=".TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK."&action=import";                
        $import_url = admin_url( $import_url );
        $params = array(
            'post_id'                   => isset( $post->ID ) ? $post->ID : '',
            'search_products_nonce'     => wp_create_nonce("search-products"),
            'settings_nonce'            => wp_create_nonce("settings-nonce"),
            'export_nonce'              => wp_create_nonce("export-nonce"),
            'check_attributes_nonce'    => wp_create_nonce( "check_attributes" ),
            'ajax_url'                  => strtok(admin_url( 'admin-ajax'.'.php' ), '?'),//WPML 3.3.x fix
            'plugin_url'                => $this->plugin_url,
            'mn_delete_file'            => __( 'Are you sure you want to delete this file?', 'woocommerce-tm-extra-product-options' ),
            'mn_delete_folder'          => __( 'Are you sure you want to delete this folder and all of its contents?', 'woocommerce-tm-extra-product-options' ),
            'delete_style'              => __( 'Are you sure you want to delete this style?', 'woocommerce-tm-extra-product-options' ),    
            'builder_delete'            => __( 'Are you sure you want to delete this item?', 'woocommerce-tm-extra-product-options' ),
            'builder_clone'             => __( 'Are you sure you want to clone this item?', 'woocommerce-tm-extra-product-options' ),
            'update'                    => __( 'Update', 'woocommerce-tm-extra-product-options' ),
            'i18n_no_variations'        => esc_js( __( 'There are no saved variations yet.', 'woocommerce-tm-extra-product-options' ) ),
            'i18n_cancel'               => __( 'Cancel', 'woocommerce-tm-extra-product-options' ),
            'edit_settings'             => __( 'Edit settings', 'woocommerce-tm-extra-product-options' ),
            'element_uniqid'            => __( 'Element id', 'woocommerce-tm-extra-product-options' ),
            'i18n_is'                   => __( 'is', 'woocommerce-tm-extra-product-options' ),
            'i18n_is_not'               => __( 'is not', 'woocommerce-tm-extra-product-options' ),
            'i18n_is_empty'             => __( 'is empty', 'woocommerce-tm-extra-product-options' ),
            'i18n_is_not_empty'         => __( 'is not empty', 'woocommerce-tm-extra-product-options' ),
            'i18n_starts_with'          => __( 'starts with', 'woocommerce-tm-extra-product-options' ),
            'i18n_ends_with'            => __( 'ends with', 'woocommerce-tm-extra-product-options' ),
            'i18n_greater_than'         => __( 'greater than', 'woocommerce-tm-extra-product-options' ),
            'i18n_less_than'            => __( 'less than', 'woocommerce-tm-extra-product-options' ),
            'cannot_apply_rules'        => __( 'Cannot apply rules on this element or section since there are not any value configured elements on other sections, or no other sections found. ', 'woocommerce-tm-extra-product-options' ),
            'invalid_request'           => __( 'Invalid request!', 'woocommerce-tm-extra-product-options' ),
            'i18n_populate'             => __( 'Populate', 'woocommerce-tm-extra-product-options' ),
            'i18n_invalid_extension'    => __( 'Invalid file extension', 'woocommerce-tm-extra-product-options' ),
            'i18n_importing'            => __( 'Importing csv...', 'woocommerce-tm-extra-product-options' ),
            'i18n_saving'               => __( 'Saving... Please wait.', 'woocommerce-tm-extra-product-options' ),
            'import_url'                => $import_url,
            'import_title'              => __( 'Importing data', 'woocommerce-tm-extra-product-options' ),
            'i18n_error_title'          => __( 'Error', 'woocommerce-tm-extra-product-options' ),
            'i18n_add_element'          => __( 'Add element', 'woocommerce-tm-extra-product-options' ),
            'i18n_overwrite_existing_elements' => __( 'Overwrite existing elements', 'woocommerce-tm-extra-product-options' ),
            'i18n_append_new_elements'  => __( 'Append new elements', 'woocommerce-tm-extra-product-options' ),
            'element_data'              => $this->js_element_data(),
        );
        wp_localize_script( 'tm_global_epo_admin', 'tm_epo_admin', $params );
        wp_enqueue_script( 'tm_global_epo_admin' );                   
    }

    public function js_element_data($button_class=""){

        $drag_elements = array();
        $tags=array();
        foreach ( TM_EPO_BUILDER()->get_elements() as $element=>$settings ) {
            if ( isset( TM_EPO_BUILDER()->elements_array[$element] ) ) {
               
                if( $settings['show_on_backend'] ){
                    $tagclass="";
                    if ($settings['_is_addon']){
                        $tags[ $settings['namespace'] ][sanitize_title("tc-".$settings['tags'])] = $settings['tags'];
                        $tagclass .=" tc-".sanitize_title($settings['tags'])." tc-".sanitize_title($settings['namespace']);
                    }else{
                        $tag = explode(" ", $settings['tags']);
                        foreach ($tag as $key => $value) {
                            $tags[ $settings['namespace'] ][sanitize_title($value)] = $value;
                            $tagclass .=" tc-".sanitize_title($value);
                        }
                    }
                    
                    $_drag_elements ='<li class="transition tm-element-button'.$tagclass.'">';
                    $_drag_elements .="<div data-element='".$element."' class='".$button_class." tc-element-button element-".$element."'>"
                    ."<div class='tm-label'>"
                    ."<i class='tmfa tcfa ".$settings["icon"]."'></i> "
                    ."<span class='tm-element-name'>".$settings["name"]."</span>"
                    ."<i class='tm-description'>".$settings["description"]."</i>"
                    ."</div></div>";
                    $_drag_elements .='</li>';

                    $drag_elements[ $settings['namespace'] ][] = $_drag_elements;

                }

            }
        }

        $tm_drag_elements = $drag_elements[ TM_EPO_BUILDER()->elements_namespace ];
        unset($drag_elements[ TM_EPO_BUILDER()->elements_namespace ]);
        $drag_elements_html = implode("", $tm_drag_elements);
        foreach ($drag_elements as $key => $value) {
            $drag_elements_html .= implode("", $value);
        }

        $tm_tags = $tags[ TM_EPO_BUILDER()->elements_namespace ];
        unset($tags[ TM_EPO_BUILDER()->elements_namespace ]);

        //$tags = array_unique($tags,SORT_REGULAR);//requires php > 5.2.9
        $tags = array_map('unserialize', array_unique(array_map('serialize', $tags)));

        $tag_counter=1;
        $out = '<div class="transition tm-tabs tm-tags-container">';
        
            $out .= '<div class="transition tm-tab-headers">';
                $out   .= '<div class="tm-box tma-tab-label">'
                        . '<h4 class="tab-header open" data-tm-tag="'.esc_attr("all").'" data-id="tc-tag'.$tag_counter.'-tab">'.__( 'All', 'woocommerce-tm-extra-product-options' ).'</h4>'
                        . '</div>';
            foreach ($tm_tags as $key => $value) {
                $tag_counter++;
                $out   .= '<div class="tm-box tma-tab-label">'
                        . '<h4 class="tab-header closed" data-tm-tag="tc-'.esc_attr($key).'" data-id="tc-tag'.$tag_counter.'-tab">'.$value.'</h4>'
                        . '</div>';
            }            
            foreach ($tags as $key => $value) {
                $tag_counter++;
                $out   .= '<div class="tm-box tma-tab-label">'
                        . '<h4 class="tab-header closed" data-tm-tag="tc-'.esc_attr(sanitize_title($key)).'" data-id="tc-tag'.$tag_counter.'-tab">'.$key.'</h4>'
                        . '</div>';
            }
            $out .= '</div>';

            $out .= '<div class="transition tm-tab tc-tag'.$tag_counter.'-tab">';
                $out .= '<ul class="tm-elements-container tm-bsbb-all">';
                $out .= $drag_elements_html;
                $out .= '</ul>';
            $out .= '</div>';

        $out .= '</div>';

        return $out;
    }

    /**
     * Init List table class
     */
    private function get_wp_list_table($class="", $args = array()){        
        //require_once( 'class-tm-epo-list-table.php' );
        $args['screen'] =  convert_to_screen( TM_EPO_GLOBAL_POST_TYPE );
        return new $class( $args );
    }
    
    public function import_array_merge( $tm_metas,$import ) {
        $clean_import=array();
        if (!isset($tm_metas['tm_meta']['tmfbuilder'])){
            $tm_metas['tm_meta']['tmfbuilder']=array();
        }
        foreach ($import['tm_meta']['tmfbuilder'] as $key => $value) {
            if (!isset($tm_metas['tm_meta']['tmfbuilder'][$key])){
                $tm_metas['tm_meta']['tmfbuilder'][$key]=array();
            }    
            $tm_metas['tm_meta']['tmfbuilder'][$key]=(array_merge($tm_metas['tm_meta']['tmfbuilder'][$key],$value));
        }
        return $tm_metas;
    }

    /**
     * Save our meta data
     */
    public function tm_save_postdata( $post_id,$post_object ) {
        if ( empty($_POST) || !isset($_POST['post_type']) || TM_EPO_GLOBAL_POST_TYPE != $_POST['post_type'] )  {
            return;
        }
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
            return $post_id;
        }
        if ( $post_object->post_type == 'revision' ){
            return;
        }
        check_admin_referer('update-post_' . $post_id);

        if ( ! current_user_can( 'edit_post', $post_id ) ){
            return $post_id;
        }
        
        if (!isset($_SESSION)){
            session_start();
        }
        $import=false;
        if (isset($_SESSION['import_csv'])){
            $import=$_SESSION['import_csv'];
        }

        if ( isset($_POST['tm_meta_serialized'])){
            $tm_metas = $_POST['tm_meta_serialized'];            
            $tm_metas = stripslashes_deep($tm_metas);
            $tm_metas = rawurldecode($tm_metas);
            $tm_metas = nl2br($tm_metas);
            $tm_metas = json_decode($tm_metas, true);

            if($tm_metas){
                if (!empty($import)){
                    if (!empty($_SESSION['import_override'])){
                        $tm_metas=$import;
                        unset($_SESSION['import_override']);
                    }else{
                        $tm_metas=$this->import_array_merge($tm_metas,$import);    
                    }                    
                    unset($_SESSION['import_csv']);
                }
                if ( !empty($tm_metas) && is_array($tm_metas) && isset($tm_metas['tm_meta']) && is_array($tm_metas['tm_meta'])){
                    $tm_meta=$tm_metas['tm_meta'];                    
                    $old_data = get_post_meta($post_id, 'tm_meta',true);
                    $this->tm_save_meta($post_id, $tm_meta, $old_data, 'tm_meta');
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
                    $this->tm_save_meta($post_id, $tm_meta, $old_data, 'tm_meta_wpml');
                }else{
                    $this->tm_save_meta($post_id, false, $old_data, 'tm_meta_wpml');
                }
                
            }             
        }
        if ( isset($_POST['tm_meta_product_ids']) ){
            if (!is_array($_POST['tm_meta_product_ids'])){
                $_POST['tm_meta_product_ids']=explode(",", $_POST['tm_meta_product_ids']);
            }
            $old_data = get_post_meta($post_id, 'tm_meta_product_ids',true);
            $this->tm_save_meta($post_id, $_POST['tm_meta_product_ids'], $old_data, 'tm_meta_product_ids');
        }else{
            $old_data = get_post_meta($post_id, 'tm_meta_product_ids',true);
            $this->tm_save_meta($post_id, array(), $old_data, 'tm_meta_product_ids');            
        }
        if ( isset($_POST['tm_meta_disable_categories']) ){
            $old_data = get_post_meta($post_id, 'tm_meta_disable_categories',true);
            $this->tm_save_meta($post_id, $_POST['tm_meta_disable_categories'], $old_data, 'tm_meta_disable_categories');            
        }else{
            $old_data = get_post_meta($post_id, 'tm_meta_disable_categories',true);
            $this->tm_save_meta($post_id, 0, $old_data, 'tm_meta_disable_categories');
        }

        if ( isset($_POST['tm_meta_enabled_roles']) ){
            $old_data = get_post_meta($post_id, 'tm_meta_enabled_roles',true);
            $this->tm_save_meta($post_id, $_POST['tm_meta_enabled_roles'], $old_data, 'tm_meta_enabled_roles');
        }else{
            $old_data = get_post_meta($post_id, 'tm_meta_enabled_roles',true);
            $this->tm_save_meta($post_id, array(), $old_data, 'tm_meta_enabled_roles');            
        }
        if ( isset($_POST['tm_meta_disabled_roles']) ){
            $old_data = get_post_meta($post_id, 'tm_meta_disabled_roles',true);
            $this->tm_save_meta($post_id, $_POST['tm_meta_disabled_roles'], $old_data, 'tm_meta_disabled_roles');
        }else{
            $old_data = get_post_meta($post_id, 'tm_meta_disabled_roles',true);
            $this->tm_save_meta($post_id, array(), $old_data, 'tm_meta_disabled_roles');            
        }
        // WPML fields
        if (TM_EPO_WPML()->is_active()){
            if ( isset($_POST[TM_EPO_WPML_PARENT_POSTID]) ){
                $old_data = get_post_meta($post_id, TM_EPO_WPML_PARENT_POSTID,true);
                $this->tm_save_meta($post_id, $_POST[TM_EPO_WPML_PARENT_POSTID], $old_data, TM_EPO_WPML_PARENT_POSTID);            
            }
            if ( isset($_POST[TM_EPO_WPML_LANG_META]) && !empty($_POST[TM_EPO_WPML_LANG_META])){
                $old_data = get_post_meta($post_id, TM_EPO_WPML_LANG_META,true);
                $this->tm_save_meta($post_id, $_POST[TM_EPO_WPML_LANG_META], $old_data, TM_EPO_WPML_LANG_META);            
            }else{
                $old_data = get_post_meta($post_id, TM_EPO_WPML_LANG_META,true);
                $this->tm_save_meta($post_id, TM_EPO_WPML()->get_default_lang(), $old_data, TM_EPO_WPML_LANG_META);
            }
        }

    }

    public function tm_save_meta($post_id, $new_data=false, $old_data=false, $meta_name){
        if(empty($old_data) && $old_data==''){
            $test=add_post_meta($post_id, $meta_name, $new_data, true);
            if (!$test){
                $test=update_post_meta($post_id, $meta_name, $new_data, $old_data);
            }
        }else if($new_data===false || (is_array($new_data) && !$new_data)){
            $test=delete_post_meta($post_id, $meta_name);
        }else if($new_data !== $old_data){
            $test=update_post_meta($post_id, $meta_name, $new_data, $old_data);
        }
    }

    /**
     * Init List table class
     */
    public function admin_screen() {
        global $bulk_counts,$bulk_messages,$general_messages;

        $post_type          = $this->tm_list_table->screen->post_type;
        $post_type_object   = get_post_type_object( $post_type );

        $parent_file        = "edit.php?post_type=product&page=".TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
        $submenu_file       = "edit.php?post_type=product&page=".TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK;
        $post_new_file      = "edit.php?post_type=product&page=".TM_EPO_GLOBAL_POST_TYPE_PAGE_HOOK."&action=add";  
        
        $doaction           = $this->tm_list_table->current_action();
        if ($doaction && in_array($doaction, array('add','export','clone','trash','untrash','delete','editpost','edit','import','download'))){
            $screen = get_current_screen();
            
            // edit screen
            if ($_REQUEST['action']=='edit' && (isset($_REQUEST['post']) || isset( $_POST['post_ID'] )) ){
                if ( isset( $_GET['post'] ) ){
                    $post_id = $post_ID = (int) $_GET['post'];
                }elseif ( isset( $_POST['post_ID'] ) ){
                    $post_id = $post_ID = (int) $_POST['post_ID'];
                }
                if (!empty($post_id)){
                    $editing = true;
                    $post = get_post($post_id, OBJECT, 'edit');
                    if ( $post ) {
                        $post_type          = $post->post_type;
                        $post_type_object   = get_post_type_object( $post_type );
                        $title              = $post_type_object->labels->edit_item;
                        $nonce_action       = 'update-post_' . $post_ID;

                        $original_id = $post_ID;
                        if (!TM_EPO_WPML()->is_original_product($post->ID,$post->post_type)){
                            $original_id=TM_EPO_WPML()->get_original_id($post->ID,$post->post_type);
                            $original_post=get_post($original_id, OBJECT, 'edit');
                        }
                        $_meta                      = get_post_meta( $original_id ,'tm_meta');
                        $_meta_product_ids          = get_post_meta( $original_id ,'tm_meta_product_ids', true);
                        $_meta_enabled_roles        = get_post_meta( $original_id ,'tm_meta_enabled_roles', true);
                        $_meta_disabled_roles       = get_post_meta( $original_id ,'tm_meta_disabled_roles', true);
                        $_meta_disable_categories   = get_post_meta( $original_id ,'tm_meta_disable_categories', true);
                        $meta_fields        = array(
                            'priority'      => 10,                            
                            'can_publish'   => current_user_can($post_type_object->cap->publish_posts)
                        );
                        $meta = array();
                        foreach ( $meta_fields as $key=>$value ) {
                            $meta[$key] = isset( $_meta[0][ $key ] ) ? maybe_unserialize( $_meta[0][ $key ] ) : $value;

                        }
                        unset($_meta);
                        $meta['product_ids']        = $_meta_product_ids;
                        $meta['enabled_roles']      = $_meta_enabled_roles;
                        $meta['disabled_roles']     = $_meta_disabled_roles;
                        $meta['disable_categories'] = $_meta_disable_categories;
                        $post->tm_meta=$meta;
                        unset($meta);

                        wp_enqueue_script('post');
                        include ('views/html-tm-epo-fields-edit.php');
                    }                    
                }
            // add screen
            }elseif ($_REQUEST['action']=='add' ){
                $post_type = $this->tm_list_table->screen->post_type;
                $post_type_object = get_post_type_object( $post_type );
                
                $parent_post_meta = array();
                $parent_post_meta_product_ids=array();
                $parent_post_meta_enabled_roles=array();
                $parent_post_meta_disabled_roles=array();
                $parent_post_meta_disable_categories=1;

                // WPML
                if(TM_EPO_WPML()->is_active()){
                    if (isset($_GET['tmparentpostid'])){
                        $parent_post = get_post((int) $_GET['tmparentpostid'], OBJECT, 'edit');
                        $parent_post_meta              = get_post_meta( $parent_post->ID ,'tm_meta');
                        $parent_post_meta_product_ids  = get_post_meta( $parent_post->ID ,'tm_meta_product_ids', true);
                        $parent_post_meta_enabled_roles  = get_post_meta( $parent_post->ID ,'tm_meta_enabled_roles', true);
                        $parent_post_meta_disabled_roles  = get_post_meta( $parent_post->ID ,'tm_meta_disabled_roles', true);
                        $parent_post_meta_disable_categories  = get_post_meta( $parent_post->ID ,'tm_meta_disable_categories', true);
                        TM_EPO_WPML()->apply_wp_terms_checklist_args_filter((int) $_GET['tmparentpostid']);
                    }
                }

                $post = get_default_post_to_edit( $post_type, true );
                if ( $post ) {
                    $post_ID = $post_id = $post->ID;

                    // WPML
                    if(!empty($parent_post)){
                        $post->post_title = $parent_post->post_title;
                        $post->post_excerpt = $parent_post->post_excerpt;
                    }

                    $title          = $post_type_object->labels->add_new;
                    $nonce_action   = 'update-post_' . $post_ID;
                    
                    $_meta = array();
                    $meta_fields = array_merge(array(
                        'priority' => 10,
                        'can_publish' => current_user_can($post_type_object->cap->publish_posts)
                    ),$parent_post_meta);
                    $meta = array();
                    foreach ( $meta_fields as $key=>$value ) {
                        $meta[$key] = isset( $_meta[0][ $key ] ) ? maybe_unserialize( $_meta[0][ $key ] ) : $value;
                    }
                    unset($_meta);
                    $meta['product_ids'] = $parent_post_meta_product_ids;
                    $meta['enabled_roles'] = $parent_post_meta_enabled_roles;
                    $meta['disabled_roles'] = $parent_post_meta_disabled_roles;

                    $meta['disable_categories'] = $parent_post_meta_disable_categories;
                    $post->tm_meta=$meta;
                    unset($meta);
                    wp_enqueue_script('post');
                    include ('views/html-tm-epo-fields-edit.php');
                }
            }
        // list screen            
        }else{            
            $this->tm_list_table->prepare_items();
            wp_enqueue_script('inline-edit-post');//list
            add_action( 'tm_list_table_action', array( $this, 'tm_list_table_action' ), 10, 2 );
            include ('views/html-tm-epo-fields.php');
        }
    }

    public function tm_list_table_action($action= "", $args=array() ){        
        if ( !$action ){
            return;
        }
        switch ( $action ){
        case "views":
            $this->tm_list_table->views();
            break;
        case "display":
            $this->tm_list_table->display();
            break;
        case "inline_edit":
            if ( $this->tm_list_table->has_items() ){
                $this->tm_list_table->inline_edit();
            }
            break;
        case "search_box":
            $this->tm_list_table->search_box( $args['text'], $args['input_id'] );
            break;
        default:
            break;            
        }
    }
    public function tm_export_form_action($post=0 ){
        
        $csv = new TM_EPO_ADMIN_CSV();
        $csv->export_by_id($post);

    }
    public function tm_clone_form_action($original_id=0){        
        // Get access to the database
        global $wpdb;
        
        // Check the nonce
        check_ajax_referer( 'tmclone_form_nonce_'.$original_id, 'security' );
                
        // Get the post as an array
        $duplicate = get_post( $original_id, 'ARRAY_A' );

        // Modify some of the elements
        $duplicate['post_title'] = $duplicate['post_title'].' '.__("Copy",'woocommerce-tm-extra-product-options');
        
        // Set the status
        $duplicate['post_status'] = 'draft';        

        // Set the post date
        $timestamp = current_time('timestamp',0);
        $duplicate['post_date'] = date('Y-m-d H:i:s', $timestamp);

        // Remove some of the keys
        unset( $duplicate['ID'] );
        unset( $duplicate['guid'] );
        unset( $duplicate['comment_count'] );

        // Insert the post into the database
        $duplicate_id = wp_insert_post( $duplicate );
        
        // Duplicate all the taxonomies/terms
        $taxonomies = get_object_taxonomies( $duplicate['post_type'] );
        foreach( $taxonomies as $taxonomy ) {
            $terms = wp_get_post_terms( $original_id, $taxonomy, array('fields' => 'names') );
            wp_set_object_terms( $duplicate_id, $terms, $taxonomy );
        }

        // Duplicate all the custom fields
        $custom_fields = get_post_custom( $original_id );
        foreach ( $custom_fields as $key => $value ) {
            if($key == "tm_meta"){
                add_post_meta( $duplicate_id, $key, TM_EPO_HELPER()->recreate_element_ids($value[0]) );
            }
            else if ($key!=TM_EPO_WPML_LANG_META && $key!=TM_EPO_WPML_PARENT_POSTID){
                add_post_meta( $duplicate_id, $key, maybe_unserialize($value[0]) );
            }            
        }

    }

}

?>