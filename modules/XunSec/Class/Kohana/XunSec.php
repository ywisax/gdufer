<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * XunSec基础类
 *
 * @package    XunSec
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
abstract class Kohana_XunSec {

	const LOG_TYPE = 'cms';

	const TEMPLATE_VIEW = 'XunSec.XHTML';

	// 当前渲染的页面
	public static $_page = NULL;

	// 是否在管理模式中
	public static $adminmode = FALSE;

	// Content if we are in override
	protected static $_content = NULL;

	// 自定义内容
	protected static $_custom_content = NULL;

	// 处于override模式
	protected static $_override = FALSE;
	
	// 一些资源文件，乱七八糟的
	protected static $_javascripts = array();
	protected static $_stylesheets = array();
	protected static $_metas = array();

	/**
	 * 返回当前页面。PS：这样写的助手方法是不是不好呢？
	 *
	 * @param  string  页面参数键
	 * @param  mixed   页面参数值
	 */
	public static function page($key = NULL, $value = NULL)
	{
		// 如果key没有的话，不管那么多，直接返回就是了
		if ($key === NULL)
		{
			return XunSec::$_page;
		}

		// 页面都没加载，玩毛
		if (XunSec::$_page === NULL)
		{
			return NULL;
		}

		$key = (string) $key;
		if ($value === NULL)
		{
			return isset(XunSec::$_page->{$key})
				? XunSec::$_page->{$key}
				: NULL;
		}
		else
		{
			XunSec::$_page->{$key} = $value;
		}
	}

	/**
	 * 用户导航输出
	 */
	public static function user_nav()
	{
		return View::factory('XunSec.Navigation.User');
	}

	/**
	 * 主系统导航
	 *
	 * @param   string  参数
	 * @return  string  渲染后的导航条
	 */
	public static function main_nav($params = '')
	{
		if (Kohana::$profiling === TRUE)
		{
			$benchmark = Profiler::start('XunSec', __FUNCTION__);
		}
		if ( ! XunSec::$_override AND ( ! XunSec::page('id')))
		{
			return __('XunSec::main_nav failed because page is not loaded');
		}

		$defaults = array('header' => FALSE, 'depth' => 2);
		$options = array_merge($defaults, Helper_Text::params($params));

		if (XunSec::$_override)
		{
			// 没办法，只能是查找第一个页面然后写咯。
			$descendants = Model::factory('Page')
				->where('lvl', '=', 0)
				->find()
				->root()
				->nav_nodes($options['depth']);
		}
		else
		{
			$descendants = XunSec::page()
				->root()
				->nav_nodes($options['depth']);
		}

		$out = View::factory('XunSec.Navigation.Default', array(
			'nodes' => $descendants,
			'level_column' => 'lvl',
			'options' => $options
		))->render();

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}
		return $out;
	}
	
	/**
	 * 二级菜单（侧边栏）菜单
	 *
	 * @param   string   参数字符串
	 * @return  string
	 */
	public static function nav($params = '')
	{
		if (Kohana::$profiling === TRUE)
		{
			$benchmark = Profiler::start('XunSec', __FUNCTION__);
		}

		// 确保页面已经加载了。。。
		if ( ! XunSec::page('id'))
		{
			return __('XunSec::secondary_nav failed because page is not loaded');
		}

		$options = XunSec::params($params);

		if (XunSec::page()->has_children())
		{
			$page = XunSec::page();
		}
		else
		{
			$page = XunSec::page()->parent();
		}

		$descendants = $page->nav_nodes($options['depth']);
		
		$out = View::factory('XunSec.Navigation.Default', array(
			'nodes' => $descendants,
			'level_column'=> 'lvl',
			'options' => $options
		))->render();

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}
		return $out;
	}
	
	/**
	 * 渲染面包屑导航
	 *
	 * @return string
	 */
	public static function bread_crumbs()
	{
		if (Kohana::$profiling === TRUE)
		{
			$benchmark = Profiler::start('XunSec', __FUNCTION__);
		}
		
		if ( ! XunSec::page('id'))
		{
			return __('XunSec::bread_crumbs failed because page is not loaded');
		}
		
		$parents = XunSec::page()->parents(); //->render_descendants('mainnav', TRUE, 'ASC', $maxdepth);
		
		$out = View::factory('XunSec.BreadCrumb')
			->set('nodes', $parents)
			->set('page', XunSec::page('name'))
			->render();

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}
		return $out;
	}

	/**
	 * 渲染站点地图
	 *
	 * @return string
	 */
	public static function site_map()
	{
		if (Kohana::$profiling === TRUE)
		{
			$benchmark = Profiler::start('XunSec', __FUNCTION__);
		}
		
		if ( ! XunSec::page('id'))
		{
			return __('XunSec::site_map failed because page is not loaded.');
		}

		$out = XunSec::page()
			->root()
			->render_descendants('XunSec.Sitemap', FALSE, 'ASC')
			->render();

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}
		return $out;
	}
	
	/**
	 * 渲染和输出元素内容
	 *
	 * @param   int     元素ID
	 * @param   string  元素名称（admin时才有用）
	 * @return  boolean
	 */
	public static function element_area($id, $name)
	{
		if ( ! XunSec::page('id'))
		{
			return __('XunSec Error: element_area(:id) failed. (XunSec::page was not set)', array(
				':id' => $id,
			));
		}

		if (Kohana::$profiling === TRUE)
		{
			$benchmark = Profiler::start('XunSec', __FUNCTION__);
		}

		// 自定义页面内容
		if (XunSec::$_content !== NULL)
		{
			return View::factory('XunSec.Element.Area', array(
				'id' => $id,
				'name' => $name,
				'content' => Helper_Array::get(XunSec::$_content, $id-1, '')
			));
		}
		$elements = Model::factory('Block')
			->where('page_id', '=', XunSec::page('id'))
			->where('area', '=', $id)
			->order_by('order', 'ASC')
			->find_all();
		$content = '';

		foreach ($elements AS $item)
		{
			try
			{
				$element = Model_Element::factory($item->type->name);
				$element->id = $item->element;
				$element->block = $item;
				$content .= $element->render();
			}
			catch (Exception $e)
			{
				$content .= "<p>Error: Could not load element." . $e . "</p>";
			}
		}

		$out = View::factory('XunSec.Element.Area', array(
			'id' => $id,
			'name' => $name,
			'content' => $content
		))->render();

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}
		return $out;
	}
	
	/**
	 * 返回指定类型和名称的元素实例
	 *
	 * 如：
	 *  echo element('snippet', 'footer');
	 *
	 * @param  string  元素类型
	 * @param  name    元素名称
	 * @return string
	 */
	public static function element($type, $name)
	{
		if (Kohana::$profiling === TRUE)
		{
			$benchmark = Profiler::start('XunSec', __FUNCTION__);
		}

		try
		{
			$element = Model_Element::factory($type)
				->where('name', '=', $name)
				->find();
		}
		catch (Exception $e)
		{
			return __("Could not render :type ':name' (:message)", array(
				':type' => $type,
				':name' => $name,
				':message' => $e->getMessage(),
			));
		}

		if ($element->loaded())
		{
			$out = $element->render();
		}
		else
		{
			$out = __("Could not render :type with the name ':name'.", array(
				':type' => $type,
				':name' => $name,
			));
		}

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}
		return $out;
	}
	
	/*
	 * CSS控制方法，这个方法可能需要继续优化下
	 */
	public static function style($stylesheet, $media = NULL)
	{
		XunSec::$_stylesheets[$stylesheet] = $media;
	}

	/**
	 * CSS渲染方法
	 */
	public static function style_render()
	{
		$out = '';
		foreach (XunSec::$_stylesheets AS $stylesheet => $media)
		{
			if ($media != NULL)
			{
				$out .= "\t" . HTML::style( Media::url($stylesheet), array('media' => $media)) . "\n";
			}
			else
			{
				$out .= "\t" . HTML::style( Media::url($stylesheet) ) . "\n";
			}
		}
		return $out;
	}

	/*
	 * Javascript控制方法，具体用法请参考Layout相关代码
	 *
	 * @param	array	要加载的脚本地址
	 * @return	void
	 */
	public static function script($javascripts = array())
	{
		if ( ! is_array($javascripts))
		{
			$javascripts = array($javascripts);
		}

		foreach ($javascripts AS $key => $javascript)
		{
			XunSec::$_javascripts[] = $javascript;
		}
	}

	/**
	 * 移除指定的脚本链接
	 */
	public static function script_remove($javascripts = array())
	{
		foreach (XunSec::$_javascripts AS $key => $javascript)
		{
			if (in_array($javascript, $javascripts))
				unset(XunSec::$_javascripts[$key]);
		}
	}

	/**
	 * JS渲染器
	 */
	public static function script_render()
	{
		$out = '';
		foreach (XunSec::$_javascripts AS $key => $javascript)
		{
			$out .= "\t" . HTML::script( Media::url($javascript) ) . "\n";
		}
		return $out;
	}

	/*
	 * META控制方法
	 */
	public static function meta($metas = array())
	{
		if ( ! is_array($metas))
		{
			$metas = array($metas);
		}	
		foreach ($metas AS $key => $meta)
		{
			XunSec::$_metas[] = $meta;
		}
	}
	
	/**
	 * META渲染方法
	 */
	public static function meta_render()
	{
		$out = '';
		foreach (XunSec::$_metas AS $key => $meta)
		{
			$out .= "\t" . $meta . "\n";
		}
		return $out;
	}
	
	/**
	 * 返回当前渲染Profile信息
	 *
	 * @return string
	 */
	public static function render_stats()
	{
		$run = Profiler::application();
		$run = $run['current'];
		return __('Page rendered in :time seconds using :memory MB', array(
			':time' => Helper_Number::format($run['time'], 3),
			':memory' => Helper_Number::format($run['memory'] / 1024 / 1024, 2),
		));
	}
	
	/**
	 * 使用指定的布局和内容来渲染
	 *
	 * 使用示例：
	 *
	 *     echo XunSec::override('error', $content);
	 * 
	 * @param  string   要使用的布局名
	 * @param  page     页面内容
	 * @return string
	 * @throws XunSec_Exception
	 */
	public static function override($layoutname, $content = NULL)
	{
		// 查找对应布局
		$layout = Model::factory('Layout')
			->where('name', '=', $layoutname)
			->find();
		if ( ! $layout->loaded())
		{
			throw new XunSec_Exception("Failed to load the layout with name ':layout'.", array(
				':layout' => $layoutname,
			));
		}

		if ($content)
		{
			XunSec::content($content);
		}
		XunSec::$_override = TRUE;
		// 设置一些需要的变量，同时渲染页面啦
		return View::factory(XunSec::TEMPLATE_VIEW, array(
			'layoutcode' => $layout->render($content)
		));
	}

	/**
	 * 指定页面内容
	 */
	public static function content($content = NULL)
	{
		if ($content === NULL)
		{
			return XunSec::$_custom_content;
		}
		XunSec::$_custom_content = $content;
	}

	/**
	 * Twig渲染的助手方法
	 */
	public static function twig_render($code)
	{
		return Twig::render($code, array('XunSec' => new XunSec));
	}

	/**
	 * 添加记录
	 */
	public static function log($type, $operator_id, $operator_name, $content)
	{
		try
		{
			$log = Model::factory('Log');
			$log->operator_id = $operator_id;
			$log->operator_name = $operator_name;
			$log->type = $type;
			$log->content = $content;
			$log->save();
		}
		catch (ORM_Validation_Exception $e)
		{
			// 有异常的话，不要抛出
		}
		// 不用返回值吧。
	}
}
