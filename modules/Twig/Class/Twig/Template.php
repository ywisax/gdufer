<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Default base class for compiled templates.
 *
 * @package    Kohana/Twig
 * @category   Template
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
abstract class Twig_Template {

	protected static $cache = array();

	protected $parent;
	protected $parents;
	protected $env;
	protected $blocks;
	protected $traits;
	protected $macros;
	
	const ANY_CALL    = 'any';
	const ARRAY_CALL  = 'array';
	const METHOD_CALL = 'method';

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $env A Twig_Environment instance
	 */
	public function __construct(Twig_Environment $env)
	{
		$this->env = $env;
		$this->blocks = array();
		$this->traits = array();
		$this->macros = array();
	}

	/**
	 * Returns the template name.
	 *
	 * @return string The template name
	 */
	abstract public function get_template_name();

	/**
	 * {@inheritdoc}
	 */
	public function get_environment()
	{
		return $this->env;
	}

	/**
	 * Returns the parent template.
	 *
	 * @return Twig_Template|false The parent template or false if there is no parent
	 */
	public function get_parent(array $context)
	{
		if ($this->parent !== NULL)
		{
			return $this->parent;
		}

		$parent = $this->do_get_parent($context);
		if ($parent === FALSE)
		{
			return FALSE;
		}
		elseif ($parent instanceof Twig_Template)
		{
			$name = $parent->get_template_name();
			$this->parents[$name] = $parent;
			$parent = $name;
		}
		elseif ( ! isset($this->parents[$parent]))
		{
			$this->parents[$parent] = $this->env->load_template($parent);
		}

		return $this->parents[$parent];
	}

	protected function do_get_parent(array $context)
	{
		return FALSE;
	}

	public function is_traitable()
	{
		return TRUE;
	}

	/**
	 * Displays a parent block.
	 *
	 * This method is for internal use only and should never be called
	 * directly.
	 *
	 * @param string $name    The block name to display from the parent
	 * @param array  $context The context
	 * @param array  $blocks  The current set of blocks
	 */
	public function display_parent_block($name, array $context, array $blocks = array())
	{
		$name = (string) $name;

		if (isset($this->traits[$name]))
		{
			$this->traits[$name][0]->display_block($name, $context, $blocks);
		}
		elseif (false !== $parent = $this->get_parent($context))
		{
			$parent->display_block($name, $context, $blocks);
		}
		else
		{
			throw new Twig_Exception_Runtime(sprintf('The template has no parent and no traits defining the "%s" block', $name), -1, $this->get_template_name());
		}
	}

	/**
	 * Displays a block.
	 *
	 * This method is for internal use only and should never be called
	 * directly.
	 *
	 * @param string $name    The block name to display
	 * @param array  $context The context
	 * @param array  $blocks  The current set of blocks
	 */
	public function display_block($name, array $context, array $blocks = array())
	{
		$name = (string) $name;

		if (isset($blocks[$name]))
		{
			$b = $blocks;
			unset($b[$name]);
			call_user_func($blocks[$name], $context, $b);
		}
		elseif (isset($this->blocks[$name]))
		{
			call_user_func($this->blocks[$name], $context, $blocks);
		}
		elseif (($parent = $this->get_parent($context)) !== FALSE)
		{
			$parent->display_block($name, $context, array_merge($this->blocks, $blocks));
		}
	}

	/**
	 * Renders a parent block.
	 *
	 * This method is for internal use only and should never be called
	 * directly.
	 *
	 * @param string $name    The block name to render from the parent
	 * @param array  $context The context
	 * @param array  $blocks  The current set of blocks
	 *
	 * @return string The rendered block
	 */
	public function render_parent_block($name, array $context, array $blocks = array())
	{
		ob_start();
		$this->display_parent_block($name, $context, $blocks);

		return ob_get_clean();
	}

	/**
	 * 渲染和呈现一个块，正常来说，不要手动使用这个方法
	 *
	 * @param string $name    The block name to render
	 * @param array  $context The context
	 * @param array  $blocks  The current set of blocks
	 *
	 * @return string The rendered block
	 */
	public function render_block($name, array $context, array $blocks = array())
	{
		ob_start();
		$this->display_block($name, $context, $blocks);
		return ob_get_clean();
	}

	/**
	 * Returns whether a block exists or not.
	 *
	 * This method is for internal use only and should never be called
	 * directly.
	 *
	 * This method does only return blocks defined in the current template
	 * or defined in "used" traits.
	 *
	 * It does not return blocks from parent templates as the parent
	 * template name can be dynamic, which is only known based on the
	 * current context.
	 *
	 * @param string $name The block name
	 *
	 * @return Boolean true if the block exists, false otherwise
	 */
	public function has_block($name)
	{
		return isset($this->blocks[(string) $name]);
	}

	/**
	 * Returns all block names.
	 *
	 * This method is for internal use only and should never be called
	 * directly.
	 *
	 * @return array An array of block names
	 */
	public function get_block_names()
	{
		return array_keys($this->blocks);
	}

	/**
	 * 返回所有块信息
	 *
	 * @return array An array of blocks
	 */
	public function get_blocks()
	{
		return $this->blocks;
	}

	/**
	 * {@inheritdoc}
	 */
	public function display(array $context, array $blocks = array())
	{
		$this->display_with_error_handling($this->env->mergeGlobals($context), $blocks);
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(array $context)
	{
		$level = ob_get_level();
		ob_start();
		try
		{
			$this->display($context);
		}
		catch (Exception $e)
		{
			while (ob_get_level() > $level)
			{
				ob_end_clean();
			}

			throw $e;
		}
		return ob_get_clean();
	}

	protected function display_with_error_handling(array $context, array $blocks = array())
	{
		try
		{
			$this->do_display($context, $blocks);
		}
		catch (Twig_Exception $e)
		{
			if ( ! $e->get_template_file())
			{
				$e->setTemplateFile($this->get_template_name());
			}

			// this is mostly useful for Twig_Exception_Loader exceptions
			// see Twig_Exception_Loader
			if ($e->getTemplateLine() === FALSE)
			{
				$e->setTemplateLine(-1);
				$e->guess();
			}

			throw $e;
		}
		catch (Exception $e)
		{
			//throw new Twig_Exception_Runtime(sprintf('An exception has been thrown during the rendering of a template ("%s").', $e->getMessage()), -1, null, $e);
			throw $e;
		}
	}

	/**
	 * Auto-generated method to display the template with the given context.
	 *
	 * @param array $context An array of parameters to pass to the template
	 * @param array $blocks  An array of blocks to pass to the template
	 */
	abstract protected function do_display(array $context, array $blocks = array());

	/**
	 * Returns a variable from the context.
	 *
	 * This method is for internal use only and should never be called
	 * directly.
	 *
	 * This method should not be overridden in a sub-class as this is an
	 * implementation detail that has been introduced to optimize variable
	 * access for versions of PHP before 5.4. This is not a way to override
	 * the way to get a variable value.
	 *
	 * @param array   $context           The context
	 * @param string  $item              The variable to return from the context
	 * @param Boolean $ignoreStrictCheck Whether to ignore the strict variable check or not
	 * @return The content of the context variable
	 * @throws Twig_Exception_Runtime if the variable does not exist and Twig is running in strict mode
	 */
    final protected function get_context($context, $item, $ignoreStrictCheck = FALSE)
	{
		if ( ! array_key_exists($item, $context))
		{
			if ($ignoreStrictCheck || !$this->env->isStrictVariables())
			{
				return NULL;
			}

			throw new Twig_Exception_Runtime(sprintf('Variable "%s" does not exist', $item), -1, $this->get_template_name());
		}

		return $context[$item];
	}

	/**
	 * Returns the attribute value for a given array/object.
	 *
	 * @param mixed   $object            The object or array from where to get the item
	 * @param mixed   $item              The item to get from the array or object
	 * @param array   $arguments         An array of arguments to pass if the item is an object method
	 * @param string  $type              The type of attribute (@see Twig_Template constants)
	 * @param Boolean $isDefinedTest     Whether this is only a defined check
	 * @param Boolean $ignoreStrictCheck Whether to ignore the strict attribute check or not
	 * @return mixed The attribute value, or a Boolean when $isDefinedTest is true, or null when the attribute is not set and $ignoreStrictCheck is true
	 * @throws Twig_Exception_Runtime if the attribute does not exist and Twig is running in strict mode and $isDefinedTest is false
	 */
	protected function get_attribute($object, $item, array $arguments = array(), $type = Twig_Template::ANY_CALL, $isDefinedTest = FALSE, $ignoreStrictCheck = FALSE)
	{
		// array
		if (Twig_Template::METHOD_CALL !== $type)
		{
			$arrayItem = is_bool($item) || is_float($item) ? (int) $item : $item;

			if ((is_array($object) && array_key_exists($arrayItem, $object))
				|| ($object instanceof ArrayAccess && isset($object[$arrayItem]))
			)
			{
				if ($isDefinedTest)
				{
					return TRUE;
				}
				return $object[$arrayItem];
			}

			if (Twig_Template::ARRAY_CALL === $type || !is_object($object))
			{
				if ($isDefinedTest)
				{
					return FALSE;
				}

				if ($ignoreStrictCheck || !$this->env->isStrictVariables())
				{
					return NULL;
				}

				if (is_object($object))
				{
					throw new Twig_Exception_Runtime(sprintf('Key "%s" in object (with ArrayAccess) of type "%s" does not exist', $arrayItem, get_class($object)), -1, $this->get_template_name());
				}
				elseif (is_array($object))
				{
					throw new Twig_Exception_Runtime(sprintf('Key "%s" for array with keys "%s" does not exist', $arrayItem, implode(', ', array_keys($object))), -1, $this->get_template_name());
				}
				elseif (Twig_Template::ARRAY_CALL === $type)
				{
					throw new Twig_Exception_Runtime(sprintf('Impossible to access a key ("%s") on a %s variable ("%s")', $item, gettype($object), $object), -1, $this->get_template_name());
				}
				else
				{
					throw new Twig_Exception_Runtime(sprintf('Impossible to access an attribute ("%s") on a %s variable ("%s")', $item, gettype($object), $object), -1, $this->get_template_name());
				}
			}
		}

		if ( ! is_object($object))
		{
			if ($isDefinedTest)
			{
				return FALSE;
			}

			if ($ignoreStrictCheck || !$this->env->isStrictVariables())
			{
				return NULL;
			}

			throw new Twig_Exception_Runtime(sprintf('Impossible to invoke a method ("%s") on a %s variable ("%s")', $item, gettype($object), $object), -1, $this->get_template_name());
		}

		$class = get_class($object);

		// object property
		if (Twig_Template::METHOD_CALL !== $type)
		{
			if (isset($object->$item) || array_key_exists((string) $item, $object))
			{
				if ($isDefinedTest)
				{
					return TRUE;
				}

				if ($this->env->hasExtension('sandbox'))
				{
					$this->env->getExtension('sandbox')->checkPropertyAllowed($object, $item);
				}

				return $object->$item;
			}
		}

		// object method
		if ( ! isset(Twig_Template::$cache[$class]['methods']))
		{
			self::$cache[$class]['methods'] = array_change_key_case(array_flip(get_class_methods($object)));
		}

		$lcItem = strtolower($item);
		if (isset(self::$cache[$class]['methods'][$lcItem]))
		{
			$method = (string) $item;
		}
		elseif (isset(self::$cache[$class]['methods']['get'.$lcItem]))
		{
			$method = 'get'.$item;
		}
		elseif (isset(self::$cache[$class]['methods']['is'.$lcItem]))
		{
			$method = 'is'.$item;
		}
		elseif (isset(self::$cache[$class]['methods']['__call']))
		{
			$method = (string) $item;
		}
		else
		{
			if ($isDefinedTest)
			{
				return FALSE;
			}

			if ($ignoreStrictCheck || !$this->env->isStrictVariables())
			{
				return NULL;
			}

			throw new Twig_Exception_Runtime(sprintf('Method "%s" for object "%s" does not exist', $item, get_class($object)), -1, $this->get_template_name());
		}

		if ($isDefinedTest)
		{
			return TRUE;
		}

		if ($this->env->hasExtension('sandbox'))
		{
			$this->env->getExtension('sandbox')->checkMethodAllowed($object, $method);
		}

		$ret = call_user_func_array(array($object, $method), $arguments);

		// useful when calling a template method from a template
		// this is not supported but unfortunately heavily used in the Symfony profiler
		if ($object instanceof Twig_Template)
		{
			return $ret === '' ? '' : new Twig_Markup($ret, $this->env->getCharset());
		}

		return $ret;
	}

	/**
	 * Calls macro in a template.
	 *
	 * @param Twig_Template $template        The template
	 * @param string        $macro           The name of macro
	 * @param array         $arguments       The arguments of macro
	 * @param array         $namedNames      An array of names of arguments as keys
	 * @param integer       $namedCount      The count of named arguments
	 * @param integer       $positionalCount The count of positional arguments
	 *
	 * @return string The content of a macro
	 *
	 * @throws Twig_Exception_Runtime if the macro is not defined
	 * @throws Twig_Exception_Runtime if the argument is defined twice
	 * @throws Twig_Exception_Runtime if the argument is unknown
	 */
	protected function call_macro(Twig_Template $template, $macro, array $arguments, array $namedNames = array(), $namedCount = 0, $positionalCount = -1)
	{
		if ( ! isset($template->macros[$macro]['reflection']))
		{
			if ( ! isset($template->macros[$macro]))
			{
				throw new Twig_Exception_Runtime(sprintf('Macro "%s" is not defined in the template "%s".', $macro, $template->get_template_name()));
			}

			$template->macros[$macro]['reflection'] = new ReflectionMethod($template, $template->macros[$macro]['method']);
		}

		if ($namedCount < 1)
		{
			return $template->macros[$macro]['reflection']->invokeArgs($template, $arguments);
		}

		$i = 0;
		$args = array();
		foreach ($template->macros[$macro]['arguments'] AS $name => $value)
		{
			if (isset($namedNames[$name]))
			{
				if ($i < $positionalCount)
				{
					throw new Twig_Exception_Runtime(sprintf('Argument "%s" is defined twice for macro "%s" defined in the template "%s".', $name, $macro, $template->get_template_name()));
				}

				$args[] = $arguments[$name];
				if (--$namedCount < 1)
				{
					break;
				}
			}
			elseif ($i < $positionalCount)
			{
				$args[] = $arguments[$i];
			}
			else
			{
				$args[] = $value;
			}

			$i++;
		}

		if ($namedCount > 0)
		{
			$parameters = array_keys(array_diff_key($namedNames, $template->macros[$macro]['arguments']));
			throw new Twig_Exception_Runtime(sprintf('Unknown argument%s "%s" for macro "%s" defined in the template "%s".', count($parameters) > 1 ? 's' : '' , implode('", "', $parameters), $macro, $template->get_template_name()));
		}

		return $template->macros[$macro]['reflection']->invokeArgs($template, $args);
	}

	/**
	 * This method is only useful when testing Twig. Do not use it.
	 */
    public static function clear_cache()
	{
		self::$cache = array();
	}
}
