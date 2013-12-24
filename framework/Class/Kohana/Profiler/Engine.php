<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 渲染引擎基础类
 *
 * @package    Kohana
 * @category   Profiler
 */
abstract class Kohana_Profiler_Engine {

	/**
	 * 抽象渲染函数
	 */
	abstract public function render();
}
