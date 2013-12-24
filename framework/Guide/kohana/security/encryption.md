# 加密

Kohana提供了一个[Encrypt]类来实现内置的加解密功能，这个助手类实质是[Mcrypt库](http://www.php.net/mcrypt)的封装。

要使用这个助手类，首先确保你的PHP已经加载了Mcrypt扩展。
你可以参考PHP主页上的[Mcrypt Installation page](http://www.php.net/manual/en/mcrypt.installation.php)这个文章。
Mcrypt扩展依赖于[libmcrypt](http://sourceforge.net/projects/mcrypt/files/)这个库。

接着，从`SYS_PATH/Config`文件中复制`Encryption.php`到你的`APP_PATH/Config`目录中去。

默认的配置文件大概内容如下：

    <?php defined('SYS_PATH') OR die('No direct script access.');

    return array(

        'default' => array(
            /**
            * The following options must be set:
            *
            * string   key     secret passphrase
            * integer  mode    encryption mode, one of MCRYPT_MODE_*
            * integer  cipher  encryption cipher, one of the Mcrpyt cipher constants
            */
            'cipher' => MCRYPT_RIJNDAEL_128,
            'mode'   => MCRYPT_MODE_NOFB,
        ),

    );


A couple of notes about the config.
首先，你可能have multiple first-level keys other than 'default' if you need to.
在这种情况下，the config file is similar to having multiple databases defined in your Config/Database.php file.
第二，notice there is no key provided.
You need to add that.
It is strongly recommended that you choose a high-strength random key using the [pwgen linux program](http://linux.die.net/man/1/pwgen)...

    shell> pwgen 63 1
    trwQwVXX96TIJoKxyBHB9AJkwAOHixuV1ENZmIWyanI0j1zNgSVvqywy044Agaj

...or by going to [GRC.com/passwords.htm](https://www.grc.com/passwords.htm).

## Complete Config Example

Here's a sample encryption configuration with three types of encryption defined. **If you copy this example, please change your keys!**

    <?php defined('SYS_PATH') OR die('No direct script access.');
    // APP_PATH/Config/Encrypt.php

    return array(

        'default' => array(
            'key'    => 'trwQwVXX96TIJoKxyBHB9AJkwAOHixuV1ENZmIWyanI0j1zNgSVvqywy044Agaj',
            'cipher' => MCRYPT_RIJNDAEL_128,
            'mode'   => MCRYPT_MODE_NOFB,
        ),
        'blowfish' => array(
            'key'    => '7bZJJkmNrelj5NaKoY6h6rMSRSmeUlJuTeOd5HHka5XknyMX4uGSfeVolTz4IYy',
            'cipher' => MCRYPT_BLOWFISH,
            'mode'   => MCRYPT_MODE_ECB,
        ),
        'tripledes' => array(
            'key'    => 'a9hcSLRvA3LkFc7EJgxXIKQuz1ec91J7P6WNq1IaxMZp4CTj5m31gZLARLxI1jD',
            'cipher' => MCRYPT_3DES,
            'mode'   => MCRYPT_MODE_CBC,
        ),
    );

You can view the available encryption ciphers and modes on your system by running...

    shell> php -r "print_r(get_defined_constants());" | grep MCRYPT

For more information on Mcrypt ciphers, visit [php.net/mcrypt.ciphers](http://us3.php.net/manual/en/mcrypt.ciphers.php).

## 基础使用

### Create an instance

To use the Encryption class, obtain an instance of the Encrypt class by calling it's *instance* method,
optionally passing the desired configuration group. If you do not pass a config group to the instance method,
the default group will be used.

    $encrypt = Encrypt::instance('tripledes');

### 加密数据

Next, encode some data using the *encode* method:

    $encrypt = Encrypt::instance('tripledes');
    $encrypted_data = $encrypt->encode('Data to Encode');
    // $encrypted_data now contains pCD5Z6oVdb9hbLxxV+FgGrhwVzZuhQoH

[!!] Raw encrypted strings usually won't print in a browser, and may not store properly in a VARCHAR or TEXT field. For this reason, Kohana's Encrypt class automatically calls base64_encode on encode, and base64_decode on decode, to prevent this problem.

[!!] One word of caution. The length of the encoded data expands quite a bit, so be sure your database column is long enough to store the encrypted data. If even one character is truncated, the data will not be recoverable.

### 解密数据

To decode some data, load it from the place you stored it (most likely your database) then pass it to the *decode* method:

    $encrypt = Encrypt::instance('tripledes');
    $decoded_string = $encrypt->decode($encrypted_data);
    echo $decoded_string;
    // prints 'Data to Encode'

You can't know in advance what the encoded string will be, and it's not reproducible, either.
That is, you can encode the same value over and over, but you'll always obtain a different encoded version,
even without changing your key, cipher and mode.  This is because Kohana adds some random entropy before encoding it with your value.
This ensures an attacker cannot easily discover your key and cipher, even given a collection of encoded values.
