

--
-- Rename the database before creating
--

CREATE DATABASE IF NOT EXISTS gallery;
USE gallery;

--
-- Definition of table `collections`
--

CREATE TABLE `collections` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) NOT NULL,
  `image` varchar(45) default NULL,
  `sequence` int(10) unsigned default NULL,
  `search` varchar(45) default NULL,
  `uselowprice` tinyint(3) unsigned default NULL,
  `text` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Definition of table `events`
--

CREATE TABLE `events` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(65) default NULL,
  `mainimage` varchar(45) default NULL,
  `date1` date default NULL,
  `date2` date default NULL,
  `year` char(6) default NULL,
  `image1` varchar(45) NOT NULL,
  `image2` varchar(45) default NULL,
  `image3` varchar(45) default NULL,
  `image4` varchar(45) default NULL,
  `image5` varchar(45) default NULL,
  `image6` varchar(45) default NULL,
  `text` text,
  `stream` varchar(24) default NULL,
  `times` varchar(45) default NULL,
  `location` varchar(45) default NULL,
  `contact` varchar(45) default NULL,
  `address` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;


--
-- Definition of table `links`
--

DROP TABLE IF EXISTS `links`;
CREATE TABLE `links` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `picture` int(10) unsigned NOT NULL,
  `collection` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=908 DEFAULT CHARSET=latin1;


--
-- Definition of table `orderitems`
--

DROP TABLE IF EXISTS `orderitems`;
CREATE TABLE `orderitems` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `orderid` int(10) unsigned default NULL,
  `item` int(10) unsigned default NULL,
  `price` decimal(6,2) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Definition of table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ref` int(10) unsigned default NULL,
  `name` varchar(45) NOT NULL,
  `addr1` varchar(45) NOT NULL,
  `addr2` varchar(45) default NULL,
  `addr3` varchar(45) default NULL,
  `addr4` varchar(45) default NULL,
  `postcode` varchar(16) NOT NULL,
  `region` varchar(20) default NULL,
  `email` varchar(45) default NULL,
  `product` int(10) unsigned default NULL,
  `price` int(10) unsigned default NULL,
  `date` varchar(12) default NULL,
  `shipped` varchar(12) default NULL,
  `status` tinyint(3) unsigned NOT NULL default '0',
  `shippingprice` int(10) unsigned default NULL,
  `voucher` varchar(16) default NULL,
  `discounta` int(10) unsigned default NULL,
  `discounts` int(10) unsigned default NULL,
  `quantity` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=latin1;


--
-- Definition of table `paintings`
--

DROP TABLE IF EXISTS `paintings`;
CREATE TABLE `paintings` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) default NULL,
  `image` varchar(100) default NULL,
  `recent` tinyint(3) unsigned default NULL,
  `year` int(10) unsigned default NULL,
  `media` varchar(45) default NULL,
  `size` varchar(24) default NULL,
  `mount` varchar(45) default NULL,
  `tags` varchar(64) default NULL,
  `priceweb` int(10) unsigned default NULL,
  `priceebay` int(10) unsigned default NULL,
  `notes` text,
  `costcovered` tinyint(3) unsigned default NULL,
  `datesold` datetime default NULL,
  `archive` tinyint(3) unsigned default NULL,
  `status` tinyint(3) unsigned default '1',
  `seq` int(10) unsigned default NULL,
  `dateset` varchar(12) default NULL,
  `shippingrate` tinyint(3) unsigned default NULL,
  `quantity` int(10) unsigned default '1',
  `away` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=332 DEFAULT CHARSET=latin1;

--
-- Definition of table `shipping`
--

DROP TABLE IF EXISTS `shipping`;
CREATE TABLE `shipping` (
  `sizeband` tinyint(3) unsigned NOT NULL,
  `description` varchar(45) default NULL,
  `collect` int(10) unsigned default NULL,
  `uk` int(10) unsigned default NULL,
  `eu` int(10) unsigned default NULL,
  `usa` int(10) unsigned default NULL,
  `aus` int(10) unsigned default NULL,
  `artist` int(10) unsigned default NULL,
  PRIMARY KEY  (`sizeband`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `system`
--

DROP TABLE IF EXISTS `system`;
CREATE TABLE `system` (
  `idsystem` int(10) unsigned NOT NULL auto_increment,
  `nextcustomer` int(10) unsigned default NULL,
  `nextorder` int(10) unsigned default NULL,
  `nextinvoice` int(10) unsigned default NULL,
  PRIMARY KEY  (`idsystem`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `system`
--

/*!40000 ALTER TABLE `system` DISABLE KEYS */;
INSERT INTO `system` (`idsystem`,`nextcustomer`,`nextorder`,`nextinvoice`) VALUES 
 (1,1,1,1);
/*!40000 ALTER TABLE `system` ENABLE KEYS */;

--
-- Definition of table `text`
--

DROP TABLE IF EXISTS `text`;
CREATE TABLE `text` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` varchar(45) default NULL,
  `text` text,
  `abouttext` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `text` DISABLE KEYS */;
INSERT INTO `text` (`id`,`type`) VALUES 
 (1,'hometext'),
 (2,'abouttext'),
 (3,'signupsubject'),
 (4,'signuptext'),
 (5,'signupprompt'),
 (6,'homeimage'),
 (7,'aboutimage');
/*!40000 ALTER TABLE `text` ENABLE KEYS */;

DROP TABLE IF EXISTS `userpages`;
CREATE TABLE `userpages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `psn` int(10) unsigned default NULL,
  `menu` varchar(45) NOT NULL,
  `slug` varchar(45) NOT NULL,
  `title` varchar(127) NOT NULL,
  `parent` int(10) unsigned zerofill default NULL,
  `text` text,
  `image` varchar(45) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;


--
-- Definition of table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(16) default NULL,
  `password` varchar(16) NOT NULL,
  `firstname` varchar(16) default NULL,
  `fullname` varchar(45) default NULL,
  `email` varchar(45) default NULL,
  `addr1` varchar(45) default NULL,
  `addr2` varchar(45) default NULL,
  `addr3` varchar(45) default NULL,
  `addr4` varchar(45) default NULL,
  `postcode` varchar(16) default NULL,
  `website` varchar(45) default NULL,
  `level` tinyint(3) unsigned default NULL,
  `territory1` VARCHAR(16) DEFAULT 'UK',
  `territory2` VARCHAR(16) DEFAULT 'EU',
  `territory3` VARCHAR(16) DEFAULT 'USA',
  `territory4` VARCHAR(16) DEFAULT 'Australia',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Definition of table `vouchers`
--

DROP TABLE IF EXISTS `vouchers`;
CREATE TABLE `vouchers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `code` varchar(12) default NULL,
  `expires` varchar(12) default NULL,
  `discount` int(10) unsigned default NULL,
  `freeship` tinyint(3) unsigned default NULL,
  `amount` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Definition of table `vouchersused`
--

DROP TABLE IF EXISTS `vouchersused`;
CREATE TABLE `vouchersused` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `voucher` int(10) unsigned default NULL,
  `picture` int(10) unsigned default NULL,
  `date` varchar(12) default NULL,
  `order` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


