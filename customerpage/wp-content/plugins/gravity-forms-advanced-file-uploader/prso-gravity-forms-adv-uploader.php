<?php
/*
 * Plugin Name: Gravity Forms Advanced File Uploader
 * Plugin URI: https://github.com/pressoholics/prso-gravity-forms-adv-uploader
 * Description: Multiple file uploader with advanced options for Gravity Forms plugin.
 * Author: Benjamin Moody
 * Version: 1.21
 * Author URI: http://www.benjaminmoody.com
 * License: GPL2+
 * Text Domain: prso_gforms_adv_uploader_plugin
 * Domain Path: /languages/
 */

//Define plugin constants
define( 'PRSOGFORMSADVUPLOADER__MINIMUM_WP_VERSION', '3.0' );
define( 'PRSOGFORMSADVUPLOADER__VERSION', '1.21' );
define( 'PRSOGFORMSADVUPLOADER__DOMAIN', 'prso_gforms_adv_uploader_plugin' );

//Plugin admin options will be available in global var with this name, also is database slug for options
define( 'PRSOGFORMSADVUPLOADER__OPTIONS_NAME', 'prso_gforms_adv_uploader_options' );

define( 'PRSOGFORMSADVUPLOADER__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PRSOGFORMSADVUPLOADER__PLUGIN_URL', plugin_dir_url( __FILE__ ) );

//Include plugin classes
require_once( PRSOGFORMSADVUPLOADER__PLUGIN_DIR . 'class.prso-gravity-forms-adv-uploader.php'               );

//Set Activation/Deactivation hooks
register_activation_hook( __FILE__, array( 'PrsoGformsAdvUploader', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'PrsoGformsAdvUploader', 'plugin_deactivation' ) );

//Set plugin config
$config_options = array();

//Instatiate plugin class and pass config options array
new PrsoGformsAdvUploader( $config_options );