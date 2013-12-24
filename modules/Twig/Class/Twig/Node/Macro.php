<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a macro node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Macro extends Twig_Node
{
	public function __construct($name, Twig_Node $body, Twig_Node $arguments, $lineno, $tag = NULL)
	{
		parent::__construct(array('body' => $body, 'arguments' => $arguments), array('name' => $name, 'method' => 'get'.ucfirst($name)), $lineno, $tag);
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
			->write(sprintf("public function %s(", $this->get_attribute('method')));

		$count = count($this->getNode('arguments'));
		$pos = 0;
		foreach ($this->getNode('arguments') AS $name => $default)
		{
			$compiler
				->raw('$_'.$name.' = ')
				->subcompile($default);

			if (++$pos < $count)
			{
				$compiler->raw(', ');
			}
		}

		$compiler
			->raw(")\n")
			->write("{\n")
			->indent();

		if ( ! count($this->getNode('arguments')))
		{
			$compiler->write("\$context = \$this->env->getGlobals();\n\n");
		}
		else
		{
			$compiler
				->write("\$context = \$this->env->mergeGlobals(array(\n")
				->indent();

			foreach ($this->getNode('arguments') AS $name => $default)
			{
				$compiler
					->write('')
					->string($name)
					->raw(' => $_'.$name)
					->raw(",\n");
			}

			$compiler
				->outdent()
				->write("));\n\n");
		}

		$compiler
			->write("\$blocks = array();\n\n")
			->write("ob_start();\n")
			->write("try {\n")
			->indent()
			->subcompile($this->getNode('body'))
			->outdent()
			->write("} catch (Exception \$e) {\n")
			->indent()
			->write("ob_end_clean();\n\n")
			->write("throw \$e;\n")
			->outdent()
			->write("}\n\n")
			->write("return ('' === \$tmp = ob_get_clean()) ? '' : new Twig_Markup(\$tmp, \$this->env->getCharset());\n")
			->outdent()
			->write("}\n\n");
	}
}
