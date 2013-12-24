CREATE TABLE IF NOT EXISTS `kohana_role` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  `order`	int(10) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `kohana_role` (`id`, `name`, `description`, `order`) VALUES(1, 'login',			'最基本的登录权限', '1');
INSERT INTO `kohana_role` (`id`, `name`, `description`, `order`) VALUES(2, 'user',			'基础的用户权限', '1');
INSERT INTO `kohana_role` (`id`, `name`, `description`, `order`) VALUES(3, 'vipuser',		'VIP用户权限', '1');
INSERT INTO `kohana_role` (`id`, `name`, `description`, `order`) VALUES(4, 'manager',		'管理员权限，属于比较高的权限', '1');
INSERT INTO `kohana_role` (`id`, `name`, `description`, `order`) VALUES(5, 'administrator',	'系统最高权限', '1');

CREATE TABLE IF NOT EXISTS `kohana_role_user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `kohana_user` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(254) NOT NULL,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL,
  `logins` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `last_login` int(10) UNSIGNED,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `kohana_user_token` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `created` int(10) UNSIGNED NOT NULL,
  `expires` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`),
  KEY `expires` (`expires`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

ALTER TABLE `kohana_role_user`
  ADD CONSTRAINT `role_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `kohana_user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_user_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `kohana_role` (`id`) ON DELETE CASCADE;

ALTER TABLE `kohana_user_token`
  ADD CONSTRAINT `user_token_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `kohana_user` (`id`) ON DELETE CASCADE;
