# 配置

最基础（默认）的配置文件在`MOD_PATH/Auth/Config/Auth.php`中。
如果你需要修改默认的配置选项，一般来说，在你未够熟悉之前，最好是复制一份到`APP_PATH/Config/Auth.php`再编辑，他们最后会合并了，请淡定。
这也是符合[级联文件系统](../kohana/files)约束的编码方式，最好大家都这样遵守。

下面是常见的配置选项：

名称 | 类型 | 默认  | 描述
-----|------|---------|------------
driver | `string` | file | The name of the auth driver to use.
hash_method | `string` | sha256 | The hashing function to use on the passwords.
hash_key | `string` | NULL | The key to use when hashing the password.
session_type | `string` | [Session::$default] | The type of session to use when storing the auth user.
session_key | `string` | auth_user | The name of the session variable used to save the user.
