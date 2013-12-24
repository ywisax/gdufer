# 级联文件系统

Kohana文件系统是一个分层结构，像目录结构那样级联。在kohana中，这个分层结构（当一个文件通过[Kohana::find_file]被加载时，会使用）按照下面的顺序：

1. **应用目录**  
   即为`index.php`中定义的`APP_PATH`，默认值为`application`.

2. **模块目录**  
   在`APP_PATH/Init.php`中，通过[Kohana::module]被设置为关联数组。数组中的每个值都可以 **按照在模块中定义的顺序** 被找到。

3. **系统目录**  
   即`SYS_PATH`，在`index.php`中定义，所有核心的文件都在这个目录中。

目录中的同名的文件，越靠近目录顶层，优先级越高。所以，同名的文件，可以通过放在高优先级的目录中，使其重载。

![级联文件系统信息图](cascading-filesystem-custom.png)

这张图片只是展示了某些文件，但是我们只是用它解释级联文件系统的例子：

* 如果Kohana捕获到了错误，它将会展示`Kohana/Error.php`视图，所以它将调用`Kohana::find_file('View', 'Kohana.Error')`，这个调用会返回`APP_PATH/View/Kohana/Error.php`，因为它的优先级比`SYS_PATH/View/Kohana/Error.php`高。通过这样做，我们就不需要去修改系统文件夹。

* 如果我们使用`View::factory('Welcome')`，它会调用`Kohana::find_file('View','Welcome')` ，并且返回`APP_PATH/View/Welcome.php`。因为它的优先级比`MOD_PATH/Common/View/Welcome.php`高。通过这样做，我们可以重写一个module做的事情，不用编辑modules文件。

* 如果我们使用Cookie类，[Kohana::auto_load] 将会调用 `Kohana::find_file('Class', 'cookie')` ，并且返回 `APP_PATH/Class/Cookie.php`。假设 `Cookie` 继承 `Kohana_Cookie`，自动加载器将会接着调用 `Kohana::find_file('Class','Kohana/Cookie')`，并且返回 `SYS_PATH/Class/Kohana/Cookie.php`，因为没有更高层级目录优先级的文件存在。这是一个清晰的[扩展示例](extension)。

* 如果我们使用 `View::factory('User')`，它会调用`Kohana::find_file('View','User')`，并且返回`MOD_PATH/Common/View/User.php`。

* 如果我们想修改`Config/Database.php`中的一些东西，我们可以拷贝这个文件到 `APP_PATH/Config/Database.php`，在这里做一些修改。记住，[合并了的配置文件](files/config#merge)要比通过级联优先级重载好。

## 文件类型

上级的目录是 `APP_PATH`，`MOD_PATH` 和 `SYS_PATH`，`SYS_PATH` 路径下，有下面几个默认的目录：

Class/
:  所有需要 [自动加载](autoloading) 的类都需要放在这里。 包括 [Controller](mvc/controllers)，[Model](mvc/models)，和所有其他的类。 所有的类都必须遵从 [类命名规范](conventions#class-names-and-file-location).

Config/
:  配置文件可以通过 [Kohana::$config] （或者[Kohana::config()]） 被加载，其中的选项以关联数组的形式返回。合并了的配置文件要比通过级联优先级重载好。请看 [配置文件](files/config) 寻找更多信息。

I18N/
:  翻译文件中的内容以关联数组的形式返回。通过 `__()` 方法进行翻译。要将"Hello, world!" 翻译成西班牙文， 你可以调用 `__('Hello, world!')` 通过设置 [I18n::$lang] 为"es-es"。合并了的I18n文件要比通过级联优先级重载好。 请看 [I18N页面](files/i18n) 寻找更多信息。

Message/
:  Message可以通过 [Kohana::message] 被加载，其中的字符串会以关联数组的形式返回。Message和i18n文件的不同在于没有被翻译，但是总是用默认的语言编写，并且只设计单个词。合并了的Message文件要比通过级联优先级重载好. 请看 [message files](files/messages) 寻找更多信息。

View/
:  视图是用来生成html或者其他输出的普通php文件。视图文件被加载到 [View] 对象，并且指定那些要转换成html片段的变量。多个视图可以互相调用。请看 [视图页面](mvc/views) 寻找更多信息。

*其它*
:  你可以包含任何其他的文件在你的级联文件系统中。例如`guide`、`vendor`、`media`和其他任何你想要的。例如，要在级联文件系统中找打 `media/logo.png`，你可以调用 `Kohana::find_file('media','logo','png')`。

## 查找文件

可以通过调用 [Kohana::find_file] 查找文件系统中的任何文件：

    // 查找文件"Class/Cookie.php"所在的全路径
    $path = Kohana::find_file('Class', 'Cookie');

    // 查找文件"views/User/Login.php"所在的全路径
    $path = Kohana::find_file('View', 'User/Login');
	
如果一个文件不是以 `.php` 结尾的，可以在第三个参数中传递文件的扩展名。

	// 查找文件"Guide/menu.md"所在的全路径
	$path = Kohana::find_file('Guide', 'menu', 'md');

	// 如果$name是"2000-01-01-first-post"，这将查找文件"Post/2000-01-01-first-post.textile"的全路径。
	$path = Kohana::find_file('Post', $name, '.textile');


## 第三方扩展

我们可以调用那些并不针对Kohana的第三方扩展或者额外的类库，它们在vendor文件夹下面，在application或者module下面都能发现这样的类库。
因为这些类库不尊从Kohana文件的命名规范，它们不会被kohana自动加载，所以需要手动包含。
第三方类库的例子有：[Markdown](http://daringfireball.net/projects/markdown/)、[DOMPDF](http://code.google.com/p/dompdf)、[Mustache](http://github.com/bobthecow/mustache.php) 和 [Swiftmailer](http://swiftmailer.org/)。

例如，如果你想使用 [DOMPDF](http://code.google.com/p/dompdf)，你需要把它拷贝到 `APP_PATH/Vendor/dompdf`，并且包含DOMPDF自动加载类。在一个控制器中，之前的方法是作为一个模块的Init.php时，或者是一个单例类的构造函数时，要注意。

    require Kohana::find_file('Vendor', 'dompdf/dompdf/dompdf_config','inc');

现在，你可以使用 DOMPDF 而不用包含其多余的文件：

    $pdf = new DOMPDF;

[!!] 如果你想把视图渲染成PDF，可以考虑使用[PDFView](http://github.com/shadowhand/pdfview)这个类库。团队其实更推荐使用[html2pdf](http://html2pdf.fr/en/default)这个类库
