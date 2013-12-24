<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 分类信息管理控制器
 * [!!] 在犹豫，其实我有必要把它写得复杂吗？
 *
 * @package    Kohana/Information
 * @category   Controller
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Controller_XunSec_Information extends Controller_XunSec_Admin {

	/**
	 * 显示分类信息的一些统计信息
	 */
	public function action_index()
	{
	}
	
	/**
	 * 查看指定类型和指定ID的分类信息
	 */
	public function action_view()
	{
	}
	
	/**
	 * 分类信息列表
	 */
	public function action_list()
	{
		$pagination_config = Kohana::config('Pagination.information.admin');
		$type = 'book';

		$model = Model_Information::factory($type);
		$records = $model
			->order_by('id', 'DESC')
			->find_all();
		
		$this->template->title = __('Information List');
		$this->template->content = View::factory('XunSec.Information.List', array(
			'records' => $records,
			'pagination' => Pagination::factory($pagination_config),
		));
	}

	/**
	 * 删除指定的分类信息
	 */
	public function action_delete()
	{
	}

	/**
	 * 模型管理（列表）
	 */
	public function action_model()
	{
	}
}
