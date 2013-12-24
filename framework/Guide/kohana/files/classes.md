# 类文件

TODO：简要介绍类。

[模型](mvc/models)和[控制器](mvc/controllers)也是类文件，但Kohana处理他们的方式稍微有点不同。
你阅读他们各自的指南以了解更多信息。

## Helper还是Library呢？

在Kohana3之前的版本，我们没有明确区分"helper"类和"library"类。
他们全部都放在`classes/`目录下，同时有一样的规范准则。
通常来说，他们的区别只在于："helper"类静态调用（你可以看看[Kohana中的Helper](helpers)这一章节），"library"中的类通常会被实例化，并返回对象（参考[数据库查询生成器](../database/query/builder)）。
当然，这并不是说他们不是"helper"，那就只能是"library"。

## 创建一个类

创建一个类很简单，只需要按照[级联文件系统](files)和[编码规范](conventions#class-names-and-file-location)要求的范式来命名文件，并且放到对应的文件夹中去就可以了。如：
	// classes/Foobar.php
	
	class Foobar {
		static function magic() {
			// 你的代码
		}
	}
	
然后你就可以直接调用`Foobar::magic()`了，Kohana会[自动加载](autoloading)对应的类文件。

也可以放在多级目录中，如：

	// classes/Professor/Baxter.php
	
	class Professor_Baxter {
		static function teach() {
			// 你的代码
		}
	}
	
然后就可以直接调用`Professor_Baxter::teach()`啦。

如果你还有什么不明白，你可以直接打开'system'目录中的'classes'文件夹，观察下他们的规律。

## 类的命名空间

TODO: 关于是否在Kohana中使用PHP的新特性——命名空间，我们还在讨论当中。
