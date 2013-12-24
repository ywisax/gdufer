<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 验证助手类.
 *
 * @package    Kohana
 * @category   Security
 */
class Kohana_Valid {

	/**
	 * @var  array  非空数组的判断标准
	 */
	public static $_empty_standard_array = array(NULL, FALSE, '', array());

	/**
	 * 检查指定的字段是否为非空
	 *
	 * @return  boolean
	 */
	public static function not_empty($value)
	{
		// 统一为数组格式
		if (is_object($value) AND $value instanceof ArrayObject)
		{
			$value = $value->getArrayCopy();
		}

		return ! in_array($value, Valid::$_empty_standard_array, TRUE);
	}
	
	/**
	 * 检查指定的字段是否为空
	 *
	 * @return  boolean
	 */
	public static function is_empty($value)
	{
		if (is_object($value) AND $value instanceof ArrayObject)
		{
			$value = $value->getArrayCopy();
		}

		return in_array($value, Valid::$_empty_standard_array, TRUE);
	}
	
	/**
	 * 检查一个字符串是否不为负
	 *
	 * @param string $value
	 * @return bool
	 */
	public static function not_negative($value)
	{
		return Valid::numeric($value) && $value >= 0;
	}

	/**
	 * 检查字符串是否与正则表达式匹配
	 *
	 * @param   string  $value      字符串
	 * @param   string  $expression 要匹配的正则表达式(包括分隔符)
	 * @return  boolean
	 */
	public static function regex($value, $expression)
	{
		return (bool) preg_match($expression, (string) $value);
	}

	/**
	 * 检查字符是否达到的最小长度限制
	 *
	 * @param   string  $value  字符串
	 * @param   integer $length 最小长度
	 * @return  boolean
	 */
	public static function min_length($value, $length)
	{
		return UTF8::strlen($value) >= $length;
	}

	/**
	 * 检查字段是否足够短
	 *
	 * @param   string  $value  value
	 * @param   integer $length maximum length required
	 * @return  boolean
	 */
	public static function max_length($value, $length)
	{
		return UTF8::strlen($value) <= $length;
	}

	/**
	 * Checks that a field is exactly the right length.
	 *
	 * @param   string          $value  value
	 * @param   integer|array   $length exact length required, or array of valid lengths
	 * @return  boolean
	 */
	public static function exact_length($value, $length)
	{
		if (is_array($length))
		{
			foreach ($length AS $strlen)
			{
				if (UTF8::strlen($value) === $strlen)
				{
					return TRUE;
				}
			}
			return FALSE;
		}

		return UTF8::strlen($value) === $length;
	}

	/**
	 * Checks that a field is exactly the value required.
	 *
	 * @param   string  $value      value
	 * @param   string  $required   required value
	 * @return  boolean
	 */
	public static function equals($value, $required)
	{
		return ($value === $required);
	}

	const EMAIL_STRICT_QTEXT = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
	const EMAIL_STRICT_DTEXT = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
	const EMAIL_STRICT_ATOM  = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
	const EMAIL_STRICT_PAIR  = '\\x5c[\\x00-\\x7f]';
	
	const EMAIL_UNSTRICT_RULE = '/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})$/iD';

	/**
	 * 检查邮箱地址是否合法
	 *
	 * @param   string  $email  email address
	 * @param   boolean $strict strict RFC compatibility
	 * @return  boolean
	 */
	public static function email($email, $strict = FALSE)
	{
		if (UTF8::strlen($email) > 254)
		{
			return FALSE;
		}

		if ($strict === TRUE)
		{
			$qtext = Valid::EMAIL_STRICT_QTEXT;
			$dtext = Valid::EMAIL_STRICT_DTEXT;
			$atom  = Valid::EMAIL_STRICT_ATOM;
			$pair  = Valid::EMAIL_STRICT_PAIR;

			$domain_literal = "\\x5b($dtext|$pair)*\\x5d";
			$quoted_string  = "\\x22($qtext|$pair)*\\x22";
			$sub_domain     = "($atom|$domain_literal)";
			$word           = "($atom|$quoted_string)";
			$domain         = "$sub_domain(\\x2e$sub_domain)*";
			$local_part     = "$word(\\x2e$word)*";

			$expression     = "/^$local_part\\x40$domain$/D";
		}
		else
		{
			$expression = Valid::EMAIL_UNSTRICT_RULE;
		}

		return (bool) preg_match($expression, (string) $email);
	}
	
	const EMAIL_DOMAIN_MATCH = '/^[^@]++@/';
	const EMAIL_DOMAIN_REPLACE = '';
	const EMAIL_DOMAIN_RECORD_TYPE = 'MX';

	/**
	 * 通过检查域名的MX记录来判断邮箱是否有效
	 *
	 * @param   string  邮件地址
	 * @return  boolean
	 */
	public static function email_domain($email)
	{
		if ( ! Valid::not_empty($email))
		{
			return FALSE;
		}
		// 检查域名是否有有效的MX记录
		return (bool) checkdnsrr(preg_replace(Valid::EMAIL_DOMAIN_MATCH, Valid::EMAIL_DOMAIN_REPLACE, $email), Valid::EMAIL_DOMAIN_RECORD_TYPE);
	}
	
	const URL_VALID_REGEX = '~^

			# scheme
			[-a-z0-9+.]++://

			# username:password (optional)
			(?:
				    [-a-z0-9$_.+!*\'(),;?&=%]++   # username
				(?::[-a-z0-9$_.+!*\'(),;?&=%]++)? # password (optional)
				@
			)?

			(?:
				# ip address
				\d{1,3}+(?:\.\d{1,3}+){3}+

				| # or

				# hostname (captured)
				(
					     (?!-)[-a-z0-9]{1,63}+(?<!-)
					(?:\.(?!-)[-a-z0-9]{1,63}+(?<!-)){0,126}+
				)
			)

			# port (optional)
			(?::\d{1,5}+)?

			# path (optional)
			(?:/.*)?

			$~iDx';

	/**
	 * 检查URL是否合法
	 *
	 * @param   string  URL
	 * @return  boolean
	 */
	public static function url($url)
	{
		// http://www.apps.ietf.org/rfc/rfc1738.html#sec-5
		if ( ! preg_match(Valid::URL_VALID_REGEX, $url, $matches))
		{
			return FALSE;
		}
		// 只有IP格式
		if ( ! isset($matches[1]))
		{
			return TRUE;
		}
		// 有长度限制的喔
		// http://en.wikipedia.org/wiki/Domain_name#cite_note-0
		if (strlen($matches[1]) > 253)
		{
			return FALSE;
		}

		// An extra check for the top level domain
		// It must start with a letter
		$tld = ltrim(substr($matches[1], (int) strrpos($matches[1], '.')), '.');
		return ctype_alpha($tld[0]);
	}

	/**
	 * 检验是否为合格的IP.
	 *
	 * @param   string  $ip             IP address
	 * @param   boolean $allow_private  allow private IP networks
	 * @return  boolean
	 */
	public static function ip($ip, $allow_private = TRUE)
	{
		// 不允许保留地址
		$flags = FILTER_FLAG_NO_RES_RANGE;
		if ($allow_private === FALSE)
		{
			$flags = $flags | FILTER_FLAG_NO_PRIV_RANGE;
		}
		return (bool) filter_var($ip, FILTER_VALIDATE_IP, $flags);
	}
	
	const CREDIT_CARD_SPLIT_MATCH = '/\D+/';
	const CREDIT_CARD_SPLIT_REPLACE = '';
	const CREDIT_CARD_DEFAULT_CONFIG_GROUP = 'default';

	/**
	 * Validate a number against the [Luhn](http://en.wikipedia.org/wiki/Luhn_algorithm)
	 * (mod10) formula.
	 *
	 * @param   string  $number number to check
	 * @return  boolean
	 */
	public static function luhn($number)
	{
		// Force the value to be a string as this method uses string functions.
		// Converting to an integer may pass PHP_INT_MAX and result in an error!
		$number = (string) $number;

		if ( ! ctype_digit($number))
		{
			// Luhn can only be used on numbers!
			return FALSE;
		}

		// Check number length
		$length = strlen($number);

		// Checksum of the card number
		$checksum = 0;

		for ($i = $length - 1; $i >= 0; $i -= 2)
		{
			// Add up every 2nd digit, starting from the right
			$checksum += substr($number, $i, 1);
		}

		for ($i = $length - 2; $i >= 0; $i -= 2)
		{
			// Add up every 2nd digit doubled, starting from the right
			$double = substr($number, $i, 1) * 2;
			// Subtract 9 from the double where value is greater than 10
			$checksum += ($double >= 10) ? ($double - 9) : $double;
		}

		// If the checksum is a multiple of 10, the number is valid
		return ($checksum % 10 === 0);
	}

	const PHONE_REGEX = '/\D+/';
	const PHONE_REPLACE = '';

	/**
	 * 检查手机号码是否合法
	 *
	 * @param   string  $number     phone number to check
	 * @param   array   $lengths
	 * @return  boolean
	 */
	public static function phone($number, $lengths = NULL)
	{
		if ( ! is_array($lengths))
		{
			$lengths = array(7,10,11);
		}
		$number = preg_replace(Valid::PHONE_REGEX, Valid::PHONE_REPLACE, $number);

		// Check if the number is within range
		return in_array(strlen($number), $lengths);
	}

	/**
	 * 检查日期字符串是否合法
	 *
	 * @param   string  $str    date to check
	 * @return  boolean
	 */
	public static function date($str)
	{
		return (strtotime($str) !== FALSE);
	}

	/**
	 * 检查是否在指定的时间范围内
	 */
	public static function date_range($data, $from, $to)
	{
		return Valid::range(strtotime($data), strtotime($from), strtotime($to));
	}

	const ALPHA_REGEX = '/^\pL++$/uD';

	/**
	 * Checks whether a string consists of alphabetical characters only.
	 *
	 * @param   string  $str    input string
	 * @param   boolean $utf8   trigger UTF-8 compatibility
	 * @return  boolean
	 */
	public static function alpha($str, $utf8 = FALSE)
	{
		$str = (string) $str;
		return ($utf8 === TRUE)
			? ((bool) preg_match(Valid::ALPHA_REGEX, $str))
			: ctype_alpha($str);
	}

	const ALPHA_NUMERIC_REGEX = '/^[\pL\pN]++$/uD';

	/**
	 * 检查一个字符串是否只包含英文字母和数字
	 *
	 * @param   string  $str    input string
	 * @param   boolean $utf8   trigger UTF-8 compatibility
	 * @return  boolean
	 */
	public static function alpha_numeric($str, $utf8 = FALSE)
	{
		return ($utf8 === TRUE)
			? (bool) preg_match(Valid::ALPHA_NUMERIC_REGEX, $str)
			: ctype_alnum($str);
	}

	const ALPHA_DASH_REGEX_UTF8 = '/^[-\pL\pN_]++$/uD';
	const ALPHA_DASH_REGEX = '/^[-a-z0-9_]++$/iD';

	/**
	 * Checks whether a string consists of alphabetical characters, numbers, underscores and dashes only.
	 *
	 * @param   string  $str    input string
	 * @param   boolean $utf8   trigger UTF-8 compatibility
	 * @return  boolean
	 */
	public static function alpha_dash($str, $utf8 = FALSE)
	{
		$regex = ($utf8 === TRUE)
			? Valid::ALPHA_DASH_REGEX_UTF8
			: Valid::ALPAH_DASH_REGEX;

		return (bool) preg_match($regex, $str);
	}

	const DIGIT_REGEX = '/^\pN++$/uD';

	/**
	 * Checks whether a string consists of digits only (no dots or dashes).
	 *
	 * @param   string  $str    input string
	 * @param   boolean $utf8   trigger UTF-8 compatibility
	 * @return  boolean
	 */
	public static function digit($str, $utf8 = FALSE)
	{
		return ($utf8 === TRUE)
			? ((bool) preg_match(Valid::DIGIT_REGEX, $str))
			: ((is_int($str) AND $str >= 0) OR ctype_digit($str));
	}

	/**
	 * Checks whether a string is a valid number (negative and decimal numbers allowed).
	 *
	 * @param   string  $str    input string
	 * @return  boolean
	 */
	public static function numeric($str)
	{
		// Get the decimal point for the current locale
		list($decimal) = array_values(localeconv());
		// A lookahead is used to make sure the string contains at least one digit (before or after the decimal point)
		return (bool) preg_match('/^-?+(?=.*[0-9])[0-9]*+'.preg_quote($decimal).'?+[0-9]*+$/D', (string) $str);
	}

	/**
	 * 检查数字是否在指定范围内
	 *
	 * @param   string  $number 要检查的数值
	 * @param   integer $min    最小值
	 * @param   integer $max    最大值
	 * @param   integer $step   步进
	 * @return  boolean
	 */
	public static function range($number, $min, $max, $step = NULL)
	{
		if ($number <= $min OR $number >= $max)
		{
			// 数字超出了范围
			return FALSE;
		}
		// 默认步进为1
		$step = ( ! $step) ? 1 : intval($step);
		return (($number - $min) % $step === 0);
	}

	/**
	 * Checks if a string is a proper decimal format. Optionally, a specific
	 * number of digits can be checked too.
	 *
	 * @param   string  $str    number to check
	 * @param   integer $places number of decimal places
	 * @param   integer $digits number of digits
	 * @return  boolean
	 */
	public static function decimal($str, $places = 2, $digits = NULL)
	{
		$digits = ($digits > 0)
			? '{'.( (int) $digits).'}' // Specific number of digits
			: '+'; // Any number of digits
		// Get the decimal point for the current locale
		list($decimal) = array_values(localeconv());
		return (bool) preg_match('/^[+-]?[0-9]'.$digits.preg_quote($decimal).'[0-9]{'.( (int) $places).'}$/D', $str);
	}

	const COLOR_REGEX = '/^#?+[0-9a-f]{3}(?:[0-9a-f]{3})?$/iD';

	/**
	 * Checks if a string is a proper hexadecimal HTML color value. The validation
	 * is quite flexible as it does not require an initial "#" and also allows for
	 * the short notation using only three instead of six hexadecimal characters.
	 *
	 * @param   string  $str    input string
	 * @return  boolean
	 */
	public static function color($str)
	{
		return (bool) preg_match(Valid::COLOR_REGEX, $str);
	}

	/**
	 * 检查字段值是否匹配
	 *
	 * @param   array   $array  array of values
	 * @param   string  $field  field name
	 * @param   string  $match  field name to match
	 * @return  boolean
	 */
	public static function matches($array, $field, $match)
	{
		return ($array[$field] === $array[$match]);
	}
	
	const IDNUMBER_REGEX_15 = "/^[0-9]{15}$/D";
	const IDNUMBER_REGEX_18 = "/^[0-9]{17}[0-9xX]$/D";

	/**
	 * 校验身份证（大陆）
	 */
	public static function idnumber($str)
	{
		// 15位
		if (preg_match(Valid::IDNUMBER_REGEX_15, $str))
		{
			return Valid::date('19'.substr($str,6,6));
		}
		if (preg_match (Valid::IDNUMBER_REGEX_18, $str))
		{
			//18位
			$wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1 );
			$checkCode = array ('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2' );
			$sum = 0;
			for($i = 0; $i < 17; $i ++)
			{
				$ai = intval ( substr ( $str, $i, 1 ) );
				$sum += $ai * $wi [$i];
			}
			return ($checkCode [$sum % 11] == substr ( $str, - 1, 1 ))
				? Valid::date(substr($str,6,8))
				: FALSE;
		}
		else
		{
			return FALSE;
		}
	}
} // End Valid
