<?php defined('SYS_PATH') OR die('No direct access allowed.');

class Kohana_Currency_Era extends Currency {

	public function convert($amount, $from, $to)
	{
		// Get the api key
		$api_key = $this->_config['api_key'];
		// Get the rate
		$rate = Request::factory("http://www.exchangerate-api.com/{$from}/{$to}/{$amount}?k=$api_key")
			->method(Request::GET)
			->execute()
			->body();

		return (float) $rate;
	}

}