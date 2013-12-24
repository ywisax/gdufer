<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Stores the Twig configuration.
 *
 * @package    Kohana/Twig
 * @category   Base
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Environment {

	protected $charset;
	protected $loader;
	protected $debug;
	protected $autoReload;
	protected $cache;
	protected $lexer;
	protected $parser;
	protected $compiler;
	protected $base_template_class;
	protected $extensions;
	protected $parsers;
	protected $visitors;
	protected $filters;
	protected $tests;
	protected $functions;
	protected $globals;
	protected $runtime_initialized;
	protected $extension_initialized;
	protected $loaded_templates;
	protected $strictVariables;
	protected $unaryOperators;
	protected $binary_operators;
	protected $template_class_prefix = '__XunSecTemplate_';
	protected $function_callbacks;
	protected $filter_callbacks;
	protected $staging;
	protected $template_classes;

	/**
	 * Constructor.
	 *
	 * Available options:
	 *
	 *  * debug: When set to true, it automatically set "auto_reload" to true as
	 *           well (default to false).
	 *
	 *  * charset: The charset used by the templates (default to UTF-8).
	 *
	 *  * base_template_class: The base template class to use for generated
	 *                         templates (default to Twig_Template).
	 *
	 *  * cache: An absolute path where to store the compiled templates, or
	 *           false to disable compilation cache (default).
	 *
	 *  * auto_reload: Whether to reload the template is the original source changed.
	 *                 If you don't provide the auto_reload option, it will be
	 *                 determined automatically base on the debug value.
	 *
	 *  * strict_variables: Whether to ignore invalid variables in templates
	 *                      (default to false).
	 *
	 *  * autoescape: Whether to enable auto-escaping (default to html):
	 *             	 * false: disable auto-escaping
	 *             	 * true: equivalent to html
	 *             	 * html, js: set the autoescaping to one of the supported strategies
	 *             	 * PHP callback: a PHP callback that returns an escaping strategy based on the template "filename"
	 *
	 *  * optimizations: A flag that indicates which optimizations to apply
	 *                   (default to -1 which means that all optimizations are enabled;
	 *                   set it to 0 to disable).
	 *
	 * @param Twig_Loader $loader  A Twig_Loader instance
	 * @param array                $options An array of options
	 */
	public function __construct(Twig_Loader $loader = NULL, $options = array())
	{
		if ($loader !== NULL)
		{
			$this->setLoader($loader);
		}

		$options = array_merge(array(
			'debug'               => FALSE,
			'charset'             => 'UTF-8',
			'base_template_class' => 'Twig_Template',
			'strict_variables'    => FALSE,
			'autoescape'          => 'html',
			'cache'               => FALSE,
			'auto_reload'         => NULL,
			'optimizations'       => -1,
		), $options);

		$this->debug              = (bool) $options['debug'];
		$this->charset            = strtoupper($options['charset']);
		$this->base_template_class  = $options['base_template_class'];
		$this->autoReload         = NULL === $options['auto_reload'] ? $this->debug : (bool) $options['auto_reload'];
		$this->strictVariables    = (bool) $options['strict_variables'];
		$this->runtime_initialized = FALSE;
		$this->setCache($options['cache']);
		$this->function_callbacks = array();
		$this->filter_callbacks = array();
		$this->template_classes = array();

		$this->add_extension(new Twig_Extension_Core());
		$this->add_extension(new Twig_Extension_Escaper($options['autoescape']));
		$this->add_extension(new Twig_Extension_Optimizer($options['optimizations']));
		$this->extension_initialized = FALSE;
		$this->staging = new Twig_Extension_Staging();
	}
	
	/**
	 * 设置或获取base_template_class
	 */
	public function base_template_class($class = NULL)
	{
		if ($class === NULL)
		{
			return $this->base_template_class;
		}
		$this->base_template_class = $class;
	}

	/**
	 * Enables debugging mode.
	 */
	public function enableDebug()
	{
		$this->debug = TRUE;
	}

	/**
	 * Disables debugging mode.
	 */
	public function disableDebug()
	{
		$this->debug = FALSE;
	}

	/**
	 * Checks if debug mode is enabled.
	 *
	 * @return Boolean true if debug mode is enabled, false otherwise
	 */
	public function isDebug()
	{
		return $this->debug;
	}

	/**
	 * Enables the auto_reload option.
	 */
	public function enableAutoReload()
	{
		$this->autoReload = TRUE;
	}

	/**
	 * Disables the auto_reload option.
	 */
	public function disableAutoReload()
	{
		$this->autoReload = FALSE;
	}

	/**
	 * Checks if the auto_reload option is enabled.
	 *
	 * @return Boolean true if auto_reload is enabled, false otherwise
	 */
	public function isAutoReload()
	{
		return $this->autoReload;
	}

	/**
	 * Enables the strict_variables option.
	 */
	public function enableStrictVariables()
	{
		$this->strictVariables = TRUE;
	}

	/**
	 * Disables the strict_variables option.
	 */
	public function disableStrictVariables()
	{
		$this->strictVariables = FALSE;
	}

	/**
	 * Checks if the strict_variables option is enabled.
	 *
	 * @return Boolean true if strict_variables is enabled, false otherwise
	 */
	public function isStrictVariables()
	{
		return $this->strictVariables;
	}

	/**
	 * Gets the cache directory or false if cache is disabled.
	 *
	 * @return string|false
	 */
	public function getCache()
	{
		return $this->cache;
	}

 	/**
 	 * Sets the cache directory or false if cache is disabled.
 	 *
 	 * @param string|false $cache The absolute path to the compiled templates,
 	 *                            or false to disable cache
 	 */
	public function setCache($cache)
	{
		$this->cache = $cache ? $cache : FALSE;
	}

	/**
	 * Gets the cache filename for a given template.
	 *
	 * @param string $name The template name
	 *
	 * @return string The cache file name
	 */
	public function getCacheFilename($name)
	{
		if ($this->cache === FALSE)
		{
			return FALSE;
		}

		$class = substr($this->get_template_class($name), strlen($this->template_class_prefix));

		return $this->getCache().'/'.substr($class, 0, 2).'/'.substr($class, 2, 2).'/'.substr($class, 4).'.php';
	}

	/**
	 * Gets the template class associated with the given string.
	 *
	 * @param string  $name  The name for which to calculate the template class name
	 * @param integer $index The index if it is an embedded template
	 *
	 * @return string The template class name
	 */
	public function get_template_class($name, $index = NULL)
	{
		$suffix = ($index === NULL) ? '' : '_'.$index;
		$cls = $name.$suffix;
		if (isset($this->template_classes[$cls]))
		{
			return $this->template_classes[$cls];
		}

		return $this->template_classes[$cls] = $this->template_class_prefix.hash('sha256', $this->getLoader()->get_cache_key($name)).$suffix;
	}

	/**
	 * Gets the template class prefix.
	 *
	 * @return string The template class prefix
	 */
	public function gettemplate_class_prefix()
	{
		return $this->template_class_prefix;
	}

	/**
	 * Renders a template.
	 *
	 * @param string $name    The template name
	 * @param array  $context An array of parameters to pass to the template
	 *
	 * @return string The rendered template
	 */
	public function render($name, array $context = array())
	{
		return $this->load_template($name)->render($context);
	}

	/**
	 * Displays a template.
	 *
	 * @param string $name    The template name
	 * @param array  $context An array of parameters to pass to the template
	 */
	public function display($name, array $context = array())
	{
		$this->load_template($name)->display($context);
	}

	/**
	 * Loads a template by name.
	 *
	 * @param string  $name  The template name
	 * @param integer $index The index if it is an embedded template
	 *
	 * @return Twig_Template A template instance representing the given template name
	 */
	public function load_template($name, $index = NULL)
	{
		$cls = $this->get_template_class($name, $index);

		if (isset($this->loaded_templates[$cls]))
		{
			return $this->loaded_templates[$cls];
		}

		if ( ! class_exists($cls, FALSE))
		{
			if (($cache = $this->getCacheFilename($name)) === FALSE)
			{
				eval('?>'.$this->compile_source($this->getLoader()->get_source($name), $name));
			}
			else
			{
				if ( ! is_file($cache) || ($this->isAutoReload() && !$this->isTemplateFresh($name, filemtime($cache))))
				{
					$this->writeCacheFile($cache, $this->compile_source($this->getLoader()->get_source($name), $name));
				}

				require_once $cache;
			}
		}

		if ( ! $this->runtime_initialized)
		{
			$this->init_runtime();
		}

		return $this->loaded_templates[$cls] = new $cls($this);
	}

	/**
	 * Returns true if the template is still fresh.
	 *
	 * Besides checking the loader for freshness information,
	 * this method also checks if the enabled extensions have
	 * not changed.
	 *
	 * @param string    $name The template name
	 * @param timestamp $time The last modification time of the cached template
	 *
	 * @return Boolean true if the template is fresh, false otherwise
	 */
	public function isTemplateFresh($name, $time)
	{
		foreach ($this->extensions AS $extension)
		{
			$r = new ReflectionObject($extension);
			if (filemtime($r->getFileName()) > $time)
			{
				return FALSE;
			}
		}

		return $this->getLoader()->is_fresh($name, $time);
	}

	public function resolveTemplate($names)
	{
		if ( ! is_array($names))
		{
			$names = array($names);
		}

		foreach ($names AS $name)
		{
			if ($name instanceof Twig_Template)
			{
				return $name;
			}

			try
			{
				return $this->load_template($name);
			}
			catch (Twig_Exception_Loader $e)
			{
			}
		}

		if (1 === count($names))
		{
			throw $e;
		}

		throw new Twig_Exception_Loader(sprintf('Unable to find one of the following templates: "%s".', implode('", "', $names)));
	}

	/**
	 * Clears the internal template cache.
	 */
	public function clearTemplateCache()
	{
		$this->loaded_templates = array();
	}

	/**
	 * Clears the template cache files on the filesystem.
	 */
	public function clear_cache_files()
	{
		if ($this->cache === FALSE)
		{
			return;
		}

		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->cache), RecursiveIteratorIterator::LEAVES_ONLY) AS $file)
		{
			if ($file->isFile())
			{
				@unlink($file->getPathname());
			}
		}
	}

	/**
	 * Gets the Lexer instance.
	 *
	 * @return Twig_Lexer A Twig_Lexer instance
	 */
	public function getLexer()
	{
		if ($this->lexer === NULL)
		{
			$this->lexer = new Twig_Lexer($this);
		}

		return $this->lexer;
	}

	/**
	 * Sets the Lexer instance.
	 *
	 * @param Twig_Lexer A Twig_Lexer instance
	 */
	public function setLexer(Twig_Lexer $lexer)
	{
		$this->lexer = $lexer;
	}

	/**
	 * Tokenizes a source code.
	 *
	 * @param string $source The template source code
	 * @param string $name   The template name
	 *
	 * @return Twig_Token_Stream A Twig_Token_Stream instance
	 */
	public function tokenize($source, $name = NULL)
	{
		return $this->getLexer()->tokenize($source, $name);
	}

	/**
	 * Gets the Parser instance.
	 *
	 * @return Twig_Parser A Twig_Parser instance
	 */
	public function getParser()
	{
		if ($this->parser === NULL)
		{
			$this->parser = new Twig_Parser($this);
		}

		return $this->parser;
	}

	/**
	 * Sets the Parser instance.
	 *
	 * @param Twig_Parser A Twig_Parser instance
	 */
	public function setParser(Twig_Parser $parser)
	{
		$this->parser = $parser;
	}

	/**
	 * Parses a token stream.
	 *
	 * @param Twig_Token_Stream $tokens A Twig_Token_Stream instance
	 *
	 * @return Twig_Node_Module A Node tree
	 */
	public function parse(Twig_Token_Stream $tokens)
	{
		return $this->getParser()->parse($tokens);
	}

	/**
	 * Gets the Compiler instance.
	 *
	 * @return Twig_Compiler A Twig_Compiler instance
	 */
	public function getCompiler()
	{
		if ($this->compiler === NULL)
		{
			$this->compiler = new Twig_Compiler($this);
		}

		return $this->compiler;
	}

	/**
	 * Sets the Compiler instance.
	 *
	 * @param Twig_Compiler $compiler A Twig_Compiler instance
	 */
	public function setCompiler(Twig_Compiler $compiler)
	{
		$this->compiler = $compiler;
	}

	/**
	 * Compiles a Node.
	 *
	 * @param Twig_Node $node A Twig_Node instance
	 *
	 * @return string The compiled PHP source code
	 */
	public function compile(Twig_Node $node)
	{
		return $this->getCompiler()->compile($node)->get_source();
	}

	/**
	 * Compiles a template source code.
	 *
	 * @param string $source The template source code
	 * @param string $name   The template name
	 *
	 * @return string The compiled PHP source code
	 */
	public function compile_source($source, $name = NULL)
	{
		try
		{
			return $this->compile($this->parse($this->tokenize($source, $name)));
		}
		catch (Twig_Exception $e)
		{
			$e->setTemplateFile($name);
			throw $e;
		}
		catch (Exception $e)
		{
			throw new Twig_Exception_Runtime(sprintf('An exception has been thrown during the compilation of a template ("%s").', $e->getMessage()), -1, $name, $e);
		}
	}

	/**
	 * Sets the Loader instance.
	 *
	 * @param Twig_Loader $loader A Twig_Loader instance
	 */
	public function setLoader(Twig_Loader $loader)
	{
		$this->loader = $loader;
	}

	/**
	 * Gets the Loader instance.
	 *
	 * @return Twig_Loader A Twig_Loader instance
	 */
	public function getLoader()
	{
		if ($this->loader === NULL)
		{
			throw new LogicException('You must set a loader first.');
		}

		return $this->loader;
	}

	/**
	 * Sets the default template charset.
	 *
	 * @param string $charset The default charset
	 */
	public function setCharset($charset)
	{
		$this->charset = strtoupper($charset);
	}

	/**
	 * Gets the default template charset.
	 *
	 * @return string The default charset
	 */
	public function getCharset()
	{
		return $this->charset;
	}

	/**
	 * Initializes the runtime environment.
	 */
	public function init_runtime()
	{
		$this->runtime_initialized = TRUE;

		foreach ($this->get_extensions() AS $extension)
		{
			$extension->init_runtime($this);
		}
	}

	/**
	 * Returns true if the given extension is registered.
	 *
	 * @param string $name The extension name
	 *
	 * @return Boolean Whether the extension is registered or not
	 */
	public function hasExtension($name)
	{
		return isset($this->extensions[$name]);
	}

	/**
	 * Gets an extension by name.
	 *
	 * @param string $name The extension name
	 *
	 * @return Twig_Extension A Twig_Extension instance
	 */
	public function getExtension($name)
	{
		if ( ! isset($this->extensions[$name]))
		{
			throw new Twig_Exception_Runtime(sprintf('The "%s" extension is not enabled.', $name));
		}

		return $this->extensions[$name];
	}

	/**
	 * Registers an extension.
	 *
	 * @param Twig_Extension $extension A Twig_Extension instance
	 */
	public function add_extension(Twig_Extension $extension)
	{
		if ($this->extension_initialized)
		{
			throw new LogicException(sprintf('Unable to register extension "%s" as extensions have already been initialized.', $extension->getName()));
		}

		$this->extensions[$extension->getName()] = $extension;
	}

	/**
	 * Removes an extension by name.
	 *
	 * This method is deprecated and you should not use it.
	 *
	 * @param string $name The extension name
	 */
	public function removeExtension($name)
	{
		if ($this->extension_initialized)
		{
			throw new LogicException(sprintf('Unable to remove extension "%s" as extensions have already been initialized.', $name));
		}

		unset($this->extensions[$name]);
	}

	/**
	 * Registers an array of extensions.
	 *
	 * @param array $extensions An array of extensions
	 */
	public function setExtensions(array $extensions)
	{
		foreach ($extensions AS $extension)
		{
			$this->add_extension($extension);
		}
	}

	/**
	 * Returns all registered extensions.
	 *
	 * @return array An array of extensions
	 */
	public function get_extensions()
	{
		return $this->extensions;
	}

	/**
	 * Registers a Token Parser.
	 *
	 * @param Twig_Token_Parser $parser A Twig_Token_Parser instance
	 */
	public function addTokenParser(Twig_Token_Parser $parser)
	{
		if ($this->extension_initialized)
		{
			throw new LogicException('Unable to add a token parser as extensions have already been initialized.');
		}

		$this->staging->addTokenParser($parser);
	}

	/**
	 * Gets the registered Token Parsers.
	 *
	 * @return Twig_Token_Parser_Broker A broker containing token parsers
	 */
	public function get_token_parsers()
	{
		if ( ! $this->extension_initialized)
		{
			$this->init_extensions();
		}

		return $this->parsers;
	}

	/**
	 * Gets registered tags.
	 *
	 * Be warned that this method cannot return tags defined by Twig_Token_Parser_Broker classes.
	 *
	 * @return Twig_Token_Parser[] An array of Twig_Token_Parser instances
	 */
	public function getTags()
	{
		$tags = array();
		foreach ($this->get_token_parsers()->getParsers() AS $parser)
		{
			if ($parser instanceof Twig_Token_Parser)
			{
				$tags[$parser->getTag()] = $parser;
			}
		}

		return $tags;
	}

	/**
	 * Registers a Node Visitor.
	 *
	 * @param Twig_Node_Visitor $visitor A Twig_Node_Visitor instance
	 */
	public function add_node_visitor(Twig_Node_Visitor $visitor)
	{
		if ($this->extension_initialized)
		{
			throw new LogicException('Unable to add a node visitor as extensions have already been initialized.');
		}

		$this->staging->add_node_visitor($visitor);
	}

	/**
	 * Gets the registered Node Visitors.
	 *
	 * @return Twig_Node_Visitor[] An array of Twig_Node_Visitor instances
	 */
	public function get_node_visitors()
	{
		if ( ! $this->extension_initialized)
		{
			$this->init_extensions();
		}

		return $this->visitors;
	}

	/**
	 * Registers a Filter.
	 *
	 * @param string|Twig_Simple_Filter               $name   The filter name or a Twig_Simple_Filter instance
	 * @param Twig_Filter|Twig_Simple_Filter $filter A Twig_Filter instance or a Twig_Simple_Filter instance
	 */
	public function addFilter($name, $filter = NULL)
	{
		if ( ! $name instanceof Twig_Simple_Filter && !($filter instanceof Twig_Simple_Filter || $filter instanceof Twig_Filter))
		{
			throw new LogicException('A filter must be an instance of Twig_Filter or Twig_Simple_Filter');
		}

		if ($name instanceof Twig_Simple_Filter)
		{
			$filter = $name;
			$name = $filter->getName();
		}
		
		if ($this->extension_initialized)
		{
			throw new LogicException(sprintf('Unable to add filter "%s" as extensions have already been initialized.', $name));
		}
		
		$this->staging->addFilter($name, $filter);
	}

	/**
	 * Get a filter by name.
	 *
	 * Subclasses may override this method and load filters differently;
	 * so no list of filters is available.
	 *
	 * @param string $name The filter name
	 *
	 * @return Twig_Filter|false A Twig_Filter instance or false if the filter does not exist
	 */
	public function getFilter($name)
	{
		if ( ! $this->extension_initialized)
		{
			$this->init_extensions();
		}

		if (isset($this->filters[$name]))
		{
			return $this->filters[$name];
		}

		foreach ($this->filters AS $pattern => $filter)
		{
			$pattern = str_replace('\\*', '(.*?)', preg_quote($pattern, '#'), $count);

			if ($count)
			{
				if (preg_match('#^'.$pattern.'$#', $name, $matches))
				{
					array_shift($matches);
					$filter->setArguments($matches);

					return $filter;
				}
			}
		}

		foreach ($this->filter_callbacks AS $callback)
		{
			if (($filter = call_user_func($callback, $name)) !== FALSE)
			{
				return $filter;
			}
		}

		return FALSE;
	}

	public function registerUndefinedFilterCallback($callable)
	{
		$this->filter_callbacks[] = $callable;
	}

	/**
	 * Gets the registered Filters.
	 *
	 * Be warned that this method cannot return filters defined with registerUndefinedFunctionCallback.
	 *
	 * @return Twig_Filter[] An array of Twig_Filter instances
	 *
	 * @see registerUndefinedFilterCallback
	 */
	public function get_filters()
	{
		if ( ! $this->extension_initialized)
		{
			$this->init_extensions();
		}

		return $this->filters;
	}

	/**
	 * Registers a Test.
	 *
	 * @param string|Twig_Simple_Test             $name The test name or a Twig_Simple_Test instance
	 * @param Twig_Test|Twig_Simple_Test $test A Twig_Test instance or a Twig_Simple_Test instance
	 */
	public function addTest($name, $test = NULL)
	{
		if ( ! $name instanceof Twig_Simple_Test && ! ($test instanceof Twig_Simple_Test || $test instanceof Twig_Test))
		{
			throw new LogicException('A test must be an instance of Twig_Test or Twig_Simple_Test');
		}

		if ($name instanceof Twig_Simple_Test)
		{
			$test = $name;
			$name = $test->getName();
		}
		
		if ($this->extension_initialized)
		{
			throw new LogicException(sprintf('Unable to add test "%s" as extensions have already been initialized.', $name));
		}

		$this->staging->addTest($name, $test);
	}

	/**
	 * Gets the registered Tests.
	 *
	 * @return Twig_Test[] An array of Twig_Test instances
	 */
	public function getTests()
	{
		if ( ! $this->extension_initialized)
		{
			$this->init_extensions();
		}
		return $this->tests;
	}

	/**
	 * Gets a test by name.
	 *
	 * @param string $name The test name
	 *
	 * @return Twig_Test|false A Twig_Test instance or false if the test does not exist
	 */
	public function getTest($name)
	{
		if ( ! $this->extension_initialized)
		{
			$this->init_extensions();
		}

		if (isset($this->tests[$name]))
		{
			return $this->tests[$name];
		}

		return FALSE;
	}

	/**
	 * Registers a Function.
	 *
	 * @param string|Twig_Simple_Function                 $name     The function name or a Twig_Simple_Function instance
	 * @param Twig_Function|Twig_Simple_Function $function A Twig_Function instance or a Twig_Simple_Function instance
	 */
	public function addFunction($name, $function = NULL)
	{
		if ( ! $name instanceof Twig_Simple_Function && !($function instanceof Twig_Simple_Function || $function instanceof Twig_Function))
		{
			throw new LogicException('A function must be an instance of Twig_Function or Twig_Simple_Function');
		}

		if ($name instanceof Twig_Simple_Function)
		{
			$function = $name;
			$name = $function->getName();
		}
		
		if ($this->extension_initialized)
		{
			throw new LogicException(sprintf('Unable to add function "%s" as extensions have already been initialized.', $name));
		}
		
		$this->staging->addFunction($name, $function);
	}
	
	const FUNCTION_PATTERN_REGEX = '\\*';
	const FUNCTION_PATTERN_REPLACE = '(.*?)';

	/**
	 * Get a function by name.
	 *
	 * Subclasses may override this method and load functions differently;
	 * so no list of functions is available.
	 *
	 * @param string $name function name
	 *
	 * @return Twig_Function|false A Twig_Function instance or false if the function does not exist
	 */
	public function getFunction($name)
	{
		if ( ! $this->extension_initialized)
		{
			$this->init_extensions();
		}

		if (isset($this->functions[$name]))
		{
			return $this->functions[$name];
		}

		foreach ($this->functions AS $pattern => $function)
		{
			$pattern = str_replace(Twig_Environment::FUNCTION_PATTERN_REGEX, Twig_Environment::FUNCTION_PATTERN_REPLACE, preg_quote($pattern, '#'), $count);

			if ($count)
			{
				if (preg_match('#^'.$pattern.'$#', $name, $matches))
				{
					array_shift($matches);
					$function->setArguments($matches);
					return $function;
				}
			}
		}

		foreach ($this->function_callbacks AS $callback)
		{
			if (FALSE !== $function = call_user_func($callback, $name))
			{
				return $function;
			}
		}

		return FALSE;
	}

	public function registerUndefinedFunctionCallback($callable)
	{
		$this->function_callbacks[] = $callable;
	}

	/**
	 * Gets registered functions.
	 *
	 * Be warned that this method cannot return functions defined with registerUndefinedFunctionCallback.
	 *
	 * @return Twig_Function[] An array of Twig_Function instances
	 *
	 * @see registerUndefinedFunctionCallback
	 */
	public function get_functions()
	{
		if ( ! $this->extension_initialized)
		{
			$this->init_extensions();
		}

		return $this->functions;
	}

	/**
	 * Registers a Global.
	 *
	 * New globals can be added before compiling or rendering a template;
	 * but after, you can only update existing globals.
	 *
	 * @param string $name  The global name
	 * @param mixed  $value The global value
	 */
	public function addGlobal($name, $value)
	{
		if ($this->extension_initialized || $this->runtime_initialized)
		{
			if ($this->globals === NULL)
			{
				$this->globals = $this->init_globals();
			}
		}

		if ($this->extension_initialized || $this->runtime_initialized)
		{
			$this->globals[$name] = $value;
		}
		else
		{
			$this->staging->addGlobal($name, $value);
		}
	}

	/**
	 * Gets the registered Globals.
	 *
	 * @return array An array of globals
	 */
	public function getGlobals()
	{
		if ( ! $this->runtime_initialized && ! $this->extension_initialized)
		{
			return $this->init_globals();
		}

		if ($this->globals === NULL)
		{
			$this->globals = $this->init_globals();
		}

		return $this->globals;
	}

	/**
	 * Merges a context with the defined globals.
	 *
	 * @param array $context An array representing the context
	 *
	 * @return array The context merged with the globals
	 */
	public function mergeGlobals(array $context)
	{
		// we don't use array_merge as the context being generally
		// bigger than globals, this code is faster.
		foreach ($this->getGlobals() AS $key => $value)
		{
			if ( ! array_key_exists($key, $context))
			{
				$context[$key] = $value;
			}
		}

		return $context;
	}

	/**
	 * Gets the registered unary Operators.
	 *
	 * @return array An array of unary operators
	 */
	public function get_unary_operators()
	{
		if ( ! $this->extension_initialized)
		{
			$this->init_extensions();
		}

		return $this->unaryOperators;
	}

	/**
	 * Gets the registered binary Operators.
	 *
	 * @return array An array of binary operators
	 */
	public function getBinaryOperators()
	{
		if ( ! $this->extension_initialized)
		{
			$this->init_extensions();
		}
		return $this->binary_operators;
	}

	public function computeAlternatives($name, $items)
	{
		$alternatives = array();
		foreach ($items AS $item)
		{
			$lev = levenshtein($name, $item);
			if ($lev <= strlen($name) / 3 || FALSE !== strpos($item, $name))
			{
				$alternatives[$item] = $lev;
			}
		}
		asort($alternatives);

		return array_keys($alternatives);
	}

	protected function init_globals()
	{
		$globals = array();
		foreach ($this->extensions AS $extension)
		{
			$extGlob = $extension->getGlobals();
			if ( ! is_array($extGlob))
			{
				throw new UnexpectedValueException(sprintf('"%s::getGlobals()" must return an array of globals.', get_class($extension)));
			}

			$globals[] = $extGlob;
		}

		$globals[] = $this->staging->getGlobals();

		return call_user_func_array('array_merge', $globals);
	}

	protected function init_extensions()
	{
		if ($this->extension_initialized)
		{
			return;
		}

		$this->extension_initialized = TRUE;
		$this->parsers = new Twig_Token_Parser_Broker();
		$this->filters = array();
		$this->functions = array();
		$this->tests = array();
		$this->visitors = array();
		$this->unaryOperators = array();
		$this->binary_operators = array();

		foreach ($this->extensions AS $extension)
		{
			$this->init_extension($extension);
		}
		$this->init_extension($this->staging);
	}

	protected function init_extension(Twig_Extension $extension)
	{
		// filters
		foreach ($extension->get_filters() AS $name => $filter)
		{
			if ($name instanceof Twig_Simple_Filter)
			{
				$filter = $name;
				$name = $filter->getName();
			}
			elseif ($filter instanceof Twig_Simple_Filter)
			{
				$name = $filter->getName();
			}

			$this->filters[$name] = $filter;
		}

		// functions
		foreach ($extension->get_functions() AS $name => $function)
		{
			if ($name instanceof Twig_Simple_Function)
			{
				$function = $name;
				$name = $function->getName();
			}
			elseif ($function instanceof Twig_Simple_Function)
			{
				$name = $function->getName();
			}

			$this->functions[$name] = $function;
		}

		// tests
		foreach ($extension->getTests() AS $name => $test)
		{
			if ($name instanceof Twig_Simple_Test)
			{
				$test = $name;
				$name = $test->getName();
			}
			elseif ($test instanceof Twig_Simple_Test)
			{
				$name = $test->getName();
			}

			$this->tests[$name] = $test;
		}

		// token parsers
		foreach ($extension->get_token_parsers() AS $parser)
		{
			if ($parser instanceof Twig_Token_Parser)
			{
				$this->parsers->addTokenParser($parser);
			}
			elseif ($parser instanceof Twig_Token_Parser_Broker)
			{
				$this->parsers->addTokenParserBroker($parser);
			}
			else
			{
				throw new LogicException('get_token_parsers() must return an array of Twig_Token_Parser or Twig_Token_Parser_Broker instances');
			}
		}

		// node visitors
		foreach ($extension->get_node_visitors() AS $visitor)
		{
			$this->visitors[] = $visitor;
		}

		// 操作符
		if ($operators = $extension->get_operators())
		{
			if (count($operators) !== 2)
			{
				throw new InvalidArgumentException(sprintf('"%s::get_operators()" does not return a valid operators array.', get_class($extension)));
			}

			$this->unaryOperators = array_merge($this->unaryOperators, $operators[0]);
			$this->binary_operators = array_merge($this->binary_operators, $operators[1]);
		}
	}

	protected function writeCacheFile($file, $content)
	{
		$dir = dirname($file);
		if ( ! is_dir($dir))
		{
			if ((@mkdir($dir, 0777, TRUE) === FALSE) AND ( ! is_dir($dir)))
			{
				throw new RuntimeException(sprintf("Unable to create the cache directory (%s).", $dir));
			}
		}
		elseif ( ! is_writable($dir))
		{
			throw new RuntimeException(sprintf("Unable to write in the cache directory (%s).", $dir));
		}

		$tmpFile = tempnam(dirname($file), basename($file));
		if (@file_put_contents($tmpFile, $content) !== FALSE)
		{
			// rename does not work on Win32 before 5.2.6
			if (@rename($tmpFile, $file) || (@copy($tmpFile, $file) && unlink($tmpFile)))
			{
				@chmod($file, 0666 & ~umask());

				return;
			}
		}

		throw new RuntimeException(sprintf('Failed to write cache file "%s".', $file));
	}
}
