-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 03, 2010 at 02:51 PM
-- Server version: 5.1.44
-- PHP Version: 5.2.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `comrad_Events`
--

-- --------------------------------------------------------

--
-- Table structure for table `Alert`
--

CREATE TABLE `Alert` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` text NOT NULL,
  `Copy` text NOT NULL,
  `Active` tinyint(1) NOT NULL,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `Alert`
--

INSERT INTO `Alert` VALUES(1, 'People Don''t Like This Song.', '<p>People really don''t like this song and they keep calling in whenever someone plays it.</p>\n<p>&nbsp;</p>\n<p>Maybe we should stop <a href="http://playing.com" target="_blank">playing</a> it?</p>', 1);
INSERT INTO `Alert` VALUES(2, 'Test Alert', '<p>Onions</p>', 1);
INSERT INTO `Alert` VALUES(3, 'Test Alert', '<p>Testing testing 4,5,6</p>', 1);
INSERT INTO `Alert` VALUES(4, 'Test Alert', '<p>Testing testing 345</p>', 1);
INSERT INTO `Alert` VALUES(5, 'Heyyo', '<p>wassup you</p>', 1);
INSERT INTO `Alert` VALUES(6, 'This Is A New Name!', '<p>Editing this alert</p>', 1);
INSERT INTO `Alert` VALUES(7, 'Red Alert', '<p><span class="importantHeading">Red alert</span>!</p>\n<p>People are attacking!</p>', 1);
INSERT INTO `Alert` VALUES(8, 'Lkn', '<p>lkjbkj</p>', 1);
INSERT INTO `Alert` VALUES(9, 'Kj', '<p>hkhl</p>', 1);
INSERT INTO `Alert` VALUES(10, 'Test Alertasase', '<p><span class="heading">nbj ,,,,jklk</span></p>\n<p>&euro;<span class="heading"><br /></span></p>', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Announcement`
--

CREATE TABLE `Announcement` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` text NOT NULL,
  `Copy` text NOT NULL,
  `Active` tinyint(1) NOT NULL,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `Announcement`
--

INSERT INTO `Announcement` VALUES(1, 'Test Announcement', '<p>Carrots!</p>', 1);
INSERT INTO `Announcement` VALUES(2, 'Edited Announcement, Edited Again.', '<p>This is a new annoucement that is edited, edited again and agaain, and re-edited a third time.</p>\n<p>Oh man, it''s been edited so many times!</p>', 1);
INSERT INTO `Announcement` VALUES(3, 'Google Freaking Rocks!', '<p>FYI, Google freaking rocks.</p>\n<p>I''m not sure if you''ve heard of this amazing company that is taking over the world, but it rocks.</p>\n<p>Click the logo below to check out their web site.</p>\n<p><a href="http://google.com" target="_blank"><img src="http://www.google.com/intl/en_ALL/images/logo.gif" alt="" /></a></p>', 1);
INSERT INTO `Announcement` VALUES(4, 'A New Announce', '<p>aa</p>', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Feature`
--

CREATE TABLE `Feature` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `ProducerName` text NOT NULL,
  `GuestName` text,
  `Title` text NOT NULL,
  `Description` text,
  `InternalNote` text,
  `Active` tinyint(1) NOT NULL,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `Feature`
--

INSERT INTO `Feature` VALUES(1, 'Name', 'Gname', 'Feature', '<p>Heyyo</p>', '<p>carrots</p>', 1);
INSERT INTO `Feature` VALUES(2, 'Goodbye', 'Sir', 'Test', 'Hello', NULL, 0);
INSERT INTO `Feature` VALUES(3, 'A', 'Your Mmom', 'Edited Feature', '<p>This is a new feature that is edited</p>', '<p>onions</p>', 0);
INSERT INTO `Feature` VALUES(4, 'Jknb', '', 'Kl', '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Host`
--

CREATE TABLE `Host` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `Host`
--

INSERT INTO `Host` VALUES(1, 'George Of The Jungle');
INSERT INTO `Host` VALUES(2, 'Sammy');
INSERT INTO `Host` VALUES(3, 'Fsdfsdf');
INSERT INTO `Host` VALUES(4, 'Treesss');
INSERT INTO `Host` VALUES(5, 'Old Hostess');
INSERT INTO `Host` VALUES(6, 'Kk');
INSERT INTO `Host` VALUES(7, 'A');
INSERT INTO `Host` VALUES(8, 'Bb');
INSERT INTO `Host` VALUES(9, 'Akhee');
INSERT INTO `Host` VALUES(10, 'Albert');

-- --------------------------------------------------------

--
-- Table structure for table `LegalID`
--

CREATE TABLE `LegalID` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` text NOT NULL,
  `Copy` text NOT NULL,
  `Active` tinyint(1) NOT NULL,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `LegalID`
--

INSERT INTO `LegalID` VALUES(21, 'Awgwe', '<p>Wassup</p>', 0);
INSERT INTO `LegalID` VALUES(22, 'This Didn''t Have A Title.', '<p>New LegalID</p>', 1);
INSERT INTO `LegalID` VALUES(23, 'Afe', '<p>Trying New Legalid</p>', 1);
INSERT INTO `LegalID` VALUES(24, 'Nb', '<p>kjbkb</p>', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ProducerName`
--

CREATE TABLE `ProducerName` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ProducerName`
--


-- --------------------------------------------------------

--
-- Table structure for table `PSA`
--

CREATE TABLE `PSA` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `StartDate` date NOT NULL,
  `KillDate` date NOT NULL,
  `Title` text NOT NULL,
  `Copy` text NOT NULL,
  `OrgName` text NOT NULL,
  `ContactName` text NOT NULL,
  `ContactPhone` text NOT NULL,
  `ContactWebsite` text,
  `ContactEmail` text,
  `Active` tinyint(1) NOT NULL,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `PSA`
--

INSERT INTO `PSA` VALUES(1, '2010-04-16', '2010-04-15', 'Awgwe', '<p>Edit my friend!</p>', 'Aweg', 'Bryan', '715.563.0797', '', 'test@gmail.com', 0);
INSERT INTO `PSA` VALUES(2, '2010-04-16', '2010-04-14', 'Aewg', '<p>They''re not really all that good for you</p>', 'A', 'Dsretyiuok;l,m Nbvcfdtyutrewq123~!@()(*&^%QW#E$R%T^YU', 'dsretyiuok;l,m nbvcfdtyutrewq123~!@()(*&^%QW#E$R%T^YU', '', 'dsretyiuok;l,m nbvcfdtyutrewq123~!@()(*&^%QW#E$R%T^YU', 0);
INSERT INTO `PSA` VALUES(3, '2010-04-07', '2010-04-16', 'Title', '<p>a</p>', 'A', 'E', '3', '', '', 0);
INSERT INTO `PSA` VALUES(4, '2010-04-24', '2010-04-29', 'New Psa', '<p>sdlfkjasd;lkfj;asdklfja</p>', 'Ssssss', 'Sssssssss', 'sssss', 'geargeod.com', 'sss', 0);

-- --------------------------------------------------------

--
-- Table structure for table `RecurringEventsDaily`
--

CREATE TABLE `RecurringEventsDaily` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ParentInstanceID` bigint(20) unsigned NOT NULL COMMENT 'ScheduledInstance ID',
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `EveryXDays` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `RecurringEventsDaily`
--


-- --------------------------------------------------------

--
-- Table structure for table `RecurringEventsException`
--

CREATE TABLE `RecurringEventsException` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `RecurringID` int(10) unsigned NOT NULL,
  `RecurringType` enum('daily','weekly','monthly','yearly') COLLATE utf8_unicode_ci NOT NULL,
  `StartDateTime` datetime NOT NULL,
  `EndDateTime` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `RecurringEventsException`
--


-- --------------------------------------------------------

--
-- Table structure for table `RecurringEventsMonthly`
--

CREATE TABLE `RecurringEventsMonthly` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ParentInstanceID` bigint(20) unsigned NOT NULL COMMENT 'ScheduledInstance ID',
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `EveryXMonths` int(11) unsigned NOT NULL,
  `RepeatBy` enum('dayOfMonth','dayOfWeek') COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `RecurringEventsMonthly`
--


-- --------------------------------------------------------

--
-- Table structure for table `RecurringEventsWeekly`
--

CREATE TABLE `RecurringEventsWeekly` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ParentInstanceID` bigint(20) unsigned NOT NULL COMMENT 'ScheduledInstance ID',
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `EveryXWeeks` int(10) unsigned NOT NULL DEFAULT '1',
  `Sunday` tinyint(1) NOT NULL,
  `Monday` tinyint(1) NOT NULL,
  `Tuesday` tinyint(1) NOT NULL,
  `Wednesday` tinyint(1) NOT NULL,
  `Thursday` tinyint(1) NOT NULL,
  `Friday` tinyint(1) NOT NULL,
  `Saturday` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `RecurringEventsWeekly`
--

INSERT INTO `RecurringEventsWeekly` VALUES(5, 2, '2010-05-31', '1969-12-31', 1, 0, 1, 0, 1, 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `RecurringEventsYearly`
--

CREATE TABLE `RecurringEventsYearly` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ParentInstanceID` bigint(20) unsigned NOT NULL COMMENT 'ScheduledInstance ID',
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `EveryXYears` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `RecurringEventsYearly`
--


-- --------------------------------------------------------

--
-- Table structure for table `ScheduledInstances`
--

CREATE TABLE `ScheduledInstances` (
  `ID` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `Username` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `EventID` int(11) NOT NULL,
  `EventType` enum('Alert','Announcement','Feature','Host','LegalID','ProducerName','PSA','Show','TicketGiveaway','Underwriting','Venue') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `RecurringID` int(10) unsigned NOT NULL,
  `RecurringType` enum('','daily','weekly','monthly','yearly') NOT NULL,
  `StartDateTime` datetime NOT NULL,
  `Duration` int(11) NOT NULL COMMENT 'Seconds',
  `Description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Scheduled` tinyint(1) NOT NULL,
  `SafeToRebuild` tinyint(1) NOT NULL COMMENT 'Whether the event can be scrubbed and rebuilt',
  `Parent` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `ScheduledInstances`
--

INSERT INTO `ScheduledInstances` VALUES(2, 'root', 1, 'Show', 5, 'weekly', '2010-05-31 06:00:00', 21600, '', 0, 0, 1);
INSERT INTO `ScheduledInstances` VALUES(3, '', 1, 'Show', 5, 'weekly', '2010-05-31 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(4, '', 1, 'Show', 5, 'weekly', '2010-06-02 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(5, '', 1, 'Show', 5, 'weekly', '2010-06-04 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(6, '', 1, 'Show', 5, 'weekly', '2010-06-07 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(7, '', 1, 'Show', 5, 'weekly', '2010-06-09 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(8, '', 1, 'Show', 5, 'weekly', '2010-06-11 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(9, '', 1, 'Show', 5, 'weekly', '2010-06-14 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(10, '', 1, 'Show', 5, 'weekly', '2010-06-16 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(11, '', 1, 'Show', 5, 'weekly', '2010-06-18 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(12, '', 1, 'Show', 5, 'weekly', '2010-06-21 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(13, '', 1, 'Show', 5, 'weekly', '2010-06-23 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(14, '', 1, 'Show', 5, 'weekly', '2010-06-25 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(15, '', 1, 'Show', 5, 'weekly', '2010-06-28 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(16, '', 1, 'Show', 5, 'weekly', '2010-06-30 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(17, '', 1, 'Show', 5, 'weekly', '2010-07-02 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(18, '', 1, 'Show', 5, 'weekly', '2010-07-05 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(19, '', 1, 'Show', 5, 'weekly', '2010-07-07 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(20, '', 1, 'Show', 5, 'weekly', '2010-07-09 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(21, '', 1, 'Show', 5, 'weekly', '2010-07-12 06:00:00', 21600, '', 0, 1, 0);
INSERT INTO `ScheduledInstances` VALUES(22, '', 1, 'Show', 5, 'weekly', '2010-07-13 06:00:00', 21600, '', 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ShowMetadata`
--

CREATE TABLE `ShowMetadata` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text CHARACTER SET latin1 NOT NULL,
  `HostID` int(11) DEFAULT NULL,
  `HasHost` tinyint(1) NOT NULL,
  `Duration` int(11) NOT NULL,
  `StartDateTime` datetime NOT NULL,
  `mp3_code` text CHARACTER SET latin1 NOT NULL,
  `record_audio` tinyint(1) NOT NULL,
  `ShowURL` text CHARACTER SET latin1,
  `Source` enum('KGNU','Ext') CHARACTER SET latin1 NOT NULL,
  `Category` enum('Announcements','Mix','Music','NewsPA','OurMusic') CHARACTER SET latin1 NOT NULL,
  `Class` text CHARACTER SET latin1 NOT NULL,
  `DescriptionShort` text CHARACTER SET latin1,
  `DescriptionLong` text CHARACTER SET latin1,
  `Active` tinyint(1) NOT NULL,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `ShowMetadata`
--

INSERT INTO `ShowMetadata` VALUES(1, 'Test Show', 0, 0, 3600, '2010-04-06 00:00:00', '88888', 0, 'gerago.com', 'Ext', 'Mix', 'mix', '<p>carrots</p>', '<p>onions</p>', 0);
INSERT INTO `ShowMetadata` VALUES(5, 'Herro', NULL, 1, 10, '0000-00-00 00:00:00', '123456', 1, NULL, 'KGNU', 'Announcements', 'fghjk', NULL, NULL, 0);
INSERT INTO `ShowMetadata` VALUES(6, 'George', 2, 0, 35, '2010-04-24 00:00:00', '43567', 1, 'gerago.com', 'Ext', 'NewsPA', 'mix it up', '<p>ffffffffffff</p>', '<p>ffffffffffffffffffffffffffffffff</p>', 1);
INSERT INTO `ShowMetadata` VALUES(7, 'Old Show', 0, 0, 50, '2010-04-29 00:00:00', '34567', 0, 'you.com', 'Ext', 'Mix', 'mix', '<p>Win</p>', '<p>Loser</p>', 0);
INSERT INTO `ShowMetadata` VALUES(8, 'Agwe', 1, 1, 60, '2010-04-26 00:00:00', 'a', 0, 'a', 'KGNU', 'Announcements', 'e', '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `TicketGiveaway`
--

CREATE TABLE `TicketGiveaway` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` text NOT NULL,
  `Copy` text NOT NULL,
  `EventDate` date NOT NULL,
  `VenueID` int(11) NOT NULL,
  `WinnerName` text,
  `WinnerPhone` text,
  `Type` enum('Hard Ticket','Guest List') NOT NULL,
  `Active` tinyint(1) NOT NULL,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `TicketGiveaway`
--

INSERT INTO `TicketGiveaway` VALUES(1, 'Test Ticket Giveaway', '<p>Congratulations! You just won tickets to see Owl City in Denver!!</p>', '2010-04-13', 1, 'Bryan', '715.563.0739', 'Hard Ticket', 0);
INSERT INTO `TicketGiveaway` VALUES(2, ',k', '<p>awga</p>', '2010-04-15', 1, '', '', 'Hard Ticket', 1);
INSERT INTO `TicketGiveaway` VALUES(3, 'Asge', '<p>a</p>', '2010-04-20', 3, '', '', 'Hard Ticket', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Underwriting`
--

CREATE TABLE `Underwriting` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` text NOT NULL,
  `Copy` text NOT NULL,
  `Active` tinyint(1) NOT NULL,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `Underwriting`
--

INSERT INTO `Underwriting` VALUES(1, 'Test Underwriting', 'This is some test copy to read during the underwriting.', 1);
INSERT INTO `Underwriting` VALUES(2, 'Test Underwriting 356', '<p>Test copy &nbsp;ccopy this copy that</p>', 1);
INSERT INTO `Underwriting` VALUES(4, 'Rawr', '<p>rawrs</p>', 0);
INSERT INTO `Underwriting` VALUES(6, 'Test This Bob', '<p>dfsdfsdf</p>', 0);
INSERT INTO `Underwriting` VALUES(10, 'Wass', '<p>dfsdfsdfwsd</p>', 1);
INSERT INTO `Underwriting` VALUES(11, 'Old Underwriting', '<p>edited</p>', 0);
INSERT INTO `Underwriting` VALUES(12, 'Aefwe', '<p>a</p>', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Venue`
--

CREATE TABLE `Venue` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `Location` text NOT NULL,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `Venue`
--

INSERT INTO `Venue` VALUES(1, 'Blue Bird Theater', 'Denver, CO');
INSERT INTO `Venue` VALUES(2, 'The Fox', 'Boulder, CO');
INSERT INTO `Venue` VALUES(3, 'The Laughing Goat', 'Boulder, CO');
