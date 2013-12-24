<?php defined('SYS_PATH') or die('No direct script access.');

I18N::lang('zh-cn');

// 运行环境配置
if ($_SERVER['HTTP_HOST'] == 'dev.gdufer.com')
{
	// 开发环境
	Kohana::$environment = Kohana::DEVELOPMENT;
}
elseif ($_SERVER['HTTP_HOST'] == 'test.gdufer.com')
{
	// 测试环境
	Kohana::$environment = Kohana::DEVELOPMENT;
}
elseif ($_SERVER['HTTP_HOST'] == 'gdufer.sinaapp.com')
{
	// 测试环境
	Kohana::$environment = Kohana::DEVELOPMENT;
}
elseif ($_SERVER['HTTP_HOST'] == 'bls.gdufer.com')
{
	// 测试环境
	Kohana::$environment = Kohana::DEVELOPMENT;
}
else
{
	// 发布环境啊
	Kohana::$environment = Kohana::PRODUCTION;
	// 发布环境关闭错误提示
	error_reporting(0);
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APP_PATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
Kohana::init(array(
	'base_url'   => '/',
	'index_file' => FALSE,
	'cache_life' => 300,
	'profile'	 => FALSE,
));

/**
 * 加载模块
 */
Kohana::module(array(
	'Forum',
	'Guide',
	'Contact',
	'Weibo',
	'Gduf',
	'Information',
	'XunSec', // 一般来说，把这个模块放在最后，防止其中的'default'路由被覆盖
));

Helper_Cookie::$salt = 'x$nS1c!@xxPo?';

// SAE环境中，上面的File类型记录器会失效，所以要另外添加一个记录器。
if (IN_SAE)
{
	Kohana::$log->attach(Log_Writer::factory('SAE'));
}

/**
 * 执行主请求
 */
echo Request::factory()
	->execute()
	->send_headers(TRUE)
	->body();

