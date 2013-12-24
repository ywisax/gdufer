# 错误和异常处理

Kohana provides both an exception handler and an error handler that transforms errors into exceptions using PHP's [ErrorException](http://php.net/errorexception) class.
Many details of the error and the internal state of the application is displayed by the handler:

1. 发生异常的文件
2. 错误级别
3. 错误信息
4. 错误的代码，还有错误的行数（高亮显示）
5. PHP执行代码的[回溯](http://php.net/debug_backtrace)
6. 已包含的文件、扩展和全局变量

## 例子

Click any of the links to toggle the display of additional information:

<div>{{userguide/examples/error}}</div>

## Disabling Error/Exception Handling

If you do not want to use the internal error handling, you can disable it (highly discouraged) when calling [Kohana::init]:

    Kohana::init(array('errors' => FALSE));

## 错误报告

By default, Kohana displays all errors, including strict mode warnings. This is set using [error_reporting](http://php.net/error_reporting):

    error_reporting(E_ALL | E_STRICT);

When you application is live and in production, a more conservative setting is recommended, such as ignoring notices:

    error_reporting(E_ALL & ~E_NOTICE);

If you get a white screen when an error is triggered, your host probably has disabled displaying errors. You can turn it on again by adding this line just after your `error_reporting` call:

    ini_set('display_errors', TRUE);

Errors should **always** be displayed, even in production, because it allows you to use [exception and error handling](debugging.errors) to serve a nice error page rather than a blank white screen when an error happens.

## HTTP异常处理

Kohana comes with a robust system for handing http errors. It includes exception classes for each http status code. To trigger a 404 in your application (the most common scenario):

	throw HTTP_Exception::factory(404, 'File not found!');

To register error pages for these, using 404 as an example:

    class HTTP_Exception_404 extends Kohana_HTTP_Exception_404 {

        public function get_response()
        {
            $response = Response::factory();

            $view = View::factory('errors/404');

            // We're inside an instance of Exception here, all the normal stuff is available.
            $view->message = $this->getMessage();

            $response->body($view->render());

            return $response;
        }

    }
