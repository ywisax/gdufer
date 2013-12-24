<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a function template test.
 *
 * @package    Kohana/Twig
 * @category   Test
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Test_Function extends Twig_Test {

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
