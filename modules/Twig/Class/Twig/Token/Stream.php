<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a token stream.
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Stream {

	protected $tokens;
	protected $current;
	protected $filename;

	/**
	 * Constructor.
	 *
	 * @param array  $tokens   An array of tokens
	 * @param string $filename The name of the filename which tokens are associated with
	 */
	public function __construct(array $tokens, $filename = NULL)
	{
		$this->tokens     = $tokens;
		$this->current    = 0;
		$this->filename   = $filename;
	}

	/**
	 * Returns a string representation of the token stream.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return implode("\n", $this->tokens);
	}

	public function inject_tokens(array $tokens)
	{
		$this->tokens = array_merge(array_slice($this->tokens, 0, $this->current), $tokens, array_slice($this->tokens, $this->current));
	}

	/**
	 * Sets the pointer to the next token and returns the old one.
	 *
	 * @return Twig_Token
	 */
	public function next()
	{
		if ( ! isset($this->tokens[++$this->current]))
		{
			throw new Twig_Exception_Syntax('Unexpected end of template', $this->tokens[$this->current - 1]->getLine(), $this->filename);
		}

		return $this->tokens[$this->current - 1];
	}

	/**
	 * Tests a token and returns it or throws a syntax error.
	 *
	 * @return Twig_Token
	 */
	public function expect($type, $value = NULL, $message = NULL)
	{
		$token = $this->tokens[$this->current];
		if ( ! $token->test($type, $value))
		{
			$line = $token->getLine();
			throw new Twig_Exception_Syntax(sprintf('%sUnexpected token "%s" of value "%s" ("%s" expected%s)',
				$message ? $message.'. ' : '',
				Twig_Token::typeToEnglish($token->getType(), $line), $token->getValue(),
				Twig_Token::typeToEnglish($type, $line), $value ? sprintf(' with value "%s"', $value) : ''),
				$line,
				$this->filename
			);
		}
		$this->next();

		return $token;
	}

	/**
	 * 查看下一个token
	 *
	 * @param integer $number
	 *
	 * @return Twig_Token
	 */
	public function look($number = 1)
	{
		if ( ! isset($this->tokens[$this->current + $number]))
		{
			throw new Twig_Exception_Syntax('Unexpected end of template', $this->tokens[$this->current + $number - 1]->getLine(), $this->filename);
		}

		return $this->tokens[$this->current + $number];
	}

	/**
	 * 测试当前token
	 *
	 * @return bool
	 */
	public function test($primary, $secondary = NULL)
	{
		return $this->tokens[$this->current]->test($primary, $secondary);
	}

	/**
	 * Checks if end of stream was reached
	 *
	 * @return bool
	 */
	public function is_eof()
	{
		return $this->tokens[$this->current]->getType() === Twig_Token::EOF_TYPE;
	}

	/**
	 * Gets the current token
	 *
	 * @return Twig_Token
	 */
	public function getCurrent()
	{
		return $this->tokens[$this->current];
	}

	/**
	 * Gets the filename associated with this stream
	 *
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}
}
