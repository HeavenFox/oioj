SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE `oj_articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `body` mediumtext NOT NULL,
  `attachments` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `oj_articles_tags` (
  `aid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  KEY `aid` (`aid`),
  KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `oj_contests` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `oj_contest_options` (
  `cid` int(11) NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` varchar(128) NOT NULL,
  KEY `cid` (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `oj_contest_problems` (
  `cid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  KEY `cid` (`cid`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `oj_contest_register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `started` int(11) DEFAULT NULL,
  `finished` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `oj_contest_submissions` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `oj_cronjobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class` varchar(32) NOT NULL,
  `method` varchar(32) NOT NULL,
  `arguments` text NOT NULL,
  `reference` int(11) DEFAULT NULL,
  `next` int(11) NOT NULL,
  `qos` tinyint(4) NOT NULL,
  `enabled` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `oj_cronjobs_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class` varchar(32) NOT NULL,
  `content` text NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `oj_dependencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `filename` varchar(128) NOT NULL,
  `type` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `oj_invitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(32) NOT NULL,
  `sender` int(11) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `oj_judgeservers` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `oj_probdist_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(127) NOT NULL,
  `server` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `server` (`server`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `oj_problems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `source` tinytext,
  `solution` text,
  `type` tinyint(4) NOT NULL,
  `input` varchar(64) NOT NULL,
  `output` varchar(64) NOT NULL,
  `compare` varchar(25) NOT NULL,
  `submission` int(11) NOT NULL DEFAULT '0',
  `accepted` int(11) NOT NULL DEFAULT '0',
  `listing` tinyint(4) NOT NULL DEFAULT '1',
  `dispatched` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `oj_problems_tags` (
  `pid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  KEY `pid` (`pid`),
  KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `oj_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `server` int(11) DEFAULT NULL,
  `lang` varchar(6) NOT NULL,
  `uid` int(11) NOT NULL,
  `score` int(11) DEFAULT NULL,
  `code` text NOT NULL,
  `cases` text,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `oj_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `filename` varchar(128) NOT NULL,
  `url` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `oj_resources_tags` (
  `rid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  KEY `rid` (`rid`),
  KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `oj_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key` (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `oj_settings` VALUES(1, 'local_judgeserver_data_dir', 'D:\\oiojtemp');
INSERT INTO `oj_settings` VALUES(2, 'tmp_dir', '/Users/zhujingsi/Documents/oiojtemp/');
INSERT INTO `oj_settings` VALUES(3, 'token', 'nVM)[6Zm@5wBU@My>uQ(tU76Z=6:.d}Mx>8cZ44K!Wyd<Hu*aSn{3~vg,~pM>tmf');
INSERT INTO `oj_settings` VALUES(4, 'backup_token', '');
INSERT INTO `oj_settings` VALUES(5, 'recaptcha_public', '6Lf85MgSAAAAAJ6wTy4saHVye28O19cvTBw1eRzE');
INSERT INTO `oj_settings` VALUES(6, 'recaptcha_private', '6Lf85MgSAAAAALSrX3MkcTibjmv8vOMDTtjxLvWK');
INSERT INTO `oj_settings` VALUES(7, 'default_timezone', 'Asia/Shanghai');

CREATE TABLE `oj_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `oj_tags` VALUES(1, 'Member');
INSERT INTO `oj_tags` VALUES(2, 'Admin');
INSERT INTO `oj_tags` VALUES(3, 'Instructor');

CREATE TABLE `oj_tag_acl` (
  `tid` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `permission` tinyint(4) NOT NULL,
  KEY `tid` (`tid`),
  KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `oj_tag_acl` VALUES(1, 'submit_solution', 1);
INSERT INTO `oj_tag_acl` VALUES(2, 'admin_cp', 1);
INSERT INTO `oj_tag_acl` VALUES(3, 'add_problem', 1);
INSERT INTO `oj_tag_acl` VALUES(2, 'add_problem', 1);

CREATE TABLE `oj_testcases` (
  `pid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `input` varchar(64) NOT NULL,
  `answer` varchar(64) NOT NULL,
  `timelimit` float NOT NULL,
  `memorylimit` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `oj_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` char(40) NOT NULL,
  `salt` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `oj_users` VALUES(1, 'HeavenFox', 'heavenfox@heavenfox.org', 'cc14565dd5e9a62682133a2f5d33f1ec2c514f05', '1ff8857242d409176f32252014370d8c');

CREATE TABLE `oj_user_acl` (
  `uid` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `permission` tinyint(4) NOT NULL,
  KEY `uid` (`uid`),
  KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `oj_user_acl` VALUES(1, 'omnipotent', 10);

CREATE TABLE `oj_user_tags` (
  `uid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  KEY `uid` (`uid`),
  KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



ALTER TABLE `oj_articles_tags`
  ADD CONSTRAINT `oj_articles_tags_ibfk_1` FOREIGN KEY (`aid`) REFERENCES `oj_articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_articles_tags_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `oj_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `oj_contest_options`
  ADD CONSTRAINT `oj_contest_options_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `oj_contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `oj_contest_problems`
  ADD CONSTRAINT `oj_contest_problems_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `oj_contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_contest_problems_ibfk_2` FOREIGN KEY (`pid`) REFERENCES `oj_problems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `oj_contest_register`
  ADD CONSTRAINT `oj_contest_register_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `oj_contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_contest_register_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `oj_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `oj_contest_submissions`
  ADD CONSTRAINT `oj_contest_submissions_ibfk_1` FOREIGN KEY (`rid`) REFERENCES `oj_records` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_contest_submissions_ibfk_2` FOREIGN KEY (`cid`) REFERENCES `oj_contests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `oj_dependencies`
  ADD CONSTRAINT `oj_dependencies_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `oj_problems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `oj_probdist_queue`
  ADD CONSTRAINT `oj_probdist_queue_ibfk_1` FOREIGN KEY (`server`) REFERENCES `oj_judgeservers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_probdist_queue_ibfk_2` FOREIGN KEY (`pid`) REFERENCES `oj_problems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `oj_problems`
  ADD CONSTRAINT `oj_problems_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `oj_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `oj_problem_tags`
  ADD CONSTRAINT `oj_problems_tags_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `oj_problems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_problems_tags_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `oj_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `oj_resources`
  ADD CONSTRAINT `oj_resources_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `oj_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `oj_resources_tags`
  ADD CONSTRAINT `oj_resources_tags_ibfk_1` FOREIGN KEY (`rid`) REFERENCES `oj_resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_resources_tags_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `oj_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `oj_tag_acl`
  ADD CONSTRAINT `oj_tag_acl_ibfk_1` FOREIGN KEY (`tid`) REFERENCES `oj_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `oj_testcases`
  ADD CONSTRAINT `oj_testcases_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `oj_problems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `oj_user_acl`
  ADD CONSTRAINT `oj_user_acl_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `oj_users` (`id`) ON DELETE CASCADE;

ALTER TABLE `oj_user_tags`
  ADD CONSTRAINT `oj_user_tags_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `oj_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oj_user_tags_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `oj_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

delimiter ;;
CREATE PROCEDURE contest_judge(contest_id INT)
BEGIN
	DECLARE done INT DEFAULT FALSE;
	DECLARE mid,mpid,muid INT DEFAULT 0;
	DECLARE mcode TEXT;
	DECLARE mlang VARCHAR(8);
	DECLARE cur CURSOR FOR SELECT `id`,`pid`,`uid`,`code`,`lang` FROM `oj_contest_submissions` WHERE `cid` = contest_id AND `timestamp` = (SELECT MAX(`temp`.`timestamp`) FROM `oj_contest_submissions` AS `temp` WHERE `temp`.`uid`=`oj_contest_submissions`.`uid` AND `temp`.`pid`=`oj_contest_submissions`.`pid` AND `temp`.`cid` = contest_id);

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	OPEN cur;

	ins: LOOP
		FETCH cur INTO mid,mpid,muid,mcode,mlang;
		IF done THEN
		  LEAVE ins;
		END IF;
		INSERT INTO `oj_records` (`pid`,`uid`,`code`,`lang`,`timestamp`) VALUES (mpid,muid,mcode,mlang, UNIX_TIMESTAMP());
		UPDATE `oj_contest_submissions` SET `rid` = LAST_INSERT_ID() WHERE `id` = mid;
	END LOOP;
END;;