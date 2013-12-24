# 创建模型

在数据库中创建一个名为`members`的表，然后创建`APP_PATH/Class/Model/Member.php`，输入代码：

	class Model_Member extends ORM {
		...
	}

上面的例子很简单，还不足以说明ORM的强大，更多的可以自己摸索下。

## 自定义表名

如果你要自定义当前模型和数据库表名的关系，那么你可以直接覆盖以下变量：

	protected $_table_name = 'strange_tablename';

## 自定义主键名

ORM默认使用的主键ID是`id`，如果当前操作表的主键不是这个名称，那么你可以这样修改：

	protected $_primary_key = 'strange_pkey';

## 使用自定义的数据库配置组

假设为了安全和规范权限，你想要为模型使用一个特定的数据库配置组，那么可以使用以下代码：

	protected $_db_group = 'alternate';

[!!] 此处描述得还不够详细，你可以直接打开[Kohana_ORM]来查看更多代码和用法。最好的学习方法就是阅读代码。
