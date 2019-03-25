/*
 Navicat Premium Data Transfer

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 50710
 Source Host           : localhost:3306
 Source Schema         : yongkun_shop

 Target Server Type    : MySQL
 Target Server Version : 50710
 File Encoding         : 65001

 Date: 25/03/2019 09:39:54
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ns_chat
-- ----------------------------
DROP TABLE IF EXISTS `ns_chat`;
CREATE TABLE `ns_chat`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `another_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '聊天主表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ns_chat_info
-- ----------------------------
DROP TABLE IF EXISTS `ns_chat_info`;
CREATE TABLE `ns_chat_info`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `chat_id` int(11) NOT NULL COMMENT '聊天室id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '内容',
  `type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'text img',
  `is_latest` int(2) NOT NULL DEFAULT 1 COMMENT '1 是否是最后一条 ，2',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '聊天详情' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ns_chat_list
-- ----------------------------
DROP TABLE IF EXISTS `ns_chat_list`;
CREATE TABLE `ns_chat_list`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `chat_id` int(11) NOT NULL COMMENT '聊天主表id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `another_id` int(11) NOT NULL COMMENT '对方id',
  `is_online` tinyint(2) NOT NULL DEFAULT 1 COMMENT '是否在线1,2',
  `unread` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '未读条数',
  `status` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1正常 2删除',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '聊天列表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
