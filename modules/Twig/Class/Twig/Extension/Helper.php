<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Twig_Extension_Helper
 *
 * @package    Kohana/Twig
 * @category   Extension
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Extension_Helper {

	/* used internally */
	public static function escape_filter_is_safe(Twig_Node $filterArgs)
	{
		foreach ($filterArgs AS $arg)
		{
			if ($arg instanceof Twig_Node_Expression_Constant)
			{
				return array($arg->get_attribute('value'));
			}
			return array();
		}

		return array('html');
	}

	public static function _twig_escape_js_callback($matches)
	{
		$char = $matches[0];

		// \xHH
		if ( ! isset($char[1]))
		{
			return '\\x'.strtoupper(substr('00'.bin2hex($char), -2));
		}

		// \uHHHH
		$char = UTF8::iconv($char, 'UTF-16BE', 'UTF-8');

		return '\\u'.strtoupper(substr('0000'.bin2hex($char), -4));
	}

	public static function _twig_escape_css_callback($matches)
	{
		$char = $matches[0];

		// \xHH
		if ( ! isset($char[1]))
		{
			$hex = ltrim(strtoupper(bin2hex($char)), '0');
			if (strlen($hex) === 0)
			{
				$hex = '0';
			}

			return '\\'.$hex.' ';
		}

		// \uHHHH
		$char = UTF8::iconv($char, 'UTF-16BE', 'UTF-8');
		return '\\'.ltrim(strtoupper(bin2hex($char)), '0').' ';
	}

	/**
	 * This function is adapted from code coming from Zend Framework.
	 *
	 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
	 * @license   http://framework.zend.com/license/new-bsd New BSD License
	 */
	public static function _twig_escape_html_attr_callback($matches)
	{
		/*
		 * While HTML supports far more named entities, the lowest common denominator
		 * has become HTML5's XML Serialisation which is restricted to the those named
		 * entities that XML supports. Using HTML entities would result in this error:
		 *     XML Parsing Error: undefined entity
		 */
		static $entityMap = array(
			34 => 'quot', /* quotation mark */
			38 => 'amp',  /* ampersand */
			60 => 'lt',   /* less-than sign */
			62 => 'gt',   /* greater-than sign */
		);

		$chr = $matches[0];
		$ord = ord($chr);

		/**
		 * The following replaces characters undefined in HTML with the
		 * hex entity for the Unicode replacement character.
		 */
		if (($ord <= 0x1f && $chr != "\t" && $chr != "\n" && $chr != "\r") || ($ord >= 0x7f && $ord <= 0x9f))
		{
			return '&#xFFFD;';
		}

		/**
		 * Check if the current character to escape has a name entity we should
		 * replace it with while grabbing the hex value of the character.
		 */
		if (strlen($chr) == 1)
		{
			$hex = strtoupper(substr('00'.bin2hex($chr), -2));
		}
		else
		{
			$chr = UTF8::iconv($chr, 'UTF-16BE', 'UTF-8');
			$hex = strtoupper(substr('0000'.bin2hex($chr), -4));
		}

		$int = hexdec($hex);
		if (array_key_exists($int, $entityMap))
		{
			return sprintf('&%s;', $entityMap[$int]);
		}

		/**
		 * Per OWASP recommendations, we'll use hex entities for any other
		 * characters where a named entity does not exist.
		 */

		return sprintf('&#x%s;', $hex);
	}

	/* used internally */
	public static function ensure_traversable($seq)
	{
		if ($seq instanceof Traversable || is_array($seq))
		{
			return $seq;
		}

		return array();
	}

	/**
	 * Renders a template.
	 *
	 * @param string  $template       The template to render
	 * @param array   $variables      The variables to pass to the template
	 * @param Boolean $with_context   Whether to pass the current context variables or not
	 * @param Boolean $ignore_missing Whether to ignore missing templates or not
	 * @param Boolean $sandboxed      Whether to sandbox the template or not
	 *
	 * @return string The rendered template
	 */
	public static function twig_include(Twig_Environment $env, $context, $template, $variables = array(), $withContext = true, $ignoreMissing = FALSE, $sandboxed = FALSE)
	{
		if ($withContext)
		{
			$variables = array_merge($context, $variables);
		}

		if ($isSandboxed = $sandboxed && $env->hasExtension('sandbox'))
		{
			$sandbox = $env->getExtension('sandbox');
			if ( ! $alreadySandboxed = $sandbox->isSandboxed())
			{
				$sandbox->enableSandbox();
			}
		}

		try
		{
			return $env->resolveTemplate($template)->render($variables);
		}
		catch (Twig_Exception_Loader $e)
		{
			if ( ! $ignoreMissing)
			{
				throw $e;
			}
		}

		if ($isSandboxed && !$alreadySandboxed)
		{
			$sandbox->disableSandbox();
		}
	}

	/**
	 * Provides the ability to get constants from instances as well as class/global constants.
	 *
	 * @param string      $constant The name of the constant
	 * @param null|object $object   The object to get the constant from
	 *
	 * @return string
	 */
	public static function constant($constant, $object = NULL)
	{
		if ($object !== NULL)
		{
			$constant = get_class($object).'::'.$constant;
		}

		return constant($constant);
	}

}
