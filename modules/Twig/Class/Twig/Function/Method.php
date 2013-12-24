<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a method template function. Use Twig_Simple_Function instead.
 *
 * @package    Kohana/Twig
 * @category   Function
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Function_Method extends Twig_Function {

	protected $extension;
	protected $method;

	public function __construct(Twig_Extension $extension, $method, array $options = array())
	{
		$options['callable'] = array($extension, $method);

		parent::__construct($options);

		$this->extension = $extension;
		$this->method = $method;
	}

	public function compile()
	{
		return sprintf('$this->env->getExtension(\'%s\')->%s', $this->extension->getName(), $this->method);
	}
}
