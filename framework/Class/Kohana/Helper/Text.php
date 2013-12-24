<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 文本助手类
 *
 * @package    Kohana
 * @category   Helpers
 */
class Kohana_Helper_Text {

	/**
	 * @var  array   number units and text equivalents
	 */
	public static $units = array(
		1000000000 => 'billion',
		1000000    => 'million',
		1000       => 'thousand',
		100        => 'hundred',
		90 => 'ninety',
		80 => 'eighty',
		70 => 'seventy',
		60 => 'sixty',
		50 => 'fifty',
		40 => 'fourty',
		30 => 'thirty',
		20 => 'twenty',
		19 => 'nineteen',
		18 => 'eighteen',
		17 => 'seventeen',
		16 => 'sixteen',
		15 => 'fifteen',
		14 => 'fourteen',
		13 => 'thirteen',
		12 => 'twelve',
		11 => 'eleven',
		10 => 'ten',
		9  => 'nine',
		8  => 'eight',
		7  => 'seven',
		6  => 'six',
		5  => 'five',
		4  => 'four',
		3  => 'three',
		2  => 'two',
		1  => 'one',
	);
   
	/**
	 * Encode special characters in a plain-text string for display as HTML.
	 *
	 * Also validates strings as UTF-8 to prevent cross site scripting attacks
	 * on Internet Explorer 6.
	 *
	 * @param  string  $text  The text to be checked or processed.
	 *
	 * @return  string An HTML safe version of `$text`, or an empty string if $text is not valid UTF-8.
	 */
	public static function plain($text)
	{
		return HTML::chars($text);
	}

	/**
	 * 限制字符长度
	 *
	 *     $text = Helper_Text::limit_words($text);
	 *
	 * @param   string  $str        phrase to limit words of
	 * @param   integer $limit      number of words to limit to
	 * @param   string  $end_char   end character or entity
	 * @return  string
	 */
	public static function limit_words($str, $limit = 100, $end_char = NULL)
	{
		$limit = (int) $limit;
		$end_char = ($end_char === NULL) ? '…' : $end_char;

		if (trim($str) === '')
		{
			return $str;
		}

		if ($limit <= 0)
		{
			return $end_char;
		}

		preg_match('/^\s*+(?:\S++\s*+){1,'.$limit.'}/u', $str, $matches);

		// Only attach the end character if the matched string is shorter
		// than the starting string.
		return rtrim($matches[0]).((strlen($matches[0]) === strlen($str)) ? '' : $end_char);
	}

	/**
	 * 限制字节长度
	 *
	 *     $text = Helper_Text::limit_chars($text);
	 *
	 * @param   string  $str            phrase to limit characters of
	 * @param   integer $limit          number of characters to limit to
	 * @param   string  $end_char       end character or entity
	 * @param   boolean $preserve_words enable or disable the preservation of words while limiting
	 * @return  string
	 */
	public static function limit_chars($str, $limit = 100, $end_char = NULL, $preserve_words = FALSE)
	{
		$end_char = ($end_char === NULL) ? '…' : $end_char;

		$limit = (int) $limit;

		if (trim($str) === '' OR UTF8::strlen($str) <= $limit)
		{
			return $str;
		}

		if ($limit <= 0)
		{
			return $end_char;
		}

		if ($preserve_words === FALSE)
		{
			return rtrim(UTF8::substr($str, 0, $limit)).$end_char;
		}

		// Don't preserve words. The limit is considered the top limit.
		// No strings with a length longer than $limit should be returned.
		if ( ! preg_match('/^.{0,'.$limit.'}\s/us', $str, $matches))
		{
			return $end_char;
		}

		return rtrim($matches[0]).((strlen($matches[0]) === strlen($str)) ? '' : $end_char);
	}

	/**
	 * Alternates between two or more strings.
	 *
	 *     echo Helper_Text::alternate('one', 'two'); // "one"
	 *     echo Helper_Text::alternate('one', 'two'); // "two"
	 *     echo Helper_Text::alternate('one', 'two'); // "one"
	 *
	 * Note that using multiple iterations of different strings may produce
	 * unexpected results.
	 *
	 * @param   string  $str,...    strings to alternate between
	 * @return  string
	 */
	public static function alternate()
	{
		static $i;
		if (func_num_args() === 0)
		{
			$i = 0;
			return '';
		}

		$args = func_get_args();
		return $args[($i++ % count($args))];
	}

	/**
	 * 根据指定配置生成随机字符串
	 *
	 *
	 *     $str = Helper_Text::random(); // 8 character random string
	 *
	 * The following types are supported:
	 *
	 * alnum
	 * :  Upper and lower case a-z, 0-9 (default)
	 *
	 * alpha
	 * :  Upper and lower case a-z
	 *
	 * hexdec
	 * :  Hexadecimal characters a-f, 0-9
	 *
	 * distinct
	 * :  Uppercase characters and numbers that cannot be confused
	 *
	 * mix
	 * :  混合模式，混合了各种字符
	 *
	 * You can also create a custom type by providing the "pool" of characters
	 * as the type.
	 *
	 * @param   string  $type   a type of pool, or a string of characters to use as the pool
	 * @param   integer $length length of string to return
	 * @return  string
	 */
	public static function random($type = NULL, $length = 8)
	{
		if ($type === NULL)
		{
			// Default is to generate an alphanumeric string
			$type = 'alnum';
		}

		$utf8 = FALSE;

		switch ($type)
		{
			case 'alnum':
				$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
			case 'alpha':
				$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
			case 'hexdec':
				$pool = '0123456789abcdef';
			break;
			case 'numeric':
				$pool = '0123456789';
			break;
			case 'nozero':
				$pool = '123456789';
			break;
			case 'distinct':
				$pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
			break;
			case 'mix':
				$pool = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'\"\\|~!@#$%^&*()_+-[]{};:,.<>/?";
			default:
				$pool = (string) $type;
				$utf8 = ! UTF8::is_ascii($pool);
			break;
		}

		// Split the pool into an array of characters
		$pool = ($utf8 === TRUE) ? UTF8::str_split($pool, 1) : str_split($pool, 1);

		// Largest pool key
		$max = count($pool) - 1;

		$str = '';
		for ($i = 0; $i < $length; $i++)
		{
			// Select a random character from the pool and add it to the string
			$str .= $pool[mt_rand(0, $max)];
		}

		// Make sure alnum strings contain at least one letter and one digit
		if ($type === 'alnum' AND $length > 1)
		{
			if (ctype_alpha($str))
			{
				// Add a random digit
				$str[mt_rand(0, $length - 1)] = chr(mt_rand(48, 57));
			}
			elseif (ctype_digit($str))
			{
				// Add a random letter
				$str[mt_rand(0, $length - 1)] = chr(mt_rand(65, 90));
			}
		}

		return $str;
	}

	/**
	 * 使用指定分隔符分割字符串后再执行ucfirst
	 * 
	 *      $str = Helper_Text::ucfirst('content-type'); // 返回"Content-Type" 
	 *
	 * @param   string  $string     string to transform
	 * @param   string  $delimiter  delemiter to use
	 * @return  string
	 */
	public static function ucfirst($string, $delimiter = '-')
	{
		// Put the keys back the Case-Convention expected
		return implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
	}
	
	const REDUCE_SLASHES_REGEX = '#(?<!:)//+#';
	const REDUCE_SLASHES_REPLACE = '/';

	/**
	 * Reduces multiple slashes in a string to single slashes.
	 *
	 *     $str = Helper_Text::reduce_slashes('foo//bar/baz'); // "foo/bar/baz"
	 *
	 * @param   string  $str    string to reduce slashes of
	 * @return  string
	 */
	public static function reduce_slashes($str)
	{
		return preg_replace(Helper_Text::REDUCE_SLASHES_REGEX, Helper_Text::REDUCE_SLASHES_REPLACE, $str);
	}

	/**
	 * Replaces the given words with a string.
	 *
	 *     // Displays "What the #####, man!"
	 *     echo Helper_Text::censor('What the frick, man!', array(
	 *         'frick' => '#####',
	 *     ));
	 *
	 * @param   string  $str                    phrase to replace words in
	 * @param   array   $badwords               words to replace
	 * @param   string  $replacement            replacement string
	 * @param   boolean $replace_partial_words  replace words across word boundries (space, period, etc)
	 * @return  string
	 */
	public static function censor($str, $badwords, $replacement = '#', $replace_partial_words = TRUE)
	{
		foreach ( (array) $badwords AS $key => $badword)
		{
			$badwords[$key] = str_replace('\*', '\S*?', preg_quote( (string) $badword));
		}

		$regex = '('.implode('|', $badwords).')';

		if ($replace_partial_words === FALSE)
		{
			// Just using \b isn't sufficient when we need to replace a badword that already contains word boundaries itself
			$regex = '(?<=\b|\s|^)'.$regex.'(?=\b|\s|$)';
		}

		$regex = '!'.$regex.'!ui';

		if (UTF8::strlen($replacement) == 1)
		{
			$regex .= 'e';
			return preg_replace($regex, 'str_repeat($replacement, UTF8::strlen(\'$1\'))', $str);
		}

		return preg_replace($regex, $replacement, $str);
	}

	/**
	 * 查找字符串的相似部分内容
	 *
	 *     $match = Helper_Text::similar(array('fred', 'fran', 'free'); // "fr"
	 *
	 * @param   array   $words  要查找和对比的字符串
	 * @return  string
	 */
	public static function similar(array $words)
	{
		// First word is the word to match against
		$word = current($words);

		for ($i = 0, $max = strlen($word); $i < $max; ++$i)
		{
			foreach ($words AS $w)
			{
				// Once a difference is found, break out of the loops
				if ( ! isset($w[$i]) OR $w[$i] !== $word[$i])
					break 2;
			}
		}

		// Return the similar text
		return substr($word, 0, $i);
	}

	/**
	 * Converts text email addresses and anchors into links. Existing links
	 * will not be altered.
	 *
	 *     echo Helper_Text::auto_link($text);
	 *
	 * [!!] This method is not foolproof since it uses regex to parse HTML.
	 *
	 * @param   string  $text   text to auto link
	 * @return  string
	 */
	public static function auto_link($text)
	{
		// Auto link emails first to prevent problems with "www.domain.com@example.com"
		return Helper_Text::auto_link_urls(Helper_Text::auto_link_emails($text));
	}

	/**
	 * Converts text anchors into links. Existing links will not be altered.
	 *
	 *     echo Helper_Text::auto_link_urls($text);
	 *
	 * [!!] This method is not foolproof since it uses regex to parse HTML.
	 *
	 * @param   string  $text   text to auto link
	 * @return  string
	 */
	public static function auto_link_urls($text)
	{
		// Find and replace all http/https/ftp/ftps links that are not part of an existing html anchor
		$text = preg_replace_callback('~\b(?<!href="|">)(?:ht|f)tps?://[^<\s]+(?:/|\b)~i', 'Helper_Text::_auto_link_urls_callback1', $text);

		// Find and replace all naked www.links.com (without http://)
		return preg_replace_callback('~\b(?<!://|">)www(?:\.[a-z0-9][-a-z0-9]*+)+\.[a-z]{2,6}[^<\s]*\b~i', 'Helper_Text::_auto_link_urls_callback2', $text);
	}

	protected static function _auto_link_urls_callback1($matches)
	{
		return HTML::anchor($matches[0]);
	}

	protected static function _auto_link_urls_callback2($matches)
	{
		return HTML::anchor('http://'.$matches[0], $matches[0]);
	}

	/**
	 * Converts text email addresses into links. Existing links will not
	 * be altered.
	 *
	 *     echo Helper_Text::auto_link_emails($text);
	 *
	 * [!!] This method is not foolproof since it uses regex to parse HTML.
	 *
	 * @param   string  $text   text to auto link
	 * @return  string
	 */
	public static function auto_link_emails($text)
	{
		// Find and replace all email addresses that are not part of an existing html mailto anchor
		// Note: The "58;" negative lookbehind prevents matching of existing encoded html mailto anchors
		//       The html entity for a colon (:) is &#58; or &#058; or &#0058; etc.
		return preg_replace_callback('~\b(?<!href="mailto:|58;)(?!\.)[-+_a-z0-9.]++(?<!\.)@(?![-.])[-a-z0-9.]+(?<!\.)\.[a-z]{2,6}\b(?!</a>)~i', 'Helper_Text::_auto_link_emails_callback', $text);
	}

	protected static function _auto_link_emails_callback($matches)
	{
		return HTML::mailto($matches[0]);
	}

	/**
	 * Automatically applies "p" and "br" markup to text.
	 * Basically [nl2br](http://php.net/nl2br) on steroids.
	 *
	 *     echo Helper_Text::auto_p($text);
	 *
	 * [!!] This method is not foolproof since it uses regex to parse HTML.
	 *
	 * @param   string  $str    subject
	 * @param   boolean $br     convert single linebreaks to <br />
	 * @return  string
	 */
	public static function auto_p($str, $br = TRUE)
	{
		if (($str = trim($str)) === '')
		{
			return '';
		}

		// 统一换行符
		$str = str_replace(array("\r\n", "\r"), "\n", $str);

		// 过滤每行的空格
		$str = preg_replace('~^[ \t]+~m', '', $str);
		$str = preg_replace('~[ \t]+$~m', '', $str);

		// The following regexes only need to be executed if the string contains html
		if ($html_found = (strpos($str, '<') !== FALSE))
		{
			// Elements that should not be surrounded by p tags
			$no_p = '(?:p|div|h[1-6r]|ul|ol|li|blockquote|d[dlt]|pre|t[dhr]|t(?:able|body|foot|head)|c(?:aption|olgroup)|form|s(?:elect|tyle)|a(?:ddress|rea)|ma(?:p|th))';

			// Put at least two linebreaks before and after $no_p elements
			$str = preg_replace('~^<'.$no_p.'[^>]*+>~im', "\n$0", $str);
			$str = preg_replace('~</'.$no_p.'\s*+>$~im', "$0\n", $str);
		}

		// Do the <p> magic!
		$str = '<p>'.trim($str).'</p>';
		$str = preg_replace('~\n{2,}~', "</p>\n\n<p>", $str);

		// The following regexes only need to be executed if the string contains html
		if ($html_found !== FALSE)
		{
			// Remove p tags around $no_p elements
			$str = preg_replace('~<p>(?=</?'.$no_p.'[^>]*+>)~i', '', $str);
			$str = preg_replace('~(</?'.$no_p.'[^>]*+>)</p>~i', '$1', $str);
		}

		// Convert single linebreaks to <br />
		if ($br === TRUE)
		{
			$str = preg_replace('~(?<!\n)\n(?!\n)~', "<br />\n", $str);
		}

		return $str;
	}

	/**
	 * Returns human readable sizes. Based on original functions written by
	 * [Aidan Lister](http://aidanlister.com/repos/v/function.size_readable.php)
	 * and [Quentin Zervaas](http://www.phpriot.com/d/code/strings/filesize-format/).
	 *
	 *     echo Helper_Text::bytes(filesize($file));
	 *
	 * @param   integer $bytes      size in bytes
	 * @param   string  $force_unit a definitive unit
	 * @param   string  $format     the return string format
	 * @param   boolean $si         whether to use SI prefixes or IEC
	 * @return  string
	 */
	public static function bytes($bytes, $force_unit = NULL, $format = NULL, $si = TRUE)
	{
		// 格式化字符串
		$format = ($format === NULL) ? '%01.2f %s' : (string) $format;

		// IEC prefixes (binary)
		if ($si == FALSE OR strpos($force_unit, 'i') !== FALSE)
		{
			$units = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
			$mod   = 1024;
		}
		// SI prefixes (decimal)
		else
		{
			$units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB');
			$mod   = 1000;
		}

		// Determine unit to use
		if (($power = array_search( (string) $force_unit, $units)) === FALSE)
		{
			$power = ($bytes > 0) ? floor(log($bytes, $mod)) : 0;
		}

		return sprintf($format, $bytes / pow($mod, $power), $units[$power]);
	}

	/**
	 * Format a number to human-readable text.
	 *
	 *     // Display: one thousand and twenty-four
	 *     echo Helper_Text::number(1024);
	 *
	 *     // Display: five million, six hundred and thirty-two
	 *     echo Helper_Text::number(5000632);
	 *
	 * @param   integer $number number to format
	 * @return  string
	 */
	public static function number($number)
	{
		// The number must always be an integer
		$number = (int) $number;

		// Uncompiled text version
		$text = array();

		// Last matched unit within the loop
		$last_unit = NULL;

		// The last matched item within the loop
		$last_item = '';

		foreach (Helper_Text::$units AS $unit => $name)
		{
			if ($number / $unit >= 1)
			{
				// $value = the number of times the number is divisble by unit
				$number -= $unit * ($value = (int) floor($number / $unit));
				// Temporary var for textifying the current unit
				$item = '';

				if ($unit < 100)
				{
					if ($last_unit < 100 AND $last_unit >= 20)
					{
						$last_item .= '-'.$name;
					}
					else
					{
						$item = $name;
					}
				}
				else
				{
					$item = Helper_Text::number($value).' '.$name;
				}

				// In the situation that we need to make a composite number (i.e. twenty-three)
				// then we need to modify the previous entry
				if (empty($item))
				{
					array_pop($text);

					$item = $last_item;
				}

				$last_item = $text[] = $item;
				$last_unit = $unit;
			}
		}

		if (count($text) > 1)
		{
			$and = array_pop($text);
		}

		$text = implode(', ', $text);

		if (isset($and))
		{
			$text .= ' and '.$and;
		}

		return $text;
	}

	/**
	 * Prevents [widow words](http://www.shauninman.com/archive/2006/08/22/widont_wordpress_plugin)
	 * by inserting a non-breaking space between the last two words.
	 *
	 *     echo Helper_Text::widont($text);
	 *
	 * @param   string  $str    text to remove widows from
	 * @return  string
	 */
	public static function widont($str)
	{
		$str = rtrim($str);
		$space = strrpos($str, ' ');

		if ($space !== FALSE)
		{
			$str = substr($str, 0, $space).'&nbsp;'.substr($str, $space + 1);
		}

		return $str;
	}
   
	/**
	 * Standardize newlines
	 *
	 * @param	string	the value
	 * @return	string
	 */
	public static function standardize($value)
	{
		if (strpos($value, "\r") !== FALSE)
		{
			// Standardize newlines
			$value = str_replace(array("\r\n", "\r"), "\n", $value);
		}

		return $value;
	}
	
	/**
	 * Extract link URLs from HTML content.
	 *
	 * @param	string	the HTML
	 * @param	boolean	remove duplicate URLs?
	 * @return	array
	 */
	public static function get_urls($html, $unique = FALSE)
	{
		$regexp = "/<a[^>]+href\s*=\s*[\"|']([^\s\"']+)[\"|'][^>]*>[^<]*<\/a>/i";
		preg_match_all($regexp, stripslashes($html), $matches);
		$matches = $matches[1];
	
		if ($unique)
		{
			$matches = array_values(array_unique($matches));
		}
	
		return $matches;
	}
	
	const NORMALIZE_SPACES_REGEX = '/[\s\n\r\t]+/';
	const NORMALIZE_SPACES_REPLACE = ' ';
	
	/**
	 * Replace runs of multiple whitespace characters with a single space.
	 *
	 * @param	string	the string to normalize
	 * @return	string
	 */
	public static function normalize_spaces($string)
	{
		$normalized = $string;
		if ( ! empty($normalized))
		{
			$normalized = preg_replace(Helper_Text::NORMALIZE_SPACES_REGEX, Helper_Text::NORMALIZE_SPACES_REPLACE, $string);
			$normalized = UTF8::trim($normalized);
		}
		return $normalized;
	}

	/**
	 * Returns information about the client user agent.
	 *
	 *     // Returns "Chrome" when using Google Chrome
	 *     $browser = Helper_Text::user_agent('browser');
	 *
	 * Multiple values can be returned at once by using an array:
	 *
	 *     // Get the browser and platform with a single call
	 *     $info = Helper_Text::user_agent(array('browser', 'platform'));
	 *
	 * When using an array for the value, an associative array will be returned.
	 *
	 * @param   mixed   $value  array or string to return: browser, version, robot, mobile, platform
	 * @return  mixed   requested information, FALSE if nothing is found
	 */
	public static function user_agent($agent, $value)
	{
		if (is_array($value))
		{
			$data = array();
			foreach ($value AS $part)
			{
				// Add each part to the set
				$data[$part] = Helper_Text::user_agent($agent, $part);
			}

			return $data;
		}

		if ($value === 'browser' OR $value == 'version')
		{
			// Extra data will be captured
			$info = array();

			// Load browsers
			$browsers = Kohana::config('UserAgent')->browser;

			foreach ($browsers AS $search => $name)
			{
				if (stripos($agent, $search) !== FALSE)
				{
					// Set the browser name
					$info['browser'] = $name;
					$info['version'] = preg_match('#'.preg_quote($search).'[^0-9.]*+([0-9.][0-9.a-z]*)#i', Request::$user_agent, $matches)
						? $matches[1] // Set the version number
						: FALSE; // No version number found

					return $info[$value];
				}
			}
		}
		else
		{
			// Load the search group for this type
			$group = Kohana::config('UserAgent')->$value;

			foreach ($group AS $search => $name)
			{
				if (stripos($agent, $search) !== FALSE)
				{
					// Set the value name
					return $name;
				}
			}
		}

		// The value requested could not be found
		return FALSE;
	}
   
	/**
	 * Converts fractions to their html equivalent (for example, 1/4 will become &frac14;)
	 *
	 * @see http://drupal.org/project/more_filters
	 * @param  string string to be processed
	 * @return string
	 */
	public static function fractions($text)
	{
		// Converts fractions to their html equivalent (for example, 1/4 will become &frac14;).
		$processed_text = $text;
		$processed_text = self::_replace_fraction('1/4', '&frac14;', $processed_text);
		$processed_text = self::_replace_fraction('3/4', '&frac34;', $processed_text);
		$processed_text = self::_replace_fraction('1/2', '&frac12;', $processed_text);
		$processed_text = self::_replace_fraction('1/3', '&#8531;', $processed_text);
		$processed_text = self::_replace_fraction('2/3', '&#8532;', $processed_text);
		$processed_text = self::_replace_fraction('1/8', '&#8539;', $processed_text);
		$processed_text = self::_replace_fraction('3/8', '&#8540;', $processed_text);
		$processed_text = self::_replace_fraction('5/8', '&#8541;', $processed_text);
		$processed_text = self::_replace_fraction('7/8', '&#8542;', $processed_text);
		
		return $processed_text;
	}
	
	private static function _replace_fraction($fraction, $html_fraction, $text)
	{
		// fraction can't be preceded or followed by a number or letter.
		$search = '/([^0-9A-Z]+)' . preg_quote($fraction, '/') . '([^0-9A-Z]+)/i';
		$replacement = '$1' . $html_fraction . '$2';
		return preg_replace($search, $replacement, $text);
	}
   
	/**
	 * Returns a string with all spaces converted to underscores (by default), accented
	 * characters converted to non-accented characters, and non word characters removed.
	 *
	 * @param string $string the string you want to slug
	 * @param string $replacement will replace keys in map
	 * @return string
	 */
	public static function convert_accented_characters($string, $replacement = '-')
	{
	    $string = strtolower($string);
	    
	    $foreign_characters = array(
		'/Ã¤|Ã¦|Ç½/' => 'ae',
		'/Ã¶|Å“/' => 'oe',
		'/Ã¼/' => 'ue',
		'/Ã„/' => 'Ae',
		'/Ãœ/' => 'Ue',
		'/Ã–/' => 'Oe',
		'/Ã€|Ã|Ã‚|Ãƒ|Ã„|Ã…|Çº|Ä€|Ä‚|Ä„|Ç/' => 'A',
		'/Ã |Ã¡|Ã¢|Ã£|Ã¥|Ç»|Ä|Äƒ|Ä…|ÇŽ|Âª/' => 'a',
		'/Ã‡|Ä†|Äˆ|ÄŠ|ÄŒ/' => 'C',
		'/Ã§|Ä‡|Ä‰|Ä‹|Ä/' => 'c',
		'/Ã|ÄŽ|Ä/' => 'D',
		'/Ã°|Ä|Ä‘/' => 'd',
		'/Ãˆ|Ã‰|ÃŠ|Ã‹|Ä’|Ä”|Ä–|Ä˜|Äš/' => 'E',
		'/Ã¨|Ã©|Ãª|Ã«|Ä“|Ä•|Ä—|Ä™|Ä›/' => 'e',
		'/Äœ|Äž|Ä |Ä¢/' => 'G',
		'/Ä|ÄŸ|Ä¡|Ä£/' => 'g',
		'/Ä¤|Ä¦/' => 'H',
		'/Ä¥|Ä§/' => 'h',
		'/ÃŒ|Ã|ÃŽ|Ã|Ä¨|Äª|Ä¬|Ç|Ä®|Ä°/' => 'I',
		'/Ã¬|Ã­|Ã®|Ã¯|Ä©|Ä«|Ä­|Ç|Ä¯|Ä±/' => 'i',
		'/Ä´/' => 'J',
		'/Äµ/' => 'j',
		'/Ä¶/' => 'K',
		'/Ä·/' => 'k',
		'/Ä¹|Ä»|Ä½|Ä¿|Å/' => 'L',
		'/Äº|Ä¼|Ä¾|Å€|Å‚/' => 'l',
		'/Ã‘|Åƒ|Å…|Å‡/' => 'N',
		'/Ã±|Å„|Å†|Åˆ|Å‰/' => 'n',
		'/Ã’|Ã“|Ã”|Ã•|ÅŒ|ÅŽ|Ç‘|Å|Æ |Ã˜|Ç¾/' => 'O',
		'/Ã²|Ã³|Ã´|Ãµ|Å|Å|Ç’|Å‘|Æ¡|Ã¸|Ç¿|Âº/' => 'o',
		'/Å”|Å–|Å˜/' => 'R',
		'/Å•|Å—|Å™/' => 'r',
		'/Åš|Åœ|Åž|Å /' => 'S',
		'/Å›|Å|ÅŸ|Å¡|Å¿/' => 's',
		'/Å¢|Å¤|Å¦/' => 'T',
		'/Å£|Å¥|Å§/' => 't',
		'/Ã™|Ãš|Ã›|Å¨|Åª|Å¬|Å®|Å°|Å²|Æ¯|Ç“|Ç•|Ç—|Ç™|Ç›/' => 'U',
		'/Ã¹|Ãº|Ã»|Å©|Å«|Å­|Å¯|Å±|Å³|Æ°|Ç”|Ç–|Ç˜|Çš|Çœ/' => 'u',
		'/Ã|Å¸|Å¶/' => 'Y',
		'/Ã½|Ã¿|Å·/' => 'y',
		'/Å´/' => 'W',
		'/Åµ/' => 'w',
		'/Å¹|Å»|Å½/' => 'Z',
		'/Åº|Å¼|Å¾/' => 'z',
		'/Ã†|Ç¼/' => 'AE',
		'/ÃŸ/' => 'ss',
		'/Ä²/' => 'IJ',
		'/Ä³/' => 'ij',
		'/Å’/' => 'OE',
		'/Æ’/' => 'f'
	    );
	
	    if (is_array($replacement))
	    {
		$map         = $replacement;
		$replacement = '_';
	    }
	
	    $quotedReplacement = preg_quote($replacement, '/');
	    
	    $merge = array(
		'/[^\s\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
		'/\\s+/' => $replacement,
		sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => ''
	    );
	
	    $map = $foreign_characters + $merge;
	    return preg_replace(array_keys($map), array_values($map), $string);
	}
   
	/**
	 * Navigates through an array and removes slashes from the values
	 *
	 * If an array is passed, the array_map() function causes a callback to pass the
	 * value back to the function. The slashes from this value will removed.
	 *
	 * It is based on the WP function `stripslashes_deep()`.
	 *
	 *
	 * @param   mixed  $value  The value to be stripped
	 * @return  array|object|string  Stripped value
	 */
	public static function strip_slashes($value)
	{
		if (is_array($value))
		{
			$value = array_map('Helper_Text::strip_slashes', $value);
		}
		elseif (is_object($value))
		{
			$vars = get_object_vars($value);
			foreach ($vars AS $key => $data)
			{
				$value->{$key} = Helper_Text::strip_slashes($data);
			}
		}
		elseif (is_string($value))
		{
			$value = stripslashes($value);
		}

		return $value;
	}
	
	/*
	 * Move links to bottom of the text
	 *
	 * @param   string  text
	 * @param   bool    Convert URLs into links. default true
	 * @return  string
	 */
	public static function move_links_to_end($text, $auto_links = FALSE)
	{
		$search  = '/<a [^>]*href="([^"]+)"[^>]*>(.*?)<\/a>/ie';
		$replace = 'self::_links_list("\\1", "\\2")';
	
		if($auto_links)
		{
			$text = Text::auto_link($text);
		}
	
		$text = preg_replace($search, $replace, $text);
	
		// Add link list
		if ( !empty(self::$_link_list) )
		{
			$text .= __("\n\nLinks:\n") . self::$_link_list;
		}
	
		//reset these vars to defaults
		self::$_link_list  = '';
		self::$_link_count = 0;
	
		return $text;
	}
	
	/*
	 * Helper function called by preg_replace() on link replacement.
	 *
	 *  @param string $link URL of the link
	 *  @param string $display Part of the text to associate number with
	 *  @return string
	 */
	private static function _links_list( $link, $display )
	{
		if ( substr($link, 0, 7) == 'http://' OR substr($link, 0, 8) == 'https://' OR
		    substr($link, 0, 7) == 'mailto:' )
		{
			self::$_link_count++;
			self::$_link_list .= "[" . self::$_link_count . "] $link\n";
			$additional = ' <sup>[' . self::$_link_count . ']</sup>';
		}
		elseif ( substr($link, 0, 11) == 'javascript:' )
		{
			// Don't count the link; ignore it
			$additional = '';
			// what about href="#anchor" ?
		}
		else
		{
			self::$_link_count++;
			self::$_link_list .= "[" . self::$_link_count . "] " . URL::site(null, TRUE);
		
			if ( substr($link, 0, 1) != '/' )
			{
				self::$_link_list .= '/';
			}
		
			self::$_link_list .= "$link\n";
			$additional = ' <sup>[' . self::$_link_count . ']</sup>';
		}

		return $display . $additional;
	}
	
	/**
	 * parses a string of params into an array, and changes numbers to ints
	 *
	 *    params('depth=2,something=test')
	 *
	 *    becomes
	 *
	 *    array(2) (
	 *       "depth" => integer 2
	 *       "something" => string(4) "test"
	 *    )
	 *
	 * @param  string  the params to parse
	 * @return array   the resulting array
	 */
	public static function params($var)
	{
		$var = explode(',', $var);
		$new = array();
		foreach ($var AS $i)
		{
			$i = explode('=',trim($i));
			$new[$i[0]] = Helper_Array::get($i,1,null);
			
			if (is_numeric($new[$i[0]]))
				$new[$i[0]] = (int) $new[$i[0]];
		}

		return $new;
	}
	
	/*
	 * Highlights search terms in a string.
	 *
	 * @param   string  string to highlight terms in
	 * @param   string  words to highlight
	 * @return  string
	*/ 
	public static function highlight($str, $keywords)
	{
		// Trim, strip tags, and replace multiple spaces with single spaces
		$keywords = preg_replace('/\s\s+/', ' ', strip_tags(trim($keywords)));

		// Highlight partial matches
		$var = '';

		foreach (explode(' ', $keywords) AS $keyword)
		{
			$replacement = '<span class="highlight-partial">'.$keyword.'</span>';
			$var .= $replacement." ";

			$str = str_ireplace($keyword, $replacement, $str);
		}

		// Highlight full matches
		$str = str_ireplace(rtrim($var), '<span class="highlight">'.$keywords.'</span>', $str);

		return $str;
	}
	
	const AUTO_P_REVERT_REGEX = '`<br>[\\n\\r]`';
	const AUTO_P_REVERT_BR = '<br>';
	const AUTO_P_REVERT_BR_TAG = '<br />';
	
	/**
	 * Reverts auto_p
	 *
	 * @param  string string to be processed
	 * @return string
	 */
	public static function auto_p_revert($str)
	{
	    $br = preg_match(Helper_Text::AUTO_P_REVERT_REGEX, $str)
			? Helper_Text::AUTO_P_REVERT_BR
			: Helper_Text::AUTO_P_REVERT_BR_TAG;
	    return preg_replace('`'.$br.'([\\n\\r])`', '$1', $str);
	}

	/**
	 * Adds <span class="ordinal"> tags around any ordinals (nd / st / th / rd)
	 *
	 * @see http://drupal.org/project/more_filters
	 * @param  string string to be processed
	 * @return string
	 */
	public static function ordinals($text)
	{
		// Adds <span class="ordinal"> tags around any ordinals (nd / st / th / rd).
		// One or more numbers in front ok, but ignore if ordinal is immediately followed by a number or letter.
		$processed_text = preg_replace('/([0-9]+)(nd|st|th|rd)([^a-zA-Z0-9]+)/', '$1<span class="ordinal">$2</span>$3', $text);
		return $processed_text;
	}
	
	/**
	 * Adds <span class="initial"> tag around the initial letter of each paragraph
	 *
	 * @see http://drupal.org/project/more_filters
	 * @param  string string to be processed
	 * @return string
	 */
	public static function initialcaps($text)
	{
		// Adds <span class="initial"> tag around the initial letter of each paragraph.
		// Only add after an opening <p> tag, ignoring any leading spaces. First letter must be a letter or number (no symbols).
		// Works with contractions.
		$processed_text = preg_replace('/(<p[^>]*>\s*)([A-Z0-9])([A-Z\'\s]{1})/i', '$1<span class="initial">$2</span>$3', $text);
		return $processed_text;
	}
	
	/**
	 * Determine if a string starts with a given needle.
	 *
	 * @param  string  $haystack
	 * @param  string|array  $needles
	 * @return bool
	 */
	public static function starts_with($haystack, $needles)
	{
		foreach ((array) $needles AS $needle)
		{
			if (strpos($haystack, $needle) === 0)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Determine if a given string ends with a given needle.
	 *
	 * @param string $haystack
	 * @param string|array $needles
	 * @return bool
	 */
	public static function ends_with($haystack, $needles)
	{
		foreach ((array) $needles AS $needle)
		{
			if ($needle == substr($haystack, strlen($haystack) - strlen($needle)))
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Return the length of the given string.
	 *
	 * @param  string  $value
	 * @return int
	 */
	public static function length($value)
	{
		return mb_strlen($value);
	}

	/**
	 * Determine if a given string contains a given sub-string.
	 *
	 * @param  string        $haystack
	 * @param  string|array  $needle
	 * @return bool
	 */
	public static function contains($haystack, $needle)
	{
		foreach ((array) $needle AS $n)
		{
			if (strpos($haystack, $n) !== FALSE)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

} // End text
