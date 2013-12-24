<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Defines a macro.
 *
 * <pre>
 * {% macro input(name, value, type, size) %}
 *    <input type="{{ type|default('text') }}" name="{{ name }}" value="{{ value|e }}" size="{{ size|default(20) }}" />
 * {% endmacro %}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Parser_Macro extends Twig_Token_Parser {

	/**
	 * Parses a token and returns a node.
	 *
	 * @param Twig_Token $token A Twig_Token instance
	 *
	 * @return Twig_Node A Twig_Node instance
	 */
	public function parse(Twig_Token $token)
	{
		$lineno = $token->getLine();
		$stream = $this->parser->getStream();
		$name = $stream->expect(Twig_Token::NAME_TYPE)->getValue();

		$arguments = $this
			->parser
			->get_expression_parser()
			->parseArguments(TRUE, TRUE);

		$stream->expect(Twig_Token::BLOCK_END_TYPE);
		$this->parser->pushLocalScope();
		$body = $this->parser->subparse(array($this, 'decide_block_end'), TRUE);
		if ($stream->test(Twig_Token::NAME_TYPE))
		{
			$value = $stream->next()->getValue();

			if ($value != $name)
			{
				throw new Twig_Exception_Syntax(sprintf("Expected endmacro for macro '$name' (but %s given)", $value), $stream->getCurrent()->getLine(), $stream->getFilename());
			}
		}
		$this->parser->popLocalScope();
		$stream->expect(Twig_Token::BLOCK_END_TYPE);

		$this->parser->setMacro($name, new Twig_Node_Macro($name, new Twig_Node_Body(array($body)), $arguments, $lineno, $this->getTag()));
	}

	public function decide_block_end(Twig_Token $token)
	{
		return $token->test('endmacro');
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'macro';
	}
}
