<?php

use Abraham\TwitterOAuth\TwitterOAuth;

if(!function_exists('tt_twitter_get_tweets')){
	function tt_twitter_get_tweets($twitteruser,$param,$instance = null){

		$cache = get_transient(THEME_NAME . '_twitter');

		if(is_array($cache)&&array_key_exists($twitteruser, $cache))
		   return $cache[$twitteruser];

		//getting credentials from widget form
		if(!empty($instance) && !empty($instance['consumerkey'])){
			$consumerkey = $instance['consumerkey'];
			$consumersecret = $instance['consumersecret'];
			$accesstoken = $instance['accesstoken'];
			$accesstokensecret = $instance['accesstokensecret'];
		}else{
			$consumerkey = _go('twitter_consumerkey');
			$consumersecret = _go('twitter_consumersecret');
			$accesstoken = _go('twitter_accesstoken');
			$accesstokensecret = _go('twitter_accesstokensecret');
		}

		if(empty($consumerkey)||empty($consumersecret)||empty($accesstoken)||empty($accesstokensecret))
			return null;

		$connection = tt_getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
		$counter = explode(',', $param);

        if(count($counter) <= 1)
		    $tweets = $connection->get("statuses/user_timeline", array("screen_name" => $twitteruser, "count" => $param[0]));
        else
		    $tweets = $connection->get("statuses/lookup", array("id" => $param));

		if(!is_array($cache))
			$cache = array();

		if(empty($tweets->error)){
			$cache[$twitteruser] = $tweets;
			set_transient(THEME_NAME . '_twitter',$cache,2 * MINUTE_IN_SECONDS);
		}

		return $tweets;
	}
}

if(!function_exists('tt_getConnectionWithAccessToken')){
	function tt_getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
		require_once TTF . '/extensions/twitteroauth/autoload.php';
		$connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
		return $connection;
	}
}

if(!function_exists('tt_linkify')){
	function tt_linkify($status_text){
		// linkify URLs
		$status_text = preg_replace(
			'/(https?:\/\/\S+)/',
			'<a href="\1">\1</a>',
			$status_text
		);

		// linkify twitter users
		$status_text = preg_replace(
			'/(^|\s)@(\w+)/',
			'\1<a href="http://twitter.com/\2">@\2</a>',
			$status_text
		);

		// linkify tags
		$status_text = preg_replace(
			'/(^|\s)#(\w+)/',
			'\1<a href="http://twitter.com/search?q=%23\2&amp;src=hash">#\2</a>',
			$status_text
		);

		return $status_text;
	}
}

/**
* Twitter backwards comptibility
* @since 1.9.2
* @todo: remove it when all themes updated
*/
if(!function_exists('twitter_generate_output')){
	function twitter_generate_output($user, $number, $callback='', $step_callback='', $before=false, $after=false, $instance = null){
		return tt_twitter_generate_output($user, $number, $callback, $step_callback, $before, $after, $instance);
	}
}

if(!function_exists('tt_twitter_generate_output')){
	function tt_twitter_generate_output($user, $number, $callback='', $step_callback='', $before=false, $after=false, $instance = null){

		$tweets = tt_twitter_get_tweets($user, $number, $instance);

		if(is_null($tweets))
			return 'Twitter is not configured.';
		if(is_object($tweets) && !empty($tweets->error))
			return $tweets->error;
		if(is_object($tweets) && !empty($tweets->errors)){
			foreach ($tweets->errors as $error) {
				$errors[] = $error->message;
			}
			return implode('; ', $errors);
		}

		$number = min(20,$number);

		$tweets = array_slice($tweets,0,$number);

		if(!empty($callback))
			return call_user_func($callback,$tweets);

		$output = $before===false?'<div class="tt_twitter"><ul class="twitter">':$before;

		$time = time();
		$last = count($tweets)-1;
		if(!empty($tweets))
			foreach($tweets as $i => $tweet){

				$date = $tweet->created_at;
				$date = date_parse($date);
				$date = mktime(0,0,0,$date['month'],$date['day'],$date['year']);
				$date = $time - $date;

				$seconds = (int)$date;
				$date=floor($date/60);
				$minutes = (int)$date;
				if($minutes){
					$date=floor($date/60);
					$hours = (int)$date;
					if($hours){
						$date=floor($date/24);
						$days = (int)$date;
						if($days){
							$date=floor($date/7);
							$weeks = (int)$date;
							if($weeks)
								$date = $weeks.' week'.(1===$weeks?'':'s').' ago';
							else
								$date = $days.' day'.(1===$days?'':'s').' ago';
						}
						else
							$date = $hours.' hour'.(1===$hours?'':'s').' ago';
					}
					else
						$date = $minutes.' minute'.(1===$minutes?'':'s').' ago';
				}
				else
					$date = 'less than a minute ago';

				$output .= 
				$step_callback===''?
				'<li'.($i===$last?' class="last"':'').'>'.
					tt_linkify($tweet->text).
					'<span class="date">'.
						$date.
					'</span>'.
				'</li>'
				:
				call_user_func($step_callback,$i,tt_linkify($tweet->text),$date);

			}

		$output .= $after===false?'</ul></div>':$after;

		return $output;
	}
}