# 视图

视图就是那些包含了应用程序要输出的信息的文件。
一般这些文件都是HTML、CSS和Javascript代码，但是你也可以随便加入你想要的内容，如XML、JSON（用于AJAX输出）。
视图文件存在的目的是为了将信息和逻辑从你的应用中分离开来，使结构更清晰。

视图文件包含了传递给它的各类参数和部分PHP代码（也可能是模板语言，Twig、Smarty或其它）。
例如，传递一个包含了产品信息的数组，然后用一个table来渲染这些数据。
视图文件使用PHP来控制输出，所以你也可以在其中添加其它代码。
但无论如何，你最好是尽量保持不在视图中处理过多的逻辑，而是在控制器或模型中进行逻辑的处理，然后把数据传递给视图。

# 创建视图文件

视图文件存放在[文件系统](files)中的`View`文件夹中。
你也可以在`View`文件夹中创建子文件夹来保存视图文件。
正确的例子如下：

    APP_PATH/View/Home.php
    APP_PATH/View/Page/About.php
    APP_PATH/View/Product/Detail.php
    MOD_PATH/Foor/View/Error/404.php
    MOD_PATH/Common/View/Template.php

## 加载视图

[View]对象一般是在[控制器](mvc/controllers)中使用[View::factory]方法来生成的。
然后把这个视图对象赋给[Request::$response]属性或者其它的视图对象，如：

    public function action_about()
    {
        $this->response->body(View::factory('pages/about'));
    }

当一个视图被分配到[Response::body]，好比上面的例子，当需要时它会自动渲染。
直接调用[View::render]，或者把视图实例转换为字符串就可以进行视图的渲染了。
当一个视图被渲染时，视图文件会被加载然后生成最终的HTML。

    public function action_index()
    {
        $view = View::factory('pages/about');

        // 渲染视图
        $about_page = $view->render();

        // 直接转换为字符串也可以
        $about_page = (string) $view;

        $this->response->body($about_page);
    }

## 视图中的变量

视图文件被成功加载后，我们就可以使用[View::set]和[View::bind]来给其中的变量赋值了。如：

    public function action_roadtrip()
    {
        $view = View::factory('User/Roadtrip')
            ->set('places', array('Rome', 'Paris', 'London', 'New York', 'Tokyo'));
            ->bind('user', $this->user);

        // 下面的这个视图内含$places和$user两个变量
        $this->response->body($view);
    }

[!!] `set()`和`bind()`的区别，在于`bind()`赋值是直接饮用的。如果你在一个变量还没定义之前就`bind()`到视图中去，此时变量会自动建立，并被赋值为`NULL`。（PHP特性，非Kohana特性）

你也可以直接对`View`对象进行赋值，系统内部会自动调用`set()`来处理。如：

	public function action_roadtrip()
	{
		$view = View::factory('User/Roadtrip');
		$view->places = array('Rome', 'Paris', 'London', 'New York', 'Tokyo');
        $view->user = $this->user;

        $this->response->body($view);
	}

### 全局变量

一个应用中，可能会使用到多个视图文件，同时有着共同的变量。（如系统配置，登录用户信息）
比如，你要在所有页面的头部展示同样的标题。
要解决这个需求，你只需要调用[View::set_global]和[View::bind_global]来创建一个全局变量就可以。如：

    // 给所有视图赋予一个名为$page_title的变量
    View::bind_global('page_title', $page_title);

如果应用中，要使用到`template`，`template/sidebar`和`pages/home`来渲染其中的一个动作，那么我们可以使用模板功能来实现它。
首先，创建一个抽象控制器来实现模板功能，如：

    abstract class Controller_Website extends Controller_Template {

        public $page_title;

        public function before()
        {
            parent::before();

            // 赋予全局变量$page_title
            View::bind_global('page_title', $this->page_title);

            // 在模板中加载$sidebar
            $this->template->sidebar = View::factory('template/sidebar');
        }

    }

然后，`Controller_Home`继承`Controller_Website`：

    class Controller_Home extends Controller_Website {

        public function action_index()
        {
            $this->page_title = 'Home';

            $this->template->content = View::factory('pages/home');
        }

    }

## 视图间的嵌套

如果你想在视图中加载另一个视图，有两个方法可以解决。
一个是在视图中调用[View::factory]然后你就可以沙盒模拟加载了该视图。
这样的话，你就需要对这个新加载的视图进行其他的赋值。
	
	// 视图文件中：
	
    <?php echo View::factory('user/login')->bind('user', $user) ?>

另一个方法就是直接包含（include或require）该视图文件，这样就不用担心命名空间和变量的问题。

	// 视图文件中：
	
    <?php include Kohana::find_file('views', 'user/login') ?>

你也可以在控制器就对视图进行嵌套赋值。例如：

	// 控制器中：

	public functin action_index()
	{
		$view = View::factory('common/template);
		
		$view->title = "显示标题";
		$view->body = View::factory('pages/foobar');
	}
	
	// views/common/template.php:
	
	<html>
	<head>
		<title><?php echo $title></title>
	</head>
	
	<body>
		<?php echo $body ?>
	</body>
	</html>

还有，你也可以在视图中直接加载一个 [Request] ：

    <?php echo Request::factory('user/login')->execute() ?>

其实这就是\[HMVC]，这个功能十分强大，你可以在视图中加载指定URL的内容，并附加到当前视图中去。
