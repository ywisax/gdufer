<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Base class for all token parsers.
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
abstract class Twig_Token_Parser {

	/**
	 * @var Twig_Parser
	 */
	protected $parser;

	/**
	 * Sets the parser associated with this token parser
	 *
	 * @param $parser A Twig_Parser instance
	 */
	public function setParser(Twig_Parser $parser)
	{
		$this->parser = $parser;
	}
	
	/**
	 * Parses a token and returns a node.
	 *
	 * @param Twig_Token $token A Twig_Token instance
	 *
	 * @return Twig_Node A Twig_Node instance
	 */
	abstract public function parse(Twig_Token $token);

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	abstract public function getTag();
}
