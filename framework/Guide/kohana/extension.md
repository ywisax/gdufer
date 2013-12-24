# 类的继承

[级联文件系统](files) 可以帮助开发者轻松地继承和扩展类的功能。
在这里，我们用 [Cookie]（`SYS_PATH/Class/Cookie.php`）这个类来作例子：

    class Cookie extends Kohana_Cookie {}

The default Kohana classes, and many extensions, use this definition so that almost all classes can be extended. You extend any class transparently, by defining your own class in `APP_PATH/Class/Cookie.php` to add your own methods.

[!!] You should **never** modify any of the files that are distributed with Kohana. Always make modifications to classes using transparent extension to prevent upgrade issues.

For instance, if you wanted to create method that sets encrypted cookies using the [Encrypt] class, you would create a file at `APP_PATH/Class/Cookie.php` that extends Kohana_Cookie, and adds your functions:

    <?php defined('SYS_PATH') OR die('No direct script access.');

    class Cookie extends Kohana_Cookie {

        /**
         * @var  mixed  default encryption instance
         */
        public static $encryption = 'default';

        /**
         * Sets an encrypted cookie.
         *
         * @uses  Cookie::set
         * @uses  Encrypt::encode
         */
         public static function encrypt($name, $value, $expiration = NULL)
         {
             $value = Encrypt::instance(Cookie::$encrpytion)->encode((string) $value);

             parent::set($name, $value, $expiration);
         }

         /**
          * 获取解密的Cookie值
          *
          * @uses  Cookie::get
          * @uses  Encrypt::decode
          */
          public static function decrypt($name, $default = NULL)
          {
              if ($value = parent::get($name, NULL))
              {
                  $value = Encrypt::instance(Cookie::$encryption)->decode($value);
              }

              return isset($value) ? $value : $default;
          }

    } // End Cookie

Now calling `Cookie::encrypt('secret', $data)` will create an encrypted cookie which we can decrypt with `$data = Cookie::decrypt('secret')`.

## 工作原理

To understand how this works, let's look at what happens normally.  When you use the Cookie class, [Kohana::autoload] looks for `classes/Cookie.php` in the [cascading filesystem](files).  It looks in `application`, then each module, then `system`. The file is found in `system` and is included.  Of course, `system/classes/Cookie.php` is just an empty class which extends `Kohana_Cookie`.  Again, [Kohana::autoload] is called this time looking for `Class/Kohana/Cookie.php` which it finds in `system`.

When you add your transparently extended cookie class at `APP_PATH/Class/Cookie.php` this file essentially "replaces" the file at `system/classes/Cookie.php` without actually touching it.  This happens because this time when we use the Cookie class [Kohana::autoload] looks for `classes/Cookie.php` and finds the file in `application` and includes that one, instead of the one in system.

## 例子：更改[Cookie]设置

If you are using the [Cookie](cookies) class, and want to change a setting, you should do so using transparent extension, rather than editing the file in the system folder.  If you edit it directly, and in the future you upgrade your Kohana version by replacing the system folder, your changes will be reverted and your cookies will probably be invalid.  Instead, create a Cookie.php file either in `APP_PATH/Class/Cookie.php` or a module (`MOD_PATH/<modulename>/Class/Cookie.php`).

	class Cookie extends Kohana_Cookie {
	
		// Set a new salt
		public $salt = "some new better random salt phrase";
		
		// Don't allow javascript access to cookies
		public $httponly = TRUE;
		
	}

## Example: TODO: an example

Just post the code and brief description of what function it adds, you don't have to do the "How it works" like above.

## Example: TODO: something else

Just post the code and brief description of what function it adds, you don't have to do the "How it works" like above.

## More examples

TODO: Provide some links to modules on github, etc that have examples of transparent extension in use.

## Multiple Levels of Extension

If you are extending a Kohana class in a module, you should maintain transparent extensions. In other words, do not include any variables or function in the "base" class (eg. Cookie). Instead make your own namespaced class, and have the "base" class extend that one. With our Encrypted cookie example we can create `MOD_PATH/MyMOD/Encrypted/Cookie.php`:

	class Encrypted_Cookie extends Kohana_Cookie {

		// Use the same encrypt() and decrypt() methods as above

	}

And create `MOD_PATH/MyMOD/Cookie.php`:

	class Cookie extends Encrypted_Cookie {}

This will still allow users to add their own extension to [Cookie] while leaving your extensions intact. To do that they would make a cookie class that extends `Encrypted_Cookie` (rather than `Kohana_Cookie`) in their application folder.
