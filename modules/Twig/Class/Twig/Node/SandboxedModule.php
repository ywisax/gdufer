<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a module node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_SandboxedModule extends Twig_Node_Module
{
	protected $usedFilters;
	protected $usedTags;
	protected $usedFunctions;

	public function __construct(Twig_Node_Module $node, array $usedFilters, array $usedTags, array $usedFunctions)
	{
		parent::__construct($node->getNode('body'), $node->getNode('parent'), $node->getNode('blocks'), $node->getNode('macros'), $node->getNode('traits'), $node->get_attribute('embedded_templates'), $node->get_attribute('filename'), $node->getLine(), $node->getNodeTag());

		$this->set_attribute('index', $node->get_attribute('index'));

		$this->usedFilters = $usedFilters;
		$this->usedTags = $usedTags;
		$this->usedFunctions = $usedFunctions;
	}

	protected function compile_display_body(Twig_Compiler $compiler)
	{
		$compiler->write("\$this->checkSecurity();\n");

		parent::compile_display_body($compiler);
	}

	protected function compile_display_footer(Twig_Compiler $compiler)
	{
		parent::compile_display_footer($compiler);

		$compiler
			->write("protected function checkSecurity()\n", "{\n")
			->indent()
			->write("\$this->env->getExtension('sandbox')->checkSecurity(\n")
			->indent()
			->write( ! $this->usedTags ? "array(),\n" : "array('".implode('\', \'', $this->usedTags)."'),\n")
			->write( ! $this->usedFilters ? "array(),\n" : "array('".implode('\', \'', $this->usedFilters)."'),\n")
			->write( ! $this->usedFunctions ? "array()\n" : "array('".implode('\', \'', $this->usedFunctions)."')\n")
			->outdent()
			->write(");\n")
			->outdent()
			->write("}\n\n");
	}
}
