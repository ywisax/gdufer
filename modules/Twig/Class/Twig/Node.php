<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a node in the AST.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node implements Countable, IteratorAggregate {

	protected $nodes;
	protected $attributes;
	protected $lineno;
	protected $tag;

	/**
	 * Constructor.
	 *
	 * The nodes are automatically made available as properties ($this->node).
	 * The attributes are automatically made available as array items ($this['name']).
	 *
	 * @param array   $nodes      An array of named nodes
	 * @param array   $attributes An array of attributes (should not be nodes)
	 * @param integer $lineno     The line number
	 * @param string  $tag        The tag name associated with the Node
	 */
	public function __construct(array $nodes = array(), array $attributes = array(), $lineno = 0, $tag = NULL)
	{
		$this->nodes = $nodes;
		$this->attributes = $attributes;
		$this->lineno = $lineno;
		$this->tag = $tag;
	}

	public function __toString()
	{
		$attributes = array();
		foreach ($this->attributes AS $name => $value)
		{
			$attributes[] = sprintf('%s: %s', $name, str_replace("\n", '', var_export($value, TRUE)));
		}

		$repr = array(get_class($this).'('.implode(', ', $attributes));

		if (count($this->nodes))
		{
			foreach ($this->nodes AS $name => $node)
			{
				$len = strlen($name) + 4;
				$noderepr = array();
				foreach (explode("\n", (string) $node) AS $line)
				{
					$noderepr[] = str_repeat(' ', $len).$line;
				}

				$repr[] = sprintf('  %s: %s', $name, ltrim(implode("\n", $noderepr)));
			}

			$repr[] = ')';
		}
		else
		{
			$repr[0] .= ')';
		}

		return implode("\n", $repr);
	}

	public function toXml($asDom = FALSE)
	{
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->formatOutput = TRUE;
		$dom->appendChild($xml = $dom->createElement('twig'));

		$xml->appendChild($node = $dom->createElement('node'));
		$node->set_attribute('class', get_class($this));

		foreach ($this->attributes AS $name => $value)
		{
			$node->appendChild($attribute = $dom->createElement('attribute'));
			$attribute->set_attribute('name', $name);
			$attribute->appendChild($dom->createTextNode($value));
		}

		foreach ($this->nodes AS $name => $n)
		{
			if ($n === NULL)
			{
				continue;
			}

			$child = $n
				->toXml(TRUE)
				->getElementsByTagName('node')
				->item(0);
			$child = $dom->importNode($child, true);
			$child->set_attribute('name', $name);

			$node->appendChild($child);
		}

		return $asDom ? $dom : $dom->saveXml();
	}

	public function compile(Twig_Compiler $compiler)
	{
		foreach ($this->nodes AS $node)
		{
			$node->compile($compiler);
		}
	}

	public function getLine()
	{
		return $this->lineno;
	}

	public function getNodeTag()
	{
		return $this->tag;
	}

	/**
	 * Returns true if the attribute is defined.
	 *
	 * @param  string  The attribute name
	 *
	 * @return Boolean true if the attribute is defined, false otherwise
	 */
	public function hasAttribute($name)
	{
		return array_key_exists($name, $this->attributes);
	}

	/**
	 * Gets an attribute.
	 *
	 * @param  string The attribute name
	 *
	 * @return mixed The attribute value
	 */
	public function get_attribute($name)
	{
		if ( ! array_key_exists($name, $this->attributes))
		{
			throw new LogicException(sprintf('Attribute "%s" does not exist for Node "%s".', $name, get_class($this)));
		}

		return $this->attributes[$name];
	}

	/**
	 * Sets an attribute.
	 *
	 * @param string The attribute name
	 * @param mixed  The attribute value
	 */
	public function set_attribute($name, $value)
	{
		$this->attributes[$name] = $value;
	}

	/**
	 * Removes an attribute.
	 *
	 * @param string The attribute name
	 */
	public function removeAttribute($name)
	{
		unset($this->attributes[$name]);
	}

	/**
	 * Returns true if the node with the given identifier exists.
	 *
	 * @param  string  The node name
	 *
	 * @return Boolean true if the node with the given name exists, false otherwise
	 */
	public function hasNode($name)
	{
		return array_key_exists($name, $this->nodes);
	}

	/**
	 * Gets a node by name.
	 *
	 * @param  string The node name
	 *
	 * @return Twig_Node A Twig_Node instance
	 */
	public function getNode($name)
	{
		if ( ! array_key_exists($name, $this->nodes))
		{
			throw new LogicException(sprintf('Node "%s" does not exist for Node "%s".', $name, get_class($this)));
		}

		return $this->nodes[$name];
	}

	/**
	 * Sets a node.
	 *
	 * @param string    The node name
	 * @param Twig_Node A Twig_Node instance
	 */
	public function setNode($name, $node = NULL)
	{
		$this->nodes[$name] = $node;
	}

	/**
	 * Removes a node by name.
	 *
	 * @param string The node name
	 */
	public function removeNode($name)
	{
		unset($this->nodes[$name]);
	}

	public function count()
	{
		return count($this->nodes);
	}

	public function getIterator()
	{
		return new ArrayIterator($this->nodes);
	}
}
