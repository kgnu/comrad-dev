SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


CREATE DATABASE `comrad_Catalog` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `comrad_Catalog`;

CREATE TABLE `Albums` (
  `a_AlbumID` int(11) NOT NULL AUTO_INCREMENT,
  `a_Title` text NOT NULL,
  `a_Artist` text,
  `a_Label` text NOT NULL,
  `a_GenreID` int(11) NOT NULL,
  `a_AddDate` datetime NOT NULL,
  `a_Local` tinyint(1) DEFAULT NULL,
  `a_Compilation` tinyint(1) DEFAULT NULL,
  `a_Location` enum('Gnu Bin','Personal','Library','Digital Library') DEFAULT NULL,
  `a_AlbumArt` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`a_AlbumID`),
  FULLTEXT KEY (`a_Title`,`a_Artist`,`a_Label`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `GenreTags` (
  `gt_GenreTagID` int(11) NOT NULL AUTO_INCREMENT,
  `gt_GenreID` int(11) NOT NULL,
  `gt_AlbumID` int(11) NOT NULL,
  PRIMARY KEY (`gt_GenreTagID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `Genres` (
  `g_GenreID` int(11) NOT NULL AUTO_INCREMENT,
  `g_Name` text NOT NULL,
  `g_TopLevel` tinyint(1) NOT NULL,
  PRIMARY KEY (`g_GenreID`),
  FULLTEXT KEY (`g_Name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `TrackPlay` (
  `tp_Id` int(10) NOT NULL AUTO_INCREMENT,
  `tp_TrackId` int(10) NOT NULL,
  `tp_ScheduledShowInstanceId` int(10) NOT NULL,
  `tp_StartDateTime` datetime NOT NULL,
  `tp_Executed` datetime DEFAULT NULL,
  `tp_Order` int(5) DEFAULT NULL,
  PRIMARY KEY (`tp_Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `Tracks` (
  `t_TrackID` int(11) NOT NULL AUTO_INCREMENT,
  `t_AlbumID` int(11) NOT NULL,
  `t_Title` text NOT NULL,
  `t_TrackNumber` smallint(6) NOT NULL,
  `t_Artist` text,
  `t_DiskNumber` smallint(6) NOT NULL,
  `t_Duration` int(11) NOT NULL,
  PRIMARY KEY (`t_TrackID`),
  FULLTEXT KEY (`t_Title`,`t_Artist`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE DATABASE `comrad_Comrad` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `comrad_Comrad`;

CREATE TABLE `Roles` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `Permissions` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

INSERT INTO `Roles` VALUES(1, 'Administrator', 'catalog=vcmr&changemypassword=vcmr&djshow=vcmr&events=vcmr&log=vcmr&roles=vcmr&schedule=vcmr&showbuilder=vcmr&users=vcmr&phpmyadmin=vcmr');
INSERT INTO `Roles` VALUES(2, 'Manager', 'users=v---&showbuilder=vcmr&calendarview=vcmr&tracks=vcmr&phpmyadmin=----&activitylog=----&roles=----');
INSERT INTO `Roles` VALUES(3, 'DJ', '');

CREATE TABLE `Users` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Role` int(10) unsigned NOT NULL,
  `LastVisit` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `Users` VALUES(1, 'root', 'ccfd60437313eab415547fd734426186', 1, '2010-10-14 14:30:32');


CREATE DATABASE `comrad_Events` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `comrad_Events`;

CREATE TABLE `Event` (
  `e_Id` int(11) NOT NULL AUTO_INCREMENT,
  `e_DISCRIMINATOR` enum('AlertEvent','AnnouncementEvent','FeatureEvent','LegalIdEvent','PSAEvent','ShowEvent','TicketGiveawayEvent','UnderwritingEvent') NOT NULL,
  `e_ProducerName` varchar(256) DEFAULT NULL,
  `e_GuestName` varchar(256) DEFAULT NULL,
  `e_Description` text,
  `e_InternalNote` text,
  `e_StartDate` date DEFAULT NULL,
  `e_KillDate` date DEFAULT NULL,
  `e_Title` text NOT NULL,
  `e_Copy` text,
  `e_OrgName` text,
  `e_ContactName` text,
  `e_ContactPhone` text,
  `e_ContactWebsite` text,
  `e_ContactEmail` text,
  `e_Active` tinyint(1) NOT NULL,
  `e_EventDate` datetime DEFAULT NULL,
  `e_VenueId` int(10) DEFAULT NULL,
  `e_WinnerName` varchar(256) DEFAULT NULL,
  `e_WinnerPhone` varchar(20) DEFAULT NULL,
  `e_TicketType` enum('Hard Ticket','Guest List') DEFAULT NULL,
  `e_HasHost` tinyint(1) DEFAULT NULL,
  `e_HostId` int(10) DEFAULT NULL,
  `e_MP3Code` varchar(256) DEFAULT NULL,
  `e_RecordAudio` tinyint(1) DEFAULT NULL,
  `e_URL` varchar(256) DEFAULT NULL,
  `e_Source` enum('KGNU','Ext') DEFAULT NULL,
  `e_Category` enum('Announcements','Mix','Music','NewsPA','OurMusic') DEFAULT NULL,
  `e_Class` varchar(256) DEFAULT NULL,
  `e_ShortDescription` text,
  `e_LongDescription` text,
  PRIMARY KEY (`e_Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `Host` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `ScheduledEvent` (
  `se_Id` int(10) NOT NULL AUTO_INCREMENT,
  `se_EventId` int(10) NOT NULL,
  `se_TimeInfoId` int(10) NOT NULL,
  PRIMARY KEY (`se_Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `ScheduledEventInstance` (
  `sei_Id` int(11) NOT NULL AUTO_INCREMENT,
  `sei_DISCRIMINATOR` enum('ScheduledAlertInstance','ScheduledAnnouncementInstance','ScheduledFeatureInstance','ScheduledLegalIdInstance','ScheduledPSAInstance','ScheduledShowInstance','ScheduledTicketGiveawayInstance','ScheduledUnderwritingInstance') NOT NULL,
  `sei_ScheduledEventId` int(10) NOT NULL,
  `sei_StartDateTime` datetime NOT NULL,
  `sei_Duration` int(5) NOT NULL,
  `sei_Executed` datetime DEFAULT NULL,
  `sei_Order` int(5) DEFAULT NULL,
  `sei_GuestName` varchar(256) DEFAULT NULL,
  `sei_Description` text,
  `sei_InternalNote` text,
  `sei_Copy` text,
  `sei_EventDate` datetime DEFAULT NULL,
  `sei_VenueId` int(10) DEFAULT NULL,
  `sei_WinnerName` varchar(256) DEFAULT NULL,
  `sei_WinnerPhone` varchar(20) DEFAULT NULL,
  `sei_TicketType` enum('Hard Ticket','Guest List') DEFAULT NULL,
  `sei_HostId` int(10) DEFAULT NULL,
  `sei_ShortDescription` text,
  `sei_LongDescription` text,
  PRIMARY KEY (`sei_Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `TimeInfo` (
  `ti_Id` int(11) NOT NULL AUTO_INCREMENT,
  `ti_DISCRIMINATOR` enum('NonRepeatingTimeInfo','DailyRepeatingTimeInfo','WeeklyRepeatingTimeInfo','MonthlyRepeatingTimeInfo','YearlyRepeatingTimeInfo') NOT NULL,
  `ti_StartDateTime` datetime NOT NULL,
  `ti_Duration` int(5) NOT NULL,
  `ti_EndDate` date DEFAULT NULL COMMENT 'Inclusive',
  `ti_Interval` int(5) DEFAULT NULL,
  `ti_WeeklyOnSunday` tinyint(1) DEFAULT NULL,
  `ti_WeeklyOnMonday` tinyint(1) DEFAULT NULL,
  `ti_WeeklyOnTuesday` tinyint(1) DEFAULT NULL,
  `ti_WeeklyOnWednesday` tinyint(1) DEFAULT NULL,
  `ti_WeeklyOnThursday` tinyint(1) DEFAULT NULL,
  `ti_WeeklyOnFriday` tinyint(1) DEFAULT NULL,
  `ti_WeeklyOnSaturday` tinyint(1) DEFAULT NULL,
  `ti_MonthlyRepeatBy` enum('DAY_OF_MONTH','DAY_OF_WEEK') DEFAULT NULL,
  PRIMARY KEY (`ti_Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `TrackFullTextSearchInfo` (
  `tftsi_TrackId` int(11) NOT NULL,
  `tftsi_TrackArtist` text,
  `tftsi_TrackTitle` text NOT NULL,
  `tftsi_AlbumArtist` text,
  `tftsi_AlbumLabel` text,
  `tftsi_AlbumTitle` text NOT NULL,
  `tftsi_GenreName` text NOT NULL,
  PRIMARY KEY (`tftsi_TrackId`),
  FULLTEXT KEY `tftsi_FullText` (`tftsi_TrackArtist`,`tftsi_TrackTitle`,`tftsi_AlbumArtist`,`tftsi_AlbumLabel`,`tftsi_AlbumTitle`,`tftsi_GenreName`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `Venue` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `Location` text NOT NULL,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;