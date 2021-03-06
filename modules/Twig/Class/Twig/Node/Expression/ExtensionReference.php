<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents an extension call node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Expression_ExtensionReference extends Twig_Node_Expression {

	public function __construct($name, $lineno, $tag = NULL)
	{
		parent::__construct(array(), array('name' => $name), $lineno, $tag);
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		$compiler->raw(sprintf("\$this->env->getExtension('%s')", $this->get_attribute('name')));
	}
}
