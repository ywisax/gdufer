<?php defined('SYS_PATH') OR die('No direct access allowed.');

class Kohana_Currency_Xe extends Currency {

	public function convert($amount, $from, $to)
	{
		// Get feed url
		$feed_url = $this->_config->feed_url[$from];

		// Get xml
		$xml = Request::factory($feed_url)
			->method(HTTP_Request::GET)
			->execute()
			->body();

		// Create new dom document
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = FALSE;
		$doc->loadXML($xml);

		// Get the currencies node list
		$currencies = $doc->getElementsByTagName('currency');

		// Set default rate
		$rate = 0;

		foreach ($currencies AS $currency)
		{
			$values = array();
			foreach ($currency->childNodes AS $node)
			{
				$values[$node->nodeName] = $node->nodeValue;
			}

			if ($values['csymbol'] == $to)
			{
				$rate = $values['crate'];
				break;
			}
		}

		return $amount * $rate;
	}

}