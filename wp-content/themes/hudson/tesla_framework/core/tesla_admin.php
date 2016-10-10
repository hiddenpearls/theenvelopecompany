<?php
if(!class_exists('Tesla_admin')){
  class Tesla_admin extends TeslaFramework {

    public $admin_options;

    public $tesla_update_checker;

    public function __construct() {
      parent::__construct();
      //loading helpers
      $this->load->helper( 'admin' );
      //Generating admin panel
      $this->admin_options = (file_exists(TT_STYLE_DIR . '/theme_config/admin-options.php'))?  include TT_STYLE_DIR . '/theme_config/admin-options.php' : include TT_THEME_DIR . '/theme_config/admin-options.php';
      do_action('tt_change_admin_options',$this);   //use this hook to customize admin options in child themes
      $this->register_admin_settings();
      $this->add_admin_menu_page();
      $this->import_xml();
      $this->export_import_options();
      $this->autoupdate();
      if($this->tesla_security->username == 'themeforest' && !defined('TT_HIDE_BANNER'))
        define('TT_HIDE_BANNER',true);
      add_action( 'admin_init', array($this, 'tt_serve_export_file' ) );
      //following block is removed in tf branch
      if( !defined('TT_HIDE_BANNER') ) {
        if(!get_transient( 'tt_notice_dismissed' )){
          add_action( 'admin_notices', array( $this , 'tt_purchase_notice') );
          add_action( 'admin_footer' , array( $this , 'tt_purchase_notice_script' ));
          add_action( 'wp_ajax_tt_dismiss_notice', array($this,'tt_dismiss_notice_ajax'));
        }
      }
    }

    private function register_admin_settings() {
      add_action( 'admin_init', array( $this, 'theme_options_init' ) );
      add_action( 'init', array( $this, 'theme_options_defaults' ) );
    }

    function theme_options_init() {
      //-------theme settings--------------------------------
      register_setting( THEME_OPTIONS, THEME_OPTIONS );
      add_action('wp_ajax_save_options', array($this,'save_options_ajax'));
      add_action('wp_ajax_clear_subscriptions', array($this,'clear_subscriptions'));
      //---------our themes section disable
      if(!empty($_GET['disabled']) && $_GET['disabled'] == 'our_themes'){
        add_option( THEME_NAME . '_our_themes', true );
      }
    }

    function save_options_ajax() {
      //check_ajax_referer('test-theme-data', 'security');
      if(!wp_verify_nonce( $_POST['tesla-options-nonce'] ))
        die('Security Breach');
      $options = $_POST[THEME_OPTIONS];

      if(!empty($options)) {
        $options = stripslashes_deep($options);
        $result = update_option(THEME_OPTIONS, $options);
        if($result) {
          die('options updated');
        } else {
          die('options did not change');
        }
      } else {
        die('No data sent');
      }
      die();
    }

    function clear_subscriptions(){
      if(update_option( THEME_OPTIONS . '_subscribers' ))
        die('Done');
      die('Error');
    }

    function theme_options_defaults() {

      $my_var_that_holds_options = get_option( THEME_OPTIONS ); //getting theme options from DB , if no options FALSE returned
      if ( !$my_var_that_holds_options ) {   //checking if no theme options where setup (first time use of theme)
        $result = seek_options( $this->admin_options, 'id' ); //getting all fields with key = 'id' from theme options array
        $ids = explode( ' ', trim( $result ) );
        foreach ( $ids as $id ) {  //building defaults as ''
          $defaults[$id] = '';
        }
        
        update_option( THEME_OPTIONS, $defaults );  //Inserting defaults to DB
      }
    }

    private function add_admin_menu_page() {
      //-------Menu add admin page-------------------------
      add_action( "admin_menu", array( $this, "setup_theme_admin_menus" ) );
    }

    function setup_theme_admin_menus() {
      if ( !empty($this) ){
        if( !$this->function_checks() ){
          $this->tesla_security->state = 'corrupt';
          if(method_exists('TT_Security','throw_errors'))
            $this->tesla_security->throw_errors();
          //else
          return;
        }
      }else
        return;
      
      $theme_admin_page = add_menu_page( 'Theme settings', THEME_PRETTY_NAME, 'manage_options', THEME_NAME . '_options', array( $this, 'theme_options_do_page' ), $this->get_admin_favico_dir() );
      add_action( 'load-' . $theme_admin_page, array($this,'load_main_page' ) );
    }

    public function get_admin_favico_dir() {
      $favico_dir = ( ! empty( $this->admin_options[ 'favico' ][ 'dir' ] ) ) ? TT_THEME_URI . $this->admin_options[ 'favico' ][ 'dir' ] : '';
      return $favico_dir;
    }

    function theme_options_do_page() {
      if ( !class_exists( 'TT_Security' ) ){
        return;
      }else if(!$this->tesla_security->check_state())
        return;
      $this->load->view( 'admin', $this->admin_options );  //Loading Theme Options Admin Panel View
    }

    function load_main_page(){
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_panel_page_head' ) );
    }

    //-------adding css nad javascript to admin head--------
    function admin_panel_page_head() {
      //enqueue scripts-----------
      echo "<script type='text/javascript'>var TT_FW = '".TT_FW."',THEME_NAME='".THEME_NAME."',updated=false</script>"; // Tesla Framework directory ,theme name, and updated options variable passed to js side
      wp_enqueue_script('tt-gmaps','https://maps.googleapis.com/maps/api/js?key=AIzaSyC3fn4SbdFYvgazSsXeeVe6kqy0RynSuhE&v=3.exp&sensor=false&libraries=places'); //google maps api in admin head
      if ( ! empty( $this->admin_options[ 'scripts' ] ) ) {
        foreach ( $this->admin_options[ 'scripts' ] as $script ) {
          if ( is_array( $script ) ) {
            foreach ( $script as $included_script )
              wp_enqueue_script( $included_script );
          }else
            wp_enqueue_script( 'admin-' . $script, TT_FW . '/static/js/' . $script . '.js', array( 'jquery','admin-codemirror-core-js' ) );
        }
      }else
        wp_enqueue_script( 'admin-bootstrap', TT_FW . '/static/js/bootstrap.js', array( 'jquery' ) );
      //enqueue styles------------
      if ( ! empty( $this->admin_options[ 'styles' ] ) ) {
        foreach ( $this->admin_options[ 'styles' ] as $style ) {
          if ( is_array( $style ) ) {
            foreach ( $style as $included_style )
              wp_enqueue_style( $included_style );
          }else
            wp_enqueue_style( 'admin-css-' . $style, TT_FW . '/static/css/' . $style . '.css' );
        }
      }else
        wp_enqueue_style( 'admin-bootstrap', TT_FW . '/static/css/bootstrap.css' );
      if ( function_exists( 'wp_enqueue_media' ) )
        wp_enqueue_media();
      if ( function_exists( 'add_thickbox' ) )
        add_thickbox();
      wp_enqueue_style( 'jquery-ui-css', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css' );
      //Codemirror--------------
      wp_enqueue_script( 'admin-codemirror-core-js', TT_FW . '/extensions/codemirror/codemirror.js', array( 'jquery' ) );
      wp_enqueue_script( 'admin-codemirror-css-mode-js', TT_FW . '/extensions/codemirror/mode/css/css.js', array( 'admin-codemirror-core-js' ) );
      wp_enqueue_script( 'admin-codemirror-javascript-mode', TT_FW . '/extensions/codemirror/mode/javascript/javascript.js', array( 'admin-codemirror-core-js' ) );
      wp_enqueue_script( 'admin-codemirror-addon-closebrakets', TT_FW . '/extensions/codemirror/addon/closebrackets.js', array( 'admin-codemirror-core-js' ) );
      wp_enqueue_script( 'admin-codemirror-addon-matchbrakets', TT_FW . '/extensions/codemirror/addon/matchbrackets.js', array( 'admin-codemirror-core-js' ) );
      wp_enqueue_style( 'admin-codemirror-core-css', TT_FW . '/extensions/codemirror/codemirror.css' );
      wp_enqueue_style( 'admin-codemirror-theme-css', TT_FW . '/extensions/codemirror/theme/mdn-like.css' );
      
    }

    /**
   * ======================================Auto import XML DEMO CONTENT================================================================
   */
    function load_additional_pages(){
      add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_css_js_additional_pages' ) );
    }

    function enqueue_css_js_additional_pages(){
      wp_enqueue_script( 'jquery-form',array('jquery'),false,true ); 
      wp_enqueue_script( 'export-import', TT_FW . '/static/js/export_import.js', array( 'jquery' ) );
      wp_enqueue_style('main-css-admin',TT_FW . "/static/css/style.css");
      ?>
      <script type='text/javascript'>var TT_FW = '<?php echo TT_FW?>',THEME_NAME='<?php echo THEME_NAME?>'</script>
      <?php
    }

    function import_xml(){
      //add_action( 'after_switch_theme', array($this,'import_xml_add_admin_page' ));
      add_action('admin_menu',array($this,'import_add_admin_page'));
      add_action('wp_ajax_import_xml', array($this,'import_xml_ajax'));
    }

    function import_add_admin_page(){
      $theme_admin_page = add_submenu_page(THEME_NAME . '_options', 'Import demo xml', 'Import Demo', 'manage_options', 'tt_fw_import', array($this, 'autoimport_page'));
      add_action( 'load-' . $theme_admin_page, array($this,'load_additional_pages' ) );
    }

    function autoimport_page() {
      $multiple = file_exists(TT_THEME_DIR . '/theme_config/import_2.xml') ? 2 : 1;
      $multiple = file_exists(TT_THEME_DIR . '/theme_config/import_3.xml') ? 3 : $multiple;
      $multiple = file_exists(TT_THEME_DIR . '/theme_config/import_4.xml') ? 4 : $multiple;
      $multiple = file_exists(TT_THEME_DIR . '/theme_config/import_5.xml') ? 5 : $multiple;
      $multiple = file_exists(TT_THEME_DIR . '/theme_config/import_6.xml') ? 6 : $multiple;
      ?>
      <div class="wrap">
        <div id='result_content' class="welcome-panel">
          <div class="welcome-panel-content">
            <div id="tt_import_alert">
              <span>Warning !</span>
              Importing Demo Content will add posts and media files to your WordPress. It is not recommended to do it if you already have your own content.
              It would be better if you back up your data before importing the demo content.
            </div>
            <button class='button button-primary button-hero' id='import_xml_button' data-multiple='<?php echo esc_attr($multiple) ?>'>Import Demo Content</button>
            <div id='result'></div>
            <div>
              &nbsp;
              <span class="spinner"></span>
            </div>
          </div>
        </div>
      </div>
      <?php
      return;
    }

    function import_xml_ajax(){
      header('Content-type: text/html; charset=utf-8');

      $import_status = get_option( 'tt_import_' . THEME_NAME );
      $first_step_imported = !empty($import_status) && !empty($import_status[0]);

      $step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
      $total_steps = !empty($_POST['total_steps']) ? (int)$_POST['total_steps'] : 1;
      if($step === 1 && $first_step_imported && $total_steps > 1)
        die("<div class='tt_successful_import'>File import.xml 1/{$total_steps} was previouslly imported. Skipping to next file.</div>");

      $multiple = !empty($step) && $step > 1 ? '_' . $step : '';
      if($step === 1)
        update_option( 'tt_import_' . THEME_NAME, array( 0, $total_steps ) );

      require_once TT_FW_DIR . '/extensions/autoimport/autoimporter.php';
      if ( ! class_exists( 'Auto_Importer' ) )
        die( 'Auto_Importer not found' );
      
      // call the auto import function from auto_importer class
      $args = array(
        'file'        => TT_THEME_DIR . '/theme_config/import'.$multiple.'.xml',
        'map_user_id' => 1
      );

      auto_import( $args );
      if($step == 1 && file_exists(TT_THEME_DIR . '/theme_config/demo_widgets.wie')){
        require TT_FW_DIR . '/extensions/widgetimport/widgetimport.php';
        $import_widgets_result = tt_process_import_file(TT_THEME_DIR . '/theme_config/demo_widgets.wie');
        tt_show_import_results($import_widgets_result);
      }
      
      if($step >= $total_steps){
        update_option( 'tt_import_' . THEME_NAME, array( $step, $total_steps, true ) );
        echo "<div class='tt_successful_import'>Demo content successfully imported. Thank you for your patience. Next you should setup your front and blog pages in Settings -> Reading. Also menus and widgets in Appearance menu.</div>";
      }else{
        update_option( 'tt_import_' . THEME_NAME, array( $step, $total_steps ) );
        echo "<div class='tt_successful_import'>File import$multiple.xml {$step}/{$total_steps} successfully imported. Proceeding to next file.</div>";
      }
      die();
    }
  //=============================================END AUTOIMPORT DEMO CONTENT XML====================================================

    //=============================================EXPORT IMPORT Options start====================================================
    function export_import_options(){
      add_action('admin_menu',array($this,'export_import_add_admin_page'));
      add_action('wp_ajax_options_actions', array($this,'options_actions_ajax'));
    }

    function export_import_add_admin_page(){
      $theme_admin_page=add_submenu_page(THEME_NAME . '_options', 'Export/Import Options', 'Export/Import Options', 'manage_options', 'tt_fw_export_import', array($this, 'export_import_page'));
      add_action( 'load-' . $theme_admin_page, array($this,'load_additional_pages' ) );
    }

    function options_actions_ajax(){
      
      $action = $_POST['option_action'];

      switch ($action) {
        case 'clear':
          if (update_option(THEME_OPTIONS, array()))
            die(true);
          else
            die(false);
          break;
        case 'reset':
          /** 
          * @updated : 1.9.3
          */
          $import_demo_options = unserialize(wp_remote_retrieve_body(wp_remote_get(TT_THEME_URI . "/theme_config/demo_options.txt")));
          if (!empty($import_demo_options)){
            if(!empty($import_demo_options['form_builder'])){
              update_option(THEME_OPTIONS,$import_demo_options['theme_options']);
              update_option(THEME_OPTIONS . '_forms',$import_demo_options['form_builder']);
            }else
              update_option(THEME_OPTIONS,$import_demo_options);
            die(true);
          }else
            die(__('Error','TeslaFramework'));
          break;
        case 'import':
          // HANDLE THE FILE UPLOAD
          // If the upload field has a file in it
          if(isset($_FILES['import_options']) && ($_FILES['import_options']['size'] > 0)) {
              // Get the type of the uploaded file. This is returned as "type/extension"
              $arr_file_type = wp_check_filetype(basename($_FILES['import_options']['name']));
              $uploaded_file_type = $arr_file_type['type'];
              // Set an array containing a list of acceptable formats
              $allowed_file_types = array('text/plain');
              // If the uploaded file is the right format
              if(in_array($uploaded_file_type, $allowed_file_types)) {
                  // Options array for the wp_handle_upload function. 'test_upload' => false
                  $upload_overrides = array( 'test_form' => false ); 
                  // Handle the upload using WP's wp_handle_upload function. Takes the posted file and an options array
                  $uploaded_file = wp_handle_upload($_FILES['import_options'], $upload_overrides);
                  // If the wp_handle_upload call returned a local path for the image
                  if(isset($uploaded_file['file'])) {
                      $import_options = unserialize(wp_remote_retrieve_body(wp_remote_get($uploaded_file['url'])));
                      if (!empty($import_options)){
                        if(!empty($import_options['form_builder'])){
                          update_option(THEME_OPTIONS,$import_options['theme_options']);
                          update_option(THEME_OPTIONS . '_forms',$import_options['form_builder']);
                        }else
                          update_option(THEME_OPTIONS,$import_options);
                        $upload_feedback = true;
                      }else
                        $upload_feedback = 'Invalid import file';
                  } else { // wp_handle_upload returned some kind of error. the return does contain error details, so you can use it here if you want.
                      $upload_feedback = 'There was a problem with your upload.';
                  }
              } else { // wrong file type
                  $upload_feedback = 'Please upload only txt files (text/plane).';
              }
          } else { // No file was passed
              $upload_feedback = 'No file passed';
          }
          die($upload_feedback);
          break;
        default:
          die(false);
          break;
      }
      die();
    }

    function export_import_page(){
      ?>
      <div class="wrap">
        <div id='result_content' class="welcome-panel">
          <div class="welcome-panel-content">
            <div id="tt_import_alert">
              <span>Warning !</span>
              Importing or clearing options will erase your theme current settings from <a href="<?php echo admin_url( 'admin.php?page=' . THEME_NAME . '_options') ?>"><?php echo THEME_PRETTY_NAME ?></a> page .
              Make sure you make a backup first by "Exporting Options".
            </div>
            <div id="controls">
              <button class='button' id='clear' data-action="Clearing">Clear Options</button>
              <button class='button button-primary' id='reset' data-action="Reseting">Import Demo Options</button>
              <button class='button' id='export' data-action="Exporting">Export Options</button>
              <button class='button' id='import' data-action="Importing">Import Options</button>
            </div>
            <form id="upload_form" method="post" enctype="multipart/form-data" action="options_actions">
              <input type="file" name="import_options">
              <input type="hidden" name="action" id="action" value="options_actions">
              <input type="hidden" name="option_action" id="action" value="import">
              <input type="submit" class="button button-primary" value="Import">
              <button class='button' id='cancel'>Cancel</button>
            </form>
            
            <progress value="0" max="100"></progress>
            <div id='result'></div>
            <p>&nbsp;</p>
          </div>
        </div>
      </div>
      <script type="text/javascript">
        var download_url  = "<?php echo admin_url( 'admin.php?page=tt_fw_export_import&tt_export_file=theme_options&nonce=' . wp_create_nonce( 'export-options' )) ?>";
      </script>
      <?php
    }

    /**
    * Serves export file for download
    */
    public function tt_serve_export_file(){
      if(!empty($_GET['tt_export_file']) && !empty($_GET['nonce']) ){
        if($_GET['tt_export_file'] == 'theme_options'){
          $filename = 'theme_options.txt';
        }
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $filename);

        if( get_option( THEME_OPTIONS . '_forms') ){ //if there is a form builder
          $all_theme_options = array(
            'theme_options' => get_option( THEME_OPTIONS ),
            'form_builder'  => get_option( THEME_OPTIONS . '_forms')
          );
        }else
          $all_theme_options = get_option(THEME_OPTIONS);
        echo serialize($all_theme_options) ; //write txt file
        die();
      }
    }

    //============================+AutoUpdate+==================================
    function autoupdate(){
      add_action( 'admin_init', array( $this, 'autoupdate_init' ) );
      add_action('wp_ajax_check_update', array($this,'check_update_ajax'));
      //ManageWp API integration
      add_filter('mwp_premium_perform_update', array($this, 'mytheme_mwp_perform_update'));
      add_filter('mwp_premium_update_notification', array($this,  'mytheme_mwp_update_notification' ));
      add_filter('mwp_premium_update_check', array($this, 'mytheme_mwp_update_check'));
    }

    function autoupdate_init(){
      //Initialize the update checker.
      require TT_FW_DIR . '/extensions/theme-updates/theme-update-checker.php';
      $this->tesla_update_checker = new ThemeUpdateChecker(
          THEME_FOLDER_NAME,
          'http://teslathemes.com/auto_update/?theme=' . THEME_NAME 
      );
      
      $this->tesla_update_checker->addResultFilter(array($this,'update_result_filter'));
    }

    function check_update_ajax(){
      $theme = $this->tesla_update_checker->requestUpdate();
      if(!empty($theme))
        $this->tesla_update_checker->checkForUpdates();
      die(json_encode($theme));
    }

    function update_result_filter($theme_update_instance,$remote_result){
      if($theme_update_instance){
        $date = strtotime( gmdate('Y-m-d H:i:s') );
        $username = $this->tesla_security->username;
        $key = $this->tesla_security->update_key;

        // EnCrypt string
        $token=base64_encode(serialize(array($date,$key,$username)));

        $theme_update_instance->download_url .= "&token=$token";
      }
      return $theme_update_instance;
    }

    //following block is removed in tf branch
    function tt_purchase_notice() {
      $api = get_transient( 'security_api_result' );
      $dashboard_link = "<a href='https://teslathemes.com/amember/member/?utm_source=banner&utm_medium=fw&utm_campaign=fw-banner' target='_blank'>TeslaThemes Dashboard</a>";
      $contact_link = "<a taget='_blank' href='http://teslathemes.com/contact/?utm_source=banner&utm_medium=fw&utm_campaign=fw-banner'>Contact us</a>";

      if( in_array( $this->tesla_security->username, array( 'tt_general_user','tt_other_marketplaces_user','creativemarket','mojomarketplace' ) ) ){
        $message = "Get all themes for only $10 $dashboard_link . Bought from 3rd party ? - $contact_link to get the upgrade discount.";
      }elseif(!empty($api) && !empty($api->subscriptions) && empty($api->subscriptions->{4}) && empty($api->subscriptions->{51}) && empty($api->subscriptions->{56})){
        if(!empty($api->subscriptions->{3}))
          $message = "Upgrade to Developer for only $20 in $dashboard_link.";
        else
          $message = "Get all themes for only $10. Upgrade in $dashboard_link.";
      }else{
        return;
      }
      echo "<div class='notice update-nag is-dismissible tt-purchase-notice'> <p>$message</p></div>"; 
    }

    //following block is removed in tf branch
    function tt_purchase_notice_script(){
      ?>
      <script type="text/javascript">jQuery('body').on('click','.tt-purchase-notice .notice-dismiss',function(){
        jQuery.post(ajaxurl, {action:'tt_dismiss_notice'});
      })</script>
      <?php
    }

    //following block is removed in tf branch
    function tt_dismiss_notice_ajax(){
      set_transient( 'tt_notice_dismissed' , true , 5 * DAY_IN_SECONDS );
    }
    
    /**********************************************

            Integration Manage WP for premium themes 

    *********************************************/


    // mwp_premium_update_notification filter
    //
    // Hook to this filter to provide the new version of your theme if available
    //

      function mytheme_mwp_update_notification( $premium_updates ){
          
          if( !function_exists( 'wp_get_theme' ))   // make sure we have the needed functions available
              if (!@include_once( ABSPATH.'wp-admin/includes/theme.php'))
                  return $premium_updates;    
              
          if(!function_exists( 'is_plugin_active') )
              if (!@include_once( ABSPATH.'wp-admin/includes/plugin.php'))
                  return $premium_updates;    
                  
          if (!(is_plugin_active('worker/init.php') || is_plugin_active('managewp/init.php'))) // ManageWP client plugin needed   
              return $premium_updates;
          
          if( function_exists( 'wp_get_theme' ) ){
              
              $themeobj = wp_get_theme();
              $mytheme['Name'] = $themeobj->Name;
              $mytheme['URI'] = $themeobj->ThemeURI;
              $mytheme['Template'] = $themeobj->Template;
              $mytheme['Version'] = $themeobj->Version;
              $mytheme['Status'] = $themeobj->Status;
          }
           
          $mytheme['old_version']=  $mytheme['Version'];
          $mytheme['type'] = 'theme';
          
          $new_theme = wp_remote_get( 'http://teslathemes.com/auto_update/?theme=' . THEME_NAME );
          $theme_meta = wp_remote_retrieve_body($new_theme);
          $theme_meta =json_decode($theme_meta);
          $mytheme['new_version'] = $theme_meta->version; // EDIT: your theme's new version

          if($mytheme['new_version']===$mytheme['old_version'])//Check if current version is equal to the old one
              return $premium_updates;

          array_push($premium_updates, $mytheme);
          return $premium_updates;
      }


    // mwp_premium_perform_update filter
    //
    // Hook to this filter to return either the URL to the new version 
    // or your callback function which will perform the update when called
    //

    function mytheme_mwp_perform_update( $update ){
          
      if( !function_exists( 'wp_get_theme' ))   // make sure we have the needed functions available
              if (!@include_once( ABSPATH.'wp-admin/includes/theme.php'))
                  return $update; 
              
          if(!function_exists( 'is_plugin_active') )
              if (!@include_once( ABSPATH.'wp-admin/includes/plugin.php'))
                  return $update; 
                  
          if (!(is_plugin_active('worker/init.php') || is_plugin_active('managewp/init.php'))) // ManageWP client plugin needed   
              return $update;     
          
          if( function_exists( 'wp_get_theme' ) ){
              
              $themeobj = wp_get_theme();
              $my_addon['Name'] = $themeobj->Name;
              $my_addon['URI'] = $themeobj->ThemeURI;
              $my_addon['Template'] = $themeobj->Template;
              $my_addon['Version'] = $themeobj->Version;
              $my_addon['Status'] = $themeobj->Status;
          }
          
          // This is the only line you need to edit
          $new_theme = wp_remote_get( 'http://teslathemes.com/auto_update/?theme=' . THEME_NAME );
          $theme_meta = wp_remote_retrieve_body($new_theme);
          $theme_meta =json_decode($theme_meta);
          $date = strtotime( gmdate('Y-m-d H:i:s') );
          $username = $this->tesla_security->username;
          $key = $this->tesla_security->update_key;

          // EnCrypt string
          $token=base64_encode(serialize(array($date,$key,$username)));

          $download_url = $theme_meta->download_url . "&token=$token";
          $my_addon['url'] = $download_url;    // EDIT: provide URL to the archive file with the new version and we will use WordPress update mechanism
          //$my_addon['callback'] = 'my_update_callback';       // EDIT: OR alternately provide your own callback function for managing the update. Do not use both.
          $my_addon['type'] ='theme'; //added after support instructions

          array_push($update, $my_addon);
          
          return $update;
    }


      // mwp_premium_update_check filter
      //
      // Hook to this filter to provide a function that checks for updates
      // This hook is required only if you use callback function to check for the new version

      function mytheme_mwp_update_check( $update ){
          
          if( !function_exists( 'wp_get_theme' ))   // make sure we have the needed functions available
              if (!@include_once( ABSPATH.'wp-admin/includes/theme.php'))
                  return $update;
              
          if(!function_exists( 'is_plugin_active') )
              if (!@include_once( ABSPATH.'wp-admin/includes/plugin.php'))
                  return $update;
                  
          if (!(is_plugin_active('worker/init.php') || is_plugin_active('managewp/init.php'))) // ManageWP client plugin needed   
              return $update;
          
          if( function_exists( 'wp_get_theme' ) ){
              
              $themeobj = wp_get_theme();
              $my_addon['Name'] = $themeobj->Name;
              $my_addon['URI'] = $themeobj->ThemeURI;
              $my_addon['Template'] = $themeobj->Template;
              $my_addon['Version'] = $themeobj->Version;
              $my_addon['Status'] = $themeobj->Status;
          }
          $my_addon['old_version']=  $my_addon['Version'];
          $my_addon['type'] = 'theme';
          // This is the only line you need to edit
          //$my_addon['callback'] = 'my_update_callback';  // EDIT: provide your callback function which checks for your theme updates
          //If you use path to zip file, you don't need callback function
          if($my_addon['new_version']===$my_addon['old_version'])
            return $update;
          
          array_push($update, $my_addon);
          
          return $update;
    }

  }

  add_action( 'tt_admin_load' ,'tt_init_admin'); // $TTA = new Tesla_admin; );
  function tt_init_admin(){
    $TTA = new Tesla_admin;
  }
  
}