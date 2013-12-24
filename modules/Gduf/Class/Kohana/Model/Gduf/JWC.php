<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 教务处基础模型
 *
 * @package		Kohana/Gduf
 * @category	Model
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Model_Gduf_JWC extends ORM {

	const JWC_URL = 'http://jwc.gduf.edu.cn';

	// 这个参数很重要。。正方居然是靠这个来验证用户状态的
	public static $state_param = NULL;

	public static $xs_main_url = NULL;
	
	/**
	 * 获取当前回话的state参数
	 */
	public static function state_param()
	{
		if (Model_Gduf_JWC::$state_param === NULL)
		{
			$request = Request::factory(Model_Gduf_JWC::JWC_URL)->execute();

			$append_sign = $request->headers('location'); // 登录地址
			$append_url = str_replace('default2.aspx', '', $append_sign);
			
			Model_Gduf_JWC::$state_param = $append_url;
		}
		
		return Model_Gduf_JWC::$state_param;
	}
	
	/**
	 * 获取带state_param的URL
	 */
	public static function url($file = NULL)
	{
		$url = Model_Gduf_JWC::JWC_URL . Model_Gduf_JWC::state_param() . $file;
		return $url;
	}

	/**
	 * 解析课程表table
	 */
	public function schedule_array()
	{
		$table = $this->schedule;
		$table = preg_replace('/<table[^>]*?>/is', '', $table);
		$table = preg_replace('/<tr[^>]*?>/si', '', $table);
		$table = preg_replace('/<td[^>]*?>/si', '', $table);
		$table = str_replace('</tr>', '{tr}', $table);
		$table = str_replace('</td>', '{td}', $table);
		$table = str_replace('&nbsp;', '', $table);
		//去掉 HTML 标记
		$table = preg_replace("'<[/!]*?[^<>]*?>'si", '', $table);
		//去掉空白字符
		$table = preg_replace("'([rn])[s]+'", '', $table);
		$table = str_replace(" ", '', $table);
		$table = str_replace(" ", '', $table);

		$table = explode('{tr}', $table);
		array_pop($table);
		foreach ($table AS $key=>$tr)
		{
			$td = explode('{td}', $tr);
			$td = explode('{td}', $tr);
			array_pop($td);
			$td_array[] = $td;
		}
		
		return Debug::vars($td_array);
		return $td_array;
	}
}

