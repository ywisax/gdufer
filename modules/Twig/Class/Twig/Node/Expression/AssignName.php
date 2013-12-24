<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a block call node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Expression_AssignName extends Twig_Node_Expression_Name {

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		$compiler
			->raw('$context[')
			->string($this->get_attribute('name'))
			->raw(']');
	}
}
