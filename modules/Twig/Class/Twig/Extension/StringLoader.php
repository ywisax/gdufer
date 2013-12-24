<?php defined('SYS_PATH') or die('No direct script access.');

class Twig_Extension_StringLoader extends Twig_Extension {

	/**
	 * {@inheritdoc}
	 */
	public function get_functions()
	{
		return array(
			new Twig_Simple_Function('template_from_string', 'Twig_Extension_StringLoader::load_template', array('needs_environment' => TRUE)),
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'string_loader';
	}
	
	/**
	 * Loads a template from a string.
	 *
	 * <pre>
	 * {{ include(template_from_string("Hello {{ name }}")) }}
	 * </pre>
	 *
	 * @param Twig_Environment $env      A Twig_Environment instance
	 * @param string           $template A template as a string
	 *
	 * @return Twig_Template A Twig_Template instance
	 */
	public static function load_template(Twig_Environment $env, $template)
	{
		$name = sprintf('__string_template__%s', hash('sha256', uniqid(mt_rand(), TRUE), FALSE));

		$loader = new Twig_Loader_Chain(array(
			new Twig_Loader_Array(array($name => $template)),
			$current = $env->getLoader(),
		));

		$env->setLoader($loader);
		try
		{
			$template = $env->load_template($name);
		}
		catch (Exception $e)
		{
			$env->setLoader($current);

			throw $e;
		}
		$env->setLoader($current);

		return $template;
	}
}
