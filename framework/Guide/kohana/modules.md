# 模块

模块是[级联文件系统](files)的扩展。
一个功能正常的模块能扩展任何代码（Class、Controller、View、Config和其他文件）。
要理解`模块`这个概念很简单，你可以简单理解为`插件`，不过在Kohana中，模块能做的明显比`插件`能做的多得多了。
For example, creating a new modeling system, a search engine, a css/js manager, etc.

## Where to find modules

Kolanos has created [kohana-universe](http://github.com/kolanos/kohana-universe/tree/master/modules/), a fairly comprehensive list of modules that are available on Github. To get your module listed there, send him a message via Github.

Mon Geslani created a [very nice site](http://kohana.mongeslani.com/) that allows you to sort Github modules by activity, watchers, forks, etc.  It seems to not be as comprehensive as kohana-universe.

Andrew Hutchings has created [kohana-modules](http://www.kohana-modules.com) which is similar to the above sites.

## Enabling modules

Modules are enabled by calling [Kohana::module] and passing an array of `'name' => 'path'`.  The name isn't important, but the path obviously is.  A module's path does not have to be in `MOD_PATH`, but usually is.  You can only call [Kohana::module] once.

	Kohana::module(array(
		'Auth'       => MOD_PATH.'Auth',       // Basic authentication
		'Cache'      => MOD_PATH.'Cache',      // Caching with multiple backends
		'CodeBench'  => MOD_PATH.'CodeBench',  // Benchmarking tool
		'Database'   => MOD_PATH.'Database',   // Database access
		'Image'      => MOD_PATH.'Image',      // Image manipulation
		'ORM'        => MOD_PATH.'ORM',        // Object Relationship Mapping
		'OAuth'      => MOD_PATH.'OAuth',      // OAuth authentication
		'Pagination' => MOD_PATH.'Pagination', // Paging of results
		'UnitTest'   => MOD_PATH.'UnitTest',   // Unit testing
		'Guide'      => MOD_PATH.'Guide',      // User guide and API documentation
		));

## Init.php

When a module is activated, if an `init.php` file exists in that module's directory, it is included.  This is the ideal place to have a module include routes or other initialization necessary for the module to function.  The Userguide and Codebench modules have init.php files you can look at.

## How modules work

A file in an enabled module is virtually the same as having that exact file in the same place in the application folder.  The main difference being that it can be overwritten by a file of the same name in a higher location (a module enabled after it, or the application folder) via the [Cascading Filesystem](files).  It also provides an easy way to organize and share your code.

## Creating your own module

To create a module simply create a folder (usually in `DOCROOT/modules`) and place the files you want to be in the module there, and activate that module in your bootstrap.  To share your module, you can upload it to [Github](http://github.com).  You can look at examples of modules made by [Kohana](http://github.com/kohana) or [other users](#where-to-find-modules).