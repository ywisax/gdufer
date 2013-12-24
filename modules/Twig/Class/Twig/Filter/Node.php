<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a template filter as a node. Use Twig_Simple_Filter instead.
 *
 * @package    Kohana/Twig
 * @category   Filter
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Filter_Node extends Twig_Filter {

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
