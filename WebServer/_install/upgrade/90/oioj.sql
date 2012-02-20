CREATE TABLE `oj_problem_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `content` text NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


ALTER TABLE  `oj_contest_options` DROP FOREIGN KEY  `oj_contest_options_ibfk_1` ;
ALTER TABLE  `oj_contest_options` DROP INDEX  `cid`
ALTER TABLE  `oioj`.`oj_contest_options` ADD UNIQUE (
`cid` ,
`key`
)

ALTER TABLE  `oj_contest_options` ADD FOREIGN KEY (  `cid` ) REFERENCES  `oioj`.`oj_contests` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;