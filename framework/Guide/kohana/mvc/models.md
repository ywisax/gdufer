# 模型

先看看Wikipedia的解释:

 > “数据模型”（Model）用于封装与应用程序的业务逻辑相关的数据以及对数据的处理方法。
 > “模型”有对数据直接访问的权力，例如对数据库的访问。
 > “模型”不依赖“视图”和“控制器”，也就是说，模型不关心它会被如何显示或是如何被操作。
 > 但是模型中数据的变化一般会通过一种刷新机制被公布。
 > 为了实现这种机制，那些用于监视此模型的视图必须事先在此模型上注册，从而，视图可以了解在数据模型上发生的改变。

创建一个简单的模型：

	class Model_Post extends Model
	{
		public function do_stuff()
		{
			// 这里填写你的逻辑代码...
		}
	}

如果你需要访问数据库，你需要在你的模型中继承 `Model_Database` 类：

	class Model_Post extends Model_Database
	{
		public function do_stuff()
		{
			// 这里填写你的逻辑代码...
		}

		public function get_stuff()
		{
			//从数据库中获取数据
			return $this->db->query(...);
		}
	}

如果你需要得到有关 CRUD/ORM 的帮助，可以查看 [ORM Module](../../guide/orm) 页面
