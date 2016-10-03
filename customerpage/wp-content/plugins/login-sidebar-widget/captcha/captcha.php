<?php
if(!class_exists('captcha')){
	class captcha{
		
	public $imgWidth = 110;
	public $imgHeight = 40;
	public $fontSize = 20;
	public $font = 'fonts/StayPuft.ttf';
	public $backgroundColor = '#ffffff';
	public $textColor = '#15A11C';
	public $fontX = 10;
	public $fontY = 28;
	public $text = 'ABCDEFGHJKLMNPQRSTWXYZ23456789';
	public $length = 5;
	
	public function __construct(){
		if(!session_id()){
			@session_start();
		}
		$this->genCaptcha();
	}
	
	public function genCaptcha(){
		
		$im = imagecreatetruecolor($this->imgWidth, $this->imgHeight);	
	
		$backgroundColor = $this->hexToRGB($this->backgroundColor);
		
		$backgroundColor = imagecolorallocate($im, $backgroundColor['r'],$backgroundColor['g'],$backgroundColor['b']);
		imagefill($im,0,0,$backgroundColor);
		
		
		$textColor = $this->hexToRGB($this->textColor);	
		$textColor = imagecolorallocate($im, $textColor['r'],$textColor['g'],$textColor['b']);	
		
		
		$code = $this->genCode();
		$_SESSION['captcha_code'] = $code;
		imagettftext($im, $this->fontSize, 0, $this->fontX, $this->fontY, $textColor, $this->font, $_SESSION['captcha_code']);	
		
		imagejpeg($im,NULL,90);
		header('Content-Type: image/jpeg');
		imagedestroy($im);
	}
	
	public function genCode(){
		$t = '';
		$textlen = strlen($this->text);
		for($i = 1; $i <= $this->length; $i++){
			$t .= substr( $this->text, rand( 0, $textlen - 1 ), 1);
		}	
		return $t;
	}
	
	public function hexToRGB($colour){
		if ( $colour[0] == '#' ) {
				$colour = substr( $colour, 1 );
		}
		if ( strlen( $colour ) == 6 ) {
				list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
		} elseif ( strlen( $colour ) == 3 ) {
				list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
		} else {
				return false;
		}
		$r = hexdec( $r );
		$g = hexdec( $g );
		$b = hexdec( $b );
		return array( 'r' => $r, 'g' => $g, 'b' => $b );
	}	
	
	}
}
new captcha;
?>