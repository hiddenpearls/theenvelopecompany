<?php
function tesla_has_woocommerce() {
    static $flag = NULL;
    if ($flag === NULL) {
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))){
            $flag = TRUE;
        }elseif(is_multisite()){
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            if(is_plugin_active_for_network('woocommerce/woocommerce.php'))
                $flag = TRUE;
        }else{
            $flag = FALSE;
        }
    }
    return $flag;
}

function tt_get_mailchimp( $url, $postdata = array( ), $grab_error = false, $get_response = false ) {
    $args = array(
        'sslverify' => false,
        'body'      => json_encode($postdata)
        );
    if ( ! empty( $postdata ) ) 
        $response = wp_remote_post( $url, $args );
    else
        $response = wp_remote_get( $url, $args );
    
    if ( empty( $response ) || is_wp_error( $response ) ){
        if($grab_error)
            return $response->get_error_message();
    } else {
        $data = json_decode( wp_remote_retrieve_body($response) );
        if ( $get_response && empty($data->error) )
            return $data->data;
        elseif( empty($data->error) )
            return TRUE;
        elseif( $grab_error )
            return $data->error;
    }
    return FALSE;
}

function tt_get_file_contents( $url ) {
    $response = wp_remote_get( $url );
    if ( !is_wp_error($contents) )
        return wp_remote_retrieve_body( $response );
    else 
        return FALSE;
}

function _gstyle_changer($id,$units = 'px'){
    $color = (_go($id."_color"))? "color:"._go($id."_color").";":'';
    $font = (_go($id."_font"))? "font-family:"._go($id."_font").";":'';
    $size = (_go($id."_size"))? "font-size:"._go($id."_size")."$units;":'';
    return array('color'=>$color,'font'=>$font,'size'=>$size);
}

function _estyle_changer($id,$units = 'px'){
    $color = (_go($id."_color"))? "color:"._go($id."_color").";":'';
    $font = (_go($id."_font"))? "font-family:"._go($id."_font").";":'';
    $size = (_go($id."_size"))? "font-size:"._go($id."_size")."$units;":'';
    print $color.$font.$size;
}

function _gcustom_styler($repeater_id){
    $style = "";
    foreach (_go_repeated($repeater_id) as $styler_index => $styler) {
        if ( !empty($styler['custom_selector']) && ( !empty($styler['custom_color']) || !empty($styler['custom_bg_color']) ) ){
            $style .= $styler['custom_selector'] . "{" ;
            $important = !empty($styler['important']) ? " !important" : "";
            $style .= !empty($styler['custom_color']) ? "color: " . $styler['custom_color']  . $important . ";" : "";
            $style .= !empty($styler['custom_bg_color']) ? "background-color: " . $styler['custom_bg_color']  . $important . ";" : "";
            $style .=  "}";
        }
    }
    return $style;
}

function tt_text_css($option_id,$selector,$units = 'px'){
    $style = $selector . "{" ;
    $settings = _gstyle_changer($option_id,$units);
    foreach ($settings as $setting => $value) {
        $style .= $value;
    }
    $style .=  "}";
    if($style == "$selector{}")
        return NULL;
    return $style;
}

function _esocial_platforms($social_platforms = array(
    'facebook',
    'twitter',
    'pinterest',
    'flickr',
    'dribbble',
    'behance',
    'google',
    'linkedin',
    'youtube',
    'rss'),$prefix='',$suffix='',$fa=false){
    foreach($social_platforms as $platform): 
        if (_go('social_platforms_' . $platform)):?>
            <li>
                <a href="<?php _eo('social_platforms_' . $platform) ?>" target="_blank">
                    <?php if($fa) : ?>
                        <i class="fa fa-<?php echo esc_attr($platform); ?>"></i>
                    <?php else: ?>
                        <img src="<?php echo TT_THEME_URI ?>/images/socials/<?php echo esc_attr($prefix.$platform.$suffix) ?>.png" alt="<?php echo esc_attr($platform) ?>" />
                    <?php endif; ?>
                </a>
            </li>
        <?php endif;
    endforeach;
}

//==========Form Builder functions=============
//Gets one form from contact builder  by id
function tt_get_form($id){
    $forms = get_option( THEME_OPTIONS . '_forms' );
    if(!empty($forms)){
        $the_form = NULL;
    foreach ($forms as $key => $form) {
        if($form['id'] == $id){
            $the_form = $form;
            break;
        }
    }
    if($the_form)
        return $the_form;
    else
        return NULL;
    }else
    return FALSE;
}

//Displays one form from contact builder  by id
function tt_form($id){
    $the_form = tt_get_form($id);
    if($the_form)
        TT_Contact_Form_Builder::render_form($id,$the_form);
    else
        return NULL;
}

//Gets all the forms from contact form builder
function tt_get_forms(){
    $forms = get_option( THEME_OPTIONS . '_forms' );
    return $forms;
}

//gets all the forms by location
function tt_form_location($location){
    $forms = tt_get_forms();
    if(!empty($forms)){
        foreach ($forms as $form) {
            if($form['location'] === $location)
                tt_form($form['id']);
        }
    }else
        return FALSE;
}

function tt_get_page_id($shop=false){
    global $wp_query;
    if(is_archive())
        return false;
    if(get_query_var('page_id'))
        $page_id = get_query_var('page_id');
    elseif(!empty($wp_query->queried_object) && !empty($wp_query->queried_object->ID))
        $page_id = $wp_query->queried_object->ID;
    elseif($shop)
        $page_id = get_option( 'woocommerce_shop_page_id' );
    else
        $page_id = false;
    return $page_id;
}

function tt_get_plugin_version($plugin_folder){
    $file_path = glob( TT_THEME_DIR . "/plugins/$plugin_folder/*.zip");
    try{
        if(empty($file_path[0]))
            throw new Exception("==========Plugin $plugin_folder file not found.=========");
        $filename = basename($file_path[0]);
        preg_match('@_([0-9.]+).zip@is', $filename, $file_version);
        if(empty($file_version[1]))
            throw new Exception("======Plugin $plugin_folder file name does not contain the version.======");
        return $file_version[1];
    }catch (Exception $e) {
        print $e->getMessage();
    }
}

/**
* Backwards compatibility for : Moving adding shortcode to fw ( in the plugin version )
* @since 1.9.4
*/
if(!function_exists('tt_register_sc')){
    function tt_register_sc($tag, $func){
        add_shortcode( $tag, $func );
    }
}