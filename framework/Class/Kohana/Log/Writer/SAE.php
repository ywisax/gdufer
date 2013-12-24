<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * SAE记录器，使用SAE的sae_debug函数来记录。
 *
 * @package    Kohana
 * @category   Logging
 */
class Kohana_Log_Writer_SAE extends Log_Writer {

	/**
	 * 构造函数，顺带检查下是否在SAE中
	 *
	 * @param   string  $directory  log directory
	 * @return  void
	 */
	public function __construct()
	{
		// 如果不在SAE中
		if ( ! IN_SAE)
		{
			throw new Kohana_Exception('This class is depend on SAE environment.');
		}
	}
	
	const MESSAGE_REGEX = '@(\w+)\=([^;]*)@e';

	/**
	 * 使用sae_debug()记录信息
	 *
	 *     $writer->write($messages);
	 *
	 * @param   array   $messages
	 * @return  void
	 */
	public function write(array $messages)
	{
		static $is_debug = NULL;
		if (is_null($is_debug))
		{
			preg_replace(Log_SAE::MESSAGE_REGEX, '$appSettings[\'\\1\']="\\2";', $_SERVER['HTTP_APPCOOKIE']);
			// 上面有个e通配符，有没有可能带来安全隐含？
			$is_debug = in_array($_SERVER['HTTP_APPVERSION'], explode(',', $appSettings['debug'])) ? TRUE : FALSE;
		}
		if ($is_debug)
		{
			sae_set_display_errors(FALSE);
		}

		foreach ($messages AS $message)
		{
			// 使用sae_debug保存
			sae_debug($this->format_message($message));
		}

		if ($is_debug)
		{
			sae_set_display_errors(TRUE);
		}
	}

} // End Kohana_Log_SAE
