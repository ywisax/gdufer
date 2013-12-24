<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Checks if a variable is the exact same value as a constant.
 *
 * <pre>
 *  {% if post.status is constant('Post::PUBLISHED') %}
 *    the status attribute is exactly the same as Post::PUBLISHED
 *  {% endif %}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Expression_Test_Constant extends Twig_Node_Expression_Test {

	public function compile(Twig_Compiler $compiler)
	{
		$compiler
			->raw('(')
			->subcompile($this->getNode('node'))
			->raw(' === constant(');

		if ($this->getNode('arguments')->hasNode(1))
		{
			$compiler
				->raw('get_class(')
				->subcompile($this->getNode('arguments')->getNode(1))
				->raw(')."::".');
		}

		$compiler
			->subcompile($this->getNode('arguments')->getNode(0))
			->raw('))');
	}

}
