<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Set Guide_Missing::create_class as an autoloading to prevent missing classes
 * from crashing the api browser.  Classes that are missing a parent will
 * extend this class, and get a warning in the API browser.
 *
 * @package    Kohana/Guide
 * @category   Parser
 */
abstract class Kohana_Guide_Missing {

	/**
	 * Creates classes when they are otherwise not found.
	 *
	 *     Guide::create_class('ThisClassDoesNotExist');
	 *
	 * [!!] All classes created will extend [Guide_Missing].
	 *
	 * @param   string   class name
	 * @return  boolean
	 */
	public static function create_class($class)
	{
		if ( ! class_exists($class))
		{
			// Create a new missing class
			eval("class {$class} extends Guide_Missing { }");
		}
		return TRUE;
	}

} // End Kohana_Guide_Missing
