<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 控制台渲染引擎
 *
 * @package    Kohana
 * @category   Profiler
 */
class Kohana_Profiler_Engine_Console extends Profiler_Engine {

	/**
	 * @var  string  默认使用的视图
	 */
	public static $default_view = 'Profiler.Console';

	/**
	 * 渲染
	 */
	public function render()
	{
		$view = Profiler_Engine_Console::$default_view;
		
		// 直接输出？
		//return View::factory($view);
		echo View::factory($view);
	}
}
