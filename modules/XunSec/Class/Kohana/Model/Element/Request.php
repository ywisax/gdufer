<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * XunSec Request Element. Executes a Kohana HMVC request and returns the result.
 *
 * @package		XunSec
 * @category	Model
 * @copyright	YwiSax
 */
class Kohana_Model_Element_Request extends Model_Element {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);
	
	/**
	 * 过滤规则
	 */
	public function filters()
	{
		return array(
			'title' => array(
				array('trim'),
				array('strip_tags'),
			),
		);
	}
	
	public function title()
	{
		return __('Request: :url', array(
			':url' => $this->url,
		));
	}
	
	/**
	 * 请求元素中可能会造成循环加载的URL，危险啊
	 */
	protected $recursion_request_url = array(
		'xunsec/view',
		'/xunsec/view',
	);

	/**
	 * 渲染
	 */
	protected function _render()
	{
		// 防止进入死循环
		if (in_array($this->url, $this->recursion_request_url))
		{
			return __('Recursion is bad!');
		}
		
		$out = '';
		try
		{
			$out = Request::factory($this->url)->execute()->body();
		}
		catch (ReflectionException $e)
		{
			$out = __('Request failed. Error: :message', array(
				':message' => $e->getMessage(),
			));
		}
		return $out;
	}
	
	/**
	 * 添加Request请求
	 */
	public function action_add($page, $area)
	{
		$view = View::factory('XunSec.Element.Request.Add', array(
			'element' => $this,
			'errors' => FALSE,
			'page' => $page,
			'area' => $area
		));
		
		if ($_POST)
		{
			try
			{
				$this->values($_POST);
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
				$view->errors = $e->errors('page');
			}
		}
		return $view;
	}

	/**
	 * 编辑请求信息
	 *
	 * @return view
	 */
	public function action_edit()
	{
		$view = View::factory('XunSec.Element.Request.Edit', array(
			'element' => $this,
		));
		
		if ($_POST)
		{
			try
			{
				$this->values($_POST);
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
	 * 保存到Log表
	 */
	public function log()
	{
		$log = Model::factory('Element.Request.Log');
		$log->request_id	= $this->id;
		$log->name			= $this->name;
		$log->title			= $this->title;
		$log->url			= $this->url;
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
				__('Add an element (:element), ID: :id', array(
					':element' => __('Request'),
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
		// 保存一份到Element.Snippet.Log中去
		if ($this->loaded())
		{
			$this->log();
			// 保存到数据库
			XunSec::log(
				XunSec::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Update an element (:element), ID: :id', array(
					':element' => __('Request'),
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
		// 保存一份到Element.Snippet.Log中去
		if ($this->loaded())
		{
			$this->log();
			// 保存到数据库
			XunSec::log(
				XunSec::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Delete an element (:element), ID: :id', array(
					':element' => __('Request'),
					':id' => $this->id,
				))
			);
		}

		return parent::delete();
	}
}
