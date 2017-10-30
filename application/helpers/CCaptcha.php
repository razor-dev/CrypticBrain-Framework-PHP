<?php

/**
 * CCaptcha helper class file
 */	  

class CCaptcha
{
	public static function generate()
	{
		$length = CConfig::get('validation.captcha.length');
		$letters = array_merge(range('a', 'z'), range(2, 9));
		unset($letters[array_search('q', $letters)]);
		unset($letters[array_search('Q', $letters)]);
		shuffle($letters);
		$letters = array_slice($letters, 0, $length);
		$letters = implode('', $letters);

		return $letters;
	}
	
	public static function getImage()
	{
		$length = CConfig::get('validation.captcha.length');
		$fontPath = APP_PATH . CConfig::get('validation.captcha.fontPath');
		$letters = str_split(CrypticBrain::app()->getSession()->get('captcha'));
		$im = imagecreatetruecolor(30*$length, 50);
		$bg = imagecolorallocate($im, 255, 255, 255);
		imagefill($im, 0, 0, $bg);
        imagettftext($im, 6, 0, 133, 49, imagecolorallocate($im, 0x00, 0x00, 0x00), "fonts/arial.ttf", "IMDEVEL.RU");
		foreach($letters as $key => $letter){
			$color = imagecolorallocate($im, rand(0, 40), rand(0, 120), rand(0, 240));
			imagefttext($im, 30, rand(-10, 10), 20+($key*25) + rand(-5, +5), 25 + rand(5, 10),  $color, $fontPath, $letter);
		}

		ob_start();
		imagepng($im);
		imagedestroy($im);
		$image = ob_get_contents();
		ob_end_clean();
		
		return 'data:image/png;base64,' . base64_encode($image);
	}
}