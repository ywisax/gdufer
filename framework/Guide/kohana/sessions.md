# 会话

Kohana提供了一个内置的基于cookie或native session的会话机制。
它可以保存各种临时数据，通常用于请求间的数据传递。

会话机制可以保存临时或私密数据。
如果是非常重要的数据要使用会话保存，那么记得使用"database"或"native"适配器。
使用"cookie"适配器时，会话数据一定要进行加密。

[!!] For more information on best practices with session variables see [the seven deadly sins of sessions](http://lists.nyphp.org/pipermail/talk/2006-December/020358.html).

## Storing, Retrieving, and Deleting Data

[Cookie] and [Session] provide a very similar API for storing data. The main difference between them is that sessions are accessed using an object, and cookies are accessed using a static class.

Accessing the session instance is done using the [Session::instance] method:

    // Get the session instance
    $session = Session::instance();

When using sessions, you can also get all of the current session data using the [Session::as_array] method:

    // Get all of the session data as an array
    $data = $session->as_array();

You can also use this to overload the `$_SESSION` global to get and set data in a way more similar to standard PHP:

    // Overload $_SESSION with the session data
    $_SESSION =& $session->as_array();
    
    // Set session data
    $_SESSION[$key] = $value;

### 保存数据

使用`set`方法来保存会话数据：

    // 设置会话数据
    $session->set($key, $value);
	// 或
	Session::instance()->set($key, $value);

    // 保存user id
    $session->set('user_id', 10);

### 获取数据

使用`get`方法来获取会话数据：

    // 获取会话数据
    $data = $session->get($key, $default_value);

    // Get the user id
    $user = $session->get('user_id');

### 删除数据

使用`delete`方法来删除会话数据：

    // 删除会话数据
    $session->delete($key);

    // 从会话删除user_id
    $session->delete('user_id');

## 会话设置

Always check these settings before making your application live, as many of them will have a direct affect on the security of your application.

## 会话适配器

When creating or accessing an instance of the [Session] class you can decide which session adapter or driver you wish to use. The session adapters that are available to you are:

Native
: Stores session data in the default location for your web server. The storage location is defined by [session.save_path](http://php.net/manual/session.configuration.php#ini.session.save-path) in `php.ini` or defined by [ini_set](http://php.net/ini_set).

Database
: Stores session data in a database table using the [Session_Database] class. Requires the [Database] module to be enabled.

Cookie
: Stores session data in a cookie using the [Cookie] class. **Sessions will have a 4KB limit when using this adapter, and should be encrypted.**

The default adapter can be set by changing the value of [Session::$default]. The default adapter is "native".

To access a Session using the default adapter, simply call [Session::instance()].  To access a Session using something other than the default, pass the adapter name to `instance()`, for example: `Session::instance('cookie')`


### 适配器设置

You can apply configuration settings to each of the session adapters by creating a session config file at `APP_PATH/Config/Session.php`. The following sample configuration file defines all the settings for each adapter:

[!!] 如果使用的是Cookie适配器，"lifetime"设置为"0"的话，会话就会在关闭浏览器后失效。

    return array(
        'native' => array(
            'name' => 'session_name',
            'lifetime' => 43200,
        ),
        'cookie' => array(
            'name' => 'cookie_name',
            'encrypted' => TRUE,
            'lifetime' => 43200,
        ),
        'database' => array(
            'name' => 'cookie_name',
            'encrypted' => TRUE,
            'lifetime' => 43200,
            'group' => 'default',
            'table' => 'table_name',
            'columns' => array(
                'session_id'  => 'session_id',
        		'last_active' => 'last_active',
        		'contents'    => 'contents'
            ),
            'gc' => 500,
        ),
    );

#### Native Adapter

类型      | 设置      | 描述	                                          | 默认值
----------|-----------|---------------------------------------------------|-----------
`string`  | name      | name of the session                               | `"session"`
`integer` | lifetime  | number of seconds the session should live for     | `0`

#### Cookie适配器

类型      | 设置      | 描述	                                          | 默认值
----------|-----------|---------------------------------------------------|-----------
`string`  | name      | name of the cookie used to store the session data | `"session"`
`boolean` | encrypted | encrypt the session data using [Encrypt]?         | `FALSE`
`integer` | lifetime  | number of seconds the session should live for     | `0`

#### 数据库适配器

类型      | 设置      | 描述	                                          | 默认值
----------|-----------|---------------------------------------------------|-----------
`string`  | group     | [Database::instance] group name                   | `"default"`
`string`  | table     | table name to store sessions in                   | `"sessions"`
`array`   | columns   | associative array of column aliases               | `array`
`integer` | gc        | 1:x chance that garbage collection will be run    | `500`
`string`  | name      | name of the cookie used to store the session data | `"session"`
`boolean` | encrypted | encrypt the session data using [Encrypt]?         | `FALSE`
`integer` | lifetime  | number of seconds the session should live for     | `0`

##### 表结构

你首先需要创建一个数据库和表来存放会话数据。默认的结构如下：

    CREATE TABLE  `sessions` (
        `session_id` VARCHAR(24) NOT NULL,
        `last_active` INT UNSIGNED NOT NULL,
        `contents` TEXT NOT NULL,
        PRIMARY KEY (`session_id`),
        INDEX (`last_active`)
    ) ENGINE = MYISAM;

##### 表字段

You can change the column names to match an existing database schema when connecting to a legacy session table. The default value is the same as the key value.

session_id
: the name of the "id" column

last_active
: UNIX timestamp of the last time the session was updated

contents
: session data stored as a serialized string, and optionally encrypted
