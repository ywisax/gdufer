<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Twig_Extension_Escaper
 *
 * @package    Kohana/Twig
 * @category   Extension
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Extension_Escaper extends Twig_Extension {

	protected $defaultStrategy;

	public function __construct($defaultStrategy = 'html')
	{
		$this->setDefaultStrategy($defaultStrategy);
	}

	/**
	 * Returns the token parser instances to add to the existing list.
	 *
	 * @return array An array of Twig_Token_Parser or Twig_Token_Parser_Broker instances
	 */
	public function get_token_parsers()
	{
		return array(new Twig_Token_Parser_AutoEscape());
	}

	/**
	 * Returns the node visitor instances to add to the existing list.
	 *
	 * @return array An array of Twig_Node_Visitor instances
	 */
	public function get_node_visitors()
	{
		return array(new Twig_Node_Visitor_Escaper());
	}

	/**
	 * Returns a list of filters to add to the existing list.
	 *
	 * @return array An array of filters
	 */
	public function get_filters()
	{
		return array(
			new Twig_Simple_Filter('raw', 'Twig_Extension::raw', array('is_safe' => array('all'))),
		);
	}

	/**
	 * Sets the default strategy to use when not defined by the user.
	 *
	 * The strategy can be a valid PHP callback that takes the template
	 * "filename" as an argument and returns the strategy to use.
	 *
	 * @param mixed $defaultStrategy An escaping strategy
	 */
	public function setDefaultStrategy($defaultStrategy)
	{
		// for BC
		if ($defaultStrategy === TRUE)
		{
			$defaultStrategy = 'html';
		}

		$this->defaultStrategy = $defaultStrategy;
	}

	/**
	 * Gets the default strategy to use when not defined by the user.
	 *
	 * @param string $filename The template "filename"
	 *
	 * @return string The default strategy to use for the template
	 */
	public function getDefaultStrategy($filename)
	{
		// disable string callables to avoid calling a function named html or js,
		// or any other upcoming escaping strategy
		if ( ! is_string($this->defaultStrategy) && is_callable($this->defaultStrategy))
		{
			return call_user_func($this->defaultStrategy, $filename);
		}

		return $this->defaultStrategy;
	}

	/**
	 * Returns the name of the extension.
	 *
	 * @return string The extension name
	 */
	public function getName()
	{
		return 'escaper';
	}
}
