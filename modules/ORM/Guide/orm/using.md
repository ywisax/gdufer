# 基础用法

## 加载一个新的模型实例

创建新的模型实例，有两种调用方法：

	$user = ORM::factory('User');
	// 或
	$user = new Model_User();

## 插入记录

要插入记录到数据库，要先创建一个模型实例：

	$user = ORM::factory('User');

然后，把数据依次传递给模型：

	$user->first_name = 'Trent';
	$user->last_name = 'Reznor';
	$user->city = 'Mercer';
	$user->state = 'PA';

调用[ORM::save]即可插入新记录到数据库。

	$user->save();

[!!] 如果当前实例没有指定主键（默认为`id`），那么 [ORM::save] 会执行一个`INSERT`查询，否则会执行一个`UPDATE`查询。

## 查找对象

要查找对象，你可以使用[ORM::find]方法也可以直接在初始化对象时传递参数到构造函数中去：

	// 查找ID为20的用户
	$user = ORM::factory('User')
		->where('id', '=', 20)
		->find();
	// 也直接直接传递参数
	$user = ORM::factory('User', 20);

[!!] 直接传递参数给构造函数，ORM会查找主键为该数值的记录，记得是查询`主键`。

## 检查记录是否已经加载

使用 [ORM::loaded] 方法来查询ORM对象是否查询成功：

	if ($user->loaded())
	{
		// 成功加载
	}
	else
	{
		// 出错
	}

## 更新和保存

查找到指定记录后，你可以直接使用以下代码来修改属性：

	$user->first_name = "Trent";
	$user->last_name = "Reznor";

等你觉得数据已经完全插入了，接下来使用`save()`来保存对象即可：

	$user->save();

## 删除记录

要删除数据十分简单，只要调用[ORM::delete]即可，当然你要先查找和加载到一个对象才能调用这个方法：

	$user = ORM::factory('User', 20);
	$user->delete();
	
## Mass assignment

To set multiple values at once, use [ORM::values]
	
	try
	{
		$user = ORM::factory('user')
			->values($this->request->post(), array('username','password'))
			->create();
	}
	catch (ORM_Validation_Exception $e)
	{
		// Handle validation errors ...
	}
	
[!!] Although the second argument is optional, it is *highly recommended* to specify the list of columns you expect to change. Not doing so will leave your code _vulnerable_ in case the attacker adds fields you didn't expect.
