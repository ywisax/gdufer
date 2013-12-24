<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * Forum的基础控制器，同时集成XunSec的默认控制器
 *
 * @package		Kohana/Forum
 * @category	Controller
 */
class Kohana_Controller_Forum extends Controller_XunSec {

	public $layout = 'blank';
	
	/**
	 * 论坛首页
	 */
	public function action_index()
	{
		$this->render(array(
			'title' => __('Forum Index'),
			'metadesc' => Kohana::config('Forum.metadesc'),
			'metakw' => Kohana::config('Forum.metakw'),
			'content' => View::factory('Forum.Index'),
		));
	}
}
