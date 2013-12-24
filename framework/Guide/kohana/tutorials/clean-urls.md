# 美化URL

从URL中删除`index.php`。

如果你想Kohana生成的URL更加整洁，那么首先就是把其中的`/index.php/`删除。
要达到目的，有两个步骤：

1. 编辑`APP_PATH/Init.php`
2. 设置URL重写

## 1. 配置初始化文件

首先在[Kohana::init]中把`index_file`项设置为`FALSE`：

    Kohana::init(array(
        'base_url'   => '/myapp/',
        'index_file' => FALSE,
    ));

这样操作之后，所有通过[URL::site]、[URL::base]和[HTML::anchor]生成的URL都不会再带有"index.php"啦。

## 2. URL重写

URL重写可能需要修改你的服务器配置。

## Apache

重命名`example.htaccess`为`.htaccess`，然后把其中的`RewriteBase`行修改为[Kohana::init]中设置的`base_url`。

    RewriteBase /myapp/

`.htaccess`文件会把所有请求都递交给`index.php`进行处理，除非对应的文件在服务器中已经存在（所有应用中的CSS、图片等只要路径正确，还是可以访问的）。
*基本上到这里，所有的工作已经完成了！*

### 404错误

如果你浏览的时候，页面返回"404 Not Found"错误，那么可能你的Apache未进行正确配置，所以读取不了`.htaccess`文件。

在你的Apache配置文件（一般名为`httpd.conf`）或虚拟主机配置文件中，检查其中的`AccessFileName`是否为`.htaccess`和`AllowOverride`是否设置为`All`。

		AccessFileName .htaccess

		<Directory "/var/www/html/myapp">
				AllowOverride All
		</Directory>


### 操作失败？

如果访问时提示"Internal Server Error"或"No input file specified"你可以尝试修改.htaccess：

    RewriteRule ^(?:application|modules|system)\b - [F,L]

替换为：

    RewriteRule ^(application|modules|system)/ - [F,L]

如果还不工作，那继续把：

    RewriteRule .* index.php/$0 [PT]

修改为：

    RewriteRule .* index.php [PT]
	
一般现在就可以了。

### 依然失败！

好吧，如果你到这里还是失败的话，那么首先你要确定你的服务器支持`mod_rewrite`模块。
如果你可以修改Apache配置文件（一般为`httpd.conf`），那么你可以在文件末尾添加以下代码：

    <Directory "/var/www/html/myapp">
        Order allow,deny
        Allow from all
        AllowOverride All
    </Directory>

同时，最好还检查下Apache的LOG文件，看看他的错误提示是什么，然后对应修复。

## NGINX

额，这里很难给出一个NGINX适用的通用例子，不过你可以参考下面的例子：

    location / {
        index     index.php index.html index.htm;
        try_files $uri index.php;
    }

    location = index.php {
        include       fastcgi.conf;
        fastcgi_pass  127.0.0.1:9000;
        fastcgi_index index.php;
    }

如果你在使用这部分的过程中还遇到其他问题，你可以激活系统的调试功能和检查错误日志，来查找对应的错误提示。
