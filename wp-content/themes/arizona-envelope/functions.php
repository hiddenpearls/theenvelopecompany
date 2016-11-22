<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/customizer.php' // Theme customizer
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

function add_custom_class($classes, $menu_item) {

  if((is_page('equipment') && 'Samples' == $menu_item->title) || (is_page('layouts-die-lines') && 'Samples' == $menu_item->title) || (is_page('helpful-information') && 'Samples' == $menu_item->title)){
    $classes = array_diff( $classes, array( 'current-page-parent', 'current-page-ancestor') ); 
  }

  if((is_page('privacy-policy') && 'About Us' == $menu_item->title) || (is_page('terms-conditions') && 'About Us' == $menu_item->title) || (is_home() && 'About Us' == $menu_item->title)){
    $classes = array_diff( $classes, array('current-page-ancestor') ); 
  }
  return $classes;
  
}
add_filter('nav_menu_css_class', 'add_custom_class', 100, 2);

/**
 * Gravity Wiz // Gravity Forms // Rename Uploaded Files
 *
 * Rename uploaded files for Gravity Forms. You can create a static naming template or using merge tags to base names on user input.
 *
 * Features:
 *  + supports single and multi-file upload fields
 *  + flexible naming template with support for static and dynamic values via GF merge tags
 *
 * Uses:
 *  + add a prefix or suffix to file uploads
 *  + include identifying submitted data in the file name like the user's first and last name
 *
 * @version   1.5
 * @author    David Smith <david@gravitywiz.com>
 * @license   GPL-2.0+
 * @link      http://gravitywiz.com/rename-uploaded-files-for-gravity-form/
 */
class GW_Rename_Uploaded_Files {

  public function __construct( $args = array() ) {

    // set our default arguments, parse against the provided arguments, and store for use throughout the class
    $this->_args = wp_parse_args( $args, array(
      'form_id'  => false,
      'field_id' => false,
      'template' => ''
    ) );

    // do version check in the init to make sure if GF is going to be loaded, it is already loaded
    add_action( 'init', array( $this, 'init' ) );

  }

  public function init() {

    // make sure we're running the required minimum version of Gravity Forms
    if( ! is_callable( array( 'GFFormsModel', 'get_physical_file_path' ) ) ) {
      return;
    }

    //add_action( 'gform_pre_submission', array( $this, 'rename_uploaded_files' ) );
    add_filter( 'gform_entry_post_save', array( $this, 'rename_uploaded_files' ), 10, 2 );

  }

  function rename_uploaded_files( $entry, $form ) {

    if( ! $this->is_applicable_form( $form ) ) {
      return $entry;
    }

    foreach( $form['fields'] as &$field ) {

      if( ! $this->is_applicable_field( $field ) ) {
        continue;
      }

      $uploaded_files = rgar( $entry, $field->id );

      if( empty( $uploaded_files ) ) {
        continue;
      }

      if( $field->get_input_type() == 'post_image' ) {
        $file_bits = explode( '|:|', $uploaded_files );
        $uploaded_files = array( $file_bits[0] );
      } else if( $field->multipleFiles ) {
        $uploaded_files = json_decode( $uploaded_files );
      } else {
        $uploaded_files = array( $uploaded_files );
      }

      $renamed_files = array();

      foreach( $uploaded_files as $file ) {

        $orig_file_name = basename( $file );
        $new_file_name  = $this->rename_file( $orig_file_name, $entry );
        $new_file       = $this->increment_file_name( str_replace( $orig_file_name, $new_file_name, $file ) );

        if( ! file_exists( GFFormsModel::get_physical_file_path( $file ) ) ) {
          continue;
        }

        $result = rename( GFFormsModel::get_physical_file_path( $file ), GFFormsModel::get_physical_file_path( $new_file ) );

        $renamed_files[] = $new_file;

      }

      if( $field->get_input_type() == 'post_image' ) {
        $value = str_replace( $uploaded_files[0], $renamed_files[0], rgar( $entry, $field->id ) );
      } else if( $field->multipleFiles ) {
        $value = json_encode( $renamed_files );
      } else {
        $value = $renamed_files[0];
      }

      GFAPI::update_entry_field( $entry['id'], $field->id, $value );

      $entry[ $field->id ] = $value;

    }

    return $entry;
  }

  function increment_file_name( $file ) {

    $file_path = GFFormsModel::get_physical_file_path( $file );
    $pathinfo  = pathinfo( $file_path );
    $counter   = 1;

    // increment the filename if it already exists (i.e. balloons.jpg, balloons1.jpg, balloons2.jpg)
    while ( file_exists( $file_path ) ) {
      $file_path = str_replace( ".{$pathinfo['extension']}", "{$counter}.{$pathinfo['extension']}", GFFormsModel::get_physical_file_path( $file ) );
      $counter++;
    }

    $file = str_replace( basename( $file ), basename( $file_path ), $file );

    return $file;
  }

  function _rename_uploaded_files( $form ) {

    if( ! $this->is_applicable_form( $form ) ) {
      return;
    }

    foreach( $form['fields'] as &$field ) {

      if( ! $this->is_applicable_field( $field ) ) {
        continue;
      }

      $is_multi_file  = rgar( $field, 'multipleFiles' ) == true;
      $input_name     = sprintf( 'input_%s', $field['id'] );
      $uploaded_files = rgars( GFFormsModel::$uploaded_files, "{$form['id']}/{$input_name}" );

      if( $is_multi_file && ! empty( $uploaded_files ) && is_array( $uploaded_files ) ) {

        foreach( $uploaded_files as &$file ) {
          $file['uploaded_filename'] = $this->rename_file( $file['uploaded_filename'] );
        }

        GFFormsModel::$uploaded_files[ $form['id'] ][ $input_name ] = $uploaded_files;

      } else {

        if( empty( $uploaded_files ) ) {

          $uploaded_files = rgar( $_FILES, $input_name );
          if( empty( $uploaded_files ) || empty( $uploaded_files['name'] ) ) {
            continue;
          }

          $uploaded_files['name'] = $this->rename_file( $uploaded_files['name'] );
          $_FILES[ $input_name ] = $uploaded_files;

        } else {

          $uploaded_files = $this->rename_file( $uploaded_files );
          GFFormsModel::$uploaded_files[ $form['id'] ][ $input_name ] = $uploaded_files;

        }

      }

    }

  }

  function rename_file( $filename, $entry ) {

    $file_info = pathinfo( $filename );
    $new_filename = $this->remove_slashes( $this->get_template_value( $this->_args['template'], $entry, $file_info['filename'] ) );

    return sprintf( '%s.%s', $new_filename, rgar( $file_info, 'extension' ) );
  }

  function get_template_value( $template, $entry, $filename ) {

    // replace our custom "{filename}" psuedo-merge-tag
    $template = str_replace( '{filename}', $filename, $template );

    $form = GFAPI::get_form( $entry['form_id'] );
    $template = $this->clean( GFCommon::replace_variables( $template, $form, $entry, false, true, false, 'text' ) );

    return $template;
  }

  function remove_slashes( $value ) {
    return stripslashes( str_replace( '/', '', $value ) );
  }

  function is_applicable_form( $form ) {

    $form_id = isset( $form['id'] ) ? $form['id'] : $form;

    return $form_id == $this->_args['form_id'];
  }

  function is_applicable_field( $field ) {

    $is_file_upload_field   = in_array( GFFormsModel::get_input_type( $field ), array( 'fileupload', 'post_image' ) );
    $is_applicable_field_id = $this->_args['field_id'] ? $field['id'] == $this->_args['field_id'] : true;

    return $is_file_upload_field && $is_applicable_field_id;
  }

  function clean( $str ) {
    return sanitize_title_with_dashes( strtr(
      utf8_decode( $str ),
      utf8_decode( 'ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'),
      'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy'
    ), 'save' );
  }

}
# Configuration
//Original Template: Name (First):1.3}-{Name (Last):1.6}
new GW_Rename_Uploaded_Files( array(
    'form_id' => 12,
    'field_id' => 47,
    'template' => '{Email:4}-{filename}' // most merge tags are supported, original file extension is preserved
) );
new GW_Rename_Uploaded_Files( array(
    'form_id' => 12,
    'field_id' => 70,
    'template' => '{Email:4}-{filename}' // most merge tags are supported, original file extension is preserved
) );

