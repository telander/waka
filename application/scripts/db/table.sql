CREATE TABLE `waka_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(2) DEFAULT '0' COMMENT '0正常，-1黑名单',
  `mobile` varchar(15) DEFAULT '' COMMENT '手机号',
  `password` varchar(255) DEFAULT NULL,
  `source` int(5) DEFAULT '1' COMMENT '0:手机；1:微信',
  `open_id` varchar(255) DEFAULT NULL COMMENT '微信的openId号。',
  `last_login_ip` varchar(255) DEFAULT NULL,
  `last_login_time` datetime DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `province` varchar(45) DEFAULT NULL,
  `head_img_url` varchar(255) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `courts_id` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT '0正常，1删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `openid_UNIQUE` (`open_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='用户表描述';

CREATE TABLE `waka_wechat_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(255) DEFAULT NULL,
  `open_id` varchar(40) NOT NULL DEFAULT '' COMMENT '微信的openId号。',
  `union_id` varchar(40) DEFAULT '' COMMENT '微信的unionId号。',
  `subscribe` int(11) DEFAULT NULL COMMENT '是否订阅',
  `sex` int(11) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `province` varchar(45) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL,
  `head_img_url` varchar(255) DEFAULT NULL,
  `subscribe_time` bigint(20) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `accuracy` double DEFAULT NULL COMMENT '地理位置精确度',
  PRIMARY KEY (`id`),
  UNIQUE KEY `open_id_unique` (`open_id`),
  UNIQUE KEY `union_id_unique` (`union_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='微信用户';

CREATE TABLE `waka_usertoken` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(40) NOT NULL,
  `expire` datetime NOT NULL,
  `logintype` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT '0正常，1删除',
  PRIMARY KEY (`id`),
  KEY `user_token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='用户登录Token';

CREATE TABLE `waka_mobile_verify` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `randstr` varchar(32) NOT NULL,
  `userid` int(10) NOT NULL,
  `phonecode` int(11) DEFAULT '86',
  `mobile` varchar(20) NOT NULL,
  `expire` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mobile` (`mobile`),
  KEY `userid` (`userid`),
  KEY `IDX_RANDSTR_MOBILE` (`randstr`,`mobile`),
  KEY `IDX_MOBILE_RANDSTR` (`mobile`,`randstr`),
  KEY `IDX_RA_MO_EX` (`randstr`,`mobile`,`expire`),
  KEY `IDX_MOBILE_TIMESTAMP` (`mobile`,`timestamp`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='验证手机';


CREATE TABLE `waka_city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `province` varchar(45) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '0' COMMENT '0开通，1开通中，2未开通',
  `create_time` datetime DEFAULT NULL,
  `lat` float DEFAULT '0',
  `lng` float DEFAULT '0',
  `is_deleted` tinyint(1) DEFAULT 0 COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='城市信息';

CREATE TABLE `waka_district` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city_id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '0' COMMENT '0开通，1开通中，2未开通',
  `is_deleted` tinyint(1) DEFAULT 0 COMMENT '是否删除',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`city_id`) REFERENCES waka_city(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='区域信息';



