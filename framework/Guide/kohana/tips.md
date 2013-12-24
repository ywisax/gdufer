# 技巧和常见错误

这里收集了一些你可以回遇到的问题和一些常见的错误。

## 不要修改`system`目录的文件！

永远不要修改`SYS_PATH`里面的文件！
Any change you want to make to files in system and modules can be made via the [cascading filesystem](files) and [transparent extension](extension) and won't break when you try to update your Kohana version.  

[!!] 爱折腾的孩子，会不会忽略这一点呢？

## 不要用一个路由来完成所有事情

Kohana3的[路由功能](routing)已经非常强大和易用了，所以你可以大胆地使用它来完成各种功能，例如实现一个漂亮的URL。

## 在某些系统中找不到文件

在Kohana3.3中，我们的文件系统和自动加载器遵循PSR-0命名规则。
This means that using the class Foo {} with a file in classes/foo.php will work on case-insensitive file systems (such as the default HFS+ FS used in Mac OS X) but will fail when used on a case-sensitive FS (typical on many production Linux servers).

## 处理大量的路由

加入你的应用是个十分复杂的应用，同时建立了很多的路由。
这时再把路由都放在 `bootstrap.php` 就很难管理了。
如果真有这样情况的话，我建议你新建个`routes.php`来专门存放你的路由规则，然后在`bootstrap.php`里面加入代码： `require_once APP_PATH.'Route'.EXT;`

## Reflection_Exception

如果打开网站时提示 `Reflection_Exception` 异常，那么很可能是因为你在 [Kohana::init] 设置的 'base_url' 错误了。
如果你确定这个选项是正确的，那么就很可能是你的 [路由](routing) 错误了。

	ReflectionException [ -1 ]: Class controller_<something> does not exist
	// where <something> is part of the url you entered in your browser

### 解决方案  {#reflection-exception-solution}

检查在[Kohana::init]中的`base_url`是否设置正确了。
这个选项是指Kohana的`index.php`到Web根目录的相对路径，别搞错咯。

## ORM/Session __sleep() bug

There is a bug in php which can corrupt your session after a fatal error.  A production server shouldn't have uncaught fatal errors, so this bug should only happen during development, when you do something stupid and cause a fatal error.  On the next page load you will get a database connection error, then all subsequent page loads will display the following error:

	ErrorException [ Notice ]: Undefined index: id
	MOD_PATH/ORM/Class/Kohana/ORM.php [ 1308 ]

### 解决方案   {#orm-session-sleep-solution}

To fix this, clear your cookies for that domain to reset your session.  This should never happen on a production server, so you won't have to explain to your clients how to clear their cookies.  You can see the [discussion on this issue](http://dev.kohanaframework.org/issues/3242) for more details.
