<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * CMS页面模型
 *
 * 在CMS中，页面主要由以下几个元素组成：
 *
 *   1. 布局
 *   2. 元素
 *     2.1 Content
 *     2.2 Snippet
 *     2.3 Request
 * 
 * 关于几种不同元素模型的区别，可以参看具体的代码
 *
 * @package		XunSec
 * @category	Model
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Model_Page extends ORM_MPTT {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);
	
	protected $_belongs_to = array(
		'layout' => array(
			'foreign_key' => 'layout_id',
			'model' => 'Layout',
		),
	);
	
	/**
	 * 过滤规则
	 */
	public function filters()
	{
		return array(
			'name' => array(
				array('trim'),
				array('strip_tags'),
			),
			'title' => array(
				array('trim'),
				array('strip_tags'),
			),
			'metakw' => array(
				array('trim'),
				array('strip_tags'),
			),
			'metadesc' => array(
				array('trim'),
				array('strip_tags'),
			),
		);
	}

	/**
	 * 在指定节点创建页面
	 *
	 * @param  XunSec_Page	父节点
	 * @param  string/int   添加位置
	 * @return void
	 */
	public function create_at($parent, $location = 'last')
	{
		// 如果不是外链的话，那就必须要有布局
		if ( ! $this->islink AND empty($this->layout->id))
		{
			throw new XunSec_Exception("You must select a layout for a page that is not an external link.");
		}
		
		// 看代码啊
		if ($location == 'first')
		{
			$this->insert_as_first_child($parent);
		}
		else if ($location == 'last')
		{
			$this->insert_as_last_child($parent);
		}
		else
		{
			$target = Model::factory('Page', intval($location));
			if ( ! $target->loaded())
			{
				throw new XunSec_Exception('Could not create page, could not find target for insert_as_next_sibling id: :location', array(
					':location' => $location,
				));
			}
			$this->insert_as_next_sibling($target);
		}
	}
	
	/**
	 * 移动页面
	 */
	public function move_to($action, $target)
	{
		$target = Model::factory('Page', $target);
		
		if ( ! $target->loaded())
		{
			throw new XunSec_Exception('Could not move page ( ID: :id ), target page did not exist.', array(
				':id' => (int) $target->id,
			));
		}
		
		if ($action == 'before')
		{
			$this->move_to_prev_sibling($target);
		}
		elseif ($action == 'after')
		{
			$this->move_to_next_sibling($target);
		}
		elseif ($action == 'first')
		{
			$this->move_to_first_child($target);
		}
		elseif ($action == 'last')
		{
			$this->move_to_last_child($target);
		}
		else
		{
			throw new XunSec_Exception("Could not move page, action should be 'before', 'after', 'first' or 'last'.");
		}
	}
	
	/**
	 * 渲染页面
	 *
	 * @returns  View  渲染的视图页面
	 */
	public function render()
	{
		if ( ! $this->loaded())
		{
			throw new XunSec_Exception('Page render failed because page was not loaded.', array(), 404);
		}
		XunSec::$_page = $this;

		// 渲染布局
		return View::factory(XunSec::TEMPLATE_VIEW, array('layoutcode' => $this->layout->render()));
	}
	
	public function nav_nodes($depth)
	{
		return ORM_MPTT::factory('Page')
			->where($this->left_column, '>=', $this->{$this->left_column})
			->where($this->right_column, '<=', $this->{$this->right_column})
			->where($this->scope_column, '=', $this->{$this->scope_column})
			->where($this->level_column, '<=', $this->{$this->level_column} + $depth)
			->where('shownav', '=', 1)
			->order_by($this->left_column, 'ASC')
			->find_all();
	}
	
	
	/**
	 * 重载values方法，进行额外的处理
	 *
	 * @param array values
	 * @return $this
	 */
	public function values(array $values, array $expected = NULL)
	{
		if (isset($values['islink']) AND is_string($values['islink']))
		{
			$values['islink'] = 1;
		}
		if (isset($values['showmap']) AND is_string($values['showmap']))
		{
			$values['showmap'] = 1;
		}
		if (isset($values['shownav']) AND is_string($values['shownav']))
		{
			$values['shownav'] = 1;
		}
	
		if ($this->loaded())
		{
			$new = array(
				'islink'  => 0,
				'showmap' => 0,
				'shownav' => 0.
			);
			return parent::values(array_merge($new, $values), $expected);
		}
		else
		{
			return parent::values($values, $expected);
		}
	}
	
	public function log()
	{
		$log = Model::factory('Page.Log');
		$log->page_id		= $this->id;
		$log->parent_id		= $this->parent_id;
		$log->url			= $this->url;
		$log->name			= $this->name;
		$log->layout_id		= $this->layout_id;
		$log->islink		= $this->islink;
		$log->showmap		= $this->showmap;
		$log->shownav		= $this->shownav;
		$log->title			= $this->title;
		$log->metadesc		= $this->metadesc;
		$log->metakw		= $this->metakw;
		$log->lft			= $this->lft;
		$log->rgt			= $this->rgt;
		$log->lvl			= $this->lvl;
		$log->scope			= $this->scope;
		$log->poster_id		= Auth::instance()->get_user()->id;
		$log->poster_name	= Auth::instance()->get_user()->username;
		$log->save();
	}
	
	/**
	 * 创建记录的同时，插入一份到Log中去
	 */
	public function create(Validation $validation = NULL)
	{
		$result = parent::create($validation);
		if ($this->loaded())
		{
			$this->log();
			// 保存到数据库
			XunSec::log(
				XunSec::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Add a page, ID: :id', array(
					':id' => $this->id,
				))
			);
		}
		return $result;
	}
	
	/**
	 * 修改记录的同时，把旧的数据保存到Log中去
	 */
	public function update(Validation $validation = NULL)
	{
		if (empty($this->_changed))
		{
			// 没有东西需要更新
			return $this;
		}

		if ($this->loaded())
		{
			$this->log();

			// 保存到数据库
			XunSec::log(
				XunSec::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Update a page, ID: :id', array(
					':id' => $this->id,
				))
			);
		}
	
		return parent::update($validation);
	}
	
	/**
	 * 删除前保存一份到Log中去
	 */
	public function delete()
	{
		if ($this->loaded())
		{
			$this->log();
			
			// 保存到数据库
			XunSec::log(
				XunSec::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Delete a page, ID: :id', array(
					':id' => $this->id,
				))
			);
		}

		return parent::delete();
	}
}
