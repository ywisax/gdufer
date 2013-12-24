<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Filters a section of a template by applying filters.
 *
 * <pre>
 * {% filter upper %}
 *  This text becomes uppercase
 * {% endfilter %}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Parser_Filter extends Twig_Token_Parser {

	/**
	 * Parses a token and returns a node.
	 *
	 * @param Twig_Token $token A Twig_Token instance
	 *
	 * @return Twig_Node A Twig_Node instance
	 */
	public function parse(Twig_Token $token)
	{
		$name = $this->parser->getVarName();
		$ref = new Twig_Node_Expression_BlockReference(new Twig_Node_Expression_Constant($name, $token->getLine()), TRUE, $token->getLine(), $this->getTag());

		$filter = $this->parser->get_expression_parser()->parse_filter_expression_raw($ref, $this->getTag());
		$this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);

		$body = $this->parser->subparse(array($this, 'decide_block_end'), TRUE);
		$this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);

		$block = new Twig_Node_Block($name, $body, $token->getLine());
		$this->parser->setBlock($name, $block);

		return new Twig_Node_Print($filter, $token->getLine(), $this->getTag());
	}

	public function decide_block_end(Twig_Token $token)
	{
		return $token->test('endfilter');
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'filter';
	}
}
