<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a block node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Block extends Twig_Node {

	public function __construct($name, Twig_Node $body, $lineno, $tag = NULL)
	{
		parent::__construct(array('body' => $body), array('name' => $name), $lineno, $tag);
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
			->write(sprintf("public function block_%s(\$context, array \$blocks = array())\n", $this->get_attribute('name')), "{\n")
			->indent();

		$compiler
			->subcompile($this->getNode('body'))
			->outdent()
			->write("}\n\n");
	}
}
