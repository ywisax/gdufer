<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 布局控制器
 *
 * @package		XunSec
 * @category	Controller
 * @copyright	YwiSax
 */
class Kohana_Controller_XunSec_Layout extends Controller_XunSec_Admin {

	/**
	 * 布局列表
	 */
	public function action_index()
	{
		$this->template->title = __('Layouts');
		$this->template->content = View::factory('XunSec.Layout.List');
		
		// 获取所有布局。目前俩看，布局还不用使用分页吧？
		$this->template->content->layouts = Model::factory('Layout')
			->order_by('id', 'ASC')
			->find_all();
	}

	/**
	 * 编辑指定的布局
	 */
	public function action_edit()
	{
		$id = (int) $this->request->param('params');
		$layout = Model::factory('Layout', $id);
		if ( ! $layout->loaded())
		{
			return $this->admin_error(__('Could not find layout with ID :id.', array(':id' => $id)));
		}

		$this->template->title = __('Edit Layout');
		$this->template->content = View::factory('XunSec.Layout.Edit', array(
			'layout' => $layout,
			'errors' => FALSE,
			'success' => FALSE,
		));

		if ($this->request->is_post())
		{
			try
			{
				$layout->values($this->request->post());
				$layout->update();
				$this->template->content->success = __('Updated Successfully');
			}
			catch (ORM_Validation_Exception $e)
			{
				$this->template->content->errors = $e->errors('layout');
			}
			catch (XunSec_Exception $e)
			{
				$this->template->content->errors = array($e->getMessage());
			}
		}
	}

	/**
	 * 新增一个布局
	 */
	public function action_new()
	{
		$layout = Model::factory('Layout');

		$this->template->title = __('New Layout');
		$this->template->content = View::factory('XunSec.Layout.New', array(
			'layout' => $layout,
			'errors' => FALSE,
		));

		if ($this->request->is_post())
		{
			// 保存提交的数据
			try
			{
				$layout->values($this->request->post());
				$layout->save();

				HTTP::redirect(Route::url('xunsec-admin', array('controller' => 'Layout')));
			}
			catch (ORM_Validation_Exception $e)
			{
				$this->template->content->errors = $e->errors('layout');
			}
			catch (XunSec_Exception $e)
			{
				$this->template->content->errors = array($e->getMessage());
			}
		}
	}
	
	/**
	 * 删除指定布局
	 */
	public function action_delete()
	{
		$id = (int) $this->request->param('params');

		// 查找布局
		$layout = Model::factory('Layout', $id);
		if ( ! $layout->loaded())
		{
			return $this->admin_error(__('Could not find layout with ID :id.', array(':id' => $id)));
		}

		$this->template->title = __('Delete Layout');
		$this->template->content = View::factory('XunSec.Layout.Delete', array(
			'errors' => FALSE,
			'layout' => $layout,
		));
		
		if ($this->request->is_post())
		{
			try
			{
				$layout->delete();
				HTTP::redirect(Route::url('xunsec-admin', array('controller' => 'Layout')));
			}
			catch (Exception $e)
			{
				$this->template->content->errors = array('submit' => __('Delete failed! This is most likely caused because this template is still being used by one or more pages.'));
			}
		}
	}
}
