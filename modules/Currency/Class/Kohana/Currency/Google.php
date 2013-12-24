<?php defined('SYS_PATH') OR die('No direct access allowed.');

class Kohana_Currency_Google extends Currency {

	const RHS_REGEX = '/rhs: \"(\d*.\d*\.?\d*)/';

	public function convert($amount, $from, $to)
	{
		$url = "http://www.google.com/ig/calculator?hl=en&q={$amount}{$from}%3D%3F{$to}";
		//echo $url.'<br />';
		$response = Request::factory($url)
			->method(Request::GET)
			->execute()
			->body();

		$matches = array();
		// Get only the rhs
		preg_match(Currency_Google::RHS_REGEX, $response, $matches);
		// Get the rate
		$rate = $matches[1];
		// Clean invalid multibyte characters
		$rate = UTF8::clean($rate);

		return (float) $rate;
	}

}