# 控制器

控制器是介于模型和视图之间执行的应用程序。
它传递数据给模型进行处理，同时又从模型中获取处理后的数据，并且最后交给视图来渲染到终端。
控制器基本上控制应用程序流，但不执行最逻辑的代码。

[路由](../routing) 在匹配到URL之后，就会提交给 [请求](../request) 的 [Request::execute()] 方法来执行相应的控制器动作。
如果你不了解这些流程，你可以去进入 [路由](../routing) 页面去了解下路由的运行机制和使用。

## 创建控制器

为实现其功能，控制器必须做到以下几点：

* 存放在 `classes/Controller` 目录（或者更下面的子目录）
* 文件名必须与类名完全相同，如：`Controller_Article` 对应 `Articles.php`
* 控制器类名必须带有前缀 `Controller_`

你可以参照下下面的例子：

	// classes/Controller/Foobar.php
	class Controller_Foobar extends Controller {
	
	// classes/Controller/Admin.php
	class Controller_Admin extends Controller {

控制器也可以放在一个子目录中：

	// classes/Controller/Baz/Bar.php
	class Controller_Baz_Bar extends Controller {
	
	// classes/Controller/Product/Category.php
	class Controller_Product_Category extends Controller {
	
[!!] 记住，在子目录中存放的控制器，默认路由是访问不了的。你需要自定义一个包含有 [directory](routing#directory) 参数的路由，或设置默认的 [directory](routing#directory) 参数。

一个控制器可以继承其它控制器。

	// classes/Controller/Users.php
	class Controller_Users extends Controller_Template
	
	// classes/Controller/Api.php
	class Controller_Api extends Controller_REST
	
[!!] [Controller_Template] 是Kohana提供的一个控制器模板。

你也可以让一个控制器继承另外一个控制器，以共享一个通用的方法或属性。
好比有些方法是要求登录的，这样就能统一控制登录了。

	// classes/Controller/Admin.php
	class Controller_Admin extends Controller {
		// 在 `before()` 方法中添加检验用户登录的代码。
	}
	
	// classes/Controller/Admin/Plugins.php
	class Controller_Admin_Plugins extends Controller_Admin {
		// 因为这个类已经继承了 `Controller_Admin`，所以他不用再检查用户是否登录啦。
	}
		
## $this->request

每个控制器都有一个 `$this->request` 属性，它表示的是执行当前控制器的那个[请求]对象。
你通过它来获取更多关于当前请求的信息，同时通过使用它的`$this->response->body($ouput)`来设置输出内容。

下面列表，内含`$this->request`的部分属性和方法。
你也可以通过`Request::instance()`来访问这个属性和方法，这里的`$this->request`只是提供了一个快捷方式。
查看[Request]的API文档可以获取更多信息。

属性/方法 | 作用
--- | ---
[$this->request->route()](../api/Request#property:route) | 匹配当前URL的[Route]对象
[$this->request->directory()](../api/Request#property:directory), <br /> [$this->request->controller()](../api/Request#property:controller), <br /> [$this->request->action()](../api/Request#property:action) | 从当前路由中匹配到的目录、控制器和动作
[$this->request->param()](../api/Request#param) | 在路由中的其他参数

## $this->response
[$this->response->body()](../api/Response#property:body) | 当前请求要返回的内容
[$this->response->status()](../api/Response#property:status) | 当前请求要返回的HTTP状态码(200, 404, 500, etc.)
[$this->response->headers()](../api/Response#property:headers) | 当前请求要返回的HTTP头信息


## 动作

创建一个动作，就是为你的控制器创建一个以 `action_` 为前缀的公共方法。
那些没有定义为 `public` 和前缀不是 `action_` 的方法是不能被路由识别的。

系统会自动决定执行哪个动作。
规范的代码，应该会使用`$this->response->body($view)`是输出由[视图文件](mvc/views)渲染而来的HTML，最好不要直接在其中停止执行（exit）。

下面是一个最基础的动作实现，仅仅简单加载了 [view](mvc/views) file.

	public function action_hello()
	{
		$this->response->body(View::factory('hello/world')); // This will load views/hello/world.php
	}

### 参数

你可以通过调用 `$this->request->param('name')` 来访问路由中指定的参数，其中`name`为你要访问的键名。

	// 你可以试试添加 Route::set('example','<controller>(/<action>(/<id>(/<new>)))');
	
	public function action_foobar()
	{
		$id = $this->request->param('id');
		$new = $this->request->param('new');

如果参数没有被设置的话，那么会直接返回NULL。
当然，你也可以在调用时，设置第二个参数。
这样，当参数没有被设置时，就会返回你要求的返回值。

	public function action_foobar()
	{
		// 如果URL中没有包含 `$id` ，那么会返回 `FALSE`。
		$id = $this->request->param('user',FALSE);

### 例子

产品详细页面的查看动作。

	public function action_view()
	{
		$product = new Model_Product($this->request->param('id'));

		if ( ! $product->loaded())
		{
			throw HTTP_Exception::factory(404, 'Product not found!');
		}

		$this->response->body(View::factory('product/view')
			->set('product', $product));
	}

用户登录动作。

	public function action_login()
	{
		$view = View::factory('user/login');

		if ($this->request->post())
		{
			// 尝试登录
			if (Auth::instance()->login($this->request->post('username'), $this->request->post('password')))
			{
				$this->redirect('home', 302);
			}

			$view->errors = 'Invalid email or password';
		}

		$this->response->body($view);
	}

## before方法和after方法

你可以使用`before()`和`after()`来执行那些你需要在控制器动作执行前或执行后需要进行的操作。
例如，你需要检查用户是否登录，然后再决定是否加载一个视图或者其他文件。

你可以在Kohana中找到这样的代码，打开`Controller_Template`你可以看到下面类似的代码。

你可以检查当前执行的动作（通过`$this->request->action`），然后根据这个动作来执行其他操作，比如判断当前动作是否为login，不是的话就要求登录之类。

	// 在before中检查`auth/login`，然后看是否需要登录

	Controller_Admin extends Controller {

		public function before()
		{
			// 如果用户没有登录或者没有amdin role
			if ( ! Auth::instance()->logged_in('admin') AND $this->request->action !== 'login')
			{
				$this->redirect('admin/login', 302);
			}
		}
		
		public function action_login() {
			...

### 自定义 `__construct()` 方法

通常来说，你没必要更改`__construct()`函数，你完全可以在`before()`中执行你的操作。
如果你需要更改构造函数，请记得保留参数，否则PHP会报错。
这是因为只有这样，Request对象才能正确调用指定的控制器。
*再重申一次，绝大多数情况下，使用`before()`就可以了，不用修改构造器*，但如果你真的，*真的*更改，那么你可以参考下面这样来改：

	// You should almost never need to do this, use before() instead!

	// Be sure Kohana_Request is in the params
	public function __construct(Request $request, Response $response)
	{
		// You must call parent::__construct at some point in your function
		parent::__construct($request, $response);
		
		// 执行其它你要执行的动作
	}

## 继承其它控制器

TODO: 关于继承的更多描述和例子，还有关于多扩展。
