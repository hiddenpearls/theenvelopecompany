<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
    die();
}

final class TM_EPO_COMPATIBILITY_q_translate_x {

    protected static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {

        add_action( 'wc_epo_add_compatibility', array( $this, 'add_compatibility' ) );

    }

    public function init() {
        
    }

    public function add_compatibility(){
        /** Q-translate-X support **/
        add_filter( 'tm_translate', array( $this, 'tm_translate' ), 50, 1 );
        if (function_exists('qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage')){
            add_filter( 'tm_translate', 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage', 51, 1 );
        }
    }

    /** Q-translate-X support **/
    public function tm_translate($text=""){
        return $text;
    }

}


?>