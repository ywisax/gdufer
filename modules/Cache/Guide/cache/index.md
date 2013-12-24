# 关于Kohana的缓存模块

[Kohana_Cache]提供了一个通用的缓存接口，用于完成多种方法和缓存引擎的具体实现。 
[Cache_Tagging] is supported where available natively to the cache system.
Kohana Cache supports multiple instances of cache engines through a grouped singleton pattern.

## 支持的缓存引擎

 *  APC ([Cache_Apc])
 *  File ([Cache_File])
 *  Memcached ([Cache_Memcache])
 *  Memcached-tags ([Cache_Memcachetag])
 *  SQLite ([Cache_Sqlite])
 *  Wincache

## 简单介绍

Caching should be implemented with consideration. Generally, caching the result of resources
is faster than reprocessing them. Choosing what, how and when to cache is vital. [PHP APC](http://php.net/manual/en/book.apc.php) is one of the fastest caching systems available, closely followed by [Memcached](http://memcached.org/). [SQLite](http://www.sqlite.org/) and File caching are two of the slowest cache methods, however usually faster than reprocessing
a complex set of instructions.

Caching engines that use memory are considerably faster than file based alternatives. But
memory is limited whereas disk space is plentiful. If caching large datasets, such as large database result sets, it is best to use file caching.

 [!!] Cache drivers require the relevant PHP extensions to be installed. APC, eAccelerator, Memecached and Xcache all require non-standard PHP extensions.

## 缓存能做什么（和做不了什么）

This module provides a simple abstracted interface to a wide selection of popular PHP cache engines.
The caching API provides the basic caching methods implemented across all solutions, memory, network or disk based.
Basic key / value storing is supported by all drivers, with additional tagging and garbage collection support where implemented or required.

_Kohana Cache_ does not provide HTTP style caching for clients (web browsers) and/or proxies (_Varnish_, _Squid_). There are other Kohana modules that provide this functionality.

## 选择适合你的引擎

Getting and setting values to cache is very simple when using the _Kohana Cache_ interface. The hardest choice is choosing which cache engine to use. When choosing a caching engine, the following criteria must be considered:

 1. __Does the cache need to be distributed?__
    This is an important consideration as it will severely limit the options available to solutions such as Memcache when a distributed solution is required.
 2. __Does the cache need to be fast?__
    In almost all cases retrieving data from a cache is faster than execution. However generally memory based caching is considerably faster than disk based caching (see table below).
 3. __How much cache is required?__
    Cache is not endless, and memory based caches are subject to a considerably more limited storage resource.

驱动			 |   存储	    | 速度		| Tags     | Distributed | 	支持自动垃圾回收	 | 备注
---------------- | ------------ | --------- | -------- | ----------- | ---------------------------- | -----------------------
APC              | __Memory__   | Excellent | 否       | 否          | 是 | Widely available PHP opcode caching solution, improves php execution performance
Wincache         | __Memory__   | Excellent | 否       | 否          | 是 | Windows variant of APC
File             | __Disk__     | Poor      | 否       | 否          | 否  | Marginally faster than execution
Memcache (tag)   | __Memory__   | Good      | 否 (yes) | 是         | 是 | Generally fast distributed solution, but has a speed hit due to variable network latency and serialization
Sqlite           | __Disk__     | Poor      | 是      | 否          | 否  | Marginally faster than execution

It is possible to have hybrid cache solutions that use a combination of the engines above in different contexts.
This is supported with _Kohana Cache_ as well

## 最低环境需求

 *  Kohana 3.0.4
 *  PHP 5.2.4 or greater
