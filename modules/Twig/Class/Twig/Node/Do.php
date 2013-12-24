<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a do node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Do extends Twig_Node {

	public function __construct(Twig_Node_Expression $expr, $lineno, $tag = NULL)
	{
		parent::__construct(array('expr' => $expr), array(), $lineno, $tag);
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		$compiler
			->debug_info($this)
			->write('')
			->subcompile($this->getNode('expr'))
			->raw(";\n");
	}
}
