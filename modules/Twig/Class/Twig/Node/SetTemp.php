<?php defined('SYS_PATH') or die('No direct script access.');

class Twig_Node_SetTemp extends Twig_Node
{
	public function __construct($name, $lineno)
	{
		parent::__construct(array(), array('name' => $name), $lineno);
	}

	public function compile(Twig_Compiler $compiler)
	{
		$name = $this->get_attribute('name');
		$compiler
			->debug_info($this)
			->write('if (isset($context[')
			->string($name)
			->raw('])) { $_')
			->raw($name)
			->raw('_ = $context[')
			->repr($name)
			->raw(']; } else { $_')
			->raw($name)
			->raw("_ = NULL; }\n");
	}
}
