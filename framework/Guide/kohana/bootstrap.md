# Bootstrap

bootstrap文件位置为`APP_PATH/Init.php`。
bootstrap主要是用来进行Kohana环境的初始化和执行系统主请求。
具体的加载在`index.php`中实现（更多可查看[请求流程](flow)章节）。

[!!] bootstrap主要是用来提供一个接口给应用来自定义。在Kohana以前的一些版本，`bootstrap.php`是放在`system`目录的，而且不可编辑。从Kohana 3开始，`bootstrap.php`开始担当一个更加灵活的角色，你可以随心所欲地编辑你的`bootsterap.php`，没必要担心什么。

## 环境搭建

bootstrap首先设置时区和区域，然后激活Kohana的自动加载器，这样才能让[级联文件系统](files)工作。
你也可以添加在此时添加其他任何设置。

~~~
// 下来是bootstrap.php中的部分代码，带注释。

// 设置默认时区
date_default_timezone_set('America/Chicago');

// 设置默认区域
setlocale(LC_ALL, 'en_US.utf-8');

// 激活Kohana自动加载器
spl_autoload_register(array('Kohana', 'auto_load'));

// 激活反序列化自动加载器
ini_set('unserialize_callback_func', 'spl_autoload_call');
~~~

## 初始化和配置

接下来，Kohana就会通过调用[Kohana::init]来完成框架初始化，同时激活Log和[配置](files/config)读/写器。

~~~
// 下来是bootstrap.php中的部分代码，带注释。

Kohana::init(array('
    base_url' => '/kohana/',
	index_file => false,
));

// 连接Log文件写操作器，支持多个写操作器。
Kohana::$log->attach(new Kohana_Log_File(APP_PATH.'Log'));

// Attach a file reader to config. Multiple readers are supported.
Kohana::$config->attach(new Kohana_Config_File);
~~~

You can add conditional statements to make the bootstrap have different values based on certain settings.  For example, detect whether we are live by checking `$_SERVER['HTTP_HOST']` and set caching, profiling, etc. accordingly.  This is just an example, there are many different ways to accomplish the same thing.

~~~
// Excerpt from http://github.com/isaiahdw/kohanaphp.com/blob/f2afe8e28b/application/bootstrap.php
... [trimmed]

/**
 * 根据域名来设置当前系统变量
 */
if (strpos($_SERVER['HTTP_HOST'], 'kohanaphp.com') !== FALSE)
{
	// We are live!
	Kohana::$environment = Kohana::PRODUCTION;

	// Turn off notices and strict errors
	error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
}

/**
 * Initialize Kohana, setting the default options.
 ... [trimmed]
 */
Kohana::init(array(
	'base_url'   => Kohana::$environment === Kohana::PRODUCTION ? '/' : '/kohanaphp.com/',
	'caching'    => Kohana::$environment === Kohana::PRODUCTION,
	'profile'    => Kohana::$environment !== Kohana::PRODUCTION,
	'index_file' => FALSE,
));

... [trimmed]

~~~

[!!] Note: The default bootstrap will set `Kohana::$environment = $_ENV['KOHANA_ENV']` if set. Docs on how to supply this variable are available in your web server's documentation (e.g. [Apache](http://httpd.apache.org/docs/1.3/mod/mod_env.html#setenv), [Lighttpd](http://redmine.lighttpd.net/wiki/1/Docs:ModSetEnv#Options)). This is considered better practice than many alternative methods to set `Kohana::$enviroment`, as you can change the setting per server, without having to rely on config options or hostnames.

## 模块

**阅读[模块页面](modules)可以获得更多描述。**

[Modules](modules) are then loaded using [Kohana::module()].  Including modules is optional.

Each key in the array should be the name of the module, and the value is the path to the module, either relative or absolute.
~~~
// Example excerpt from bootstrap.php

Kohana::module(array(
	'Database'	=> MOD_PATH.'Database',
	'ORM'		=> MOD_PATH.'ORM',
	'Guide'		=> MOD_PATH.'Guide',
));
~~~

## 路由

**Read the [Routing](routing) page for a more detailed description and more examples.**

[Routes](routing) are then defined via [Route::set()].

~~~
// The default route that comes with Kohana 3
Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'Welcome',
		'action'     => 'index',
	));
~~~
