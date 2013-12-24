
我们鼓励你按照Kohana的编码风格来编写属于你的Kohana代码。
这样会让代码更加容易阅读和容易分享。

## 编码标准

为了产生高度一致的源代码，我们要求每个人都尽可能地按照编码标准。

### 括号

请使用 [BSD/Allman风格](http://en.wikipedia.org/wiki/Indent_style#BSD.2FAllman_style) 的括号。  

#### 大括号

大括号单独放在一行中，并缩进到相同水平的控制语句。如：

	// 正确
	if ($a === $b)
	{
		...
	}
	else
	{
		...
	}

	// 错误
	if ($a === $b) {
		...
	} else {
		...
	}

#### 类中的括号

括号规则中唯一例外的是，一个类的开始括号跟类名放在同一行。如：

	// 对
	class Foo {

	// 错
	class Foo
	{

#### 空括号

在空括号中不要放任何字符。

	// 对
	class Foo {}

	// 错
	class Foo { }

#### 数组的括号

数组可以单行或多行放置。

	array('a' => 'b', 'c' => 'd')
	
	array(
		'a' => 'b', 
		'c' => 'd',
	)

##### 左括号

数组的开始括号应该跟开始字符在同一行。如：

	// 对
	array(
		...
	)

	// 错
	array
	(
		...
	)

##### 闭括号

###### 一维数组

多行的一维数组的闭括号应该单独一行，并且跟数组开始处于同一缩进级别。

	// 对
	$array = array(
		...
	)

	// 错
	$array = array(
		...
		)

###### 多维数组

嵌套的数组应缩进一个Tab切换到右侧，具体可以参照下面例子：

	// 对
	array(
		'arr' => array(
			...
		),
		'arr' => array(
			...
		),
	)
	
	// 错
	array(
		'arr' => array(...),
		'arr' => array(...),
	)
	
##### 数组为方法参数


	// 对
	do(array(
		...
	))
	
	// 错
	do(array(
		...
		))

同时注意数组括号部分的开始，单行语法同样有效。

	// 对
	do(array(...))
	
	// 其他过长的行，降低到下一行
	do($bar, 'this is a very long line',
		array(...));

### 命名惯例

Kohana使用下划线命名，不使用驼峰命名法。

#### 类

	// 控制器类，使用 `Controller_` 前缀
	class Controller_Apple extends Controller {

	// 模型类，使用 `Model_` 前缀
	class Model_Cheese extends Model {

	// 普通类
	class Peanut {

当创建一个类的实例，如果你没有传递参数到构造函数，那么不使用括号：

	// 对
	$db = new Database;

	// 错
	$db = new Database();

#### 函数和方法

所有函数都应该使用小写字符，同时使用下划线来分割单词：

	function drink_beverage($beverage)
	{
	}

#### 变量

所有变量都应该使用小写字符，同时使用下划线来分割单词，不使用任何形式的驼峰命名：

	// 对
	$foo = 'bar';
	$long_example = 'uses underscores';

	// 错
	$weDontWantThis = 'understood?';

### 缩进

必须使用Tab制表符来缩进你的代码！
不用使用空格来缩进你的代码。

允许为保存垂直间距（在多行代码的情况下）而使用空格。
这是因为Tab没有良好的垂直对齐方式，不同的人有不同的标签宽度。

	$text = 'this is a long text block that is wrapped. Normally, we aim for '
		  .'wrapping at 80 chars. Vertical alignment is very important for '
		  .'code readability. Remember that all indentation is done with tabs,'
		  .'but vertical alignment should be completed with spaces, after '
		  .'indenting with tabs.';

### 字符串连接

在连接操作符两边不要使用空格：

[!!] YwiSax说，实际上，我觉得使用空格会好点的。

	// 对
	$str = 'one'.$var.'two';

	// 错
	$str = 'one'. $var .'two';
	$str = 'one' . $var . 'two';

### 单行声明

只有在那些会打破正常执行顺序的代码中才能使用当行IF声明。（如return或continue）：

	// 可以接受的代码：
	if ($foo == $bar)
		return $foo;

	if ($foo == $bar)
		continue;

	if ($foo == $bar)
		break;

	if ($foo == $bar)
		throw new Exception('You screwed up!');

	// 拒绝这样的代码！
	if ($baz == $bun)
		$baz = $bar + 2;

### 比较操作

使用 `AND` 和 `OR` 来进行比较：

	// 对
	if (($foo AND $bar) OR ($b AND $c))

	// 错
	if (($foo && $bar) || ($b && $c))
	
使用 `elseif`，不要使用 `else if`：

	// 对
	elseif ($bar)

	// 错
	else if($bar)

### Switch结构

每个 `case`，`break` 和 `default` 都应该在单独一行上。
每个 `case` 中的代码块都必须缩进一个Tab

	switch ($var)
	{
		case 'bar':
		case 'foo':
			echo 'hello';
		break;
		case 1:
			echo 'one';
		break;
		default:
			echo 'bye';
		break;
	}

[!!] 虽然 `case 1:` 跟 `case 1;` 在执行上没有区别，但是我们推荐统一使用前一种。

### 括号

There should be one space after statement name, followed by a parenthesis.
`!` 字符的两边必须留个空格， 以方便阅读。
Except in the case of a bang or type casting, there should be no whitespace after an opening parenthesis or before a closing parenthesis.

	// 对
	if ($foo == $bar)
	if ( ! $foo)

	// 错
	if($foo == $bar)
	if(!$foo)
	if ((int) $foo)
	if ( $foo == $bar )
	if (! $foo)

### 三元操作符

所有的三元操作符都应该遵循标准格式。
用括号包含其中的表达式，变量就不用什么了。如：

	$foo = ($bar == $foo) ? $foo : $bar;
	$foo = $bar ? $foo : $bar;

所有的比较和其他操作都必须在单独一个括号内完成：

	$foo = ($bar > 5) ? ($bar + $foo) : strlen($bar);

如果要分割一个复杂的三元操作语句时（第一部分超出了80个字符）为多行，应使用空格来缩进操作符，如：

	$foo = ($bar == $foo)
		 ? $foo
		 : $bar;

### 类型转换

类型强制转换时应加上空格来进行分割：

	// 对
	$foo = (string) $bar;
	if ( (string) $bar)

	// 错
	$foo = (string)$bar;

如果可以的话，最好使用类型转换来替换三元操作符：

	// 对
	$foo = (bool) $bar;

	// 错
	$foo = ($bar == TRUE) ? TRUE : FALSE;

在转换整数型或布尔型时，请使用它们的短名：

	// 对
	$foo = (int) $bar;
	$foo = (bool) $bar;

	// 错
	$foo = (integer) $bar;
	$foo = (boolean) $bar;

### 常量

常量必须定义为全部大写：

	// 对
	define('MY_CONSTANT', 'my_value');
	$a = TRUE;
	$b = NULL;

	// 错
	define('MyConstant', 'my_value');
	$a = True;
	$b = null;

在做比较时，请把常量放在后面：

	// 对
	if ($foo !== FALSE)

	// 错
	if (FALSE !== $foo)

这是一个略有争议的选择，所以我会解释我这样决定的理由。
如果我们把前面的例子写成可阅读的英语，那么它们读起来应该是：

	if variable $foo is not exactly FALSE

错误的用法，读起来就是：

	if FALSE is not exactly variable $foo

因为我们都是从左向右阅读的，如果我们把常量放在开头，那么读起来就有点怪怪的了。

### 注释

#### 单行注释

使用 `//` 来注释代码，在你要添加注释的代码的前一行开始添加注释。
在注释正文前添加一个空格，还有开头要大写（如果你是用英文注释的话）。
记住，任何情况下都不使用 `#`.

	// 正确

	//不正确
	// 不正确
	# 不正确

### 正则表达式

如果要使用正则表达式，请使用 `PCRE` 模式来代替 `POSIX` 模式。
通常情况下，`PCRE` 应该会比 `POSIX` 要快，同时功能更加强大。

	// 对
	if (preg_match('/abc/i', $str))

	// 错
	if (eregi('abc', $str))

在你的正则表达式中使用单引号，不要随便使用双引号。
单引号的字符串更容易使用，因为他们相对来说更简单。
但是它们也不同于双引号字符串， 他们不支持变量代换，也不支持反斜杠自动slash（如`\n`、`\t`这些字符）。

	// 对
	preg_match('/abc/', $str);

	// 错
	preg_match("/abc/", $str);

当使用正则表达式来查找和替换字符串时，请使用 `$n` 来代替 `\\n`，因为前者更像一个变量，容易让人理解。

	// 对
	preg_replace('/(\d+) dollar/', '$1 euro', $str);

	// 错
	preg_replace('/(\d+) dollar/', '\\1 euro', $str);

最后，请记得that the $ character for matching the position at the end of the line allows for a following newline character.
Use the D modifier to fix this if needed.
[阅读更多](http://blog.php-security.org/archives/76-Holes-in-most-preg_match-filters.html).

	$str = "email@example.com\n";

	preg_match('/^.+@.+$/', $str);  // TRUE
	preg_match('/^.+@.+$/D', $str); // FALSE


## 类名和文件位置

Kohana中的类名设计应该严格遵循，以契合 [自动加载](autoloading)。
类名第一个字母应为大写，并用下划线来分隔单词。
下划线是重要的，因为它们直接反映了文件在文件系统中的位置。

下列约定适用于：

1. CamelCased class names should be used when it is undesirable to create a new directory level.
2. All class file names and directory names must match the case of the class as per [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md).
3. All classes should be in the `classes` directory. This may be at any level in the [cascading filesystem](files).

### 例子  {#class-name-examples}

请记住，在一个类名中，一个下划线意味着一级目录。
你可以参考下面的例子：

类名                  | 文件路径
----------------------|-------------------------------
Controller_Template   | class/Controller/Template.php
Model_User            | class/Model/User.php
Model_BlogPost        | class/Model/BlogPost.php
Database              | class/Database.php
Database_Query        | class/Database/Query.php
Form                  | class/Form.php
