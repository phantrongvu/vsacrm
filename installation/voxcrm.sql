-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 28, 2013 at 07:45 AM
-- Server version: 5.1.44
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `voxcrm`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `eid` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `student_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `created` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`eid`),
  KEY `staff_id` (`student_id`,`product_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
CREATE TABLE IF NOT EXISTS `notes` (
  `nid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `sid` int(10) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `body` text NOT NULL,
  `created` int(10) unsigned NOT NULL,
  PRIMARY KEY (`nid`),
  KEY `uid` (`uid`,`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `pid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`pid`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `pid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `lessons` smallint(6) unsigned NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`pid`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `rid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`rid`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
CREATE TABLE IF NOT EXISTS `role_permission` (
  `rid` int(11) unsigned NOT NULL,
  `pid` int(11) unsigned NOT NULL,
  UNIQUE KEY `rid` (`rid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
CREATE TABLE IF NOT EXISTS `schedules` (
  `cid` int(10) unsigned NOT NULL auto_increment,
  `eid` int(10) unsigned NOT NULL,
  `staff_id` int(10) unsigned NOT NULL,
  `studio_id` int(10) unsigned NOT NULL,
  `sequence` smallint(10) unsigned NOT NULL,
  `start` int(10) unsigned NOT NULL,
  `end` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`cid`),
  KEY `eid` (`eid`),
  KEY `staff_id` (`staff_id`,`studio_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `sid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mail` varchar(128) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `street` varchar(128) NOT NULL,
  `additional` varchar(128) NOT NULL,
  `city` varchar(60) NOT NULL,
  `postcode` varchar(5) NOT NULL,
  `state` varchar(3) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `created` int(11) unsigned NOT NULL,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `studios`
--

DROP TABLE IF EXISTS `studios`;
CREATE TABLE IF NOT EXISTS `studios` (
  `sid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `street` varchar(128) NOT NULL,
  `additional` varchar(128) NOT NULL,
  `city` varchar(50) NOT NULL,
  `postcode` varchar(5) NOT NULL,
  `state` varchar(3) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `fax` varchar(20) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`sid`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mail` varchar(128) NOT NULL,
  `pass` varchar(128) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `street` varchar(128) NOT NULL,
  `additional` varchar(128) NOT NULL,
  `city` varchar(60) NOT NULL,
  `postcode` varchar(5) NOT NULL,
  `state` varchar(3) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `created` int(11) unsigned NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE IF NOT EXISTS `user_role` (
  `uid` int(11) unsigned NOT NULL,
  `rid` int(11) unsigned NOT NULL,
  UNIQUE KEY `uid` (`uid`,`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `mail`, `pass`, `first_name`, `last_name`, `dob`, `street`, `additional`, `city`, `postcode`, `state`, `phone`, `mobile`, `description`, `created`) VALUES
(1, 'admin@voxsingingacademy.com', 'c12b2857f00cc1075d70f3aad304f32142518d9e', 'Admin', 'Vox', '', '', '', '', '', '', '', '', '', 0);

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`rid`, `name`) VALUES
(1, 'admin'),
(2, 'teacher');

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`uid`, `rid`) VALUES
(1, 1);

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`pid`, `name`) VALUES
(5, 'manage permissions'),
(6, 'manage people'),
(7, 'manage studios'),
(8, 'manage products'),
(9, 'manage event'),
(10, 'view calendar'),
(11, 'manage notes');

--
-- Dumping data for table `role_permission`
--

INSERT INTO `role_permission` (`rid`, `pid`) VALUES
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(2, 10),
(2, 11);
