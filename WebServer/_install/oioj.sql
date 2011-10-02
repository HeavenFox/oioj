-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 02, 2011 at 12:16 PM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `oioj`
--

-- --------------------------------------------------------

--
-- Table structure for table `oj_articles`
--

CREATE TABLE IF NOT EXISTS `oj_articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `body` mediumtext NOT NULL,
  `attachments` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oj_articles`
--


-- --------------------------------------------------------

--
-- Table structure for table `oj_articles_tags`
--

CREATE TABLE IF NOT EXISTS `oj_articles_tags` (
  `aid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  KEY `aid` (`aid`),
  KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oj_articles_tags`
--


-- --------------------------------------------------------

--
-- Table structure for table `oj_contests`
--

CREATE TABLE IF NOT EXISTS `oj_contests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `startTime` int(11) NOT NULL,
  `endTime` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `type` smallint(6) NOT NULL,
  `publicity` tinyint(4) NOT NULL,
  `regDeadline` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oj_contests`
--


-- --------------------------------------------------------

--
-- Table structure for table `oj_contest_problems`
--

CREATE TABLE IF NOT EXISTS `oj_contest_problems` (
  `cid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  KEY `cid` (`cid`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oj_contest_problems`
--


-- --------------------------------------------------------

--
-- Table structure for table `oj_dependencies`
--

CREATE TABLE IF NOT EXISTS `oj_dependencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `filename` varchar(128) NOT NULL,
  `type` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oj_dependencies`
--


-- --------------------------------------------------------

--
-- Table structure for table `oj_judgeservers`
--

CREATE TABLE IF NOT EXISTS `oj_judgeservers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `port` int(11) NOT NULL,
  `workload` int(11) NOT NULL DEFAULT '0',
  `maxWorkload` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `oj_judgeservers`
--

INSERT INTO `oj_judgeservers` (`id`, `name`, `ip`, `port`, `workload`, `maxWorkload`) VALUES
(1, 'VBox', '192.168.1.104', 9458, 18, 1500);

-- --------------------------------------------------------

--
-- Table structure for table `oj_participants`
--

CREATE TABLE IF NOT EXISTS `oj_participants` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `beginTime` int(11) NOT NULL,
  PRIMARY KEY (`cid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oj_participants`
--


-- --------------------------------------------------------

--
-- Table structure for table `oj_problems`
--

CREATE TABLE IF NOT EXISTS `oj_problems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `uid` int(11) NOT NULL,
  `type` smallint(6) NOT NULL,
  `input` varchar(64) NOT NULL,
  `output` varchar(64) NOT NULL,
  `compare` varchar(25) NOT NULL,
  `submission` int(11) NOT NULL,
  `accepted` int(11) NOT NULL,
  `listing` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `oj_problems`
--

INSERT INTO `oj_problems` (`id`, `title`, `body`, `uid`, `type`, `input`, `output`, `compare`, `submission`, `accepted`, `listing`) VALUES
(1, 'A+B Problem', '<h3>Description</h3>\r\nWrite a problem that outputs A+B\r\n\r\n<h3>Input</h3>\r\n<p>input file: ab.in</p>\r\n<p>first line consists of two integers, a and b.</p>\r\n<h3>Output</h3>\r\n<p>outputfile: ab.out</p>\r\n<p>one line, a single integer: the sum</p>', 1, 1, 'ab.in', 'ab.out', '/FULLTEXT/', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `oj_problems_tags`
--

CREATE TABLE IF NOT EXISTS `oj_problems_tags` (
  `pid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  KEY `pid` (`pid`),
  KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oj_problems_tags`
--


-- --------------------------------------------------------

--
-- Table structure for table `oj_records`
--

CREATE TABLE IF NOT EXISTS `oj_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `status` smallint(6) NOT NULL,
  `server` int(11) NOT NULL,
  `lang` varchar(6) NOT NULL,
  `uid` int(11) NOT NULL,
  `code` text NOT NULL,
  `cases` varchar(1024) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oj_records`
--


-- --------------------------------------------------------

--
-- Table structure for table `oj_resources`
--

CREATE TABLE IF NOT EXISTS `oj_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `filename` varchar(128) NOT NULL,
  `url` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oj_resources`
--


-- --------------------------------------------------------

--
-- Table structure for table `oj_resources_tags`
--

CREATE TABLE IF NOT EXISTS `oj_resources_tags` (
  `rid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  KEY `rid` (`rid`),
  KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oj_resources_tags`
--


-- --------------------------------------------------------

--
-- Table structure for table `oj_tags`
--

CREATE TABLE IF NOT EXISTS `oj_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `oj_tags`
--

INSERT INTO `oj_tags` (`id`, `tag`) VALUES
(1, 'Member'),
(2, 'Admin'),
(3, 'Instructor');

-- --------------------------------------------------------

--
-- Table structure for table `oj_tags_acl`
--

CREATE TABLE IF NOT EXISTS `oj_tags_acl` (
  `tid` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `permission` tinyint(4) NOT NULL,
  KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oj_tags_acl`
--

INSERT INTO `oj_tags_acl` (`tid`, `key`, `permission`) VALUES
(1, 'submit_solution', 1),
(2, 'admin_cp', 1);

-- --------------------------------------------------------

--
-- Table structure for table `oj_testcases`
--

CREATE TABLE IF NOT EXISTS `oj_testcases` (
  `pid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `input` varchar(64) NOT NULL,
  `answer` varchar(64) NOT NULL,
  `timelimit` float NOT NULL,
  `memorylimit` int(11) NOT NULL,
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oj_testcases`
--

INSERT INTO `oj_testcases` (`pid`, `cid`, `input`, `answer`, `timelimit`, `memorylimit`) VALUES
(1, 1, 'ab1.in', 'ab1.out', 1, 128),
(1, 1, 'ab2.in', 'ab2.out', 1, 128);

-- --------------------------------------------------------

--
-- Table structure for table `oj_users`
--

CREATE TABLE IF NOT EXISTS `oj_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` char(20) NOT NULL,
  `salt` char(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `oj_users`
--

INSERT INTO `oj_users` (`id`, `name`, `email`, `password`, `salt`) VALUES
(1, 'HeavenFox', 'heavenfox@heavenfox.org', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `oj_users_acl`
--

CREATE TABLE IF NOT EXISTS `oj_users_acl` (
  `uid` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `permission` tinyint(4) NOT NULL,
  KEY `uid` (`uid`),
  KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oj_users_acl`
--

INSERT INTO `oj_users_acl` (`uid`, `key`, `permission`) VALUES
(1, 'admin_cp', 1);

-- --------------------------------------------------------

--
-- Table structure for table `oj_users_tags`
--

CREATE TABLE IF NOT EXISTS `oj_users_tags` (
  `uid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  KEY `uid` (`uid`),
  KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oj_users_tags`
--

INSERT INTO `oj_users_tags` (`uid`, `tid`) VALUES
(1, 1),
(1, 2);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `oj_articles_tags`
--
ALTER TABLE `oj_articles_tags`
  ADD CONSTRAINT `oj_articles_tags_ibfk_1` FOREIGN KEY (`aid`) REFERENCES `oj_articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_articles_tags_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `oj_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oj_contest_problems`
--
ALTER TABLE `oj_contest_problems`
  ADD CONSTRAINT `oj_contest_problems_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `oj_contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_contest_problems_ibfk_2` FOREIGN KEY (`pid`) REFERENCES `oj_problems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oj_dependencies`
--
ALTER TABLE `oj_dependencies`
  ADD CONSTRAINT `oj_dependencies_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `oj_problems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oj_participants`
--
ALTER TABLE `oj_participants`
  ADD CONSTRAINT `oj_participants_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `oj_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_participants_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `oj_contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oj_problems`
--
ALTER TABLE `oj_problems`
  ADD CONSTRAINT `oj_problems_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `oj_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oj_problems_tags`
--
ALTER TABLE `oj_problems_tags`
  ADD CONSTRAINT `oj_problems_tags_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `oj_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_problems_tags_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `oj_problems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oj_resources_tags`
--
ALTER TABLE `oj_resources_tags`
  ADD CONSTRAINT `oj_resources_tags_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `oj_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_resources_tags_ibfk_1` FOREIGN KEY (`rid`) REFERENCES `oj_resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oj_tags_acl`
--
ALTER TABLE `oj_tags_acl`
  ADD CONSTRAINT `oj_tags_acl_ibfk_1` FOREIGN KEY (`tid`) REFERENCES `oj_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oj_testcases`
--
ALTER TABLE `oj_testcases`
  ADD CONSTRAINT `oj_testcases_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `oj_problems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oj_users_acl`
--
ALTER TABLE `oj_users_acl`
  ADD CONSTRAINT `oj_users_acl_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `oj_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `oj_users_tags`
--
ALTER TABLE `oj_users_tags`
  ADD CONSTRAINT `oj_users_tags_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `oj_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_users_tags_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `oj_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
