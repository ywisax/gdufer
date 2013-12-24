<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a template test as a Node.
 *
 * @package    Kohana/Twig
 * @category   Test
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Test_Node extends Twig_Test {

	protected $class;

	public function __construct($class, array $options = array())
	{
		parent::__construct($options);

		$this->class = $class;
	}

	public function getClass()
	{
		return $this->class;
	}

	public function compile()
	{
	}
}
