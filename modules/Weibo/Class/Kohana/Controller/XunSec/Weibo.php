<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 微博管理控制器
 *
 * @package		Kohana/Weibo
 * @category	Controller
 * @author		YwiSax
 */
class Kohana_Controller_XunSec_Weibo extends Controller_XunSec_Admin {

	/**
	 * 默认首页，显示微博列表啦
	 */
	public function action_index()
	{
		$page = (int) $this->request->query('page');
		if ($page < 1)
		{
			$page = 1;
		}
		$limit = 30;
		$offset = ($page - 1) * $limit;
	
		$pagination_config = Kohana::config('Pagination.weibo');
		$pagination_config['total_items'] = Model::factory('Weibo.Feed')->find_all()->count();
		$pagination_config['items_per_page'] = $limit;
	
		$feeds = Model::factory('Weibo.Feed')
			->order_by('id', 'DESC')
			->limit($limit)
			->offset($offset)
			->find_all();
	
		$this->template->title = __('Weibo List');
		$this->template->content = View::factory('XunSec.Weibo.List');
		$this->template->content->feeds = $feeds;
		$this->template->content->pagination = Pagination::factory($pagination_config);
	}
	
	/**
	 * 微博设置页面
	 */
	public function action_setting()
	{
		// 先查找到所有设置
		$settings = Model::factory('Weibo.Setting')
			->find_all()
			->as_array('key');
	
		// 更新
		if ($this->request->is_post())
		{
			// 重置新数据
			$updated = FALSE;
			foreach ($this->request->post() AS $key => $val)
			{
				if (isset($settings[$key]))
				{
					if ($key == 'client_status')
					{
						$val = (bool) $val;
					}
					$settings[$key]->val = $val;
					$settings[$key]->save();
					$updated = TRUE;
				}
			}
			// client_status要额外处理啊
			if ( ! $this->request->post('client_status'))
			{
				$settings['client_status']->val = 0;
				$settings['client_status']->save();
				$updated = TRUE;
			}
			
			if ($updated)
			{
				// 保存记录
				XunSec::log(
					Weibo::LOG_TYPE,
					$this->user->id,
					$this->user->username,
					__('Update weibo setting')
				);
			}
		}

		$this->template->title = __('Weibo Setting');
		$this->template->content = View::factory('XunSec.Weibo.Setting');
		$this->template->content->settings = $settings;
	}
	
	/**
	 * 列出认证用户
	 */
	public function action_user()
	{
		$page = (int) $this->request->query('page');
		if ($page < 1)
		{
			$page = 1;
		}
		$limit = 50;
		$offset = ($page - 1) * $limit;
	
		$pagination_config = Kohana::config('Pagination.weibo');
		$pagination_config['total_items'] = Model::factory('Weibo.User')->find_all()->count();
		$pagination_config['items_per_page'] = $limit;
	
		// 分页暂时不管
		$users = Model::factory('Weibo.User')
			->order_by('uid', 'DESC')
			->limit($limit)
			->offset($offset)
			->find_all();

		$this->template->title = __('Authentication User');
		$this->template->content = View::factory('XunSec.Weibo.User');
		$this->template->content->users = $users;
		$this->template->content->pagination = Pagination::factory($pagination_config);
	}
	
	/**
	 * 禁用用户指定天数
	 */
	public function action_banuser()
	{
		// 获取UID
		$uid = $this->request->param('params');
		$weibo_user = Model::factory('Weibo.User')
			->where('uid', '=', $uid)
			->find();
		if ($weibo_user->loaded() AND ($day = $this->request->post('day')))
		{
			$weibo_user->ban_expired = time() + ($day * 24 * 60 * 60);
			$weibo_user->save();
			
			// 保存记录
			XunSec::log(
				Weibo::LOG_TYPE,
				$this->user->id,
				$this->user->username,
				__('Ban user :screen_name (uid:uid) for :day days', array(
					':screen_name' => $weibo_user->screen_name,
					':uid' => $weibo_user->uid,
					':day' => $day,
				))
			);
		}
		exit;
	}
	
	/**
	 * 解禁用户
	 */
	public function action_release()
	{
		$uid = $this->request->param('params');
		$weibo_user = Model::factory('Weibo.User')
			->where('uid', '=', $uid)
			->find();
		if ($weibo_user->loaded())
		{
			// 到期时间设置为0即可。
			$weibo_user->ban_expired = 0;
			$weibo_user->save();
			
			// 保存记录
			XunSec::log(
				Weibo::LOG_TYPE,
				$this->user->id,
				$this->user->username,
				__('Release user :screen_name (uid:uid)', array(
					':screen_name' => $weibo_user->screen_name,
					':uid' => $weibo_user->uid,
				))
			);
			
			echo 'true';
		}
		else
		{
			echo 'false';
		}
		exit;
	}
	
	/**
	 * 查看微博管理日志
	 */
	public function action_log()
	{
		$page = (int) $this->request->query('page');
		if ($page < 1)
		{
			$page = 1;
		}
		$limit = 30;
		$offset = ($page - 1) * $limit;
	
		$pagination_config = Kohana::config('Pagination.weibo');
		$pagination_config['total_items'] = Model::factory('Log')
			->where('type', '=', Weibo::LOG_TYPE)
			->find_all()
			->count();
		$pagination_config['items_per_page'] = $limit;
	
		$logs = Model::factory('Log')
			->where('type', '=', Weibo::LOG_TYPE)
			->order_by('id', 'DESC')
			->limit($limit)
			->offset($offset)
			->find_all();
	
		$this->template->title = __('Weibo Log');
		$this->template->content = View::factory('XunSec.Weibo.Log');
		$this->template->content->logs = $logs;
		$this->template->content->pagination = Pagination::factory($pagination_config);
	}
}
