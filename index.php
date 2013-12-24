<?php
/**
 * 首页文件，框架和应用入口
 *
 * 主要功能：
 *	1. 判断当前执行应用
 *	2. 定义系统路径常量
 *	3. 加载和执行核心文件
 */

/**
 * 设置PHP报错级别
 *
 * 正在开发中的项目，建议设置为：E_ALL | E_STRICT
 *
 * 正在运营中的项目，建议设置为：E_ALL ^ E_NOTICE
 *
 * 如果你的PHP版本 >= 5.3，那么建议你使用：E_ALL & ~ E_DEPRECATED
 */
error_reporting(E_ALL | E_STRICT);

// 网站根目录
defined('WEB_PATH') OR define('WEB_PATH', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

// 应用目录
defined('APP_PATH') OR define('APP_PATH', WEB_PATH.'application'.DIRECTORY_SEPARATOR);

// 附加模块目录
defined('MOD_PATH') OR define('MOD_PATH', WEB_PATH.'modules'.DIRECTORY_SEPARATOR);

// 系统目录
defined('SYS_PATH') OR define('SYS_PATH', WEB_PATH.'framework'.DIRECTORY_SEPARATOR);

// 公用目录
defined('PUB_PATH') OR define('PUB_PATH', WEB_PATH.'media'.DIRECTORY_SEPARATOR); 

// 加载核心初始化文件
require SYS_PATH.'Init.php';

