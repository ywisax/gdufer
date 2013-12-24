<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 目录助手类
 *
 * @package    Kohana
 * @category   Helpers
 */
class Kohana_Helper_Directory {

	public static function create($path, $multi = FALSE)
	{
		if ( ! is_dir($path))
		{
			if ($multi)
			{
				Helper_Directory::create(dirname($path), $multi); 
			}
			mkdir($path, 0777); 
		}
	}

}
