<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * Kohana核心类，包含了一些最低级的助手方法：
 *
 * - 环境初始化
 * - 级联文件系统的实现
 * - 类的自动加载
 * - 变量和路径调试
 *
 * @package    Kohana
 * @category   Base
 */
class Kohana_Core {

	/**
	 * @var  string  当前版本
	 */
	const VERSION  = 'x1';

	/**
	 * @var  string  代码代号
	 */
	const CODENAME = 'Test';

	// 常见的环境类型常量
	const PRODUCTION  = 10;
	const STAGING     = 20;
	const TESTING     = 30;
	const DEVELOPMENT = 40;

	// 被生成的PHP文件头自动添加的内容
	const FILE_SECURITY = '<?php defined(\'SYS_PATH\') OR die(\'No direct script access.\');';

	// 文件缓存格式：头部，缓存名，数据
	const FILE_CACHE = ":header \n\n// :name\n\n:data\n";

	/**
	 * @var  string  当前环境
	 */
	public static $environment = Kohana::DEVELOPMENT;

	/**
	 * @var  boolean  是否运行在Windows系统中
	 */
	public static $is_windows = FALSE;

	/**
	 * @var  boolean  [魔法引号](http://php.net/manual/en/security.magicquotes.php)是否被激活
	 */
	public static $magic_quotes = FALSE;

	/**
	 * @var  boolean  是否在PHP安全模式中
	 */
	public static $safe_mode = FALSE;

	/**
	 * @var  string  默认输出的Content Type
	 */
	public static $content_type = 'text/html';

	/**
	 * @var  string  输入输出的字符集编码
	 */
	public static $charset = 'utf-8';

	/**
	 * @var  string  当前运行的服务器名称
	 */
	public static $server_name = '';

	/**
	 * @var  array   有效的主机名列表
	 */
	public static $hostnames = array();

	/**
	 * @var  string  当前系统所在的目录（基于WEB目录对比）
	 */
	public static $base_url = '/';

	/**
	 * @var  string  缺省文件入口，会被附加到生成的URL中去，当然你也可以在[Kohana::init]中把它设置为空
	 */
	public static $index_file = 'index.php';

	/**
	 * @var  string  内置缓存器缓存目录，在[Kohana::cache]中会用到，在调用[Kohana::init]时初始化
	 */
	public static $cache_dir;

	/**
	 * @var  integer  内置缓存的默认生命周期，单位秒，在[Kohana::cache]会使用到，[Kohana::init]中设置
	 */
	public static $cache_life = 60;

	/**
	 * @var  boolean  Whether to use internal caching for [Kohana::find_file], does not apply to [Kohana::cache]. Set by [Kohana::init]
	 */
	public static $caching = FALSE;
	
	/**
	 * 系统内置缓存使用的Key
	 */
	public static $internal_caching_key = 'Kohana::find_file()';

	/**
	 * @var  boolean  Whether to enable [profiling](kohana/profiling). Set by [Kohana::init]
	 */
	public static $profiling = TRUE;

	/**
	 * @var  boolean  Enable Kohana catching and displaying PHP errors and exceptions. Set by [Kohana::init]
	 */
	public static $errors = TRUE;

	/**
	 * @var  array  Types of errors to display at shutdown
	 */
	public static $shutdown_errors = array(E_PARSE, E_ERROR, E_USER_ERROR);

	/**
	 * @var  boolean  set the X-Powered-By header
	 */
	public static $expose = FALSE;

	/**
	 * @var  Log  logging object
	 */
	public static $log;

	/**
	 * @var  Config  config object
	 */
	public static $config;

	/**
	 * @var  boolean  Has [Kohana::init] been called?
	 */
	protected static $_init = FALSE;

	/**
	 * @var  array   Currently active modules
	 */
	protected static $_modules = array();

	/**
	 * @var  array   默认的文件包含路径
	 */
	protected static $_paths = array(APP_PATH, SYS_PATH);

	/**
	 * @var  array   File path cache, used when caching is true in [Kohana::init]
	 */
	protected static $_files = array();

	/**
	 * @var  boolean  Has the file path cache changed during this execution?  Used internally when when caching is true in [Kohana::init]
	 */
	protected static $_files_changed = FALSE;
	
	/**
	 * SAE内置缓存的实现方式
	 *
	 * 1.Memcache
	 * 2.KVDB
	 */
	public static $_sae_internal_cache = 1;
	
	public static $_sae_memcache = NULL;

	/**
	 * Initializes the environment:
	 *
	 * - Disables register_globals and magic_quotes_gpc
	 * - Determines the current environment
	 * - Set global settings
	 * - Sanitizes GET, POST, and COOKIE variables
	 * - Converts GET, POST, and COOKIE variables to the global character set
	 *
	 * The following settings can be set:
	 *
	 * Type      | Setting    |	Description                                    | Default Value
	 * ----------|------------|------------------------------------------------|---------------
	 * `string`  | base_url   | The base URL for your application.  This should be the *relative* path from your WEB_PATH to your `index.php` file, in other words, if Kohana is in a subfolder, set this to the subfolder name, otherwise leave it as the default.  **The leading slash is required**, trailing slash is optional.   | `"/"`
	 * `string`  | index_file | The name of the [front controller](http://en.wikipedia.org/wiki/Front_Controller_pattern).  This is used by Kohana to generate relative urls like [HTML::anchor()] and [URL::base()]. This is usually `index.php`.  To [remove index.php from your urls](tutorials/clean-urls), set this to `FALSE`. | `"index.php"`
	 * `string`  | charset    | Character set used for all input and output    | `"utf-8"`
	 * `string`  | cache_dir  | Kohana's cache directory.  Used by [Kohana::cache] for simple internal caching, like [Fragments](kohana/fragments) and **\[caching database queries](this should link somewhere)**.  This has nothing to do with the [Cache module](cache). | `APP_PATH."cache"`
	 * `integer` | cache_life | Lifetime, in seconds, of items cached by [Kohana::cache]         | `60`
	 * `boolean` | errors     | Should Kohana catch PHP errors and uncaught Exceptions and show the `error_view`. See [Error Handling](kohana/errors) for more info. <br /> <br /> Recommended setting: `TRUE` while developing, `FALSE` on production servers. | `TRUE`
	 * `boolean` | profile    | Whether to enable the [Profiler](kohana/profiling). <br /> <br />Recommended setting: `TRUE` while developing, `FALSE` on production servers. | `TRUE`
	 * `boolean` | caching    | Cache file locations to speed up [Kohana::find_file].  This has nothing to do with [Kohana::cache], [Fragments](kohana/fragments) or the [Cache module](cache).  <br /> <br />  Recommended setting: `FALSE` while developing, `TRUE` on production servers. | `FALSE`
	 * `boolean` | expose     | Set the X-Powered-By header
	 *
	 * @param   array   $settings   Array of settings.  See above.
	 * @return  void
	 */
	public static function init(array $settings = NULL)
	{
		if (Kohana::$_init)
		{
			// 不允许执行两次
			return;
		}
		Kohana::$_init = TRUE;

		if (isset($settings['profile']))
		{
			// Enable profiling
			Kohana::$profiling = (bool) $settings['profile'];
		}

		// Start an output buffer
		ob_start();

		if (isset($settings['errors']))
		{
			// Enable error handling
			Kohana::$errors = (bool) $settings['errors'];
		}

		if (Kohana::$errors === TRUE)
		{
			// Enable Kohana exception handling, adds stack traces and error source.
			set_exception_handler(array('Kohana_Exception', 'handler'));

			// Enable Kohana error handling, converts all PHP errors to exceptions.
			set_error_handler(array('Kohana', 'error_handler'));
		}

		/**
		 * Enable xdebug parameter collection in development mode to improve fatal stack traces.
		 */
		if (Kohana::$environment == Kohana::DEVELOPMENT AND extension_loaded('xdebug'))
		{
		    ini_set('xdebug.collect_params', 3);
		}

		// Enable the Kohana shutdown handler, which catches E_FATAL errors.
		register_shutdown_function(array('Kohana', 'shutdown_handler'));

		if (ini_get('register_globals'))
		{
			// Reverse the effects of register_globals
			Kohana::globals();
		}

		if (isset($settings['expose']))
		{
			Kohana::$expose = (bool) $settings['expose'];
		}

		// Determine if we are running in a Windows environment
		Kohana::$is_windows = (DIRECTORY_SEPARATOR === '\\');

		// Determine if we are running in safe mode
		Kohana::$safe_mode = (bool) ini_get('safe_mode');

		if (isset($settings['cache_dir']))
		{
			if (( ! IN_SAE) AND ( ! is_dir($settings['cache_dir'])))
			{
				try
				{
					// 创建缓存目录
					mkdir($settings['cache_dir'], 0755, TRUE);
					// Set permissions (must be manually set to fix umask issues)
					chmod($settings['cache_dir'], 0755);
				}
				catch (Exception $e)
				{
					throw new Kohana_Exception('Could not create cache directory :dir',
						array(':dir' => Debug::path($settings['cache_dir'])));
				}
			}

			// Set the cache directory path
			Kohana::$cache_dir = realpath($settings['cache_dir']);
		}
		else
		{
			// 使用默认缓存目录
			Kohana::$cache_dir = APP_PATH.'Cache';
		}

		// 缓存目录不可写，其实没必要抛出异常吧？
		if (( ! IN_SAE) AND ( ! is_writable(Kohana::$cache_dir)))
		{
			throw new Kohana_Exception('Directory :dir must be writable',
				array(':dir' => Debug::path(Kohana::$cache_dir)));
		}

		if (isset($settings['cache_life']))
		{
			// 设置缓存默认生命周期
			Kohana::$cache_life = (int) $settings['cache_life'];
		}

		if (isset($settings['caching']))
		{
			// 是否使用内置缓存
			Kohana::$caching = (bool) $settings['caching'];
		}

		if (Kohana::$caching === TRUE)
		{
			// 加载文件路径缓存
			Kohana::$_files = Kohana::cache(Kohana::$internal_caching_key);
		}

		if (isset($settings['charset']))
		{
			// 系统字符编码
			Kohana::$charset = strtolower($settings['charset']);
		}

		if (function_exists('mb_internal_encoding'))
		{
			// Set the MB extension encoding to the same character set
			mb_internal_encoding(Kohana::$charset);
		}

		if (isset($settings['base_url']))
		{
			// 自动设置基址
			Kohana::$base_url = rtrim($settings['base_url'], '/').'/';
		}

		if (isset($settings['index_file']))
		{
			// 首页缺省文件
			Kohana::$index_file = trim($settings['index_file'], '/');
		}

		// Determine if the extremely evil magic quotes are enabled
		Kohana::$magic_quotes = (version_compare(PHP_VERSION, '5.4') < 0 AND get_magic_quotes_gpc());

		// 过滤所有提交的变量
		// SESSION要不要也过滤呢？严格来说的话应该也过滤的。
		// 不过SESSION是服务端来生成和保存的，也就是说在处理SESSION的时候能谨慎点，那么应该不用在这里再过滤一次
		$_GET    = Kohana::sanitize($_GET);
		$_POST   = Kohana::sanitize($_POST);
		$_COOKIE = Kohana::sanitize($_COOKIE);
	}
	
	/**
	 * 一个很实用的方法，Ko3.2后反而删除了，现在重新添加上
	 *
	 * @param	string	组名
	 */
	public static function config($group = NULL)
	{
		if ($group === NULL)
		{
			return Kohana::$config;
		}
		else
		{
			return Kohana::$config->load($group);
		}
	}

	/**
	 * Cleans up the environment:
	 *
	 * - Restore the previous error and exception handlers
	 * - Destroy the Kohana::$log and Kohana::$config objects
	 *
	 * @return  void
	 */
	public static function deinit()
	{
		if (Kohana::$_init)
		{
			// Removed the autoloader
			spl_autoload_unregister(array('Kohana', 'auto_load'));

			if (Kohana::$errors)
			{
				// Go back to the previous error handler
				restore_error_handler();

				// Go back to the previous exception handler
				restore_exception_handler();
			}

			// Destroy objects created by init
			Kohana::$log = Kohana::$config = NULL;

			// Reset internal storage
			Kohana::$_modules = Kohana::$_files = array();
			Kohana::$_paths   = array(APP_PATH, SYS_PATH);

			// Reset file cache status
			Kohana::$_files_changed = FALSE;

			// Kohana is no longer initialized
			Kohana::$_init = FALSE;
		}
	}

	/**
	 * 过滤全局变量。如果`register_globals`设置为`on`的话，[Kohana::init]会自动调用这个方法
	 *
	 * @return  void
	 */
	public static function globals()
	{
		if (isset($_REQUEST['GLOBALS']) OR isset($_FILES['GLOBALS']))
		{
			// 阻止全局变量攻击
			echo "Global variable overload attack detected! Request aborted.\n";
			exit(1);
		}

		// 获取所有提交的变量
		$global_variables = array_keys($GLOBALS);

		// 从列表中移除标准的全局变量
		$global_variables = array_diff($global_variables, array(
			'_COOKIE',
			'_ENV',
			'_GET',
			'_FILES',
			'_POST',
			'_REQUEST',
			'_SERVER',
			'_SESSION',
			'GLOBALS',
		));

		foreach ($global_variables AS $name)
		{
			unset($GLOBALS[$name]);
		}
	}

	/**
	 * Recursively sanitizes an input variable:
	 *
	 * - Strips slashes if magic quotes are enabled
	 * - Normalizes all newlines to LF
	 *
	 * @param   mixed   $value  any variable
	 * @return  mixed   sanitized variable
	 */
	public static function sanitize($value)
	{
		if (is_array($value) OR is_object($value))
		{
			foreach ($value AS $key => $val)
			{
				// Recursively clean each value
				$value[$key] = Kohana::sanitize($val);
			}
		}
		elseif (is_string($value))
		{
			if (Kohana::$magic_quotes === TRUE)
			{
				// Remove slashes added by magic quotes
				$value = stripslashes($value);
			}

			if (strpos($value, "\r") !== FALSE)
			{
				// Standardize newlines
				$value = str_replace(array("\r\n", "\r"), "\n", $value);
			}
		}

		return $value;
	}

	/**
	 * Kohana的类自动加载器具体实现
	 *
	 *     // 加载`Class/My/Class/Name.php`
	 *     Kohana::auto_load('My_Class_Name');
	 *
	 * 你也可以传递一个子目录:
	 *
	 *     // 加载`vendor/My/Class/Name.php`
	 *     Kohana::auto_load('My_Class_Name', 'vendor');
	 *
	 * 不要做无聊的事情，不要自己调用这个方法。在你的`Init.php`中使用以下代码来实现自动加载：
	 *
	 *     spl_autoload_register(array('Kohana', 'auto_load'));
	 *
	 * @param   string  $class      类名
	 * @param   string  $directory  要查找的目录
	 * @return  boolean
	 */
	public static function auto_load($class, $directory = 'Class')
	{
		// 该死的命名空间
		$class     = ltrim($class, '\\');
		$file      = '';
		$namespace = '';

		if ($last_namespace_position = strripos($class, '\\'))
		{
			$namespace = substr($class, 0, $last_namespace_position);
			$class     = substr($class, $last_namespace_position + 1);
			$file      = str_replace('\\', DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
		}

		$file .= str_replace('_', DIRECTORY_SEPARATOR, $class);

		if ($path = Kohana::find_file($directory, $file))
		{
			// 加载类文件
			require_once $path;
			// 没有错误，返回TRUE
			return TRUE;
		}

		// 未找到指定的类文件
		return FALSE;
	}

	/**
	 * 加载模块，或获取当前激活模块的列表，如：
	 *
	 *     Kohana::module(array('Auth', 'Database'));
	 *
	 * @param   array   $modules    要加载的模块列表，可留空
	 * @return  array   已激活的模块列表
	 */
	public static function module($modules = NULL, $clear = FALSE)
	{
		if ($modules === NULL)
		{
			// 返回Module列表
			return Kohana::$_modules;
		}
		
		// 从module列表中移除
		// 为了安全着想，现在是只能一次移除一个
		if ($clear === TRUE)
		{
			unset(Kohana::$_modules[$modules]);
			return;
		}
		
		if (is_array($modules))
		{
			foreach ($modules AS $module)
			{
				Kohana::module($module);
			}
		}
		else
		{
			$name = $modules;
			// 检查是否已经存在该Module
			if (isset(Kohana::$_modules[$name]))
			{
				return TRUE;
			}
			
			// 跳过那些错误的信息
			if ( ! $name)
			{
				return FALSE;
			}

			try
			{
				$path = realpath(MOD_PATH.$name).DIRECTORY_SEPARATOR;
				if ( ! is_dir($path))
				{
					// MOD无效喔
					throw new Kohana_Exception('Attempted to load an invalid or missing module \':module\' at \':path\'', array(
						':module' => $name,
						':path'   => Debug::path($path),
					));
				}
			}
			catch (Exception $e)
			{
				// MOD无效喔
				throw new Kohana_Exception('Attempted to load an invalid or missing module \':module\' at \':path\'', array(
					':module' => $name,
					':path'   => Debug::path($path),
				));
			}

			// 保存当前的Module列表
			Kohana::$_modules[$name] = $path;

			// 第一次保存Module信息时，同时也加载他们的初始化脚本
			$init = $path.'Init'.EXT;
			if (is_file($init))
			{
				// 加载模块自由的初始化脚本
				require_once $init;
			}
			
			// 执行成功
			return $path;
		}
	}

	/**
	 * 返回当前使用的`include_paths`
	 *
	 * @return  array
	 */
	public static function include_paths()
	{
		if (empty(Kohana::$_modules))
		{
			return Kohana::$_paths;
		}
		return array('APP_PATH' => APP_PATH) + Kohana::$_modules + array('SYS_PATH' => SYS_PATH);
	}

	/**
	 * 根据[级联文件系统](kohana/files)规则来查找文件
	 *
	 * 如果查找的目录名为`Config`、`Message`或`I18N`，又或者`$array`参数设置为`TRUE`
	 * Kohana会根据[级联系统](kohana/files)查找所有目录的这个文件，合并文件内容并返回一个数组
	 *
	 * 如果没有指定后缀，那么会自动使用在`index.php`中设置的`EXT`作为后缀
	 *
	 *     // 返回View/Template.php的完整路径
	 *     Kohana::find_file('View', 'Template');
	 *
	 *     // 返回media/css/style.css
	 *     Kohana::find_file('media', 'css/style', 'css');
	 *
	 *     // 返回一个数组
	 *     Kohana::find_file('Config', 'Mime');
	 *
	 * @param   string  $dir    directory name (View, I18N, Class, extensions, etc.)
	 * @param   string  $file   filename with subdirectory
	 * @param   string  $ext    extension to search for
	 * @param   boolean $array  return an array of files?
	 * @return  array   a list of files when $array is TRUE
	 * @return  string  single file path
	 */
	public static function find_file($dir, $file, $ext = NULL, $array = FALSE)
	{
		$ext = ($ext === NULL)
			? EXT // 默认后缀
			: (
				$ext
				? ".{$ext}" // 在前面补充个点
				: '' // 无后缀
			);

		// Media目录中的文件，可以在文件名和文件夹名中使用 . 的
		if ($dir != 'Media')
		{
			$file = str_replace('.', DIRECTORY_SEPARATOR, $file);
		}
		// Create a partial path of the filename
		$path = $dir.DIRECTORY_SEPARATOR.$file.$ext;

		if (Kohana::$caching === TRUE AND isset(Kohana::$_files[$path.($array ? '_array' : '_path')]))
		{
			// This path has been cached
			return Kohana::$_files[$path.($array ? '_array' : '_path')];
		}

		if (Kohana::$profiling === TRUE AND class_exists('Profiler', FALSE))
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Kohana', __FUNCTION__);
		}

		if ($array OR $dir === 'Config' OR $dir === 'I18N' OR $dir === 'Message')
		{
			// Include paths must be searched in reverse
			$paths = array_reverse(Kohana::include_paths());

			// Array of files that have been found
			$found = array();

			foreach ($paths AS $dir)
			{
				if (is_file($dir.$path))
				{
					// This path has a file, add it to the list
					$found[] = $dir.$path;
				}
			}
		}
		else
		{
			// The file has not been found yet
			$found = FALSE;

			foreach (Kohana::include_paths() AS $dir)
			{
				if (is_file($dir.$path))
				{
					// A path has been found
					$found = $dir.$path;

					// Stop searching
					break;
				}
			}
		}

		if (Kohana::$caching === TRUE)
		{
			// Add the path to the cache
			Kohana::$_files[$path.($array ? '_array' : '_path')] = $found;

			// Files have been changed
			Kohana::$_files_changed = TRUE;
		}

		if (isset($benchmark))
		{
			// Stop the benchmark
			Profiler::stop($benchmark);
		}

		return $found;
	}

	/**
	 * 列出指定目录的文件
	 *
	 *     // 查找所有视图文件
	 *     $views = Kohana::list_files('View');
	 *
	 * @param   string  $directory  directory name
	 * @param   array   $paths      list of paths to search
	 * @return  array
	 */
	public static function list_files($directory = NULL, array $paths = NULL)
	{
		//static $i = 0;
		static $skip_dirs = array();
		
		$directory = str_replace('.', DIRECTORY_SEPARATOR, $directory);
		if ($directory !== NULL)
		{
			// Add the directory separator
			$directory .= DIRECTORY_SEPARATOR;
		}

		if ($paths === NULL)
		{
			// 使用默认查找路径
			$paths = Kohana::include_paths();
		}

		// 保存所有查找到的文件
		$found = array();

		foreach ($paths AS $path)
		{
			// 如果存在这个目录
			if ( ! in_array($path.$directory, $skip_dirs) AND is_dir($path.$directory))
			{
				$skip_dirs[] = $path.$directory;
				// 创建一个迭代器
				$dir = new DirectoryIterator($path.$directory);

				foreach ($dir AS $file)
				{
					// Get the file name
					$filename = $file->getFilename();

					if ($filename[0] === '.' OR $filename[strlen($filename)-1] === '~')
					{
						// Skip all hidden files and UNIX backup files
						continue;
					}

					// Relative filename is the array key
					$key = $directory.$filename;

					if ($file->isDir())
					{
						//echo '<strong>'.$path.'</strong>' . $key . '<br />';
						//$i++;
						//if ($i >= 200)
						//{
						//	exit;
						//}
						if ($sub_dir = Kohana::list_files($key, $paths))
						{
							if (isset($found[$key]))
							{
								// Append the sub-directory list
								$found[$key] += $sub_dir;
							}
							else
							{
								// Create a new sub-directory list
								$found[$key] = $sub_dir;
							}
						}
					}
					else
					{
						if ( ! isset($found[$key]))
						{
							// Add new files to the list
							$found[$key] = realpath($file->getPathName());
						}
					}
				}
			}
		}

		// Sort the results alphabetically
		ksort($found);

		return $found;
	}

	/**
	 * 加载指定的文件，返回文件的返回内容。如：
	 *
	 *     $foo = Kohana::load('foo.php');
	 *
	 * @param   string  $file  文件名
	 * @return  mixed
	 */
	public static function load($file)
	{
		return include $file;
	}
	
	/**
	 * 覆盖原来的cache方法
	 */
	public static function cache($name, $data = NULL, $lifetime = NULL)
	{
		if (IN_SAE)
		{
			return Kohana::_sae_cache($name, $data, $lifetime);
		}
		else
		{
			return Kohana::_internal_cache($name, $data, $lifetime);
		}
	}
	
	public static function _sae_cache($name, $data = NULL, $lifetime = NULL)
	{
		if (Kohana::$_sae_internal_cache === 1)
		{
			return Kohana::_sae_mc_cache($name, $data, $lifetime);
		}
		elseif (Kohana::$_sae_internal_cache === 2)
		{
			return Kohana::_sae_kv_cache($name, $data, $lifetime);
		}
		else
		{
			throw new Kohana_Exception('Unknown SAE cache type.');
		}
	}
	
	public static function _sae_kv_cache($name, $data = NULL, $lifetime = NULL)
	{
	}
	
	/**
	 * 为SAE设置的内置缓存，使用Memcache来实现
	 */
	public static function _sae_mc_cache($name, $data = NULL, $lifetime = NULL)
	{
		//return Kohana::_internal_cache($name, $data, $lifetime);
		// SAE资源丰富啊，直接用Memcache吧，速度够快
		
		// 不要调用Kohana自带的那些类，会占用部分查找文件的时间
		if ( ! Kohana::$_sae_memcache)
		{
			Kohana::$_sae_memcache = memcache_init();
		}
		
		// data为空，就是在获取获取数据咯
		if ($data === NULL)
		{
			try
			{
				return memcache_get(Kohana::$_sae_memcache, $name);
			}
			catch (Exception $e)
			{
				return NULL;
			}
		}
		else
		{
			// 获取生命周期
			$lifetime = ($lifetime === NULL) ? Kohana::$cache_life : (int) $lifetime;
			//return (bool) $memcache->set($name, $data, $lifetime);
			return (bool) memcache_set(Kohana::$_sae_memcache, $name, $data, 0, $lifetime);
		}
	}

	/**
	 * Provides simple file-based caching for strings and arrays:
	 *
	 *     // Set the "foo" cache
	 *     Kohana::cache('foo', 'hello, world');
	 *
	 *     // Get the "foo" cache
	 *     $foo = Kohana::cache('foo');
	 *
	 * All caches are stored as PHP code, generated with [var_export][ref-var].
	 * Caching objects may not work as expected. Storing references or an
	 * object or array that has recursion will cause an E_FATAL.
	 *
	 * The cache directory and default cache lifetime is set by [Kohana::init]
	 *
	 * [ref-var]: http://php.net/var_export
	 *
	 * @param   string  $name       name of the cache
	 * @param   mixed   $data       data to cache
	 * @param   integer $lifetime   number of seconds the cache is valid for
	 * @return  mixed    for getting
	 * @return  boolean  for setting
	 */
	public static function _internal_cache($name, $data = NULL, $lifetime = NULL)
	{
		// Cache file is a hash of the name
		$file = sha1($name).'.txt';

		// Cache directories are split by keys to prevent filesystem overload
		$dir = Kohana::$cache_dir.DIRECTORY_SEPARATOR.$file[0].$file[1].DIRECTORY_SEPARATOR;

		if ($lifetime === NULL)
		{
			// Use the default lifetime
			$lifetime = Kohana::$cache_life;
		}

		if ($data === NULL)
		{
			if (is_file($dir.$file))
			{
				if ((time() - filemtime($dir.$file)) < $lifetime)
				{
					// Return the cache
					try
					{
						return unserialize(file_get_contents($dir.$file));
					}
					catch (Exception $e)
					{
						// Cache is corrupt, let return happen normally.
					}
				}
				else
				{
					try
					{
						// Cache has expired
						unlink($dir.$file);
					}
					catch (Exception $e)
					{
						// Cache has mostly likely already been deleted,
						// let return happen normally.
					}
				}
			}

			// Cache not found
			return NULL;
		}

		if ( ! is_dir($dir))
		{
			// Create the cache directory
			mkdir($dir, 0777, TRUE);

			// Set permissions (must be manually set to fix umask issues)
			chmod($dir, 0777);
		}

		// Force the data to be a string
		$data = serialize($data);

		try
		{
			// 写入缓存
			return (bool) file_put_contents($dir.$file, $data, LOCK_EX);
		}
		catch (Exception $e)
		{
			// 写缓存失败，不抛出异常，返回FALSE就行了
			return FALSE;
		}
	}

	/**
	 * Get a message from a file. Messages are arbitary strings that are stored
	 * in the `messages/` directory and reference by a key. Translation is not
	 * performed on the returned values.  See [message files](kohana/files/messages)
	 * for more information.
	 *
	 *     // Get "username" from messages/text.php
	 *     $username = Kohana::message('text', 'username');
	 *
	 * @param   string  $file       file name
	 * @param   string  $path       key path to get
	 * @param   mixed   $default    default value if the path does not exist
	 * @return  string  message string for the given path
	 * @return  array   complete message list, when no path is specified
	 */
	public static function message($file, $path = NULL, $default = NULL)
	{
		static $messages;

		if ( ! isset($messages[$file]))
		{
			// Create a new message list
			$messages[$file] = array();

			if ($files = Kohana::find_file('Message', $file))
			{
				foreach ($files AS $f)
				{
					// Combine all the messages recursively
					$messages[$file] = Helper_Array::merge($messages[$file], Kohana::load($f));
				}
			}
		}

		return ($path === NULL)
			? $messages[$file] // 返回指定的所有信息
			: Helper_Array::path($messages[$file], $path, $default); // Get a message using the path
	}

	/**
	 * PHP error handler, converts all errors into ErrorExceptions. This handler
	 * respects error_reporting settings.
	 *
	 * @return  TRUE
	 */
	public static function error_handler($code, $error, $file = NULL, $line = NULL)
	{
		if (error_reporting() & $code)
		{
			// This error is not suppressed by current error reporting settings
			// Convert the error into an ErrorException
			throw new ErrorException($error, $code, 0, $file, $line);
		}

		// Do not execute the PHP error handler
		return TRUE;
	}

	/**
	 * Catches errors that are not caught by the error handler, such as E_PARSE.
	 *
	 * @return  void
	 */
	public static function shutdown_handler()
	{
		if ( ! Kohana::$_init)
		{
			// Do not execute when not active
			return;
		}

		try
		{
			if (Kohana::$caching === TRUE AND Kohana::$_files_changed === TRUE)
			{
				// Write the file path cache
				Kohana::cache('Kohana::find_file()', Kohana::$_files);
			}
		}
		catch (Exception $e)
		{
			// Pass the exception to the handler
			Kohana_Exception::handler($e);
		}

		if (Kohana::$errors AND $error = error_get_last() AND in_array($error['type'], Kohana::$shutdown_errors))
		{
			// 清空已输出的缓存
			ob_get_level() AND ob_clean();

			// Fake an exception for nice debugging
			Kohana_Exception::handler(new ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));

			// Shutdown now to avoid a "death loop"
			exit(1);
		}
	}

	/**
	 * 返回当前运行的系统版本
	 * 
	 * @return string
	 */
	public static function version()
	{
		return 'Kohana Framework '.Kohana::VERSION.' ('.Kohana::CODENAME.')';
	}

} // End Kohana
