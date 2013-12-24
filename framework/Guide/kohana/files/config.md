# 配置文件

配置文件一般用于保存一些静态的配置信息。
Kohana的配置文件是直接使用PHP编写的，保存在`Config/`目录中，代码就像这样：

    <?php defined('SYS_PATH') OR die('No direct script access.');

    return array(
        'setting' => 'value',
        'options' => array(
            'foo' => 'bar',
        ),
    );

如果你把上面的代码命名为`myconf.php`，那么你可以通过这样来调用它：

    $config = Kohana::config('myconf');
    $options = $config->get('options')

## 合并

配置文件跟[级联文件系统](../files)中的其他文件不同，对于重复文件，它会**合并**，而不是覆盖。
这就意味着所有同名的配置文件，会在Kohana中被合并成一个配置，并被用户调用。
The end result is that you can overload *individual* settings rather than duplicating an entire file.

例如，假设你想在Inflector配置中添加你自己的选项，我们不喜欢复制一个完整的配置文件过来，然后进行编辑，只需要：

    // Config/Inflector.php

    <?php defined('SYS_PATH') OR die('No direct script access.');

    return array(
        'irregular' => array(
            'die' => 'dice', // 默认配置中不存在
            'mouse' => 'mouses', // 覆盖默认配置文件中的 'mouse' => 'mice'
    );


## 创建你自己的配置文件

假设你正需要在一个配置文件中保存一些信息，如网站标题或Google Analytics的代码。
那么你可以先创建一个配置文件，命名为`site.php`：

    // Config/Site.php

    <?php defined('SYS_PATH') OR die('No direct script access.');

    return array(
        'title' => 'Kohana示范站',
        'analytics' => FALSE, // 在这里防止代码，设置为FALSE就是关闭Google Analytics的意思
    );

然后你就可以调用`Kohana::config('Site.title')`来获取站点名称，同样`Kohana::config('Site.analytics')`就可以获取代码。

假设你需要保存一些软件的版本信息。
那么你可以新建一个文件来保存它们，示例代码就是这样：

	// Config/Version.php

	<?php defined('SYS_PATH') OR die('No direct script access.');
	
    return array(
		'1.0.0' => array(
			'codename' => 'Frog',
			'download' => 'files/ourapp-1.0.0.tar.gz',
			'documentation' => 'docs/1.0.0',
			'released' => '06/05/2009',
			'issues' => 'link/to/bug/tracker',
		),
		'1.1.0' => array(
			'codename' => 'Lizard',
			'download' => 'files/ourapp-1.1.0.tar.gz',
			'documentation' => 'docs/1.1.0',
			'released' => '10/15/2009',
			'issues' => 'link/to/bug/tracker',
		),
		/// ... etc ...
	);

然后你就可以继续写下面的代码：

	// 控制器中
	$view->versions = Kohana::config('Versions');
	
	// 视图中
	foreach ($versions as $version)
	{
	}
