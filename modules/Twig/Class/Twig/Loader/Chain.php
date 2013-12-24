<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Loads templates from other loaders.
 *
 * @package    Kohana/Twig
 * @category   Loader
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Loader_Chain extends Twig_Loader {

    private $hasSourceCache = array();
	protected $loaders;

	/**
	 * Constructor.
	 *
	 * @param Twig_Loader[] $loaders An array of loader instances
	 */
	public function __construct(array $loaders = array())
	{
		$this->loaders = array();
		foreach ($loaders AS $loader)
		{
			$this->addLoader($loader);
		}
	}

	/**
	 * Adds a loader instance.
	 *
	 * @param Twig_Loader $loader A Loader instance
	 */
	public function addLoader(Twig_Loader $loader)
	{
		$this->loaders[] = $loader;
		$this->hasSourceCache = array();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_source($name)
	{
		$exceptions = array();
		foreach ($this->loaders AS $loader)
		{
			if ($loader instanceof Twig_Loader && ! $loader->exists($name))
			{
				continue;
			}

			try
			{
				return $loader->get_source($name);
			}
			catch (Twig_Exception_Loader $e)
			{
				$exceptions[] = $e->getMessage();
			}
		}

		throw new Twig_Exception_Loader(sprintf('Template "%s" is not defined (%s).', $name, implode(', ', $exceptions)));
	}

	/**
	 * {@inheritdoc}
	 */
	public function exists($name)
	{
		$name = (string) $name;

		if (isset($this->hasSourceCache[$name]))
		{
			return $this->hasSourceCache[$name];
		}

		foreach ($this->loaders AS $loader)
		{
			if ($loader instanceof Twig_Loader)
			{
				if ($loader->exists($name))
				{
					return $this->hasSourceCache[$name] = TRUE;
				}

				continue;
			}

			try
			{
				$loader->get_source($name);

				return $this->hasSourceCache[$name] = TRUE;
			}
			catch (Twig_Exception_Loader $e)
			{
			}
		}

		return $this->hasSourceCache[$name] = FALSE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_cache_key($name)
	{
		$exceptions = array();
		foreach ($this->loaders AS $loader)
		{
			if ($loader instanceof Twig_Loader && !$loader->exists($name))
			{
				continue;
			}

			try
			{
				return $loader->get_cache_key($name);
			}
			catch (Twig_Exception_Loader $e)
			{
				$exceptions[] = get_class($loader).': '.$e->getMessage();
			}
		}

		throw new Twig_Exception_Loader(sprintf('Template "%s" is not defined (%s).', $name, implode(' ', $exceptions)));
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_fresh($name, $time)
	{
		$exceptions = array();
		foreach ($this->loaders AS $loader)
		{
			if ($loader instanceof Twig_Loader && !$loader->exists($name))
			{
				continue;
			}

			try
			{
				return $loader->is_fresh($name, $time);
			}
			catch (Twig_Exception_Loader $e)
			{
				$exceptions[] = get_class($loader).': '.$e->getMessage();
			}
		}

		throw new Twig_Exception_Loader(sprintf('Template "%s" is not defined (%s).', $name, implode(' ', $exceptions)));
	}
}
