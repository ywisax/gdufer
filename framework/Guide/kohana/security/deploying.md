当你编码并测试完成之后，你就需要更改当前运行环境并发布了。

安全设置在[Bootstrap章节](bootstrap)中

## 设置生产环境

在你要把应用正式投入应用之前，你还需要进行一些细微设置的修改。

1. 查看文档中的[Bootstrap章节](bootstrap)。
   检查各个选项，并把不需要的关闭了。
   例如，你要激活内置路径缓存功能并关闭Profiling（[Kohana::init]中设置）。 
   如果你有很多路由，那么[Route::cache]也会改善你的性能
2. 开启APC或其他opcode缓存器
   这是提升PHP性能，最简单最容易的方法。
   越是复杂的应用，使用opcode缓存的好处就越多。

		/**
		 * 根据域名来判断当前环境（默认是Kohana::DEVELOPMENT）。
		 */
		Kohana::$environment = ($_SERVER['SERVER_NAME'] !== 'localhost') ? Kohana::PRODUCTION : Kohana::DEVELOPMENT;
		/**
		 * 根据环境来初始化选项
		 */
		Kohana::init(array(
			'base_url'   => '/',
			'index_file' => FALSE,
			'profile'    => Kohana::$environment !== Kohana::PRODUCTION,
			'caching'    => Kohana::$environment === Kohana::PRODUCTION,
		));
