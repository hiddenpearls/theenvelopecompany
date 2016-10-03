<?php

class profile_edit_afo{

	 public function __construct() {
		add_action( 'init', array($this, 'edit_profile_validate' ) );
	 }
	 	 
	 public function is_field_enabled($value){
		$data = get_option( $value );
		if($data == 'Yes'){
			return true;
		} else {
			return false;
		}
	}
	
	public function is_field_required($value){
		$data = get_option( $value );
		if($data == 'Yes'){
			return 'required="required"';
		} else {
			return '';
		}
	}
	
	
	function edit_profile_validate(){
		if(!session_id()){
			@session_start();
		}
		
		if(isset($_POST['option']) and sanitize_text_field($_POST['option']) == "afo_user_edit_profile"){
			global $post;
			$error = false;
			
			if(!is_user_logged_in()){
				$msg = __('Login to update profile!','wp-register-profile-with-shortcode');
				$error = true;
			}
			
			
			if(!$error){
			
				$user_id = get_current_user_id();
				
				$userdata = array(
					'ID' => $user_id
				);
				
				if( $this->is_field_enabled('firstname_in_profile') ){
					$userdata['first_name'] = sanitize_text_field($_POST['first_name']);
				}
				
				if( $this->is_field_enabled('lastname_in_profile') ){
					$userdata['last_name'] = sanitize_text_field($_POST['last_name']);
				}
				
				if( $this->is_field_enabled('displayname_in_profile') ){
					$userdata['display_name'] = sanitize_text_field($_POST['display_name']);
				}
				
				if( $this->is_field_enabled('userdescription_in_profile') ){
					$userdata['description'] = sanitize_text_field($_POST['description']);
				} 
				
				if( $this->is_field_enabled('userurl_in_profile') ){
					$userdata['user_url'] = sanitize_text_field($_POST['user_url']);
				} 
				
				// update user profile in db //
					$user_id = wp_update_user( $userdata );
				// update user profile in db //
				
				$_SESSION['reg_error_msg'] = __('Profile data updated successfully.','wp-register-profile-with-shortcode');
				$_SESSION['reg_msg_class'] = 'reg_success';
				
			} else {
				$_SESSION['reg_error_msg'] = $msg;
				$_SESSION['reg_msg_class'] = 'reg_error';
			}
			
			if(!empty($_POST['redirect'])){
				$redirect =  sanitize_text_field($_POST['redirect']);
				wp_redirect($redirect);
				exit;
			}
		}
		
	 }
	 
	 
	public function profileEdit(){
		global $post;
		$this->error_message();
		if(is_user_logged_in()){
      	$current_user = wp_get_current_user();
		$user_id = get_current_user_id();
		?>
		<form name="profile" id="profile" method="post" action="" enctype="multipart/form-data">
		<input type="hidden" name="option" value="afo_user_edit_profile" />
        <input type="hidden" name="redirect" value="<?php echo sanitize_text_field( register_wid::curPageURL() ); ?>">
		<div id="reg_forms" class="reg_forms">

			<div class="reg-form-group">
			<label for="name"><?php _e('Username','wp-register-profile-with-shortcode');?> </label>
			<input type="text" required="required" value="<?php echo $current_user->user_login;?>" disabled="disabled"/>
			</div>
			
			<div class="reg-form-group">
			<label for="name"><?php _e('User Email','wp-register-profile-with-shortcode');?> </label>
			<input type="email" value="<?php echo $current_user->user_email;?>" disabled="disabled"/>
			</div>
		
			<?php if($this->is_field_enabled('firstname_in_profile')){ ?>
			<div class="reg-form-group">
			<label for="name"><?php _e('First Name','wp-register-profile-with-shortcode');?> </label>
			<input type="text" name="first_name" <?php echo $this->is_field_required('is_firstname_required');?> placeholder="First Name" value="<?php echo $current_user->first_name;?>"/>
			</div>
			<?php } ?>
			
			<?php if($this->is_field_enabled('lastname_in_profile')){ ?>
			<div class="reg-form-group">
			<label for="name"><?php _e('Last Name','wp-register-profile-with-shortcode');?> </label>
			<input type="text" name="last_name" <?php echo $this->is_field_required('is_lastname_required');?> placeholder="Last Name" value="<?php echo $current_user->last_name;?>"/>
			</div>
			<?php } ?>
			
			<?php if($this->is_field_enabled('displayname_in_profile')){ ?>
			<div class="reg-form-group">
			<label for="name"><?php _e('Display Name','wp-register-profile-with-shortcode');?> </label>
			<input type="text" name="display_name" <?php echo $this->is_field_required('is_displayname_required');?> placeholder="Display Name" value="<?php echo $current_user->display_name;?>"/>
			</div>
			<?php } ?>
			
			<?php if($this->is_field_enabled('userdescription_in_profile')){ ?>
			<div class="reg-form-group">
			<label for="name"><?php _e('About User','wp-register-profile-with-shortcode');?> </label>
			<textarea name="description" <?php echo $this->is_field_required('is_userdescription_required');?>><?php echo get_the_author_meta( 'description', $user_id );?></textarea>
			</div>
			<?php } ?>
			
			<?php if($this->is_field_enabled('userurl_in_profile')){ ?>
			<div class="reg-form-group">
			<label for="name"><?php _e('Website','wp-register-profile-with-shortcode');?> </label>
			<input type="url" name="user_url" <?php echo $this->is_field_required('is_userurl_required');?> placeholder="User URL" value="<?php echo get_the_author_meta( 'user_url', $user_id );?>"/>
			</div>
			<?php } ?>
			
			<div class="reg-form-group">
			<input name="profile" type="submit" value="<?php _e('Update','wp-register-profile-with-shortcode');?>" />
			</div>

		</div>
		</form>
		<?php 
		} 
	}
	
	public function error_message(){
		if(!session_id()){
			@session_start();
		}
		
		if(isset($_SESSION['reg_error_msg']) and $_SESSION['reg_error_msg']){
			echo '<div class="'.$_SESSION['reg_msg_class'].'">'.$_SESSION['reg_error_msg'].'</div>';
			unset($_SESSION['reg_error_msg']);
			unset($_SESSION['reg_msg_class']);
		}
	}
		
}

new profile_edit_afo;

class update_password_afo{

	 public function __construct() {
		add_action( 'init', array($this, 'update_password_validate' ) );
	 }
	 	 

	function update_password_validate(){
		if(isset($_POST['option']) and sanitize_text_field($_POST['option']) == "afo_user_update_password"){
			global $post;
			$error = false;
			
			if(!is_user_logged_in()){
				$msg = __('Login to update profile!','wp-register-profile-with-shortcode');
				$error = true;
			}
			
			if(isset($_POST['user_new_password']) and (sanitize_text_field($_POST['user_new_password']) != sanitize_text_field($_POST['user_retype_password']))){
				$msg = __('Your new password don\'t match with retype password!','wp-register-profile-with-shortcode');
				$error = true;
			}
						
			if(!$error){
				$user_id = get_current_user_id();
				wp_set_password( sanitize_text_field($_POST['user_new_password']), $user_id );
				
				$_SESSION['reg_error_msg'] = __('Your password updated successfully. Please login again.','wp-register-profile-with-shortcode');
				$_SESSION['reg_msg_class'] = 'reg_success';
				
			} else {
				$_SESSION['reg_error_msg'] = $msg;
				$_SESSION['reg_msg_class'] = 'reg_error';
			}
			
			if(!empty($_POST['redirect'])){
				$redirect =  sanitize_text_field($_POST['redirect']);
				wp_redirect($redirect);
				exit;
			}
		}
	 }
	 
	 
	public function updatePasswordForm(){
		global $post;
		$this->error_message();
		if(is_user_logged_in()){
		?>
		<form name="profile" id="profile" method="post" action="">
		<input type="hidden" name="option" value="afo_user_update_password" />
        <input type="hidden" name="redirect" value="<?php echo sanitize_text_field( register_wid::curPageURL() ); ?>">
		<div id="reg_forms" class="reg_forms">
			
			<div class="reg-form-group">
			<label for="name"><?php _e('New Password','wp-register-profile-with-shortcode');?> </label>
			<input type="password" name="user_new_password" required="required"/>
			</div>
			
			<div class="reg-form-group">
			<label for="name"><?php _e('Retype Password','wp-register-profile-with-shortcode');?> </label>
			<input type="password" name="user_retype_password" required="required" />
			</div>

			<div class="reg-form-group">
			<input name="profile" type="submit" value="<?php _e('Update','wp-register-profile-with-shortcode');?>" />
			</div>

		</div>
		</form>
		<?php 
		} 
	}
	
	public function error_message(){
		if(isset($_SESSION['reg_error_msg']) and $_SESSION['reg_error_msg']){
			echo '<div class="'.$_SESSION['reg_msg_class'].'">'.$_SESSION['reg_error_msg'].'</div>';
			unset($_SESSION['reg_error_msg']);
			unset($_SESSION['reg_msg_class']);
		}
	}
		
}

new update_password_afo;

function register_widget_pro_afo_shortcode( $atts ) {
     global $post;
	 extract( shortcode_atts( array(
	      'title' => '',
     ), $atts ) );
     
	ob_start();
	$wid = new register_wid;
	if($title){
		echo '<h2>'.$title.'</h2>';
	}
	$wid->registerForm();
	$ret = ob_get_contents();	
	ob_end_clean();
	return $ret;
}
add_shortcode( 'rp_register_widget', 'register_widget_pro_afo_shortcode' );

function user_profile_edit_pro_afo_shortcode( $atts ) {
     global $post;
	 extract( shortcode_atts( array(
	      'title' => '',
     ), $atts ) );
     
	ob_start();
	$pea = new profile_edit_afo;
	if($title){
		echo '<h2>'.$title.'</h2>';
	}
	$pea->profileEdit();
	$ret = ob_get_contents();	
	ob_end_clean();
	return $ret;
}
add_shortcode( 'rp_profile_edit', 'user_profile_edit_pro_afo_shortcode' );

function user_password_afo_shortcode( $atts ) {
     global $post;
	 extract( shortcode_atts( array(
	      'title' => '',
     ), $atts ) );
     
	ob_start();
	$up_afo = new update_password_afo;
	if($title){
		echo '<h2>'.$title.'</h2>';
	}
	$up_afo->updatePasswordForm();
	$ret = ob_get_contents();	
	ob_end_clean();
	return $ret;
}
add_shortcode( 'rp_update_password', 'user_password_afo_shortcode' );


function get_user_data_afo( $atts ) {
     global $post;
	 extract( shortcode_atts( array(
	      'field' => '',
		  'user_id' => '',
     ), $atts ) );
     
	 $error = false;
	 if($atts['user_id'] == '' and is_user_logged_in()){
	 	$user_id = get_current_user_id();
	 } elseif($atts['user_id']){
	 	$user_id = $atts['user_id'];
	 } else if($atts['user_id'] == '' and !is_user_logged_in()){
	 	$error = true;
	 }
	 if(!$error){
	 	$ret = get_the_author_meta( $atts['field'], $user_id );
	 } else {
	 	$ret = __('Sorry. no user was found!','wp-register-profile-with-shortcode');
	 }
		
	 return $ret;
}
add_shortcode( 'rp_user_data', 'get_user_data_afo' );

function rp_user_data_func($field='',$user_id=''){
	echo do_shortcode('[rp_user_data field="'.$field.'" user_id="'.$user_id.'"]');
}
?>