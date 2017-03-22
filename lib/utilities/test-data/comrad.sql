SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE `Albums` (
  `a_AlbumID` int(11) NOT NULL AUTO_INCREMENT,
  `a_Title` text NOT NULL,
  `a_Artist` text,
  `a_Label` text NOT NULL,
  `a_GenreID` int(11),
  `a_AddDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `a_Local` tinyint(1) DEFAULT NULL,
  `a_Compilation` tinyint(1) DEFAULT NULL,
  `a_Location` enum('Gnu Bin','Personal','Library','Digital Library') DEFAULT NULL,
  `a_AlbumArt` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`a_AlbumID`),
  FULLTEXT KEY `a_Title` (`a_Title`,`a_Artist`,`a_Label`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10000 ;


CREATE TABLE `Event` (
  `e_Id` int(11) NOT NULL AUTO_INCREMENT,
  `e_DISCRIMINATOR` enum('AlertEvent','AnnouncementEvent','EASTestEvent','FeatureEvent','LegalIdEvent','PSAEvent','ShowEvent','TicketGiveawayEvent','UnderwritingEvent') NOT NULL,
  `e_ProducerName` varchar(256) DEFAULT NULL,
  `e_GuestName` varchar(256) DEFAULT NULL,
  `e_Description` text,
  `e_InternalNote` varchar(256) DEFAULT NULL,
  `e_PSACategoryId` int(10) DEFAULT NULL,
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
  `e_RecordAudio` tinyint(1) DEFAULT NULL,
  `e_URL` varchar(256) DEFAULT NULL,
  `e_Source` enum('KGNU','Ext') DEFAULT NULL,
  `e_Category` enum('Announcements','Mix','Music','NewsPA','OurMusic') DEFAULT NULL,
  `e_Class` varchar(256) DEFAULT NULL,
  `e_ShortDescription` varchar(256) DEFAULT NULL,
  `e_LongDescription` text,
  PRIMARY KEY (`e_Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

INSERT INTO `Event` VALUES(1, 'AlertEvent', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Test Alert', '<p>This is a test alert.</p>', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `Event` VALUES(2, 'AnnouncementEvent', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Test Announcement', '<p>This is a test announcement.</p>', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `Event` VALUES(3, 'EASTestEvent', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Test Eas Test', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `Event` VALUES(4, 'FeatureEvent', 'Testy Testosterone', '', '<p>This is a test feature.</p>', '<p>This is just a test.</p>', NULL, NULL, NULL, 'Test Feature', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `Event` VALUES(5, 'LegalIdEvent', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Test Legal ID', '<p>This is a test legal ID</p>', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `Event` VALUES(6, 'PSAEvent', NULL, NULL, NULL, NULL, 2, '2011-02-01', '2020-02-01', 'Test PSA', '<p>This is a test PSA.</p>', 'A Test Organization', 'Testy Testosterone', '123 456 7890', 'http://test.com', 'test@test.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `Event` VALUES(7, 'ShowEvent', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Test Show', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 0, 1, 1, 'http://kgnu.org/testshow', 'KGNU', 'Music', 'Music', '<p>This is a test show.</p>', '<p>This is the long description of a test show.</p>');
INSERT INTO `Event` VALUES(8, 'UnderwritingEvent', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Test Underwriting', '<p>This is a test underwriting.</p>', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `Event` VALUES(9, 'TicketGiveawayEvent', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Test Ticket Giveaway', '<p>This is a test ticket giveaway.</p>', NULL, NULL, NULL, NULL, NULL, 1, '2011-02-01 00:00:00', 1, 'Testy Testosterone', '123 456 7890', 'Hard Ticket', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

CREATE TABLE `FloatingShowElement` (
  `fse_Id` int(10) NOT NULL AUTO_INCREMENT,
  `fse_DISCRIMINATOR` enum('TrackPlay','DJComment','VoiceBreak','FloatingShowEvent') NOT NULL,
  `fse_ScheduledShowInstanceId` int(10) NOT NULL,
  `fse_StartDateTime` datetime NOT NULL,
  `fse_Executed` datetime DEFAULT NULL,
  `fse_TrackId` int(10) DEFAULT NULL,
  `fse_Body` text,
  `fse_EventId` int(10) DEFAULT NULL,
  PRIMARY KEY (`fse_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `FloatingShowElement` ADD INDEX (  `fse_ScheduledShowInstanceId` ) ;


CREATE TABLE `Genres` (
  `g_GenreID` int(11) NOT NULL AUTO_INCREMENT,
  `g_Name` text NOT NULL,
  `g_TopLevel` tinyint(1) NOT NULL,
  PRIMARY KEY (`g_GenreID`),
  FULLTEXT KEY `g_Name` (`g_Name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=283 ;

INSERT INTO `Genres` VALUES(1, 'Unknown', 1);
INSERT INTO `Genres` VALUES(2, 'Reggae', 1);
INSERT INTO `Genres` VALUES(3, 'Blues', 1);
INSERT INTO `Genres` VALUES(4, 'Classical', 1);
INSERT INTO `Genres` VALUES(5, 'Folk', 1);
INSERT INTO `Genres` VALUES(6, 'Country', 1);
INSERT INTO `Genres` VALUES(7, 'International', 1);
INSERT INTO `Genres` VALUES(8, 'Jazz', 1);
INSERT INTO `Genres` VALUES(9, 'Lounge/Schlock', 1);
INSERT INTO `Genres` VALUES(10, 'Rock', 1);
INSERT INTO `Genres` VALUES(11, 'Space', 1);
INSERT INTO `Genres` VALUES(12, 'Spoken Word', 1);
INSERT INTO `Genres` VALUES(13, 'Sound Track', 1);
INSERT INTO `Genres` VALUES(14, 'Bluegrass', 1);
INSERT INTO `Genres` VALUES(15, 'Gospel', 1);
INSERT INTO `Genres` VALUES(16, 'R & B', 1);
INSERT INTO `Genres` VALUES(17, 'Rap', 1);
INSERT INTO `Genres` VALUES(18, 'Xmas', 1);
INSERT INTO `Genres` VALUES(19, 'Techno', 1);
INSERT INTO `Genres` VALUES(20, 'Modern', 1);
INSERT INTO `Genres` VALUES(21, 'Humor', 1);
INSERT INTO `Genres` VALUES(22, 'Celtic', 1);
INSERT INTO `Genres` VALUES(23, 'Ragtime', 1);
INSERT INTO `Genres` VALUES(24, 'Cajun', 1);
INSERT INTO `Genres` VALUES(25, 'Zydeco', 1);
INSERT INTO `Genres` VALUES(26, 'Childrens', 1);
INSERT INTO `Genres` VALUES(27, 'Hip Hop', 1);
INSERT INTO `Genres` VALUES(28, 'African', 0);
INSERT INTO `Genres` VALUES(29, 'World', 0);
INSERT INTO `Genres` VALUES(30, 'Tex-Mex', 0);
INSERT INTO `Genres` VALUES(31, 'Mexico', 0);
INSERT INTO `Genres` VALUES(32, 'Cuba', 0);
INSERT INTO `Genres` VALUES(33, 'Carribbean', 0);
INSERT INTO `Genres` VALUES(34, 'Latin America', 0);
INSERT INTO `Genres` VALUES(35, 'Brazil', 0);
INSERT INTO `Genres` VALUES(36, 'Western Europe', 0);
INSERT INTO `Genres` VALUES(37, 'Eastern Europe', 0);
INSERT INTO `Genres` VALUES(38, 'Middle East', 0);
INSERT INTO `Genres` VALUES(39, 'Indian Sub-Continent', 0);
INSERT INTO `Genres` VALUES(40, 'East Asia', 0);
INSERT INTO `Genres` VALUES(41, 'Oceania', 0);
INSERT INTO `Genres` VALUES(42, 'Contemporary', 0);
INSERT INTO `Genres` VALUES(43, 'Musical', 0);
INSERT INTO `Genres` VALUES(44, '20th Century', 0);
INSERT INTO `Genres` VALUES(45, 'Reissue', 0);
INSERT INTO `Genres` VALUES(46, 'Red Star', 0);
INSERT INTO `Genres` VALUES(47, 'Orange Star', 0);
INSERT INTO `Genres` VALUES(48, 'Rasta Star', 0);
INSERT INTO `Genres` VALUES(49, 'Latin', 0);
INSERT INTO `Genres` VALUES(50, 'Ska', 0);
INSERT INTO `Genres` VALUES(51, 'Hanukah', 0);
INSERT INTO `Genres` VALUES(52, 'Kwanzaa', 0);
INSERT INTO `Genres` VALUES(53, 'Documentary', 0);
INSERT INTO `Genres` VALUES(54, 'Irish', 0);
INSERT INTO `Genres` VALUES(55, 'Scottish', 0);
INSERT INTO `Genres` VALUES(56, 'Breton', 0);
INSERT INTO `Genres` VALUES(57, 'Galician', 0);
INSERT INTO `Genres` VALUES(58, 'Canadian', 0);
INSERT INTO `Genres` VALUES(59, 'Green Star', 0);
INSERT INTO `Genres` VALUES(60, 'Native American', 0);
INSERT INTO `Genres` VALUES(61, 'Dixie', 0);
INSERT INTO `Genres` VALUES(62, 'Compilation', 0);
INSERT INTO `Genres` VALUES(63, 'Vocal', 0);
INSERT INTO `Genres` VALUES(64, 'Rap Star', 0);
INSERT INTO `Genres` VALUES(65, 'Italy', 0);
INSERT INTO `Genres` VALUES(66, 'Puerto Rico', 0);
INSERT INTO `Genres` VALUES(67, 'Finland', 0);
INSERT INTO `Genres` VALUES(68, 'Scotland', 0);
INSERT INTO `Genres` VALUES(69, 'New Orleans', 0);
INSERT INTO `Genres` VALUES(70, 'Japanese', 0);
INSERT INTO `Genres` VALUES(71, 'Ireland', 0);
INSERT INTO `Genres` VALUES(72, 'Native Amer.', 0);
INSERT INTO `Genres` VALUES(73, 'Welsh', 0);
INSERT INTO `Genres` VALUES(74, 'Manx', 0);
INSERT INTO `Genres` VALUES(75, 'United Kingdom', 0);
INSERT INTO `Genres` VALUES(76, 'American', 0);
INSERT INTO `Genres` VALUES(77, 'Old School', 0);
INSERT INTO `Genres` VALUES(78, 'Cuban', 0);
INSERT INTO `Genres` VALUES(79, 'Industrial', 0);
INSERT INTO `Genres` VALUES(80, 'Hardcore/Punk', 0);
INSERT INTO `Genres` VALUES(81, 'Greek', 0);
INSERT INTO `Genres` VALUES(82, 'Early Music', 0);
INSERT INTO `Genres` VALUES(83, 'Polka', 0);
INSERT INTO `Genres` VALUES(84, 'Children', 0);
INSERT INTO `Genres` VALUES(85, 'Funk', 0);
INSERT INTO `Genres` VALUES(86, 'Americana', 0);
INSERT INTO `Genres` VALUES(87, 'Swing', 0);
INSERT INTO `Genres` VALUES(88, 'White Star', 0);
INSERT INTO `Genres` VALUES(89, 'R & B Star', 0);
INSERT INTO `Genres` VALUES(90, 'Jazz Star', 0);
INSERT INTO `Genres` VALUES(91, 'Klezmer', 0);
INSERT INTO `Genres` VALUES(92, 'French', 0);
INSERT INTO `Genres` VALUES(93, 'Guitar', 0);
INSERT INTO `Genres` VALUES(94, 'Opera', 0);
INSERT INTO `Genres` VALUES(95, 'Modern Star', 0);
INSERT INTO `Genres` VALUES(96, 'Brazillian', 0);
INSERT INTO `Genres` VALUES(97, 'Rockabilly', 0);
INSERT INTO `Genres` VALUES(98, 'Norway', 0);
INSERT INTO `Genres` VALUES(99, 'Caribbean', 0);
INSERT INTO `Genres` VALUES(100, 'India', 0);
INSERT INTO `Genres` VALUES(101, 'Austrailian', 0);
INSERT INTO `Genres` VALUES(102, 'Xmas Star', 0);
INSERT INTO `Genres` VALUES(103, 'Soundtrack', 0);
INSERT INTO `Genres` VALUES(104, 'Gypsy', 0);
INSERT INTO `Genres` VALUES(105, 'Senegal', 0);
INSERT INTO `Genres` VALUES(106, 'Spain', 0);
INSERT INTO `Genres` VALUES(107, 'Local', 0);
INSERT INTO `Genres` VALUES(108, 'Tibet', 0);
INSERT INTO `Genres` VALUES(109, 'Brazilian', 0);
INSERT INTO `Genres` VALUES(110, 'Iran', 0);
INSERT INTO `Genres` VALUES(111, 'Mali', 0);
INSERT INTO `Genres` VALUES(112, 'Antigua', 0);
INSERT INTO `Genres` VALUES(113, 'Bahamas', 0);
INSERT INTO `Genres` VALUES(114, 'Barbados', 0);
INSERT INTO `Genres` VALUES(115, 'Dominican Republic', 0);
INSERT INTO `Genres` VALUES(116, 'Haiti', 0);
INSERT INTO `Genres` VALUES(117, 'Jamaica (non-reggae)', 0);
INSERT INTO `Genres` VALUES(118, 'Panama', 0);
INSERT INTO `Genres` VALUES(119, 'Martinique', 0);
INSERT INTO `Genres` VALUES(120, 'Trinidad/Tobago', 0);
INSERT INTO `Genres` VALUES(121, 'Burma', 0);
INSERT INTO `Genres` VALUES(122, 'Cambodia', 0);
INSERT INTO `Genres` VALUES(123, 'China', 0);
INSERT INTO `Genres` VALUES(124, 'Indonesia', 0);
INSERT INTO `Genres` VALUES(125, 'Japan', 0);
INSERT INTO `Genres` VALUES(126, 'Korea', 0);
INSERT INTO `Genres` VALUES(127, 'Mongolia', 0);
INSERT INTO `Genres` VALUES(128, 'Philippines', 0);
INSERT INTO `Genres` VALUES(129, 'Tuva', 0);
INSERT INTO `Genres` VALUES(130, 'Vietnam', 0);
INSERT INTO `Genres` VALUES(131, 'Czech Republic', 0);
INSERT INTO `Genres` VALUES(132, 'Bulgaria', 0);
INSERT INTO `Genres` VALUES(133, 'Georgia', 0);
INSERT INTO `Genres` VALUES(134, 'Greece', 0);
INSERT INTO `Genres` VALUES(135, 'Hungary', 0);
INSERT INTO `Genres` VALUES(136, 'Latvia', 0);
INSERT INTO `Genres` VALUES(137, 'Poland', 0);
INSERT INTO `Genres` VALUES(138, 'Romania', 0);
INSERT INTO `Genres` VALUES(139, 'Russia', 0);
INSERT INTO `Genres` VALUES(140, 'Ukraine', 0);
INSERT INTO `Genres` VALUES(141, 'Pakistan', 0);
INSERT INTO `Genres` VALUES(142, 'Argentina', 0);
INSERT INTO `Genres` VALUES(143, 'Belize', 0);
INSERT INTO `Genres` VALUES(144, 'Bolivia', 0);
INSERT INTO `Genres` VALUES(145, 'Chile', 0);
INSERT INTO `Genres` VALUES(146, 'Colombia', 0);
INSERT INTO `Genres` VALUES(147, 'El Salvador', 0);
INSERT INTO `Genres` VALUES(148, 'Nicaragua', 0);
INSERT INTO `Genres` VALUES(149, 'Peru', 0);
INSERT INTO `Genres` VALUES(150, 'Venezuela', 0);
INSERT INTO `Genres` VALUES(151, 'Afghanistan', 0);
INSERT INTO `Genres` VALUES(152, 'Armenia', 0);
INSERT INTO `Genres` VALUES(153, 'Azerbaidjan', 0);
INSERT INTO `Genres` VALUES(154, 'Israel', 0);
INSERT INTO `Genres` VALUES(155, 'Syria', 0);
INSERT INTO `Genres` VALUES(156, 'Turkey', 0);
INSERT INTO `Genres` VALUES(157, 'Australia', 0);
INSERT INTO `Genres` VALUES(158, 'New Zealand', 0);
INSERT INTO `Genres` VALUES(159, 'Hawaii', 0);
INSERT INTO `Genres` VALUES(160, 'Polynesia', 0);
INSERT INTO `Genres` VALUES(161, 'Belgium', 0);
INSERT INTO `Genres` VALUES(162, 'Finland (& Samiland)', 0);
INSERT INTO `Genres` VALUES(163, 'France (& Corsica)', 0);
INSERT INTO `Genres` VALUES(164, 'Italy (& Sardinia)', 0);
INSERT INTO `Genres` VALUES(165, 'Portugal', 0);
INSERT INTO `Genres` VALUES(166, 'Spain (& Basque)', 0);
INSERT INTO `Genres` VALUES(167, 'Sweden', 0);
INSERT INTO `Genres` VALUES(168, 'Switzerland', 0);
INSERT INTO `Genres` VALUES(169, 'Cape Breton', 0);
INSERT INTO `Genres` VALUES(170, 'Instrumental', 0);
INSERT INTO `Genres` VALUES(171, 'Congo', 0);
INSERT INTO `Genres` VALUES(172, 'South Africa', 0);
INSERT INTO `Genres` VALUES(173, 'Algeria', 0);
INSERT INTO `Genres` VALUES(174, 'Ghana', 0);
INSERT INTO `Genres` VALUES(175, 'Nigeria', 0);
INSERT INTO `Genres` VALUES(176, 'Tunisia', 0);
INSERT INTO `Genres` VALUES(177, 'Egypt', 0);
INSERT INTO `Genres` VALUES(178, 'Morocco', 0);
INSERT INTO `Genres` VALUES(179, 'Ethiopia', 0);
INSERT INTO `Genres` VALUES(180, 'Somalia', 0);
INSERT INTO `Genres` VALUES(181, 'Mauritania', 0);
INSERT INTO `Genres` VALUES(182, 'Sudan', 0);
INSERT INTO `Genres` VALUES(183, 'Niger', 0);
INSERT INTO `Genres` VALUES(184, 'Ivory Coast', 0);
INSERT INTO `Genres` VALUES(185, 'Burkina Faso', 0);
INSERT INTO `Genres` VALUES(186, 'Gambia', 0);
INSERT INTO `Genres` VALUES(187, 'Iceland', 0);
INSERT INTO `Genres` VALUES(188, 'Estonia', 0);
INSERT INTO `Genres` VALUES(189, 'Denmark', 0);
INSERT INTO `Genres` VALUES(190, 'Germany', 0);
INSERT INTO `Genres` VALUES(191, 'Holland', 0);
INSERT INTO `Genres` VALUES(192, 'Malta', 0);
INSERT INTO `Genres` VALUES(193, 'Ballad', 0);
INSERT INTO `Genres` VALUES(194, 'Cameroon', 0);
INSERT INTO `Genres` VALUES(195, 'Guinea', 0);
INSERT INTO `Genres` VALUES(196, 'Benin', 0);
INSERT INTO `Genres` VALUES(197, 'Guinea Bissau', 0);
INSERT INTO `Genres` VALUES(198, 'Burundi', 0);
INSERT INTO `Genres` VALUES(199, 'Angola', 0);
INSERT INTO `Genres` VALUES(200, 'Cape Verde', 0);
INSERT INTO `Genres` VALUES(201, 'Central African Republic', 0);
INSERT INTO `Genres` VALUES(202, 'Zimbabwe', 0);
INSERT INTO `Genres` VALUES(203, 'Kenya', 0);
INSERT INTO `Genres` VALUES(204, 'Uzbekistan', 0);
INSERT INTO `Genres` VALUES(205, 'Balkan', 0);
INSERT INTO `Genres` VALUES(206, 'General', 0);
INSERT INTO `Genres` VALUES(207, 'Croatia', 0);
INSERT INTO `Genres` VALUES(208, 'Malawi', 0);
INSERT INTO `Genres` VALUES(209, 'Madagascar', 0);
INSERT INTO `Genres` VALUES(210, 'Oman', 0);
INSERT INTO `Genres` VALUES(211, 'Iraq', 0);
INSERT INTO `Genres` VALUES(212, 'Lebanon', 0);
INSERT INTO `Genres` VALUES(213, 'East European', 0);
INSERT INTO `Genres` VALUES(214, 'Nepal', 0);
INSERT INTO `Genres` VALUES(215, 'Bhutan', 0);
INSERT INTO `Genres` VALUES(216, 'Sri Lanka', 0);
INSERT INTO `Genres` VALUES(217, 'Macedonia', 0);
INSERT INTO `Genres` VALUES(218, 'Sierre Leone', 0);
INSERT INTO `Genres` VALUES(219, 'France', 0);
INSERT INTO `Genres` VALUES(220, 'Laos', 0);
INSERT INTO `Genres` VALUES(221, 'Malasia', 0);
INSERT INTO `Genres` VALUES(222, 'Taiwan', 0);
INSERT INTO `Genres` VALUES(223, 'Thailand', 0);
INSERT INTO `Genres` VALUES(224, 'Solomon Islands', 0);
INSERT INTO `Genres` VALUES(225, 'Jazz/latin', 0);
INSERT INTO `Genres` VALUES(226, 'Antilles', 0);
INSERT INTO `Genres` VALUES(227, 'Tanzania', 0);
INSERT INTO `Genres` VALUES(228, 'Africa', 0);
INSERT INTO `Genres` VALUES(229, 'Acid Jazz', 0);
INSERT INTO `Genres` VALUES(230, 'Nordic', 0);
INSERT INTO `Genres` VALUES(231, 'Arab-Jewish', 0);
INSERT INTO `Genres` VALUES(232, 'Mozambique', 0);
INSERT INTO `Genres` VALUES(233, 'Zaire', 0);
INSERT INTO `Genres` VALUES(234, 'West', 0);
INSERT INTO `Genres` VALUES(235, 'Serbia/Yugoslavia', 0);
INSERT INTO `Genres` VALUES(236, 'Central America', 0);
INSERT INTO `Genres` VALUES(237, 'Gabon', 0);
INSERT INTO `Genres` VALUES(238, 'Uganda', 0);
INSERT INTO `Genres` VALUES(239, 'North', 0);
INSERT INTO `Genres` VALUES(240, 'Lounge', 0);
INSERT INTO `Genres` VALUES(241, 'Sicily', 0);
INSERT INTO `Genres` VALUES(242, 'Hawaii/Reunion', 0);
INSERT INTO `Genres` VALUES(243, 'Nubia', 0);
INSERT INTO `Genres` VALUES(244, 'USA & Mali', 0);
INSERT INTO `Genres` VALUES(245, 'India & UK', 0);
INSERT INTO `Genres` VALUES(246, 'Canada', 0);
INSERT INTO `Genres` VALUES(247, 'Flamenco', 0);
INSERT INTO `Genres` VALUES(248, 'East', 0);
INSERT INTO `Genres` VALUES(249, 'Palestine', 0);
INSERT INTO `Genres` VALUES(250, 'Africa/Latin', 0);
INSERT INTO `Genres` VALUES(251, 'Paraguay', 0);
INSERT INTO `Genres` VALUES(252, 'England', 0);
INSERT INTO `Genres` VALUES(253, 'Afro-Cuban', 0);
INSERT INTO `Genres` VALUES(254, 'Scandinavia', 0);
INSERT INTO `Genres` VALUES(255, 'Eastern European', 0);
INSERT INTO `Genres` VALUES(256, 'Slovenia', 0);
INSERT INTO `Genres` VALUES(257, 'West Europe', 0);
INSERT INTO `Genres` VALUES(258, 'Ecuador', 0);
INSERT INTO `Genres` VALUES(259, 'Chant', 0);
INSERT INTO `Genres` VALUES(260, 'Iberian', 0);
INSERT INTO `Genres` VALUES(261, 'Tahiti', 0);
INSERT INTO `Genres` VALUES(262, 'Togo', 0);
INSERT INTO `Genres` VALUES(263, 'Austrailia', 0);
INSERT INTO `Genres` VALUES(264, 'Kazakhstan', 0);
INSERT INTO `Genres` VALUES(265, 'Kids', 0);
INSERT INTO `Genres` VALUES(266, 'Reunion', 0);
INSERT INTO `Genres` VALUES(267, 'Tajikstan', 0);
INSERT INTO `Genres` VALUES(268, 'Dominica', 0);
INSERT INTO `Genres` VALUES(269, 'Rwanda', 0);
INSERT INTO `Genres` VALUES(270, 'Taureg', 0);
INSERT INTO `Genres` VALUES(271, 'Zanzibar', 0);
INSERT INTO `Genres` VALUES(272, 'French Canadian', 0);
INSERT INTO `Genres` VALUES(273, 'Russian', 0);
INSERT INTO `Genres` VALUES(274, 'Netherlands', 0);
INSERT INTO `Genres` VALUES(275, 'Bangladesh', 0);
INSERT INTO `Genres` VALUES(276, 'Jewish', 0);
INSERT INTO `Genres` VALUES(277, 'Afrobeat', 0);
INSERT INTO `Genres` VALUES(278, 'Papua New Guinea', 0);
INSERT INTO `Genres` VALUES(279, 'Columbia', 0);
INSERT INTO `Genres` VALUES(280, 'Cyprus', 0);
INSERT INTO `Genres` VALUES(281, 'Malaysia', 0);
INSERT INTO `Genres` VALUES(282, 'Uruguay', 0);

CREATE TABLE `GenreTags` (
  `gt_GenreTagID` int(11) NOT NULL AUTO_INCREMENT,
  `gt_GenreID` int(11) NOT NULL,
  `gt_AlbumID` int(11) NOT NULL,
  PRIMARY KEY (`gt_GenreTagID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `Host` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `Active` tinyint(1) NOT NULL,
  `Internal` tinyint(1) NOT NULL,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `Host` VALUES(1, 'Test Host');

CREATE TABLE `PSACategory` (
  `Id` int(10) NOT NULL AUTO_INCREMENT,
  `Title` varchar(64) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

INSERT INTO `PSACategory` VALUES(1, 'Benefit');
INSERT INTO `PSACategory` VALUES(2, 'Arts/Film/Performance');
INSERT INTO `PSACategory` VALUES(3, 'Environment');
INSERT INTO `PSACategory` VALUES(4, 'Education');
INSERT INTO `PSACategory` VALUES(5, 'Women');
INSERT INTO `PSACategory` VALUES(6, 'Health');
INSERT INTO `PSACategory` VALUES(7, 'Animals');
INSERT INTO `PSACategory` VALUES(8, 'Workshop/Self-Help');
INSERT INTO `PSACategory` VALUES(9, 'Youth/Child');
INSERT INTO `PSACategory` VALUES(10, 'Lecture');
INSERT INTO `PSACategory` VALUES(11, 'Political');
INSERT INTO `PSACategory` VALUES(12, 'Seniors');
INSERT INTO `PSACategory` VALUES(13, 'Support');
INSERT INTO `PSACategory` VALUES(14, 'Volunteer');
INSERT INTO `PSACategory` VALUES(15, 'Public Meetings');
INSERT INTO `PSACategory` VALUES(16, 'Miscellaneous');
INSERT INTO `PSACategory` VALUES(17, 'Recreation/Dance');
INSERT INTO `PSACategory` VALUES(18, 'Special Events/Seasonal');

CREATE TABLE `Role` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

INSERT INTO `Role` VALUES(1, 'administrator');
INSERT INTO `Role` VALUES(2, 'staff');
INSERT INTO `Role` VALUES(3, 'volunteer');

CREATE TABLE `ScheduledEvent` (
  `se_Id` int(10) NOT NULL AUTO_INCREMENT,
  `se_EventId` int(10) NOT NULL,
  `se_TimeInfoId` int(10) NOT NULL,
  `se_RecordingOffset` int(2) NOT NULL DEFAULT '0' COMMENT '(in minutes)',
  PRIMARY KEY (`se_Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `ScheduledEvent` VALUES(1, 7, 1, 0);
INSERT INTO `ScheduledEvent` VALUES(2, 7, 2, 0);
INSERT INTO `ScheduledEvent` VALUES(3, 8, 3, 0);
INSERT INTO `ScheduledEvent` VALUES(4, 3, 4, 0);
INSERT INTO `ScheduledEvent` VALUES(5, 5, 5, 0);

CREATE TABLE `ScheduledEventInstance` (
  `sei_Id` int(11) NOT NULL AUTO_INCREMENT,
  `sei_DISCRIMINATOR` enum('ScheduledAlertInstance','ScheduledAnnouncementInstance','ScheduledEASTestInstance','ScheduledFeatureInstance','ScheduledLegalIdInstance','ScheduledPSAInstance','ScheduledShowInstance','ScheduledTicketGiveawayInstance','ScheduledUnderwritingInstance') NOT NULL,
  `sei_ScheduledEventId` int(10) NOT NULL,
  `sei_StartDateTime` datetime NOT NULL,
  `sei_Duration` int(5) NOT NULL,
  `sei_Executed` datetime DEFAULT NULL,
  `sei_Order` int(5) DEFAULT NULL,
  `sei_GuestName` varchar(256) DEFAULT NULL,
  `sei_Description` text,
  `sei_InternalNote` varchar(256) DEFAULT NULL,
  `sei_Copy` text,
  `sei_EventDate` datetime DEFAULT NULL,
  `sei_VenueId` int(10) DEFAULT NULL,
  `sei_WinnerName` varchar(256) DEFAULT NULL,
  `sei_WinnerPhone` varchar(20) DEFAULT NULL,
  `sei_TicketType` enum('Hard Ticket','Guest List') DEFAULT NULL,
  `sei_HostId` int(10) DEFAULT NULL,
  `sei_ShortDescription` varchar(256) DEFAULT NULL,
  `sei_LongDescription` text,
  `sei_RecordedFileName` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`sei_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `ScheduledEventException` (
  `see_Id` int(10) NOT NULL AUTO_INCREMENT,
  `see_ScheduledEventId` int(10) NOT NULL,
  `see_ExceptionDate` date NOT NULL,
  PRIMARY KEY (`see_Id`)
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `TimeInfo` VALUES(1, 'DailyRepeatingTimeInfo', '2011-01-01 09:00:00', 180, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `TimeInfo` VALUES(2, 'WeeklyRepeatingTimeInfo', '2011-01-01 12:00:00', 180, NULL, 1, 0, 1, 1, 1, 1, 1, 0, NULL);
INSERT INTO `TimeInfo` VALUES(3, 'WeeklyRepeatingTimeInfo', '2011-01-01 12:00:00', 5, NULL, 1, 0, 1, 0, 1, 0, 1, 0, NULL);
INSERT INTO `TimeInfo` VALUES(4, 'WeeklyRepeatingTimeInfo', '2011-01-01 11:00:00', 5, NULL, 1, 0, 0, 0, 0, 0, 0, 1, NULL);
INSERT INTO `TimeInfo` VALUES(5, 'DailyRepeatingTimeInfo', '2011-01-01 10:00:00', 5, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

CREATE TABLE `Tracks` (
  `t_TrackID` int(11) NOT NULL AUTO_INCREMENT,
  `t_AlbumID` int(11) NOT NULL,
  `t_Title` text NOT NULL,
  `t_TrackNumber` smallint(6) NOT NULL,
  `t_Artist` text,
  `t_DiskNumber` smallint(6),
  `t_Duration` int(11) NOT NULL,
  PRIMARY KEY (`t_TrackID`),
  FULLTEXT KEY `t_Title` (`t_Title`,`t_Artist`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `User` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Username` varchar(100) NOT NULL,
  `PasswordHash` varchar(100) NOT NULL,
  `RoleId` int(10) unsigned NOT NULL,
  `LastVisit` datetime DEFAULT NULL,
  `Shared` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

INSERT INTO `User` VALUES(1, 'root', 'cd78abd152b9c698766c4c16738bd104', 1, NULL);
INSERT INTO `User` VALUES(2, 'admin', 'c9e89b42584b5114d96343deccf4d015', 1, NULL);
INSERT INTO `User` VALUES(3, 'staff', '01aafde5b3258827567b44216220bb3e', 2, NULL);
INSERT INTO `User` VALUES(4, 'volunteer', '86f9e1c243e4a7b358130a44dad55701', 3, NULL);

CREATE TABLE `Venue` (
  `UID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `Location` text NOT NULL,
  `Active` tinyint(1) NOT NULL,
  PRIMARY KEY (`UID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `Venue` VALUES(1, 'Test Venue', '123 Fake St, Imaginary, CO');

CREATE TABLE `DBObject` (
  `o_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `o_ParentId` int(10) DEFAULT NULL,
  `o_Name` varchar(100) NOT NULL,
  PRIMARY KEY (`o_Id`),
  UNIQUE KEY `Name` (`o_Name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47 ;

INSERT INTO `DBObject` VALUES(1, NULL, 'Album');
INSERT INTO `DBObject` VALUES(2, NULL, 'Event');
INSERT INTO `DBObject` VALUES(3, 6, 'AlertEvent');
INSERT INTO `DBObject` VALUES(4, 6, 'AnnouncementEvent');
INSERT INTO `DBObject` VALUES(5, 2, 'EASTestEvent');
INSERT INTO `DBObject` VALUES(6, 2, 'EventWithCopy');
INSERT INTO `DBObject` VALUES(7, 2, 'FeatureEvent');
INSERT INTO `DBObject` VALUES(8, 6, 'LegalIdEvent');
INSERT INTO `DBObject` VALUES(9, 6, 'PSAEvent');
INSERT INTO `DBObject` VALUES(10, 2, 'ShowEvent');
INSERT INTO `DBObject` VALUES(11, 6, 'TicketGiveawayEvent');
INSERT INTO `DBObject` VALUES(12, 6, 'UnderwritingEvent');
INSERT INTO `DBObject` VALUES(13, NULL, 'FloatingShowElement');
INSERT INTO `DBObject` VALUES(14, 13, 'DJComment');
INSERT INTO `DBObject` VALUES(15, 13, 'TrackPlay');
INSERT INTO `DBObject` VALUES(16, 13, 'VoiceBreak');
INSERT INTO `DBObject` VALUES(17, 13, 'FloatingShowEvent');
INSERT INTO `DBObject` VALUES(18, NULL, 'Genre');
INSERT INTO `DBObject` VALUES(19, NULL, 'GenreTag');
INSERT INTO `DBObject` VALUES(20, NULL, 'Host');
INSERT INTO `DBObject` VALUES(21, NULL, 'PSACategory');
INSERT INTO `DBObject` VALUES(22, NULL, 'Role');
INSERT INTO `DBObject` VALUES(23, NULL, 'ScheduledEvent');
INSERT INTO `DBObject` VALUES(24, NULL, 'ScheduledEventInstance');
INSERT INTO `DBObject` VALUES(25, 34, 'ScheduledAlertInstance');
INSERT INTO `DBObject` VALUES(26, 34, 'ScheduledAnnouncementInstance');
INSERT INTO `DBObject` VALUES(27, 34, 'ScheduledEASTestInstance');
INSERT INTO `DBObject` VALUES(28, 34, 'ScheduledFeatureInstance');
INSERT INTO `DBObject` VALUES(29, 34, 'ScheduledLegalIdInstance');
INSERT INTO `DBObject` VALUES(30, 34, 'ScheduledPSAInstance');
INSERT INTO `DBObject` VALUES(31, 24, 'ScheduledShowInstance');
INSERT INTO `DBObject` VALUES(32, 34, 'ScheduledTicketGiveawayInstance');
INSERT INTO `DBObject` VALUES(33, 34, 'ScheduledUnderwritingInstance');
INSERT INTO `DBObject` VALUES(34, 24, 'ExecutableScheduledEventInstance');
INSERT INTO `DBObject` VALUES(35, NULL, 'TimeInfo');
INSERT INTO `DBObject` VALUES(36, 35, 'NonRepeatingTimeInfo');
INSERT INTO `DBObject` VALUES(37, 35, 'RepeatingTimeInfo');
INSERT INTO `DBObject` VALUES(38, 37, 'DailyRepeatingTimeInfo');
INSERT INTO `DBObject` VALUES(39, 37, 'WeeklyRepeatingTimeInfo');
INSERT INTO `DBObject` VALUES(40, 37, 'MonthlyRepeatingTimeInfo');
INSERT INTO `DBObject` VALUES(41, 37, 'YearlyRepeatingTimeInfo');
INSERT INTO `DBObject` VALUES(42, NULL, 'Track');
INSERT INTO `DBObject` VALUES(43, NULL, 'User');
INSERT INTO `DBObject` VALUES(44, NULL, 'Venue');
INSERT INTO `DBObject` VALUES(45, NULL, 'DBObject');
INSERT INTO `DBObject` VALUES(46, NULL, 'DBObjectPermission');
INSERT INTO `DBObject` VALUES(47, NULL, 'ScheduledEventException');

CREATE TABLE IF NOT EXISTS `DBObjectPermission` (
  `op_Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `op_DBObjectId` int(10) NOT NULL,
  `op_RoleId` int(10) NOT NULL,
  `op_Read` tinyint(1) NOT NULL,
  `op_Write` tinyint(1) NOT NULL,
  `op_Insert` tinyint(1) NOT NULL,
  `op_Delete` tinyint(1) NOT NULL,
  PRIMARY KEY (`op_Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=131 ;

INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(1, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(2, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(3, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(4, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(5, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(6, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(7, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(8, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(9, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(10, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(11, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(12, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(13, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(14, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(15, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(16, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(17, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(18, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(19, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(20, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(21, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(22, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(23, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(24, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(25, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(26, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(27, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(28, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(29, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(30, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(31, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(32, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(33, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(34, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(35, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(36, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(37, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(38, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(39, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(40, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(41, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(42, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(43, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(44, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(45, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(46, 1, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(47, 1, 1, 1, 1, 1);

INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(1, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(2, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(3, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(4, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(5, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(6, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(7, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(8, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(9, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(10, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(11, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(12, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(13, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(14, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(15, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(16, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(17, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(18, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(19, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(20, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(21, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(23, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(24, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(25, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(26, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(27, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(28, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(29, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(30, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(31, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(32, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(33, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(34, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(35, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(36, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(37, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(38, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(39, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(40, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(41, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(42, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(44, 2, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(47, 2, 1, 1, 1, 1);

INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(1, 3, 1, 1, 1, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(2, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(3, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(4, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(5, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(6, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(7, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(8, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(9, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(10, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(11, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(12, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(13, 3, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(14, 3, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(15, 3, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(16, 3, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(17, 3, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(18, 3, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(19, 3, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(20, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(21, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(23, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(24, 3, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(25, 3, 1, 1, 1, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(26, 3, 1, 1, 1, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(27, 3, 1, 1, 1, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(28, 3, 1, 1, 1, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(29, 3, 1, 1, 1, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(30, 3, 1, 1, 1, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(31, 3, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(32, 3, 1, 1, 1, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(33, 3, 1, 1, 1, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(34, 3, 1, 1, 1, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(35, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(36, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(37, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(38, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(39, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(40, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(41, 3, 1, 0, 0, 0);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(42, 3, 1, 1, 1, 1);
INSERT INTO `DBObjectPermission` (`op_DBObjectId`, `op_RoleId`, `op_Read`, `op_Write`, `op_Insert`, `op_Delete`) VALUES(44, 3, 1, 0, 0, 0);
