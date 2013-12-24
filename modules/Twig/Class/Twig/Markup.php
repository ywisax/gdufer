<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Marks a content as safe.
 *
 * @package    Kohana/Twig
 * @category   Base
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Markup implements Countable {

	protected $content;
	protected $charset;

	public function __construct($content, $charset)
	{
		$this->content = (string) $content;
		$this->charset = $charset;
	}

	public function __toString()
	{
		return $this->content;
	}

	public function count()
	{
		return function_exists('mb_get_info') ? mb_strlen($this->content, $this->charset) : strlen($this->content);
	}
}
