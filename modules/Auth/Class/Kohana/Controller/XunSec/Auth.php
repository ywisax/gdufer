<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Users Controller
 *
 * @package		XunSec/Auth
 * @category	Controller
 * @copyright	YwiSax
 */
class Kohana_Controller_XunSec_Auth extends Controller_XunSec_Admin {

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
		$pagination_config['total_items'] = Model::factory('User')->find_all()->count();
		$pagination_config['items_per_page'] = $limit;
	
		$users = Model::factory('User')
			->order_by('id', 'DESC')
			->limit($limit)
			->offset($offset)
			->find_all();

		$this->template->content = View::factory('XunSec.Auth.List', array(
			'users' => $users,
			'pagination' => Pagination::factory($pagination_config),
		));
	}
	
	public function action_new()
	{
		$user = Model::factory('User');
		$errors = FALSE;
		if ($this->request->is_post())
		{
			try
			{
				$user->values($this->request->post());
				$user->create();
				HTTP::redirect(Route::url('xunsec-admin', array('controller' => 'User')));
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = $e->errors('user');
			}
		}
		
		$this->template->title = __('Create New User');
		$this->template->content = View::factory('XunSec.Auth.New', array(
			'user' => $user,
			'errors' => $errors,
		));
	}
	
	public function action_edit()
	{
		$id = (int) $this->request->param('params');

		// Find the layout
		$user = Model::factory('User', $id);
		if ( ! $user->loaded())
		{
			return $this->admin_error("Could not find user with id <strong>$id</strong>");
		}

		$errors = $success = FALSE;
		
		if ($this->request->is_post())
		{
			try
			{
				$user->values($this->request->post());
				$user->update();
				$success = __('Updated Successfully');
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = $e->errors('users');
			}
		}
		
		$this->template->title = __('Editing User');
		$this->template->content = View::factory('XunSec.Auth.Edit', array(
			'user' => $user,
			'errors' => $errors,
			'success' => $success,
		));
	}
	
	/**
	 * 删除指定的用户，慎用啊
	 */
	public function action_delete()
	{
		$id = (int) $this->request->param('params');

		// Find the user
		$user = Model::factory('User', $id);
		if ( ! $user->loaded())
		{
			return $this->admin_error("Could not find user with id <strong>$id</strong>");
		}

		$errors = FALSE;
		// If the form was submitted, delete the user.
		if ($this->request->is_post())
		{
			try
			{
				$user->delete();
				HTTP::redirect(Route::url('xunsec-admin', array('controller' => 'User')));
			}
			catch (Exception $e)
			{
				//throw $e;
				$errors = array('submit' => __('Could not delete user.'));
			}
		}
		
		$this->template->title = __('Delete User');
		$this->template->content = View::factory('XunSec.Auth.Delete', array(
			'user' => $user,
			'errors' => $errors,
		));
	}
}
