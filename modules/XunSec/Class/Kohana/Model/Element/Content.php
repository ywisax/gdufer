<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * XunSec Content Elemenent. Can render markdown and/or twig.
 *
 * @package		XunSec
 * @category	Model
 * @copyright	YwiSax
 */
class Kohana_Model_Element_Content extends Model_Element {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);

	protected $_table_name = 'element_content';
	
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
		return __('Content: :title', array(
			':title' => $this->title,
		));
	}

	protected function _render()
	{
		$out = $this->code;
		
		// Should we run it through markdown?
		if ($this->markdown)
		{
			$out = Markdown($out);
		}
		// Should we run it through twig?
		if ($this->twig)
		{
			$out = XunSec::twig_render($out);
		}

		return $out;
	}
	
	public function action_edit()
	{
		$view = View::factory('XunSec.Element.Content.Edit', array(
			'element' => $this,
			'errors' => FALSE,
			'success' => FALSE,
		));

		if ($this->request->is_post())
		{
			$this->values($this->request->post());
			if ($this->twig)
			{
				try
				{
					$test = XunSec::twig_render($_POST['code']);
				}
				catch (Twig_SyntaxError $e)
				{
					$e->setFilename('code');
					$view->errors[] = __('There was a Twig Syntax error: :message', array(
						':message' => $e->getMessage(),
					));
					return $view;
				}
			}

			// Try saving the element
			try
			{
				$this->update();
				$view->success = __('Updated successfully');
			}
			catch (ORM_Validation_Exception $e)
			{
				$view->errors = $e->errors('page');
			}
		}
		
		return $view;
	}
	
	public function action_add($page, $area)
	{
		$view = View::factory('XunSec.Element.Content.Add', array(
			'element' => $this,
			'errors' => FALSE,
			'page' => $page,
			'area' => $area
		));
		
		if ($this->request->is_post())
		{
			$this->values($this->request->post());
			
			if ($this->twig)
			{
				try
				{
					$test = XunSec::twig_render($_POST['code']);
				}
				catch (Twig_SyntaxError $e)
				{
					$e->setFilename('code');
					$view->errors[] = __('There was a Twig Syntax error: :message', array(
						':message' => $e->getMessage(),
					));
					return $view;
				}
			}

			try
			{
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
	
	/** overload values to fix checkboxes
	 *
	 * @param array values
	 * @return $this
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
	
	public function log()
	{
		$log = Model::factory('Element.Content.Log');
		$log->content_id	= $this->id;
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
					':element' => __('Content'),
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
		if ($this->loaded())
		{
			$this->log();

			// 保存到数据库
			XunSec::log(
				XunSec::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Update an element (:element), ID: :id', array(
					':element' => __('Content'),
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
				__('Delete an element (:element), ID: :id', array(
					':element' => __('Content'),
					':id' => $this->id,
				))
			);
		}
		return parent::delete();
	}
}
