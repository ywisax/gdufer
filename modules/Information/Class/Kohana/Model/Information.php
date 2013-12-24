<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 分类信息基础模型
 *
 * @package    Kohana/Information
 * @category   Model
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Model_Information extends ORM {

	protected $_list_view_file = 'Information.List';
	protected $_list_per_page = 20;

	public static $_valid_model_type = array(
		'book' => 'Book',
	);
	
	/**
	 * 返回当前分类信息模型名称
	 */
	public function type()
	{
		throw new Information_Exception('You need to extend this method at first.');
	}

	/**
	 * Return an element of a certain type.
	 *
	 * @param  string  The type of element to create
	 * @return Model_Element object
	 */
	final public static function factory($name, $id = NULL)
	{
		if ( ! isset(Model_Information::$_valid_model_type[$name]))
		{
			throw new Information_Exception('不支持的分类模型');
		}
	
		$model = 'Model_Information_' . Model_Information::$_valid_model_type[$name];
		$model = new $model;
		return $model;
	}
	
	/**
	 * 返回一个自定义的标题
	 */
	public function title()
	{
		return __('Untitled document');
	}
	
	/**
	 * 要自己继承这个
	 */
	public function action_list($page = 1)
	{
		throw new Information_Exception('XXXXX');
	}
	
	/**
	 * 要自己继承这个
	 */
	public function action_save($data)
	{
		throw new Information_Exception('XXXXX');
	}
	
	/**
	 * 要自己继承这个
	 */
	public function action_view()
	{
		throw new Information_Exception('XXXXX');
	}

	/**
	 * 要自己继承这个
	 */
	public function action_search($keyword)
	{
		throw new Information_Exception('XXXXX');
	}
	
	/**
	 * 添加评论
	 */
	public function save_comment($content)
	{
		$comment = Model::factory('Information.Comment');
		$comment->model_type = $this->type();
		$comment->related_id = $this->id;
		$comment->poster_id = Auth::instance()->logged_in() ? Auth::instance()->get_user()->id : 0;
		$comment->poster_name = Auth::instance()->logged_in() ? Auth::instance()->get_user()->username : '';
		$comment->content = $content;
		$comment->save();
	}
	
	/**
	 * 获取评论内容
	 */
	public function all_comments()
	{
		return Model::factory('Information.Comment')
			->where('model_type', '=', $this->type())
			->where('related_id', '=', $this->id)
			->order_by('date_created', 'DESC')
			->find_all();
	}
	
	/**
	 * 生成链接
	 */
	public function link($action = 'view')
	{
		return Route::url('information-action', array(
			'type' => $this->type(),
			'action' => $action,
			'id' => $this->id,
		));
	}

}
