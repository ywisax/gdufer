<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 后台基础控制器
 *
 * @package		XunSec
 * @category	Controller
 * @copyright	YwiSax
 */
class Kohana_Controller_XunSec_Admin extends Controller_Template {

	// 当前已经登陆的用户
	public $user;
	
	/**
	 * @var  View  后台的模板视图
	 */
	public $template = 'XunSec.Admin';

	public $auto_render = TRUE;

	public $requires_login = TRUE;
	
	/**
	 * 后台的前置操作，主要是进行用户的检验等等
	 */
	public function before()
	{
		parent::before();
		// 登陆不需要验证啊，别搞错了
		if ($this->request->action() === 'login')
		{
			$this->requires_login = FALSE;
		}

		if ($this->requires_login)
		{
			// 检查用户登录成功啦
			if ($this->user = Auth::instance()->get_user())
			{
				// 其实最好是直接传个对象进去
				$this->template->user = $this->user->username;
			}
			else
			{
				// 保存无效访问记录
				XunSec::log(
					XunSec::LOG_TYPE,
					0,
					'',
					__('Auth failded, need login')
				);
			
				HTTP::redirect(Route::url('xunsec-login', array('action' => 'login')));
			}
		}
		
		// 其实目前暂时不需要这个切换语言的功能，cry。。。先写了
		if ($this->request->query('lang'))
		{
			$translations = array_keys(Kohana::message('XunSec', 'translations'));

			if (in_array($this->request->query('lang'), $translations))
			{
				// 设置后台语言
				Helper_Cookie::set('admin_language', $this->request->query('lang'), Date::YEAR);
				
				// 记录设置后台语言啦，其实这里有必要吗？
				XunSec::log(
					XunSec::LOG_TYPE,
					$this->user->id,
					$this->user->username,
					__(':username change language', array(
						':username' => $this->user->username,
					))
				);
			}
			// 重新加载后台
			HTTP::redirect(Route::url('xunsec-admin', array('controller' => 'Page')));
		}
		I18N::lang( Helper_Cookie::get('admin_language', Kohana::config('XunSec')->lang) );
	}

	/**
	 * 调用了无效的方法
	 */
	public function __call($method, $args)
	{
		$this->admin_error('Could not find the url you requested.');
	}

	public function admin_error($message)
	{
		$this->before();
		
		// 保存错误记录
		XunSec::log(
			XunSec::LOG_TYPE,
			$this->user->id,
			$this->user->username,
			__('Admin error: :message', array(
				':message' => $message
			))
		);

		$this->template->content = View::factory('XunSec.Admin.Error', array(
			'message' => $message,
		));
	}

	public function after()
	{
		// 如果有必要的话，这里可以加一些操作记录下每一步
		parent::after();
	}

	/**
	 * 登录到后台
	 */
	public function action_login()
	{
		// 如果已经登录了，那就跳转到页面管理页面算了
		if ($this->user)
		{
			HTTP::redirect(Route::url('xunsec-admin', array('controller' => 'Page')));
		}

		$errors = array();
		if ($this->request->is_post())
		{
			// 登录后台前，可能需要先注销掉之前的用户。
			// 这里还要判断下是否有后台权限！
			if (
				Auth::instance()->login($this->request->post('username'), $this->request->post('password'))
				AND
				Auth::instance()->get_user()->has_role('admin')
			)
			{
				$this->user = Auth::instance()->get_user();
				// 登录成功记录
				XunSec::log(
					XunSec::LOG_TYPE,
					$this->user->id,
					$this->user->username,
					__(':username login success', array(
						':username' => $this->user->username,
					))
				);
			
				HTTP::redirect(Route::url('xunsec-admin', array('controller' => 'Page')));
			}
			else
			{
				// 登录失败记录
				XunSec::log(
					XunSec::LOG_TYPE,
					0,
					'',
					__('Login failed (:username / :password)', array(
						':username' => $this->request->post('username'),
						':password' => $this->request->post('password'),
					))
				);
			}
		}
		
		// 重载模板视图，渲染登陆页面
		$this->template = View::factory('XunSec.Login', array(
			'title' => __('Admin Login'),
			'post' => $this->request->post(),
			'errors' => $errors,
		));
	}

	/**
	 * 注销当前登录用户
	 */
	public function action_logout()
	{
		// 注销记录
		XunSec::log(
			XunSec::LOG_TYPE,
			$this->user->id,
			$this->user->username,
			__(':username logout', array(
				':username' => $this->user->username,
			))
		);
	
		// 删除cookie
		Helper_Cookie::delete('user');
		HTTP::redirect(Route::url('xunsec-login', array('action' => 'login')));
	}

	public function action_lang()
	{
		$this->response->body(View::factory('XunSec.Lang', array(
			'translations' => Kohana::message('XunSec', 'translations'))
		));
	}
}
