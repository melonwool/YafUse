-- phpMyAdmin SQL Dump
-- version 3.3.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2013 年 12 月 18 日 20:03
-- 服务器版本: 5.1.38
-- PHP 版本: 5.5.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `yaf_demo`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `nickname` varchar(32) NOT NULL,
  `realname` varchar(16) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_del` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='管理员表' AUTO_INCREMENT=8 ;

--
-- 转存表中的数据 `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nickname`, `realname`, `email`, `is_del`) VALUES
(1, 'melon', '25d55ad283aa400af464c76d713c07ad', 'melon', 'melon', 'malong.chn@gmail.com', '0'),
(3, 'admin', '25d55ad283aa400af464c76d713c07ad', 'admin', 'admin', 'admin@gmail.com', '0'),
(7, 'melons', 'b45746b95e3ca1a2486ad63222c37c4b', 'melons', 'melons', 'melons@gmail.com', '0');