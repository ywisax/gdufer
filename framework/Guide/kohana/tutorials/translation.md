# Route and Controller for Handling Static Pages

Kohana works extremely well for building dynamic web sites and applications.
However not everything needs to be dynamic and creating a controller/action combination for every static page is a little over kill. Here is how you can create a controller and route to handle static pages and support i18n so that your site can be multilingual. 

## 路由

I'm going to provide two examples of Routes which will support static pages.
The first one assumes that we have a set number of pages that we need to support and uses a very clean URL.
The second will support any number of pages and uses a prefix 'page' in the request URI. 

	/**
	 * This route supports the pages called 'about', 'faq' and 'locations'.
	 * Each page accessible using only its name, for example
	 * http://example.com/about or http://example.com/faq
	 */
	Route::set('static', '<page>', array('page' => 'about|faq|locations'))
		->defaults(array(
			'controller' => 'page',
			'action' => 'static',
		));

Or use a route that supports dynamic page names

	/**
	 * This route supports any number of pages.
	 * Each page accessible the following URL where page_name is the
	 * name of the page you want to load. 
	 * http://example.com/page/page_name
	 */
	Route::set('static2', 'page/<page>', array('page' => '.*')) 
		->defaults(array( 
			'controller' => 'page', 
			'action' => 'static',
	)); 

## 控制器

The same controller/action combination should be able to support both routes.
The action will be responsible for looking at the name of the requested page and then load the appropriate view. 

My example below uses the default Kohana template controller. 

	<?php defined('SYS_PATH') or die('No direct script access.');
  
	class Controller_Pages extends Controller_Template {
  
		public function action_static()
		{
			// Get the name of our requested page	
			$page = Request::instance()->param('page');
  
			// Assign the appropriate view file to the template content
			$this->template->content = View::factory('page/'. i18n::$lang .'/'. $page );
		}
	}

## 视图模板

You'll notice that in the controller that view we load uses a static property from the Kohana i18n class.
i18n::$lang will return the default language for your Kohana web site and can be defined in your application/bootstrap.php file. 

If your page name is 'about' and your default language is en-US then the controller will look for the following view file. 

	page/en-us/about

The full path for the view file will be something like the following

	/home/kerkness/kohana/application/views/page/en-us/about.php

You'll need to create view files for all the pages and languages you need to support.
 
# 用路由实现多语言

There are many ways to accomplish i18n functionality with Kohana.
One way is to incorporate language into a Route.

For example

	http://example.com/en-us/products // For English
	http://example.com/fr-fr/products // For French

You can define your Route to catch the language portion of the request.
The example below only allows English, French and Dutch to be requested and defaults to English. 

	Route::set('default', '(<lang>/)(<controller>)(/<action>(/<id>))', array('lang' => '(en-us|fr-fr|nl-nl)', 'id'=>'.+'))
		->defaults(array( 
			'lang' => 'en-us', 
			'controller' => 'welcome', 
			'action' => 'index', 
	)); 

The language can then be set in the controller.
The example below sets the language in a custom template controller.

	class Controller_Website extends Controller_Template
	{
		public function before()
		{
			// Set the language
			I18n::$lang = Request::instance()->param('lang');
		}
	}

Create language files in the directory application/i18n

	// /application/i18n/fr.php
	<?php defined('SYS_PATH') or die('No direct access allowed.');
	return array(
		'Hello World' => 'Bonjour Monde',
	);

Create language specific views in appropriate folders under application/views

	// application/views/fr-fr/index.php
	<p>Merci de visiter notre site Internet.</p>
	<p>Voici mon contenu de homepage...

All your controllers will use the appropriate language.

	class Controller_Welcome extends Controller_Website
	{
		public function action_index() 
		{
			// The string 'Hello World' will be translated
			$this->template->title = __('Hello World');
          
			// Load a language specific view
			$this->template->content = View::factory('page/'.I18n::$lang.'/index');
			//OR use the same template for all languages
			//$this->template->content = View::factory('page/index');
		} 
	} 
