-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2018-03-14 09:02:54
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
-- 表的结构 `hr_letvprice`
--

CREATE TABLE IF NOT EXISTS `hr_letvprice` (
  `id` int(11) NOT NULL,
  `num` int(11) NOT NULL DEFAULT '1' COMMENT '1:100;2:200;3:500;4:1000;5:2000;6:5000;7:10000;8:20000',
  `code` int(11) NOT NULL DEFAULT '1' COMMENT '码率kbps:13,流畅版350k ;16,高清版800k;25:超清版1300k;99:1080p版2000k',
  `length` int(11) NOT NULL DEFAULT '1' COMMENT '1:40;2:1小时;3:1时30分;4:2小时;5:2时30分;6:3小时',
  `price` decimal(10,2) NOT NULL DEFAULT '5.00' COMMENT '价格',
  `type` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `hr_letvprice`
--

INSERT INTO `hr_letvprice` (`id`, `num`, `code`, `length`, `price`, `type`) VALUES
(1, 1, 13, 1, '15.00', 1),
(2, 2, 13, 1, '20.00', 1),
(3, 3, 13, 1, '35.00', 1),
(4, 4, 13, 1, '60.00', 1),
(5, 5, 13, 1, '120.00', 1),
(6, 6, 13, 1, '270.00', 1),
(7, 7, 13, 1, '550.00', 1),
(8, 8, 13, 1, '1100.00', 1),
(9, 1, 13, 2, '17.50', 1),
(10, 2, 13, 2, '25.00', 1),
(11, 3, 13, 2, '47.50', 1),
(12, 4, 13, 2, '85.00', 1),
(13, 5, 13, 2, '170.00', 1),
(14, 6, 13, 2, '405.00', 1),
(15, 7, 13, 2, '800.00', 1),
(16, 8, 13, 2, '1600.00', 1),
(17, 1, 13, 3, '21.25', 1),
(18, 2, 13, 3, '32.50', 1),
(19, 3, 13, 3, '66.25', 1),
(20, 4, 13, 3, '132.50', 1),
(21, 5, 13, 3, '245.00', 1),
(22, 6, 13, 3, '612.50', 1),
(23, 7, 13, 3, '1225.00', 1),
(24, 8, 13, 3, '2450.00', 1),
(25, 1, 13, 4, '25.00', 1),
(26, 2, 13, 4, '40.00', 1),
(27, 3, 13, 4, '85.00', 1),
(28, 4, 13, 4, '170.00', 1),
(29, 5, 13, 4, '330.00', 1),
(30, 6, 13, 4, '800.00', 1),
(31, 7, 13, 4, '1600.00', 1),
(32, 8, 13, 4, '3300.00', 1),
(33, 1, 13, 5, '28.75', 1),
(34, 2, 13, 5, '47.50', 1),
(35, 3, 13, 5, '103.75', 1),
(36, 4, 13, 5, '207.20', 1),
(37, 5, 13, 5, '405.00', 1),
(38, 6, 13, 5, '987.50', 1),
(39, 7, 13, 5, '1975.00', 1),
(40, 8, 13, 5, '4050.00', 1),
(41, 1, 13, 6, '32.50', 1),
(42, 2, 13, 6, '55.00', 1),
(43, 3, 13, 6, '132.50', 1),
(44, 4, 13, 6, '245.00', 1),
(45, 5, 13, 6, '480.00', 1),
(46, 6, 13, 6, '1225.00', 1),
(47, 7, 13, 6, '2450.00', 1),
(48, 8, 13, 6, '4800.00', 1),
(49, 1, 16, 1, '21.50', 1),
(50, 2, 16, 1, '33.00', 1),
(51, 3, 16, 1, '67.50', 1),
(52, 4, 16, 1, '135.00', 1),
(53, 5, 16, 1, '250.00', 1),
(54, 6, 16, 1, '625.00', 1),
(55, 7, 16, 1, '1250.00', 1),
(56, 8, 16, 1, '2500.00', 1),
(57, 1, 16, 2, '27.25', 1),
(58, 2, 16, 2, '44.50', 1),
(59, 3, 16, 2, '96.25', 1),
(60, 4, 16, 2, '192.50', 1),
(61, 5, 16, 2, '375.00', 1),
(62, 6, 16, 2, '912.50', 1),
(63, 7, 16, 2, '1825.00', 1),
(64, 8, 16, 2, '3750.00', 1),
(65, 1, 16, 3, '35.88', 1),
(66, 2, 16, 3, '61.75', 1),
(67, 3, 16, 3, '149.38', 1),
(68, 4, 16, 3, '278.80', 1),
(69, 5, 16, 3, '567.50', 1),
(70, 6, 16, 3, '1393.80', 1),
(71, 7, 16, 3, '2788.00', 1),
(72, 8, 16, 3, '5575.00', 1),
(73, 1, 16, 4, '44.50', 1),
(74, 2, 16, 4, '79.00', 1),
(75, 3, 16, 4, '192.50', 1),
(76, 4, 16, 4, '375.00', 1),
(77, 5, 16, 4, '740.00', 1),
(78, 6, 16, 4, '1825.00', 1),
(79, 7, 16, 4, '3750.00', 1),
(80, 8, 16, 4, '7300.00', 1),
(81, 1, 16, 5, '53.13', 1),
(82, 2, 16, 5, '96.25', 1),
(83, 3, 16, 5, '235.63', 1),
(84, 4, 16, 5, '481.30', 1),
(85, 5, 16, 5, '912.50', 1),
(86, 6, 16, 5, '2356.30', 1),
(87, 7, 16, 5, '4613.00', 1),
(88, 8, 16, 5, '9025.00', 1),
(89, 1, 16, 6, '61.75', 1),
(90, 2, 16, 6, '123.50', 1),
(91, 3, 16, 6, '278.75', 1),
(92, 4, 16, 6, '567.50', 1),
(93, 5, 16, 6, '1135.00', 1),
(94, 6, 16, 6, '2787.50', 1),
(95, 7, 16, 6, '5575.00', 1),
(96, 8, 16, 6, '10850.00', 1),
(97, 1, 25, 1, '28.60', 1),
(98, 2, 25, 1, '47.20', 1),
(99, 3, 25, 1, '103.00', 1),
(100, 4, 25, 1, '206.00', 1),
(101, 5, 25, 1, '402.00', 1),
(102, 6, 25, 1, '980.00', 1),
(103, 7, 25, 1, '1960.00', 1),
(104, 8, 25, 1, '4020.00', 1),
(105, 1, 25, 2, '37.90', 1),
(106, 2, 25, 2, '65.80', 1),
(107, 3, 25, 2, '159.50', 1),
(108, 4, 25, 2, '299.00', 1),
(109, 5, 25, 2, '608.00', 1),
(110, 6, 25, 2, '1495.00', 1),
(111, 7, 25, 2, '2990.00', 1),
(112, 8, 25, 2, '5980.00', 1),
(113, 1, 25, 3, '51.85', 1),
(114, 2, 25, 3, '93.70', 1),
(115, 3, 25, 3, '229.25', 1),
(116, 4, 25, 3, '448.50', 1),
(117, 5, 25, 3, '887.00', 1),
(118, 6, 25, 3, '2292.50', 1),
(119, 7, 25, 3, '4485.00', 1),
(120, 8, 25, 3, '8770.00', 1),
(121, 1, 25, 4, '65.80', 1),
(122, 2, 25, 4, '131.60', 1),
(123, 3, 25, 4, '309.00', 1),
(124, 4, 25, 4, '608.00', 1),
(125, 5, 25, 4, '1216.00', 1),
(126, 6, 25, 4, '2990.00', 1),
(127, 7, 25, 4, '5980.00', 1),
(128, 8, 25, 4, '11660.00', 1),
(129, 1, 25, 5, '79.75', 1),
(130, 2, 25, 5, '159.50', 1),
(131, 3, 25, 5, '378.75', 1),
(132, 4, 25, 5, '747.50', 1),
(133, 5, 25, 5, '1495.00', 1),
(134, 6, 25, 5, '3787.50', 1),
(135, 7, 25, 5, '7375.00', 1),
(136, 8, 25, 5, '14450.00', 1),
(137, 1, 25, 6, '93.70', 1),
(138, 2, 25, 6, '187.40', 1),
(139, 3, 25, 6, '448.50', 1),
(140, 4, 25, 6, '887.00', 1),
(141, 5, 25, 6, '1774.00', 1),
(142, 6, 25, 6, '4485.00', 1),
(143, 7, 25, 6, '8770.00', 1),
(144, 8, 25, 6, '17240.00', 1),
(145, 1, 99, 1, '38.80', 1),
(146, 2, 99, 1, '67.60', 1),
(147, 3, 99, 1, '164.00', 1),
(148, 4, 99, 1, '308.00', 1),
(149, 5, 99, 1, '626.00', 1),
(150, 6, 99, 1, '1540.00', 1),
(151, 7, 99, 1, '3080.00', 1),
(152, 8, 99, 1, '6160.00', 1),
(153, 1, 99, 2, '53.20', 1),
(154, 2, 99, 2, '96.40', 1),
(155, 3, 99, 2, '236.00', 1),
(156, 4, 99, 2, '462.00', 1),
(157, 5, 99, 2, '914.00', 1),
(158, 6, 99, 2, '2360.00', 1),
(159, 7, 99, 2, '4620.00', 1),
(160, 8, 99, 2, '9040.00', 1),
(161, 1, 99, 3, '74.80', 1),
(162, 2, 99, 3, '149.60', 1),
(163, 3, 99, 3, '354.00', 1),
(164, 4, 99, 3, '698.00', 1),
(165, 5, 99, 3, '1396.00', 1),
(166, 6, 99, 3, '3540.00', 1),
(167, 7, 99, 3, '6880.00', 1),
(168, 8, 99, 3, '13460.00', 1),
(169, 1, 99, 4, '96.40', 1),
(170, 2, 99, 4, '192.80', 1),
(171, 3, 99, 4, '462.00', 1),
(172, 4, 99, 4, '914.00', 1),
(173, 5, 99, 4, '1828.00', 1),
(174, 6, 99, 4, '4620.00', 1),
(175, 7, 99, 4, '9040.00', 1),
(176, 8, 99, 4, '17780.00', 1),
(177, 1, 99, 5, '128.00', 1),
(178, 2, 99, 5, '236.00', 1),
(179, 3, 99, 5, '590.00', 1),
(180, 4, 99, 5, '1180.00', 1),
(181, 5, 99, 5, '2360.00', 1),
(182, 6, 99, 5, '5800.00', 1),
(183, 7, 99, 5, '11300.00', 1),
(184, 8, 99, 5, '22100.00', 1),
(185, 1, 99, 6, '149.60', 1),
(186, 2, 99, 6, '279.20', 1),
(187, 3, 99, 6, '698.00', 1),
(188, 4, 99, 6, '1396.00', 1),
(189, 5, 99, 6, '2792.00', 1),
(190, 6, 99, 6, '6880.00', 1),
(191, 7, 99, 6, '13460.00', 1),
(192, 8, 99, 6, '26420.00', 1),
(193, 1, 1, 1, '5.00', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hr_letvprice`
--
ALTER TABLE `hr_letvprice`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hr_letvprice`
--
ALTER TABLE `hr_letvprice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=194;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
