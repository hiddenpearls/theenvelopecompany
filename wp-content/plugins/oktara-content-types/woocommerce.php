<?php
/**
* Add new register fields for WooCommerce registration.
*
* @return string Register fields HTML.
*/

function wooc_extra_register_fields() {
       ?>
       <p class="form-row form-row-first">
       <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?><span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
       </p>
       <p class="form-row form-row-last">
       <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
       </p>
       <div class="clear"></div>
       <!--<p class="form-row form-row-wide">
        <label for="reg_password2"><?php _e( 'Password Repeat', 'woocommerce' ); ?> <span class="required">*</span></label>
        <input type="password" class="input-text" name="password2" id="reg_password2" value="<?php //if ( ! empty( $_POST['password2'] ) ) echo esc_attr( $_POST['password2'] ); ?>" />
      </p>-->
      <p class="form-row form-row-wide">
        <label for="reg_billing_company"><?php _e( 'Company', 'woocommerce' ); ?></label>
        <input type="text" class="input-text" name="billing_company" id="reg_billing_company" value="<?php if ( ! empty( $_POST['billing_company'] ) ) esc_attr_e( $_POST['billing_company'] ); ?>" />
      </p>
       <?php
}
add_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields' );
// 
add_filter('woocommerce_registration_errors', 'registration_errors_validation', 10,3);
function registration_errors_validation($reg_errors, $sanitized_user_login, $user_email) {
  global $woocommerce;
  extract( $_POST );
  if ( strcmp( $password, $password2 ) !== 0 ) {
    return new WP_Error( 'registration-error', __( 'Passwords do not match.', 'woocommerce' ) );
  }
  return $reg_errors;
}
add_action( 'woocommerce_register_form', 'wc_register_form_password_repeat' );
function wc_register_form_password_repeat() {
  ?>
  <p class="form-row form-row-wide">
    <label for="reg_password2"><?php _e( 'Password Confirmation', 'woocommerce' ); ?> <span class="required">*</span></label>
    <input type="password" class="input-text" name="password2" id="reg_password2" value="<?php if ( ! empty( $_POST['password2'] ) ) echo esc_attr( $_POST['password2'] ); ?>" />
  </p>
  <?php
}
/**
* Validate the extra register fields.
*
* @param string $username         Current username.
* @param string $email             Current email.
* @param object $validation_errorsWP_Error object.
*
* @return void
*/

function wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {
       if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
              $validation_errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'woocommerce' ) );
       }
       if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
              $validation_errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'woocommerce' ) );
       }
       if( isset($_POST['password']) && empty ( $_POST['password']) || isset($_POST['password2']) && empty ( $_POST['password2']) ){
        $validation_errors->add( 'registration-error', __( 'Passwords mismatch.', 'woocommerce' ) );
       }
      //return $reg_errors;
       /*if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {
              $validation_errors->add( 'billing_phone_error', __( '<strong>Error</strong>: Phone is required!.', 'woocommerce' ) );
       }*/
}
add_action( 'woocommerce_register_post', 'wooc_validate_extra_register_fields', 10, 3 );


/**
* Save the extra register fields.
*
* @paramint $customer_id Current customer ID.
*
* @return void
*/
function wooc_save_extra_register_fields( $customer_id ) {
       if ( isset( $_POST['billing_first_name'] ) ) {
              // WordPress default first name field.
              update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
              // WooCommerce billing first name.
              update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
       }
       if ( isset( $_POST['billing_last_name'] ) ) {
              // WordPress default last name field.
              update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
              // WooCommerce billing last name.
              update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
       }
       if ( isset( $_POST['billing_company'] ) ) {
              // WordPress default billing company field.
              //update_user_meta( $customer_id, 'company', sanitize_text_field( $_POST['billing_company'] ) );
              //update_user_meta( $customer_id, 'user_company', esc_attr( $_POST['billing_company'] ) );
              // WooCommerce billing company.
              update_user_meta( $customer_id, 'user_company', sanitize_text_field( $_POST['billing_company'] ) );
              
              //update_user_meta( $customer_id, 'user_company', sanitize_text_field( $_POST['billing_company'] ) );
              
       }
       /*if ( isset( $_POST['billing_phone'] ) ) {
              // WooCommerce billing phone
              update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
       }*/
}
add_action( 'woocommerce_created_customer', 'wooc_save_extra_register_fields' );

/**
 *  Add a custom email to the list of emails WooCommerce should load
 *
 * @since 0.1
 * @param array $email_classes available email classes
 * @return array filtered available email classes
 */
function add_azenv_order_woocommerce_email( $email_classes ) {
	// include our custom email class
	require_once( 'includes/class-wc-custom-order-email.php' );
	// add the email class to the list of email classes that WooCommerce loads
	$email_classes['WC_Custom_Order_Email'] = new WC_Custom_Order_Email();
	return $email_classes;
}
add_filter( 'woocommerce_email_classes', 'add_azenv_order_woocommerce_email' );


/**
 *  Add a custom field to the contact information form in Wordpress
 *
 */
function modify_contact_methods($profile_fields) {
	// Add new fields
	$profile_fields['user_company'] = 'Company';
	return $profile_fields;
}
add_filter('user_contactmethods', 'modify_contact_methods');

/**
 *  Add company field to account details form and save to user profile
 *
 */
//add_action( 'woocommerce_edit_account_form', 'my_woocommerce_edit_account_form' );
add_action( 'woocommerce_save_account_details', 'my_woocommerce_save_account_details' );
 
/*function my_woocommerce_edit_account_form() {
 
  $user_id = get_current_user_id();
  $user = get_userdata( $user_id );
 
  if ( !$user )
    return;
 
  //$twitter = get_user_meta( $user_id, 'twitter', true );
  //$url = $user->user_url;
  $company = $user->user_company;
  
 
  ?>
 
  <p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
		<label for="user_company"><?php _e( 'Company', 'woocommerce' ); ?> </label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="user_company" id="user_company" value="<?php echo esc_attr( $user->user_company ); ?>" />
	</p>
 
  <?php
 
}*/
 
function my_woocommerce_save_account_details( $user_id ) {
  $user_id = get_current_user_id();
  $user = get_userdata( $user_id );
  //update_user_meta( $user_id, 'twitter', htmlentities( $_POST[ 'twitter' ] ) );
 
  //$user = wp_update_user( array( 'ID' => $user_id, 'user_url' => esc_url( $_POST[ 'url' ] ) ) );
   update_user_meta( $user_id, 'user_company', sanitize_text_field( $_POST['user_company'] ) );
 
}

?>