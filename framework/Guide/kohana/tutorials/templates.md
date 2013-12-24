## 创建模板

Step 1 when creating a template based website with Kohana is to create the template.

By default Kohana assumes that the template file is called *template.php* and is located in the views folder.

	/home/htdocs/kohana/APP/Demo/View/Demo/Template.php

Your template file should contain the full headers and footers for your web site and echo PHP variables where dynamic content will be inserted by your controller(s).
The following very basic template contains the following dynamic content. 

* title (string) - This is the page title
* scripts (array) - An array of Javascripts required by the page
* styles (array) - An array of CSS style sheets required by the template
* content (string) - This is the content of the page

	<!doctype html>
	<html lang="<?php echo I18n::$lang ?>">
		<head>
			<meta charset="utf-8">
			<title><?php echo $title ?></title>
			<?php foreach ($styles as $file => $type) echo HTML::style($file, array('media' => $type)), PHP_EOL ?>
			<?php foreach ($scripts as $file) echo HTML::script($file), PHP_EOL ?>
		</head>
		<body>
			<?php echo $content ?>
		</body>
	</html>

Your template can be as complex or as minimal as you like with as many dynamic elements as necessary. 

# 继承默认模板控制器

Step 2 when creating a template site with Kohana should be to extend the Controller_Template class.
While this isn't absolutely necessary because you could just work with the Controller_Template directly, it is good practice to extend the controller so that you can easily set up default values and customize output based on request. 

**How our Controllers will be organized：**

* Controller_Template extends Controller
	* Controller_Demo extends Controller_Template
		* Controller_Page extends Controller_Demo

Our custom template controller will be called `Demo.php` and should be created in the following directory 

	/home/htdocs/kohana/APP/Demo/Class/Controller/Demo.php

Here is our customized template controller:

	<?php defined('SYS_PATH') or die('No direct script access.');
  
	class Controller_Demo extends Controller_Template 
	{
  
		public $template = 'demo/template';
  
		/**
		 * The before() method is called before your controller action.
		 * In our template controller we override this method so that we can
		 * set up default values. These variables are then available to our
		 * controllers if they need to be modified.
		*/
		public function before()
		{
			parent::before();
  
			if ($this->auto_render)
			{
				// Initialize empty values
				$this->template->title   = '';
				$this->template->content = '';
  			
			$this->template->styles = array();
			$this->template->scripts = array();
			
			}
		}
  	
		/**
		 * The after() method is called after your controller action.
         * In our template controller we override this method so that we can
         * make any last minute modifications to the template before anything
         * is rendered.
		 */
		public function after()
		{
			if ($this->auto_render)
			{
				$styles = array(
					'media/css/screen.css' => 'screen, projection',
					'media/css/print.css' => 'print',
					'media/css/style.css' => 'screen',
				);
  
				$scripts = array(
					'http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js',
				);
		
				$this->template->styles = array_merge( $this->template->styles, $styles );
				$this->template->scripts = array_merge( $this->template->scripts, $scripts );
			}
			parent::after();
		}
	}

Our template controller performs two main functions：

1.Prepares default values and set up before our action is called via the `before()` method.
2.Modifies and verifies value after our action is called and before any response is rendered via the `after()` method

In the above example the `before()` method sets up empty variables so that they are available for our controllers.
The `after()` method adds default javascript and css files which will be used by all pages of our web site. 

## 页面控制器

Now that we have set up a template and extended the Kohana template controller so that we can customize how our web site responds, it is now time to create an actual page controller so that we can start serving web pages. 

Our page controller will be called `Page.php` and should be created in the following directory:

	/home/htdocs/kohana/APP/Demo/Class/Controller/Page.php

Here is our page controller.
It contains two actions, one for loading a home page and another for loading a contact page.

	<?php defined('SYS_PATH') or die('No direct script access.');
  
	class Controller_Page extends Controller_Demo {
  
		public function action_home()
		{
			$this->template->title = __('Welcome To Acme Widgets');
  		
			$this->template->content = View::factory('page/home' );
		}
  	
		public function action_contact()
		{
			$this->template->title = __('Contact Information for Acme Widgets');
  		
			$this->template->content = View::factory('page/contact' );
		}
  	
	}

Now we have to create view files for our two pages.
The files will be call `Home.php` and `Contact.php` and should be created in the following location. 

	/home/htdocs/kohana/APP/Demo/View/Page/Home.php
	/home/htdocs/kohana/APP/Demo/View/Page/Contact.php

Here are two view files you can use.
At the moment these are very basic but could be customized to any extent. 

**Home.php**

	<h1>Welcome to our homepage.</h1>
	<p>We love our new Kohana web site</p>

**contact.php**

	<h1>How to contact us.</h1>
	<p>We don't like to be contacted so don't bother.</p>
