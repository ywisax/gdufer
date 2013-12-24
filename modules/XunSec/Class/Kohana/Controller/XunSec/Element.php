<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 元素控制器
 *
 * @package		XunSec
 * @category	Controller
 * @copyright	YwiSax
 */
class Kohana_Controller_XunSec_Element extends Controller_XunSec_Admin {

	/**
	 * 上移
	 */
	public function action_moveup()
	{
		$id = (int) $this->request->param('params');
		$block = Model::factory('Block')
			->where('id', '=', $id)
			->find();
		if ( ! $block->loaded())
		{
			return $this->admin_error(__('Couldn\'t find block ID :id.', array(':id' => $id)));
		}

		// 查找同页面的下一个块
		$other = Model::factory('Block')
			->where('area', '=', $block->area)
			->where('page_id', '=', $block->page)
			->where('order', '<', $block->order)
			->order_by('order', 'DESC')
			->find();
		
		if ($other->loaded())
		{
			// Swap their orders
			$temp = $block->order;
			$block->order = $other->order;
			$other->order = $temp;
			$block->update();
			$other->update();
		}
		// 跳转回编辑页面
		HTTP::redirect(Route::url('xunsec-admin', array(
			'controller' => 'Page',
			'action' => 'edit',
			'params' => $block->page->id,
		)));
	}
	
	/**
	 * 下移
	 */
	public function action_movedown()
	{
		$id = (int) $this->request->param('params');
		$block = Model::factory('Block', $id);
		if ( ! $block->loaded())
		{
			return $this->admin_error(__('Couldn\'t find block ID :id.', array(':id' => $id)));
		}
		
		$other = Model::factory('Block')
			->where('area', '=', $block->area)
			->where('page_id', '=', $block->page)
			->where('order', '>', $block->order)
			->order_by('order', 'ASC')
			->find();
		
		if ($other->loaded())
		{
			$temp = $block->order;
			$block->order = $other->order;
			$other->order = $temp;
			
			$block->update();
			$other->update();
		}
		
		HTTP::redirect(Route::url('xunsec-admin', array(
			'controller' => 'Page',
			'action' => 'edit',
			'params' => $block->page->id,
		)));
	}

	/**
	 * 返回添加元素的页面
	 *
	 * @param   string   type/page/area 如: 3/89/1
	 * @return  void
	 */
	public function action_add()
	{
		$params = $this->request->param('params');
		$params = explode('/', $params);
		$type = Helper_Array::get($params, 0, NULL);
		$page = Helper_Array::get($params, 1, NULL);
		$area = Helper_Array::get($params, 2, NULL);
		
		if ($page == NULL OR $type == NULL OR $area == NULL)
		{
			return $this->admin_error(__('Add requires 3 parameters, type, page and area.'));
		}

		$type = (int) $type;
		$page = (int) $page;
		$area = (int) $area;
		$type = Model::factory('Element.Type', intval($type));
		if ( ! $type->loaded())
		{
			return $this->admin_error(__('Elementtype :type could not be loaded.', array(':type' => (int) $block->elementtype->id)));
		}

		$class = Model_Element::factory($type->name);
		$class->request =& $this->request;

		$this->template->title = __('Add Element');
		$this->template->content = $class->action_add((int) $page, (int) $area);
		$this->template->content->page = $page;
	}
	
	/**
	 * 返回一个编辑元素的页面
	 *
	 * @param   int   要编辑的block ID
	 * @return  void
	 */
	public function action_edit()
	{
		$id = (int) $this->request->param('params');
		// 加载block
		$block = Model::factory('Block', $id);
		if ( ! $block->loaded())
		{
			return $this->admin_error(__('Couldn\'t find block ID :id.', array(':id' => $id)));
		}

		// Block对应的类型
		$type = $block->type;
		if ( ! $type->loaded())
		{
			return $this->admin_error(__('Elementtype :type could not be loaded.', array(':type' => (int) $block->elementtype->id)));
		}

		$class = Model_Element::factory($type->name)
			->where('id', '=', $block->element)
			->find();
		if ( ! $class->loaded())
		{
			return $this->admin_error(__(':type with ID :id could not be found.', array(
				':type' => $type->name,
				':id' => (int) $block->element,
			)));
		}

		$class->request =& $this->request;
		$class->block =& $block;

		$this->template->title = __('Editing :element', array(':element' => __(ucfirst($type->name))));
		$this->template->content = $class->action_edit();
		$this->template->content->page = $block->page->id;
	}
	
	/**
	 * 删除指定的元素
	 */
	public function action_delete()
	{
		$id = (int) $this->request->param('params');
		$block = Model::factory('Block', $id);
		if ( ! $block->loaded())
		{
			return $this->admin_error(__('Couldn\'t find block ID :id.', array(':id' => $id)));
		}

		// 类型
		$type = $block->type;
		if ( ! $type->loaded())
		{
			return $this->admin_error(__('Elementtype :type could not be loaded.', array(
				':type'=> (int) $block->elementtype->id,
			)));
		}

		$class = Model_Element::factory($type->name)
			->where('id', '=', $block->element)
			->find();
		$class->block =& $block;

		if ( ! $class->loaded())
		{
			return $this->admin_error(__(':type with ID :id could not be found.', array(
				':type' => $type->name,
				':id' => (int) $block->element,
			)));
		}

		$this->template->title = __('Delete :element', array(':element' => __(ucfirst($type->name))));
		$this->template->content = $class->action_delete();
	}
}
