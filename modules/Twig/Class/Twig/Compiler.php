<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Compiles a node to PHP code.
 *
 * @package    Kohana/Twig
 * @category   Compiler
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Compiler {

	protected $lastLine;
	protected $source;
	protected $indentation;
	protected $env;
	protected $debug_info;
	protected $source_offset;
	protected $sourceLine;
	protected $filename;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $env The twig environment instance
	 */
	public function __construct(Twig_Environment $env)
	{
		$this->env = $env;
		$this->debug_info = array();
	}

	public function getFilename()
	{
		return $this->filename;
	}

	/**
	 * Returns the environment instance related to this compiler.
	 *
	 * @return Twig_Environment The environment instance
	 */
	public function get_environment()
	{
		return $this->env;
	}

	/**
	 * Gets the current PHP code after compilation.
	 *
	 * @return string The PHP code
	 */
	public function get_source()
	{
		return $this->source;
	}

	/**
	 * Compiles a node.
	 *
	 * @param Twig_Node $node        The node to compile
	 * @param integer            $indentation The current indentation
	 *
	 * @return Twig_Compiler The current compiler instance
	 */
	public function compile(Twig_Node $node, $indentation = 0)
	{
		$this->lastLine = NULL;
		$this->source = '';
		$this->source_offset = 0;
		// source code starts at 1 (as we then increment it when we encounter new lines)
		$this->sourceLine = 1;
		$this->indentation = $indentation;

		if ($node instanceof Twig_Node_Module)
		{
			$this->filename = $node->get_attribute('filename');
		}

		$node->compile($this);

		return $this;
	}

	public function subcompile(Twig_Node $node, $raw = TRUE)
	{
		if ($raw === FALSE)
		{
			$this->add_indentation();
		}
		$node->compile($this);

		return $this;
	}

	/**
	 * Adds a raw string to the compiled code.
	 *
	 * @param string $string The string
	 *
	 * @return Twig_Compiler The current compiler instance
	 */
	public function raw($string)
	{
		$this->source .= $string;

		return $this;
	}

	/**
	 * Writes a string to the compiled code by adding indentation.
	 *
	 * @return Twig_Compiler The current compiler instance
	 */
	public function write()
	{
		$strings = func_get_args();
		foreach ($strings AS $string)
		{
			$this->add_indentation();
			$this->source .= $string;
		}

		return $this;
	}

	/**
	 * Appends an indentation to the current PHP code after compilation.
	 *
	 * @return Twig_Compiler The current compiler instance
	 */
	public function add_indentation()
	{
		$this->source .= str_repeat(' ', $this->indentation * 4);
		return $this;
	}

	/**
	 * Adds a quoted string to the compiled code.
	 *
	 * @param string $value The string
	 *
	 * @return Twig_Compiler The current compiler instance
	 */
	public function string($value)
	{
		$this->source .= sprintf('"%s"', addcslashes($value, "\0\t\"\$\\"));
		return $this;
	}

	/**
	 * Returns a PHP representation of a given value.
	 *
	 * @param mixed $value The value to convert
	 *
	 * @return Twig_Compiler The current compiler instance
	 */
	public function repr($value)
	{
		if (is_int($value) || is_float($value))
		{
			if (($locale = setlocale(LC_NUMERIC, 0)) !== FALSE)
			{
				setlocale(LC_NUMERIC, 'C');
			}

			$this->raw($value);

			if ($locale !== FALSE)
			{
				setlocale(LC_NUMERIC, $locale);
			}
		}
		elseif ($value === NULL)
		{
			$this->raw('null');
		}
		elseif (is_bool($value))
		{
			$this->raw($value ? 'true' : 'false');
		}
		elseif (is_array($value))
		{
			$this->raw('array(');
			$i = 0;
			foreach ($value AS $key => $value)
			{
				if ($i++)
				{
					$this->raw(', ');
				}
				$this->repr($key);
				$this->raw(' => ');
				$this->repr($value);
			}
			$this->raw(')');
		}
		else
		{
			$this->string($value);
		}

		return $this;
	}
	
	/**
	 * debugging information.
	 *
	 * @param Twig_Node $node The related twig node
	 *
	 * @return Twig_Compiler The current compiler instance
	 */
	public function debug_info(Twig_Node $node = NULL)
	{
		if ($node === NULL)
		{
			return $this->debug_info;
		}

		if ($node->getLine() != $this->lastLine)
		{
			$this->write("// line {$node->getLine()}\n");

			// when mbstring.func_overload is set to 2
			// mb_substr_count() replaces substr_count()
			// but they have different signatures!
			if (((int) ini_get('mbstring.func_overload')) & 2)
			{
				// this is much slower than the "right" version
				$this->sourceLine += mb_substr_count(mb_substr($this->source, $this->source_offset), "\n");
			}
			else
			{
				$this->sourceLine += substr_count($this->source, "\n", $this->source_offset);
			}
			$this->source_offset = strlen($this->source);
			$this->debug_info[$this->sourceLine] = $node->getLine();

			$this->lastLine = $node->getLine();
		}

		return $this;
	}

	/**
	 * Indents the generated code.
	 *
	 * @param integer $step The number of indentation to add
	 *
	 * @return Twig_Compiler The current compiler instance
	 */
	public function indent($step = 1)
	{
		$this->indentation += $step;
		return $this;
	}

	/**
	 * Outdents the generated code.
	 *
	 * @param integer $step The number of indentation to remove
	 *
	 * @return Twig_Compiler The current compiler instance
	 */
	public function outdent($step = 1)
	{
		// can't outdent by more steps than the current indentation level
		if ($this->indentation < $step)
		{
			throw new LogicException('Unable to call outdent() as the indentation would become negative');
		}
		$this->indentation -= $step;

		return $this;
	}
}
