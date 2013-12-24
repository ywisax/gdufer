<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Checks if a variable is the same as another one (=== in PHP).
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Expression_Test_Sameas extends Twig_Node_Expression_Test {

	public function compile(Twig_Compiler $compiler)
	{
		$compiler
			->raw('(')
			->subcompile($this->getNode('node'))
			->raw(' === ')
			->subcompile($this->getNode('arguments')->getNode(0))
			->raw(')');
	}
}
