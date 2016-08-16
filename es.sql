-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016 年 08 月 16 日 18:28
-- 服务器版本: 5.5.40
-- PHP 版本: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `es`
--

-- --------------------------------------------------------

--
-- 表的结构 `es_admin`
--

CREATE TABLE IF NOT EXISTS `es_admin` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `username` varchar(20) DEFAULT NULL COMMENT '昵称',
  `email` varchar(50) DEFAULT NULL COMMENT '邮箱',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `mobile` varchar(11) DEFAULT NULL,
  `regtime` int(10) DEFAULT NULL,
  `logintime` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- 转存表中的数据 `es_admin`
--

INSERT INTO `es_admin` (`id`, `username`, `email`, `password`, `mobile`, `regtime`, `logintime`) VALUES
(1, 'admin', 'ceshi@qq.com', 'df0b9c7ec013e6ae79e52df274e8b610', '13103855928', NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `es_seven_list`
--

CREATE TABLE IF NOT EXISTS `es_seven_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `openid` varchar(50) NOT NULL,
  `addtime` int(10) NOT NULL,
  `src` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- 转存表中的数据 `es_seven_list`
--

INSERT INTO `es_seven_list` (`id`, `openid`, `addtime`, `src`) VALUES
(8, 'test', 1471363200, NULL),
(7, 'test', 1471276800, NULL),
(9, 'test', 1471449600, NULL),
(10, 'testjsdoiqwornqwnd', 1471276800, NULL),
(11, 'mf34u9834534frnewr', 1471276800, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `es_seven_user`
--

CREATE TABLE IF NOT EXISTS `es_seven_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `openid` varchar(50) NOT NULL,
  `addtime` int(10) NOT NULL,
  `total` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `es_seven_user`
--

INSERT INTO `es_seven_user` (`id`, `openid`, `addtime`, `total`) VALUES
(1, 'test', 1471449600, 3),
(2, 'testjsdoiqwornqwnd', 1471276800, 1),
(3, 'mf34u9834534frnewr', 1471276800, 1);

-- --------------------------------------------------------

--
-- 表的结构 `es_system`
--

CREATE TABLE IF NOT EXISTS `es_system` (
  `id` int(1) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `web` varchar(1000) DEFAULT NULL COMMENT '网站标题',
  `msg` varchar(200) DEFAULT NULL COMMENT '网站关闭提示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `es_system`
--

INSERT INTO `es_system` (`id`, `web`, `msg`) VALUES
(1, '{"title":"demo","description":"\\u63cf\\u8ff0","keywords":"\\u5173\\u952e\\u8bcd","copyright":"\\u7248\\u6743","icp":"111111"}', '网站关闭中...');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
