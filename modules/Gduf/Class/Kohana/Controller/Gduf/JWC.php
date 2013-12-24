<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 广金教务处
 *
 * @package		Kohana/Gduf
 * @category	JWC
 */
class Kohana_Controller_Gduf_JWC extends Controller_Gduf {

	/**
	 * 教务处首页，默认是课程表啦
	 */
	public function action_index()
	{
		// 需要先登陆
		if (Auth::instance()->logged_in())
		{
			$jwc_user = Model::factory('Gduf.JWC.User')
				->where('owner_id', '=', Auth::instance()->get_user()->id)
				->find();
			// 如果数据库还没有记录，那么需要跳转到绑定页面
			if ( ! $jwc_user->loaded())
			{
				HTTP::redirect( Route::url('gduf-jwc-action', array('action' => 'login')) );
			}
			
			// 如果还没有课程表，那就获取一次
			if ( ! $jwc_user->schedule)
			{
				$this->action_fetch();
			}

			$content = View::factory('Gduf.JWC.Schedule', array(
				'user' => $jwc_user,
			));
		}
		else
		{
			$content = View::factory('Gduf.JWC.NeedLogin');
		}
	
		$this->render(array(
			'title' => '课程表',
			'metadesc' => 'desc',
			'metakw' => 'kw',
			'content' => $content,
		));
	}

	/**
	 * 登陆到教务处并获取课程表
	 */
	public function action_login()
	{
		if ( ! Auth::instance()->logged_in())
		{
			$this->render(array(
				'title' => '课程表',
				'metadesc' => 'desc',
				'metakw' => 'kw',
				'content' => View::factory('Gduf.JWC.NeedLogin'),
			));
			return;
		}
	
		$errors = array();
		if ($this->request->is_post())
		{
			// 先检查验证码是否为空
			$captcha_code = $this->request->post('captcha');
			if ( ! $captcha_code)
			{
				$errors = array(
					'captcha' => __('Captcha code must not be empty'),
				);
			}
			else
			{
				$this->request->post('owner_id', Auth::instance()->get_user()->id);
			
				try
				{
					$jwc_user = Model::factory('Gduf.JWC.User')
						->values($this->request->post())
						->save();
					// 登陆
					if ($jwc_user->login())
					{
						// 获取课程表
						$jwc_user->fetch_schedule();
						HTTP::redirect( Route::url('gduf-jwc') );
					}
					else
					{
						$errors = array(
							'username' => '用户名或密码错误',
							'password' => '用户名或密码错误',
						);
					}
				}
				catch (ORM_Validation_Exception $e)
				{
					$errors = $e->errors();
				}
			}
		}

		// 那么就输出查找页面
		$this->render(array(
			'title' => '课程表',
			'metadesc' => 'desc',
			'metakw' => 'kw',
			'content' => View::factory('Gduf.JWC.Login', array(
				'errors' => $errors,
				'post' => $this->request->post(),
			)),
		));
	}
	
	/**
	 * 获取课程表
	 */
	public function action_fetch()
	{
		// 先不考虑登陆用户的问题
		// 先检查数据库中是否有跟当前用户对应的记录了
		$jwc_user = Model::factory('Gduf.JWC.User')
			->where('owner_id', '=', Auth::instance()->get_user()->id)
			->find();
			
		// 要限制次数
		if ( ! $jwc_user->second_limit())
		{
			$this->response->body('更新太频繁了，请稍后重试。' . $jwc_user->schedule);
			return;
		}

		// 如果数据库中没查找到
		if ( ! $jwc_user->loaded())
		{
			HTTP::redirect( Route::url('gduf-jwc-action', array('action' => 'login')) );
		}
		
		// 要限制次数才行，要不不断尝试的话，就成ddos了。。

		// 登陆到教务处
		if ($jwc_user->login())
		{
			// 重新获取课程表
			$jwc_user->fetch_schedule();
			$this->response->body($jwc_user->schedule);
			return;
		}
		else
		{
			HTTP::redirect( Route::url('gduf-jwc-action', array('action' => 'login')) );
		}
	}

} // End Kohana_Controller_Gduf_JWC
