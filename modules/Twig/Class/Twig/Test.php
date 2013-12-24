<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a template test.
 *
 * @package    Kohana/Twig
 * @category   Test
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Test {

	protected $options;
	protected $arguments = array();

	public function __construct(array $options = array())
	{
		$this->options = array_merge(array(
			'callable' => NULL,
		), $options);
	}

	public function getCallable()
	{
		return $this->options['callable'];
	}
}
