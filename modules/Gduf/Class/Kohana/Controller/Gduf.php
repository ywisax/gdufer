<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 广金相关应用的基础控制器
 *
 * @package		Kohana/Gduf
 * @category	Base
 */
class Kohana_Controller_Gduf extends Controller_XunSec {

	public $layout = 'blank';
	
	public function action_index()
	{
		$this->request->response('hello, GDUF!');
	}
}
