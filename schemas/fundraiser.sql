DROP USER 'YYYYYYYY'@'localhost';
CREATE USER 'YYYYYYYY'@'localhost' IDENTIFIED BY 'XXXXXXXX';
GRANT ALL PRIVILEGES ON *.* TO 'YYYYYYYY'@'localhost';
FLUSH PRIVILEGES;
SHOW GRANTS FOR 'YYYYYYYY'@'localhost';


DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(255) DEFAULT NULL,
  `user_datecreated` datetime DEFAULT NULL,
  `user_status` int(10) unsigned NOT NULL DEFAULT '0',
  `user_password` varchar(45) DEFAULT NULL,
  `user_password_is_temp` varchar(1) NOT NULL DEFAULT 'Y',
  `user_passwordexpiration` datetime DEFAULT NULL,
  `user_lastpasswordchange` datetime DEFAULT NULL,
  `user_first_name` varchar(255) DEFAULT NULL,
  `user_last_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id_UNIQUE` (`user_id`),
  UNIQUE KEY `user_login_UNIQUE` (`user_login`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `UserSession`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserSession` (
  `session_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `user_startdate` datetime DEFAULT NULL,
  `user_enddate` datetime DEFAULT NULL,
  `user_token` varchar(255) NOT NULL,
  `user_lastactive` datetime DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  UNIQUE KEY `user_token_UNIQUE` (`user_token`)
) ENGINE=MyISAM AUTO_INCREMENT=259 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `Organization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Organization` (
  `org_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_name` varchar(255) NOT NULL,
  `org_fullname` varchar(255) NOT NULL,
  `org_datejoined` datetime DEFAULT NULL,
  `org_status` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`org_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `UserRole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserRole` (
  `user_id` bigint(20) NOT NULL,
  `org_id` bigint(20) DEFAULT NULL,
  `role_id` bigint(20) DEFAULT NULL,
  `camp_id` bigint(20) DEFAULT '0',
  `create_date` datetime DEFAULT NULL,
  `expire_date` datetime DEFAULT NULL,
  KEY `uerorg` (`user_id`,`org_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `Campaign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Campaign` (
  `camp_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `camp_type` varchar(45) DEFAULT NULL,
  `camp_orgid` bigint(20) unsigned DEFAULT NULL,
  `camp_creatorid` bigint(20) unsigned DEFAULT NULL,
  `camp_name` varchar(255) DEFAULT NULL,
  `camp_status` varchar(45) DEFAULT NULL,
  `camp_refname` varchar(255) DEFAULT NULL,
  `camp_url` varchar(255) DEFAULT NULL,
  `camp_datecreated` datetime DEFAULT NULL,
  `camp_startdate` datetime DEFAULT NULL,
  `camp_enddate` datetime DEFAULT NULL,
  `camp_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`camp_id`),
  UNIQUE KEY `campaign_id_UNIQUE` (`camp_type`,`camp_name`,`camp_orgid`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `CampaignType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CampaignType` (
  `camp_type` varchar(45) NOT NULL,
  `camp_description` varchar(255) DEFAULT NULL,
  `camp_maxperorganization` int(11) DEFAULT NULL,
  `camp_duration` datetime DEFAULT NULL,
  PRIMARY KEY (`camp_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `SaleItem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SaleItem` (
  `item_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_type` varchar(45) DEFAULT NULL,
  `item_orgid` bigint(20) unsigned DEFAULT NULL,
  `item_creatorid` bigint(20) unsigned DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `item_status` varchar(45) DEFAULT NULL,
  `item_refname` varchar(255) DEFAULT NULL,
  `item_url` varchar(255) DEFAULT NULL,
  `item_datecreated` datetime DEFAULT NULL,
  `item_startdate` datetime DEFAULT NULL,
  `item_enddate` datetime DEFAULT NULL,
  `item_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `campaign_id_UNIQUE` (`item_type`,`item_name`,`item_orgid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CampaignType`
--

DROP TABLE IF EXISTS `SaleItemType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SaleItemType` (
  `item_type` varchar(45) NOT NULL,
  `item_description` varchar(255) DEFAULT NULL,
  `item_maxperorganization` int(11) DEFAULT NULL,
  `item_duration` datetime DEFAULT NULL,
  PRIMARY KEY (`item_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;



DROP TABLE IF EXISTS `EmailLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EmailLog` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(255) NOT NULL,
  `externalStatus` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Keycode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Keycode` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `max_uses` int(10) DEFAULT 1,
  `use_count` int(10) DEFAULT 0,
  `owner_id` bigint(20) unsigned DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;