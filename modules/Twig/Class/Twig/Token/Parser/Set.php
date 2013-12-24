<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Defines a variable.
 *
 * <pre>
 *  {% set foo = 'foo' %}
 *  {% set foo = [1, 2] %}
 *  {% set foo = {'foo': 'bar'} %}
 *  {% set foo = 'foo' ~ 'bar' %}
 *  {% set foo, bar = 'foo', 'bar' %}
 *  {% set foo %}Some content{% endset %}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Parser_Set extends Twig_Token_Parser {

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
		$stream = $this
			->parser
			->getStream();
		$names = $this
			->parser
			->get_expression_parser()
			->parse_assignment_expression();

		$capture = FALSE;
		if ($stream->test(Twig_Token::OPERATOR_TYPE, '='))
		{
			$stream->next();
			$values = $this->parser->get_expression_parser()->parse_multitarget_expression();

			$stream->expect(Twig_Token::BLOCK_END_TYPE);

			if (count($names) !== count($values))
			{
				throw new Twig_Exception_Syntax("When using set, you must have the same number of variables and assignments.", $stream->getCurrent()->getLine(), $stream->getFilename());
			}
		}
		else
		{
			$capture = TRUE;

			if (count($names) > 1)
			{
				throw new Twig_Exception_Syntax("When using set with a block, you cannot have a multi-target.", $stream->getCurrent()->getLine(), $stream->getFilename());
			}

			$stream->expect(Twig_Token::BLOCK_END_TYPE);

			$values = $this->parser->subparse(array($this, 'decide_block_end'), TRUE);
			$stream->expect(Twig_Token::BLOCK_END_TYPE);
		}

		return new Twig_Node_Set($capture, $names, $values, $lineno, $this->getTag());
	}

	public function decide_block_end(Twig_Token $token)
	{
		return $token->test('endset');
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'set';
	}
}
