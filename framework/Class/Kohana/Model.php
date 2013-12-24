<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 基础的模型类，所有的模型都需要继承这个类
 * 这个模型类其实功能十分有限，最好是使用ORM模块来代替
 *
 * @package    Kohana
 * @category   Models
 */
abstract class Kohana_Model {

	protected static $_model_name_separator = array('.', '/');

	/**
	 * 创建模型实例
	 *
	 *     $model = Model::factory($name);
	 *
	 * @param   string  $name   模型名称
	 * @return  Model
	 */
	public static function factory($name, $id = NULL)
	{
		// 转换文件名
		$name = str_replace(Model::$_model_name_separator, '_', $name);
		// 添加前缀
		$class = 'Model_'.$name;

		return new $class($id);
	}

} // End Model
