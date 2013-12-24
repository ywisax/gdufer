<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 记录控制器，保存后台的所有操作记录
 *
 * @package		XunSec
 * @category	Controller
 * @copyright	YwiSax
 */
class Kohana_Controller_XunSec_Log extends Controller_XunSec_Admin {

	public function before()
	{
		parent::before();
	}

	public function action_index()
	{
		$page = (int) $this->request->query('page');
		if ($page < 1)
		{
			$page = 1;
		}
		$limit = 30;
		$offset = ($page - 1) * $limit;
	
		$pagination_config = Kohana::config('Pagination.cms');
		$pagination_config['total_items'] = Model::factory('Log')
			->where('type', '=', XunSec::LOG_TYPE)
			->find_all()
			->count();
		$pagination_config['items_per_page'] = $limit;
	
		$logs = Model::factory('Log')
			->where('type', '=', XunSec::LOG_TYPE)
			->order_by('id', 'DESC')
			->limit($limit)
			->offset($offset)
			->find_all();
	
		$this->template->title = __('CMS Admin Log');
		$this->template->content = View::factory('XunSec.Log.List');
		$this->template->content->logs = $logs;
		$this->template->content->pagination = Pagination::factory($pagination_config);
	}
}
