--
-- Databases: `cts_training_dev`, `cts_training_test`, `cts_training`
--

SET FOREIGN_KEY_CHECKS=0;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Trainees`;
CREATE TABLE `Trainees` (
  `SID` int(11) NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(150) DEFAULT NULL,
  `LastName` varchar(150) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Division` varchar(150) DEFAULT NULL,
  `Dept` varchar(100) DEFAULT NULL,
  `TDate` date DEFAULT NULL,
  `TStartTime` time DEFAULT NULL,
  `TEndTime` time DEFAULT NULL,
  `ASM` varchar(100) DEFAULT NULL,
  `EmpID` varchar(25) DEFAULT NULL,
  `EmpStatus` varchar(25) DEFAULT NULL,
  `Ext` varchar(25) DEFAULT NULL,
  `Description` varchar(150) DEFAULT NULL,
  `TID` varchar(25) DEFAULT NULL,
  `Reg_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Attend` char(1) DEFAULT NULL,
  `Wait` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`SID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Trainers`;
CREATE TABLE `Trainers` (
  `TRID` int(25) NOT NULL AUTO_INCREMENT,
  `Name` text,
  PRIMARY KEY (`TRID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `Training`;
CREATE TABLE `Training` (
  `TID` int(11) NOT NULL AUTO_INCREMENT,
  `TDate` date DEFAULT NULL,
  `IsPrivate` tinyint(1) DEFAULT '0',
  `IsVisible` tinyint(1) DEFAULT '1',
  `Description` varchar(120) DEFAULT NULL,
  `TStartTime` time DEFAULT NULL,
  `TEndTime` time NOT NULL,
  `TSeats` int(25) DEFAULT NULL,
  `Email_Confirm` longtext,
  `Location` text,
  `Trainer` text,
  `Short_Description` varchar(50) DEFAULT NULL,
  `Details` text,
  `TWait` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`TID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
