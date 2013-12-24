<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a function template function. Use Twig_Simple_Function instead.
 *
 * @package    Kohana/Twig
 * @category   Function
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Function_Function extends Twig_Function {

	protected $function;

	public function __construct($function, array $options = array())
	{
		$options['callable'] = $function;

		parent::__construct($options);

		$this->function = $function;
	}

	public function compile()
	{
		return $this->function;
	}
}
