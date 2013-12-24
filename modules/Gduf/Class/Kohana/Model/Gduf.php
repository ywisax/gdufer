<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 广金基础模型
 *
 * @package		Kohana/Gduf
 * @category	Model
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Model_Gduf extends ORM {

	const GDUF_LOGIN_FAILED_TEXT1 = "alert('用户名不正确!')";

	public static $JSESSIONID = '';
	
	public static $request = NULL;
	
	/**
	 * 返回当前会话ID
	 */
	public static function session_id()
	{
		if ( ! Model_Gduf::check_login())
		{
			return FALSE;
		}
		return Model_Gduf::$JSESSIONID;
	}
	
	const GDUF_LOGIN_FAILED_TEXT2 = "alert('密码不正确!')";
	
	/**
	 * 登陆用户，返回session_ID
	 */
	public static function login($username, $password)
	{
		$callback = GDUF_DOMAIN . 'checkuser.jsp';

		$request = Request::factory($callback);
		$request->method('POST');
		$request->post(array(
			'username' => $username,
			'password' => $password,
		));
		$request->referrer(GDUF_DOMAIN);
		$response = $request->execute();
		
		// 获取返回的状态码，如果不是200那就是错误了
		$response_status = $response->status();
		if ($response_status != 200)
		{
			Helper_Cookie::delete('JSESSIONID');
			return '';
		}

		// 转换编码，要不会乱码的啊啊啊啊啊
		$response_body = $response->body();
		$response_body = iconv("gb2312", "utf-8//IGNORE", $response_body);

		if (strpos($response_body, Model_Gduf::GDUF_LOGIN_FAILED_TEXT1) OR strpos($response_body, Model_Gduf::GDUF_LOGIN_FAILED_TEXT2))
		{
			return '';
		}
		else
		{
			$JSESSIONID = $response->headers('set-cookie');
			$JSESSIONID = str_replace(array('JSESSIONID=', '; Path=/'), NULL, $JSESSIONID);
			Helper_Cookie::set('JSESSIONID', $JSESSIONID); // 这里保存的JSESSIONID是加密的喔
			return $JSESSIONID;
		}
	}
	
	/**
	 * 检查是否登录
	 */
	public static function check_login($session = NULL)
	{
		if ( ! Model_Gduf::$request)
		{
			Model_Gduf::$request =& Request::$current;
		}
		if ($session === NULL)
		{
			$session = Model_Gduf::$request->cookie('JSESSIONID');
			if ( ! $session)
			{
				$session = Model_Gduf::$request->query('id');
			}
		}
		return Model_Gduf::$JSESSIONID = $session;
	}
	
	/**
	 * 转码
	 * 参考 http://www.kaijia.me/2013/02/iconv-detected-an-illegal-character-in-input-string-solved/
	 */
	public static function convert($str)
	{
		try
		{
			$converted = iconv("GB18030", "UTF-8//IGNORE", $str);
		}
		catch (Exception $e)
		{
			return $str;
		}
		return $converted;
	}
}

