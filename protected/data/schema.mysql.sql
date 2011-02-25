-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 25, 2011 at 10:21 AM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `openeyes`
--

-- --------------------------------------------------------

--
-- Table structure for table `authassignment`
--

CREATE TABLE IF NOT EXISTS `authassignment` (
  `itemname` varchar(64) COLLATE utf8_bin NOT NULL,
  `userid` varchar(64) COLLATE utf8_bin NOT NULL,
  `bizrule` text COLLATE utf8_bin,
  `data` text COLLATE utf8_bin,
  PRIMARY KEY (`itemname`,`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `authassignment`
--

INSERT INTO `authassignment` (`itemname`, `userid`, `bizrule`, `data`) VALUES
('admin', '1', NULL, 'N;'),
('admin', '23', NULL, 'N;');

-- --------------------------------------------------------

--
-- Table structure for table `authitem`
--

CREATE TABLE IF NOT EXISTS `authitem` (
  `name` varchar(64) COLLATE utf8_bin NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_bin,
  `bizrule` text COLLATE utf8_bin,
  `data` text COLLATE utf8_bin,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `authitem`
--

INSERT INTO `authitem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES
('Rbac', 0, 'Rbac', NULL, 'N;'),
('admin', 2, '', NULL, 'N;'),
('create User', 0, 'create User', NULL, 'N;'),
('delete User', 0, 'delete User', NULL, 'N;'),
('update User', 0, 'update User', NULL, 'N;'),
('view User', 0, 'view User', NULL, 'N;');

-- --------------------------------------------------------

--
-- Table structure for table `authitemchild`
--

CREATE TABLE IF NOT EXISTS `authitemchild` (
  `parent` varchar(64) COLLATE utf8_bin NOT NULL,
  `child` varchar(64) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `authitemchild`
--

INSERT INTO `authitemchild` (`parent`, `child`) VALUES
('admin', 'Rbac'),
('admin', 'create User'),
('admin', 'delete User'),
('admin', 'update User'),
('admin', 'view User');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE IF NOT EXISTS `contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nick_name` varchar(80) COLLATE utf8_bin DEFAULT NULL,
  `consultant` tinyint(1) NOT NULL DEFAULT '0',
  `contact_type_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_type_id` (`contact_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `nick_name`, `consultant`, `contact_type_id`) VALUES
(1, 'test contact 1', 0, 1),
(2, 'test contact 2', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `contact_type`
--

CREATE TABLE IF NOT EXISTS `contact_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8_bin NOT NULL,
  `macro_only` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=9 ;

--
-- Dumping data for table `contact_type`
--

INSERT INTO `contact_type` (`id`, `name`, `macro_only`) VALUES
(1, 'GP', 0),
(2, 'Ophthalmologist', 0),
(3, 'Optometrist', 0),
(4, 'Specialist', 0),
(5, 'Social Worker', 0),
(6, 'Health Visitor', 0),
(7, 'Solicitor', 0),
(8, 'Patient', 1);

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` char(2) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=249 ;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `code`, `name`) VALUES
(1, 'GB', 'United Kingdom'),
(2, 'AF', 'Afghanistan'),
(3, 'AX', 'Aland Islands'),
(4, 'AL', 'Albania'),
(5, 'DZ', 'Algeria'),
(6, 'AS', 'American Samoa'),
(7, 'AD', 'Andorra'),
(8, 'AO', 'Angola'),
(9, 'AI', 'Anguilla'),
(10, 'AQ', 'Antarctica'),
(11, 'AG', 'Antigua and Barbuda'),
(12, 'AR', 'Argentina'),
(13, 'AM', 'Armenia'),
(14, 'AW', 'Aruba'),
(15, 'AU', 'Australia'),
(16, 'AT', 'Austria'),
(17, 'AZ', 'Azerbaijan'),
(18, 'BS', 'Bahamas'),
(19, 'BH', 'Bahrain'),
(20, 'BD', 'Bangladesh'),
(21, 'BB', 'Barbados'),
(22, 'BY', 'Belarus'),
(23, 'BE', 'Belgium'),
(24, 'BZ', 'Belize'),
(25, 'BJ', 'Benin'),
(26, 'BM', 'Bermuda'),
(27, 'BT', 'Bhutan'),
(28, 'BO', 'Bolivia, Plurinational State of'),
(29, 'BQ', 'Bonaire, Saint Eustatius and Saba'),
(30, 'BA', 'Bosnia and Herzegovina'),
(31, 'BW', 'Botswana'),
(32, 'BV', 'Bouvet Island'),
(33, 'BR', 'Brazil'),
(34, 'IO', 'British Indian Ocean Territory'),
(35, 'BN', 'Brunei Darussalam'),
(36, 'BG', 'Bulgaria'),
(37, 'BF', 'Burkina Faso'),
(38, 'BI', 'Burundi'),
(39, 'KH', 'Cambodia'),
(40, 'CM', 'Cameroon'),
(41, 'CA', 'Canada'),
(42, 'CV', 'Cape Verde'),
(43, 'KY', 'Cayman Islands'),
(44, 'CF', 'Central African Republic'),
(45, 'TD', 'Chad'),
(46, 'CL', 'Chile'),
(47, 'CN', 'China'),
(48, 'CX', 'Christmas Island'),
(49, 'CC', 'Cocos (Keeling) Islands'),
(50, 'CO', 'Colombia'),
(51, 'KM', 'Comoros'),
(52, 'CG', 'Congo'),
(53, 'CD', 'Congo, The Democratic Republic of the'),
(54, 'CK', 'Cook Islands'),
(55, 'CR', 'Costa Rica'),
(56, 'CI', 'Cote D''ivoire'),
(57, 'HR', 'Croatia'),
(58, 'CU', 'Cuba'),
(59, 'CW', 'Curacao'),
(60, 'CY', 'Cyprus'),
(61, 'CZ', 'Czech Republic'),
(62, 'DK', 'Denmark'),
(63, 'DJ', 'Djibouti'),
(64, 'DM', 'Dominica'),
(65, 'DO', 'Dominican Republic'),
(66, 'EC', 'Ecuador'),
(67, 'EG', 'Egypt'),
(68, 'SV', 'El Salvador'),
(69, 'GQ', 'Equatorial Guinea'),
(70, 'ER', 'Eritrea'),
(71, 'EE', 'Estonia'),
(72, 'ET', 'Ethiopia'),
(73, 'FK', 'Falkland Islands (Malvinas)'),
(74, 'FO', 'Faroe Islands'),
(75, 'FJ', 'Fiji'),
(76, 'FI', 'Finland'),
(77, 'FR', 'France'),
(78, 'GF', 'French Guiana'),
(79, 'PF', 'French Polynesia'),
(80, 'TF', 'French Southern Territories'),
(81, 'GA', 'Gabon'),
(82, 'GM', 'Gambia'),
(83, 'GE', 'Georgia'),
(84, 'DE', 'Germany'),
(85, 'GH', 'Ghana'),
(86, 'GI', 'Gibraltar'),
(87, 'GR', 'Greece'),
(88, 'GL', 'Greenland'),
(89, 'GD', 'Grenada'),
(90, 'GP', 'Guadeloupe'),
(91, 'GU', 'Guam'),
(92, 'GT', 'Guatemala'),
(93, 'GG', 'Guernsey'),
(94, 'GN', 'Guinea'),
(95, 'GW', 'Guinea-Bissau'),
(96, 'GY', 'Guyana'),
(97, 'HT', 'Haiti'),
(98, 'HM', 'Heard Island and McDonald Islands'),
(99, 'VA', 'Holy See (Vatican City State)'),
(100, 'HN', 'Honduras'),
(101, 'HK', 'Hong Kong'),
(102, 'HU', 'Hungary'),
(103, 'IS', 'Iceland'),
(104, 'IN', 'India'),
(105, 'ID', 'Indonesia'),
(106, 'IR', 'Iran, Islamic Republic of'),
(107, 'IQ', 'Iraq'),
(108, 'IE', 'Ireland'),
(109, 'IM', 'Isle of Man'),
(110, 'IL', 'Israel'),
(111, 'IT', 'Italy'),
(112, 'JM', 'Jamaica'),
(113, 'JP', 'Japan'),
(114, 'JE', 'Jersey'),
(115, 'JO', 'Jordan'),
(116, 'KZ', 'Kazakhstan'),
(117, 'KE', 'Kenya'),
(118, 'KI', 'Kiribati'),
(119, 'KP', 'Korea, Democratic People''s Republic of'),
(120, 'KR', 'Korea, Republic of'),
(121, 'KW', 'Kuwait'),
(122, 'KG', 'Kyrgyzstan'),
(123, 'LA', 'Lao People''s Democratic Republic'),
(124, 'LV', 'Latvia'),
(125, 'LB', 'Lebanon'),
(126, 'LS', 'Lesotho'),
(127, 'LR', 'Liberia'),
(128, 'LY', 'Libyan Arab Jamahiriya'),
(129, 'LI', 'Liechtenstein'),
(130, 'LT', 'Lithuania'),
(131, 'LU', 'Luxembourg'),
(132, 'MO', 'Macao'),
(133, 'MK', 'Macedonia, The Former Yugoslav Republic of'),
(134, 'MG', 'Madagascar'),
(135, 'MW', 'Malawi'),
(136, 'MY', 'Malaysia'),
(137, 'MV', 'Maldives'),
(138, 'ML', 'Mali'),
(139, 'MT', 'Malta'),
(140, 'MH', 'Marshall Islands'),
(141, 'MQ', 'Martinique'),
(142, 'MR', 'Mauritania'),
(143, 'MU', 'Mauritius'),
(144, 'YT', 'Mayotte'),
(145, 'MX', 'Mexico'),
(146, 'FM', 'Micronesia, Federated States of'),
(147, 'MD', 'Moldova, Republic of'),
(148, 'MC', 'Monaco'),
(149, 'MN', 'Mongolia'),
(150, 'ME', 'Montenegro'),
(151, 'MS', 'Montserrat'),
(152, 'MA', 'Morocco'),
(153, 'MZ', 'Mozambique'),
(154, 'MM', 'Myanmar'),
(155, 'NA', 'Namibia'),
(156, 'NR', 'Nauru'),
(157, 'NP', 'Nepal'),
(158, 'NL', 'Netherlands'),
(159, 'NC', 'New Caledonia'),
(160, 'NZ', 'New Zealand'),
(161, 'NI', 'Nicaragua'),
(162, 'NE', 'Niger'),
(163, 'NG', 'Nigeria'),
(164, 'NU', 'Niue'),
(165, 'NF', 'Norfolk Island'),
(166, 'MP', 'Northern Mariana Islands'),
(167, 'NO', 'Norway'),
(168, 'OM', 'Oman'),
(169, 'PK', 'Pakistan'),
(170, 'PW', 'Palau'),
(171, 'PS', 'Palestinian Territory, Occupied'),
(172, 'PA', 'Panama'),
(173, 'PG', 'Papua New Guinea'),
(174, 'PY', 'Paraguay'),
(175, 'PE', 'Peru'),
(176, 'PH', 'Philippines'),
(177, 'PN', 'Pitcairn'),
(178, 'PL', 'Poland'),
(179, 'PT', 'Portugal'),
(180, 'PR', 'Puerto Rico'),
(181, 'QA', 'Qatar'),
(182, 'RE', 'Reunion'),
(183, 'RO', 'Romania'),
(184, 'RU', 'Russian Federation'),
(185, 'RW', 'Rwanda'),
(186, 'BL', 'Saint Barthelemy'),
(187, 'SH', 'Saint Helena, Ascension and Tristan Da Cunha'),
(188, 'KN', 'Saint Kitts and Nevis'),
(189, 'LC', 'Saint Lucia'),
(190, 'MF', 'Saint Martin (French Part)'),
(191, 'PM', 'Saint Pierre and Miquelon'),
(192, 'VC', 'Saint Vincent and the Grenadines'),
(193, 'WS', 'Samoa'),
(194, 'SM', 'San Marino'),
(195, 'ST', 'Sao Tome and Principe'),
(196, 'SA', 'Saudi Arabia'),
(197, 'SN', 'Senegal'),
(198, 'RS', 'Serbia'),
(199, 'SC', 'Seychelles'),
(200, 'SL', 'Sierra Leone'),
(201, 'SG', 'Singapore'),
(202, 'SX', 'Sint Maarten (Dutch Part)'),
(203, 'SK', 'Slovakia'),
(204, 'SI', 'Slovenia'),
(205, 'SB', 'Solomon Islands'),
(206, 'SO', 'Somalia'),
(207, 'ZA', 'South Africa'),
(208, 'GS', 'South Georgia and the South Sandwich Islands'),
(209, 'ES', 'Spain'),
(210, 'LK', 'Sri Lanka'),
(211, 'SD', 'Sudan'),
(212, 'SR', 'Suriname'),
(213, 'SJ', 'Svalbard and Jan Mayen'),
(214, 'SZ', 'Swaziland'),
(215, 'SE', 'Sweden'),
(216, 'CH', 'Switzerland'),
(217, 'SY', 'Syrian Arab Republic'),
(218, 'TW', 'Taiwan, Province of China'),
(219, 'TJ', 'Tajikistan'),
(220, 'TZ', 'Tanzania, United Republic of'),
(221, 'TH', 'Thailand'),
(222, 'TL', 'Timor-Leste'),
(223, 'TG', 'Togo'),
(224, 'TK', 'Tokelau'),
(225, 'TO', 'Tonga'),
(226, 'TT', 'Trinidad and Tobago'),
(227, 'TN', 'Tunisia'),
(228, 'TR', 'Turkey'),
(229, 'TM', 'Turkmenistan'),
(230, 'TC', 'Turks and Caicos Islands'),
(231, 'TV', 'Tuvalu'),
(232, 'UG', 'Uganda'),
(233, 'UA', 'Ukraine'),
(234, 'AE', 'United Arab Emirates'),
(235, 'US', 'United States'),
(236, 'UM', 'United States Minor Outlying Islands'),
(237, 'UY', 'Uruguay'),
(238, 'UZ', 'Uzbekistan'),
(239, 'VU', 'Vanuatu'),
(240, 'VE', 'Venezuela, Bolivarian Republic of'),
(241, 'VN', 'Viet Nam'),
(242, 'VG', 'Virgin Islands, British'),
(243, 'VI', 'Virgin Islands, U.S.'),
(244, 'WF', 'Wallis and Futuna'),
(245, 'EH', 'Western Sahara'),
(246, 'YE', 'Yemen'),
(247, 'ZM', 'Zambia'),
(248, 'ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `element_history`
--

CREATE TABLE IF NOT EXISTS `element_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `description` text COLLATE utf8_bin,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;


--
-- Table structure for table `element_type`
--

CREATE TABLE IF NOT EXISTS `element_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `class_name` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=17 ;

--
-- Dumping data for table `element_type`
--

INSERT INTO `element_type` (`id`, `name`, `class_name`) VALUES
(1, 'History', 'ElementHistory'),
(2, 'Past History', 'ElementPastHistory'),
(3, 'Visual function', 'ElementVisualFunction'),
(4, 'Visual acuity', 'ElementVisualAcuity'),
(5, 'Mini-refraction', 'ElementMiniRefraction'),
(6, 'Visual fields', 'ElementVisualFields'),
(7, 'Extraocular movements', 'ElementExtraocularMovements'),
(8, 'Cranial nervers', 'ElementCranialNervers'),
(9, 'Orbital examination', 'ElementOrbitalExamination'),
(10, 'Anterior segment', 'ElementAnteriorSegment'),
(11, 'Anterior segment drawing', 'ElementAnteriorSegmentDrawing'),
(12, 'Gonioscopy', 'ElementGonioscopy'),
(13, 'intraocular pressure', 'ElementIntraocularPressure'),
(14, 'Posterior segment', 'ElementPosteriorSegment'),
(15, 'Posterior segment drawing', 'ElementPosteriorSegmentDrawing'),
(16, 'Conclusion', 'ElementConclusion');

-- --------------------------------------------------------

--
-- Table structure for table `episode`
--

CREATE TABLE IF NOT EXISTS `episode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(10) unsigned NOT NULL,
  `firm_id` int(10) unsigned DEFAULT NULL,
  `startdate` datetime NOT NULL,
  `enddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `episode_1` (`patient_id`),
  KEY `episode_2` (`firm_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE IF NOT EXISTS `event` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `episode_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `event_type_id` int(10) unsigned NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_1` (`episode_id`),
  KEY `event_2` (`user_id`),
  KEY `event_3` (`event_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `event_type`
--

CREATE TABLE IF NOT EXISTS `event_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=24 ;

--
-- Dumping data for table `event_type`
--

INSERT INTO `event_type` (`id`, `name`) VALUES
(17, 'amdapplication'),
(18, 'amdinjection'),
(16, 'anaesth'),
(13, 'bloodtest'),
(11, 'ctscan'),
(23, 'cvi'),
(4, 'diagnosis'),
(1, 'examination'),
(5, 'ffa'),
(8, 'field'),
(6, 'icg'),
(19, 'injection'),
(20, 'laser'),
(21, 'letterin'),
(22, 'letterout'),
(12, 'mriscan'),
(7, 'oct'),
(3, 'orthoptics'),
(15, 'preassess'),
(14, 'prescription'),
(2, 'refraction'),
(9, 'ultrasound'),
(10, 'xray');

-- --------------------------------------------------------

--
-- Table structure for table `exam_phrase`
--

CREATE TABLE IF NOT EXISTS `exam_phrase` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `specialty_id` int(10) unsigned NOT NULL,
  `part` int(10) DEFAULT '0',
  `phrase` varchar(80) COLLATE utf8_bin NOT NULL,
  `order` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `specialty_id` (`specialty_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Dumping data for table `exam_phrase`
--

INSERT INTO `exam_phrase` (`id`, `specialty_id`, `part`, `phrase`, `order`) VALUES
(1, 12, 4, '1234', 4);

-- --------------------------------------------------------

--
-- Table structure for table `firm`
--

CREATE TABLE IF NOT EXISTS `firm` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `service_specialty_assignment_id` int(10) unsigned NOT NULL,
  `pas_code` char(4) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(40) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_specialty_assignment_id` (`service_specialty_assignment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=12 ;

--
-- Dumping data for table `firm`
--

INSERT INTO `firm` (`id`, `service_specialty_assignment_id`, `pas_code`, `name`) VALUES
(7, 3, 'AEAB', 'Aylward Firm'),
(8, 4, 'ADCR', 'Collin Firm'),
(9, 5, 'CADB', 'Bessant Firm'),
(10, 6, 'EXAB', 'Allan Firm');

-- --------------------------------------------------------

--
-- Table structure for table `firm_user_assignment`
--

CREATE TABLE IF NOT EXISTS `firm_user_assignment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firm_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `firm_id` (`firm_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `firm_user_assignment`
--

INSERT INTO `firm_user_assignment` (`id`, `firm_id`, `user_id`) VALUES
(2, 7, 1),
(3, 8, 1),
(4, 9, 1);

-- --------------------------------------------------------

--
-- Table structure for table `letter_phrase`
--

CREATE TABLE IF NOT EXISTS `letter_phrase` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firm_id` int(10) unsigned NOT NULL,
  `name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `phrase` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `order` int(10) unsigned DEFAULT '0',
  `section` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `firm_id` (`firm_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Dumping data for table `letter_phrase`
--

INSERT INTO `letter_phrase` (`id`, `firm_id`, `name`, `phrase`, `order`, `section`) VALUES
(1, 9, 'test', 'test', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `letter_template`
--

CREATE TABLE IF NOT EXISTS `letter_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `specialty_id` int(10) unsigned NOT NULL,
  `name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `contact_type_id` int(10) unsigned NOT NULL,
  `text` text COLLATE utf8_bin,
  `cc` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `specialty_id` (`specialty_id`),
  KEY `contact_type_id` (`contact_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `letter_template`
--

INSERT INTO `letter_template` (`id`, `specialty_id`, `name`, `contact_type_id`, `text`, `cc`) VALUES
(1, 9, '1233', 7, '1233', '2344'),
(2, 2, 'asdf', 4, 'asdfasd', 'sdfgsdf');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE IF NOT EXISTS `patient` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pas_key` int(10) unsigned DEFAULT NULL,
  `title` varchar(8) COLLATE utf8_bin DEFAULT NULL,
  `first_name` varchar(40) COLLATE utf8_bin NOT NULL,
  `last_name` varchar(40) COLLATE utf8_bin NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` char(1) COLLATE utf8_bin DEFAULT NULL,
  `hos_num` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `nhs_num` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `address1` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `address2` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `city` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `postcode` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `country` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `telephone` varchar(24) COLLATE utf8_bin DEFAULT NULL,
  `mobile` varchar(24) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(60) COLLATE utf8_bin DEFAULT NULL,
  `comments` tinytext COLLATE utf8_bin,
  `pmh` text COLLATE utf8_bin,
  `poh` text COLLATE utf8_bin,
  `drugs` text COLLATE utf8_bin,
  `allergies` text COLLATE utf8_bin,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`id`, `pas_key`, `title`, `first_name`, `last_name`, `dob`, `gender`, `hos_num`, `nhs_num`, `address1`, `address2`, `city`, `postcode`, `country`, `telephone`, `mobile`, `email`, `comments`, `pmh`, `poh`, `drugs`, `allergies`) VALUES
(1, 123, 'Mr.', 'John', 'Smith', '1970-01-01', 'M', '12345', '54321', 'Flat 1A', '23 Main St', 'London', 'N1 1AB', 'UK', '02071 234567', '07012 345678', 'jsmith@gmail.com', '', '', '', '', ''),
(2, 456, 'Mr.', 'John', 'Jones', '1972-01-01', 'M', '23456', '65432', 'Flat 2B', '23 Center St', 'London', 'EC1 1AB', 'UK', '02072 345678', '07023 456789', 'jones@gmail.com', '', '', '', '', ''),
(3, 789, 'Mrs.', 'Katherine', 'Smith', '1960-01-01', 'F', '34567', '76543', 'Flat 3', '23 Southern St', 'London', 'SW1 1AB', 'UK', '02073 456789', '07013 456789', 'ksmith@gmail.com', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `possible_element_type`
--

CREATE TABLE IF NOT EXISTS `possible_element_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_type_id` int(10) unsigned NOT NULL,
  `element_type_id` int(10) unsigned NOT NULL,
  `view_number` int(10) unsigned NOT NULL DEFAULT '1',
  `order` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_type_id` (`event_type_id`),
  KEY `element_type_id` (`element_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `possible_element_type`
--

INSERT INTO `possible_element_type` (`id`, `event_type_id`, `element_type_id`, `view_number`, `order`) VALUES
(1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE IF NOT EXISTS `service` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=12 ;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`id`, `name`) VALUES
(1, 'Accident and Emergency Service'),
(2, 'Adnexal Service'),
(3, 'Anaesthetic Service'),
(4, 'Cataract Service'),
(5, 'Corneal Service'),
(6, 'Glaucoma Service'),
(7, 'Medical Retina Service'),
(8, 'Paediatric Service'),
(9, 'Refractive Service'),
(10, 'Strabismus Service'),
(11, 'Vitreoretinal Service');

-- --------------------------------------------------------

--
-- Table structure for table `service_specialty_assignment`
--

CREATE TABLE IF NOT EXISTS `service_specialty_assignment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` int(10) unsigned NOT NULL,
  `specialty_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `specialty_id` (`specialty_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `service_specialty_assignment`
--

INSERT INTO `service_specialty_assignment` (`id`, `service_id`, `specialty_id`) VALUES
(3, 1, 1),
(4, 2, 2),
(5, 4, 4),
(6, 5, 5);

-- --------------------------------------------------------

--
-- Table structure for table `site_element_type`
--

CREATE TABLE IF NOT EXISTS `site_element_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `possible_element_type_id` int(10) unsigned NOT NULL,
  `specialty_id` int(10) unsigned NOT NULL,
  `view_number` int(10) unsigned NOT NULL,
  `default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `first_in_episode` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `possible_element_type_id` (`possible_element_type_id`),
  KEY `specialty_id` (`specialty_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `site_element_type`
--

INSERT INTO `site_element_type` (`id`, `possible_element_type_id`, `specialty_id`, `view_number`, `default`, `first_in_episode`) VALUES
(1, 1, 1, 2, 1, 1),
(2, 1, 2, 1, 1, 1),
(3, 1, 3, 1, 1, 1),
(4, 1, 4, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `specialty`
--

CREATE TABLE IF NOT EXISTS `specialty` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8_bin NOT NULL,
  `class_name` varchar(40) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=17 ;

--
-- Dumping data for table `specialty`
--

INSERT INTO `specialty` (`id`, `name`, `class_name`) VALUES
(1, 'Accident & Emergency', ''),
(2, 'Adnexal', 'Adnexal'),
(3, 'Anaesthetics', ''),
(4, 'Cataract', ''),
(5, 'Cornea', ''),
(6, 'External', ''),
(7, 'Glaucoma', ''),
(8, 'Medical Retina', ''),
(9, 'Neuroophthalmology', ''),
(10, 'Oncology', ''),
(11, 'Paediatrics', ''),
(12, 'Primary Care', ''),
(13, 'Refractive', ''),
(14, 'Strabismus', ''),
(15, 'Uveitis', ''),
(16, 'Vitreoretinal', '');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(40) COLLATE utf8_bin NOT NULL,
  `first_name` varchar(40) COLLATE utf8_bin NOT NULL,
  `last_name` varchar(40) COLLATE utf8_bin NOT NULL,
  `email` varchar(80) COLLATE utf8_bin NOT NULL,
  `active` tinyint(1) NOT NULL,
  `password` varchar(40) COLLATE utf8_bin NOT NULL,
  `salt` varchar(10) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `first_name`, `last_name`, `email`, `active`, `password`, `salt`) VALUES
(1, 'admin', 'admin', 'admin', 'admin@admin.com', 1, 'd45409ef1eaa57f5041bf3a1b510097b', 'FbYJis0YG3'),
(2, 'joebloggs', 'Joe', 'Bloggs', 'joebloggs@openeyes.org.uk', 1, 'd45409ef1eaa57f5041bf3a1b510097b', 'FbYJis0YG3');

-- --------------------------------------------------------

--
-- Table structure for table `user_contact_assignment`
--

CREATE TABLE IF NOT EXISTS `user_contact_assignment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `contact_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `user_contact_assignment`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `contact`
--
ALTER TABLE `contact`
  ADD CONSTRAINT `contact_ibfk_1` FOREIGN KEY (`contact_type_id`) REFERENCES `contact_type` (`id`);

--
-- Constraints for table `element_history`
--
ALTER TABLE `element_history`
  ADD CONSTRAINT `element_history_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`);

--
-- Constraints for table `episode`
--
ALTER TABLE `episode`
  ADD CONSTRAINT `episode_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  ADD CONSTRAINT `episode_2` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`);

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_1` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`),
  ADD CONSTRAINT `event_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `event_3` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`id`);

--
-- Constraints for table `exam_phrase`
--
ALTER TABLE `exam_phrase`
  ADD CONSTRAINT `exam_phrase_ibfk_1` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`);

--
-- Constraints for table `firm`
--
ALTER TABLE `firm`
  ADD CONSTRAINT `firm_ibfk_1` FOREIGN KEY (`service_specialty_assignment_id`) REFERENCES `service_specialty_assignment` (`id`);

--
-- Constraints for table `firm_user_assignment`
--
ALTER TABLE `firm_user_assignment`
  ADD CONSTRAINT `firm_user_assignment_ibfk_1` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
  ADD CONSTRAINT `firm_user_assignment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `letter_phrase`
--
ALTER TABLE `letter_phrase`
  ADD CONSTRAINT `letter_phrase_ibfk_1` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`);

--
-- Constraints for table `letter_template`
--
ALTER TABLE `letter_template`
  ADD CONSTRAINT `letter_template_ibfk_1` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`),
  ADD CONSTRAINT `letter_template_ibfk_2` FOREIGN KEY (`contact_type_id`) REFERENCES `contact_type` (`id`);

--
-- Constraints for table `possible_element_type`
--
ALTER TABLE `possible_element_type`
  ADD CONSTRAINT `possible_element_type_ibfk_1` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`id`),
  ADD CONSTRAINT `possible_element_type_ibfk_2` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`);

--
-- Constraints for table `service_specialty_assignment`
--
ALTER TABLE `service_specialty_assignment`
  ADD CONSTRAINT `service_specialty_assignment_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`),
  ADD CONSTRAINT `service_specialty_assignment_ibfk_2` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`);

--
-- Constraints for table `site_element_type`
--
ALTER TABLE `site_element_type`
  ADD CONSTRAINT `site_element_type_ibfk_1` FOREIGN KEY (`possible_element_type_id`) REFERENCES `possible_element_type` (`id`),
  ADD CONSTRAINT `site_element_type_ibfk_2` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`);

--
-- Constraints for table `user_contact_assignment`
--
ALTER TABLE `user_contact_assignment`
  ADD CONSTRAINT `user_contact_assignment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `user_contact_assignment_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`);
