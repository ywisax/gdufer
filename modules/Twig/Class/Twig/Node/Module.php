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
class Twig_Node_Module extends Twig_Node {

	public function __construct(Twig_Node $body, Twig_Node_Expression $parent = NULL, Twig_Node $blocks, Twig_Node $macros, Twig_Node $traits, $embedded_templates, $filename)
	{
		// embedded templates are set as attributes so that they are only visited once by the visitors
		parent::__construct(array('parent' => $parent, 'body' => $body, 'blocks' => $blocks, 'macros' => $macros, 'traits' => $traits), array('filename' => $filename, 'index' => NULL, 'embedded_templates' => $embedded_templates), 1);
	}

	public function set_index($index)
	{
		$this->set_attribute('index', $index);
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		$this->compile_template($compiler);
		foreach ($this->get_attribute('embedded_templates') AS $template)
		{
			$compiler->subcompile($template);
		}
	}

	protected function compile_template(Twig_Compiler $compiler)
	{
		if ( ! $this->get_attribute('index'))
		{
			$compiler->write('<?php');
		}

		$this->compileClassHeader($compiler);

		if (count($this->getNode('blocks')) || count($this->getNode('traits')) || $this->getNode('parent') === NULL || $this->getNode('parent') instanceof Twig_Node_Expression_Constant)
		{
			$this->compile_constructor($compiler);
		}

		$this->compile_get_parent($compiler);

		$this->compile_display_header($compiler);

		$this->compile_display_body($compiler);

		$this->compile_display_footer($compiler);

		$compiler->subcompile($this->getNode('blocks'));

		$this->compile_macros($compiler);

		$this->compile_get_template_name($compiler);

		$this->compile_is_traitable($compiler);

		$this->compile_debu_info($compiler);

		$this->compile_class_footer($compiler);
	}

	protected function compile_get_parent(Twig_Compiler $compiler)
	{
		if ($this->getNode('parent') === NULL)
		{
			return;
		}

		$compiler
			->write("protected function do_get_parent(array \$context)\n", "{\n")
			->indent()
			->write("return ");

		if ($this->getNode('parent') instanceof Twig_Node_Expression_Constant)
		{
			$compiler->subcompile($this->getNode('parent'));
		}
		else
		{
			$compiler
				->raw("\$this->env->resolveTemplate(")
				->subcompile($this->getNode('parent'))
				->raw(")");
		}

		$compiler
			->raw(";\n")
			->outdent()
			->write("}\n\n");
	}

	protected function compile_display_body(Twig_Compiler $compiler)
	{
		$compiler->subcompile($this->getNode('body'));

		if (null !== $this->getNode('parent'))
		{
			if ($this->getNode('parent') instanceof Twig_Node_Expression_Constant)
			{
				$compiler->write("\$this->parent");
			}
			else
			{
				$compiler->write("\$this->get_parent(\$context)");
			}
			$compiler->raw("->display(\$context, array_merge(\$this->blocks, \$blocks));\n");
		}
	}

	protected function compileClassHeader(Twig_Compiler $compiler)
	{
		$compiler
			->write("\n\n")
			// if the filename contains */, add a blank to avoid a PHP parse error
			->write("/* ".str_replace('*/', '* /', $this->get_attribute('filename'))." */\n")
			->write('class '.$compiler->get_environment()->get_template_class($this->get_attribute('filename'), $this->get_attribute('index')))
			->raw(sprintf(" extends %s\n", $compiler->get_environment()->base_template_class()))
			->write("{\n")
			->indent();
	}

	protected function compile_constructor(Twig_Compiler $compiler)
	{
		$compiler
			->write("public function __construct(Twig_Environment \$env)\n", "{\n")
			->indent()
			->write("parent::__construct(\$env);\n\n")
		;

		// parent
		if ($this->getNode('parent') === NULL)
		{
			$compiler->write("\$this->parent = FALSE;\n\n");
		}
		elseif ($this->getNode('parent') instanceof Twig_Node_Expression_Constant)
		{
			$compiler
				->write("\$this->parent = \$this->env->load_template(")
				->subcompile($this->getNode('parent'))
				->raw(");\n\n");
		}

		$countTraits = count($this->getNode('traits'));
		if ($countTraits)
		{
			// traits
			foreach ($this->getNode('traits') AS $i => $trait)
			{
				$this->compile_load_template($compiler, $trait->getNode('template'), sprintf('$_trait_%s', $i));

				$compiler
					->debug_info($trait->getNode('template'))
					->write(sprintf("if ( ! \$_trait_%s->is_traitable()) {\n", $i))
					->indent()
					->write("throw new Twig_Exception_Runtime('Template \"'.")
					->subcompile($trait->getNode('template'))
					->raw(".'\" cannot be used as a trait.');\n")
					->outdent()
					->write("}\n")
					->write(sprintf("\$_trait_%s_blocks = \$_trait_%s->get_blocks();\n\n", $i, $i));

				foreach ($trait->getNode('targets') AS $key => $value)
				{
					$compiler
						->write(sprintf("\$_trait_%s_blocks[", $i))
						->subcompile($value)
						->raw(sprintf("] = \$_trait_%s_blocks[", $i))
						->string($key)
						->raw(sprintf("]; unset(\$_trait_%s_blocks[", $i))
						->string($key)
						->raw("]);\n\n");
				}
			}

			if ($countTraits > 1)
			{
				$compiler
					->write("\$this->traits = array_merge(\n")
					->indent();

				for ($i = 0; $i < $countTraits; $i++)
				{
					$compiler
						->write(sprintf("\$_trait_%s_blocks".($i == $countTraits - 1 ? '' : ',')."\n", $i));
				}

				$compiler
					->outdent()
					->write(");\n\n");
			}
			else
			{
				$compiler
					->write("\$this->traits = \$_trait_0_blocks;\n\n");
			}

			$compiler
				->write("\$this->blocks = array_merge(\n")
				->indent()
				->write("\$this->traits,\n")
				->write("array(\n");
		}
		else
		{
			$compiler
				->write("\$this->blocks = array(\n");
		}

		// blocks
		$compiler
			->indent();

		foreach ($this->getNode('blocks') AS $name => $node)
		{
			$compiler
				->write(sprintf("'%s' => array(\$this, 'block_%s'),\n", $name, $name));
		}

		if ($countTraits)
		{
			$compiler
				->outdent()
				->write(")\n");
		}

		$compiler
			->outdent()
			->write(");\n\n");

		// macro information
		$compiler
			->write("\$this->macros = array(\n")
			->indent();

		foreach ($this->getNode('macros') AS $name => $node)
		{
			$compiler
				->add_indentation()->repr($name)->raw(" => array(\n")
				->indent()
				->write("'method' => ")->repr($node->get_attribute('method'))->raw(",\n")
				->write("'arguments' => array(\n")
				->indent();
			foreach ($node->getNode('arguments') AS $argument => $value)
			{
				$compiler->add_indentation()->repr($argument)->raw (' => ')->subcompile($value)->raw(",\n");
			}
			$compiler
				->outdent()
				->write("),\n")
				->outdent()
				->write("),\n");
		}
		$compiler
			->outdent()
			->write(");\n");

		$compiler
			->outdent()
			->write("}\n\n");
	}

	protected function compile_display_header(Twig_Compiler $compiler)
	{
		$compiler
			->write("protected function do_display(array \$context, array \$blocks = array())\n", "{\n")
			->indent();
	}

	protected function compile_display_footer(Twig_Compiler $compiler)
	{
		$compiler
			->outdent()
			->write("}\n\n");
	}

	protected function compile_class_footer(Twig_Compiler $compiler)
	{
		$compiler
			->outdent()
			->write("}\n");
	}

	protected function compile_macros(Twig_Compiler $compiler)
	{
		$compiler->subcompile($this->getNode('macros'));
	}

	protected function compile_get_template_name(Twig_Compiler $compiler)
	{
		$compiler
			->write("public function get_template_name()\n", "{\n")
			->indent()
			->write('return ')
			->repr($this->get_attribute('filename'))
			->raw(";\n")
			->outdent()
			->write("}\n\n");
	}

	protected function compile_is_traitable(Twig_Compiler $compiler)
	{
		// A template can be used as a trait if:
		//   * it has no parent
		//   * it has no macros
		//   * it has no body
		//
		// Put another way, a template can be used as a trait if it
		// only contains blocks and use statements.
		$traitable = NULL === $this->getNode('parent') && 0 === count($this->getNode('macros'));
		if ($traitable)
		{
			if ($this->getNode('body') instanceof Twig_Node_Body)
			{
				$nodes = $this->getNode('body')->getNode(0);
			}
			else
			{
				$nodes = $this->getNode('body');
			}

			if ( ! count($nodes))
			{
				$nodes = new Twig_Node(array($nodes));
			}

			foreach ($nodes AS $node)
			{
				if ( ! count($node))
				{
					continue;
				}

				if ($node instanceof Twig_Node_Text && ctype_space($node->get_attribute('data')))
				{
					continue;
				}

				if ($node instanceof Twig_Node_BlockReference)
				{
					continue;
				}

				$traitable = FALSE;
				break;
			}
		}

		if ($traitable)
		{
			return;
		}

		$compiler
			->write("public function is_traitable()\n", "{\n")
			->indent()
			->write(sprintf("return %s;\n", $traitable ? 'true' : 'false'))
			->outdent()
			->write("}\n\n");
	}

	protected function compile_debu_info(Twig_Compiler $compiler)
	{
		$compiler
			->write("public function debug_info()\n", "{\n")
			->indent()
			->write(sprintf("return %s;\n", str_replace("\n", '', var_export(array_reverse($compiler->debug_info(), TRUE), TRUE))))
			->outdent()
			->write("}\n");
	}

	protected function compile_load_template(Twig_Compiler $compiler, $node, $var)
	{
		if ($node instanceof Twig_Node_Expression_Constant)
		{
			$compiler
				->write(sprintf("%s = \$this->env->load_template(", $var))
				->subcompile($node)
				->raw(");\n");
		}
		else
		{
			$compiler
				->write(sprintf("%s = ", $var))
				->subcompile($node)
				->raw(";\n")
				->write(sprintf("if ( ! %s", $var))
				->raw(" instanceof Twig_Template) {\n")
				->indent()
				->write(sprintf("%s = \$this->env->load_template(%s);\n", $var, $var))
				->outdent()
				->write("}\n");
		}
	}
}
