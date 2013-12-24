<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * CMS布局模型
 * 在CMS中，模型是跟页面直接相关的部分，因为布局决定页面的总体
 * 你也可以把这里的布局理解为Template（模板）
 *
 * @package		XunSec
 * @category	Model
 * @author		YwiSax
 * @copyright	(c) 2009 XunSec Team
 * @license		http://www.xunsec.com/license
 */
class Kohana_Model_Layout extends ORM {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);

	protected $_has_many = array(
		'pages' => array(
			'model'=> 'Page',
			'foreign_key' => 'layout_id',
		),
	);

	/**
	 * 渲染布局内容
	 */
	public function render($content = NULL)
	{
		// 确保布局已经加载完成
		if ( ! $this->loaded())
		{
			return __('Layout Failed to render because it wasn\'t loaded.');
		}

		if (Kohana::$profiling === TRUE)
		{
			$benchmark = Profiler::start('XunSec', 'Render Layout');
		}
		
		$out = XunSec::twig_render($this->code);
		
		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}

		return $out;
	}
	
	/**
	 * 检验当前Twig模板的有效性
	 */
	public function test_twig()
	{
		// 确保布局代码没有语法错误
		try
		{
			$test = XunSec::twig_render($this->code);
		}
		catch (Twig_SyntaxError $e)
		{
			$e->setFilename('code');
			throw new XunSec_Exception('There was a Twig Syntax error: :message', array(
				':message' => $e->getMessage(),
			));
		}
		catch (Exception $e)
		{
			throw new XunSec_Exception('There was an error: :message on line :line', array(
				':message' => $e->getMessage(),
				':line' => $e->getLine(),
			));
		}
	}
	
	/**
	 * 备份
	 */
	public function log()
	{
		$log = Model::factory('Layout.Log');
		$log->layout_id		= $this->id;
		$log->name			= $this->name;
		$log->title			= $this->title;
		$log->desc			= $this->desc;
		$log->code			= $this->code;
		$log->poster_id		= Auth::instance()->get_user()->id;
		$log->poster_name	= Auth::instance()->get_user()->username;
		$log->save();
	}

	/**
	 * 重载原来的[create]方法，判断Twig模板有效性等。
	 */
	public function create(Validation $validation = NULL)
	{
		$this->test_twig();

		$result = parent::create($validation);

		if ($this->loaded())
		{
			$this->log();
			// 保存到数据库
			XunSec::log(
				XunSec::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Add a layout, ID: :id', array(
					':id' => $this->id,
				))
			);
		}

		return $result;
	}

	/**
	 * 重载原来的[update]方法，判断Twig模板有效性等。
	 */
	public function update(Validation $validation = NULL)
	{
		$this->test_twig();
		
		if ($this->loaded())
		{
			$this->log();
			// 保存到数据库
			XunSec::log(
				XunSec::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Update a layout, ID: :id', array(
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
			XunSec::log(
				XunSec::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Delete a layout, ID: :id', array(
					':id' => $this->id,
				))
			);
		}

		return parent::delete();
	}
}
