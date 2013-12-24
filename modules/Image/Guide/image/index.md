# 图片模块

Kohana 3.x provides a simple yet powerful image manipulation module. The [Image] module provides features that allows your application to resize images, crop, rotate, flip and many more.

## 驱动

[Image] module ships with [Image_GD] driver which requires `GD` extension enabled in your PHP installation. This is the default driver. Additional drivers can be created by extending the [Image] class.

## 快速入门

Before using the image module, we must enable it first on `APP_PATH/Init.php`:

~~~
Kohana::module(array(
    ...
    'image' => MOD_PATH.'Image',
    ...
));
~~~

下一步： [学会使用图片模块](using).
