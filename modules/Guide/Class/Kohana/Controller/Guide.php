<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 用户向导控制器
 *
 * @package    Kohana/Guide
 * @category   Controllers
 * @author     Kohana Team
 */
abstract class Kohana_Controller_Guide extends Controller_Template {

	public $template = 'Guide.Template';

	// 向导的路由
	protected $guide;

	public function before()
	{
		parent::before();

		$this->guide = Route::get('guide-doc');
		// 设置基址，主要是用来修正图片路径
		Guide_Markdown::$base_url  = URL::site($this->guide->uri()).'/';
		Guide_Markdown::$image_url = URL::site('media/guide').'/';
		// 是否显示评论
		$this->template->show_comments = Kohana::config('Guide.show_comments');
	}
	
	// List all modules that have userguides
	public function index()
	{
		$this->template->title = __('Userguide');
		$this->template->breadcrumb = array(__('User Guide'));
		$this->template->content = View::factory('Guide.Index', array('modules' => $this->_modules()));
		$this->template->menu = View::factory('Guide.Menu', array('modules' => $this->_modules()));
		
		// Don't show disqus on the index page
		$this->template->show_comments = FALSE;
	}
	
	// Display an error if a page isn't found
	public function error($message)
	{
		$this->response->status(404);
		$this->template->title = __('Userguide - Error');
		$this->template->content = View::factory('Guide.Error', array(
			'message' => __($message),
		));
		
		// Don't show disqus on error pages
		$this->template->show_comments = FALSE;

		// If we are in a module and that module has a menu, show that
		if ($module = $this->request->param('module') AND $menu = $this->file($module.'/menu') AND Kohana::config('Guide.modules.'.$module.'.enabled'))
		{
			// Namespace the markdown parser
			Guide_Markdown::$base_url  = URL::site($this->guide->uri()).'/'.$module.'/';
			Guide_Markdown::$image_url = URL::site("media/guide/$module").'/';

			$this->template->menu = Guide_Markdown::markdown($this->_get_all_menu_markdown());
			$this->template->breadcrumb = array(
				$this->guide->uri() => __('User Guide'),
				$this->guide->uri(array('module' => $module)) => Kohana::config('Guide.modules.'.$module.'.name'),
				'Error'
			);
		}
		// If we are in the api browser, show the menu and show the api browser in the breadcrumbs
		else if (Route::name($this->request->route()) == 'guide-api')
		{
			$this->template->menu = Guide::menu();

			// Bind the breadcrumb
			$this->template->breadcrumb = array(
				$this->guide->uri(array('page' => NULL)) => __('User Guide'),
				$this->request->route()->uri() => 'API Browser',
				'Error'
			);
		}
		// Otherwise, show the userguide module menu on the side
		else
		{
			$this->template->menu = View::factory('Guide.Menu', array(
				'modules' => $this->_modules(),
			));
			$this->template->breadcrumb = array($this->request->route()->uri() => __('User Guide'), __('Error'));
		}
	}

	public function action_doc()
	{
		$module = $this->request->param('module');
		$page = $this->request->param('page');

		// Trim trailing slash
		$page = rtrim($page, '/');

		// If no module provided in the url, show the user guide index page, which lists the modules.
		if ( ! $module)
		{
			return $this->index();
		}
		
		// If this module's userguide pages are disabled, show the error page
		if ( ! Kohana::config('Guide.modules.'.$module.'.enabled'))
		{
			return $this->error('That module doesn\'t exist, or has userguide pages disabled.');
		}
		
		// Prevent "guide/module" and "guide/module/index" from having duplicate content
		if ( $page == 'index')
		{
			return $this->error('Userguide page not found');
		}
		
		// If a module is set, but no page was provided in the url, show the index page
		if ( ! $page )
		{
			$page = 'index';
		}

		// Find the markdown file for this page
		$file = $this->file($module.'/'.$page);

		// If it's not found, show the error page
		if ( ! $file)
		{
			return $this->error('Userguide page not found');
		}
		
		// Namespace the markdown parser
		Guide_Markdown::$base_url  = URL::site($this->guide->uri()).'/'.$module.'/';
		Guide_Markdown::$image_url = URL::site("media/guide/$module").'/';

		// Set the page title
		$this->template->title = $page == 'index' ? Kohana::config('Guide.modules.'.$module.'.name') : $this->title($page);

		// Parse the page contents into the template
		Guide_Markdown::$show_toc = TRUE;
		$this->template->content = Guide_Markdown::markdown(file_get_contents($file));
		Guide_Markdown::$show_toc = FALSE;

		// Attach this module's menu to the template
		$this->template->menu = Guide_Markdown::markdown($this->_get_all_menu_markdown());

		// Bind the breadcrumb
		$this->template->bind('breadcrumb', $breadcrumb);
		
		// Bind the copyright
		$this->template->copyright = Kohana::config('Guide.modules.'.$module.'.copyright');

		// Add the breadcrumb trail
		$breadcrumb = array();
		$breadcrumb[$this->guide->uri()] = __('User Guide');
		$breadcrumb[$this->guide->uri(array('module' => $module))] = Kohana::config('Guide.modules.'.$module.'.name');
		
		// TODO try and get parent category names (from menu).  Regex magic or javascript dom stuff perhaps?
		
		// Only add the current page title to breadcrumbs if it isn't the index, otherwise we get repeats.
		if ($page != 'index')
		{
			$breadcrumb[] = $this->template->title;
		}
	}

	public function action_api()
	{
		// Enable the missing class autoloader.  If a class cannot be found a
		// fake class will be created that extends Guide_Missing
		spl_autoload_register(array('Guide_Missing', 'create_class'));

		// Get the class from the request
		$class = $this->request->param('class');

		// If no class was passed to the url, display the API index page
		if ( ! $class)
		{
			$this->template->title = __('Table of Contents');

			$this->template->content = View::factory('Guide.API.Toc')
				->set('classes', Guide::class_methods())
				->set('route', $this->request->route());
		}
		else
		{
			// Create the Guide_Class version of this class.
			$_class = Guide_Class::factory($class);
			
			// If the class requested and the actual class name are different
			// (different case, orm vs ORM, auth vs Auth) redirect
			if ($_class->class->name != $class)
			{
				HTTP::redirect($this->request->route()->uri(array('class'=>$_class->class->name)));
			}

			// If this classes immediate parent is Guide_Missing, then it should 404
			if ($_class->class->getParentClass() AND $_class->class->getParentClass()->name == 'Guide_Missing')
			{
				return $this->error('That class was not found. Check your url and make sure that the module with that class is enabled.');
			}
			// 如果该类在APP_PATH中，应该返回404
			if ( ! Kohana::config('Guide.include_apppath') AND UTF8::substr($_class->class->getFileName(), 0, UTF8::strlen(APP_PATH)) == APP_PATH)
			{
				return $this->error('That class was not found. Check your url and make sure that the module with that class is enabled.');
			}

			// If this classes package has been disabled via the config, 404
			if ( ! Guide::show_class($_class))
				return $this->error('That class is in package that is hidden.  Check the <code>api_packages</code> config setting.');

			// Everything is fine, display the class.
			$this->template->title = $class;

			$this->template->content = View::factory('Guide.API.Class')
				->set('doc', $_class)
				->set('route', $this->request->route());
		}

		// Attach the menu to the template
		$this->template->menu = Guide::menu();

		// Bind the breadcrumb
		$this->template->bind('breadcrumb', $breadcrumb);

		// Add the breadcrumb
		$breadcrumb = array();
		$breadcrumb[$this->guide->uri(array('page' => NULL))] = __('User Guide');
		$breadcrumb[$this->request->route()->uri()] = __('API Browser');
		$breadcrumb[] = $this->template->title;
	}

	public function after()
	{
		if ($this->auto_render)
		{
			// 添加风格文件
			$this->template->styles = array(
				Media::url('bluetrip/css/print.css')				=> 'print',
				Media::url('bluetrip/css/screen.css')				=> 'screen',
				Media::url('guide/css/guide.css')					=> 'screen',
				Media::url('syntaxhighlighter/styles/shCore.css')	=> 'screen',
				Media::url('guide/css/shThemeKohana.css')			=> 'screen',
			);

			// 添加脚本文件
			$this->template->scripts = array(
				Media::url('jquery/jquery-1.7.2.min.js'),
				Media::url('jquery/jquery.cookie.js'),
				// 语法着色器
				Media::url('syntaxhighlighter/scripts/shCore.js'),
				Media::url('syntaxhighlighter/scripts/shBrushPhp.js'),
				Media::url('guide/js/guide.js'),
			);

			// 添加语言脚本
			$this->template->translations = Kohana::message('Guide', 'translations');
		}

		return parent::after();
	}

	/**
	 * Locates the appropriate markdown file for a given guide page. Page URLS
	 * can be specified in one of three forms:
	 *
	 *  * userguide/adding
	 *  * userguide/adding.md
	 *  * userguide/adding.markdown
	 *
	 * In every case, the userguide will search the cascading file system paths
	 * for the file guide/userguide/adding.md.
	 *
	 * @param string $page The relative URL of the guide page
	 * @return string
	 */
	public function file($page)
	{

		// Strip optional .md or .markdown suffix from the passed filename
		$info = pathinfo($page);
		if (isset($info['extension'])
			AND (($info['extension'] === 'md') OR ($info['extension'] === 'markdown')))
		{
			$page = $info['dirname'].DIRECTORY_SEPARATOR.$info['filename'];
		}
		return Kohana::find_file('Guide', $page, 'md');
	}

	public function section($page)
	{
		$markdown = $this->_get_all_menu_markdown();
		
		if (preg_match('~\*{2}(.+?)\*{2}[^*]+\[[^\]]+\]\('.preg_quote($page).'\)~mu', $markdown, $matches))
		{
			return $matches[1];
		}
		
		return $page;
	}

	public function title($page)
	{
		$markdown = $this->_get_all_menu_markdown();
		
		if (preg_match('~\[([^\]]+)\]\('.preg_quote($page).'\)~mu', $markdown, $matches))
		{
			// Found a title for this link
			return $matches[1];
		}
		
		return $page;
	}
	
	protected function _get_all_menu_markdown()
	{
		// Only do this once per request...
		static $markdown = '';
		
		if (empty($markdown))
		{
			// 获取菜单选项
			$file = $this->file($this->request->param('module').'/menu');
	
			if ($file AND $text = file_get_contents($file))
			{
				// Add spans around non-link categories. This is a terrible hack.
				//echo Debug::vars($text);
				
				//$text = preg_replace('/(\s*[\-\*\+]\s*)(.*)/','$1<span>$2</span>', $text);
				$text = preg_replace('/^(\s*[\-\*\+]\s*)([^\[\]]+)$/m','$1<span>$2</span>', $text);
				//echo Debug::vars($text);
				$markdown .= $text;
			}
			
		}
		
		return $markdown;
	}

	// Get the list of modules from the config, and reverses it so it displays in the order the modules are added, but move Kohana to the top.
	protected function _modules()
	{
		$modules = array_reverse(Kohana::config('Guide.modules'));
		
		if (isset($modules['kohana']))
		{
			$kohana = $modules['kohana'];
			unset($modules['kohana']);
			$modules = array_merge(array('kohana' => $kohana), $modules);
		}
		
		// Remove modules that have been disabled via config
		foreach ($modules AS $key => $value)
		{
			if ( ! Kohana::config('Guide.modules.'.$key.'.enabled'))
			{
				unset($modules[$key]);
			}
		}
		
		return $modules;
	}

} // End Userguide
