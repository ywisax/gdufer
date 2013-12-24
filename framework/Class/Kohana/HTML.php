<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * HTML助手类，提供一些简单实用的HTML方法
 *
 * @package    Kohana
 * @category   Helpers
 */
class Kohana_HTML {

	/**
	 * @var  array  属性的首选排序
	 */
	public static $attribute_order = array
	(
		'action',
		'method',
		'type',
		'id',
		'name',
		'value',
		'href',
		'src',
		'width',
		'height',
		'cols',
		'rows',
		'size',
		'maxlength',
		'rel',
		'media',
		'accept-charset',
		'accept',
		'tabindex',
		'accesskey',
		'alt',
		'title',
		'class',
		'style',
		'selected',
		'checked',
		'readonly',
		'disabled',
	);

	/**
	 * @var  boolean  是否使用严格的XHTML
	 */
	public static $strict = TRUE;

	/**
	 * @var  boolean  新窗口打开连接
	 */
	public static $windowed_urls = FALSE;

	/**
	 * 转换特殊字符为HTML实体，防止XSS攻击
	 *
	 *     echo HTML::chars($username);
	 *
	 * @param   string  $value          要转换的字符串
	 * @param   boolean $double_encode  是否加密已转换的实体
	 * @return  string
	 */
	public static function chars($value, $double_encode = TRUE)
	{
		return htmlspecialchars( (string) $value, ENT_QUOTES, Kohana::$charset, $double_encode);
	}

	/**
	 * 转换字符串为HTML实体
	 *
	 *     echo HTML::entities($username);
	 *
	 * @param	string	$value	转换字符串
	 * @param	boolean	$double_encode	是否加密存在的实体
	 * @return	string
	 */
	public static function entities($value, $double_encode = TRUE)
	{
		return htmlentities( (string) $value, ENT_QUOTES, Kohana::$charset, $double_encode);
	}

	/**
	 * 生成链接
	 *
	 *     echo HTML::anchor('/user/profile', 'My Profile');
	 *
	 * @param   string  $uri        URL或者URI
	 * @param   string  $title      连接文本
	 * @param   array   $attributes 附加的属性
	 * @param   mixed   $protocol   protocol to pass to URL::base()
	 * @param   boolean $index      include the index page
	 * @return  string
	 */
	public static function anchor($uri, $title = NULL, array $attributes = NULL, $protocol = NULL, $index = TRUE)
	{
		if ($title === NULL)
		{
			// Use the URI as the title
			$title = $uri;
		}

		if ($uri === '')
		{
			// Only use the base URL
			$uri = URL::base($protocol, $index);
		}
		else
		{
			if (strpos($uri, '://') !== FALSE)
			{
				if (HTML::$windowed_urls === TRUE AND empty($attributes['target']))
				{
					// Make the link open in a new window
					$attributes['target'] = '_blank';
				}
			}
			elseif ($uri[0] !== '#')
			{
				// Make the URI absolute for non-id anchors
				$uri = URL::site($uri, $protocol, $index);
			}
		}

		// Add the sanitized link to the attributes
		$attributes['href'] = $uri;

		return '<a'.HTML::attributes($attributes).'>'.$title.'</a>';
	}

	/**
	 * Creates an HTML anchor to a file. Note that the title is not escaped,
	 * to allow HTML elements within links (images, etc).
	 *
	 *     echo HTML::file_anchor('media/doc/user_guide.pdf', 'User Guide');
	 *
	 * @param   string  $file       name of file to link to
	 * @param   string  $title      link text
	 * @param   array   $attributes HTML anchor attributes
	 * @param   mixed   $protocol   protocol to pass to URL::base()
	 * @param   boolean $index      include the index page
	 * @return  string
	 */
	public static function file_anchor($file, $title = NULL, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		if ($title === NULL)
		{
			// 如果标题为空，那就使用文件名作为标题
			$title = basename($file);
		}
		// 添加文件链接到属性列表中去
		$attributes['href'] = URL::site($file, $protocol, $index);
		return '<a'.HTML::attributes($attributes).'>'.$title.'</a>';
	}
	
	const MAILTO_TAG_ENTRY = '&#109;&#097;&#105;&#108;&#116;&#111;&#058;';

	/**
	 * Creates an email (mailto:) anchor. Note that the title is not escaped,
	 * to allow HTML elements within links (images, etc).
	 *
	 *     echo HTML::mailto($address);
	 *
	 * @param   string  $email      email address to send to
	 * @param   string  $title      link text
	 * @param   array   $attributes HTML anchor attributes
	 * @return  string
	 */
	public static function mailto($email, $title = NULL, array $attributes = NULL)
	{
		if ($title === NULL)
		{
			$title = $email;
		}
		return '<a href="'.HTML::MAILTO_TAG_ENTRY.$email.'"'.HTML::attributes($attributes).'>'.$title.'</a>';
	}

	/**
	 * 创建一个CSS链接
	 *
	 *     echo HTML::style('media/css/screen.css');
	 *
	 * @param   string  $file       文件名
	 * @param   array   $attributes 属性
	 * @param   mixed   $protocol   要传递给[URL::base()]的协议参数
	 * @param   boolean $index      是否包含`index.php`
	 * @return  string
	 */
	public static function style($file, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		if (strpos($file, '://') === FALSE)
		{
			$file = URL::site($file, $protocol, $index);
		}
		$attributes['href'] = $file;
		$attributes['rel'] = empty($attributes['rel']) ? 'stylesheet' : $attributes['rel'];
		$attributes['type'] = 'text/css';

		return '<link'.HTML::attributes($attributes).' />';
	}

	/**
	 * 创建一个脚本链接
	 *
	 *     echo HTML::script('media/js/jquery.min.js');
	 *
	 * @param   string  $file       文件名
	 * @param   array   $attributes 默认属性
	 * @param   mixed   $protocol   protocol to pass to URL::base()
	 * @param   boolean $index      是否包含`index.php`
	 * @return  string
	 */
	public static function script($file, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		if (strpos($file, '://') === FALSE)
		{
			$file = URL::site($file, $protocol, $index);
		}
		$attributes['src'] = $file;
		$attributes['type'] = 'text/javascript';

		return '<script'.HTML::attributes($attributes).'></script>';
	}

	/**
	 * Creates a image link.
	 *
	 *     echo HTML::image('media/img/logo.png', array('alt' => 'My Company'));
	 *
	 * @param   string  $file       file name
	 * @param   array   $attributes default attributes
	 * @param   mixed   $protocol   protocol to pass to URL::base()
	 * @param   boolean $index      include the index page
	 * @return  string
	 */
	public static function image($file, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		if (strpos($file, '://') === FALSE)
		{
			$file = URL::site($file, $protocol, $index);
		}
		// 添加图片链接
		$attributes['src'] = $file;
		return '<img'.HTML::attributes($attributes).' />';
	}

	/**
	 * Compiles an array of HTML attributes into an attribute string.
	 * Attributes will be sorted using HTML::$attribute_order for consistency.
	 *
	 *     echo '<div'.HTML::attributes($attrs).'>'.$content.'</div>';
	 *
	 * @param   array   $attributes attribute list
	 * @return  string
	 */
	public static function attributes(array $attributes = NULL)
	{
		if (empty($attributes))
		{
			return '';
		}

		$sorted = array();
		foreach (HTML::$attribute_order AS $key)
		{
			if (isset($attributes[$key]))
			{
				// Add the attribute to the sorted list
				$sorted[$key] = $attributes[$key];
			}
		}

		// 合并已经排序的数组
		$attributes = $sorted + $attributes;
		$compiled = '';
		foreach ($attributes AS $key => $value)
		{
			if ($value === NULL)
			{
				continue;
			}

			if (is_int($key))
			{
				// Assume non-associative keys are mirrored attributes
				$key = $value;
				if ( ! HTML::$strict)
				{
					// Just use a key
					$value = FALSE;
				}
			}

			// 添加属性名称
			$compiled .= ' '.$key;
			if ($value OR HTML::$strict)
			{
				// 添加属性值
				$compiled .= '="'.HTML::chars($value).'"';
			}
		}

		return $compiled;
	}

} // End html
