<?php
/**
 * PHP�ļ�����չ
 */
if ( ! defined('EXT'))
{
	define('EXT', '.php');
}

/**
 * �ж��Ƿ���SAE��
 */
if ( ! defined('IN_SAE'))
{
	define('IN_SAE', (bool) function_exists('sae_debug'));
}

/**
 * ��¼��ܿ�ʼǰ��ʱ��
 */
if ( ! defined('KOHANA_START_TIME'))
{
	define('KOHANA_START_TIME', microtime(TRUE));
}

/**
 * ��¼��ܿ�ʼʹ��ʱ���ڴ�ռ��
 */
if ( ! defined('KOHANA_START_MEMORY'))
{
	define('KOHANA_START_MEMORY', memory_get_usage());
}

/**
 * ����Ĭ��ʱ��
 */
if ( ! defined('APP_TIMEZONE'))
{
	define('APP_TIMEZONE', 'Asia/Chongqing');
}
date_default_timezone_set(APP_TIMEZONE); // PRCΪ���л����񹲺͹���

/**
 * ���ñ�������
 */
if ( ! defined('APP_LOCALE'))
{
	define('APP_LOCALE', 'RPC');
}
setlocale(LC_ALL, APP_LOCALE);

/**
 * ����Kohana������
 */
require SYS_PATH.'Class/Kohana/Core'.EXT;
require is_file(APP_PATH.'Class/Kohana'.EXT)
	? APP_PATH.'Class/Kohana'.EXT // Ӧ�ó������չ
	: SYS_PATH.'Class/Kohana'.EXT; // ����Ĭ�ϵĿ���չ

/**
 * ����Kohana�Զ�������.
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * �������ڷ����л����Զ�������.
 *
 * [!!] ����������SAE�����лᱨ�����ԼӶ��˸��жϡ�
 *
 */
if ( ! IN_SAE)
{
	ini_set('unserialize_callback_func', 'spl_autoload_call');
}

/**
 * ͨ���������������ı䵱ǰ����
 */
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * ����Ĭ������
 */
I18N::lang('zh-cn');

// ���ؼ�¼��
if ( ! Kohana::$log instanceof Log)
{
	Kohana::$log = Log::instance();
}
Kohana::$log->attach(Log_Writer::factory('File', array(
	'directory' => APP_PATH.'Log', // Ĭ���Ǹ��ӵ������¼��
)));

// ����������
if ( ! Kohana::$config instanceof Config)
{
	Kohana::$config = new Config;
}
Kohana::$config->attach(Config_Reader::factory('File', array(
	'directory' => 'Config',
)));

// ����Ӧ������ĳ�ʼ���ļ�
require APP_PATH.'Init'.EXT;

/**
 * Ĭ��·��
 */
if ( ! Route::exist('default'))
{
	Route::set('default', '(<controller>(/<action>(/<id>)))')
		->defaults(array(
			'controller' => 'Welcome',
			'action' => 'index',
		));
}

// ���ϵͳ��û������
if ( ! Request::initial())
{
	/**
	 * ִ��������
	 */
	echo Request::factory()
		->execute()
		->send_headers(TRUE)
		->body();
}
