<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Contact管理控制器
 *
 * @package    Kohana/Contact
 * @category   Admin
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/
 */
class Kohana_Controller_XunSec_Contact extends Controller_XunSec_Admin {

	/**
	 * 留言列表
	 */
	public function action_list()
	{
		$page = (int) $this->request->query('page');
		if ($page < 1)
		{
			$page = 1;
		}
		$limit = 30;
		$offset = ($page - 1) * $limit;
	
		$pagination_config = Kohana::config('Pagination.contact');
		$pagination_config['total_items'] = Model::factory('Contact.Info')
			->where('status', '=', 1)
			->find_all()
			->count();
		$pagination_config['items_per_page'] = $limit;
	
		$infos = Model::factory('Contact.Info')
			->where('status', '=', 1)
			->order_by('id', 'DESC')
			->limit($limit)
			->offset($offset)
			->find_all();
	
		$this->template->title = __('Contact List');
		$this->template->content = View::factory('XunSec.Contact.List');
		$this->template->content->infos = $infos;
		$this->template->content->pagination = Pagination::factory($pagination_config);
	}

	/**
	 * 删除留言
	 */
	public function action_delete()
	{
		$id = $this->request->param('params');
		$info = Model::factory('Contact.Info')
			->where('id', '=', $id)
			->find();
		if ($info->loaded())
		{
			$info->status = 0;
			$info->save();
		}
		HTTP::redirect( $this->request->referrer() );
	}
}
