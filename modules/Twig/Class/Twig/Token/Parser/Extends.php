<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Extends a template by another one.
 *
 * <pre>
 *  {% extends "base.html" %}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Parser_Extends extends Twig_Token_Parser {

	/**
	 * Parses a token and returns a node.
	 *
	 * @param Twig_Token $token A Twig_Token instance
	 *
	 * @return Twig_Node A Twig_Node instance
	 */
	public function parse(Twig_Token $token)
	{
		if ( ! $this->parser->is_main_scope())
		{
			throw new Twig_Exception_Syntax('Cannot extend from a block', $token->getLine(), $this->parser->getFilename());
		}

		if ($this->parser->get_parent() !== NULL)
		{
			throw new Twig_Exception_Syntax('Multiple extends tags are forbidden', $token->getLine(), $this->parser->getFilename());
		}
		$this->parser->setParent($this->parser->get_expression_parser()->parse_expression());

		$this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'extends';
	}
}
