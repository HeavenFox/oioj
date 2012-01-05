-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 05, 2012 at 01:22 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


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

-- --------------------------------------------------------

--
-- Table structure for table `oj_contests`
--

CREATE TABLE IF NOT EXISTS `oj_contests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `begin_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `type` smallint(6) NOT NULL,
  `publicity` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `reg_begin` int(11) DEFAULT NULL,
  `reg_deadline` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `oj_contests`
--

INSERT INTO `oj_contests` (`id`, `uid`, `title`, `description`, `begin_time`, `end_time`, `duration`, `type`, `publicity`, `status`, `reg_begin`, `reg_deadline`) VALUES
(1, 1, 'Joined', 'Joined contest', 21, 1333099372, 3600, 1, 2, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `oj_contest_options`
--

CREATE TABLE IF NOT EXISTS `oj_contest_options` (
  `cid` int(11) NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` varchar(128) NOT NULL,
  KEY `cid` (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oj_contest_options`
--

INSERT INTO `oj_contest_options` (`cid`, `key`, `value`) VALUES
(1, 'ranking_criteria', 'dtotal_score;atotal_time'),
(1, 'ranking_display_params', 'total_score;total_time');

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

INSERT INTO `oj_contest_problems` (`cid`, `pid`) VALUES
(1, 1),
(1, 2),
(1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `oj_contest_register`
--

CREATE TABLE IF NOT EXISTS `oj_contest_register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `started` int(11) DEFAULT NULL,
  `finished` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `oj_contest_register`
--

INSERT INTO `oj_contest_register` (`id`, `cid`, `uid`, `started`, `finished`) VALUES
(1, 1, 1, 1323263426, NULL),
(2, 1, 2, 1323238985, 1323239975),
(3, 1, 3, 1323238945, 1323239985);

-- --------------------------------------------------------

--
-- Table structure for table `oj_contest_submissions`
--

CREATE TABLE IF NOT EXISTS `oj_contest_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `rid` int(11) DEFAULT NULL,
  `code` text NOT NULL,
  `lang` varchar(8) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`),
  KEY `rid` (`rid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `oj_contest_submissions`
--

INSERT INTO `oj_contest_submissions` (`id`, `cid`, `uid`, `pid`, `rid`, `code`, `lang`, `timestamp`) VALUES
(2, 1, 1, 1, 1, 'inc', 'cpp', 1323266448),
(3, 1, 2, 2, 2, 'inc', 'cpp', 1323266438),
(4, 1, 1, 2, 3, 'inc', 'cpp', 1323266428),
(5, 1, 1, 3, 4, 'inc', 'cpp', 1323266426),
(6, 1, 3, 3, 5, 'inc', 'cpp', 1323266418),
(7, 1, 1, 1, NULL, 'wrong code', 'cpp', 1000000);

-- --------------------------------------------------------

--
-- Table structure for table `oj_datadist_queue`
--

CREATE TABLE IF NOT EXISTS `oj_datadist_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(127) NOT NULL,
  `server` int(11) NOT NULL,
  `list_pid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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

-- --------------------------------------------------------

--
-- Table structure for table `oj_invitations`
--

CREATE TABLE IF NOT EXISTS `oj_invitations` (
  `id` int(11) NOT NULL,
  `code` char(32) NOT NULL,
  `sender` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `ftp_username` varchar(32) NOT NULL,
  `ftp_password` varchar(64) NOT NULL,
  `online` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `oj_judgeservers`
--

INSERT INTO `oj_judgeservers` (`id`, `name`, `ip`, `port`, `workload`, `maxWorkload`, `ftp_username`, `ftp_password`, `online`) VALUES
(1, 'local', '127.0.0.1', 9458, 0, 20, '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `oj_problems`
--

CREATE TABLE IF NOT EXISTS `oj_problems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `source` tinytext,
  `solution` text,
  `type` smallint(6) NOT NULL,
  `input` varchar(64) NOT NULL,
  `output` varchar(64) NOT NULL,
  `compare` varchar(25) NOT NULL,
  `submission` int(11) NOT NULL DEFAULT '0',
  `accepted` int(11) NOT NULL DEFAULT '0',
  `listing` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `oj_problems`
--

INSERT INTO `oj_problems` (`id`, `title`, `body`, `uid`, `source`, `solution`, `type`, `input`, `output`, `compare`, `submission`, `accepted`, `listing`) VALUES
(1, 'Hello', 'Hello', 1, NULL, NULL, 1, 'a.in', 'a.out', '/FULLTEXT/', 0, 0, 0),
(2, 'Hello 2', 'Hello 2', 1, NULL, NULL, 1, 'b.in', 'b.out', '/FULLTEXT/', 0, 0, 0),
(3, 'Hello 3', 'hello 3', 1, NULL, NULL, 1, 'c.in', 'c.out', '/FULLTEXT/', 0, 0, 0),
(4, 'Test', 'Test', 1, NULL, NULL, 1, 'ab.in', 'ab.out', '/FULLTEXT/', 0, 0, 1);

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
  `score` int(11) DEFAULT NULL,
  `code` text NOT NULL,
  `cases` varchar(1024) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `oj_records`
--

INSERT INTO `oj_records` (`id`, `pid`, `status`, `server`, `lang`, `uid`, `score`, `code`, `cases`, `timestamp`) VALUES
(1, 1, 2, 1, 'cpp', 1, 100, 'inc', 'a:3:{i:0;a:6:{s:6:"CaseID";i:1;s:10:"CaseResult";i:7;s:16:"CaseExtendedCode";i:0;s:9:"CaseScore";i:10;s:8:"CaseTime";d:0.5;s:10:"CaseMemory";d:12.699999999999999289457264239899814128875732421875;}i:1;a:6:{s:6:"CaseID";i:2;s:10:"CaseResult";i:7;s:16:"CaseExtendedCode";i:0;s:9:"CaseScore";i:10;s:8:"CaseTime";d:0.5;s:10:"CaseMemory";d:12.699999999999999289457264239899814128875732421875;}i:2;a:6:{s:6:"CaseID";i:3;s:10:"CaseResult";i:6;s:16:"CaseExtendedCode";i:0;s:9:"CaseScore";i:0;s:8:"CaseTime";d:0.5;s:10:"CaseMemory";d:12.699999999999999289457264239899814128875732421875;}}', 1323266458),
(2, 2, 2, 1, 'cpp', 2, 100, 'inc', 'a:3:{i:0;a:6:{s:6:"CaseID";i:1;s:10:"CaseResult";i:7;s:16:"CaseExtendedCode";i:0;s:9:"CaseScore";i:10;s:8:"CaseTime";d:0.5;s:10:"CaseMemory";d:12.699999999999999289457264239899814128875732421875;}i:1;a:6:{s:6:"CaseID";i:2;s:10:"CaseResult";i:7;s:16:"CaseExtendedCode";i:0;s:9:"CaseScore";i:10;s:8:"CaseTime";d:0.5;s:10:"CaseMemory";d:12.699999999999999289457264239899814128875732421875;}i:2;a:6:{s:6:"CaseID";i:3;s:10:"CaseResult";i:6;s:16:"CaseExtendedCode";i:0;s:9:"CaseScore";i:0;s:8:"CaseTime";d:0.5;s:10:"CaseMemory";d:12.699999999999999289457264239899814128875732421875;}}', 1323266458),
(3, 2, 4, 1, 'cpp', 1, 60, 'inc', 'a:3:{i:0;a:6:{s:6:"CaseID";i:1;s:10:"CaseResult";i:7;s:16:"CaseExtendedCode";i:0;s:9:"CaseScore";i:10;s:8:"CaseTime";d:0.5;s:10:"CaseMemory";d:12.699999999999999289457264239899814128875732421875;}i:1;a:6:{s:6:"CaseID";i:2;s:10:"CaseResult";i:7;s:16:"CaseExtendedCode";i:0;s:9:"CaseScore";i:10;s:8:"CaseTime";d:0.5;s:10:"CaseMemory";d:12.699999999999999289457264239899814128875732421875;}i:2;a:6:{s:6:"CaseID";i:3;s:10:"CaseResult";i:6;s:16:"CaseExtendedCode";i:0;s:9:"CaseScore";i:0;s:8:"CaseTime";d:0.5;s:10:"CaseMemory";d:12.699999999999999289457264239899814128875732421875;}}', 1323266458),
(4, 3, 4, 1, 'cpp', 1, 10, 'inc', 'a:3:{i:0;a:6:{s:6:"CaseID";i:1;s:10:"CaseResult";i:7;s:16:"CaseExtendedCode";i:0;s:9:"CaseScore";i:10;s:8:"CaseTime";d:0.5;s:10:"CaseMemory";d:12.699999999999999289457264239899814128875732421875;}i:1;a:6:{s:6:"CaseID";i:2;s:10:"CaseResult";i:7;s:16:"CaseExtendedCode";i:0;s:9:"CaseScore";i:10;s:8:"CaseTime";d:0.5;s:10:"CaseMemory";d:12.699999999999999289457264239899814128875732421875;}i:2;a:6:{s:6:"CaseID";i:3;s:10:"CaseResult";i:6;s:16:"CaseExtendedCode";i:0;s:9:"CaseScore";i:0;s:8:"CaseTime";d:0.5;s:10:"CaseMemory";d:12.699999999999999289457264239899814128875732421875;}}', 1323266458),
(5, 3, 4, 1, 'cpp', 3, 20, 'inc', 'a:3:{i:0;a:6:{s:6:"CaseID";i:1;s:10:"CaseResult";i:7;s:16:"CaseExtendedCode";i:0;s:9:"CaseScore";i:10;s:8:"CaseTime";d:0.5;s:10:"CaseMemory";d:12.699999999999999289457264239899814128875732421875;}i:1;a:6:{s:6:"CaseID";i:2;s:10:"CaseResult";i:7;s:16:"CaseExtendedCode";i:0;s:9:"CaseScore";i:10;s:8:"CaseTime";d:0.5;s:10:"CaseMemory";d:12.699999999999999289457264239899814128875732421875;}i:2;a:6:{s:6:"CaseID";i:3;s:10:"CaseResult";i:6;s:16:"CaseExtendedCode";i:0;s:9:"CaseScore";i:0;s:8:"CaseTime";d:0.5;s:10:"CaseMemory";d:12.699999999999999289457264239899814128875732421875;}}', 1323266458),
(30, 4, 0, 0, 'cpp', 1, NULL, '#include', '', 1325769455),
(31, 4, 0, 0, 'cpp', 1, NULL, '#include', '', 1325769568);

-- --------------------------------------------------------

--
-- Table structure for table `oj_resources`
--

CREATE TABLE IF NOT EXISTS `oj_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `filename` varchar(128) NOT NULL,
  `url` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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

-- --------------------------------------------------------

--
-- Table structure for table `oj_settings`
--

CREATE TABLE IF NOT EXISTS `oj_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key` (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `oj_settings`
--

INSERT INTO `oj_settings` (`id`, `key`, `value`) VALUES
(1, 'local_judgeserver_data_dir', 'D:\\oiojtemp'),
(2, 'tmp_dir', 'D:\\oiojtemp'),
(3, 'token', 'nVM)[6Zm@5wBU@My>uQ(tU76Z=6:.d}Mx>8cZ44K!Wyd<Hu*aSn{3~vg,~pM>tmf'),
(4, 'backup_tmp', ''),
(5, 'recaptcha_public', '6Lf85MgSAAAAAJ6wTy4saHVye28O19cvTBw1eRzE'),
(6, 'recaptcha_private', '6Lf85MgSAAAAALSrX3MkcTibjmv8vOMDTtjxLvWK');

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
  KEY `tid` (`tid`),
  KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oj_tags_acl`
--

INSERT INTO `oj_tags_acl` (`tid`, `key`, `permission`) VALUES
(1, 'submit_solution', 1),
(2, 'admin_cp', 1),
(3, 'add_problem', 1),
(2, 'add_problem', 1);

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
  `score` int(11) NOT NULL,
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `oj_users`
--

CREATE TABLE IF NOT EXISTS `oj_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` char(40) NOT NULL,
  `salt` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `oj_users`
--

INSERT INTO `oj_users` (`id`, `username`, `email`, `password`, `salt`) VALUES
(1, 'HeavenFox', 'heavenfox@heavenfox.org', 'ac259b3949a3046aa731da51413775fd5f44fb4b', '78e3b5ca79e1d3688b73c13540b5c261'),
(2, 'Nora', 'nora@example.com', 'fjsklafjalsfj', 'fjdakslfjlasf'),
(3, 'Van', 'van@example.com', 'fjaskfjaslssl', 'fjierwoedd;lja');

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
(1, 'omnipotent', 10);

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
-- Constraints for table `oj_contest_options`
--
ALTER TABLE `oj_contest_options`
  ADD CONSTRAINT `oj_contest_options_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `oj_contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oj_contest_problems`
--
ALTER TABLE `oj_contest_problems`
  ADD CONSTRAINT `oj_contest_problems_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `oj_contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_contest_problems_ibfk_2` FOREIGN KEY (`pid`) REFERENCES `oj_problems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oj_contest_register`
--
ALTER TABLE `oj_contest_register`
  ADD CONSTRAINT `oj_contest_register_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `oj_contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_contest_register_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `oj_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oj_contest_submissions`
--
ALTER TABLE `oj_contest_submissions`
  ADD CONSTRAINT `oj_contest_submissions_ibfk_1` FOREIGN KEY (`rid`) REFERENCES `oj_records` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_contest_submissions_ibfk_2` FOREIGN KEY (`cid`) REFERENCES `oj_contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oj_dependencies`
--
ALTER TABLE `oj_dependencies`
  ADD CONSTRAINT `oj_dependencies_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `oj_problems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oj_problems`
--
ALTER TABLE `oj_problems`
  ADD CONSTRAINT `oj_problems_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `oj_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `oj_problems_tags`
--
ALTER TABLE `oj_problems_tags`
  ADD CONSTRAINT `oj_problems_tags_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `oj_problems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_problems_tags_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `oj_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `oj_resources`
--
ALTER TABLE `oj_resources`
  ADD CONSTRAINT `oj_resources_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `oj_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `oj_resources_tags`
--
ALTER TABLE `oj_resources_tags`
  ADD CONSTRAINT `oj_resources_tags_ibfk_1` FOREIGN KEY (`rid`) REFERENCES `oj_resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_resources_tags_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `oj_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
