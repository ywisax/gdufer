# 什么是Kohana?

Kohana是一个开放源码, [面向对象](http://en.wikipedia.org/wiki/Object-oriented_programming) 和基于 [MVC](http://en.wikipedia.org/wiki/Model–view–controller "Model View Controller") 开发的 [PHP5](http://php.net/manual/intro-whatis "PHP Hypertext Preprocessor") [Web框架](http://en.wikipedia.org/wiki/Web_application_framework)，它的目标是提供一个安全轻便，且易于使用的开发框架，缩短开发周期。 

[!!] Kohana目前是基于 [BSD授权](http://kohanaframework.org/license) 的，所以你可以合法地在任何一种开源的，商业或个人项目上使用它。

## 是什么让 Kohana 如此强大？

强大的 [文件系统](files) 设计让我们可以轻松扩展我们的代码，只需要进行很少甚至不用任何 [配置](config)。
[错误回溯](errors) 功能能帮助你快速定位到代码中的错误。
还有 [调试功能](debugging) 和 [分析功能](profiling) 可以让你直观地观察到应用运行内部的数据。

为了保护你的应用，我们默认还包含了 [输入验证](security/validation), [带标识符的Cookies](security/cookies), [表单] 和 [HTML] 生成器功能。
[数据库](security/database) 层会自动对 [SQL注射](http://wikipedia.org/wiki/SQL_injection) 进行防范。
当然还有，我们所有的官方代码都是精心编写，并且经过了审查，确保安全的。

## 对文档做出你的共享

我们正在努力工作，以提供完整的文档。如果你要帮助我们完善这个文档, 请 [Fork我们的用户手册](http://github.com/kohana/userguide), 修改你熟悉的部分，并发送一个pull请求。
如果你不熟悉Git, 你也可以提交一个 [feature请求](http://dev.kohanaframework.org/projects/kohana3/issues) 给我们(需要注册)。

## 非正式文档

* 如果你在查找答案的时候遇到了文档中未提及的部分和其他未知的问题，那么你也可以去查看我们的 [非官方wiki](http://kerkness.ca/kowiki/doku.php) 试图查找。
* 同时，你也可以去我们的 [论坛](http://forum.kohanaframework.org/) 或者 [Stack Overflow](http://stackoverflow.com/questions/tagged/kohana) 去提问和获取你要的答案。
* 此外，你还可以在freenode [#kohana](irc://irc.freenode.net/kohana) IRC频道上与我们的开发者进行交流。
* 当然，更加友好的方法就是进入我们的论坛来线上求助，或者联系我们提供付费服务。

[!!] 在阅读的过程中，你可能发现部分代码不能使用或者找不到指定的文件，这是因为本文档所使用的`Kohana框架`已经经过了团队内部人员的若干次修改，已经与官方分支有了不少差异。请自行阅读代码和理解改动之处。
