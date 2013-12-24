# Cross-Site Scripting (XSS)安全

*当前页面的介绍是不完整的，还不足以完整阐述XSS的产生和防范，所以程序猿你得下点功夫啊。*

要防范[XSS攻击](http://wikipedia.org/wiki/Cross-Site_Scripting)，第一步就是要知道为什么你要防范它。
XSS是一种隐藏在HTML中的恶意代码攻击手段，恶意代码可能是从输入或数据库中出来。
任何全局变量，只要是用户可控的，包括`$_GET`、`$_POST`和`$_COOKIE`数据，那么都有可能导致XSS攻击。

## 防范

有一些简单的防范方法可以保护你的应用程序，不受XSS攻击。
如果你觉得有一个变量，其中是不用包含HTML代码的，那么你使用[strip_tags](http://php.net/strip_tags)来移除那些可能有危害的HTML代码。

[!!] 如果你允许用户在你的应用中提交HTML，那么我们强烈建议你使用一些HTML检查器，如[HTML Purifier](http://htmlpurifier.org/)或[HTML Tidy](http://php.net/tidy)。

还有就是，往数据库或其他地方插入HTML代码时，永远记住要过滤。
Kohana的[HTML]助手类提供了生成器来生成各种标签，包括JS、样式表链接、超链接、图像和邮箱地址链接等。
总之记住，任何不信任的数据都应该使用[HTML::chars]来过滤。

## 引用

* [OWASP XSS Cheat Sheet](http://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet)