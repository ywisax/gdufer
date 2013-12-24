<?php defined('SYS_PATH') OR die('No direct script access.');

// Markdown解析器版本信息
defined('MARKDOWN_VERSION') OR define('MARKDOWN_VERSION', "1.0.1m");
defined('MARKDOWNEXTRA_VERSION') OR define('MARKDOWNEXTRA_VERSION', "1.2.3");

/**
 * Global default settings:
 */
// Change to ">" for HTML output
defined('MARKDOWN_EMPTY_ELEMENT_SUFFIX') OR define('MARKDOWN_EMPTY_ELEMENT_SUFFIX',  " />");

// Define the width of a tab for code blocks.
defined('MARKDOWN_TAB_WIDTH') OR define('MARKDOWN_TAB_WIDTH',     4 );

// Optional title attribute for footnote links and backlinks.
defined('MARKDOWN_FN_LINK_TITLE') OR define('MARKDOWN_FN_LINK_TITLE',         '' );
defined('MARKDOWN_FN_BACKLINK_TITLE') OR define('MARKDOWN_FN_BACKLINK_TITLE',     '' );

// Optional class attribute for footnote links and backlinks.
defined('MARKDOWN_FN_LINK_CLASS') OR define('MARKDOWN_FN_LINK_CLASS',         '' );
defined('MARKDOWN_FN_BACKLINK_CLASS') OR define('MARKDOWN_FN_BACKLINK_CLASS',     '' );

// Standard Function Interface
defined('MARKDOWN_PARSER_CLASS') OR define('MARKDOWN_PARSER_CLASS', 'Markdown_Extra');

if ( ! function_exists('Markdown'))
{
	/**
	 * 定义一个助手函数，初始化解析器，同时返回解析结果
	 */
	function Markdown($text)
	{
		static $parser;
		if ( ! isset($parser))
		{
			$parser_class = MARKDOWN_PARSER_CLASS;
			$parser = new $parser_class;
		}

		// 使用内部方法解析
		return $parser->transform($text);
	}
}
