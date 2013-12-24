<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Exception thrown when an error occurs during template loading.
 *
 * Automatic template information guessing is always turned off as
 * if a template cannot be loaded, there is nothing to guess.
 * However, when a template is loaded from another one, then, we need
 * to find the current context and this is automatically done by
 * Twig_Template::display_with_error_handling().
 *
 * This strategy makes Twig_Environment::resolveTemplate() much faster.
 *
 * @package    Kohana/Twig
 * @category   Exception
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Exception_Loader extends Twig_Exception {

	public function __construct($message, $lineno = -1, $filename = NULL, Exception $previous = NULL)
	{
		parent::__construct($message, FALSE, FALSE, $previous);
	}
}
