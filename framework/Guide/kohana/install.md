# 通用安装

[!!] 此页面只针对团队内部的`Kohana`，如果你使用的[Kohana主页](http://kohanaframework.org/)官方下载的版本，请自行摸索此页面的步骤，步骤差别不大。

1. 问团队成员索取最新的 **稳定** 版本。
2. 解压下载到的压缩包并创建一个名为 `kohana` 的目录。
3. 上传这个目录到你的服务器上（FTP，SSH，或者SVN）。
4. 打开 `APP_PATH/Init.php` 并修改以下代码：

	- 设置默认时区 [timezone](http://php.net/timezones)。

		~~~
			date_default_timezone_set('Asia/Shanghai');
		~~~

	- 在 [Kohana::init] 中设置 `base_url`，这个目录是Kohana安装相对于网站根目录来说的。

		~~~
			// 假设你的文件部署在/var/www/mywebsite
			// Apache的文档目录设置为/var/www
			Kohana::init(array(
				'base_url'   => '/mywebsite',
			));
		~~~

5. 确定`APP_PATH/Cache`和`APP_PATH/Log`这两个目录有可写权限

	~~~
		sudo chmod 777 -R APP_PATH/Cache
		sudo chmod 777 -R APP_PATH/Log
	~~~
	
6. 在浏览器打开站点URL，然后就可以测试你的框架啦

[!!] 因为平台差异，所以你可以会被一些权限相关的问题迷惑。一般来说，`Kohana`只要上面的两个目录为可写，其他目录不可写没大影响。

正常情况下，你可以看到下面的界面，如果没有绿色报错，那么说明你安装成功了。

![安装页面](install.png "安装成功后显示的页面")

[!!] 再次说明，你当前使用的`Kohana`版本不一定有这个文件，因为它非官方版。

当安装页面显示运行环境无错之后，你应该马上从Kohana目录中删除`install.php`。然后刷新访问看看：

![欢迎页面](welcome.png "默认欢迎页面")

恩，就是这么简单，不用什么复杂的部署和项目生成，你只需要复制下文件即可。

# 从GitHub安装Kohana

Kohana的[源码](http://github.com/kohana/kohana)放置在[GitHub](http://github.com)上。 你可以使用[git](http://git-scm.com/)来实时更新的框架代码。但是，如果你使用了本项目来正式开发了项目，那在你确保自己充分了解两个版本之前，请不要随便升级。

[!!] 要获取更多关于如何使用Git来安装Kohana的信息，请查看 [Working with Git](tutorials/git) 部分教程。
