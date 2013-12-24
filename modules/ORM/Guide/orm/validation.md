# 校验

ORM models are tightly integrated with the [Validation] library and the module comes with a very flexible [ORM_Validation_Exception] that helps you quickly handle validation errors from basic CRUD operations.

## 定义规则

Validation rules are defined in the `ORM::rules()` method. This method returns the array of rules to be added to the [Validation] object like so:

	public function rules()
	{
		return array(
			'username' => array(
				// Uses Valid::not_empty($value);
				array('not_empty'),
				// Calls Some_Class::some_method('param1', 'param2');
				array('Some_Class::some_method', array('param1', 'param2')),
				// Calls A_Class::a_method($value);
				array(array('A_Class', 'a_method')),
				// Calls the lambda function and passes the field value and the validation object
				array(function($value, Validation $object)
				{
					$object->error('some_field', 'some_error');
				}, array(':value', ':validation')),
			),
		);
	}

### 绑定值

ORM模块会使用`Validation::bind()`来自动绑定以下值：

- **:field** - 当前使用到的列名
- **:value** - 当前列名的值
- **:model** - 当前要检验的模型实例

## 自动检验

当你调用`ORM::save()`、`ORM::update()`或`ORM::create()`时，ORM会自动检验一次输入的值。
如果检验不通过，会抛出一个[ORM_Validation_Exception]异常。参看以下代码：

	public function action_create()
	{
		try
		{
			$user = ORM::factory('User');
			$user->username = 'invalid username';
			$user->save();
		}
		catch (ORM_Validation_Exception $e)
		{
			$errors = $e->errors();
		}
	}

## 处理异常

The [ORM_Validation_Exception] will give you access to the validation errors that were encountered while trying to save a model's information. The `ORM_Validation_Exception::errors()` method works very similarly to `Validation::errors()`. Not passing a first parameter will return the name of the rules that failed. But unlike `Validate::errors()`, the first parameter of `ORM_Validation_Exception::errors()` is a directory path. The model's ORM::$_object_name will be appended to the directory in order to form the message file for `Validation::errors()` to use. The second parameter is identical to that of `Validation::errors()`.

In the below example, the error messages will be defined in `APP_PATH/Message/models/user.php`

	public function action_create()
	{
		try
		{
			$user = ORM::factory('User');
			$user->username = 'invalid username';
			$user->save();
		}
		catch (ORM_Validation_Exception $e)
		{
			$errors = $e->errors('models');
		}
	}

## 外部验证

Certain forms contain information that should not be validated by the model, but by the controller. Information such as a [CSRF](http://en.wikipedia.org/wiki/Cross-site_request_forgery) token, password verification, or a [CAPTCHA](http://en.wikipedia.org/wiki/CAPTCHA) should never be validated by a model. However, validating information in multiple places and combining the errors to provide the user with a good experience is often quite tedius. For this reason, the [ORM_Validation_Exception] is built to handle multiple Validation objects and namespaces the array of errors automatically for you. `ORM::save()`, `ORM::update()`, and `ORM::create()` all take an optional first parameter which is a [Validation] object to validate along with the model.

	public function action_create()
	{
		try
		{
			$user = ORM::factory('User');
			$user->username = $_POST['username'];
			$user->password = $_POST['password'];

			$extra_rules = Validation::factory($_POST)
				->rule('password_confirm', 'matches', array(
					':validation', ':field', 'password'
				));

			// Pass the extra rules to be validated with the model
			$user->save($extra_rules);
		}
		catch (ORM_Validation_Exception $e)
		{
			$errors = $e->errors('models');
		}
	}

Because the validation object was passed as a parameter to the model, any errors found in that check will be namespaced into a sub-array called `_external`. The array of errors would look something like this:

	array(
		'username'  => 'This field cannot be empty.',
		'_external' => array(
			'password_confirm' => 'The values you entered in the password fields did not match.',
		),
	);

This ensures that errors from multiple validation objects and models will never overwrite each other.

[!!] The power of the [ORM_Validation_Exception] can be leveraged in many different ways to merge errors from related models.你可以看下[示例页面](examples)中的例子来学习更高级的用法。
