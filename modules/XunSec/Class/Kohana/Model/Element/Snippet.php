<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * XunSec Snippet Element 模型
 *
 * 表结构：
 *
 * CREATE TABLE `xunsec_element_snippet` (
 *   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 *   `name` varchar(150) NOT NULL,
 *   `title` varchar(150) DEFAULT NULL,
 *   `code` text NOT NULL,
 *   `markdown` tinyint(1) unsigned NOT NULL DEFAULT '1',
 *   `twig` tinyint(1) unsigned NOT NULL DEFAULT '1',
 *   `date_created` int(10) DEFAULT NULL,
 *   `date_updated` int(10) DEFAULT NULL,
 *   PRIMARY KEY (`id`)
 * ) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
 *
 * @package		XunSec
 * @category	Model
 * @copyright	YwiSax
 */
class Kohana_Model_Element_Snippet extends Model_Element {

	protected $_unique = FALSE;

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

	/**
	 * 渲染方法的最后实现
	 */
	protected function _render()
	{
		$out = $this->code;
		if ($this->markdown)
		{
			$out = Markdown($out);
		}
		if ($this->twig)
		{
			$out = XunSec::twig_render($out);
		}

		return $out;
	}
	
	/**
	 * 生成一个标题
	 */
	public function title()
	{
		return __('Snippet: :snippet', array(
			':snippet' => $this->name
		));
	}
	
	/**
	 * 添加一个记录
	 */
	public function action_add($page, $area)
	{
		$snippets = new Model_Element_Snippet;
		$snippets = $snippets->find_all();
		$view = View::factory('XunSec.Element.Snippet.Add')
			->bind('element', $this)
			->set('snippets', $snippets);
		
		if ($this->request->is_post())
		{
			try
			{
				$this->id = (int) $this->request->post('element');
				$this->find();
				if ( ! $this->loaded())
				{
					throw new XunSec_Exception('Attempting to add an element that does not exist. Id: {$this->id}');
				}

				$this->create_block($page, $area);
				HTTP::redirect(Route::url('xunsec-admin', array(
					'controller' => 'Page',
					'action' => 'edit',
					'params' => $page,
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
	 * 编辑元素
	 */
	public function action_edit()
	{
		$snippets = new Model_Element_Snippet;
		$snippets = $snippets
			->find_all()
			->as_array('id');
		$view = View::factory('XunSec.Element.Snippet.Edit')
			->bind('element', $this)
			->set('snippets', $snippets);

		if ($this->request->is_post())
		{
			try
			{
				$element_id = (int) $this->request->post('element');
				if ( ! isset($snippets[$element_id]))
				{
					return FALSE;
				}
				$this->block->element = $element_id;
				$this->block->save();
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
	 * XXXXXXXX
	 */
	public function values(array $values, array $expected = NULL)
	{
		if (isset($values['twig']) AND is_string($values['twig']))
		{
			$values['twig'] = 1;
		}
		if (isset($values['markdown']) AND is_string($values['markdown']))
		{
			$values['markdown'] = 1;
		}
	
		if ($this->loaded())
		{
			$new = array(
				'twig'  => 0,
				'markdown' => 0,
			);
			return parent::values(array_merge($new, $values), $expected);
		}
		else
		{
			return parent::values($values, $expected);
		}
	}
	
	/**
	 * 备份当前记录
	 */
	public function log()
	{
		$log = Model::factory('Element.Snippet.Log');
		$log->snippet_id	= $this->id;
		$log->name			= $this->name;
		$log->title			= $this->title;
		$log->code			= $this->code;
		$log->markdown		= $this->markdown;
		$log->twig			= $this->twig;
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
					':element' => __('Snippet'),
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
					':element' => __('Snippet'),
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
					':element' => __('Snippet'),
					':id' => $this->id,
				))
			);
		}

		return parent::delete();
	}
}
