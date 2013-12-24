<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 跳转管理
 *
 * @package		XunSec
 * @category	Controller
 * @copyright	YwiSax
 */
class Kohana_Controller_XunSec_Redirect extends Controller_XunSec_Admin {

	public function action_index()
	{
		$redirects = Model::factory('Redirect')->find_all();
		$this->template->title = __('Redirects');
		$this->template->content = View::factory('XunSec.Redirect.List', array('redirects' => $redirects));
	}
	
	/**
	 * 控制器方法：新建跳转
	 */
	public function action_new()
	{
		$redirect = Model::factory('Redirect');
		$errors = FALSE;
		if ($this->request->is_post())
		{
			try
			{
				$redirect->values($this->request->post());
				$redirect->save();
				HTTP::redirect(Route::url('xunsec-admin', array('controller' => 'Redirect')));
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = $e->errors('redirect');
			}
		}
		
		$this->template->title = __('New Redirect');
		$this->template->content = View::factory('XunSec.Redirect.New');
		$this->template->content->redirect = $redirect;
		$this->template->content->errors = $errors;
	}
	
	/**
	 * 控制器方法：编辑跳转
	 */
	public function action_edit()
	{
		$id = (int) $this->request->param('params');
		$redirect = Model::factory('Redirect', $id);
		if ( ! $redirect->loaded())
		{
			return $this->admin_error("Could not find redirect with id <strong>$id</strong>.");
		}
		
		$errors = FALSE;
		$success = FALSE;
		
		if ($this->request->is_post())
		{
			try
			{
				$redirect->values($this->request->post());
				$redirect->update();
				$success = __('Updated Successfully');
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = $e->errors('redirect');
			}
		}
		
		$this->template->title = __('Editing Redirect');
		$this->template->content = View::factory('XunSec.Redirect.Edit');
	
		$this->template->content->redirect = $redirect;
		$this->template->content->errors = $errors;
		$this->template->content->success = $success;
	}
	
	/**
	 * 控制器方法：删除跳转
	 */
	public function action_delete()
	{
		$id = (int) $this->request->param('params');
		
		$redirect = Model::factory('Redirect', $id);
		if ( ! $redirect->loaded())
		{
			return $this->admin_error("Could not find redirect with id <strong>$id</strong>.");
		}
		
		$errors = FALSE;

		if ($this->request->is_post())
		{
			try
			{
				$redirect->delete();
				HTTP::redirect(Route::url('xunsec-admin', array('controller' => 'Redirect')));
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = array('submit' => 'Delete failed!');
			}
		}

		$this->template->title = __('Delete Redirect');
		$this->template->content = View::factory('XunSec.Redirect.Delete', array('redirect' => $redirect));
		
		$this->template->content->redirect = $redirect;
		$this->template->content->errors = $errors;
	}
}
