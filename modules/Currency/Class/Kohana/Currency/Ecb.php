<?php defined('SYS_PATH') OR die('No direct access allowed.');

class Kohana_Currency_Ecb extends Currency {

	const CURRENCY_ALPHA_REGEX = "/currency='([[:alpha:]]+)'/";
	const RATE_GRAPH_REGEX = "/rate='([[:graph:]]+)'/";

	public function convert($amount, $from, $to)
	{
		throw new Currency_Exception('Ecb driver did not support this method.');
	}
	
	public function get_all()
	{
		$xml= file($this->_config->ecb_feed);
		//the file is updated daily between 14:15 and 15:00 CET
		
		$found_currency = array();
		foreach ($xml AS $line)
		{
			if (preg_match(Currency_Ecb::CURRENCY_ALPHA_REGEX, $line, $code))
			{
				if (preg_match(Currency_Ecb::RATE_GRAPH_REGEX, $line, $rate))
				{
					// Output the value of 1 EUR for a currency code 
					$found_currency[$code[1]] = $rate[1];
				}
			}
		}
		return $found_currency;
	}

}
