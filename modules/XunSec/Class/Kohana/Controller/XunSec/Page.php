<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 页面管理控制器，具体的页面加载已经移至[Controller_XunSec::view]中去了
 *
 * @package		XunSec
 * @category	Controller
 * @copyright	YwiSax
 */
class Kohana_Controller_XunSec_Page extends Controller_XunSec_Admin {
	
	/**
	 * 页面列表，page tree
	 */
	public function action_index()
	{
		// 查找最上级的页面节点
		$root = Model::factory('Page')
			->where('lft', '=', 1)
			->find();

		if ( ! $root->loaded())
		{
			return $this->admin_error('Could not load root node.');
		}

		$this->template->title = __('Pages');
		$this->template->content = View::factory('XunSec.Page.List', array(
			'list' => $root->render_descendants('XunSec.Page.MPTT', TRUE, 'ASC', 10),
		));
	}
	
	/**
	 * 编辑页面meta信息
	 */
	public function action_meta()
	{
		$id = $this->request->param('params');
		
		// 查找页面
		$page = Model::factory('Page', $id);
		if ( ! $page->loaded())
		{
			return $this->admin_error(__('Could not find page with ID :id.', array(':id' => $id)));
		}

		$this->template->title = __('Editing Page');
		$this->template->content = View::factory('XunSec.Page.Edit', array(
			'success' => FALSE,
			'errors' => FALSE,
			'page' => $page,
			'layouts' => Model::factory('Layout')
				->order_by('id', 'ASC')
				->find_all(),
		));

		// 如果有提交数据，那就保存把
		if ($this->request->post())
		{
			try
			{
				$page->values($this->request->post())
					->update();
				$this->template->content->success = __('Updated successfully');
			}
			catch (ORM_Validation_Exception $e)
			{
				$this->template->content->errors = $e->errors('page');
			}
			catch (XunSec_Exception $e)
			{
				$this->template->content->errors = array($e->getMessage());
			}
		}
	}
	
	/**
	 * 编辑指定页面
	 */
	public function action_edit()
	{
		$id = $this->request->param('params');
	
		// 查找页面
		$page = Model::factory('Page', (int) $id);

		if ( ! $page->loaded())
		{
			return $this->admin_error(__('Could not find page with ID :id.', array(':id' => $id)));
		}

		// 如果当前页面是外链的话，那就没必要再处理其他的选项了
		if ($page->islink)
		{
			HTTP::redirect(Route::url('xunsec-admin', array(
				'controller' => 'Page',
				'action' => 'meta',
				'params' => $id
			)));
		}

		// 正在添加元素？
		if ($this->request->is_post())
		{
			HTTP::redirect(Route::url('xunsec-admin', array(
				'controller' => 'Element',
				'action' => 'add',
				'params' => $this->request->post('type') .'/'. $id .'/' . $this->request->post('area'),
			)));
		}

		$this->auto_render = FALSE;
		XunSec::$adminmode = TRUE;
		XunSec::style('css/page.css');
		$this->response->body($page->render());
	}

	public function action_add()
	{
		$id = (int) $this->request->param('params');
		// 查找上级页面
		$parent = Model::factory('Page', $id);
		if ( ! $parent->loaded())
		{
			return $this->admin_error(__('Could not find page with ID :id.', array(':id' => $id)));
		}

		$page = Model::factory('Page');
		$this->template->title=__('Adding New Page');
		$this->template->content = View::factory('XunSec.Page.Add', array(
			'errors' => FALSE,
			'success' => FALSE,
			'parent' => $parent,
			'page' => $page,
			'layouts' => Model::factory('Layout')
				->order_by('id', 'ASC')
				->find_all(),
		));

		if ($this->request->is_post())
		{
			try
			{
				$page->values($this->request->post());
				$page->create_at($parent, ($this->request->post('location') ? $this->request->post('location') : 'last'));
				HTTP::redirect(Route::url('xunsec-admin', array('controller' => 'Page', 'action' => 'edit', 'params'=>$page->id)));
			}
			catch (ORM_Validation_Exception $e)
			{
				$this->template->content->errors = $e->errors('page');
			}
			catch (XunSec_Exception $e)
			{
				$this->template->content->errors = array($e->getMessage());
			}

			// 保存添加页面记录
			XunSec::log(
				XunSec::LOG_TYPE,
				$this->user->id,
				$this->user->username,
				__('Add :page (ID::page_id) successful', array(
					':page' => $page->name,
					':page_id' => $page->id,
				))
			);
		}
	}

	/**
	 * 移动页面
	 */	
	public function action_move()
	{
		$pages = Model::factory('Page')
			->where('lft', '=', 1)
			->find()
			->rebuild_tree();
	
		$id = $this->request->param('params');
		// 其实有必要修改下默认的Model类，唉唉
		$page = Model::factory('Page', $id);
		if ( ! $page->loaded())
		{
			return $this->admin_error(__('Could not find page with ID :id.', array(':id' => $id)));
		}
		
		$this->template->title = __('Move Page');
		$this->template->content = View::factory('XunSec.Page.Move', array(
			'page' => $page,
			'errors' => FALSE,
		));
		
		if ($this->request->is_post())
		{
			try
			{
				$page->move_to(
					Helper_Array::get($_POST, 'action', NULL),
					Helper_Array::get($_POST, 'target', NULL)
				);
				HTTP::redirect(Route::url('xunsec-admin', array('controller' => 'Page')));
			}
			catch (XunSec_Exception $e)
			{
				$this->template->content->errors = array($e->getMessage());
			}
			
			// 保存移动页面记录
			XunSec::log(
				XunSec::LOG_TYPE,
				$this->user->id,
				$this->user->username,
				__('Move :page (ID::page_id) successful, from :from to :to', array(
					':page' => $page->name,
					':page_id' => $page->id,
				))
			);
		}
	}
	
	/**
	 * 删除指定页面
	 */
	public function action_delete()
	{
		$id = (int) $this->request->param('params');
		$page = Model::factory('Page', $id);
		if ( ! $page->loaded())
		{
			return $this->admin_error(__('Could not find page with ID :id.', array(':id' => $id)));
		}

		$this->template->title=__('Delete Page');
		$this->template->content = View::factory('XunSec.Page.Delete', array('page' => $page));

		if ($this->request->is_post())
		{
			$page->delete();
			HTTP::redirect(Route::url('xunsec-admin', array('controller' => 'Page')));
		}
	}
}

