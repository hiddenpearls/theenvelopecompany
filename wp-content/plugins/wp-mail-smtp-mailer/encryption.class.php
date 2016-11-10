<?php 

if (!defined( 'ABSPATH')) exit;

/**
*  Encryption 
*/
class WPMS_encryption{
	

 

	public function data_encrypt($text, $salt){

	 	$salt = md5($salt);

	    return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
	}

 
	public function data_decrypt($text, $salt){

	 	$salt = md5($salt);
	 	
	    return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
	}





}