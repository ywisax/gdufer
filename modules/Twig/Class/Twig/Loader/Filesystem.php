<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 从文件系统加载模板
 *
 * @package    Kohana/Twig
 * @category   Loader
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Loader_Filesystem extends Twig_Loader {

	/** Identifier of the main namespace. */
	const MAIN_NAMESPACE = '__main__';

	protected $paths;
	protected $cache;

	/**
	 * Constructor.
	 *
	 * @param string|array $paths A path or an array of paths where to look for templates
	 */
	public function __construct($paths = array())
	{
		if ($paths)
		{
			$this->setPaths($paths);
		}
	}

	/**
	 * Returns the paths to the templates.
	 *
	 * @param string $namespace A path namespace
	 *
	 * @return array The array of paths where to look for templates
	 */
	public function getPaths($namespace = self::MAIN_NAMESPACE)
	{
		return isset($this->paths[$namespace]) ? $this->paths[$namespace] : array();
	}

	/**
	 * Returns the path namespaces.
	 *
	 * The main namespace is always defined.
	 *
	 * @return array The array of defined namespaces
	 */
	public function getNamespaces()
	{
		return array_keys($this->paths);
	}

	/**
	 * Sets the paths where templates are stored.
	 *
	 * @param string|array $paths     A path or an array of paths where to look for templates
	 * @param string       $namespace A path namespace
	 */
	public function setPaths($paths, $namespace = self::MAIN_NAMESPACE)
	{
		if ( ! is_array($paths))
		{
			$paths = array($paths);
		}

		$this->paths[$namespace] = array();
		foreach ($paths AS $path)
		{
			$this->addPath($path, $namespace);
		}
	}

	/**
	 * Adds a path where templates are stored.
	 *
	 * @param string $path      A path where to look for templates
	 * @param string $namespace A path name
	 *
	 * @throws Twig_Exception_Loader
	 */
	public function addPath($path, $namespace = self::MAIN_NAMESPACE)
	{
		// invalidate the cache
		$this->cache = array();

		if ( ! is_dir($path))
		{
			throw new Twig_Exception_Loader(sprintf('The "%s" directory does not exist.', $path));
		}

		$this->paths[$namespace][] = rtrim($path, '/\\');
	}

	/**
	 * Prepends a path where templates are stored.
	 *
	 * @param string $path      A path where to look for templates
	 * @param string $namespace A path name
	 *
	 * @throws Twig_Exception_Loader
	 */
	public function prependPath($path, $namespace = self::MAIN_NAMESPACE)
	{
		// invalidate the cache
		$this->cache = array();

		if ( ! is_dir($path))
		{
			throw new Twig_Exception_Loader(sprintf('The "%s" directory does not exist.', $path));
		}

		$path = rtrim($path, '/\\');

		if ( ! isset($this->paths[$namespace]))
		{
			$this->paths[$namespace][] = $path;
		}
		else
		{
			array_unshift($this->paths[$namespace], $path);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_source($name)
	{
		return file_get_contents($this->findTemplate($name));
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_cache_key($name)
	{
		return $this->findTemplate($name);
	}

	/**
	 * {@inheritdoc}
	 */
	public function exists($name)
	{
		$name = (string) $name;
		if (isset($this->cache[$name]))
		{
			return TRUE;
		}

		try
		{
			$this->findTemplate($name);

			return TRUE;
		}
		catch (Twig_Exception_Loader $exception)
		{
			return FALSE;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_fresh($name, $time)
	{
		return filemtime($this->findTemplate($name)) <= $time;
	}

	protected function findTemplate($name)
	{
		$name = (string) $name;
		// normalize name
		$name = preg_replace('#/{2,}#', '/', strtr($name, '\\', '/'));

		if (isset($this->cache[$name]))
		{
			return $this->cache[$name];
		}

		$this->validateName($name);

		$namespace = self::MAIN_NAMESPACE;
		$shortname = $name;
		if (isset($name[0]) && '@' == $name[0])
		{
			if (($pos = strpos($name, '/')) === FALSE)
			{
				throw new Twig_Exception_Loader(sprintf('Malformed namespaced template name "%s" (expecting "@namespace/template_name").', $name));
			}

			$namespace = substr($name, 1, $pos - 1);
			$shortname = substr($name, $pos + 1);
		}

		if ( ! isset($this->paths[$namespace]))
		{
			throw new Twig_Exception_Loader(sprintf('There are no registered paths for namespace "%s".', $namespace));
		}

		foreach ($this->paths[$namespace] AS $path)
		{
			if (is_file($path.'/'.$shortname))
			{
				return $this->cache[$name] = $path.'/'.$shortname;
			}
		}

		throw new Twig_Exception_Loader(sprintf('Unable to find template "%s" (looked into: %s).', $name, implode(', ', $this->paths[$namespace])));
	}

	protected function validateName($name)
	{
		if (strpos($name, "\0") !== FALSE)
		{
			throw new Twig_Exception_Loader('A template name cannot contain NUL bytes.');
		}

		$name = ltrim($name, '/');
		$parts = explode('/', $name);
		$level = 0;
		foreach ($parts AS $part)
		{
			if ('..' === $part)
			{
				--$level;
			}
			elseif ('.' !== $part)
			{
				++$level;
			}

			if ($level < 0)
			{
				throw new Twig_Exception_Loader(sprintf('Looks like you try to load a template outside configured directories (%s).', $name));
			}
		}
	}
}
