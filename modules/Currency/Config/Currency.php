<?php defined('SYS_PATH') OR die('No direct access allowed.');

return array(
	// Currently supported services are google, exchangerate-api.com, xe.com
	// Driver can be 'google', 'era', 'xe', 'ecb'
	'driver' => 'google',
	
	// exchangerate-api.com API key
	'api_key' => '',
	
	// xe.com feed url
	'feed_url' => array(
		// These are the xe base currences list feed url, you need one feed for each base currency
		// For example if you have only USD and GBP feed url, you can only convert from USD or GBP to other currencies.
		// 下面的feed地址貌似失效了。。。
		'USD' => 'http://www.xe.com/dfs/sample-usd.xml',
		'GBP' => 'http://www.xe.com/dfs/sample-gbp.xml',
	),
	
	'ecb_feed' => 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml',
	
);
