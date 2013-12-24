<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Guide模块基础类
 *
 * @package    Kohana/Guide
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
abstract class Kohana_Guide {

	/**
	 * @var string  PCRE fragment for matching 'Class', 'Class::method', 'Class::method()' or 'Class::$property'
	 */
	public static $regex_class_member = '((\w++)(?:::(\$?\w++))?(?:\(\))?)';

	/**
	 * Make a class#member API link using an array of matches from [Guide::$regex_class_member]
	 *
	 * @param   array   $matches    array( 1 => link text, 2 => class name, [3 => member name] )
	 * @return  string
	 */
	public static function link_class_member($matches)
	{
		$link = $matches[1];
		$class = $matches[2];
		$member = NULL;

		if (isset($matches[3]))
		{
			// If the first char is a $ it is a property, e.g. Kohana::$base_url
			if ($matches[3][0] === '$')
			{
				$member = '#property:'.substr($matches[3], 1);
			}
			else
			{
				$member = '#'.$matches[3];
			}
		}

		return HTML::anchor(
			Route::url('guide-api', array('class' => $class)).$member,
			$link,
			NULL,
			NULL,
			TRUE
		);
	}

	public static function factory($class)
	{
		return new Guide_Class($class);
	}

	/**
	 * Creates an html list of all classes sorted by category (or package if no category)
	 *
	 * @return   string   the html for the menu
	 */
	public static function menu()
	{
		$classes = Guide::classes();

		ksort($classes);

		$menu = array();

		$route = Route::get('guide-api');

		foreach ($classes AS $class)
		{
			if (Guide::is_transparent($class, $classes))
			{
				continue;
			}

			$class = Guide_Class::factory($class);
			// 是否应该显示这个类
			if ( ! Guide::show_class($class))
			{
				continue;
			}

			$link = HTML::anchor($route->uri(array('class' => $class->class->name)), $class->class->name);
			if (isset($class->tags['package']))
			{
				foreach ($class->tags['package'] AS $package)
				{
					if (isset($class->tags['category']))
					{
						foreach ($class->tags['category'] AS $category)
						{
							$menu[$package][$category][] = $link;
						}
					}
					else
					{
						$menu[$package]['Base'][] = $link;
					}
				}
			}
			else
			{
				$menu[__('[Unknown]')]['Base'][] = $link;
			}
		}

		// 对查找到的内容进行排序
		ksort($menu);
		return View::factory('Guide.API.Menu')
			->bind('menu', $menu);
	}
	
	const CLASS_DIR = 'Class';
	const CLASS_NAME_SEPARATOR = '_';

	/**
	 * 返回一个包含了所有当前项目能查找到的类的列表
	 *
	 * @param   array   文件列表，一般使用[Kohana::list_files]来获取
	 * @return  array   返回查找到的类
	 */
	public static function classes(array $list = NULL)
	{
		// 做缓存啊，要不卡死
		if ($list === NULL)
		{
			// Kohana::list_files耗费十分多的资源
			$list = Kohana::list_files(Guide::CLASS_DIR);
		}

		// 下面的classes需要不需要缓存，这个得评估一下。
		$classes = array();
		// 多次调用这个变量
		$ext_length = strlen(EXT);
		// 文件夹名加上最后的斜杆
		$class_dir_length = strlen(Guide::CLASS_DIR) + 1;
		foreach ($list AS $name => $path)
		{
			if (is_array($path))
			{
				$classes += Guide::classes($path);
			}
			elseif (substr($name, -$ext_length) === EXT)
			{
				// 删除 "Class/"
				$class = substr($name, $class_dir_length, -$ext_length);
				$class = str_replace(DIRECTORY_SEPARATOR, Guide::CLASS_NAME_SEPARATOR, $class);
				$classes[$class] = $class;
			}
		}

		return $classes;
	}

	/**
	 * 获取指定列表的类和方法
	 *
	 * @param  array  要查找的列表
	 */
	public static function class_methods(array $list = NULL)
	{
		$list = Guide::classes($list);

		$classes = array();
		foreach ($list AS $class)
		{
			// Skip transparent extension classes
			if (Guide::is_transparent($class))
			{
				continue;
			}

			$_class = new ReflectionClass($class);

			$methods = array();

			foreach ($_class->getMethods() AS $_method)
			{
				$declares = $_method->getDeclaringClass()->name;

				// Remove the transparent prefix from declaring classes
				if ($child = Guide::is_transparent($declares))
				{
					$declares = $child;
				}

				if ($declares === $_class->name OR $declares === 'Core')
				{
					$methods[] = $_method->name;
				}
			}

			sort($methods);

			$classes[$_class->name] = $methods;
		}

		return $classes;
	}

	/**
	 * Generate HTML for the content of a tag.
	 *
	 * @param   string  $tag    Name of the tag without @
	 * @param   string  $text   Content of the tag
	 * @return  string  HTML
	 */
	public static function format_tag($tag, $text)
	{
		if ($tag === 'license')
		{
			if (strpos($text, '://') !== FALSE)
				return HTML::anchor($text);
		}
		elseif ($tag === 'link')
		{
			$split = preg_split('/\s+/', $text, 2);

			return HTML::anchor(
				$split[0],
				isset($split[1]) ? $split[1] : $split[0]
			);
		}
		elseif ($tag === 'copyright')
		{
			// Convert the copyright symbol
			return str_replace('(c)', '&copy;', $text);
		}
		elseif ($tag === 'throws')
		{
			$route = Route::get('guide-api');

			if (preg_match('/^(\w+)\W(.*)$/D', $text, $matches))
			{
				return HTML::anchor(
					$route->uri(array('class' => $matches[1])),
					$matches[1]
				).' '.$matches[2];
			}

			return HTML::anchor(
				$route->uri(array('class' => $text)),
				$text
			);
		}
		elseif ($tag === 'see' OR $tag === 'uses')
		{
			if (preg_match('/^'.Guide::$regex_class_member.'/', $text, $matches))
				return Guide::link_class_member($matches);
		}

		return $text;
	}

	/**
	 * Parse a comment to extract the description and the tags
	 *
	 * [!!] Converting the output to HTML in this method is deprecated in 3.3
	 *
	 * @param   string  $comment    The DocBlock to parse
	 * @param   boolean $html       Whether or not to convert the return values
	 *   to HTML (deprecated)
	 * @return  array   array(string $description, array $tags)
	 */
	public static function parse($comment, $html = TRUE)
	{
		// Normalize all new lines to \n
		$comment = str_replace(array("\r\n", "\n"), "\n", $comment);

		// Split into lines while capturing without leading whitespace
		preg_match_all('/^\s*\* ?(.*)\n/m', $comment, $lines);

		// Tag content
		$tags = array();

		/**
		 * Process a tag and add it to $tags
		 *
		 * @param   string  $tag    Name of the tag without @
		 * @param   string  $text   Content of the tag
		 * @return  void
		 */
		$add_tag = function($tag, $text) use ($html, &$tags)
		{
			// Don't show @access lines, they are shown elsewhere
			if ($tag !== 'access')
			{
				if ($html)
				{
					$text = Guide::format_tag($tag, $text);
				}

				// Add the tag
				$tags[$tag][] = $text;
			}
		};

		$comment = $tag = null;
		$end = count($lines[1]) - 1;

		foreach ($lines[1] AS $i => $line)
		{
			// Search this line for a tag
			if (preg_match('/^@(\S+)\s*(.+)?$/', $line, $matches))
			{
				if ($tag)
				{
					// Previous tag is finished
					$add_tag($tag, $text);
				}

				$tag = $matches[1];
				$text = isset($matches[2]) ? $matches[2] : '';

				if ($i === $end)
				{
					// No more lines
					$add_tag($tag, $text);
				}
			}
			elseif ($tag)
			{
				// This is the continuation of the previous tag
				$text .= "\n".$line;

				if ($i === $end)
				{
					// No more lines
					$add_tag($tag, $text);
				}
			}
			else
			{
				$comment .= "\n".$line;
			}
		}

		$comment = trim($comment, "\n");

		if ($comment AND $html)
		{
			// Parse the comment with Markdown
			$comment = Guide_Markdown::markdown($comment);
		}

		return array($comment, $tags);
	}

	/**
	 * 获取一个函数的源码
	 *
	 * @param  string   文件名
	 * @param  int      开始行
	 * @param  int      结束行
	 */
	public static function source($file, $start, $end)
	{
		if ( ! $file) return FALSE;

		$file = file($file, FILE_IGNORE_NEW_LINES);

		$file = array_slice($file, $start - 1, $end - $start + 1);

		if (preg_match('/^(\s+)/', $file[0], $matches))
		{
			$padding = strlen($matches[1]);

			foreach ($file as & $line)
			{
				$line = substr($line, $padding);
			}
		}

		return implode("\n", $file);
	}

	/**
	 * Test whether a class should be shown, based on the api_packages config option
	 *
	 * @param  Guide_Class  the class to test
	 * @return  bool  whether this class should be shown
	 */
	public static function show_class(Guide_Class $class)
	{
		$api_packages = Kohana::config('Guide.api_packages');
		// 如果配置中选择开启API浏览器了，那么所有都可以浏览
		if ($api_packages === TRUE)
		{
			return TRUE;
		}

		// Get the package tags for this class (as an array)
		$packages = Helper_Array::get($class->tags, 'package', array('None'));

		$show_this = FALSE;

		// Loop through each package tag
		foreach ($packages AS $package)
		{
			// If this package is in the allowed packages, set show this to true
			if (in_array($package, explode(',', $api_packages)))
			{
				$show_this = TRUE;
			}
		}

		return $show_this;
	}

	/**
	 * Checks whether a class is a transparent extension class or not.
	 *
	 * This method takes an optional $classes parameter, a list of all defined
	 * class names. If provided, the method will return false unless the extension
	 * class exists. If not, the method will only check known transparent class
	 * prefixes.
	 *
	 * Transparent prefixes are defined in the userguide.php config file:
	 *
	 *     'transparent_prefixes' => array(
	 *         'Kohana' => TRUE,
	 *     );
	 *
	 * Module developers can therefore add their own transparent extension
	 * namespaces and exclude them from the userguide.
	 *          
	 * @param string $class The name of the class to check for transparency
	 * @param array $classes An optional list of all defined classes
	 * @return false If this is not a transparent extension class 
	 * @return string The name of the class that extends this (in the case provided)
	 * @throws InvalidArgumentException If the $classes array is provided and the $class variable is not lowercase
	 */
	public static function is_transparent($class, $classes = NULL)
	{

		static $transparent_prefixes = NULL;

		if ( ! $transparent_prefixes)
		{
			$transparent_prefixes = Kohana::config('Guide.transparent_prefixes');
		}

		// Split the class name at the first underscore
		$segments = explode('_', $class, 2);

		if ((count($segments) == 2) AND (isset($transparent_prefixes[$segments[0]])))
		{
			if ($segments[1] === 'Core')
			{
				// Cater for Module extends Module_Core naming
				$child_class = $segments[0];
			}
			else
			{
				// Cater for Foo extends Module_Foo naming
				$child_class = $segments[1];
			}
			
			// It is only a transparent class if the unprefixed class also exists
			if ($classes AND ! isset($classes[$child_class]))
				return FALSE;
			
			// Return the name of the child class
			return $child_class;
		}
		else
		{
			// Not a transparent class
			return FALSE;
		}
	}
} // End Guide
