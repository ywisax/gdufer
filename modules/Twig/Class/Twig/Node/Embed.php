<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents an embed node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Embed extends Twig_Node_Include {

    // we don't inject the module to avoid node visitors to traverse it twice (as it will be already visited in the main module)
	public function __construct($filename, $index, Twig_Node_Expression $variables = NULL, $only = FALSE, $ignoreMissing = FALSE, $lineno, $tag = NULL)
	{
		parent::__construct(new Twig_Node_Expression_Constant('not_used', $lineno), $variables, $only, $ignoreMissing, $lineno, $tag);

		$this->set_attribute('filename', $filename);
		$this->set_attribute('index', $index);
	}

	protected function add_get_template(Twig_Compiler $compiler)
	{
		$compiler
			->write("\$this->env->load_template(")
			->string($this->get_attribute('filename'))
			->raw(', ')
			->string($this->get_attribute('index'))
			->raw(")");
	}
}
