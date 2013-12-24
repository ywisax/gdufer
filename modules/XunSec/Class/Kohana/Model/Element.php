<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Element是XunSec中最重要的概念。所有页面都是基于Element来组成的。
 *
 * @package		XunSec
 * @category	Model
 * @author		YwiSax
 * @copyright	(c) 2009 XunSec Team
 * @license		http://www.xunsec.com/license
 */
abstract class Kohana_Model_Element extends ORM {

	/**
	 * @var  bool  当前元素是否允许唯一（只允许使用一次）。如果设置为`FALSE`，那么他就跟Snippet作用一样了，可以多处使用。
	 */
	protected $_unique = TRUE;

	/**
	 * @var  object  当前元素所属的block对象，感觉这里可以改进下
	 */
	public $block = NULL;
	
	/**
	 * @var  object  绑定控制器的request到这里
	 */
	public $request = NULL;

	/**
	 * 渲染元素
	 *
	 * @return string
	 */
	abstract protected function _render();
	
	/**
	 * 返回当前模型的自定义标题文本
	 *
	 * @return string
	 */
	abstract public function title();
	
	/**
	 * 自动绑定Request
	 */
	public function __construct($id = NULL)
	{
		// 绑定request
		//$this->request =& Request::current();
		
		// 呵呵
		return parent::__construct($id);
	}
	
	/**
	 * 添加页面元素
	 *
	 * @param  int  要添加的页面ID
	 * @param  int  要添加的区域位置
	 * @return view
	 */
	public function action_add($page, $area)
	{
		$view = View::factory('XunSec.Element.Add', array(
			'element' => $this,
			'page' => $page,
			'area' => $area,
		));

		if ($this->request->is_post())
		{
			try
			{
				$this->values($this->request->post());
				$this->create();
				$this->create_block($page, $area);
				HTTP::redirect(Route::url('xunsec-admin', array(
					'controller' => 'Page',
					'action' => 'edit',
					'params' => $page
				)));
			}
			catch (ORM_Validation_Exception $e)
			{
				$view->errors = $e->errors();
			}
		}
		return $view;
	}

	/**
	 * 编辑指定元素
	 *
	 * @return view
	 */
	public function action_edit()
	{
		$view = View::factory('XunSec.Element.Edit', array(
			'element' => $this
		));

		if ($this->request->is_post())
		{
			try
			{
				$this->values($this->request->post());
				$this->update();
				$view->success = __('Update successfully');
			}
			catch (ORM_Validation_Exception $e)
			{
				$view->errors = $e->errors('page');
			}
		}

		return $view;
	}
	
	/**
	 * 删除元素
	 *
	 * @return view
	 */
	public function action_delete()
	{
		$view = View::factory('XunSec.Element.Delete', array('element' => $this));

		if ($this->request->is_post())
		{
			if ($this->_unique == TRUE)
			{
				$this->delete();
			}

			$page = $this->block->page;
			// Delete the block
			$this->block->delete();
			HTTP::redirect(Route::url('xunsec-admin', array(
				'controller' => 'Page',
				'action' => 'edit',
				'params' => $page->id
			)));
		}
		
		return $view;
	}
	
	/**
	 * 返回当前元素的类型
	 *
	 * @return  string  类型字符串
	 */
	final public function type()
	{
		return str_replace('Model_Element_', '', get_class($this));
	}
	
	/**
	 * 返回指定类型的模型实例
	 *
	 * @param  string  要创建的元素类型
	 * @return Model_Element object
	 */
	final public static function factory($model, $id = NULL)
	{
		if ($model == 'Type')
		{
			throw new Kohana_Exception('It seems not like a correct model type.');
		}
	
		$model = 'Model_Element_' . ucfirst($model);
		$model = new $model;
		if ($id)
		{
			$model->values($id);
		}

		return $model;
	}
	
	/**
	 * 渲染元素
	 *
	 * @return string
	 */
	final public function render()
	{
		$out = '';

		// 确保这个元素已经加载
		if ( ! $this->loaded())
		{
			// 重新加载一次元素
			$this
				->where('id', '=', $this->block->element)
				->find();
			if ( ! $this->loaded())
			{
				$out = __('Rendering of element failed, element could not be loaded. Block id # :id', array(
					':id' => $this->block->id
				));
				$out .= '<br />';
			}
		}

		// 如果是管理权限，那就渲染控制面板
		if (XunSec::$adminmode)
		{
			$out .= $this->render_panel();
		}

		// 渲染
		try
		{
			$out .= $this->_render();
		}
		catch (Exception $e)
		{
			$out .= '<p>' . __('There was an error while rendering the element: :message', array(
				':message' => $e->getMessage(),
			)) . '</p>';
		}
		
		return $out;
	}

	/**
	 * 渲染控制面板
	 *
	 * @return view
	 */
	final public function render_panel()
	{
		if ($this->block == NULL)
		{
			return;
		}

		return View::factory('XunSec.Element.Panel', array(
			'title' => $this->title(),
			'block' => $this->block,
		)); 
	}

	/**
	 * 创建BLOCK记录
	 *
	 * @param  int  页面ID
	 * @param  int  位置ID
	 * @return view
	 */
	final public function create_block($page, $area)
	{
		if ( ! $this->loaded())
		{
			throw new XunSec_Exception('Attempting to create a block for an element that does not exist, or has not been created yet.');
		}
		Model::factory('Block')->add_one($page, $area, $this->type(), $this->id);
	}
}
