<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Default implementation of a token parser broker.
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Parser_Broker {

	protected $parser;
	protected $parsers = array();
	protected $brokers = array();

	/**
	 * Constructor.
	 *
	 * @param array|Traversable $parsers A Traversable of Twig_Token_Parser instances
	 * @param array|Traversable $brokers A Traversable of Twig_Token_Parser_Broker instances
	 */
	public function __construct($parsers = array(), $brokers = array())
	{
		foreach ($parsers AS $parser)
		{
			if ( ! $parser instanceof Twig_Token_Parser)
			{
				throw new LogicException('$parsers must a an array of Twig_Token_Parser');
			}
			$this->parsers[$parser->getTag()] = $parser;
		}
		foreach ($brokers AS $broker)
		{
			if ( ! $broker instanceof Twig_Token_Parser_Broker)
			{
				throw new LogicException('$brokers must a an array of Twig_Token_Parser_Broker');
			}
			$this->brokers[] = $broker;
		}
	}

	/**
	 * Adds a TokenParser.
	 *
	 * @param Twig_Token_Parser $parser A Twig_Token_Parser instance
	 */
	public function addTokenParser(Twig_Token_Parser $parser)
	{
		$this->parsers[$parser->getTag()] = $parser;
	}

	/**
	 * Removes a TokenParser.
	 *
	 * @param Twig_Token_Parser $parser A Twig_Token_Parser instance
	 */
	public function removeTokenParser(Twig_Token_Parser $parser)
	{
		$name = $parser->getTag();
		if (isset($this->parsers[$name]) && $parser === $this->parsers[$name])
		{
			unset($this->parsers[$name]);
		}
	}

	/**
	 * Adds a TokenParserBroker.
	 *
	 * @param Twig_Token_Parser_Broker $broker A Twig_Token_Parser_Broker instance
	 */
	public function addTokenParserBroker(Twig_Token_Parser_Broker $broker)
	{
		$this->brokers[] = $broker;
	}

	/**
	 * Removes a TokenParserBroker.
	 *
	 * @param Twig_Token_Parser_Broker $broker A Twig_Token_Parser_Broker instance
	 */
	public function removeTokenParserBroker(Twig_Token_Parser_Broker $broker)
	{
		if (($pos = array_search($broker, $this->brokers)) !== FALSE)
		{
			unset($this->brokers[$pos]);
		}
	}

	/**
	 * Gets a suitable TokenParser for a tag.
	 *
	 * First looks in parsers, then in brokers.
	 *
	 * @param string $tag A tag name
	 *
	 * @return null|Twig_Token_Parser A Twig_Token_Parser or null if no suitable TokenParser was found
	 */
	public function getTokenParser($tag)
	{
		if (isset($this->parsers[$tag]))
		{
			return $this->parsers[$tag];
		}
		$broker = end($this->brokers);
		while ($broker !== FALSE)
		{
			$parser = $broker->getTokenParser($tag);
			if ($parser !== NULL)
			{
				return $parser;
			}
			$broker = prev($this->brokers);
		}
	}

	public function getParsers()
	{
		return $this->parsers;
	}

	public function getParser()
	{
		return $this->parser;
	}

	public function setParser(Twig_Parser $parser)
	{
		$this->parser = $parser;
		foreach ($this->parsers AS $tokenParser)
		{
			$tokenParser->setParser($parser);
		}
		foreach ($this->brokers AS $broker)
		{
			$broker->setParser($parser);
		}
	}
}
