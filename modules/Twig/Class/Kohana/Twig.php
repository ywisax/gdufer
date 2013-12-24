<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Twig模板的简便实现类
 *
 * @package		Kohana/Twig
 * @category	Base
 * @author		XunSec
 * @copyright	(c) 2008-2012 XunSec Team
 * @license		http://www.xunsec.com/license
 */
class Kohana_Twig {

	public static $instance = NULL;

	/**
	 * 渲染Twig模板
	 * 
	 * @param  string  The code to render
	 * @return string
	 */
	public static function render($code, $data = array())
	{
		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Twig', 'Twig Render');
		}

		$twig_cache_dir = IN_SAE ? 'saekv://twig' : APP_PATH.'Cache/twig';
		if (Twig::$instance === NULL)
		{
			$loader = new Twig_Loader_String();
			Twig::$instance = new Twig_Environment($loader, array(
				'cache' => $twig_cache_dir,
				'autoescape' => FALSE,
			));
		}

		$template = Twig::$instance->load_template($code);
		$content = $template->render($data);

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}
		return $content;
	}
}
