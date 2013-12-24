原文档没有这个页面，我就在这里简单说下MVC好了。

关于MVC，具体的定义就不详细介绍了，大家可以去看看[百度百科的词条](http://baike.baidu.com/view/5432454.htm)和[维基百科的词条](http://zh.wikipedia.org/zh-cn/MVC)。他们讲述得比我更加专业。

在Kohana中，一个controller就是在models和views之间执行的class文件，算是中间层吧。
当数据改变时controlller把信息传递给modle，相应的需要数据时，controller也会像model请求。

当数据请求完毕后controller就将 数据交给view来展示给用户。

controller 是由 Request::execute() 调用的，不同的 `request` 根据Route策略调用不同的controller。

Model管理着应用所需要的数据及行为，并对特定的请求进行响应。
Model负责管理应用的所有数据，对于数据的处理和管理也应由model负责。

Controller和model都属于class，而view不是。View是包含了展示各方面信息的文件，通常来说view包含了HTML，CSS及Javascript，但是也可以是XML或者AJAX的JSON数据。

使用view的主要目的是剥离信息展示和应用逻辑之间的耦合，这样便能轻松复用并写出清晰的代码。

Kohana中的view依然是php文件，所以你可以随意编码，但是除了展示逻辑，其他的数据处理等逻辑还是交给model和controller吧。
