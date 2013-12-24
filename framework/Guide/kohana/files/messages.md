# 消息文本

Kohana中有一个强大的基于K-V的查询系统，您可以使用它来定义系统消息或其它。

## 获取消息文本

使用Kohana::message()获取对应的消息文本：

	Kohana::message('forms', 'foobar');

这样，系统就会在`messages/forms.php`文件中查找`foobar`项：

	<?php
	
	return array(
		'foobar' => 'Hello, world!',
	);

你也可以这样查找子目录或者查找子项：

	Kohana::message('forms/contact', 'foobar.bar');

系统就会在`messages/forms/contact.php`文件中查找`[foobar][bar]`项：

	<?php
	
	return array(
		'foobar' => array(
			'bar' => 'Hello, world!',
		),
	);

## 附记

 * 不要在消息文件中使用`__()`，这样也可以工作，但可能有些意外情况出现。
 * 多个级别的消息文件会被级联文件系统同时合并，不会像类那样存在继承和覆盖关系。
