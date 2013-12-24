<?php defined('SYS_PATH') OR die('No direct access.');
/**
 * Math captcha class.
 *
 * @package		Kohana/Captcha
 * @category	Driver
 */
class Kohana_Captcha_Math extends Captcha
{
	/**
	 * @var string Captcha math exercise
	 */
	private $math_exercise;

	/**
	 * Generates a new Captcha challenge.
	 *
	 * @return string The challenge answer
	 */
	public function generate_challenge()
	{
		// Easy
		if (Captcha::$config['complexity'] < 4)
		{
			$numbers[] = mt_rand(1, 5);
			$numbers[] = mt_rand(1, 4);
		}
		// Normal
		elseif (Captcha::$config['complexity'] < 7)
		{
			$numbers[] = mt_rand(10, 20);
			$numbers[] = mt_rand(1, 10);
		}
		// Difficult, well, not really ;)
		else
		{
			$numbers[] = mt_rand(100, 200);
			$numbers[] = mt_rand(10, 20);
			$numbers[] = mt_rand(1, 10);
		}

		// Store the question for output
		$this->math_exercise = implode(' + ', $numbers).' = ';

		// Return the answer
		return array_sum($numbers);
	}

	/**
	 * Outputs the Captcha riddle.
	 *
	 * @param boolean $html HTML output
	 * @return mixed
	 */
	public function render($html = TRUE)
	{
		return $this->math_exercise;
	}

} // End Captcha Math Driver Class
