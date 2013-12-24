<?php defined('SYS_PATH') OR die('No direct access.');
/**
 * Word captcha class.
 *
 * @package		Kohana/Captcha
 * @category	Driver
 */
class Kohana_Captcha_Word extends Captcha_Basic
{
	/**
	 * Generates a new Captcha challenge.
	 *
	 * @return string The challenge answer
	 */
	public function generate_challenge()
	{
		// Load words from the current language and randomize them
		$words = Kohana::config('Captcha.words');
		shuffle($words);

		// Loop over each word...
		foreach ($words AS $word)
		{
			// ...until we find one of the desired length
			if (abs(Captcha::$config['complexity'] - strlen($word)) < 2)
			{
				return strtoupper($word);
			}
		}
		
		// Return any random word as final fallback
		return strtoupper($words[array_rand($words)]);
	}

} // End Captcha Word Driver Class
