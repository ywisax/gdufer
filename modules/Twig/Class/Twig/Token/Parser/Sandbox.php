<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Marks a section of a template as untrusted code that must be evaluated in the sandbox mode.
 *
 * <pre>
 * {% sandbox %}
 *     {% include 'user.html' %}
 * {% endsandbox %}
 * </pre>
 *
 * @see http://www.twig-project.org/doc/api.html#sandbox-extension for details
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Parser_Sandbox extends Twig_Token_Parser {

	/**
	 * Parses a token and returns a node.
	 *
	 * @param Twig_Token $token A Twig_Token instance
	 *
	 * @return Twig_Node A Twig_Node instance
	 */
	public function parse(Twig_Token $token)
	{
		$this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
		$body = $this->parser->subparse(array($this, 'decide_block_end'), TRUE);
		$this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);

		// in a sandbox tag, only include tags are allowed
		if ( ! $body instanceof Twig_Node_Include)
		{
			foreach ($body AS $node)
			{
				if ($node instanceof Twig_Node_Text && ctype_space($node->get_attribute('data')))
				{
					continue;
				}

				if ( ! $node instanceof Twig_Node_Include)
				{
					throw new Twig_Exception_Syntax('Only "include" tags are allowed within a "sandbox" section', $node->getLine(), $this->parser->getFilename());
				}
			}
		}

		return new Twig_Node_Sandbox($body, $token->getLine(), $this->getTag());
	}

	public function decide_block_end(Twig_Token $token)
	{
		return $token->test('endsandbox');
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'sandbox';
	}
}
