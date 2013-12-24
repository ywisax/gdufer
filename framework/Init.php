<?php
/**
 * PHP文件名扩展
 */
if ( ! defined('EXT'))
{
	define('EXT', '.php');
}

/**
 * 判断是否在SAE中
 */
if ( ! defined('IN_SAE'))
{
	define('IN_SAE', (bool) function_exists('sae_debug'));
}

/**
 * 记录框架开始前的时间
 */
if ( ! defined('KOHANA_START_TIME'))
{
	define('KOHANA_START_TIME', microtime(TRUE));
}

/**
 * 记录框架开始使用时的内存占用
 */
if ( ! defined('KOHANA_START_MEMORY'))
{
	define('KOHANA_START_MEMORY', memory_get_usage());
}

/**
 * 设置默认时区
 */
if ( ! defined('APP_TIMEZONE'))
{
	define('APP_TIMEZONE', 'Asia/Chongqing');
}
date_default_timezone_set(APP_TIMEZONE); // PRC为“中华人民共和国”

/**
 * 设置本地区域
 */
if ( ! defined('APP_LOCALE'))
{
	define('APP_LOCALE', 'RPC');
}
setlocale(LC_ALL, APP_LOCALE);

/**
 * 加载Kohana核心类
 */
require SYS_PATH.'Class/Kohana/Core'.EXT;
require is_file(APP_PATH.'Class/Kohana'.EXT)
	? APP_PATH.'Class/Kohana'.EXT // 应用程序的扩展
	: SYS_PATH.'Class/Kohana'.EXT; // 加载默认的空扩展

/**
 * 激活Kohana自动加载器.
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * 激活用于反序列化的自动加载器.
 *
 * [!!] 下面的语句在SAE环境中会报错，所以加多了个判断。
 *
 */
if ( ! IN_SAE)
{
	ini_set('unserialize_callback_func', 'spl_autoload_call');
}

/**
 * 通过服务器变量来改变当前环境
 */
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * 设置默认语言
 */
I18N::lang('zh-cn');

// 加载记录器
if ( ! Kohana::$log instanceof Log)
{
	Kohana::$log = Log::instance();
}
Kohana::$log->attach(Log_Writer::factory('File', array(
	'directory' => APP_PATH.'Log', // 默认是附加到这个记录的
)));

// 加载配置器
if ( ! Kohana::$config instanceof Config)
{
	Kohana::$config = new Config;
}
Kohana::$config->attach(Config_Reader::factory('File', array(
	'directory' => 'Config',
)));

// 加载应用自身的初始化文件
require APP_PATH.'Init'.EXT;

/**
 * 默认路由
 */
if ( ! Route::exist('default'))
{
	Route::set('default', '(<controller>(/<action>(/<id>)))')
		->defaults(array(
			'controller' => 'Welcome',
			'action' => 'index',
		));
}

// 如果系统还没有请求
if ( ! Request::initial())
{
	/**
	 * 执行主请求
	 */
	echo Request::factory()
		->execute()
		->send_headers(TRUE)
		->body();
}
