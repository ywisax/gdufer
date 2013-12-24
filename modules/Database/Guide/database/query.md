# 创建请求

有两种不同的方法可以创建请求。
最简单的方法就是通过[DB::query]来创建一个[Database_Query]请求。
These queries are called [parameterized statements](query/parameterized) and allow you to set query parameters which are automatically escaped.
第二种方法就是通过请求生成器方法来构建。
具体可以看 [请求生成器](query/builder) 页面。

[!!] All queries are run using the `execute` method, which accepts a [Database] object or instance name. See [Database_Query::execute] for more information.
