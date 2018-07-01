# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.5-10.2.11-MariaDB)
# Database: tchg
# Generation Time: 2018-07-01 10:03:19 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table department
# ------------------------------------------------------------

DROP TABLE IF EXISTS `department`;

CREATE TABLE `department` (
  `pid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `pname` varchar(20) NOT NULL,
  `plevel` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`pid`),
  UNIQUE KEY `pname` (`pname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='部门表';

LOCK TABLES `department` WRITE;
/*!40000 ALTER TABLE `department` DISABLE KEYS */;

INSERT INTO `department` (`pid`, `pname`, `plevel`)
VALUES
	(1,'太仓海警支队',0),
	(2,'司令部',1),
	(3,'政治处',1),
	(4,'后勤处',1),
	(5,'一大队',2),
	(6,'二大队',2),
	(7,'三大队',2),
	(8,'四大队',2),
	(9,'勤务中队',2),
	(10,'机动中队',2);

/*!40000 ALTER TABLE `department` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table job
# ------------------------------------------------------------

DROP TABLE IF EXISTS `job`;

CREATE TABLE `job` (
  `jid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `jname` varchar(20) NOT NULL,
  `pid` tinyint(3) unsigned NOT NULL COMMENT '职务属于哪个1级部门',
  PRIMARY KEY (`jid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='职务表';

LOCK TABLES `job` WRITE;
/*!40000 ALTER TABLE `job` DISABLE KEYS */;

INSERT INTO `job` (`jid`, `jname`, `pid`)
VALUES
	(1,'参谋',2),
	(2,'干事',3),
	(3,'助理',4);

/*!40000 ALTER TABLE `job` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table missiondepartment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `missiondepartment`;

CREATE TABLE `missiondepartment` (
  `mid` int(10) unsigned NOT NULL COMMENT '对应任务表的mid',
  `pid` tinyint(4) NOT NULL COMMENT '对应部门表的pid，0表示所有部门',
  PRIMARY KEY (`mid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `missiondepartment` WRITE;
/*!40000 ALTER TABLE `missiondepartment` DISABLE KEYS */;

INSERT INTO `missiondepartment` (`mid`, `pid`)
VALUES
	(1,5),
	(1,6),
	(6,5),
	(6,6),
	(6,7),
	(6,8),
	(7,9),
	(7,10),
	(8,5),
	(8,6),
	(9,5),
	(9,6),
	(9,9),
	(10,5),
	(10,6),
	(10,10),
	(11,0);

/*!40000 ALTER TABLE `missiondepartment` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table missionlist
# ------------------------------------------------------------

DROP TABLE IF EXISTS `missionlist`;

CREATE TABLE `missionlist` (
  `mid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mtitle` varchar(50) NOT NULL COMMENT '任务标题',
  `datemake` date NOT NULL COMMENT '添加任务的日期',
  `datestart` date NOT NULL COMMENT '任务开始日期',
  `dateend` date DEFAULT '9999-12-31' COMMENT '任务截止日期',
  `mcontent` text NOT NULL COMMENT '任务简要描述',
  `annex` text NOT NULL COMMENT '任务附件',
  `author` int(11) NOT NULL COMMENT '任务发布用户id',
  `tips` varchar(15) DEFAULT NULL COMMENT '任务标题的额外注释',
  `status` tinyint(4) NOT NULL COMMENT '任务状态 0未发布，1发布，2处理中，3待评审，4完成，5退回,-1删除',
  `timeout` tinyint(1) NOT NULL DEFAULT 0 COMMENT '任务是否超时',
  `rcount` tinyint(4) NOT NULL DEFAULT 0 COMMENT '回复统计',
  `pass` text NOT NULL COMMENT '可访问部门的逗号分隔字符串',
  PRIMARY KEY (`mid`),
  KEY `author` (`author`),
  KEY `status` (`status`),
  FULLTEXT KEY `mtitle` (`mtitle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `missionlist` WRITE;
/*!40000 ALTER TABLE `missionlist` DISABLE KEYS */;

INSERT INTO `missionlist` (`mid`, `mtitle`, `datemake`, `datestart`, `dateend`, `mcontent`, `annex`, `author`, `tips`, `status`, `timeout`, `rcount`, `pass`)
VALUES
	(1,'测试任务','2018-06-19','2018-06-19','2018-07-19','测试任务测试任务测试任务','[{\"type\":\"1\",\"url\":\"\\u516c\\u544a.doc\",\"name\":\"\\u516c\\u544a\"}]',3,'临时',-1,0,0,'5,6'),
	(6,'群租整治','2018-06-19','2018-06-19','2018-08-01','请核实相关情况，并再期限内督促指定整改方案','[{\"type\":\"1\",\"url\":\"\\u7fa4\\u79df\\u6574\\u6539.doc\",\"name\":\"\\u7fa4\\u79df\\u6574\\u6539\"}]',3,'司令部',1,0,0,'5,6,7,8'),
	(7,'燃气使用中的注意事项','2018-06-20','2018-06-20','9999-12-31','请查看相关附件','[{\"type\":\"1\",\"url\":\"\\u71c3\\u6c14\\u4f7f\\u7528\\u6ce8\\u610f\\u4e8b\\u9879.doc\",\"name\":\"\\u71c3\\u6c14\\u4f7f\\u7528\\u6ce8\\u610f\\u4e8b\\u9879\"}]',3,'',1,0,0,'9,10'),
	(8,'测试任务自动过期','2018-06-20','2018-06-17','2018-06-19','仅是测试任务过期，测试mysql的event是否能够正常运行','[{\"type\":\"1\",\"url\":\"MYSQL\\u6559\\u7a0b.txt\",\"name\":\"MYSQL\\u6559\\u7a0b\"}]',3,'政治部',1,1,0,'6,5'),
	(9,'再一次测试','2018-06-20','2018-06-12','9999-12-31','看日期默认值是否生效了','[{\"type\":\"1\",\"url\":\"\\u5947\\u602a\\u7684MYSQL.txt\",\"name\":\"\\u5947\\u602a\\u7684MYSQL\"}]',3,'后勤处',1,0,0,'5,6,9'),
	(10,'可以的，在试试吧','2018-06-20','2018-06-19','9999-12-31','册那，是不是真的搞不定了，果然很久没搞数据库了','[{\"type\":\"1\",\"url\":\"\\u4e0d\\u77e5\\u9053\\u662f\\u5426\\u53ef\\u884c.doc\",\"name\":\"\\u4e0d\\u77e5\\u9053\\u662f\\u5426\\u53ef\\u884c\"}]',3,'司令部',1,0,0,'5,6,10'),
	(11,'领导发的任务给其他人','2018-06-19','2018-06-20','2018-12-31','大家好，我是领导，这里给大家发个任务。请大家回去做好自我检查工作，上级有领导不定时会下来做工作检查和指导，所以大家心里要有数。最终请司令部相关通知落实下。','[{\"type\":\"1\",\"url\":\"\\u516c\\u544a.doc\",\"name\":\"\\u516c\\u544a\"},{\"type\":\"1\",\"url\":\"\\u4e0d\\u77e5\\u9053\\u662f\\u5426\\u53ef\\u884c.doc\",\"name\":\"\\u4e0d\\u77e5\\u9053\\u662f\\u5426\\u53ef\\u884c\"}]',5,'司令部',1,0,0,'0');

/*!40000 ALTER TABLE `missionlist` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table missionreturn
# ------------------------------------------------------------

DROP TABLE IF EXISTS `missionreturn`;

CREATE TABLE `missionreturn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mid` int(11) NOT NULL COMMENT '对应的任务id',
  `uid` int(11) NOT NULL COMMENT '对应的用户id',
  `content` text DEFAULT NULL COMMENT '日志内容',
  `retype` tinyint(4) NOT NULL DEFAULT 0 COMMENT '日志类型 0接受 1回复 2退回 3完成 10修改 11删除',
  `datemake` date NOT NULL COMMENT '日志记录日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `missionreturn` WRITE;
/*!40000 ALTER TABLE `missionreturn` DISABLE KEYS */;

INSERT INTO `missionreturn` (`id`, `mid`, `uid`, `content`, `retype`, `datemake`)
VALUES
	(1,1,2,'测试内容，回复的测试内容可以查看，这个任务很好的完成了，请上级审核吧',0,'2018-06-20');

/*!40000 ALTER TABLE `missionreturn` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table task
# ------------------------------------------------------------

CREATE TABLE `task` (
  `mid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `mtitle` varchar(20) NOT NULL DEFAULT '' COMMENT '副标题',
  `content` text NOT NULL COMMENT '内容',
  `start_at` int(11) NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_at` int(11) NOT NULL DEFAULT 0 COMMENT '结束时间',
  `create_at` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_at` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `create_user_id` int(11) NOT NULL DEFAULT 0 COMMENT '创建的用户id',
  `last_do_user_id` int(11) NOT NULL DEFAULT 0 COMMENT '最近修改的用户id',
  `count` int(11) NOT NULL DEFAULT 0 COMMENT '回复数量',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '状态（1:初使，2:已接受 3:已回复 4:完成 5:打回 6:已删除 7:已作废）',
  `replys` text NOT NULL COMMENT '回复',
  `annex` text NOT NULL COMMENT '附件',
  `is_timeout` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否过期（0:未过期，1过期）',
  `departments` varchar(20) NOT NULL DEFAULT '' COMMENT '部门',
  PRIMARY KEY (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `task` WRITE;
/*!40000 ALTER TABLE `task` DISABLE KEYS */;

INSERT INTO `task` (`mid`, `title`, `mtitle`, `content`, `start_at`, `end_at`, `create_at`, `update_at`, `create_user_id`, `last_do_user_id`, `count`, `status`, `replys`, `annex`, `is_timeout`, `departments`)
VALUES
	(1,'测试234','','测试',1433100599,2147483647,1530379943,1530427465,3,2,1,1,'[{\"reply_id\":1,\"create_user_id\":\"2\",\"content\":\"\\u6d4b\\u8bd5\",\"update_at\":1530427465,\"status\":\"2\"}]','[]',0,'5,6,7'),
	(2,'测试1234567','','测试22',1433100599,1433100599,1530381930,1530427749,5,2,2,1,'[{\"reply_id\":1,\"create_user_id\":\"2\",\"content\":\"\\u6d4b\\u8bd5\",\"update_at\":1530427716,\"status\":\"2\"},{\"reply_id\":2,\"create_user_id\":\"2\",\"content\":\"\\u6d4b\\u8bd5\",\"update_at\":1530427749,\"status\":\"3\"}]','[]',0,'3,5,6');

/*!40000 ALTER TABLE `task` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table task_department_relation
# ------------------------------------------------------------

CREATE TABLE `task_department_relation` (
  `mid` int(11) NOT NULL COMMENT '任务id',
  `pid` int(11) NOT NULL COMMENT '部门id',
  PRIMARY KEY (`mid`,`pid`),
  KEY `department_id` (`pid`,`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `task_department_relation` WRITE;
/*!40000 ALTER TABLE `task_department_relation` DISABLE KEYS */;

INSERT INTO `task_department_relation` (`mid`, `pid`)
VALUES
	(1,5),
	(1,6),
	(1,7),
	(2,3),
	(2,5),
	(2,6);

/*!40000 ALTER TABLE `task_department_relation` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `userpwd` char(32) NOT NULL,
  `tid` tinyint(3) unsigned NOT NULL COMMENT '用户类型tid',
  `pid` tinyint(3) unsigned NOT NULL COMMENT '所属部门pid',
  `jid` tinyint(3) unsigned NOT NULL COMMENT '所属职务jid',
  `realname` varchar(30) DEFAULT NULL COMMENT '真实姓名',
  `telnumber` varchar(20) DEFAULT NULL COMMENT '联系电话',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;

INSERT INTO `user` (`uid`, `username`, `userpwd`, `tid`, `pid`, `jid`, `realname`, `telnumber`)
VALUES
	(1,'admin','21232f297a57a5a743894a0e4a801fc3',1,0,0,'张三丰',''),
	(2,'user','96e79218965eb72c92a549dd5a330112',4,6,2,'李一天','222111'),
	(3,'manager','e10adc3949ba59abbe56e057f20f883e',3,3,0,'王五湖',''),
	(5,'leader','670b14728ad9902aecba32e22fa4f6bd',2,0,0,'李四海','');

/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table usertype
# ------------------------------------------------------------

DROP TABLE IF EXISTS `usertype`;

CREATE TABLE `usertype` (
  `tid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `tname` varchar(20) NOT NULL,
  `plevel` tinyint(3) unsigned NOT NULL COMMENT '用于标记该类型用户对应部门的级别',
  `job_flag` tinyint(1) NOT NULL DEFAULT 0 COMMENT '用于标记该类型用于是否可用职务数据',
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户类型表';

LOCK TABLES `usertype` WRITE;
/*!40000 ALTER TABLE `usertype` DISABLE KEYS */;

INSERT INTO `usertype` (`tid`, `tname`, `plevel`, `job_flag`)
VALUES
	(1,'系统管理员',0,0),
	(2,'支队领导',0,0),
	(3,'上级用户',1,0),
	(4,'下级用户',2,1);

/*!40000 ALTER TABLE `usertype` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
