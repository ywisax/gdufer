# 过滤器

本版本的ORM过滤器跟Kohana3.0.x中的*Validate*类差不多。
但是为了跟后来的版本修改匹配，我们也进行了很多改造。

当你对模型中的字段数据进行修改时，过滤器就开始起作用了，这样是为了避免那些未经过滤的数据进行到数据库。
Filters are defined the same way you define [rules](validation), as an array returned by the `ORM::filters()` method, like the following:

	public function filters()
	{
		return array(
			// Field Filters
			// $field_name => array(mixed $callback[, array $params = array(':value')]),
			'username' => array(
				// PHP Function Callback, default implicit param of ':value'
				array('trim'),
			),
			'password' => array(
				// Callback method with object context and params
				array(array($this, 'hash_password'), array(':value', Model_User::salt())),
			),
			'created_on' => array(
				// Callback static method with params
				array('Format::date', array(':value', 'Y-m-d H:i:s')),
			),
			'other_field' => array(
				// Callback static method with implicit param of ':value'
				array('MyClass::static_method'),
				// Callback method with object context with implicit param of ':value'
				array(array($this, 'change_other_field')),
				// PHP function callback with explicit params
				array('str_replace', array('luango', 'thomas', ':value'),
				// Function as the callback (PHP 5.3+)
				array(function($value) {
					// Do something to $value and return it.
					return some_function($value);
				}),
			),

		);
	}

[!!] 在过滤器中，你可能会使用以下默认参数：`:value`、`:field` 和 `:model`，他们分别对应`字段值`、`字段名`和`当前模型实例`。
