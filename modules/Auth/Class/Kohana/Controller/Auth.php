<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 用户控制器
 *
 * @package		Kohana/Auth
 * @category	Controller
 */
class Kohana_Controller_Auth extends Controller_XunSec {

	public $layout = 'blank';
	
	/**
	 * 查看用户
	 */
	public function action_view()
	{
		$id = $this->request->param('id');
		
		HTTP::redirect('/');
		
		$this->render(array(
			'title' => __('User Login'),
			'content' => View::factory('Auth.Login', array(
				'username' => $this->request->post('username'),
				'redir' => $redirect,
				'errors' => $errors,
			)),
		));
	}
	
	/**
	 * 登录页面
	 */
	public function action_login()
	{
		if (Auth::instance()->logged_in())
		{
			HTTP::redirect($this->request->referrer());
		}

		$errors = array();
		if ($this->request->referrer())
		{
			$redirect = $this->request->referrer();
		}
		else
		{
			$redirect = URL::site();
		}

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
				// 检查验证码
				if ( ! Captcha::valid($captcha_code))
				{
					$errors = array(
						'captcha' => __('Invalid Captcha code. Do you pass the verify or typo?'),
					);
				}
				else
				{
					if ( ! $this->request->post('username'))
					{
						$errors['username'] = __('Login must not be empty');
					}
					elseif ( ! $this->request->post('password'))
					{
						$errors['password'] = __('Password must not be empty');
					}
					else
					{
						$remember = (bool) $this->request->post('remember');
					
						$login_status = Auth::instance()->login($this->request->post('username'), $this->request->post('password'), $remember);

						if ($login_status)
						{
							$disable_redirect = array
							(
								'auth', 'register', 'login', 'logout', 'invate',
								'lostpassword', 'changepassword', 'verity'
							);
							
							// 如果在表单中要求重定向
							if ($this->request->post('redir'))
							{
								$redirect = $this->request->post('redir');
							}
							
							foreach ($disable_redirect AS $key)
							{
								if (strpos($redirect, $key) !== FALSE)
								{
									$redirect = URL::site();
									break;
								}
							}
							HTTP::redirect($redirect);
						}
						else
						{
							$errors['username'] = $errors['password'] = __('Incorrect Login or Password');
						}
					}
				}
			}
		}

		
		if ($this->request->is_ajax() OR isset($_GET['test_ajax']))
		{
			$this->auto_render = FALSE;
			$this->response->body(View::factory('Auth.Ajax.Login', array(
				'redir' => $this->request->referrer(),
			)));
		}
		else
		{
			$this->render(array(
				'title' => __('User Login'),
				'content' => View::factory('Auth.Login', array(
					'username' => $this->request->post('username'),
					'redir' => $redirect,
					'errors' => $errors,
				)),
			));
		}
	}
	
	/**
	 * 注册页面
	 */
	public function action_register()
	{
		// 如果已经登陆
		if (Auth::instance()->logged_in())
		{
			HTTP::redirect($this->request->referrer());
		}
		
		$redirect = $this->request->referrer();
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
				// 检查验证码
				if ( ! Captcha::valid($captcha_code))
				{
					$errors = array(
						'captcha' => __('Invalid Captcha code. Do you pass the verify or typo?'),
					);
				}
				else
				{
					if ($this->request->post('password') == $this->request->post('password_confirm'))
					{
						try
						{
							$user = Model::factory('User');
							$user->values($this->request->post());
							$user->save();

							// 一些必要的初始化操作
							$user->init_user();

							// 强制登陆该用户
							Auth::instance()->force_login($user->username, TRUE);
							Auth::instance()->auto_login();

							// 设置跳转相关
							$redirect = $this->request->post('redir');
							if ( ! $redirect)
							{
								$redirect = $this->request->query('redir');
							}
							if ( ! $redirect)
							{
								$redirect = $this->request->referrer();
							}
							$disable_redirect = array
							(
								'auth', 'register', 'login', 'logout', 'invate',
								'lostpassword', 'changepassword', 'verity'
							);
							foreach ($disable_redirect AS $key)
							{
								if (strpos($redirect, $key) !== FALSE)
								{
									$redirect = URL::site();
									break;
								}
							}

							$this->render(array(
								'title' => __('Register successful'),
								'content' => View::factory('Auth.Register.Welcome', array(
									'redirect' => URL::site($redirect),
								)),
							));
							return;
						}
						catch (ORM_Validation_Exception $e)
						{
							$errors = $e->errors();
						}
					}
					else
					{
						$errors = array(
							'password' => __('Password must be same'),
							'password_confirm' => __('Password must be same'),
						);
					}
				}
			}
		}
		
		if ($this->request->is_ajax() OR isset($_GET['test_ajax']))
		{
			$this->response->body(View::factory('Auth.Ajax.Register', array(
				'redir' => $this->request->referrer(),
			)));
		}
		else
		{
			$this->render(array(
				'title' => __('User Register'),
				'content' => View::factory('Auth.Register', array(
					'redirect' => URL::site($redirect),
					'post' => $this->request->post(),
					'errors' => $errors,
				)),
			));
		}
	}
	
	/**
	 * 重置账户
	 */
	public function action_reset()
	{
		$this->render(array(
			'title' => __('User Reset'),
			'content' => View::factory('Auth.Reset'),
		));
	}
	
	/**
	 * 注销登录
	 */
	public function action_logout()
	{
		Auth::instance()->logout(TRUE); // 完全注销登录
		HTTP::redirect(Route::url('auth-action', array('action' => 'login')));
	}
	
	/**
	 * 使用ajax来修改头像吧
	 */
	public function action_avatar()
	{
		// 要先登陆
		if ( ! Auth::instance()->logged_in())
		{
			exit('Access denied.');
		}
		// 只允许post
		if ( ! $this->request->is_post())
		{
			exit('Method not allowed.');
		}
		// 文件未上传
		if ( ! isset($_FILES['Filedata']))
		{
			exit('File not uploaded.');
		}

		$image = Auth::instance()->get_user()->new_avatar($_FILES, 'Filedata', 120);

		// 返回一段json、
		$this->response->headers('Content-type', 'text/json');
		$this->response->body( json_encode( $image ? array('image' => Media::url($image, 'http')) : array('image' => '')) );
	}
	
	/**
	 * 用户设置
	 */
	public function action_setting()
	{
		// 未登录访问个毛啊
		if ( ! Auth::instance()->logged_in())
		{
			HTTP::redirect( URL::base() );
		}
		$errors = array();
		$success = FALSE;
		$user = Auth::instance()->get_user();
		
		if ($this->request->is_post())
		{
			try
			{
				if ($this->request->post('password') != $this->request->post('repeat_password'))
				{
					$errors['password'] = $errors['repeat_password'] = __('You need to enter the same password twice.');
				}
				else
				{
					// 如果提交的密码为空，那么无视之
					if ( ! $this->request->post('password'))
					{
						$this->request->post('password', NULL);
					}
				
					$user
						->values($this->request->post())
						->save();
					$success = TRUE;
				}
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = $e->errors();
				$success = FALSE;
			}
		}
		
		// 如果有提交密码，并且成功更新了，那么需要重新登录
		if ($success AND $this->request->post('password'))
		{
			// 注销，重新登录
			$this->action_logout();
		}

		$this->render(array(
			'title' => __('User Setting'),
			'content' => View::factory('Auth.Setting', array(
				'user' => $user,
				'errors' => $errors,
				'success' => $success,
			)),
		));
	}
}
