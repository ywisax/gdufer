<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Loads a template from an array.
 *
 * When using this loader with a cache mechanism, you should know that a new cache
 * key is generated each time a template content "changes" (the cache key being the
 * source code of the template). If you don't want to see your cache grows out of
 * control, you need to take care of clearing the old cache file by yourself.
 *
 * @package    Kohana/Twig
 * @category   Loader
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Loader_Array extends Twig_Loader {

	protected $templates;

	/**
	 * Constructor.
	 *
	 * @param array $templates An array of templates (keys are the names, and values are the source code)
	 *
	 * @see Twig_Loader
	 */
	public function __construct(array $templates)
	{
		$this->templates = array();
		foreach ($templates AS $name => $template)
		{
			$this->templates[$name] = $template;
		}
	}

	/**
	 * Adds or overrides a template.
	 *
	 * @param string $name     The template name
	 * @param string $template The template source
	 */
	public function setTemplate($name, $template)
	{
		$this->templates[(string) $name] = $template;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_source($name)
	{
		$name = (string) $name;
		if ( ! isset($this->templates[$name]))
		{
			throw new Twig_Exception_Loader(sprintf('Template "%s" is not defined.', $name));
		}

		return $this->templates[$name];
	}

	/**
	 * {@inheritdoc}
	 */
	public function exists($name)
	{
		return isset($this->templates[(string) $name]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_cache_key($name)
	{
		$name = (string) $name;
		if ( ! isset($this->templates[$name]))
		{
			throw new Twig_Exception_Loader(sprintf('Template "%s" is not defined.', $name));
		}

		return $this->templates[$name];
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_fresh($name, $time)
	{
		$name = (string) $name;
		if ( ! isset($this->templates[$name]))
		{
			throw new Twig_Exception_Loader(sprintf('Template "%s" is not defined.', $name));
		}

		return TRUE;
	}
}
