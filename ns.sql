/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 80018
 Source Host           : localhost:3306
 Source Schema         : ns

 Target Server Type    : MySQL
 Target Server Version : 80018
 File Encoding         : 65001

 Date: 01/01/2021 09:21:35
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ns_admin
-- ----------------------------
DROP TABLE IF EXISTS `ns_admin`;
CREATE TABLE `ns_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `loginfailure` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '失败次数',
  `logintime` int(10) DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '登录IP',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(59) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'Session标识',
  `status` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='管理员表';

INSERT INTO `ns_admin` VALUES (1, 'admin', 'Admin', 'dafe9eadacdc3441137215784b83908c', '427367', '/assets/img/avatar.png', 'admin@admin.com', 0, 1609596586, '223.74.101.104', 1492186163, 1609596586, '535781ed-2c99-437d-9c71-03972951c036', 'normal');
INSERT INTO `ns_admin` VALUES (2, 'nansha', '南沙', 'dcae1ae8820aa78fa28225f867cc6925', 'YWU0Sm', '/assets/img/avatar.png', 'ns@qq.com', 0, 1609574592, '223.74.101.104', 1601370336, 1609574592, '15dfc393-96d6-4b6b-896e-9a667ffde9b0', 'normal');

-- ----------------------------
-- Table structure for ns_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `ns_admin_log`;
CREATE TABLE `ns_admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `username` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理员名字',
  `url` varchar(1500) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '操作页面',
  `title` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '日志标题',
  `content` mediumtext COLLATE utf8mb4_general_ci NOT NULL COMMENT '内容',
  `ip` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'IP',
  `useragent` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'User-Agent',
  `createtime` int(10) DEFAULT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`),
  KEY `name` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=532 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='管理员日志表';

-- ----------------------------
-- Table structure for ns_ads
-- ----------------------------
DROP TABLE IF EXISTS `ns_ads`;
CREATE TABLE `ns_ads` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '广告类型:1=首页banner,2=首页弹窗',
  `title` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '图片',
  `link_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '跳转类型:1=内部跳转,2=外部跳转,3=关联小程序',
  `link_info` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '跳转地址',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态:0=禁用,1=启用',
  `sort` int(10) NOT NULL DEFAULT '1' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='广告管理';

-- ----------------------------
-- Table structure for ns_area
-- ----------------------------
DROP TABLE IF EXISTS `ns_area`;
CREATE TABLE `ns_area` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int(10) DEFAULT NULL COMMENT '父id',
  `shortname` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '简称',
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '名称',
  `mergename` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '全称',
  `level` tinyint(4) DEFAULT NULL COMMENT '层级 0 1 2 省市区县',
  `pinyin` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '拼音',
  `code` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '长途区号',
  `zip` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '邮编',
  `first` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '首字母',
  `lng` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '经度',
  `lat` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '纬度',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=3750 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='地区表';

-- ----------------------------
-- Table structure for ns_attachment
-- ----------------------------
DROP TABLE IF EXISTS `ns_attachment`;
CREATE TABLE `ns_attachment` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `url` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '物理路径',
  `imagewidth` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '宽度',
  `imageheight` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '高度',
  `imagetype` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片类型',
  `imageframes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片帧数',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `mimetype` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'mime类型',
  `extparam` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '透传数据',
  `createtime` int(10) DEFAULT NULL COMMENT '创建日期',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `uploadtime` int(10) DEFAULT NULL COMMENT '上传时间',
  `storage` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `sha1` varchar(40) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='附件表';

-- ----------------------------
-- Table structure for ns_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `ns_auth_group`;
CREATE TABLE `ns_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父组别',
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '组名',
  `rules` mediumtext COLLATE utf8mb4_general_ci NOT NULL COMMENT '规则ID',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `status` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='分组表';


INSERT INTO `ns_auth_group` VALUES (1, 0, 'Admin group', '*', 1490883540, 149088354, 'normal');
INSERT INTO `ns_auth_group` VALUES (2, 1, '管理组', '7,23,24,25,26,27,28,29,30,31,32,33,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,2,8', 1490883540, 1608474421, 'normal');
INSERT INTO `ns_auth_group` VALUES (3, 2, 'Third group', '1,4,9,10,11,13,14,15,16,17,40,41,42,43,44,45,46,47,48,49,50,55,56,57,58,59,60,61,62,63,64,65,5', 1490883540, 1502205322, 'normal');
INSERT INTO `ns_auth_group` VALUES (4, 1, 'Second group 2', '1,4,13,14,15,16,17,55,56,57,58,59,60,61,62,63,64,65', 1490883540, 1502205350, 'normal');
INSERT INTO `ns_auth_group` VALUES (5, 2, 'Third group 2', '1,2,6,7,8,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34', 1490883540, 1502205344, 'normal');


-- ----------------------------
-- Table structure for ns_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `ns_auth_group_access`;
CREATE TABLE `ns_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '会员ID',
  `group_id` int(10) unsigned NOT NULL COMMENT '级别ID',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='权限分组表';

INSERT INTO `ns_auth_group_access` VALUES (1, 1);
INSERT INTO `ns_auth_group_access` VALUES (2, 2);

-- ----------------------------
-- Table structure for ns_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `ns_auth_rule`;
CREATE TABLE `ns_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('menu','file') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'file' COMMENT 'menu为菜单,file为权限节点',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '规则名称',
  `route` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '路由规则',
  `title` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图标',
  `condition` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '条件',
  `remark` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为菜单',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `pid` (`pid`),
  KEY `weigh` (`weigh`)
) ENGINE=InnoDB AUTO_INCREMENT=174 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='节点表';


INSERT INTO `ns_auth_rule` VALUES (1, 'file', 0, 'dashboard', '', 'Dashboard', 'fa fa-dashboard', '', 'Dashboard tips', 1, 1497429920, 1497429920, 143, 'normal');
INSERT INTO `ns_auth_rule` VALUES (2, 'file', 0, 'general', '', 'General', 'fa fa-cogs', '', '', 1, 1497429920, 1497430169, 137, 'normal');
INSERT INTO `ns_auth_rule` VALUES (3, 'file', 0, 'category', '', 'Category', 'fa fa-leaf', '', 'Category tips', 1, 1497429920, 1497429920, 119, 'normal');
INSERT INTO `ns_auth_rule` VALUES (4, 'file', 0, 'addon', '', 'Addon', 'fa fa-rocket', '', 'Addon tips', 1, 1502035509, 1502035509, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (5, 'file', 0, 'auth', '', 'Auth', 'fa fa-group', '', '', 1, 1497429920, 1497430092, 99, 'normal');
INSERT INTO `ns_auth_rule` VALUES (6, 'file', 2, 'general/config', 'general.config/index', 'Config', 'fa fa-cog', '', 'Config tips', 1, 1497429920, 1497430683, 60, 'normal');
INSERT INTO `ns_auth_rule` VALUES (7, 'file', 2, 'general/attachment', 'general.attachment/index', 'Attachment', 'fa fa-file-image-o', '', 'Attachment tips', 1, 1497429920, 1497430699, 53, 'normal');
INSERT INTO `ns_auth_rule` VALUES (8, 'file', 2, 'general/profile', 'general.profile/index', 'Profile', 'fa fa-user', '', '', 1, 1497429920, 1497429920, 34, 'normal');
INSERT INTO `ns_auth_rule` VALUES (9, 'file', 5, 'auth/admin', 'auth.admin/index', 'Admin', 'fa fa-user', '', 'Admin tips', 1, 1497429920, 1497430320, 118, 'normal');
INSERT INTO `ns_auth_rule` VALUES (10, 'file', 5, 'auth/adminlog', 'auth.adminlog/index', 'Admin log', 'fa fa-list-alt', '', 'Admin log tips', 1, 1497429920, 1497430307, 113, 'normal');
INSERT INTO `ns_auth_rule` VALUES (11, 'file', 5, 'auth/group', 'auth.group/index', 'Group', 'fa fa-group', '', 'Group tips', 1, 1497429920, 1497429920, 109, 'normal');
INSERT INTO `ns_auth_rule` VALUES (12, 'file', 5, 'auth/rule', 'auth.rule/index', 'Rule', 'fa fa-bars', '', 'Rule tips', 1, 1497429920, 1497430581, 104, 'normal');
INSERT INTO `ns_auth_rule` VALUES (13, 'file', 1, 'dashboard/index', '', 'View', 'fa fa-circle-o', '', '', 0, 1497429920, 1602136933, 136, 'normal');
INSERT INTO `ns_auth_rule` VALUES (14, 'file', 1, 'dashboard/add', '', 'Add', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 135, 'normal');
INSERT INTO `ns_auth_rule` VALUES (15, 'file', 1, 'dashboard/del', '', 'Delete', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 133, 'normal');
INSERT INTO `ns_auth_rule` VALUES (16, 'file', 1, 'dashboard/edit', '', 'Edit', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 134, 'normal');
INSERT INTO `ns_auth_rule` VALUES (17, 'file', 1, 'dashboard/multi', '', 'Multi', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 132, 'normal');
INSERT INTO `ns_auth_rule` VALUES (18, 'file', 6, 'general/config/index', 'general.config/index', 'View', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 52, 'normal');
INSERT INTO `ns_auth_rule` VALUES (19, 'file', 6, 'general/config/add', 'general.config/add', 'Add', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 51, 'normal');
INSERT INTO `ns_auth_rule` VALUES (20, 'file', 6, 'general/config/edit', 'general.config/edit', 'Edit', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 50, 'normal');
INSERT INTO `ns_auth_rule` VALUES (21, 'file', 6, 'general/config/del', 'general.config/del', 'Delete', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 49, 'normal');
INSERT INTO `ns_auth_rule` VALUES (22, 'file', 6, 'general/config/multi', 'general.config/multi', 'Multi', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 48, 'normal');
INSERT INTO `ns_auth_rule` VALUES (23, 'file', 7, 'general/attachment/index', 'general.attachment/index', 'View', 'fa fa-circle-o', '', 'Attachment tips', 0, 1497429920, 1497429920, 59, 'normal');
INSERT INTO `ns_auth_rule` VALUES (24, 'file', 7, 'general/attachment/select', 'general.attachment/select', 'Select attachment', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 58, 'normal');
INSERT INTO `ns_auth_rule` VALUES (25, 'file', 7, 'general/attachment/add', 'general.attachment/add', 'Add', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 57, 'normal');
INSERT INTO `ns_auth_rule` VALUES (26, 'file', 7, 'general/attachment/edit', 'general.attachment/edit', 'Edit', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 56, 'normal');
INSERT INTO `ns_auth_rule` VALUES (27, 'file', 7, 'general/attachment/del', 'general.attachment/del', 'Delete', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 55, 'normal');
INSERT INTO `ns_auth_rule` VALUES (28, 'file', 7, 'general/attachment/multi', 'general.attachment/multi', 'Multi', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 54, 'normal');
INSERT INTO `ns_auth_rule` VALUES (29, 'file', 8, 'general/profile/index', 'general.profile/index', 'View', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 33, 'normal');
INSERT INTO `ns_auth_rule` VALUES (30, 'file', 8, 'general/profile/update', 'general.profile/update', 'Update profile', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 32, 'normal');
INSERT INTO `ns_auth_rule` VALUES (31, 'file', 8, 'general/profile/add', 'general.profile/add', 'Add', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 31, 'normal');
INSERT INTO `ns_auth_rule` VALUES (32, 'file', 8, 'general/profile/edit', 'general.profile/edit', 'Edit', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 30, 'normal');
INSERT INTO `ns_auth_rule` VALUES (33, 'file', 8, 'general/profile/del', 'general.profile/del', 'Delete', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 29, 'normal');
INSERT INTO `ns_auth_rule` VALUES (34, 'file', 8, 'general/profile/multi', 'general.profile/multi', 'Multi', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 28, 'normal');
INSERT INTO `ns_auth_rule` VALUES (35, 'file', 3, 'category/index', '', 'View', 'fa fa-circle-o', '', 'Category tips', 0, 1497429920, 1497429920, 142, 'normal');
INSERT INTO `ns_auth_rule` VALUES (36, 'file', 3, 'category/add', '', 'Add', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 141, 'normal');
INSERT INTO `ns_auth_rule` VALUES (37, 'file', 3, 'category/edit', '', 'Edit', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 140, 'normal');
INSERT INTO `ns_auth_rule` VALUES (38, 'file', 3, 'category/del', '', 'Delete', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 139, 'normal');
INSERT INTO `ns_auth_rule` VALUES (39, 'file', 3, 'category/multi', '', 'Multi', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 138, 'normal');
INSERT INTO `ns_auth_rule` VALUES (40, 'file', 9, 'auth/admin/index', 'auth.admin/index', 'View', 'fa fa-circle-o', '', 'Admin tips', 0, 1497429920, 1497429920, 117, 'normal');
INSERT INTO `ns_auth_rule` VALUES (41, 'file', 9, 'auth/admin/add', 'auth.admin/add', 'Add', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 116, 'normal');
INSERT INTO `ns_auth_rule` VALUES (42, 'file', 9, 'auth/admin/edit', 'auth.admin/edit', 'Edit', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 115, 'normal');
INSERT INTO `ns_auth_rule` VALUES (43, 'file', 9, 'auth/admin/del', 'auth.admin/del', 'Delete', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 114, 'normal');
INSERT INTO `ns_auth_rule` VALUES (44, 'file', 10, 'auth/adminlog/index', 'auth.adminlog/index', 'View', 'fa fa-circle-o', '', 'Admin log tips', 0, 1497429920, 1497429920, 112, 'normal');
INSERT INTO `ns_auth_rule` VALUES (45, 'file', 10, 'auth/adminlog/detail', 'auth.adminlog/detail', 'Detail', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 111, 'normal');
INSERT INTO `ns_auth_rule` VALUES (46, 'file', 10, 'auth/adminlog/del', 'auth.adminlog/del', 'Delete', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 110, 'normal');
INSERT INTO `ns_auth_rule` VALUES (47, 'file', 11, 'auth/group/index', 'auth.group/index', 'View', 'fa fa-circle-o', '', 'Group tips', 0, 1497429920, 1497429920, 108, 'normal');
INSERT INTO `ns_auth_rule` VALUES (48, 'file', 11, 'auth/group/add', 'auth.group/add', 'Add', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 107, 'normal');
INSERT INTO `ns_auth_rule` VALUES (49, 'file', 11, 'auth/group/edit', 'auth.group/edit', 'Edit', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 106, 'normal');
INSERT INTO `ns_auth_rule` VALUES (50, 'file', 11, 'auth/group/del', 'auth.group/del', 'Delete', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 105, 'normal');
INSERT INTO `ns_auth_rule` VALUES (51, 'file', 12, 'auth/rule/index', 'auth.rule/index', 'View', 'fa fa-circle-o', '', 'Rule tips', 0, 1497429920, 1497429920, 103, 'normal');
INSERT INTO `ns_auth_rule` VALUES (52, 'file', 12, 'auth/rule/add', 'auth.rule/add', 'Add', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 102, 'normal');
INSERT INTO `ns_auth_rule` VALUES (53, 'file', 12, 'auth/rule/edit', 'auth.rule/edit', 'Edit', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 101, 'normal');
INSERT INTO `ns_auth_rule` VALUES (54, 'file', 12, 'auth/rule/del', 'auth.rule/del', 'Delete', 'fa fa-circle-o', '', '', 0, 1497429920, 1497429920, 100, 'normal');
INSERT INTO `ns_auth_rule` VALUES (55, 'file', 4, 'addon/index', '', 'View', 'fa fa-circle-o', '', 'Addon tips', 0, 1502035509, 1502035509, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (56, 'file', 4, 'addon/add', '', 'Add', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (57, 'file', 4, 'addon/edit', '', 'Edit', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (58, 'file', 4, 'addon/del', '', 'Delete', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (59, 'file', 4, 'addon/downloaded', '', 'Local addon', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (60, 'file', 4, 'addon/state', '', 'Update state', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (63, 'file', 4, 'addon/config', '', 'Setting', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (64, 'file', 4, 'addon/refresh', '', 'Refresh', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (65, 'file', 4, 'addon/multi', '', 'Multi', 'fa fa-circle-o', '', '', 0, 1502035509, 1502035509, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (66, 'file', 0, 'user', '', 'User', 'fa fa-list', '', '', 1, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (67, 'file', 66, 'user/user', 'user.user/index', 'User', 'fa fa-user', '', '', 1, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (68, 'file', 67, 'user/user/index', 'user.user/index', 'View', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (69, 'file', 67, 'user/user/edit', 'user.user/edit', 'Edit', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (70, 'file', 67, 'user/user/add', 'user.user/add', 'Add', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (71, 'file', 67, 'user/user/del', 'user.user/del', 'Del', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (72, 'file', 67, 'user/user/multi', 'user.user/multi', 'Multi', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (73, 'file', 66, 'user/group', 'user.group/index', 'User group', 'fa fa-users', '', '', 1, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (74, 'file', 73, 'user/group/add', 'user.group/add', 'Add', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (75, 'file', 73, 'user/group/edit', 'user.group/edit', 'Edit', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (76, 'file', 73, 'user/group/index', 'user.group/index', 'View', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (77, 'file', 73, 'user/group/del', 'user.group/del', 'Del', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (78, 'file', 73, 'user/group/multi', 'user.group/multi', 'Multi', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (79, 'file', 66, 'user/rule', 'user.rule/index', 'User rule', 'fa fa-circle-o', '', '', 1, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (80, 'file', 79, 'user/rule/index', 'user.rule/index', 'View', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (81, 'file', 79, 'user/rule/del', 'user.rule/del', 'Del', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (82, 'file', 79, 'user/rule/add', 'user.rule/add', 'Add', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (83, 'file', 79, 'user/rule/edit', 'user.rule/edit', 'Edit', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (84, 'file', 79, 'user/rule/multi', 'user.rule/multi', 'Multi', 'fa fa-circle-o', '', '', 0, 1516374729, 1516374729, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (85, 'file', 0, 'example', '', '开发示例管理', 'fa fa-magic', '', '', 1, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (86, 'file', 85, 'example/bootstraptable', 'example.bootstraptable/index', '表格完整示例', 'fa fa-table', '', '', 1, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (87, 'file', 86, 'example/bootstraptable/index', 'example.bootstraptable/index', '查看', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (88, 'file', 86, 'example/bootstraptable/detail', 'example.bootstraptable/detail', '详情', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (89, 'file', 86, 'example/bootstraptable/change', 'example.bootstraptable/change', '变更', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (90, 'file', 86, 'example/bootstraptable/del', 'example.bootstraptable/del', '删除', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (91, 'file', 86, 'example/bootstraptable/multi', 'example.bootstraptable/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (92, 'file', 85, 'example/customsearch', 'example.customsearch/index', '自定义搜索', 'fa fa-table', '', '', 1, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (93, 'file', 92, 'example/customsearch/index', 'example.customsearch/index', '查看', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (94, 'file', 92, 'example/customsearch/del', 'example.customsearch/del', '删除', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (95, 'file', 92, 'example/customsearch/multi', 'example.customsearch/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (96, 'file', 85, 'example/customform', 'example.customform/index', '自定义表单示例', 'fa fa-edit', '', '', 1, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (97, 'file', 96, 'example/customform/index', 'example.customform/index', '查看', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (98, 'file', 85, 'example/tablelink', 'example.tablelink/index', '表格联动示例', 'fa fa-table', '', '', 1, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (99, 'file', 98, 'example/tablelink/index', 'example.tablelink/index', '查看', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (100, 'file', 85, 'example/colorbadge', 'example.colorbadge/index', '彩色角标', 'fa fa-table', '', '', 1, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (101, 'file', 100, 'example/colorbadge/index', 'example.colorbadge/index', '查看', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (102, 'file', 100, 'example/colorbadge/del', 'example.colorbadge/del', '删除', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (103, 'file', 100, 'example/colorbadge/multi', 'example.colorbadge/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (104, 'file', 85, 'example/controllerjump', 'example.controllerjump/index', '控制器间跳转', 'fa fa-table', '', '', 1, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (105, 'file', 104, 'example/controllerjump/index', 'example.controllerjump/index', '查看', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (106, 'file', 104, 'example/controllerjump/del', 'example.controllerjump/del', '删除', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (107, 'file', 104, 'example/controllerjump/multi', 'example.controllerjump/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (108, 'file', 85, 'example/cxselect', 'example.cxselect/index', '多级联动', 'fa fa-table', '', '', 1, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (109, 'file', 108, 'example/cxselect/index', 'example.cxselect/index', '查看', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (110, 'file', 108, 'example/cxselect/del', 'example.cxselect/del', '删除', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (111, 'file', 108, 'example/cxselect/multi', 'example.cxselect/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (112, 'file', 85, 'example/multitable', 'example.multitable/index', '多表格示例', 'fa fa-table', '', '', 1, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (113, 'file', 112, 'example/multitable/index', 'example.multitable/index', '查看', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (114, 'file', 112, 'example/multitable/del', 'example.multitable/del', '删除', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (115, 'file', 112, 'example/multitable/multi', 'example.multitable/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (116, 'file', 85, 'example/relationmodel', 'example.relationmodel/index', '关联模型示例', 'fa fa-table', '', '', 1, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (117, 'file', 116, 'example/relationmodel/index', 'example.relationmodel/index', '查看', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (118, 'file', 116, 'example/relationmodel/del', 'example.relationmodel/del', '删除', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (119, 'file', 116, 'example/relationmodel/multi', 'example.relationmodel/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (120, 'file', 85, 'example/tabletemplate', 'example.tabletemplate/index', '表格模板示例', 'fa fa-table', '', '', 1, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (121, 'file', 120, 'example/tabletemplate/index', 'example.tabletemplate/index', '查看', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (122, 'file', 120, 'example/tabletemplate/detail', 'example.tabletemplate/detail', '详情', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (123, 'file', 120, 'example/tabletemplate/del', 'example.tabletemplate/del', '删除', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (124, 'file', 120, 'example/tabletemplate/multi', 'example.tabletemplate/multi', '批量更新', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (125, 'file', 85, 'example/baidumap', 'example.baidumap/index', '百度地图示例', 'fa fa-map-pin', '', '', 1, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (126, 'file', 125, 'example/baidumap/index', 'example.baidumap/index', '查看', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (127, 'file', 125, 'example/baidumap/map', 'example.baidumap/map', '详情', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (128, 'file', 125, 'example/baidumap/del', 'example.baidumap/del', '删除', 'fa fa-circle-o', '', '', 0, 1599917333, 1599917333, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (129, 'file', 0, 'ns', 'ns', '南沙', 'fa fa-list', '', '', 1, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (130, 'file', 129, 'ns/content', 'ns.content/index', '内容管理', 'fa fa-circle-o', '', '', 1, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (131, 'file', 130, 'ns/content/index', 'ns.content/index', '查看', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (132, 'file', 130, 'ns/content/add', 'ns.content/add', '添加', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (133, 'file', 130, 'ns/content/edit', 'ns.content/edit', '编辑', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (134, 'file', 130, 'ns/content/del', 'ns.content/del', '删除', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (135, 'file', 130, 'ns/content/multi', 'ns.content/multi', '批量更新', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (136, 'file', 129, 'ns/ads', 'ns.ads/index', '广告管理', 'fa fa-circle-o', '', '', 1, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (137, 'file', 136, 'ns/ads/index', 'ns.ads/index', '查看', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (138, 'file', 136, 'ns/ads/add', 'ns.ads/add', '添加', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (139, 'file', 136, 'ns/ads/edit', 'ns.ads/edit', '编辑', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (140, 'file', 136, 'ns/ads/del', 'ns.ads/del', '删除', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (141, 'file', 136, 'ns/ads/multi', 'ns.ads/multi', '批量更新', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (142, 'file', 129, 'ns/column', 'ns.column/index', '栏目管理', 'fa fa-circle-o', '', '', 1, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (143, 'file', 142, 'ns/column/index', 'ns.column/index', '查看', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (144, 'file', 142, 'ns/column/add', 'ns.column/add', '添加', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (145, 'file', 142, 'ns/column/edit', 'ns.column/edit', '编辑', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (146, 'file', 142, 'ns/column/del', 'ns.column/del', '删除', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (147, 'file', 142, 'ns/column/multi', 'ns.column/multi', '批量更新', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (148, 'file', 129, 'ns/wxuser', 'ns.wxuser/index', '微信用户', 'fa fa-circle-o', '', '', 1, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (149, 'file', 148, 'ns/wxuser/index', 'ns.wxuser/index', '查看', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (150, 'file', 148, 'ns/wxuser/add', 'ns.wxuser/add', '添加', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (151, 'file', 148, 'ns/wxuser/edit', 'ns.wxuser/edit', '编辑', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (152, 'file', 148, 'ns/wxuser/del', 'ns.wxuser/del', '删除', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (153, 'file', 148, 'ns/wxuser/multi', 'ns.wxuser/multi', '批量更新', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (154, 'file', 129, 'ns/navigation', 'ns.navigation/index', '导航管理', 'fa fa-circle-o', '', '', 1, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (155, 'file', 154, 'ns/navigation/index', 'ns.navigation/index', '查看', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (156, 'file', 154, 'ns/navigation/add', 'ns.navigation/add', '添加', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (157, 'file', 154, 'ns/navigation/edit', 'ns.navigation/edit', '编辑', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (158, 'file', 154, 'ns/navigation/del', 'ns.navigation/del', '删除', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (159, 'file', 154, 'ns/navigation/multi', 'ns.navigation/multi', '批量更新', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (160, 'file', 129, 'ns/comment', 'ns.comment/index', '评论管理', 'fa fa-comment', '', '', 1, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (161, 'file', 160, 'ns/comment/index', 'ns.comment/index', '查看', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (162, 'file', 160, 'ns/comment/add', 'ns.comment/add', '添加', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (163, 'file', 160, 'ns/comment/edit', 'ns.comment/edit', '编辑', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (164, 'file', 160, 'ns/comment/del', 'ns.comment/del', '删除', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (165, 'file', 160, 'ns/comment/multi', 'ns.comment/multi', '批量更新', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (166, 'file', 129, 'ns/notice', 'ns.notice/index', '公告管理', 'fa fa-circle-o', '', '', 1, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (167, 'file', 166, 'ns/notice/index', 'ns.notice/index', '查看', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (168, 'file', 166, 'ns/notice/add', 'ns.notice/add', '添加', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (169, 'file', 166, 'ns/notice/edit', 'ns.notice/edit', '编辑', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (170, 'file', 166, 'ns/notice/del', 'ns.notice/del', '删除', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (171, 'file', 166, 'ns/notice/multi', 'ns.notice/multi', '批量更新', 'fa fa-circle-o', '', '', 0, NULL, NULL, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (172, 'file', 130, 'ns/column/cxselect', 'ns.column/cxselect', '栏目分类', 'fa fa-circle-o', '', '', 0, 1605094464, 1605094575, 0, 'normal');
INSERT INTO `ns_auth_rule` VALUES (173, 'file', 130, 'ns/content/refresh', 'ns.content/refresh', '刷新内容发布时间', 'fa fa-circle-o', '', '', 0, 1605604933, 1605604933, 0, 'normal');


-- ----------------------------
-- Table structure for ns_category
-- ----------------------------
DROP TABLE IF EXISTS `ns_category`;
CREATE TABLE `ns_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `type` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '栏目类型',
  `name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `nickname` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `flag` set('hot','index','recommend') COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `image` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片',
  `keywords` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '关键字',
  `description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `diyname` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '自定义名称',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `weigh` (`weigh`,`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='分类表';

-- ----------------------------
-- Table structure for ns_column
-- ----------------------------
DROP TABLE IF EXISTS `ns_column`;
CREATE TABLE `ns_column` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '父栏目',
  `icon` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'icon',
  `price` decimal(9,2) NOT NULL DEFAULT '0.00' COMMENT '发帖金额',
  `refresh_price` decimal(9,2) NOT NULL DEFAULT '0.00' COMMENT '刷新金额',
  `name` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '栏目名称',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `sort` int(10) NOT NULL DEFAULT '1' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态:0=无效,1=有效',
  `template` text COLLATE utf8mb4_general_ci COMMENT '文章模板',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='专栏管理';

-- ----------------------------
-- Table structure for ns_column_content
-- ----------------------------
DROP TABLE IF EXISTS `ns_column_content`;
CREATE TABLE `ns_column_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `column_id` int(10) NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `cid` int(10) NOT NULL DEFAULT '0' COMMENT '内容ID',
  `top` tinyint(4) NOT NULL DEFAULT '0' COMMENT '置顶:0=不置顶,1=置顶',
  `expiry_time` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态:0=待审核,1=通过,2=不通过',
  `pay_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '支付状态:0=未支付,1=已支付',
  `create_time` int(10) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  `is_online` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '0下线1上线',
  PRIMARY KEY (`id`),
  KEY `idx_cid` (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for ns_comment
-- ----------------------------
DROP TABLE IF EXISTS `ns_comment`;
CREATE TABLE `ns_comment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cid` int(10) NOT NULL DEFAULT '0' COMMENT '内容id',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '评论的id',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '评论人',
  `to_uid` int(10) unsigned DEFAULT '0' COMMENT '被评论人',
  `content` varchar(256) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '评论内容',
  `pictures` json DEFAULT NULL COMMENT '评论图',
  `like_count` int(10) NOT NULL DEFAULT '0' COMMENT '点赞数',
  `replay_count` int(10) NOT NULL DEFAULT '0' COMMENT '回复数',
  `delete_time` int(10) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_online` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '0下线1上线',
  PRIMARY KEY (`id`),
  KEY `idx_cid` (`cid`),
  KEY `idx_uid` (`uid`),
  KEY `idx_pid` (`pid`) USING BTREE,
  KEY `idx_touid_createtime` (`to_uid`,`create_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for ns_config
-- ----------------------------
DROP TABLE IF EXISTS `ns_config`;
CREATE TABLE `ns_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '变量名',
  `group` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分组',
  `title` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '变量标题',
  `tip` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '变量描述',
  `type` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
  `value` mediumtext COLLATE utf8mb4_general_ci NOT NULL COMMENT '变量值',
  `content` mediumtext COLLATE utf8mb4_general_ci NOT NULL COMMENT '变量字典数据',
  `rule` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '扩展属性',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='系统配置';

-- ----------------------------
-- Table structure for ns_content
-- ----------------------------
DROP TABLE IF EXISTS `ns_content`;
CREATE TABLE `ns_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `column_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '栏目ids',
  `mobile` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `contacts` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '联系人',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '内容',
  `pictures` json DEFAULT NULL COMMENT '图片',
  `like_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `share_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分享数',
  `comment_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `view_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `address` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '具体位置',
  `lng` decimal(9,6) NOT NULL DEFAULT '0.000000' COMMENT '经度',
  `lat` decimal(9,6) NOT NULL DEFAULT '0.000000' COMMENT '纬度',
  `geohash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'geohash',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `expiry_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '置顶过期时间',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态:0=待审核,1=通过,2=不通过',
  `top` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '置顶:0=不置顶,1=置顶',
  `pay_status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '状态:0=待支付,1=已支付,2=支付失败',
  `is_online` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '0下线1上线',
  `extra` json DEFAULT NULL COMMENT '附加的管理员可见信息',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_expiry_time` (`expiry_time`),
  KEY `idx_geohash` (`geohash`),
  FULLTEXT KEY `ft_index` (`content`) /*!50100 WITH PARSER `ngram` */ 
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for ns_ems
-- ----------------------------
DROP TABLE IF EXISTS `ns_ems`;
CREATE TABLE `ns_ems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `event` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '事件',
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `code` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '验证码',
  `times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '验证次数',
  `ip` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'IP',
  `createtime` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='邮箱验证码表';

-- ----------------------------
-- Table structure for ns_like_log
-- ----------------------------
DROP TABLE IF EXISTS `ns_like_log`;
CREATE TABLE `ns_like_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0',
  `target_id` int(10) NOT NULL DEFAULT '0' COMMENT '内容id',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1内容2评论',
  `create_time` int(10) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uid_targetId_type` (`uid`,`target_id`,`type`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Table structure for ns_navigation
-- ----------------------------
DROP TABLE IF EXISTS `ns_navigation`;
CREATE TABLE `ns_navigation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '导航名称',
  `picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '导航图标',
  `link_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '跳转类型:1=内部跳转,2=外部跳转,3=关联小程序',
  `link_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '跳转内容',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态:0=禁用,1=启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='导航管理';

-- ----------------------------
-- Table structure for ns_news
-- ----------------------------
DROP TABLE IF EXISTS `ns_news`;
CREATE TABLE `ns_news` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `content` text COLLATE utf8mb4_general_ci COMMENT '内容',
  `cate_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '分类',
  `view_count` int(10) NOT NULL DEFAULT '0' COMMENT '阅读数',
  `comment_count` int(10) NOT NULL DEFAULT '0' COMMENT '评论数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='资讯管理';

-- ----------------------------
-- Table structure for ns_notice
-- ----------------------------
DROP TABLE IF EXISTS `ns_notice`;
CREATE TABLE `ns_notice` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `content` text COLLATE utf8mb4_general_ci COMMENT '内容',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态:0=下线,1=上线',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '类型:1=头条必读,2=帮助中心,3发布须知',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='公告管理';

-- ----------------------------
-- Table structure for ns_orders
-- ----------------------------
DROP TABLE IF EXISTS `ns_orders`;
CREATE TABLE `ns_orders` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '订单号',
  `cid` int(10) NOT NULL DEFAULT '0' COMMENT '内容ID',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT 'UID',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态:0=待支付,1=已支付,2=支付失败',
  `pay_time` int(10) NOT NULL DEFAULT '0' COMMENT '支付完成时间',
  `order_amount` decimal(9,2) NOT NULL DEFAULT '0.00' COMMENT '订单总金额',
  `top_id` int(10) NOT NULL DEFAULT '0' COMMENT '置顶类型',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `bank_type` varchar(16) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '付款银行	',
  `transaction_id` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '微信支付订单号	',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_order_sn` (`order_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for ns_sms
-- ----------------------------
DROP TABLE IF EXISTS `ns_sms`;
CREATE TABLE `ns_sms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `event` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '事件',
  `mobile` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `code` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '验证码',
  `times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '验证次数',
  `ip` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'IP',
  `createtime` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='短信验证码表';

-- ----------------------------
-- Table structure for ns_test
-- ----------------------------
DROP TABLE IF EXISTS `ns_test`;
CREATE TABLE `ns_test` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID(单选)',
  `category_ids` varchar(100) COLLATE utf8mb4_general_ci NOT NULL COMMENT '分类ID(多选)',
  `week` enum('monday','tuesday','wednesday') COLLATE utf8mb4_general_ci NOT NULL COMMENT '星期(单选):monday=星期一,tuesday=星期二,wednesday=星期三',
  `flag` set('hot','index','recommend') COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标志(多选):hot=热门,index=首页,recommend=推荐',
  `genderdata` enum('male','female') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'male' COMMENT '性别(单选):male=男,female=女',
  `hobbydata` set('music','reading','swimming') COLLATE utf8mb4_general_ci NOT NULL COMMENT '爱好(多选):music=音乐,reading=读书,swimming=游泳',
  `title` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `content` mediumtext COLLATE utf8mb4_general_ci NOT NULL COMMENT '内容',
  `image` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片',
  `images` varchar(1500) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图片组',
  `attachfile` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '附件',
  `keywords` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '关键字',
  `description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `city` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '省市',
  `json` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '配置:key=名称,value=值',
  `price` float(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '价格',
  `views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点击',
  `startdate` date DEFAULT NULL COMMENT '开始日期',
  `activitytime` datetime DEFAULT NULL COMMENT '活动时间(datetime)',
  `year` year(4) DEFAULT NULL COMMENT '年',
  `times` time DEFAULT NULL COMMENT '时间',
  `refreshtime` int(10) DEFAULT NULL COMMENT '刷新时间(int)',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `deletetime` int(10) DEFAULT NULL COMMENT '删除时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `switch` tinyint(1) NOT NULL DEFAULT '0' COMMENT '开关',
  `status` enum('normal','hidden') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
  `state` enum('0','1','2') COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1' COMMENT '状态值:0=禁用,1=正常,2=推荐',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='测试表';

-- ----------------------------
-- Table structure for ns_top_config
-- ----------------------------
DROP TABLE IF EXISTS `ns_top_config`;
CREATE TABLE `ns_top_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `price` decimal(9,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `type` tinyint(4) NOT NULL COMMENT '期限类型:1=一天,2=一周,3=一个月',
  `sort` tinyint(4) NOT NULL COMMENT '排序',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='置顶配置';

-- ----------------------------
-- Table structure for ns_user
-- ----------------------------
DROP TABLE IF EXISTS `ns_user`;
CREATE TABLE `ns_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '组别ID',
  `username` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '密码盐',
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `mobile` varchar(11) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `avatar` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '头像',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '等级',
  `gender` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `bio` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '格言',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '余额',
  `score` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '积分',
  `successions` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '连续登录天数',
  `maxsuccessions` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '最大连续登录天数',
  `prevtime` int(10) NOT NULL COMMENT '上次登录时间',
  `logintime` int(10) NOT NULL DEFAULT '0' COMMENT '登录时间',
  `loginip` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '登录IP',
  `loginfailure` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '失败次数',
  `joinip` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '加入IP',
  `jointime` int(10) NOT NULL COMMENT '加入时间',
  `createtime` int(10) NOT NULL COMMENT '创建时间',
  `updatetime` int(10) NOT NULL COMMENT '更新时间',
  `token` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'Token',
  `status` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '状态',
  `verification` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '验证',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `mobile` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='会员表';

-- ----------------------------
-- Table structure for ns_user_group
-- ----------------------------
DROP TABLE IF EXISTS `ns_user_group`;
CREATE TABLE `ns_user_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '组名',
  `rules` mediumtext COLLATE utf8mb4_general_ci COMMENT '权限节点',
  `createtime` int(10) DEFAULT NULL COMMENT '添加时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `status` enum('normal','hidden') COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='会员组表';

-- ----------------------------
-- Table structure for ns_user_money_log
-- ----------------------------
DROP TABLE IF EXISTS `ns_user_money_log`;
CREATE TABLE `ns_user_money_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更余额',
  `before` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更前余额',
  `after` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更后余额',
  `memo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='会员余额变动表';

-- ----------------------------
-- Table structure for ns_user_rule
-- ----------------------------
DROP TABLE IF EXISTS `ns_user_rule`;
CREATE TABLE `ns_user_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) DEFAULT NULL COMMENT '父ID',
  `name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '名称',
  `title` varchar(50) COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '标题',
  `remark` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '备注',
  `ismenu` tinyint(1) DEFAULT NULL COMMENT '是否菜单',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(10) DEFAULT '0' COMMENT '权重',
  `status` enum('normal','hidden') COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='会员规则表';

-- ----------------------------
-- Table structure for ns_user_score_log
-- ----------------------------
DROP TABLE IF EXISTS `ns_user_score_log`;
CREATE TABLE `ns_user_score_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `score` int(10) NOT NULL DEFAULT '0' COMMENT '变更积分',
  `before` int(10) NOT NULL DEFAULT '0' COMMENT '变更前积分',
  `after` int(10) NOT NULL DEFAULT '0' COMMENT '变更后积分',
  `memo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='会员积分变动表';

-- ----------------------------
-- Table structure for ns_user_token
-- ----------------------------
DROP TABLE IF EXISTS `ns_user_token`;
CREATE TABLE `ns_user_token` (
  `token` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Token',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `expiretime` int(10) DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='会员Token表';

-- ----------------------------
-- Table structure for ns_version
-- ----------------------------
DROP TABLE IF EXISTS `ns_version`;
CREATE TABLE `ns_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `oldversion` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '旧版本号',
  `newversion` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '新版本号',
  `packagesize` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '包大小',
  `content` varchar(500) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '升级内容',
  `downloadurl` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '下载地址',
  `enforce` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '强制更新',
  `createtime` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT COMMENT='版本表';

-- ----------------------------
-- Table structure for ns_wxuser
-- ----------------------------
DROP TABLE IF EXISTS `ns_wxuser`;
CREATE TABLE `ns_wxuser` (
  `uid` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `openid` varchar(36) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'openid',
  `unionid` varchar(64) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `session_key` varchar(64) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '登录key',
  `mobile` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `nickname` varchar(64) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户昵称',
  `sex` tinyint(4) NOT NULL DEFAULT '0' COMMENT '性别:1=男,2=女',
  `language` varchar(16) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '语言',
  `country` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '国家',
  `province` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '省',
  `city` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '市',
  `headimgurl` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '头像',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '授权时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态:0=拉黑,1=正常',
  `ip` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ip',
  `post_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发帖数',
  `last_read_comment_time` int(10) NOT NULL DEFAULT '0' COMMENT '最后查看评论的时间',
  `delete_time` int(101) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`uid`) USING BTREE,
  UNIQUE KEY `openid` (`openid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='微信用户';

SET FOREIGN_KEY_CHECKS = 1;
