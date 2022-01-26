CREATE TABLE `__PREFIX__manystore_shop` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(100) NOT NULL COMMENT '店铺名称',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT '品牌LOGO',
  `image` varchar(200) NOT NULL COMMENT '封面图',
  `images` text COMMENT '店铺环境照片',
  `address_city` varchar(255) NOT NULL COMMENT '城市选择',
  `province` int(11) NOT NULL COMMENT '省编号',
  `city` int(11) NOT NULL COMMENT '市编号',
  `district` int(11) NOT NULL COMMENT '县区编号',
  `address` varchar(200) NOT NULL COMMENT '店铺地址',
  `address_detail` varchar(200) NOT NULL DEFAULT '' COMMENT '店铺详细地址',
  `longitude` varchar(30) NOT NULL DEFAULT '' COMMENT '经度',
  `latitude` varchar(30) NOT NULL DEFAULT '' COMMENT '纬度',
  `yyzzdm` varchar(200) NOT NULL DEFAULT '' COMMENT '营业执照',
  `yyzz_images` varchar(1000) NOT NULL DEFAULT '' COMMENT '营业执照照片',
  `tel` varchar(20) DEFAULT NULL COMMENT '服务电话',
  `content` text COMMENT '店铺详情',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '审核状态:0=待审核,1=审核通过,2=审核失败',
  `reason` varchar(200) DEFAULT '' COMMENT '审核不通过原因',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX__manystore` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `shop_id` INT(10) NOT NULL DEFAULT '0' COMMENT '商家ID',
  `is_main` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否主账号',
  `username` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `password` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '密码盐',
  `avatar` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '头像',
  `email` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `loginfailure` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '失败次数',
  `logintime` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登录时间',
  `loginip` VARCHAR(50) DEFAULT NULL COMMENT '登录IP',
  `createtime` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `token` VARCHAR(59) NOT NULL DEFAULT '' COMMENT 'Session标识',
  `status` VARCHAR(30) NOT NULL DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家管理员表';


CREATE TABLE IF NOT EXISTS `__PREFIX__manystore_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) NOT NULL COMMENT '商家ID',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父组别',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '组名',
  `rules` text NOT NULL COMMENT '规则ID',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` varchar(30) NOT NULL DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商家分组表';


CREATE TABLE IF NOT EXISTS `__PREFIX__manystore_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '会员ID',
  `group_id` int(10) unsigned NOT NULL COMMENT '级别ID',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家权限分组表';


CREATE TABLE IF NOT EXISTS `__PREFIX__manystore_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('menu','file') NOT NULL DEFAULT 'file' COMMENT 'menu为菜单,file为权限节点',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '图标',
  `condition` varchar(255) NOT NULL DEFAULT '' COMMENT '条件',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为菜单',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `pid` (`pid`),
  KEY `weigh` (`weigh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家菜单节点表';


CREATE TABLE IF NOT EXISTS `__PREFIX__manystore_config_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `unique` varchar(20) NOT NULL COMMENT '字符唯一标识',
  `name` varchar(10) NOT NULL COMMENT '名称',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`unique`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='商家系统配置分组';


CREATE TABLE IF NOT EXISTS `__PREFIX__manystore_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '变量名',
  `group` varchar(30) NOT NULL DEFAULT '' COMMENT '分组',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '变量标题',
  `tip` varchar(100) NOT NULL DEFAULT '' COMMENT '变量描述',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
  `content` text NOT NULL COMMENT '变量字典数据',
  `rule` varchar(100) NOT NULL DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) NOT NULL DEFAULT '' COMMENT '扩展属性',
  `setting` varchar(255) NOT NULL DEFAULT '' COMMENT '配置',
  `default` text NOT NULL COMMENT '默认值',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家系统配置表';



CREATE TABLE IF NOT EXISTS `__PREFIX__manystore_value` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) NOT NULL COMMENT '商家ID',
  `store_id` int(10) NOT NULL COMMENT '管理员ID',
  `config_id` int(10) NOT NULL COMMENT '配置id',
  `value` text COMMENT '变量值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家系统配置数据表';


CREATE TABLE IF NOT EXISTS `__PREFIX__manystore_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `shop_id` int(10) NOT NULL DEFAULT '0' COMMENT '商家ID',
  `store_id` int(10) NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '管理员名字',
  `url` varchar(1500) NOT NULL DEFAULT '' COMMENT '操作页面',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '日志标题',
  `content` text NOT NULL COMMENT '内容',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'IP',
  `useragent` varchar(255) NOT NULL DEFAULT '' COMMENT 'User-Agent',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  PRIMARY KEY (`id`),
  KEY `name` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家管理员日志表';


CREATE TABLE IF NOT EXISTS `__PREFIX__manystore_command` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '类型',
  `params` varchar(1500) NOT NULL DEFAULT '' COMMENT '参数',
  `command` varchar(1500) NOT NULL DEFAULT '' COMMENT '命令',
  `content` text COMMENT '返回结果',
  `executetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行时间',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` enum('successed','failured') NOT NULL DEFAULT 'failured' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='商家在线命令表';


CREATE TABLE IF NOT EXISTS `__PREFIX__manystore_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商家ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '物理路径',
  `imagewidth` varchar(30) NOT NULL DEFAULT '' COMMENT '宽度',
  `imageheight` varchar(30) NOT NULL DEFAULT '' COMMENT '高度',
  `imagetype` varchar(30) NOT NULL DEFAULT '' COMMENT '图片类型',
  `imageframes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片帧数',
  `filename` varchar(100) NOT NULL DEFAULT '' COMMENT '文件名称',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `mimetype` varchar(100) NOT NULL DEFAULT '' COMMENT 'mime类型',
  `extparam` varchar(255) NOT NULL DEFAULT '' COMMENT '透传数据',
  `createtime` int(10) DEFAULT NULL COMMENT '创建日期',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `uploadtime` int(10) DEFAULT NULL COMMENT '上传时间',
  `storage` varchar(100) NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `sha1` varchar(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  COMMENT='附件表';





