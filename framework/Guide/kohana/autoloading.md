# 加载类文件

Kohana在v3.3版本中全面支持[PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)自动加载命名规范。
然后你就可以享受PHP[自动加载](http://php.net/manual/language.oop5.autoload.php) 的特性啦，在调用类之前不用手动 [include](http://php.net/include) 或 [require](http://php.net/require) 类文件
当你要用到一个类时，Kohana会为你自动查找该类并加载它。
例如，当你要使用 [Cookie::set] 方法，你只需要直接调用就可以：

    Cookie::set('mycookie', 'any string value');

或者你要获取 [Encrypt] 的实例对象，那只需要调用 [Encrypt::instance]:

    $encrypt = Encrypt::instance();

[Kohana::auto_load]会根据类和文件之间的转换规则，自动加载类文件：

1. 文件存放在`classes/`目录下。
2. 把类名中的下划线替换为斜杠
2. 类名必须与文件名或目录名匹配

当调用一个还没有被加载的类（如：`Session_Cookie`），Kohana会使用 [Kohana::find_file] 方法在文件系统中查找路径为 `classes/Session/Cookie.php` 的文件。

如果你的类不遵循这Kohana特有的惯例，他们将不能被自动加载。
那时你就需要手工去include你的文件，或者添加你自己的[自动加载方法](http://us3.php.net/manual/en/function.spl-autoload-register.php)。

## 自定义自动加载器

Kohana的默认自动加载器是在 `APP_PATH/Init.php` 中被激活，并且是使用 [spl_autoload_register](http://php.net/spl_autoload_register) 来实现的：

    spl_autoload_register(array('Kohana', 'auto_load'));

执行了上述代码，系统在一个类不存在时，就会自动加载其文件，只要它符合了PSR-0命名规范。
如果你希望能支持旧版本的自动加载器（文件名全部使用小写），你可以在Kohana中再激活它：

    spl_autoload_register(array('Kohana', 'auto_load_lowercase'));


### 以Zend Framework为例

如果他们自身包含了自动加载器，那么我们可以轻易地访问到他们的类库。
下面以Zend Framework为例，讲解下如何在Kohana中调用Zend Framework的类库

#### 下载和安装Zend Framework

- [下载最新版本的Zend Framework](http://framework.zend.com/download/latest)。
- 创建 `APP_PATH/Vendor` 目录。这个文件夹保存第三方软件提供，独立于应用程序的类。
- 将解压缩后的Zend文件夹中（包含Zend Framework）移动到 `APP_PATH/APP_NAME/Vendor/Zend`。


#### 在你的bootstrap中加载Zend的自动加载器

在你的 `APP_PATH/Init.php` 中，复制以下代码：

	/**
	 * 激活Zend Framework自动加载器
	 */
	if ($path = Kohana::find_file('vendor', 'Zend/Loader'))
	{
	    ini_set('include_path',
	    ini_get('include_path').PATH_SEPARATOR.dirname(dirname($path)));

	    require_once 'Zend/Loader/Autoloader.php';
	    Zend_Loader_Autoloader::getInstance();
	}
	
#### 用法实例

现在，您可以在你的Kohana的应用程序自动加载任何Zend Framework的类。

	if ($validate($this->request->post()))
	{
		$mailer = new Zend_Mail;

		$mailer->setBodyHtml($view)
			->setFrom(Kohana::config('Site')->email_from)
			->addTo($email)
			->setSubject($message)
			->send();
	}
