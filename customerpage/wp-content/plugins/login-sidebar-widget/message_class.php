<?php
if(!class_exists('login_message_class')){
	class login_message_class {
		public function __construct(){
			if(!session_id()){
				@session_start();
			}
		}
		
		public function show_message(){
			if(isset($_SESSION['login_message_class']) and $_SESSION['login_message_msg']){
				echo '<p class="'.$_SESSION['login_message_class'].'">'.$_SESSION['login_message_msg'].'</p>';
				unset($_SESSION['login_message_msg']);
				unset($_SESSION['login_message_class']);
			}
		}
		
		public function add_message($msg = '', $class = ''){
			$_SESSION['login_message_msg'] = $msg;
			$_SESSION['login_message_class'] = $class;		
		}
	}
}