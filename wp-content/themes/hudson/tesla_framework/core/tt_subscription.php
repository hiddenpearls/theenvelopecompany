<?php
//===============Subscription class================================ 
if(!class_exists('TT_Subscription')){
	class TT_Subscription extends TeslaFramework{

		private static $config;

		public function __construct(){}

		public static function subscription_init() {
			if (file_exists( TT_THEME_DIR . '/theme_config/subscription.php' )){
				//get config files
				self::$config = (file_exists(TT_STYLE_DIR . '/theme_config/subscription.php')) ? include TT_STYLE_DIR . '/theme_config/subscription.php' : include TT_THEME_DIR . '/theme_config/subscription.php';
				//registering axaj hooks
				self::subscriptions_ajax_hooks();
				add_action( 'wp_enqueue_scripts', array( 'TT_Subscription', 'enqueue_scripts' ) );
				add_action( 'admin_init', array( 'TT_Subscription', 'serve_export_subscr_file' ) );
			}
		}

		private static function subscriptions_ajax_hooks(){
			add_action('wp_ajax_insert_subscription', array('TT_Subscription','insert_subscription_ajax'));
			add_action('wp_ajax_nopriv_insert_subscription', array('TT_Subscription','insert_subscription_ajax'));

			add_action('wp_ajax_get_mailchimp_lists', array('TT_Subscription','get_mailchimp_lists_ajax'));
		}

		public static function enqueue_scripts(){
			wp_enqueue_script('subscription', TT_FW . '/static/js/subscription.js', '', false, true);
			self::$config['subscription_nonce'] = wp_create_nonce('tt-subscription-nonce');
			wp_localize_script( 'subscription', 'ttSubscrConfig', self::$config );
		}

		public static function insert_subscription_ajax(){
			check_ajax_referer( 'tt-subscription-nonce' , 'subscription-nonce' );
			$form = $_POST;			

			foreach( $form as $name => $input ){
				if($name == 'action' || $name == 'subscription-nonce')
					continue;
				$data[$name] = $input;
				$headlines[$name] = ucfirst($name);
			}
			if(!empty($data)){
				//echo json_encode($data);
				$headlines[] = (!empty(self::$config['date_headline'])) ? self::$config['date_headline'] : 'Date' ;
				$format = !empty( self::$config['date_format'] ) ? self:: $config['date_format'] : "F j, Y, g:i a" ;
				$date = date( $format );
				$data['date'] = $date;

				$subscribers = tt_get_subscriptions();
				if(empty($subscribers[$data['email']])){
					if(!is_array($subscribers))
						$subscribers = array($data['email'] => $data);
					else
						$subscribers[$data['email']] = $data;	//add new subscriber to existing array
					
					if( update_option( THEME_OPTIONS . '_subscribers', $subscribers ) )
						$result = (!empty(self::$config['success_msg'])) ? self::$config['success_msg'] : __('Subscribed','TeslaFramework');
					else
						$result = (!empty(self::$config['error_writing_msg']))?self::$config['error_writing_msg'] : __('Error','TeslaFramework');
				}else{
					$result = !empty( self::$config['email_exists_msg'] )  ? self::$config['email_exists_msg'] : __('Email already exists','TeslaFramework');
				}
				
				if(_go('mailchimp')){       //send api call to mailchimp if so selected in FW
					$mailchimp_msg = ( !empty(self::$config['error_mailchimp'] ) ) ? self::mailchimp_call($data) : self::mailchimp_call($data,true);
					$result = $mailchimp_msg === true ? $result : (!empty($mailchimp_msg) ? $mailchimp_msg : (!empty(self::$config['error_mailchimp']) ? self::$config['error_mailchimp'] : __('MailChimp Error','TeslaFramework')));
				}

			}else
				$result = ( !empty( self::$config['no_data_posted_msg'] ) ) ? self::$config['no_data_posted_msg'] : __('No data received','TeslaFramework');
			
			die(json_encode($result));
		}

		private static function mailchimp_call($data,$grab_error = false){
			if(_go('mailchimp_api_key') && _go('mailchimp_list_id')){
				$apikey = _go('mailchimp_api_key');
				$lsit_id = _go('mailchimp_list_id');
				if(preg_match('@-(.*)@is',$apikey,$dc_matches)){
					$dc = $dc_matches[1];
					$url = "https://$dc.api.mailchimp.com/2.0/lists/subscribe"; //subscribes user to list with id
					$email = $data['email'];
					unset($data['email']);
					$post_data = array(
						'apikey'=>$apikey,
						'id'=>$lsit_id,
						'email'=>array(
								'email'=>$email
							),
						'merge_vars'=>$data
						);
					if($grab_error)
						$result = tt_get_mailchimp($url,$post_data,true);
					else
						$result = tt_get_mailchimp($url,$post_data);
				}
			}else
				$result = ($grab_error) ? __('No API key for mailchimp','TeslaFramework') : FALSE;
			return $result;
		}

		public static function get_mailchimp_lists($custom_api_key = NULL){
			$apikey = ($custom_api_key) ? $custom_api_key : _go('mailchimp_api_key');
			if(preg_match('@-(.*)@is',$apikey,$dc_matches)){
				$dc = $dc_matches[1];
				$url = "https://$dc.api.mailchimp.com/2.0/lists/list";
				$post_data = array(
							'apikey'=>$apikey
							);
				$result = tt_get_mailchimp($url,$post_data,true,true);
			}else
				$result = __('Not a valid mailchimp api key.','TeslaFramework');
			return $result;
		}

		static function get_mailchimp_lists_ajax(){
			$api_key = (!empty($_POST['api_key'])) ? $_POST['api_key'] : NULL;
			die(json_encode(self::get_mailchimp_lists($api_key)));
		}

		/**
		* Generates and serves export file
		*/
		public static function serve_export_subscr_file(){
			if(isset($_GET['tt-export-subsc']) && !empty($_GET['nonce']) ){
				if( ! wp_verify_nonce( $_GET['nonce'], 'tt-export-subscr' ) ){
					die( 'Security check' );
				}else{
					$subscribers = tt_get_subscriptions();
					header("Content-Type: application/octet-stream");
      				header("Content-Disposition: attachment; filename=tt-export-subscribers.txt");
      				foreach($subscribers as $email => $subscriber_data){
      					echo implode($subscriber_data,"\t") . "\n";
      				}
      				die();
				}

			}
		}

	}
	TT_Subscription::subscription_init();
}
//=================END SUBSCRIPTION class=============================