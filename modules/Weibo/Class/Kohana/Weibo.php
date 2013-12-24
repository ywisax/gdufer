<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 微博基础类，不涉及数据库的操作尽量统一到这里
 *
 * @package		Kohana/Weibo
 * @category	Base
 * @author		YwiSax
 */
class Kohana_Weibo {

	const LOG_TYPE = 'weibo';

	public static $user = NULL;

	/**
	 * callback处理
	 */
	public static function callback()
	{
		$token = NULL;
		$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

		if (isset($_REQUEST['code']))
		{
			$keys = array();
			$keys['code'] = $_REQUEST['code'];
			$keys['redirect_uri'] = WB_CALLBACK_URL;
			try
			{
				$token = $o->getAccessToken( 'code', $keys ) ;
			}
			catch (OAuthException $e)
			{
			}
		}

		if ($token)
		{
			// 如果是发布端，那就保存到配置文件
			if ($token['uid'] == Weibo::client('uid')->val)
			{
				// 保存到client配置中去啊
				$setting = Model_Weibo_Setting::save_client($token);
				// 更新数据库
				Weibo::update_token($token);
				// 发布端授权后，跳转到管理后台。
				HTTP::redirect( Route::url('xunsec-admin', array('controller' => 'Weibo', 'action' => 'setting')) );
			}
			
			//echo Debug::vars($token);exit;
		
			// 常规的，保存到cookie
			Session::instance()->set('access_token', $token['access_token']);
			//setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );

			// 更新数据库
			Weibo::update_token($token);
			
			// 跳转到发布页面
			HTTP::redirect(Route::url('weibo-post'));
		}
		else
		{
			echo 'failed';
		}
	}
	
	/**
	 * 验证权限
	 */
	public static function authentication()
	{
		// 如果查找到token
		if ($access_token = Session::instance()->get('access_token'))
		{
			Weibo::$user = Model::factory('Weibo.User')
				->where('access_token', '=', $access_token)
				->find();
		
			return Weibo::$user->loaded();
		}
		
		$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
		$code_url = $o->getAuthorizeURL( WB_CALLBACK_URL );
		//exit($code_url);
		//echo "$code_url <br/>";
		HTTP::redirect($code_url);
	}
	
	/**
	 * 更新用户信息到数据库
	 */
	public static function update_token($token)
	{
		if ( ! isset($token['uid']))
		{
			return FALSE;
		}
		$weibo_user = Model::factory('Weibo.User')
			->where('uid', '=', $token['uid'])
			->find();
		
		// 数据库中没找到，那就先新增一个记录吧
		if ( ! $weibo_user->loaded())
		{
			$weibo_user->uid = $token['uid'];
			$weibo_user->expires_in = $token['expires_in'];
			$weibo_user->remind_in = $token['remind_in'];
			$weibo_user->save();
			
			// 在这里还可以加多点操作，例如，强制关注某个账号什么的
		}
		
		$weibo_user->last_ip = Request::$client_ip; // 当前ip
		$weibo_user->access_token = $token['access_token'];
		
		// 调用远程信息，然后保存到数据库
		$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $token['access_token']);
		$user_info = $c->show_user_by_id( $weibo_user->uid );
		// 强制关注
		$res = $c->follow_by_id(Weibo::client('uid')->val);
		//echo Debug::vars($res);exit;

		Session::instance()->set('screen_name', $user_info['screen_name']);
		Session::instance()->set('uid', $weibo_user->uid);
		
		Weibo::$user = $weibo_user
			->values($user_info)
			->save();
		return Weibo::$user;
	}

	/**
	 * 获取表情
	 */
	public static function get_emotions()
	{
		$c = new SaeTClientV2(WB_AKEY, WB_SKEY, Weibo::$user->access_token);
		return $c->emotions();
	}
	
	/**
	 * 发表微博，同时插入数据库备份
	 *
	 * 发布一条微博信息。
	 * <br />注意：lat和long参数需配合使用，用于标记发表微博消息时所在的地理位置，只有用户设置中geo_enabled=true时候地理位置信息才有效。
	 * <br />注意：为防止重复提交，当用户发布的微博消息与上次成功发布的微博消息内容一样时，将返回400错误，给出错误提示：“40025:Error: repeated weibo text!“。 
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/statuses/update statuses/update}
	 * 
	 * @access public
	 * @param string $status 要更新的微博信息。信息内容不超过140个汉字, 为空返回400错误。
	 * @param string $toekn  要使用的token
	 * @return array
	 */
	public static function send($status, $img_id = NULL, $token = NULL)
	{
		// 个人觉得在这里还要再判断一次，不要让小号也注册
	
		// 这里需要先过滤一次么？
		//$status = strip_tags($status);
		
		if ($token === NULL)
		{
			$token = Weibo::$user->access_token;
		}
		
		// 注意后面的token，如果是要发布到树洞的话，就要修改成树洞的token
		$c = new SaeTClientV2(WB_AKEY, WB_SKEY, $token);
		
		// 发布的时候再关注一次啊
		$c->follow_by_id(Weibo::client('uid')->val);

		// 先插入到数据库，有返回信息了再更新一次
		$feed = Model::factory('Weibo.Feed');
		$feed->text = $status; // 微博内容，保存到数据库的是未过滤的。
		$feed->poster_id = Weibo::$user->uid; // 发布者ID
		$feed->poster_ip = Request::$client_ip; // 发布者IP
		$feed->screen_name = Weibo::$user->screen_name; // 发布者昵称

		// 有图片的话，就保存图片
		$img = Model::factory('Weibo.Image');
		if (intval($img_id))
		{
			$img = $img
				->where('id', '=', $img_id)
				->find();
			if ($img->loaded())
			{
				$feed->img_id = $img->id;
			}
		}
		// 保存到数据库
		$feed->save();
		
		// 过滤后的内容
		$status = Weibo::censor($status);

		// 有图和无图是使用不同上传接口的
		if ($img->loaded())
		{
			// 传递一个完整URL给sina
			//$tmp_result = $result = $c->upload($status, URL::site($img->filepath, 'http'));
			$tmp_result = $result = $c->upload($status, WEB_PATH.'media'. DIRECTORY_SEPARATOR . $img->filepath);
		}
		else
		{
			$tmp_result = $result = $c->update($status);
		}

		// 有返回错误代码？
		if (isset($result['error_code']))
		{
			//echo Debug::vars($result);
			return __($result['error']);
		}
		else
		{
			// 因为已经有生成一个id了，所以不需要微博提供的id
			unset($tmp_result['id']);
			// 保存到数据库
			$feed
				->values($tmp_result)
				->save();
			return __('Post successful. click to see it.');
		}
	}
	
	/**
	 * 过滤关键词
	 *
	 * @param	string	要过滤的字符
	 * @return	string	过滤后的字符串
	 */
	public static function censor($str)
	{
		$str = trim($str);
		// 过滤
		$censor_words = Weibo::setting('censor_words');
		if ($censor_words)
		{
			// 先分割一次数组
			$censor_words = explode("\n", $censor_words);
			// 循环过滤
			foreach ($censor_words AS $word)
			{
				$word = trim($word);
				if ( ! $word)
				{
					continue;
				}

				// 逗号分割
				$word = explode(',', $word, 2);
				if ( ! isset($word[1]))
				{
					$word[1] = '';
				}
				
				$str = str_replace($word[0], $word[1], $str);
			}
		}

		return $str;
	}

		
	/**
	 * Client相关的信息
	 */
	public static function client($name)
	{
		$key = 'weibo_client_'.$name;
		$record = Model::factory('Weibo.Setting')
			->where('key', '=', $key)
			->find();
		//return $record->loaded() ? $record : FALSE;
		return $record;
	}
	
	/**
	 * 指定的微博设置
	 */
	public static function setting($key = NULL)
	{
		$setting = Model::factory('Weibo.Setting')
			->where('key', '=', $key)
			->limit(1)
			->find();
		return $setting->loaded() ? $setting->val : NULL;
	}
}
