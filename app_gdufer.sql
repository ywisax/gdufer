/*
Navicat MySQL Data Transfer

Source Server         : XAMPP-Mysql
Source Server Version : 50527
Source Host           : localhost:3306
Source Database       : app_gdufer_2

Target Server Type    : MYSQL
Target Server Version : 50527
File Encoding         : 65001

Date: 2013-12-26 00:42:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for xunsec_attachment
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_attachment`;
CREATE TABLE `xunsec_attachment` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `uid` int(10) NOT NULL COMMENT '提交者ID',
  `type` varchar(30) DEFAULT NULL COMMENT '附件类型',
  `file` varchar(200) NOT NULL COMMENT '文件路径，相对比本地的路径',
  `name` varchar(200) NOT NULL COMMENT '上传时使用的文件名',
  `ip` varchar(30) DEFAULT NULL COMMENT '提交者IP',
  `ua` varchar(100) DEFAULT NULL COMMENT '提交者浏览器标志',
  `date_created` int(10) NOT NULL COMMENT '创建日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=181 DEFAULT CHARSET=utf8 COMMENT='保存附件的表';

-- ----------------------------
-- Records of xunsec_attachment
-- ----------------------------
INSERT INTO `xunsec_attachment` VALUES ('1', '1000001', null, 'upload/2013/06/10/BMEuavSLry0UHVgLkgUw.jpg', '', null, null, '1370849854');

-- ----------------------------
-- Table structure for xunsec_attachment_log
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_attachment_log`;
CREATE TABLE `xunsec_attachment_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `attachment_id` int(10) DEFAULT NULL COMMENT '附件ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '发布者ID',
  `file` varchar(200) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `ip` varchar(20) NOT NULL,
  `ua` varchar(100) DEFAULT NULL,
  `date_created` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1446 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_attachment_log
-- ----------------------------
INSERT INTO `xunsec_attachment_log` VALUES ('1', null, '1000003', '/media/upload/2013/06/12/0wYxaZV2y3H9Ij5hNnco.doc', null, '127.0.0.1', null, '1371022223');

-- ----------------------------
-- Table structure for xunsec_block
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_block`;
CREATE TABLE `xunsec_block` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'BLOCK ID',
  `page_id` int(11) NOT NULL COMMENT '页面ID',
  `area` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `elementtype` int(11) NOT NULL,
  `element` int(11) NOT NULL COMMENT '元素ID',
  `date_created` int(10) NOT NULL COMMENT '添加日期',
  `date_updated` int(10) NOT NULL COMMENT '更新日期',
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COMMENT='CMS元素关系表';

-- ----------------------------
-- Records of xunsec_block
-- ----------------------------
INSERT INTO `xunsec_block` VALUES ('1', '1', '1', '3', '1', '1', '0', '0');
INSERT INTO `xunsec_block` VALUES ('7', '11', '1', '1', '1', '5', '0', '0');
INSERT INTO `xunsec_block` VALUES ('8', '13', '1', '1', '1', '7', '0', '0');
INSERT INTO `xunsec_block` VALUES ('15', '15', '1', '1', '1', '11', '0', '0');
INSERT INTO `xunsec_block` VALUES ('16', '18', '1', '1', '1', '13', '0', '0');
INSERT INTO `xunsec_block` VALUES ('17', '21', '1', '1', '1', '14', '0', '0');
INSERT INTO `xunsec_block` VALUES ('28', '20', '1', '1', '1', '22', '0', '0');
INSERT INTO `xunsec_block` VALUES ('29', '16', '1', '1', '1', '23', '0', '0');
INSERT INTO `xunsec_block` VALUES ('33', '25', '1', '1', '1', '28', '0', '0');
INSERT INTO `xunsec_block` VALUES ('34', '25', '1', '3', '1', '29', '0', '0');
INSERT INTO `xunsec_block` VALUES ('40', '24', '1', '5', '1', '31', '0', '0');
INSERT INTO `xunsec_block` VALUES ('41', '1', '1', '4', '1', '32', '1382891126', '0');
INSERT INTO `xunsec_block` VALUES ('42', '27', '1', '1', '1', '33', '1382893783', '0');
INSERT INTO `xunsec_block` VALUES ('43', '28', '1', '1', '1', '34', '1382894178', '0');
INSERT INTO `xunsec_block` VALUES ('44', '17', '1', '1', '1', '35', '1382895213', '0');
INSERT INTO `xunsec_block` VALUES ('48', '31', '1', '1', '1', '38', '1383918959', '0');

-- ----------------------------
-- Table structure for xunsec_book_buy
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_book_buy`;
CREATE TABLE `xunsec_book_buy` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `date_created` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图书求购';

-- ----------------------------
-- Records of xunsec_book_buy
-- ----------------------------

-- ----------------------------
-- Table structure for xunsec_book_category
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_book_category`;
CREATE TABLE `xunsec_book_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL,
  `count` int(10) NOT NULL DEFAULT '0',
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_book_category
-- ----------------------------
INSERT INTO `xunsec_book_category` VALUES ('1', '0', '学生教材', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('2', '0', '教辅考试', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('3', '0', '科学技术', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('4', '0', '经济管理', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('5', '0', '人文社科', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('6', '0', '文学艺术', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('7', '0', '生活休闲', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('8', '0', '音乐影像', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('9', '1', '文科教材', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('10', '1', '法律教材', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('11', '1', '理科教材', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('12', '1', '工科教材', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('13', '1', '农学教材', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('14', '1', '医学教材', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('15', '1', '财经教材', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('16', '1', '公共课教材', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('17', '1', '工具书', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('18', '2', '考研', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('19', '2', '外语', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('20', '2', '公务员', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('21', '2', '计算机类', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('22', '2', '财税保险类', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('23', '2', '建筑工程类', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('24', '2', '医药卫生类', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('25', '2', '艺术体育类', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('26', '2', '成人/自学类', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('27', '2', '其它类', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('28', '3', '科普', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('29', '3', '建筑', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('30', '3', '医学', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('31', '3', '农林', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('32', '3', '工业', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('33', '3', '计算机与网络', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('34', '3', '自然科学', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('35', '4', '管理', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('36', '4', '营销', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('37', '4', '保险', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('38', '4', '经济/金融', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('39', '4', '投资/理财', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('40', '5', '语言', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('41', '5', '文化', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('42', '5', '历史', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('43', '5', '法律', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('44', '5', '国学/古籍', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('45', '5', '政治/军事', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('46', '5', '哲学/宗教', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('47', '5', '励志/心理', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('48', '5', '社会科学', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('49', '6', '文学', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('50', '6', '小说', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('51', '6', '传记', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('52', '6', '青春', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('53', '6', '幽默/娱乐', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('54', '6', '艺术/摄影', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('55', '6', '动漫/绘本', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('56', '6', '杂志/期刊', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('57', '7', '家庭教育', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('58', '7', '家居休闲', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('59', '7', '美丽装扮', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('60', '7', '美食/养生', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('61', '7', '运动/健身', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('62', '7', '旅游/地图', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('63', '7', '收藏/鉴赏', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('64', '7', '两性/保健', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('65', '8', '音乐', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('66', '8', '影视', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('67', '8', '软件', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('68', '8', '游戏', '0', null, null);
INSERT INTO `xunsec_book_category` VALUES ('69', '8', '教育', '0', null, null);

-- ----------------------------
-- Table structure for xunsec_book_sale
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_book_sale`;
CREATE TABLE `xunsec_book_sale` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `poster_id` int(10) DEFAULT NULL,
  `poster_name` varchar(100) DEFAULT NULL,
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='要销售的书';

-- ----------------------------
-- Records of xunsec_book_sale
-- ----------------------------

-- ----------------------------
-- Table structure for xunsec_contact_info
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_contact_info`;
CREATE TABLE `xunsec_contact_info` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `realname` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `ip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of xunsec_contact_info
-- ----------------------------
INSERT INTO `xunsec_contact_info` VALUES ('1', 'ffff', 'fff', 'fdsfdsa', null, '0', '1384096309', '1384102137');

-- ----------------------------
-- Table structure for xunsec_element_content
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_element_content`;
CREATE TABLE `xunsec_element_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL COMMENT '名称，方便调用',
  `title` varchar(100) DEFAULT NULL COMMENT '一个简短的描述',
  `code` text NOT NULL,
  `markdown` int(1) unsigned NOT NULL DEFAULT '1',
  `twig` int(1) unsigned NOT NULL DEFAULT '1',
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_element_content
-- ----------------------------
INSERT INTO `xunsec_element_content` VALUES ('1', null, '首页nivoSlider部分', '{{ XunSec.style(\'nivo-slider/themes/default/default.css\') }}\n{{ XunSec.style(\'nivo-slider/nivo-slider.css\') }}\n{{ XunSec.script(\'nivo-slider/jquery.nivo.slider.pack.js\') }}\n{{ XunSec.script(\'js/nivo-slider-custom.js\') }}\n<div id=\"index-slider\" class=\"container\">\n	<div class=\"slider-wrapper theme-default\">\n		<div id=\"slider\" class=\"nivoSlider\">\n			<img src=\"http://gdufcdn.sinaapp.com/media/img/slider/slider1.jpg\" data-thumb=\"http://gdufcdn.sinaapp.com/media/img/slider/slider1.jpg\" alt=\"\" />\n			<a href=\"http://www.baidu.com\"><img src=\"http://gdufcdn.sinaapp.com/media/img/slider/slider2.jpg\" data-thumb=\"http://gdufcdn.sinaapp.com/media/img/slider/slider2.jpg\" alt=\"\" title=\"This is an example of a caption\" /></a>\n			<img src=\"http://gdufcdn.sinaapp.com/media/img/slider/slider3.jpg\" data-thumb=\"http://gdufcdn.sinaapp.com/media/img/slider/slider3.jpg\" alt=\"\" data-transition=\"slideInLeft\" />\n			<img src=\"http://gdufcdn.sinaapp.com/media/img/slider/slider4.jpg\" data-thumb=\"http://gdufcdn.sinaapp.com/media/img/slider/slider4.jpg\" alt=\"\" title=\"#htmlcaption\" />\n		</div>\n		<div id=\"htmlcaption\" class=\"nivo-html-caption\">\n			<strong>This</strong> is an example of a <em>HTML</em> caption with <a href=\"#\">a link</a>. \n		</div>\n	</div>\n</div>\n', '0', '1', null, '1384278500');
INSERT INTO `xunsec_element_content` VALUES ('5', null, '', '# 找不到该页面\n\nThe page could not be found.  You could try going [Home](/) or see our [Products](/products).\n\n{{ XunSec.status(404) }}', '1', '1', null, '1382857189');
INSERT INTO `xunsec_element_content` VALUES ('7', null, '兼职平台', '# Our story\n\n编辑中', '1', '0', null, '1383817278');
INSERT INTO `xunsec_element_content` VALUES ('11', null, null, '# Frequently Asked Questions\n\n#### Lorem Ipsum?\n\nLorem ipsum dolor sit amet consectetor\n\n#### Dolor sit?\n\nBlah blah blah blah blah\n', '1', '0', null, null);
INSERT INTO `xunsec_element_content` VALUES ('13', null, '站点地图调用', '{{ XunSec.site_map() }}', '0', '1', null, null);
INSERT INTO `xunsec_element_content` VALUES ('14', null, null, '# Header 1\nIf you login to the admin and edit this page you will see that it shows a variety of different elements and how you achieve them in [markdown syntax](http://kohanut.com/docs/using.markdown).  So without further ado, lots of different headers, tables, lists and quotes filled with various Latin phrases.  Enjoy!\n\n## Header 2\nLorem ipsum dolor sit amet, consectetur adipiscing elit.\n\n### Header 3\nLorem ipsum dolor sit amet, consectetur adipiscing elit.\n\n#### Header 4\nLorem ipsum dolor sit amet, consectetur adipiscing elit.\n\n##### Header 5\nLorem ipsum dolor sit amet, consectetur adipiscing elit.\n\n##### Header 6\nLorem ipsum dolor sit amet, consectetur adipiscing elit.\n\n----------\n\n## Blockquote\n\n> Praesent orci nisi, interdum quis, tristique vitae, consectetur sed, arcu. Ut at sapien non dolor semper sollicitudin. Etiam semper erat quis odio. Quisque commodo suscipit velit. Nulla facilisi.\n>\n> \\- *Duis justo quam*\n\n-----------\n\n## Code Segment\n\n    public function()\n    {\n       echo \"You create code by indenting several lines \" .\n            \"by 4 spaces, then all white space after that \" .\n            \"will be preserved.\"; \n    }\n\n-----------\n\n## Lists\n\n### Unsorted List\n*  Blandit in, interdum a\n*  Ultrices non lectus\n*  Nunc id odio\n*  Fusce ultricies\n\n### Ordered list\n1.  Blandit in, interdum a\n2.  Ultrices non lectus\n3.  Nunc id odio\n4.  Fusce ultricies\n   *  Sub list\n      1.  Etcetera\n\n### Definition List\nTitle\n:   Definition of something\n\nTitle 1\nTitle 2\n:   1- Lorem ipsum dolor sit amet.\n:   2- It can also mean this\n\n------------\n\n## Tables\n\n  Property 1   | Property 2     | Property 3    | Property 4\n---------------|----------------|---------------|--------------\n  Lorem ipsum  |Dolor sit amet  |Consectetuer   |Adipiscing elit\nDolor sit amet |Consectetuer    |Adipiscing elit|  Lorem ipsum\nConsectetuer   |Adipiscing elit |  Lorem ipsum  | Dolor sit amet\n', '1', '1', null, null);
INSERT INTO `xunsec_element_content` VALUES ('31', null, '产品介绍-树', '<div id=\"service-outer\">\n	<div id=\"service-inner\">\n		<div class=\"block left\">\n			<h2>金牌旅行社</h2>\n			<div class=\"content\">\n			金牌店长服务是基于阿里巴巴平台基础上推出托管服务，服务内容主要包括阿里巴巴诚信通客户产品信息优化、网上店铺设计、店铺日常管理等，能满足客户实现B2B推广交易的目标。\n			</div>\n		</div>\n		<div class=\"clear\"></div>\n		<div class=\"block left\">\n			<h2>云平台</h2>\n			<div class=\"content\">\n			金牌店长服务是基于阿里巴巴平台基础上推出托管服务，服务内容主要包括阿里巴巴诚信通客户产品信息优化、网上店铺设计、店铺日常管理等，能满足客户实现B2B推广交易的目标。\n			</div>\n		</div>\n		<div class=\"block right\">\n			<h2>一站通</h2>\n			<div class=\"content\">\n			金牌店长服务是基于阿里巴巴平台基础上推出托管服务，服务内容主要包括阿里巴巴诚信通客户产品信息优化、网上店铺设计、店铺日常管理等，能满足客户实现B2B推广交易的目标。\n			</div>\n		</div>\n		<div class=\"clear\"></div>\n		<div class=\"block center_wrapper\">\n			<h2>外贸通</h2>\n			<div class=\"content\">\n			金牌店长服务是基于阿里巴巴平台基础上推出托管服务，服务内容主要包括阿里巴巴诚信通客户产品信息优化、网上店铺设计、店铺日常管理等，能满足客户实现B2B推广交易的目标。\n			</div>\n		</div>\n	</div>\n</div>', '0', '0', null, null);
INSERT INTO `xunsec_element_content` VALUES ('22', null, null, '## Samples\n\n*  To see some basic content and snippet examples, click on \"Basic\" from the \"Samples\" category.\n*  The \"About\" link in the navigation doesn\'t actually represent a page, it just links to \"/about/story\".  Navigation items don\'t have to actually be a page.\n*  To see a sample of an External Link, click on \"External Link\" in the \"Samples\" category.  It\'s usually a bad idea to do this from the main nav, but it can be useful.\n*  To see an example of Integrating Kohana, you can see the [contact form](/contact), which uses a sub request, or the [override example](/samples/override).\n\n## Navigation Samples\n\nAs an example, the Home template has a secondary nav (on the right side of the page) with the header turned off, and the depth set to 2.  The code for this nav is like this:\n\n    {{ XunSec.nav(\'header=0, depth=2\') }}\n\nWhere as on all the other pages in this demo the nav has a header and a depth of 1, like this:\n\n    {{ XunSec.nav(\'header=1, header_class=nav-header, depth=1\') }}\n\nSee the complete list of the [navigation options].', '1', '0', null, null);
INSERT INTO `xunsec_element_content` VALUES ('23', null, null, '# Privacy Policy\n\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus nec justo a est rhoncus rutrum. Mauris vel vehicula diam. Phasellus tristique, augue nec convallis luctus, ante nisi commodo est, a feugiat lacus ipsum sit amet sem. Integer pretium condimentum ante, eu sagittis elit pretium pharetra. In congue semper viverra. Cras tincidunt ligula nec justo placerat vehicula. Vestibulum urna nisl, fermentum in blandit a, accumsan id massa. \n\n*   Praesent adipiscing scelerisque mi. Vivamus aliquam, neque sit amet ultrices viverra, ante lorem condimentum purus, sit amet dapibus purus est ut erat. Mauris odio orci, posuere nec malesuada at, sollicitudin eu mi. Suspendisse eget massa id ante porta sodales. \n*   Aliquam hendrerit dui vitae risus dignissim nec scelerisque dolor dapibus. \n*   Maecenas libero diam, euismod non porttitor a, sollicitudin non tortor. Proin massa leo, consectetur at dapibus non, bibendum ac velit. Sed posuere euismod neque, at ultrices mauris semper sed. Donec purus justo, malesuada sed interdum et, tempus vel sem.', '1', '0', null, null);
INSERT INTO `xunsec_element_content` VALUES ('28', null, null, '法撒旦范德萨范德萨', '1', '1', null, null);
INSERT INTO `xunsec_element_content` VALUES ('29', null, null, '飞阿嘎嘎嘎嘎嘎嘎灌灌灌灌灌灌灌灌灌灌', '1', '1', null, null);
INSERT INTO `xunsec_element_content` VALUES ('32', null, '首页-校训', '<div id=\"xiaoxun\">\n	<img src=\"http://gdufcdn.sinaapp.com/media/img/xiaoxun-1.png\" width=\"120\" height=\"60\" alt=\"勤奋\">\n	<img src=\"http://gdufcdn.sinaapp.com/media/img/xiaoxun-2.png\" width=\"120\" height=\"60\" alt=\"求是\">\n	<img src=\"http://gdufcdn.sinaapp.com/media/img/xiaoxun-3.png\" width=\"120\" height=\"60\" alt=\"廉洁\">\n	<img src=\"http://gdufcdn.sinaapp.com/media/img/xiaoxun-4.png\" width=\"120\" height=\"60\" alt=\"开拓\">\n</div>', '0', '0', '1382891126', '1384278371');
INSERT INTO `xunsec_element_content` VALUES ('33', null, '免责声明正文', '<div id=\"privacy-main\" class=\"row-fluid\">\n	<div class=\"span8 offset2\">\n		<div class=\"panel panel-default inner\">\n			<div class=\"panel-body\">\n				<h3>免责声明</h3>\n				<p>\n				用户在接受广金在线服务之前，请务必仔细阅读本条款并同意本声明。\n				访问者直接或通过站外引用等各类方式使用广金在线的行为，都将被视作是对本声明全部内容的无异议的认可。\n				如有异议，请立即与社区服务人员联系，并停止使用网站服务（包括网站功能和网站内容）。\n				</p>\n				<ul class=\"statement-content\">\n					<li>用户以各种方式使用广金在线服务和数据（包括但不限于发表、宣传介绍、转载、浏览及利用广金在线用户发布内容）的过程中，不得以任何方式利用广金在线直接或间接从事违反中国法律和社会公德的行为；</li>\n					<li>遵守广金在线以及与之相关的网络服务的协议、指导原则、管理细则等；不得干扰、损害和侵犯广金在线及其会员的各种合法权利与利益。广金在线有权对违反上述承诺的内容予以删除；</li>\n					<li>用户在广金在线发表的内容仅表明其个人的立场和观点，并不代表广金在线的立场或观点。作为内容的发表者，需自行对所发表内容负责，因所发表内容引发的一切纠纷，由该内容的发表者承担全部法律及连带责任。广金在线不承担任何法律及连带责任；</li>\n					<li>广金在线只审核内容合法与否，不审核内容真实性，请内容参与者自行审核，并对自己的参与行为及由此可能产生的法律风险负全部责任。</li>\n					<li>社区原创作品版权归属原作者所有，广金在线拥有使用权，可合理用于宣传、推广及刊发。未经允许不得转载，凡是要转载请预先通知网站管理员，取得同意后方可转载，否则不得任意转载或用于其他用途。</li>\n					<li>广金在线有权随时变更或中断或终止部分或全部网络服务。为了网站的正常运行，广金在线定期或不定期地对提供网络服务的平台（如互联网网站、移动网络等）或相关的设备进行检修或者维护而造成合理时间内的服务中断，广金在线无需为此承担任何责任。</li>\n					<li>广金在线会实时收集浏览者的访问记录，数据可能用于科研目的，不涉及用户隐私，同时广金在线承诺数据不做其他用途。</li>\n				</ul>\n				<p>对免责声明的解释、修改及更新权均属广金在线所有。</p>\n			</div>\n		</div>\n	</div>\n</div>\n', '1', '1', '1382893783', null);
INSERT INTO `xunsec_element_content` VALUES ('34', null, '加入我们的正文代码', '{{ XunSec.style(\'css/team.css\') }}\n<div id=\"team\" class=\"row-fluid\">\n	<h1 class=\"span12 header\">GDUFER团队</h1>\n	<div class=\"span10 offset1 desc\">\n		<img src=\"/media/img/jobs/jobs01.png\">\n		<div class=\"text\">\n			<p> </p>\n			<p> </p>\n			<p class=\"line1\">我们是技术控，我们热爱生活</p>\n			<p class=\"line2\">只要你对新技术有着执着和狂热</p>\n			<p class=\"line3\">那么GDUDER就有属于你的位置！</p>\n		</div>\n	</div>\n</div>\n<hr>\n<div id=\"jobs\" class=\"row-fluid\">\n	<div class=\"span1 offset1 php\">\n		<a role=\"button\" data-toggle=\"modal\" href=\"#php-modal\">PHP工程师</a>\n	</div>\n	<div class=\"span1 front\">\n		<a role=\"button\" data-toggle=\"modal\" href=\"#ued-modal\">前端设计师</a>\n	</div>\n	<div class=\"span1 linux\">\n		<a role=\"button\" data-toggle=\"modal\" href=\"#linux-modal\">Linux运维</a>\n	</div>\n	<div class=\"span1 market\">\n		<a role=\"button\" data-toggle=\"modal\" href=\"#mp-modal\">营销策划</a>\n	</div>\n	<div class=\"span8 more\">\n		<p>更多职位留待你发掘...</p>\n	</div>\n</div>\n<hr>\n<div id=\"contact\" class=\"block\">\n	<p>如果你对自己有信心，那就放心地投简历吧，Believe you can !</p>\n	<p class=\"email\"><a href=\"mailto:admin@gdufer.com\">admin@gdufer.com</a></p>\n</div>\n<!-- Modal -->\n<div id=\"php-modal\" class=\"modal hide fade\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"php-modal-label\" aria-hidden=\"true\">\n	<div class=\"modal-header\">\n		<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\n		<h3 id=\"php-modal-label\">PHP工程师</h3>\n	</div>\n	<div class=\"modal-body\">\n		<p>工作职责</p>\n		<ul>\n			<li>负责日常站点功能的维护和开发。</li>\n			<li>按照需要编写开发文档。</li>\n			<li>定期总结和做Code Review。</li>\n		</ul>\n	</div>\n	<div class=\"modal-footer\">\n		<button class=\"btn btn-primary\" data-dismiss=\"modal\" aria-hidden=\"true\">关闭</button>\n	</div>\n</div>\n<div id=\"ued-modal\" class=\"modal hide fade\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"ued-modal-label\" aria-hidden=\"true\">\n	<div class=\"modal-header\">\n		<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\n		<h3 id=\"ued-modal-label\">前端设计师</h3>\n	</div>\n	<div class=\"modal-body\">\n		<p>工作职责</p>\n		<ul>\n			<li>根据美工设计出来的效果图分割成最终HTML</li>\n			<li>优化页面流，努力提高加载速度</li>\n			<li>给页面设计一些漂亮又实用的小特效</li>\n		</ul>\n	</div>\n	<div class=\"modal-footer\">\n		<button class=\"btn btn-primary\" data-dismiss=\"modal\" aria-hidden=\"true\">关闭</button>\n	</div>\n</div>\n<div id=\"linux-modal\" class=\"modal hide fade\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"linux-modal-label\" aria-hidden=\"true\">\n	<div class=\"modal-header\">\n		<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\n		<h3 id=\"linux-modal-label\">Linux运维</h3>\n	</div>\n	<div class=\"modal-body\">\n		<p>工作职责</p>\n		<ul>\n			<li>定期检查服务器，保证站点稳定运行</li>\n			<li>定期检查服务器安全性</li>\n			<li>不用加班不用熬夜，偶尔负责拿外卖</li>\n		</ul>\n	</div>\n	<div class=\"modal-footer\">\n		<button class=\"btn btn-primary\" data-dismiss=\"modal\" aria-hidden=\"true\">关闭</button>\n	</div>\n</div>\n<div id=\"mp-modal\" class=\"modal hide fade\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"mp-modal-label\" aria-hidden=\"true\">\n	<div class=\"modal-header\">\n		<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\n		<h3 id=\"mp-modal-label\">营销策划</h3>\n	</div>\n	<div class=\"modal-body\">\n		<p>工作职责</p>\n		<ul>\n			<li>带动站点会员的线上活跃度</li>\n			<li>线下尽量跟会员多多互动</li>\n			<li>如果你能吸引萌妹子，或者你本身就是萌妹子，那就更好了</li>\n		</ul>\n	</div>\n	<div class=\"modal-footer\">\n		<button class=\"btn btn-primary\" data-dismiss=\"modal\" aria-hidden=\"true\">关闭</button>\n	</div>\n</div>\n', '0', '1', '1382894178', '1383756624');
INSERT INTO `xunsec_element_content` VALUES ('35', null, '校史timeline', '{{ XunSec.style(\'css/timeline.css\') }}\n{{ XunSec.script(\'timelinr/jquery.timelinr-0.9.53.js\') }}\n{{ XunSec.script(\'js/timeline.js\') }}\n<div id=\"timeline\">\n	<ul id=\"dates\">\n		<li><a href=\"#2013\">2013</a></li>\n		<li><a href=\"#2011\">2011</a></li>\n		<li><a href=\"#2004\">2004</a></li>\n		<li><a href=\"#2003\">2003</a></li>\n		<li><a href=\"#2002\">2002</a></li>\n		<li><a href=\"#1951\">1951</a></li>\n		<li><a href=\"#1950\">1950</a></li>\n	</ul> \n	<ul id=\"issues\">\n		<li id=\"2013\">\n			<h1>广金欢迎你！</h1>\n			<p>如果你还不熟悉广金的发展历程，可以在左侧菜单处细细查看广金的发展。</p>\n			<p>真心希望2013年到来的你们，能给广金带来新气象！</p>\n		</li>\n		<li id=\"2011\">\n			<h1>新的起点</h1>\n			<p>广东金融学院成为硕士专业学位研究生培养试点单位，专业学位类别为“金融硕士”。</p>\n		</li>\n		<li id=\"2004\">\n			<h1>升本，腾飞</h1>\n			<p>3月27日 教育部高等院校设置评议委员会召开全体会议，在与会专家的投票表决中，以全票通过了该校升格为广东金融学院的申请。</p>\n			<p>5月13日 教育部颁发了《关于同意在广州金融高等专科学校基础上建立广东金融学院的通知》（教发函 【 2004 】 92 号），正式批准该校升格为广东金融学院。</p>\n			<p>6月26日 广东金融学院成立。</p>\n		</li>\n		<li id=\"2003\">\n			<h1>腾飞的前奏</h1>\n			<p>8月 省委、省政府正式拟文上报国家教育部，请求申办广东金融学院。</p>\n		</li>\n		<li id=\"2002\">\n			<h1>预备</h1>\n			<p>10月 学校向省委、省政府、省教育厅正式提出申办广东金融学院的请求。</p>\n		</li>\n		<li id=\"1951\">\n			<h1>第一次更名</h1>\n			<p>更名为中国人民银行广东省分行银行学校。</p>\n		</li>\n		<li id=\"1950\">\n			<h1>广金前身的诞生</h1>\n			<p>中国人民银行华南分区行银行学校正式成立。</p>\n		</li>\n	</ul>\n	<a href=\"#\" id=\"next\">+</a> <!-- optional -->\n	<a href=\"#\" id=\"prev\">-</a> <!-- optional -->\n</div>', '0', '1', '1382895213', '1383552202');
INSERT INTO `xunsec_element_content` VALUES ('38', null, '', '<div class=\"inner row-fluid\">\n	<div id=\"content\" class=\"span12\">\n		<div id=\"gduf-login\" class=\"row-fluid\">\n			<div class=\"offset3 span6\" id=\"gduf-login-block\">\n				<form method=\"post\" id=\"gduf-login-form\" class=\"form-horizontal well login-form\"\n					data-login-callback=\"/gduf/proxy_login\"\n					data-mail-list-callback=\"/gduf/proxy_mail_list\"\n					data-logout-callback=\"/gduf/proxy_logout\"\n					data-mail-read-callback=\"/gduf/proxy_mail_read\"\n				>\n					<fieldset>\n						<legend>\n							登陆新版校内邮箱\n							<small class=\"pull-right\"><a target=\"_blank\" href=\"http://www.gduf.edu.cn/\">使用旧版邮箱</a></small>\n						</legend>\n						<div class=\"control-group\">\n							<div class=\"control-label\">\n								<label for=\"gduf-username\">用户名：</label>\n							</div>\n							<div class=\"controls\">\n								<input type=\"text\" id=\"gduf-username\" name=\"username\" placeholder=\"用户名\" class=\"input-large\" required />\n							</div>\n						</div>\n						<div class=\"control-group\">\n							<div class=\"control-label\">\n								<label for=\"gduf-password\">密 码：</label>\n							</div>\n							<div class=\"controls\">\n								<input type=\"password\" id=\"gduf-password\" name=\"password\" placeholder=\"密 码\" class=\"input-large\" required />\n							</div>\n						</div>\n						<div class=\"control-group\">\n							<div class=\"controls\">\n								<a id=\"gduf-login-new\" class=\"btn btn-primary\">登陆到新版</a>\n								<a id=\"gduf-login-old\" class=\"btn\">登陆到旧版</a>\n							</div>\n						</div>\n						<div class=\"control-group notice\">\n							<p><span class=\"label label-warning\">提示</span> 目前本邮箱系统还只支持读信功能，暂未开放发送和回复功能（防垃圾邮件）。</p>\n						</div>\n					</fieldset>\n				</form>\n			</div>\n		</div>\n		<div id=\"gduf-mailbox\" class=\"row-fluid\" style=\"display:none;\">\n			<div id=\"gduf-menu\" class=\"span2\">\n				<div class=\"well\">\n					<ul class=\"unstyled\">\n						<li class=\"write\"><a href=\"#send-modal\" data-toggle=\"modal\">写邮件</a></li>\n						<li class=\"inbox\"><a href=\"#\">收信箱</a></li>\n						<li class=\"outbox\"><a href=\"#\">发信箱</a></li>\n						<li class=\"draft\"><a href=\"#\">草稿箱</a></li>\n						<li class=\"trash\"><a href=\"#\">垃圾箱</a></li>\n						<li class=\"logout\"><a href=\"#\">退出邮箱</a></li>\n					</ul>\n				</div>\n			</div>\n			<div id=\"mail-content\" class=\"span10\">\n			</div>\n		</div>\n		<!-- 阅读邮件 -->\n		<div id=\"mail-modal\" class=\"modal hide fade\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"mail-modal-label\" aria-hidden=\"true\">\n			<div class=\"modal-header\">\n				<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\n				<div id=\"mail-modal-label\">阅读邮件：<span>标题</span></div>\n			</div>\n			<div class=\"modal-body\">\n				<p>邮件正文</p>\n			</div>\n			<div class=\"modal-footer\">\n				<button data-id=\"0\" class=\"reply btn btn-primary\">答复</button>\n				<button data-id=\"0\" class=\"forward btn\">转发</button>\n				<button data-id=\"0\" class=\"delete btn\">删除</button>\n				<button data-id=\"0\" class=\"delete1 btn\">永久删除</button>\n				<button class=\"back btn\" data-dismiss=\"modal\" aria-hidden=\"true\">返回</button>\n			</div>\n		</div>\n		<!-- 发送邮件 -->\n		<div id=\"send-modal\" class=\"modal hide fade\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"send-modal-label\" aria-hidden=\"true\">\n			<div class=\"modal-header\">\n				<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\n				<div id=\"send-modal-label\">编辑邮件：<span></span></div>\n			</div>\n			<div class=\"modal-body\">\n				<form class=\"form-horizontal\" method=\"post\" data-callback=\"/gduf/proxy_mail_send\">\n					<div class=\"control-group\">\n						<label class=\"control-label\" for=\"subject\">主 题：</label>\n						<div id=\"subject-control\" class=\"controls\">\n							<input type=\"text\" id=\"subject\" class=\"input-xxlarge\" placeholder=\"填写主题\" />\n						</div>\n					</div>\n					<div class=\"control-group\">\n						<label class=\"control-label\" for=\"receiver\">收件人：</label>\n						<div id=\"receiver-control\" class=\"controls\">\n							<input type=\"text\" id=\"receiver\" class=\"input-xxlarge\" placeholder=\"收件人\" />\n							<button id=\"search-user\" class=\"btn\">查找用户</button>\n						</div>\n					</div>\n					<hr />\n					<div class=\"control-group\">\n						<label class=\"control-label\" for=\"receiver\">正文：</label>\n						<div class=\"controls\">\n							<div id=\"gduf-mail-send-content\" name=\"content\"></div>\n						</div>\n					</div>\n				</form>\n			</div>\n			<div class=\"modal-footer\">\n				<button id=\"gduf-mail-send-submit\" class=\"btn btn-primary\">发送</button>\n				<button class=\"btn\" data-dismiss=\"modal\" aria-hidden=\"true\">返回</button>\n			</div>\n		</div>\n		<div id=\"dump\">\n		</div>\n	</div>\n</div>\n', '1', '1', '1383918959', null);

-- ----------------------------
-- Table structure for xunsec_element_content_log
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_element_content_log`;
CREATE TABLE `xunsec_element_content_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(10) NOT NULL,
  `name` varchar(60) DEFAULT NULL COMMENT '名称，方便调用',
  `title` varchar(100) DEFAULT NULL COMMENT '一个简短的描述',
  `code` text NOT NULL,
  `markdown` int(1) unsigned NOT NULL DEFAULT '1',
  `twig` int(1) unsigned NOT NULL DEFAULT '1',
  `poster_id` int(10) NOT NULL,
  `poster_name` varchar(100) NOT NULL,
  `date_created` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_element_content_log
-- ----------------------------
INSERT INTO `xunsec_element_content_log` VALUES ('1', '1', null, '首页nivoSlider部分', '{{ XunSec.style(\'nivo-slider/themes/default/default.css\') }}\n{{ XunSec.style(\'nivo-slider/nivo-slider.css\') }}\n{{ XunSec.script(\'nivo-slider/jquery.nivo.slider.pack.js\') }}\n{{ XunSec.script(\'js/nivo-slider-custom.js\') }}\n<div id=\"index-slider\" class=\"container\">\n	<div class=\"slider-wrapper theme-default\">\n		<div id=\"slider\" class=\"nivoSlider\">\n			<img src=\"http://gdufcdn.sinapp.com/media/img/slider/slider1.jpg\" data-thumb=\"http://gdufcdn.sinapp.com/media/img/slider/slider1.jpg\" alt=\"\" />\n			<a href=\"http://www.baidu.com\"><img src=\"http://gdufcdn.sinapp.com/media/img/slider/slider2.jpg\" data-thumb=\"http://gdufcdn.sinapp.com/media/img/slider/slider2.jpg\" alt=\"\" title=\"This is an example of a caption\" /></a>\n			<img src=\"http://gdufcdn.sinapp.com/media/img/slider/slider3.jpg\" data-thumb=\"http://gdufcdn.sinapp.com/media/img/slider/slider3.jpg\" alt=\"\" data-transition=\"slideInLeft\" />\n			<img src=\"http://gdufcdn.sinapp.com/media/img/slider/slider4.jpg\" data-thumb=\"http://gdufcdn.sinapp.com/media/img/slider/slider4.jpg\" alt=\"\" title=\"#htmlcaption\" />\n		</div>\n		<div id=\"htmlcaption\" class=\"nivo-html-caption\">\n			<strong>This</strong> is an example of a <em>HTML</em> caption with <a href=\"#\">a link</a>. \n		</div>\n	</div>\n</div>\n', '0', '1', '1000001', 'admin', '1384278135');
INSERT INTO `xunsec_element_content_log` VALUES ('2', '1', null, '首页nivoSlider部分', '{{ XunSec.style(\'nivo-slider/themes/default/default.css\') }}\n{{ XunSec.style(\'nivo-slider/nivo-slider.css\') }}\n{{ XunSec.script(\'nivo-slider/jquery.nivo.slider.pack.js\') }}\n{{ XunSec.script(\'js/nivo-slider-custom.js\') }}\n<div id=\"index-slider\" class=\"container\">\n	<div class=\"slider-wrapper theme-default\">\n		<div id=\"slider\" class=\"nivoSlider\">\n			<img src=\"/media/img/slider/slider1.jpg\" data-thumb=\"/media/img/slider/slider1.jpg\" alt=\"\" />\n			<a href=\"http://www.baidu.com\"><img src=\"/media/img/slider/slider2.jpg\" data-thumb=\"/media/img/slider/slider2.jpg\" alt=\"\" title=\"This is an example of a caption\" /></a>\n			<img src=\"/media/img/slider/slider3.jpg\" data-thumb=\"/media/img/slider/slider3.jpg\" alt=\"\" data-transition=\"slideInLeft\" />\n			<img src=\"/media/img/slider/slider4.jpg\" data-thumb=\"/media/img/slider/slider4.jpg\" alt=\"\" title=\"#htmlcaption\" />\n		</div>\n		<div id=\"htmlcaption\" class=\"nivo-html-caption\">\n			<strong>This</strong> is an example of a <em>HTML</em> caption with <a href=\"#\">a link</a>. \n		</div>\n	</div>\n</div>\n', '0', '1', '1000001', 'admin', '1384278240');
INSERT INTO `xunsec_element_content_log` VALUES ('3', '32', null, '首页-校训', '<div id=\"xiaoxun\">\n	<img src=\"http://gdufcdn.sinaapp.com/media/img/xiaoxun-1.png\" width=\"120\" height=\"60\" alt=\"勤奋\">\n	<img src=\"http://gdufcdn.sinaapp.com/media/img/xiaoxun-2.png\" width=\"120\" height=\"60\" alt=\"求是\">\n	<img src=\"http://gdufcdn.sinaapp.com/media/img/xiaoxun-3.png\" width=\"120\" height=\"60\" alt=\"廉洁\">\n	<img src=\"http://gdufcdn.sinaapp.com/media/img/xiaoxun-4.png\" width=\"120\" height=\"60\" alt=\"开拓\">\n</div>', '0', '0', '1000001', 'admin', '1384278371');
INSERT INTO `xunsec_element_content_log` VALUES ('4', '1', null, '首页nivoSlider部分', '{{ XunSec.style(\'nivo-slider/themes/default/default.css\') }}\n{{ XunSec.style(\'nivo-slider/nivo-slider.css\') }}\n{{ XunSec.script(\'nivo-slider/jquery.nivo.slider.pack.js\') }}\n{{ XunSec.script(\'js/nivo-slider-custom.js\') }}\n<div id=\"index-slider\" class=\"container\">\n	<div class=\"slider-wrapper theme-default\">\n		<div id=\"slider\" class=\"nivoSlider\">\n			<img src=\"http://gdufcdn.sinaapp.com/media/img/slider/slider1.jpg\" data-thumb=\"http://gdufcdn.sinaapp.com/media/img/slider/slider1.jpg\" alt=\"\" />\n			<a href=\"http://www.baidu.com\"><img src=\"http://gdufcdn.sinaapp.com/media/img/slider/slider2.jpg\" data-thumb=\"http://gdufcdn.sinaapp.com/media/img/slider/slider2.jpg\" alt=\"\" title=\"This is an example of a caption\" /></a>\n			<img src=\"http://gdufcdn.sinaapp.com/media/img/slider/slider3.jpg\" data-thumb=\"http://gdufcdn.sinaapp.com/media/img/slider/slider3.jpg\" alt=\"\" data-transition=\"slideInLeft\" />\n			<img src=\"http://gdufcdn.sinaapp.com/media/img/slider/slider4.jpg\" data-thumb=\"http://gdufcdn.sinaapp.com/media/img/slider/slider4.jpg\" alt=\"\" title=\"#htmlcaption\" />\n		</div>\n		<div id=\"htmlcaption\" class=\"nivo-html-caption\">\n			<strong>This</strong> is an example of a <em>HTML</em> caption with <a href=\"#\">a link</a>. \n		</div>\n	</div>\n</div>\n', '0', '1', '1000001', 'admin', '1384278500');

-- ----------------------------
-- Table structure for xunsec_element_request
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_element_request`;
CREATE TABLE `xunsec_element_request` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL COMMENT '名称，方便调用啊',
  `title` varchar(100) DEFAULT NULL COMMENT '一个简短的描述',
  `url` text NOT NULL,
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_element_request
-- ----------------------------

-- ----------------------------
-- Table structure for xunsec_element_request_log
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_element_request_log`;
CREATE TABLE `xunsec_element_request_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `request_id` int(10) NOT NULL,
  `name` varchar(60) DEFAULT NULL COMMENT '名称，方便调用啊',
  `title` varchar(100) DEFAULT NULL COMMENT '一个简短的描述',
  `url` text NOT NULL,
  `poster_id` int(10) NOT NULL,
  `poster_name` varchar(100) NOT NULL,
  `date_created` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_id` (`request_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_element_request_log
-- ----------------------------
INSERT INTO `xunsec_element_request_log` VALUES ('1', '6', null, null, 'http://www.baidu.com', '1000001', 'admin', '1383815404');
INSERT INTO `xunsec_element_request_log` VALUES ('2', '6', 'baidu', '百度首页', 'http://www.baidu.com', '1000001', 'admin', '1383816156');
INSERT INTO `xunsec_element_request_log` VALUES ('3', '6', 'baidu', '百度首页', 'http://www.baidu.com', '1000001', 'admin', '1383816196');
INSERT INTO `xunsec_element_request_log` VALUES ('4', '6', 'baidu', '百度首页1', 'http://www.baidu.com', '1000001', 'admin', '1383816209');
INSERT INTO `xunsec_element_request_log` VALUES ('5', '6', 'baidu', '百度首页1', 'http://www.baidu.com', '1000001', 'admin', '1383816626');

-- ----------------------------
-- Table structure for xunsec_element_snippet
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_element_snippet`;
CREATE TABLE `xunsec_element_snippet` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `code` text NOT NULL,
  `markdown` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `twig` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_element_snippet
-- ----------------------------
INSERT INTO `xunsec_element_snippet` VALUES ('1', 'footer', '默认-底部', '<div class=\"container\">\n	<div class=\"inner clearfix\">\n		<p>© 2012-2013 <a href=\"/join-us.html\">加入我们</a> | <a href=\"/privacy.html\">免责声明</a> | <a id=\"contact-link\" href=\"/contact.html\">留言反馈</a></p>\n		<p>本站由 <a href=\"http://www.gdufer.com/\" target=\"_blank\">Gdufer</a> && <a href=\"http://www.xunsec.com/\" target=\"_blank\">XunSec</a> 开发与维护</p>\n		<p>最佳显示器尺寸为 1366x768, 最佳浏览器为 Chrome</p>\n		<p>校址：<span class=\"label label-success\">广州市天河区迎福路527号</span> 邮编：<span class=\"label label-warning\">510521</span></p>\n	</div>\n</div>\n<script type=\"text/javascript\">\nvar _bdhmProtocol = ((\"https:\" == document.location.protocol) ? \" https://\" : \" http://\");\ndocument.write(unescape(\"%3Cscript src=\'\" + _bdhmProtocol + \"hm.baidu.com/h.js%3Fdfb3c2e40349102a5d406c1a3b68ad7d\' type=\'text/javascript\'%3E%3C/script%3E\"));\n</script>', '0', '0', null, '1383554111');
INSERT INTO `xunsec_element_snippet` VALUES ('3', 'header', '默认-头部', '{{ XunSec.style(\'bootstrap/css/bootstrap.min.css\') }}\n{{ XunSec.style(\'bootstrap/css/bootstrap-responsive.min.css\') }}\n{{ XunSec.style(\'css/style.css\') }}\n{{ XunSec.style(\'css/style-responsive.css\') }}\n{{ XunSec.style(\'font-awesome/css/font-awesome.min.css\') }}\n{{ XunSec.script(\'jquery/jquery-1.7.2.min.js\') }}\n{{ XunSec.script(\'bootstrap/js/bootstrap.min.js\') }}\n{{ XunSec.script(\'js/script.js\') }}', '0', '1', null, '1384075708');
INSERT INTO `xunsec_element_snippet` VALUES ('4', 'dashboard', '默认-仪表盘', '<div class=\"container\">\n	<div class=\"row-fluid\">\n		<div class=\"span4\">\n			<div class=\"col3_content\" markdown=\"1\">\n				#### 产品动态\n				*  [陈晨晨：选择途者，我节省了一半人工](#)\n				*  [我司产品购买人数达500家旅行社](#)\n				*  [XXX网对我司旅行社系统进行专评和测试](#)\n				*  [恭喜XXX旅行社获得我们的第一个测试资格](#)\n				*  [途者信息管理系统DEMO开放测试](http://www.baidu.com)\n			</div>\n		</div>\n		<div class=\"span4\">\n			<div class=\"col3_content\" markdown=\"1\">\n				#### 行业动态\n\n				*  [首个新旅游法合同出台:导游小费列入条款](#)\n				*  [泰国拟将清迈建成会展旅游城市，借此增收](#)\n				*  [台湾与云南进行“候鸟式”旅游合作](#)\n				*  [泰国旅游局首次来兰推介高品质泰国游产品](#)               \n				*  [江州区：吹响旅游业大发展号角](#)\n			</div>\n		</div>\n		<div class=\"span4\">\n			<div class=\"col3_content\" markdown=\"1\">\n				#### 途者优势\n\n				*  15人研发队伍和20名技术支持\n				*  市场覆盖率在南方占20%          \n				*  18小时售后服务和VIP定制服务\n				*  专注安全和稳定，系统100%安全\n				*  商业源码无加密，支持客户自定制\n			</div>\n		</div>\n	</div>\n</div>\n', '1', '0', null, '1382886706');
INSERT INTO `xunsec_element_snippet` VALUES ('7', 'navigation', '默认-导航', '<div id=\"topnav\" class=\"navbar navbar-fixed-top\">\n	<div class=\"navbar-inner\">\n		<div class=\"container\">\n			<button type=\"button\" class=\"btn btn-navbar\" data-toggle=\"collapse\" data-target=\".nav-collapse\">\n				<span class=\"icon-bar\"></span>\n				<span class=\"icon-bar\"></span>\n				<span class=\"icon-bar\"></span>\n			</button>\n			<a class=\"brand\" href=\"/\">\n				<img src=\"http://gdufer.sinaapp.com/media/img/logo-title.png\" alt=\"\" height=\"24\">\n			</a>\n			<div class=\"nav-collapse collapse\">\n				{{ XunSec.main_nav() }}\n				{{ XunSec.user_nav() }}\n			</div>\n		</div>\n	</div>\n</div>', '0', '1', '1383829722', '1384274976');

-- ----------------------------
-- Table structure for xunsec_element_snippet_log
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_element_snippet_log`;
CREATE TABLE `xunsec_element_snippet_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `snippet_id` int(10) NOT NULL,
  `name` varchar(150) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `code` text NOT NULL,
  `markdown` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `twig` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `poster_id` int(10) NOT NULL,
  `poster_name` varchar(100) NOT NULL,
  `date_created` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `snippet_id` (`snippet_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for xunsec_element_type
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_element_type`;
CREATE TABLE `xunsec_element_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_element_type
-- ----------------------------
INSERT INTO `xunsec_element_type` VALUES ('1', 'content');
INSERT INTO `xunsec_element_type` VALUES ('2', 'request');
INSERT INTO `xunsec_element_type` VALUES ('3', 'snippet');

-- ----------------------------
-- Table structure for xunsec_forum_group
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_forum_group`;
CREATE TABLE `xunsec_forum_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `uri` varchar(50) NOT NULL DEFAULT '',
  `count` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `limited` int(10) unsigned NOT NULL DEFAULT '50000',
  `mode` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0对登陆用户开放，1完整开放',
  `date_created` int(10) unsigned NOT NULL DEFAULT '0',
  `date_updated` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1000018 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_forum_group
-- ----------------------------
INSERT INTO `xunsec_forum_group` VALUES ('1000003', '1000002', '资料共享', '<p>\r\n	<strong>电子学习资料分享专版</strong>，供广大学子共享手上的学习资料，只要善用本站的搜索功能就能找到高质量的学习资料。\r\n</p>\r\n<p>\r\n	大家可以共享学习经验，实习经历，或者课件PPT等，格式限于PDF、DOC（X）、PPT、XLS等办公档案。\r\n</p>\r\n<p>\r\n	最后，如果你的资源来自他处，请特别标注，以免版权纠纷。同时也希望大家增强版权意识！不要使用本站的资料来牟利！\r\n</p>', 'document', '28', '0', '1', '50000', '1', '1369762068', '1379261820');
INSERT INTO `xunsec_forum_group` VALUES ('1000005', '1000002', '主题复习', '什么复习资料都可以发上来，求期末高分。', 'theme', '24', '0', '1', '50000', '1', '1369841374', '1380977629');
INSERT INTO `xunsec_forum_group` VALUES ('1000011', '1000002', '闲暇时光', '<p>\n	广金新生咨询必备书签，收藏吧。各位师兄师姐也别藏私了，快出手啊。\n</p>', 'newbie', '3', '0', '1', '50000', '1', '1370704286', '1375929821');
INSERT INTO `xunsec_forum_group` VALUES ('1000013', '1000002', '学习交流', '<p>\n	交流学习心得，交流考证考研留学经验。\n</p>', 'learn', '2', '0', '1', '50000', '1', '1370862604', '1374474283');
INSERT INTO `xunsec_forum_group` VALUES ('1000015', '1000014', '建议反馈', '<p>\n	你对本站有什么意见呢？\n</p>', 'suggestion', '2', '0', '1', '50000', '1', '1370884426', '1372304741');
INSERT INTO `xunsec_forum_group` VALUES ('1000016', '1000002', '校园剪影', '<p>\n	分享广金的美图。\n</p>\n<p>\n	发图跟平时编辑帖子排版一样。\n</p>\n<p>\n	注意图片不要引用其他地方的，上传到本站之后再编辑。\n</p>\n<p>\n	帖子第一张图片会自动设置为封面图，并生成缓存。\n</p>\n<p>\n	<strong>注意：上传的图片请注意大小，不要超过2M，否则会上传失败。</strong> \n</p>\n<p>\n	<strong>20130630:因为代码修改，暂时停用瀑布流。</strong>\n</p>', 'photo', '6', '0', '1', '50000', '1', '1371024147', '1374250516');
INSERT INTO `xunsec_forum_group` VALUES ('1000017', '0', '校内跳蚤', '', 'ershou', '0', '0', '1', '50000', '1', '0', '0');

-- ----------------------------
-- Table structure for xunsec_forum_reply
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_forum_reply`;
CREATE TABLE `xunsec_forum_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` int(10) unsigned NOT NULL,
  `poster_id` int(10) unsigned NOT NULL,
  `poster_name` varchar(100) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `ip` varchar(20) NOT NULL COMMENT '回复者IP',
  `date_created` int(10) unsigned NOT NULL DEFAULT '0',
  `date_updated` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1000232 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for xunsec_forum_reply_log
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_forum_reply_log`;
CREATE TABLE `xunsec_forum_reply_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reply_id` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_id` int(10) unsigned NOT NULL,
  `poster_id` int(10) unsigned NOT NULL,
  `poster_name` varchar(100) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `ip` varchar(20) NOT NULL COMMENT '回复者IP',
  `operator_id` int(10) DEFAULT NULL,
  `operator_name` varchar(100) DEFAULT NULL,
  `date_created` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for xunsec_forum_search
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_forum_search`;
CREATE TABLE `xunsec_forum_search` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(100) DEFAULT NULL,
  `count` int(10) DEFAULT '1',
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_forum_search
-- ----------------------------

-- ----------------------------
-- Table structure for xunsec_forum_setting
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_forum_setting`;
CREATE TABLE `xunsec_forum_setting` (
  `key` varchar(255) NOT NULL,
  `val` text,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_forum_setting
-- ----------------------------

-- ----------------------------
-- Table structure for xunsec_forum_topic
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_forum_topic`;
CREATE TABLE `xunsec_forum_topic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL,
  `poster_id` int(10) unsigned NOT NULL,
  `poster_name` varchar(100) NOT NULL COMMENT '发帖者昵称',
  `title` varchar(100) NOT NULL,
  `content` longtext NOT NULL,
  `sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `comments` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(20) NOT NULL COMMENT '发帖者IP',
  `date_created` int(10) unsigned NOT NULL DEFAULT '0',
  `date_updated` int(10) unsigned NOT NULL DEFAULT '0',
  `date_touched` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `title` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=1000142 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for xunsec_forum_topic_log
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_forum_topic_log`;
CREATE TABLE `xunsec_forum_topic_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` int(10) NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `poster_id` int(10) unsigned NOT NULL,
  `poster_name` varchar(100) NOT NULL COMMENT '发帖者昵称',
  `title` varchar(100) NOT NULL,
  `content` longtext NOT NULL,
  `sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `comments` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(20) NOT NULL COMMENT '发帖者IP',
  `date_touched` int(10) unsigned NOT NULL DEFAULT '0',
  `operator_id` int(10) NOT NULL,
  `operator_name` varchar(100) NOT NULL,
  `date_created` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for xunsec_gduf_jwc_user
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_gduf_jwc_user`;
CREATE TABLE `xunsec_gduf_jwc_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) DEFAULT NULL,
  `student_no` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `realname` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `major` varchar(100) DEFAULT NULL,
  `class` varchar(100) DEFAULT NULL,
  `schedule` mediumtext,
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  `date_touched` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_gduf_jwc_user
-- ----------------------------
INSERT INTO `xunsec_gduf_jwc_user` VALUES ('2', '1000001', '111', '111', '111', '111', '111', '1111', '1115864', '<table id=\"Table1\" class=\"table table-bordered\" bordercolor=\"Black\" border=\"0\" width=\"100%\">\r\n	<tr>\r\n		<td colspan=\"2\" rowspan=\"1\" width=\"2%\">时间</td><td align=\"Center\" width=\"14%\">星期一</td><td align=\"Center\" width=\"14%\">星期二</td><td align=\"Center\" width=\"14%\">星期三</td><td align=\"Center\" width=\"14%\">星期四</td><td align=\"Center\" width=\"14%\">星期五</td><td class=\"noprint\" align=\"Center\" width=\"14%\">星期六</td><td class=\"noprint\" align=\"Center\" width=\"14%\">星期日</td>\r\n	</tr><tr>\r\n		<td colspan=\"2\">早晨</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td>\r\n	</tr><tr>\r\n		<td rowspan=\"5\" width=\"1%\">上午</td><td width=\"1%\">第1节</td><td align=\"Center\" rowspan=\"2\" width=\"7%\">审计学<br>周一第1,2节{第1-17周}<br>包强<br>北教C202</td><td align=\"Center\" rowspan=\"2\" width=\"7%\">信用风险定量分析系统<br>周二第1,2节{第1-17周}<br>范忠伟<br>实验楼1001</td><td align=\"Center\" width=\"7%\">&nbsp;</td><td align=\"Center\" rowspan=\"2\" width=\"7%\">资产评估理论与实务<br>周四第1,2节{第1-17周}<br>范忠伟<br>北教C401</td><td align=\"Center\" rowspan=\"2\" width=\"7%\">消费者信用管理<br>周五第1,2节{第1-17周}<br>叶湘榕<br>北教C302</td><td class=\"noprint\" align=\"Center\" width=\"7%\">&nbsp;</td><td class=\"noprint\" align=\"Center\" width=\"7%\">&nbsp;</td>\r\n	</tr><tr>\r\n		<td>第2节</td><td align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td>\r\n	</tr><tr>\r\n		<td>第3节</td><td align=\"Center\" rowspan=\"2\">金融机构信用管理<br>周一第3,4节{第1-17周}<br>肖旦<br>北教C302</td><td align=\"Center\" rowspan=\"3\">企业信用管理<br>周二第3,4,5节{第1-17周}<br>何南<br>北教C302</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\" rowspan=\"2\">征信理论与实务<br>周五第3,4节{第1-17周}<br>唐明琴<br>北教C202</td><td class=\"noprint\" align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td>\r\n	</tr><tr>\r\n		<td>第4节</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td>\r\n	</tr><tr>\r\n		<td>第5节</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td>\r\n	</tr><tr>\r\n		<td rowspan=\"3\">下午</td><td>第6节</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\" rowspan=\"3\">马克思主义基本原理<br>周三第6,7,8节{第1-17周}<br>杨济源<br>北教A503</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td>\r\n	</tr><tr>\r\n		<td>第7节</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td>\r\n	</tr><tr>\r\n		<td>第8节</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td>\r\n	</tr><tr>\r\n		<td rowspan=\"4\" width=\"1%\">晚上</td><td>第9节</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\" rowspan=\"2\">客户管理与商帐追收<br>周四第9,10节{第1-17周}<br>蔡赛男<br>北教D402</td><td align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td>\r\n	</tr><tr>\r\n		<td>第10节</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td>\r\n	</tr><tr>\r\n		<td>第11节</td><td align=\"Center\" rowspan=\"2\">西方哲学史<br>周一第11,12节{第1-17周}<br>曾锋<br>北阶103</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td>\r\n	</tr><tr>\r\n		<td>第12节</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td><td class=\"noprint\" align=\"Center\">&nbsp;</td>\r\n	</tr>\r\n</table>\r\n						<br>', '1384580861', '1384581526', '1384581526');

-- ----------------------------
-- Table structure for xunsec_gduf_mail
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_gduf_mail`;
CREATE TABLE `xunsec_gduf_mail` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `mail_id` int(10) NOT NULL COMMENT '在学校记录的邮件ID',
  `content` mediumtext NOT NULL COMMENT 'fetch到的邮件正文',
  `fetcher` varchar(255) NOT NULL COMMENT '远程邮箱中的用户名信息',
  `poster_id` int(10) NOT NULL COMMENT '提交入库的本站用户ID',
  `count` int(10) NOT NULL DEFAULT '0' COMMENT '点击数',
  `date_created` int(10) NOT NULL,
  `date_updated` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mail_id` (`mail_id`),
  KEY `fetcher` (`fetcher`),
  KEY `poster_id` (`poster_id`)
) ENGINE=MyISAM AUTO_INCREMENT=263 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for xunsec_gduf_user
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_gduf_user`;
CREATE TABLE `xunsec_gduf_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) DEFAULT NULL,
  `username` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_gduf_user
-- ----------------------------
INSERT INTO `xunsec_gduf_user` VALUES ('1', '1000001', '111586437', '11111', '1384061644');
INSERT INTO `xunsec_gduf_user` VALUES ('2', '1000003', '111586437', '11111', '1385958277');

-- ----------------------------
-- Table structure for xunsec_information
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_information`;
CREATE TABLE `xunsec_information` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `model` varchar(100) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_information
-- ----------------------------
INSERT INTO `xunsec_information` VALUES ('1', 'book', '图书模型');

-- ----------------------------
-- Table structure for xunsec_information_book
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_information_book`;
CREATE TABLE `xunsec_information_book` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `category_id` int(10) DEFAULT NULL COMMENT '所属分类ID',
  `book_name` varchar(100) NOT NULL COMMENT '书名',
  `book_author` varchar(100) NOT NULL COMMENT '书的作者/译者',
  `quality` tinyint(1) DEFAULT '0' COMMENT '书本成色，默认是0（正常痕迹）',
  `publisher` varchar(100) NOT NULL COMMENT '出版社',
  `raw_price` float NOT NULL COMMENT '原价',
  `image` varchar(160) NOT NULL COMMENT '图片',
  `description` varchar(255) NOT NULL COMMENT '255字的描述',
  `return_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '想要的回报类型',
  `return_text` varchar(255) NOT NULL COMMENT '回报内容，只有return_type=0时才有效',
  `realname` varchar(60) NOT NULL COMMENT '姓名',
  `telephone` varchar(20) NOT NULL COMMENT '手机号码',
  `remark` varchar(100) NOT NULL COMMENT '备注',
  `poster_id` int(10) NOT NULL COMMENT '发布者UID',
  `poster_name` varchar(100) NOT NULL COMMENT '发布者昵称',
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `book_name` (`book_name`),
  KEY `book_author` (`book_author`),
  KEY `publisher` (`publisher`),
  KEY `return_type` (`return_type`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_information_book
-- ----------------------------
INSERT INTO `xunsec_information_book` VALUES ('1', '1', '色彩心理学', '黑哥', '0', '人民日报', '10', '/media/upload/2013/11/21/1Um9HAO2NcNM3XxDy04z.jpg', 'fdsa fdsa fdsa', '2', '', '卢钟鹏', '111', '中午不要联系', '1000003', 'YwiSax', '1384970273', null);
INSERT INTO `xunsec_information_book` VALUES ('2', '1', '色彩心理学', '黑哥', '0', '人民日报', '10', '/media/upload/2013/11/21/1Um9HAO2NcNM3XxDy04z.jpg', 'fdsa fdsa fdsa', '3', '', '卢钟鹏', '111', '', '1000003', 'YwiSax', '1384970292', null);
INSERT INTO `xunsec_information_book` VALUES ('3', '1', '色彩心理学', '黑哥', '0', '人民日报', '10', '', 'ttttttttttttt', '2', '', 'dddd', '111', '', '1000003', 'YwiSax', '1384971994', null);
INSERT INTO `xunsec_information_book` VALUES ('4', '3', '二手你懂的', '黑哥', '0', '人民日报', '10', '/media/upload/2013/12/18/4gQN2m3LK72Cv1hlMQFr.png', 'f大范德萨范德萨范德萨范德萨', '0', '人民币100', '卢钟鹏', '111', '2132123132', '1000397', '3dst4d', '1387297339', null);

-- ----------------------------
-- Table structure for xunsec_information_category
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_information_category`;
CREATE TABLE `xunsec_information_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `model_id` int(10) DEFAULT NULL,
  `parent_id` int(10) DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `count` int(10) DEFAULT NULL,
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_information_category
-- ----------------------------
INSERT INTO `xunsec_information_category` VALUES ('1', '1', '0', '考研', null, null, null);
INSERT INTO `xunsec_information_category` VALUES ('2', '1', '0', '快从', null, null, null);
INSERT INTO `xunsec_information_category` VALUES ('3', '1', '0', '证券', null, null, null);
INSERT INTO `xunsec_information_category` VALUES ('4', '1', '0', '教材', null, null, null);
INSERT INTO `xunsec_information_category` VALUES ('5', '1', '0', '小说', null, null, null);

-- ----------------------------
-- Table structure for xunsec_information_comment
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_information_comment`;
CREATE TABLE `xunsec_information_comment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `model_type` varchar(20) DEFAULT NULL,
  `related_id` int(10) DEFAULT NULL,
  `content` text,
  `poster_id` int(10) DEFAULT NULL,
  `poster_name` varchar(100) DEFAULT NULL,
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for xunsec_layout
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_layout`;
CREATE TABLE `xunsec_layout` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `desc` varchar(256) DEFAULT NULL,
  `code` text,
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_layout
-- ----------------------------
INSERT INTO `xunsec_layout` VALUES ('1', 'default-one', '默认-单列布局', '默认使用的单页布局', '<div id=\"header\">\n   {{ XunSec.element(\'snippet\',\'header\') }}\n</div>\n<div id=\"navigation\">\n   {{ XunSec.element(\'snippet\',\'navigation\') }}\n</div>\n<div id=\"mainbox\" class=\"container\">\n	<div id=\"main-content\" class=\"row-fluid\">\n		<div class=\"span12\">\n			{{ XunSec.element_area(1,\'Main Column\') }}\n		</div>\n	</div>\n</div>\n<div id=\"footer\">\n   {{ XunSec.element(\'snippet\',\'footer\') }}\n</div>', null, '1383829780');
INSERT INTO `xunsec_layout` VALUES ('4', 'blank', '空白页面', '空白页面空白页面空白页面', '<div id=\"header\">\n   {{ XunSec.element(\'snippet\',\'header\') }}\n</div>\n<div id=\"navigation\">\n   {{ XunSec.element(\'snippet\',\'navigation\') }}\n</div>\n<div id=\"mainbox\" class=\"container\">\n	<div id=\"main-content\" class=\"row-fluid\">\n		<div class=\"span12\">\n			{{ XunSec.content() }}\n		</div>\n	</div>\n</div>\n<div id=\"footer\">\n   {{ XunSec.element(\'snippet\',\'footer\') }}\n</div>', null, '1383829794');
INSERT INTO `xunsec_layout` VALUES ('2', 'default-two', '默认-两列布局', '默认使用的两列布局', '<div id=\"header\">\n   {{ XunSec.element(\'snippet\',\'header\') }}\n</div>\n<div id=\"navigation\">\n   {{ XunSec.element(\'snippet\',\'navigation\') }}\n</div>\n<div id=\"mainbox\" class=\"container\">\n	<div id=\"main-content\" class=\"row-fluid\">\n		<div class=\"span9\">\n			{{ XunSec.element_area(1,\'Main Column\') }}\n		</div>\n		<div class=\"span3\">\n			{{ XunSec.element_area(2,\'Side Column\') }}\n		</div>\n	</div>\n</div>\n<div id=\"footer\">\n   {{ XunSec.element(\'snippet\',\'footer\') }}\n</div>', null, '1383829787');
INSERT INTO `xunsec_layout` VALUES ('5', 'fdsafdsa', '5555555', '22222', '8', '1383904099', '1383905349');

-- ----------------------------
-- Table structure for xunsec_layout_log
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_layout_log`;
CREATE TABLE `xunsec_layout_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `layout_id` int(10) NOT NULL COMMENT '原布局ID',
  `name` varchar(60) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `desc` varchar(256) DEFAULT NULL,
  `code` text,
  `poster_id` int(10) NOT NULL,
  `poster_name` varchar(100) NOT NULL,
  `date_created` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `layout_id` (`layout_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_layout_log
-- ----------------------------
INSERT INTO `xunsec_layout_log` VALUES ('12', '5', 'fdsafdsa', '5555555', '22222', '8', '1000001', 'admin', '1383905349');
INSERT INTO `xunsec_layout_log` VALUES ('11', '5', 'fdsafdsa', '5555555', '22222', '4', '1000001', 'admin', '1383905329');

-- ----------------------------
-- Table structure for xunsec_log
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_log`;
CREATE TABLE `xunsec_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(60) DEFAULT 'cms',
  `date_created` int(10) NOT NULL,
  `operator_id` int(10) NOT NULL COMMENT '操作者ID',
  `operator_name` varchar(60) NOT NULL COMMENT '操作者名称，防止改名啊',
  `content` text NOT NULL COMMENT '操作内容',
  `ip` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=415 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for xunsec_page
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_page`;
CREATE TABLE `xunsec_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `url` varchar(256) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `layout_id` int(10) unsigned DEFAULT '0',
  `islink` tinyint(1) unsigned DEFAULT '0',
  `showmap` tinyint(3) unsigned DEFAULT '1',
  `shownav` tinyint(3) unsigned DEFAULT '1',
  `title` varchar(256) DEFAULT NULL,
  `metadesc` text,
  `metakw` text,
  `lft` int(10) unsigned DEFAULT NULL,
  `rgt` int(10) unsigned DEFAULT NULL,
  `lvl` int(10) unsigned DEFAULT NULL,
  `scope` int(10) unsigned DEFAULT NULL,
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Page-Layout` (`layout_id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_page
-- ----------------------------
INSERT INTO `xunsec_page` VALUES ('1', null, '', '首页', '1', '0', '1', '1', '广金在线', '广金在线，连接广金你我。', '广东金融学院,广金论坛,广金首页,广金教务系统,广金', '1', '22', '0', '1', null, '1384795945');
INSERT INTO `xunsec_page` VALUES ('11', '1', 'error', '页面不存在', '1', '0', '0', '0', '找不到指定页面', '', '', '18', '19', '1', '1', null, '1384795945');
INSERT INTO `xunsec_page` VALUES ('35', '1', 'contact.html', '联系我们', '1', '1', '1', '0', '', '', '', '20', '21', '1', '1', '1384093430', '1384795945');
INSERT INTO `xunsec_page` VALUES ('17', null, 'gduf.html', '校史回忆', '1', '0', '1', '0', '联系我们', '广金发展的点点滴滴', '广金在线,广金历史,广金编年史', '10', '11', '1', '1', null, '1385835931');
INSERT INTO `xunsec_page` VALUES ('18', null, 'sitemap', '站点地图', '2', '0', '0', '0', '站点地图 - 途者在线', '', '', '12', '13', '1', '1', null, '1384795945');
INSERT INTO `xunsec_page` VALUES ('26', null, 'forum.html', '广金论坛', '1', '1', '1', '1', null, null, null, '2', '3', '1', '1', '1382853905', '1384795945');
INSERT INTO `xunsec_page` VALUES ('27', '1', 'privacy.html', '免责声明', '1', '0', '1', '0', '免责声明', '', '', '14', '15', '1', '1', '1382893749', '1384795945');
INSERT INTO `xunsec_page` VALUES ('28', '1', 'join-us.html', '加入我们', '1', '0', '1', '0', '加入我们', '', '', '16', '17', '1', '1', '1382894047', '1384795945');
INSERT INTO `xunsec_page` VALUES ('29', '1', 'weibo-post.html', '广金树洞', '1', '1', '0', '0', null, null, null, '8', '9', '1', '1', '1382984033', '1384795945');
INSERT INTO `xunsec_page` VALUES ('34', null, 'gduf/mail.html', '校内邮箱', '1', '1', '0', '0', null, null, null, '6', '7', '1', '1', '1384059285', '1385959421');
INSERT INTO `xunsec_page` VALUES ('36', '1', 'info.html', '广金书屋', '1', '1', '1', '1', null, null, null, '4', '5', '1', '1', '1384795932', '1384795945');

-- ----------------------------
-- Table structure for xunsec_page_log
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_page_log`;
CREATE TABLE `xunsec_page_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) NOT NULL COMMENT '原始页面ID',
  `parent_id` int(10) DEFAULT NULL,
  `url` varchar(256) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `layout_id` int(10) unsigned DEFAULT '0',
  `islink` tinyint(1) unsigned DEFAULT '0',
  `showmap` tinyint(3) unsigned DEFAULT '1',
  `shownav` tinyint(3) unsigned DEFAULT '1',
  `title` varchar(256) DEFAULT NULL,
  `metadesc` text,
  `metakw` text,
  `lft` int(10) unsigned DEFAULT NULL,
  `rgt` int(10) unsigned DEFAULT NULL,
  `lvl` int(10) unsigned DEFAULT NULL,
  `scope` int(10) unsigned DEFAULT NULL,
  `poster_id` int(10) NOT NULL,
  `poster_name` int(10) NOT NULL,
  `date_created` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Page-Layout` (`layout_id`)
) ENGINE=MyISAM AUTO_INCREMENT=234 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for xunsec_redirect
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_redirect`;
CREATE TABLE `xunsec_redirect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `newurl` varchar(255) NOT NULL,
  `type` enum('301','302') NOT NULL DEFAULT '302',
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_redirect
-- ----------------------------
INSERT INTO `xunsec_redirect` VALUES ('1', 'test', 'about', '301', null, null);

-- ----------------------------
-- Table structure for xunsec_redirect_log
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_redirect_log`;
CREATE TABLE `xunsec_redirect_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `redirect_id` int(10) NOT NULL,
  `url` varchar(255) NOT NULL,
  `newurl` varchar(255) NOT NULL,
  `type` enum('301','302') NOT NULL DEFAULT '302',
  `poster_id` int(10) NOT NULL,
  `poster_name` varchar(100) NOT NULL,
  `date_created` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `poster_id` (`poster_id`),
  KEY `redirect_id` (`redirect_id`),
  KEY `url` (`url`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_redirect_log
-- ----------------------------
INSERT INTO `xunsec_redirect_log` VALUES ('1', '1', 'test', 'about', '301', '0', '0', null);

-- ----------------------------
-- Table structure for xunsec_role
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_role`;
CREATE TABLE `xunsec_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `desc` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_role
-- ----------------------------
INSERT INTO `xunsec_role` VALUES ('1', 'login', '论坛成员，基本的发帖权限');
INSERT INTO `xunsec_role` VALUES ('2', 'admin', '论坛管理员，可以对论坛进行全局设置');

-- ----------------------------
-- Table structure for xunsec_role_user
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_role_user`;
CREATE TABLE `xunsec_role_user` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_role_user
-- ----------------------------
INSERT INTO `xunsec_role_user` VALUES ('1000001', '1');
INSERT INTO `xunsec_role_user` VALUES ('1000001', '2');

-- ----------------------------
-- Table structure for xunsec_user
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_user`;
CREATE TABLE `xunsec_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(120) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gender` int(1) unsigned NOT NULL DEFAULT '5',
  `point` int(10) NOT NULL DEFAULT '0',
  `stuno` varchar(20) NOT NULL DEFAULT '',
  `realname` varchar(50) NOT NULL DEFAULT '',
  `telephone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `location` varchar(50) NOT NULL,
  `website` varchar(100) NOT NULL,
  `qq` varchar(100) NOT NULL DEFAULT '',
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned NOT NULL DEFAULT '0',
  `last_ua` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=1000398 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_user
-- ----------------------------
INSERT INTO `xunsec_user` VALUES ('1000001', 'admin', '自己想办法', 'admin@gdufer.com', '5', '0', '2222', '广金在线', '222', '', '广金1', 'http://www.gdufer.com', '123456', '1369745015', '0', '55', '1386222009', '');

-- ----------------------------
-- Table structure for xunsec_user_token
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_user_token`;
CREATE TABLE `xunsec_user_token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_agent` text NOT NULL,
  `token` varchar(50) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_TOKEN` (`token`),
  KEY `INDEX_USER_ID` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_user_token
-- ----------------------------

-- ----------------------------
-- Table structure for xunsec_weibo_feed
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_weibo_feed`;
CREATE TABLE `xunsec_weibo_feed` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '本地ID',
  `mid` varchar(20) DEFAULT NULL COMMENT '微博MID',
  `idstr` varchar(60) DEFAULT NULL COMMENT '字符串型的微博ID',
  `poster_id` varchar(10) DEFAULT NULL,
  `screen_name` varchar(120) DEFAULT NULL,
  `text` text,
  `source` varchar(60) DEFAULT NULL COMMENT '微博来源',
  `favorited` tinyint(1) DEFAULT NULL,
  `truncated` tinyint(1) DEFAULT NULL,
  `in_reply_to_status_id` varchar(60) DEFAULT NULL,
  `in_reply_to_user_id` varchar(60) DEFAULT NULL,
  `in_reply_to_screen_name` varchar(60) DEFAULT NULL,
  `thumbnail_pic` varchar(255) DEFAULT NULL COMMENT '缩略图片地址，没有时不返回此字段',
  `bmiddle_pic` varchar(255) DEFAULT NULL COMMENT '中等尺寸图片地址，没有时不返回此字段',
  `original_pic` varchar(255) DEFAULT NULL COMMENT '原始图片地址，没有时不返回此字段',
  `geo` text,
  `user` text,
  `retweeted_status` text,
  `reposts_count` int(10) DEFAULT NULL,
  `comments_count` int(10) DEFAULT NULL,
  `attitudes_count` int(10) DEFAULT NULL,
  `mlevel` int(10) DEFAULT NULL,
  `visible` text COMMENT '微博的可见性及指定可见分组信息。该object中type取值，0：普通微博，1：私密微博，3：指定分组微博，4：密友微博；list_id为分组的组号',
  `pic_urls` text COMMENT '微博配图地址。多图时返回多图链接。无配图返回“[]”',
  `ad` text COMMENT '微博流内的推广微博ID',
  `created_at` varchar(60) DEFAULT NULL COMMENT '微博创建时间（微博返回的）',
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  `poster_ip` varchar(30) DEFAULT NULL COMMENT '发布者IP',
  `img_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for xunsec_weibo_image
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_weibo_image`;
CREATE TABLE `xunsec_weibo_image` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '图片ID',
  `poster_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '上传者ID',
  `poster_name` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '上传者昵称',
  `filename` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '最原始的文件名',
  `filepath` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` int(10) DEFAULT NULL COMMENT '上传时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for xunsec_weibo_setting
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_weibo_setting`;
CREATE TABLE `xunsec_weibo_setting` (
  `key` varchar(255) NOT NULL,
  `val` text,
  `date_updated` int(10) DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for xunsec_weibo_setting_log
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_weibo_setting_log`;
CREATE TABLE `xunsec_weibo_setting_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `key` varchar(255) NOT NULL,
  `val` varchar(255) NOT NULL,
  `operator_id` int(10) NOT NULL,
  `operator_name` varchar(100) NOT NULL,
  `date_created` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xunsec_weibo_setting_log
-- ----------------------------

-- ----------------------------
-- Table structure for xunsec_weibo_user
-- ----------------------------
DROP TABLE IF EXISTS `xunsec_weibo_user`;
CREATE TABLE `xunsec_weibo_user` (
  `uid` varchar(20) NOT NULL COMMENT '微博中返回的ID',
  `idstr` varchar(100) DEFAULT NULL COMMENT '字符串型的用户UID',
  `screen_name` varchar(80) DEFAULT NULL COMMENT '用户昵称',
  `name` varchar(80) DEFAULT NULL COMMENT '友好显示名称',
  `province` int(10) DEFAULT NULL COMMENT '用户所在省级ID',
  `city` int(10) DEFAULT NULL COMMENT '用户所在城市ID',
  `location` varchar(60) DEFAULT NULL COMMENT '用户所在地',
  `description` varchar(255) DEFAULT NULL COMMENT '用户个人描述',
  `url` varchar(150) DEFAULT NULL COMMENT '用户博客地址',
  `profile_image_url` varchar(255) DEFAULT NULL COMMENT '用户头像地址，50×50像素',
  `profile_url` varchar(255) DEFAULT NULL COMMENT '用户的微博统一URL地址',
  `domain` varchar(60) DEFAULT NULL COMMENT '用户的个性化域名',
  `weihao` varchar(30) DEFAULT NULL COMMENT '用户的微号',
  `gender` varchar(10) DEFAULT NULL COMMENT '性别，m：男、f：女、n：未知',
  `followers_count` int(10) DEFAULT NULL COMMENT '粉丝数',
  `friends_count` int(10) DEFAULT NULL COMMENT '关注数',
  `statuses_count` int(10) DEFAULT NULL COMMENT '微博数',
  `favourites_count` int(10) DEFAULT NULL COMMENT '收藏数',
  `created_at` varchar(50) DEFAULT NULL COMMENT '用户创建（注册）时间',
  `following` tinyint(1) DEFAULT NULL COMMENT '暂未支持',
  `allow_all_act_msg` tinyint(1) DEFAULT NULL COMMENT '是否允许所有人给我发私信，true：是，false：否',
  `geo_enabled` tinyint(1) DEFAULT NULL COMMENT '是否允许标识用户的地理位置，true：是，false：否',
  `verified` tinyint(1) DEFAULT NULL COMMENT '是否是微博认证用户，即加V用户，true：是，false：否',
  `verified_type` int(10) DEFAULT NULL COMMENT '暂未支持',
  `remark` text COMMENT '用户备注信息，只有在查询用户关系时才返回此字段',
  `status` text COMMENT '用户的最近一条微博信息字段 详细',
  `allow_all_comment` tinyint(1) DEFAULT NULL COMMENT '是否允许所有人对我的微博进行评论，true：是，false：否',
  `avatar_large` varchar(255) DEFAULT NULL COMMENT '用户大头像地址',
  `verified_reason` varchar(255) DEFAULT NULL COMMENT '认证原因',
  `follow_me` tinyint(1) DEFAULT NULL COMMENT '该用户是否关注当前登录用户，true：是，false：否',
  `online_status` tinyint(1) DEFAULT NULL COMMENT '用户的在线状态，0：不在线、1：在线',
  `bi_followers_count` int(10) DEFAULT NULL COMMENT '用户的互粉数',
  `lang` varchar(30) DEFAULT NULL COMMENT '用户当前的语言版本，zh-cn：简体中文，zh-tw：繁体中文，en：英语',
  `access_token` varchar(255) DEFAULT NULL COMMENT '令牌',
  `remind_in` int(10) DEFAULT NULL,
  `expires_in` int(10) DEFAULT NULL,
  `date_created` int(10) DEFAULT NULL,
  `date_updated` int(10) DEFAULT NULL,
  `last_ip` varchar(30) DEFAULT NULL,
  `ban_expired` int(10) DEFAULT NULL COMMENT '禁用到期时间，默认为0（不禁用），-1时为永久禁用'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
