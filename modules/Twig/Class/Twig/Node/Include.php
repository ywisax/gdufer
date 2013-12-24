<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents an include node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Include extends Twig_Node {

	public function __construct(Twig_Node_Expression $expr, Twig_Node_Expression $variables = NULL, $only = FALSE, $ignoreMissing = FALSE, $lineno, $tag = NULL)
	{
		parent::__construct(array('expr' => $expr, 'variables' => $variables), array('only' => (Boolean) $only, 'ignore_missing' => (Boolean) $ignoreMissing), $lineno, $tag);
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		$compiler->debug_info($this);

		if ($this->get_attribute('ignore_missing'))
		{
			$compiler
				->write("try {\n")
				->indent();
		}

		$this->add_get_template($compiler);

		$compiler->raw('->display(');

		$this->add_template_arguments($compiler);

		$compiler->raw(");\n");

		if ($this->get_attribute('ignore_missing'))
		{
			$compiler
				->outdent()
				->write("} catch (Twig_Exception_Loader \$e) {\n")
				->indent()
				->write("// ignore missing template\n")
				->outdent()
				->write("}\n\n");
		}
	}

	protected function add_get_template(Twig_Compiler $compiler)
	{
		if ($this->getNode('expr') instanceof Twig_Node_Expression_Constant)
		{
			$compiler
				->write("\$this->env->load_template(")
				->subcompile($this->getNode('expr'))
				->raw(")");
		}
		else
		{
			$compiler
				->write("\$template = \$this->env->resolveTemplate(")
				->subcompile($this->getNode('expr'))
				->raw(");\n")
				->write('$template');
		}
	}

	protected function add_template_arguments(Twig_Compiler $compiler)
	{
		if ($this->get_attribute('only') === FALSE)
		{
			if ($this->getNode('variables') === NULL)
			{
				$compiler->raw('$context');
			}
			else
			{
				$compiler
					->raw('array_merge($context, ')
					->subcompile($this->getNode('variables'))
					->raw(')');
			}
		}
		else
		{
			if ($this->getNode('variables') === NULL)
			{
				$compiler->raw('array()');
			}
			else
			{
				$compiler->subcompile($this->getNode('variables'));
			}
		}
	}
}
