<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * XXXXX
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Expression_GetAttr extends Twig_Node_Expression {

	public function __construct(Twig_Node_Expression $node, Twig_Node_Expression $attribute, Twig_Node_Expression_Array $arguments, $type, $lineno)
	{
		parent::__construct(array('node' => $node, 'attribute' => $attribute, 'arguments' => $arguments), array('type' => $type, 'is_defined_test' => FALSE, 'ignore_strict_check' => FALSE, 'disable_c_ext' => FALSE), $lineno);
	}

	public function compile(Twig_Compiler $compiler)
	{
		if (function_exists('twig_template_get_attributes') && !$this->get_attribute('disable_c_ext'))
		{
			$compiler->raw('twig_template_get_attributes($this, ');
		}
		else
		{
			$compiler->raw('$this->get_attribute(');
		}

		if ($this->get_attribute('ignore_strict_check'))
		{
			$this->getNode('node')->set_attribute('ignore_strict_check', TRUE);
		}

		$compiler->subcompile($this->getNode('node'));

		$compiler->raw(', ')->subcompile($this->getNode('attribute'));

		if (count($this->getNode('arguments')) || Twig_Template::ANY_CALL !== $this->get_attribute('type') || $this->get_attribute('is_defined_test') || $this->get_attribute('ignore_strict_check'))
		{
			$compiler->raw(', ')->subcompile($this->getNode('arguments'));

			if (Twig_Template::ANY_CALL !== $this->get_attribute('type') || $this->get_attribute('is_defined_test') || $this->get_attribute('ignore_strict_check'))
			{
				$compiler->raw(', ')->repr($this->get_attribute('type'));
			}

			if ($this->get_attribute('is_defined_test') || $this->get_attribute('ignore_strict_check'))
			{
				$compiler->raw(', '.($this->get_attribute('is_defined_test') ? 'true' : 'false'));
			}

			if ($this->get_attribute('ignore_strict_check'))
			{
				$compiler->raw(', '.($this->get_attribute('ignore_strict_check') ? 'true' : 'false'));
			}
		}

		$compiler->raw(')');
	}
}
