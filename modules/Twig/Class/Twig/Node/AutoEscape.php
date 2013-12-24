<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents an autoescape node.
 *
 * The value is the escaping strategy (can be html, js, ...)
 *
 * The true value is equivalent to html.
 *
 * If autoescaping is disabled, then the value is false.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_AutoEscape extends Twig_Node {

	public function __construct($value, Twig_Node $body, $lineno, $tag = 'autoescape')
	{
		parent::__construct(array('body' => $body), array('value' => $value), $lineno, $tag);
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		$compiler->subcompile($this->getNode('body'));
	}
}
