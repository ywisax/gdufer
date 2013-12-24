<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Loops over each item of a sequence.
 *
 * <pre>
 * <ul>
 *  {% for user in users %}
 *    <li>{{ user.username|e }}</li>
 *  {% endfor %}
 * </ul>
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Parser_For extends Twig_Token_Parser {

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
		$targets = $this->parser->get_expression_parser()->parse_assignment_expression();
		$stream->expect(Twig_Token::OPERATOR_TYPE, 'in');
		$seq = $this->parser->get_expression_parser()->parse_expression();

		$ifexpr = NULL;
		if ($stream->test(Twig_Token::NAME_TYPE, 'if'))
		{
			$stream->next();
			$ifexpr = $this->parser->get_expression_parser()->parse_expression();
		}

		$stream->expect(Twig_Token::BLOCK_END_TYPE);
		$body = $this->parser->subparse(array($this, 'decideForFork'));
		if ($stream->next()->getValue() == 'else')
		{
			$stream->expect(Twig_Token::BLOCK_END_TYPE);
			$else = $this->parser->subparse(array($this, 'decideForEnd'), TRUE);
		}
		else
		{
			$else = NULL;
		}
		$stream->expect(Twig_Token::BLOCK_END_TYPE);

		if (count($targets) > 1)
		{
			$keyTarget = $targets->getNode(0);
			$keyTarget = new Twig_Node_Expression_AssignName($keyTarget->get_attribute('name'), $keyTarget->getLine());
			$valueTarget = $targets->getNode(1);
			$valueTarget = new Twig_Node_Expression_AssignName($valueTarget->get_attribute('name'), $valueTarget->getLine());
		}
		else
		{
			$keyTarget = new Twig_Node_Expression_AssignName('_key', $lineno);
			$valueTarget = $targets->getNode(0);
			$valueTarget = new Twig_Node_Expression_AssignName($valueTarget->get_attribute('name'), $valueTarget->getLine());
		}

		if ($ifexpr)
		{
			$this->checkLoopUsageCondition($stream, $ifexpr);
			$this->checkLoopUsageBody($stream, $body);
		}

		return new Twig_Node_For($keyTarget, $valueTarget, $seq, $ifexpr, $body, $else, $lineno, $this->getTag());
	}

	public function decideForFork(Twig_Token $token)
	{
		return $token->test(array('else', 'endfor'));
	}

	public function decideForEnd(Twig_Token $token)
	{
		return $token->test('endfor');
	}

    // the loop variable cannot be used in the condition
	protected function checkLoopUsageCondition(Twig_Token_Stream $stream, Twig_Node $node)
	{
		if ($node instanceof Twig_Node_Expression_GetAttr && $node->getNode('node') instanceof Twig_Node_Expression_Name && 'loop' == $node->getNode('node')->get_attribute('name'))
		{
			throw new Twig_Exception_Syntax('The "loop" variable cannot be used in a looping condition', $node->getLine(), $stream->getFilename());
		}

		foreach ($node AS $n)
		{
			if ( ! $n)
			{
				continue;
			}
			$this->checkLoopUsageCondition($stream, $n);
		}
	}

    // check usage of non-defined loop-items
    // it does not catch all problems (for instance when a for is included into another or when the variable is used in an include)
	protected function checkLoopUsageBody(Twig_Token_Stream $stream, Twig_Node $node)
	{
		if ($node instanceof Twig_Node_Expression_GetAttr && $node->getNode('node') instanceof Twig_Node_Expression_Name && 'loop' == $node->getNode('node')->get_attribute('name'))
		{
			$attribute = $node->getNode('attribute');
			if ($attribute instanceof Twig_Node_Expression_Constant && in_array($attribute->get_attribute('value'), array('length', 'revindex0', 'revindex', 'last')))
			{
				throw new Twig_Exception_Syntax(sprintf('The "loop.%s" variable is not defined when looping with a condition', $attribute->get_attribute('value')), $node->getLine(), $stream->getFilename());
			}
		}

		// should check for parent.loop.XXX usage
		if ($node instanceof Twig_Node_For)
		{
			return;
		}

		foreach ($node AS $n)
		{
			if ( ! $n)
			{
				continue;
			}

			$this->checkLoopUsageBody($stream, $n);
		}
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'for';
	}
}
