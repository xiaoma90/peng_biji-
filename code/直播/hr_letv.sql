-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2018-03-14 09:03:35
-- 服务器版本： 5.6.36-log
-- PHP Version: 7.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `api_hongrunet`
--

-- --------------------------------------------------------

--
-- 表的结构 `hr_letv`
--

CREATE TABLE IF NOT EXISTS `hr_letv` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `cid` varchar(255) DEFAULT '大神直播' COMMENT '直播分类',
  `createtime` varchar(255) NOT NULL COMMENT '申请时间',
  `title` varchar(255) NOT NULL COMMENT '直播标题',
  `codeRateTypes` varchar(255) NOT NULL DEFAULT '13' COMMENT '13 流畅；16 高清；19 超清； 25   1080P；99 原画',
  `coverImgUrl` varchar(255) NOT NULL COMMENT '封面图片地址',
  `activityId` varchar(255) NOT NULL COMMENT '返回的活动ID',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '0:未创建1:直播中,2:已结束',
  `num` int(11) NOT NULL DEFAULT '1' COMMENT '直播预估人数1:100,2:200,3:500,4:1000,5:2000,6:5000,7:10000,8:20000',
  `timelength` int(11) NOT NULL DEFAULT '1' COMMENT '时长(letvprice表中的length)',
  `start` varchar(255) NOT NULL COMMENT '开始时间',
  `endTime` varchar(255) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '1' COMMENT '1:讲师付费；2:x学员付费',
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '学员付费金额',
  `pushUrl` varchar(255) NOT NULL DEFAULT '' COMMENT '推流地址',
  `videoUrl` varchar(255) NOT NULL DEFAULT '0' COMMENT '播放页地址',
  `ustatus` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '0:未结束1：已结束'
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `hr_letv`
--

INSERT INTO `hr_letv` (`id`, `uid`, `cid`, `createtime`, `title`, `codeRateTypes`, `coverImgUrl`, `activityId`, `status`, `num`, `timelength`, `start`, `endTime`, `type`, `fee`, `pushUrl`, `videoUrl`, `ustatus`) VALUES
(1, 261, '大神直播', '1503973886', '鸿儒商学讲堂，教程直播', '13', 'http://hr2.hongrunet.com/headimg/201708291030381729000000172732713.jpg', 'A20170829000001v', 0, 1, 2, '1503988200', '1503991800', 1, '0.00', 'rtmp://w.gslb.lecloud.com/live/2017082930000001x99', '0', 0),
(2, 261, '大神直播', '1503999107', '啊', '13', 'http://hr2.hongrunet.com/headimg/201708291731411729000000153141441.jpg', '', 0, 1, 2, '1503999060', '0', 2, '0.18', '', '0', 0),
(3, 1925, '大神直播', '1504144586', '啊啊啊', '13', 'http://hr2.hongrunet.com/headimg/201708310956101731000000441426485.jpg', '', 0, 1, 1, '', '2400', 1, '0.00', '', '0', 0),
(4, 261, '大神直播', '1504857579', '全民视商到来了', '13', 'http://hr2.hongrunet.com/headimg/201709081559011780000004844432342.png', 'A20170908000006j', 0, 1, 3, '1504938600', '1504944000', 1, '0.00', 'rtmp://w.gslb.lecloud.com/live/2017090830000006l99', '0', 0),
(5, 68, '大神直播', '1512631071', '承德老酒讲解', '13', 'http://hr2.hongrunet.com/headimg/201712071517331770000001019131714.jpg', '', 0, 1, 1, '', '2400', 1, '0.00', '', '0', 0),
(6, 3925, '大神直播', '1516445939', '承德老酒余瑞', '13', 'http://hr2.hongrunet.com/headimg/201801201858421820000000242224209.png', '', 0, 1, 1, '', '0', 1, '0.00', '', '0', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hr_letv`
--
ALTER TABLE `hr_letv`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hr_letv`
--
ALTER TABLE `hr_letv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
