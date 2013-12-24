<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 微博设置模型
 *
 * @package		Kohana/Weibo
 * @category	Model
 * @author		YwiSax
 */
class Kohana_Model_Weibo_Setting extends Model_Weibo {

	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);

	protected $_primary_key = 'key';
	
	public static function save_client($token)
	{
		// 令牌
		if (isset($token['access_token']))
		{
			$setting = Model::factory('Weibo.Setting')
				->where('key', '=', 'weibo_client_token')
				->find();
			$setting->val = $token['access_token'];
			$setting->save();
		}
		
		// 过期时间
		if (isset($token['expires_in']))
		{
			$setting = Model::factory('Weibo.Setting')
				->where('key', '=', 'weibo_client_expired')
				->find();
			$setting->val = $token['expires_in'];
			$setting->save();
			
			// 有过期时间，同时插入更新时间
			$setting = Model::factory('Weibo.Setting')
				->where('key', '=', 'weibo_client_created')
				->find();
			$setting->val = time();
			$setting->save();
		}
		
		// 保存记录
		XunSec::log(
			Weibo::LOG_TYPE,
			0,
			'',
			__('Reconfigurate the client setting')
		);
	}
}
