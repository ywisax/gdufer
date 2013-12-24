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
class Twig_Node_Expression_BlockReference extends Twig_Node_Expression {

	public function __construct(Twig_Node $name, $asString = FALSE, $lineno, $tag = NULL)
	{
		parent::__construct(array('name' => $name), array('as_string' => $asString, 'output' => FALSE), $lineno, $tag);
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		if ($this->get_attribute('as_string'))
		{
			$compiler->raw('(string) ');
		}

		if ($this->get_attribute('output'))
		{
			$compiler
				->debug_info($this)
				->write("\$this->display_block(")
				->subcompile($this->getNode('name'))
				->raw(", \$context, \$blocks);\n");
		}
		else
		{
			$compiler
				->raw("\$this->render_block(")
				->subcompile($this->getNode('name'))
				->raw(", \$context, \$blocks)");
		}
	}
}
