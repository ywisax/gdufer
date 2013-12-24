<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 内容片断控制器，主要用来保存一些通用的内容块，方便布局调用
 *
 * @package		XunSec
 * @category	Controller
 * @copyright	YwiSax
 */
class Kohana_Controller_XunSec_Snippet extends Controller_XunSec_Admin {

	/**
	 * 片段首页，其实就是片段列表
	 */
	public function action_index()
	{		
		$this->template->title = __('Snippets');
		$this->template->content = View::factory('XunSec.Snippet.List', array(
			'snippets' => Model_Element::factory('Snippet')
				->order_by('id', 'ASC')
				->find_all(),
		));
	}
	
	/**
	 * 新建片段代码
	 */
	public function action_new()
	{
		$snippet = Model_Element::factory('Snippet');

		$this->template->title = __('Adding Snippet');
		$this->template->content = View::factory('XunSec.Snippet.New', array('snippet' => $snippet, 'errors' => FALSE));

		if ($this->request->is_post())
		{
			$snippet->values($this->request->post());
			// 保存时要确保twig语法无错误啊
			if ($snippet->twig)
			{
				try
				{
					$test = XunSec::twig_render($this->request->post('code'));
				}
				catch (Twig_SyntaxError $e)
				{
					$e->setFilename('code');
					$this->template->content->errors[] = __('There was a Twig Syntax error: :message', array(
						':message' => $e->getMessage(),
					));
					return;
				}
			}

			try
			{
				$snippet->save();
				HTTP::redirect(Route::url('xunsec-admin', array('controller' => 'Snippet')));
			}
			catch (ORM_Validation_Exception $e)
			{
				$this->template->content->errors = $e->errors('snippet');
			}
		}
	}
	
	/**
	 * 编辑片段信息
	 */
	public function action_edit()
	{
		$id = (int) $this->request->param('params');
		// 查找片段
		$snippet = Model_Element::factory('Snippet')
			->where('id', '=', $id)
			->find();
		
		$this->template->title = __('Editing Snippet');
		$this->template->content = View::factory('XunSec.Snippet.Edit', array(
			'snippet' => $snippet,
			'errors' => FALSE,
			'success' => FALSE,
		));
		
		if ( ! $snippet->loaded())
		{
			return $this->admin_error("Could not find snippet with id <strong>$id</strong>.");
		}
		
		if ($this->request->is_post())
		{
			
			$snippet->values($this->request->post());
			
			// Make sure there are no twig syntax errors
			if ($snippet->twig)
			{
				try
				{
					$test = XunSec::twig_render($this->request->post('code'));
				}
				catch (Twig_SyntaxError $e)
				{
					$e->setFilename('code');
					// 好像有点错误
					$this->template->content->errors[] = __('There was a Twig Syntax error: :message', array(
						':message' => $e->getMessage(),
					));
					return;
				}
			}

			try
			{
				$snippet->update();
				$this->template->content->success = __('Updated Successfully');
			}
			catch (ORM_Validation_Exception $e)
			{
				$this->template->content->errors = $e->errors('snippet');
			}
		}
	}
	
	/**
	 * 删除指定的片段
	 */
	public function action_delete()
	{
		$id = (int) $this->request->param('params');
		// 查找片段信息
		$snippet = Model_Element::factory('Snippet')
			->where('id', '=', $id)
			->find();

		if ( ! $snippet->loaded())
		{
			return $this->admin_error("Could not find snippet with id <strong>$id</strong>.");
		}

		$errors = FALSE;
		if ($this->request->is_post())
		{
			try
			{
				$snippet->delete();
				HTTP::redirect(Route::url('xunsec-admin', array('controller' => 'Snippet')));
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = array('submit' => "Delete failed!");
			}
			
		}

		$this->template->title = __('Delete Snippet');
		$this->template->content = View::factory('XunSec.Snippet.Delete', array('snippet' => $snippet));
		$this->template->content->snippet = $snippet;
		$this->template->content->errors = $errors;
	}
}
