<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a security policy which need to be enforced when sandbox mode is enabled.
 *
 * @package    Kohana/Twig
 * @category   Sandbox
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Sandbox_Policy {

	protected $allowedTags;
	protected $allowedFilters;
	protected $allowedMethods;
	protected $allowedProperties;
	protected $allowedFunctions;

	public function __construct(array $allowedTags = array(), array $allowedFilters = array(), array $allowedMethods = array(), array $allowedProperties = array(), array $allowedFunctions = array())
	{
		$this->allowedTags = $allowedTags;
		$this->allowedFilters = $allowedFilters;
		$this->setAllowedMethods($allowedMethods);
		$this->allowedProperties = $allowedProperties;
		$this->allowedFunctions = $allowedFunctions;
	}

	public function setAllowedTags(array $tags)
	{
		$this->allowedTags = $tags;
	}

	public function setAllowedFilters(array $filters)
	{
		$this->allowedFilters = $filters;
	}

	public function setAllowedMethods(array $methods)
	{
		$this->allowedMethods = array();
		foreach ($methods AS $class => $m)
		{
			$this->allowedMethods[$class] = array_map('strtolower', is_array($m) ? $m : array($m));
		}
	}

	public function setAllowedProperties(array $properties)
	{
		$this->allowedProperties = $properties;
	}

	public function setAllowedFunctions(array $functions)
	{
		$this->allowedFunctions = $functions;
	}

	public function checkSecurity($tags, $filters, $functions)
	{
		foreach ($tags AS $tag)
		{
			if ( ! in_array($tag, $this->allowedTags))
			{
				throw new Twig_Sandbox_Exception(sprintf('Tag "%s" is not allowed.', $tag));
			}
		}

		foreach ($filters AS $filter)
		{
			if ( ! in_array($filter, $this->allowedFilters))
			{
				throw new Twig_Sandbox_Exception(sprintf('Filter "%s" is not allowed.', $filter));
			}
		}

		foreach ($functions AS $function)
		{
			if ( ! in_array($function, $this->allowedFunctions))
			{
				throw new Twig_Sandbox_Exception(sprintf('Function "%s" is not allowed.', $function));
			}
		}
	}

	public function checkMethodAllowed($obj, $method)
	{
		if ($obj instanceof Twig_Template || $obj instanceof Twig_Markup)
		{
			return TRUE;
		}

		$allowed = FALSE;
		$method = strtolower($method);
		foreach ($this->allowedMethods AS $class => $methods)
		{
			if ($obj instanceof $class)
			{
				$allowed = in_array($method, $methods);
				break;
			}
		}

		if ( ! $allowed)
		{
			throw new Twig_Sandbox_Exception(sprintf('Calling "%s" method on a "%s" object is not allowed.', $method, get_class($obj)));
		}
	}

	public function checkPropertyAllowed($obj, $property)
	{
		$allowed = FALSE;
		foreach ($this->allowedProperties AS $class => $properties)
		{
			if ($obj instanceof $class)
			{
				$allowed = in_array($property, is_array($properties) ? $properties : array($properties));

				break;
			}
		}

		if ( ! $allowed)
		{
			throw new Twig_Sandbox_Exception(sprintf('Calling "%s" property on a "%s" object is not allowed.', $property, get_class($obj)));
		}
	}
}
