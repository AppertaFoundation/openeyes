-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 28, 2011 at 03:08 PM
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
('admin', '1', NULL, 'N;');

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
('admin', 2, '', NULL, 'N;');

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
('admin', 'Rbac');

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
-- Table structure for table `diagnosis`
--

CREATE TABLE IF NOT EXISTS `diagnosis` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `disorder_id` int(10) unsigned NOT NULL,
  `datetime` datetime NOT NULL,
  `site` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `user_id` (`user_id`),
  KEY `disorder_id` (`disorder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Dumping data for table `diagnosis`
--


-- --------------------------------------------------------

--
-- Table structure for table `disorder`
--

CREATE TABLE IF NOT EXISTS `disorder` (
  `id` int(10) unsigned NOT NULL,
  `fully_specified_name` char(255) CHARACTER SET latin1 NOT NULL,
  `term` char(255) CHARACTER SET latin1 NOT NULL,
  `systemic` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `term` (`term`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `disorder`
--

-- --------------------------------------------------------


--
-- Table structure for table `element_visual_function`
--

CREATE TABLE IF NOT EXISTS `element_visual_function` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Table structure for table `element_visual_acuity`
--

CREATE TABLE IF NOT EXISTS `element_visual_acuity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Table structure for table `element_mini_refraction`
--

CREATE TABLE IF NOT EXISTS `element_mini_refraction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Table structure for table `element_visual_fields`
--

CREATE TABLE IF NOT EXISTS `element_visual_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Table structure for table `element_extraocular_movements`
--

CREATE TABLE IF NOT EXISTS `element_extraocular_movements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Table structure for table `element_cranial_nerves`
--

CREATE TABLE IF NOT EXISTS `element_cranial_nerves` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Table structure for table `element_orbital_examination`
--

CREATE TABLE IF NOT EXISTS `element_orbital_examination` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Table structure for table `element_anterior_segment`
--

CREATE TABLE IF NOT EXISTS `element_anterior_segment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Table structure for table `element_anterior_segment_drawing`
--

CREATE TABLE IF NOT EXISTS `element_anterior_segment_drawing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Table structure for table `element_gonioscopy`
--

CREATE TABLE IF NOT EXISTS `element_gonioscopy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Table structure for table `element_intraocular_pressure`
--

CREATE TABLE IF NOT EXISTS `element_intraocular_pressure` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Table structure for table `element_posterior_segment`
--

CREATE TABLE IF NOT EXISTS `element_posterior_segment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Table structure for table `element_posterior_segment_drawing`
--

CREATE TABLE IF NOT EXISTS `element_posterior_segment_drawing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Table structure for table `element_conclusion`
--

CREATE TABLE IF NOT EXISTS `element_conclusion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Table structure for table `element_past_history`
--

CREATE TABLE IF NOT EXISTS `element_past_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;


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
-- Dumping data for table `element_history`
--


-- --------------------------------------------------------

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
(2, 'Past history', 'ElementPastHistory'),
(3, 'Visual function', 'ElementVisualFunction'),
(4, 'Visual acuity', 'ElementVisualAcuity'),
(5, 'Mini-refraction', 'ElementMiniRefraction'),
(6, 'Visual fields', 'ElementVisualFields'),
(7, 'Extraocular movements', 'ElementExtraocularMovements'),
(8, 'Cranial nervers', 'ElementCranialNerves'),
(9, 'Orbital examination', 'ElementOrbitalExamination'),
(10, 'Anterior segment', 'ElementAnteriorSegment'),
(11, 'Anterior segment drawing', 'ElementAnteriorSegmentDrawing'),
(12, 'Gonioscopy', 'ElementGonioscopy'),
(13, 'Intraocular pressure', 'ElementIntraocularPressure'),
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

--
-- Dumping data for table `episode`
--


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

--
-- Dumping data for table `event`
--

-- --------------------------------------------------------

--
-- Table structure for table `event_type`
--

CREATE TABLE IF NOT EXISTS `event_type` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(40) COLLATE utf8_bin NOT NULL,
	`first_in_episode_possible` tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=24 ;

--
-- Dumping data for table `event_type`
--

INSERT INTO `event_type` (`id`, `name`, `first_in_episode_possible`) VALUES
(17, 'amdapplication', 0),
(18, 'amdinjection', 0),
(16, 'anaesth', 0),
(13, 'bloodtest', 0),
(11, 'ctscan', 0),
(23, 'cvi', 0),
(4, 'diagnosis', 0),
(1, 'examination', 1),
(5, 'ffa', 0),
(8, 'field', 0),
(6, 'icg', 0),
(19, 'injection', 0),
(20, 'laser', 0),
(21, 'letterin', 0),
(22, 'letterout', 0),
(12, 'mriscan', 0),
(7, 'oct', 0),
(3, 'orthoptics', 0),
(15, 'preassess', 0),
(14, 'prescription', 0),
(2, 'refraction', 0),
(9, 'ultrasound', 0),
(10, 'xray', 0);

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
(1, 3, 'AEAB', 'Aylward Firm'),
(2, 4, 'ADCR', 'Collin Firm'),
(3, 5, 'CADB', 'Bessant Firm'),
(4, 6, 'EXAB', 'Allan Firm');

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
(1, 1, 1),
(2, 2, 1),
(3, 3, 1);

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
  `num_views` int(10) unsigned NOT NULL DEFAULT '1',
  `order` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_type_id` (`event_type_id`),
  KEY `element_type_id` (`element_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `possible_element_type`
--

INSERT INTO `possible_element_type` (`id`, `event_type_id`, `element_type_id`, `num_views`, `order`) VALUES
(1, 1, 1, 1, 1),
(2, 1, 2, 1, 2),
(3, 1, 3, 1, 3),
(4, 1, 4, 1, 4),
(5, 1, 5, 1, 5),
(6, 1, 6, 1, 6),
(7, 1, 7, 1, 7),
(8, 1, 8, 1, 8),
(9, 1, 9, 1, 9),
(10, 1, 10, 1, 10),
(11, 1, 11, 1, 11),
(12, 1, 12, 1, 12),
(13, 1, 13, 1, 13),
(14, 1, 14, 1, 14),
(15, 1, 15, 1, 15),
(16, 1, 16, 1, 16);

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
  `required` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `first_in_episode` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `possible_element_type_id` (`possible_element_type_id`),
  KEY `specialty_id` (`specialty_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `site_element_type`
--


-- --------------------------------------------------------

--
-- Table structure for table `specialty`
--

CREATE TABLE IF NOT EXISTS `specialty` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=17 ;

--
-- Dumping data for table `specialty`
--

INSERT INTO `specialty` (`id`, `name`) VALUES
(1, 'Accident & Emergency'),
(2, 'Adnexal'),
(3, 'Anaesthetics'),
(4, 'Cataract'),
(5, 'Cornea'),
(6, 'External'),
(7, 'Glaucoma'),
(8, 'Medical Retina'),
(9, 'Neuro-ophthalmology'),
(10, 'Oncology'),
(11, 'Paediatrics'),
(12, 'Primary Care'),
(13, 'Refractive'),
(14, 'Strabismus'),
(15, 'Uveitis'),
(16, 'Vitreoretinal');

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
(1, 'admin', 'admin', 'admin', 'admin@admin.com', 1, 'd45409ef1eaa57f5041bf3a1b510097b', 'FbYJis0YG3');

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



LOCK TABLES `site_element_type` WRITE;
/*!40000 ALTER TABLE `site_element_type` DISABLE KEYS */;
INSERT INTO `site_element_type` VALUES (5,1,1,1,0,0),(6,1,1,1,0,1),(7,2,1,1,0,0),(8,2,1,1,0,1),(9,3,1,1,0,0),(10,3,1,1,0,1),(11,4,1,1,0,0),(12,4,1,1,0,1),(13,5,1,1,0,0),(14,5,1,1,0,1),(15,6,1,1,0,0),(16,6,1,1,0,1),(17,7,1,1,0,0),(18,7,1,1,0,1),(19,8,1,1,0,0),(20,8,1,1,0,1),(21,9,1,1,0,0),(22,9,1,1,0,1),(23,10,1,1,0,0),(24,10,1,1,0,1),(25,11,1,1,0,0),(26,11,1,1,0,1),(27,12,1,1,0,0),(28,12,1,1,0,1),(29,13,1,1,0,0),(30,13,1,1,0,1),(31,14,1,1,0,0),(32,14,1,1,0,1),(33,15,1,1,0,0),(34,15,1,1,0,1),(35,16,1,1,0,0),(36,16,1,1,0,1),(37,1,2,1,0,0),(38,1,2,1,0,1),(39,2,2,1,0,0),(40,2,2,1,0,1),(41,3,2,1,0,0),(42,3,2,1,0,1),(43,4,2,1,0,0),(44,4,2,1,0,1),(45,5,2,1,0,0),(46,5,2,1,0,1),(47,6,2,1,0,0),(48,6,2,1,0,1),(49,7,2,1,0,0),(50,7,2,1,0,1),(51,8,2,1,0,0),(52,8,2,1,0,1),(53,9,2,1,0,0),(54,9,2,1,0,1),(55,10,2,1,0,0),(56,10,2,1,0,1),(57,11,2,1,0,0),(58,11,2,1,0,1),(59,12,2,1,0,0),(60,12,2,1,0,1),(61,13,2,1,0,0),(62,13,2,1,0,1),(63,14,2,1,0,0),(64,14,2,1,0,1),(65,15,2,1,0,0),(66,15,2,1,0,1),(67,16,2,1,0,0),(68,16,2,1,0,1),(69,1,3,1,0,0),(70,1,3,1,0,1),(71,2,3,1,0,0),(72,2,3,1,0,1),(73,3,3,1,0,0),(74,3,3,1,0,1),(75,4,3,1,0,0),(76,4,3,1,0,1),(77,5,3,1,0,0),(78,5,3,1,0,1),(79,6,3,1,0,0),(80,6,3,1,0,1),(81,7,3,1,0,0),(82,7,3,1,0,1),(83,8,3,1,0,0),(84,8,3,1,0,1),(85,9,3,1,0,0),(86,9,3,1,0,1),(87,10,3,1,0,0),(88,10,3,1,0,1),(89,11,3,1,0,0),(90,11,3,1,0,1),(91,12,3,1,0,0),(92,12,3,1,0,1),(93,13,3,1,0,0),(94,13,3,1,0,1),(95,14,3,1,0,0),(96,14,3,1,0,1),(97,15,3,1,0,0),(98,15,3,1,0,1),(99,16,3,1,0,0),(100,16,3,1,0,1),(101,1,4,1,0,0),(102,1,4,1,0,1),(103,2,4,1,0,0),(104,2,4,1,0,1),(105,3,4,1,0,0),(106,3,4,1,0,1),(107,4,4,1,0,0),(108,4,4,1,0,1),(109,5,4,1,0,0),(110,5,4,1,0,1),(111,6,4,1,0,0),(112,6,4,1,0,1),(113,7,4,1,0,0),(114,7,4,1,0,1),(115,8,4,1,0,0),(116,8,4,1,0,1),(117,9,4,1,0,0),(118,9,4,1,0,1),(119,10,4,1,0,0),(120,10,4,1,0,1),(121,11,4,1,0,0),(122,11,4,1,0,1),(123,12,4,1,0,0),(124,12,4,1,0,1),(125,13,4,1,0,0),(126,13,4,1,0,1),(127,14,4,1,0,0),(128,14,4,1,0,1),(129,15,4,1,0,0),(130,15,4,1,0,1),(131,16,4,1,0,0),(132,16,4,1,0,1),(133,1,5,1,0,0),(134,1,5,1,0,1),(135,2,5,1,0,0),(136,2,5,1,0,1),(137,3,5,1,0,0),(138,3,5,1,0,1),(139,4,5,1,0,0),(140,4,5,1,0,1),(141,5,5,1,0,0),(142,5,5,1,0,1),(143,6,5,1,0,0),(144,6,5,1,0,1),(145,7,5,1,0,0),(146,7,5,1,0,1),(147,8,5,1,0,0),(148,8,5,1,0,1),(149,9,5,1,0,0),(150,9,5,1,0,1),(151,10,5,1,0,0),(152,10,5,1,0,1),(153,11,5,1,0,0),(154,11,5,1,0,1),(155,12,5,1,0,0),(156,12,5,1,0,1),(157,13,5,1,0,0),(158,13,5,1,0,1),(159,14,5,1,0,0),(160,14,5,1,0,1),(161,15,5,1,0,0),(162,15,5,1,0,1),(163,16,5,1,0,0),(164,16,5,1,0,1),(165,1,6,1,0,0),(166,1,6,1,0,1),(167,2,6,1,0,0),(168,2,6,1,0,1),(169,3,6,1,0,0),(170,3,6,1,0,1),(171,4,6,1,0,0),(172,4,6,1,0,1),(173,5,6,1,0,0),(174,5,6,1,0,1),(175,6,6,1,0,0),(176,6,6,1,0,1),(177,7,6,1,0,0),(178,7,6,1,0,1),(179,8,6,1,0,0),(180,8,6,1,0,1),(181,9,6,1,0,0),(182,9,6,1,0,1),(183,10,6,1,0,0),(184,10,6,1,0,1),(185,11,6,1,0,0),(186,11,6,1,0,1),(187,12,6,1,0,0),(188,12,6,1,0,1),(189,13,6,1,0,0),(190,13,6,1,0,1),(191,14,6,1,0,0),(192,14,6,1,0,1),(193,15,6,1,0,0),(194,15,6,1,0,1),(195,16,6,1,0,0),(196,16,6,1,0,1),(197,1,7,1,0,0),(198,1,7,1,0,1),(199,2,7,1,0,0),(200,2,7,1,0,1),(201,3,7,1,0,0),(202,3,7,1,0,1),(203,4,7,1,0,0),(204,4,7,1,0,1),(205,5,7,1,0,0),(206,5,7,1,0,1),(207,6,7,1,0,0),(208,6,7,1,0,1),(209,7,7,1,0,0),(210,7,7,1,0,1),(211,8,7,1,0,0),(212,8,7,1,0,1),(213,9,7,1,0,0),(214,9,7,1,0,1),(215,10,7,1,0,0),(216,10,7,1,0,1),(217,11,7,1,0,0),(218,11,7,1,0,1),(219,12,7,1,0,0),(220,12,7,1,0,1),(221,13,7,1,0,0),(222,13,7,1,0,1),(223,14,7,1,0,0),(224,14,7,1,0,1),(225,15,7,1,0,0),(226,15,7,1,0,1),(227,16,7,1,0,0),(228,16,7,1,0,1),(229,1,8,1,0,0),(230,1,8,1,0,1),(231,2,8,1,0,0),(232,2,8,1,0,1),(233,3,8,1,0,0),(234,3,8,1,0,1),(235,4,8,1,0,0),(236,4,8,1,0,1),(237,5,8,1,0,0),(238,5,8,1,0,1),(239,6,8,1,0,0),(240,6,8,1,0,1),(241,7,8,1,0,0),(242,7,8,1,0,1),(243,8,8,1,0,0),(244,8,8,1,0,1),(245,9,8,1,0,0),(246,9,8,1,0,1),(247,10,8,1,0,0),(248,10,8,1,0,1),(249,11,8,1,0,0),(250,11,8,1,0,1),(251,12,8,1,0,0),(252,12,8,1,0,1),(253,13,8,1,0,0),(254,13,8,1,0,1),(255,14,8,1,0,0),(256,14,8,1,0,1),(257,15,8,1,0,0),(258,15,8,1,0,1),(259,16,8,1,0,0),(260,16,8,1,0,1),(261,1,9,1,0,0),(262,1,9,1,0,1),(263,2,9,1,0,0),(264,2,9,1,0,1),(265,3,9,1,0,0),(266,3,9,1,0,1),(267,4,9,1,0,0),(268,4,9,1,0,1),(269,5,9,1,0,0),(270,5,9,1,0,1),(271,6,9,1,0,0),(272,6,9,1,0,1),(273,7,9,1,0,0),(274,7,9,1,0,1),(275,8,9,1,0,0),(276,8,9,1,0,1),(277,9,9,1,0,0),(278,9,9,1,0,1),(279,10,9,1,0,0),(280,10,9,1,0,1),(281,11,9,1,0,0),(282,11,9,1,0,1),(283,12,9,1,0,0),(284,12,9,1,0,1),(285,13,9,1,0,0),(286,13,9,1,0,1),(287,14,9,1,0,0),(288,14,9,1,0,1),(289,15,9,1,0,0),(290,15,9,1,0,1),(291,16,9,1,0,0),(292,16,9,1,0,1),(293,1,10,1,0,0),(294,1,10,1,0,1),(295,2,10,1,0,0),(296,2,10,1,0,1),(297,3,10,1,0,0),(298,3,10,1,0,1),(299,4,10,1,0,0),(300,4,10,1,0,1),(301,5,10,1,0,0),(302,5,10,1,0,1),(303,6,10,1,0,0),(304,6,10,1,0,1),(305,7,10,1,0,0),(306,7,10,1,0,1),(307,8,10,1,0,0),(308,8,10,1,0,1),(309,9,10,1,0,0),(310,9,10,1,0,1),(311,10,10,1,0,0),(312,10,10,1,0,1),(313,11,10,1,0,0),(314,11,10,1,0,1),(315,12,10,1,0,0),(316,12,10,1,0,1),(317,13,10,1,0,0),(318,13,10,1,0,1),(319,14,10,1,0,0),(320,14,10,1,0,1),(321,15,10,1,0,0),(322,15,10,1,0,1),(323,16,10,1,0,0),(324,16,10,1,0,1),(325,1,11,1,0,0),(326,1,11,1,0,1),(327,2,11,1,0,0),(328,2,11,1,0,1),(329,3,11,1,0,0),(330,3,11,1,0,1),(331,4,11,1,0,0),(332,4,11,1,0,1),(333,5,11,1,0,0),(334,5,11,1,0,1),(335,6,11,1,0,0),(336,6,11,1,0,1),(337,7,11,1,0,0),(338,7,11,1,0,1),(339,8,11,1,0,0),(340,8,11,1,0,1),(341,9,11,1,0,0),(342,9,11,1,0,1),(343,10,11,1,0,0),(344,10,11,1,0,1),(345,11,11,1,0,0),(346,11,11,1,0,1),(347,12,11,1,0,0),(348,12,11,1,0,1),(349,13,11,1,0,0),(350,13,11,1,0,1),(351,14,11,1,0,0),(352,14,11,1,0,1),(353,15,11,1,0,0),(354,15,11,1,0,1),(355,16,11,1,0,0),(356,16,11,1,0,1),(357,1,12,1,0,0),(358,1,12,1,0,1),(359,2,12,1,0,0),(360,2,12,1,0,1),(361,3,12,1,0,0),(362,3,12,1,0,1),(363,4,12,1,0,0),(364,4,12,1,0,1),(365,5,12,1,0,0),(366,5,12,1,0,1),(367,6,12,1,0,0),(368,6,12,1,0,1),(369,7,12,1,0,0),(370,7,12,1,0,1),(371,8,12,1,0,0),(372,8,12,1,0,1),(373,9,12,1,0,0),(374,9,12,1,0,1),(375,10,12,1,0,0),(376,10,12,1,0,1),(377,11,12,1,0,0),(378,11,12,1,0,1),(379,12,12,1,0,0),(380,12,12,1,0,1),(381,13,12,1,0,0),(382,13,12,1,0,1),(383,14,12,1,0,0),(384,14,12,1,0,1),(385,15,12,1,0,0),(386,15,12,1,0,1),(387,16,12,1,0,0),(388,16,12,1,0,1),(389,1,13,1,0,0),(390,1,13,1,0,1),(391,2,13,1,0,0),(392,2,13,1,0,1),(393,3,13,1,0,0),(394,3,13,1,0,1),(395,4,13,1,0,0),(396,4,13,1,0,1),(397,5,13,1,0,0),(398,5,13,1,0,1),(399,6,13,1,0,0),(400,6,13,1,0,1),(401,7,13,1,0,0),(402,7,13,1,0,1),(403,8,13,1,0,0),(404,8,13,1,0,1),(405,9,13,1,0,0),(406,9,13,1,0,1),(407,10,13,1,0,0),(408,10,13,1,0,1),(409,11,13,1,0,0),(410,11,13,1,0,1),(411,12,13,1,0,0),(412,12,13,1,0,1),(413,13,13,1,0,0),(414,13,13,1,0,1),(415,14,13,1,0,0),(416,14,13,1,0,1),(417,15,13,1,0,0),(418,15,13,1,0,1),(419,16,13,1,0,0),(420,16,13,1,0,1),(421,1,14,1,0,0),(422,1,14,1,0,1),(423,2,14,1,0,0),(424,2,14,1,0,1),(425,3,14,1,0,0),(426,3,14,1,0,1),(427,4,14,1,0,0),(428,4,14,1,0,1),(429,5,14,1,0,0),(430,5,14,1,0,1),(431,6,14,1,0,0),(432,6,14,1,0,1),(433,7,14,1,0,0),(434,7,14,1,0,1),(435,8,14,1,0,0),(436,8,14,1,0,1),(437,9,14,1,0,0),(438,9,14,1,0,1),(439,10,14,1,0,0),(440,10,14,1,0,1),(441,11,14,1,0,0),(442,11,14,1,0,1),(443,12,14,1,0,0),(444,12,14,1,0,1),(445,13,14,1,0,0),(446,13,14,1,0,1),(447,14,14,1,0,0),(448,14,14,1,0,1),(449,15,14,1,0,0),(450,15,14,1,0,1),(451,16,14,1,0,0),(452,16,14,1,0,1),(453,1,15,1,0,0),(454,1,15,1,0,1),(455,2,15,1,0,0),(456,2,15,1,0,1),(457,3,15,1,0,0),(458,3,15,1,0,1),(459,4,15,1,0,0),(460,4,15,1,0,1),(461,5,15,1,0,0),(462,5,15,1,0,1),(463,6,15,1,0,0),(464,6,15,1,0,1),(465,7,15,1,0,0),(466,7,15,1,0,1),(467,8,15,1,0,0),(468,8,15,1,0,1),(469,9,15,1,0,0),(470,9,15,1,0,1),(471,10,15,1,0,0),(472,10,15,1,0,1),(473,11,15,1,0,0),(474,11,15,1,0,1),(475,12,15,1,0,0),(476,12,15,1,0,1),(477,13,15,1,0,0),(478,13,15,1,0,1),(479,14,15,1,0,0),(480,14,15,1,0,1),(481,15,15,1,0,0),(482,15,15,1,0,1),(483,16,15,1,0,0),(484,16,15,1,0,1),(485,1,16,1,0,0),(486,1,16,1,0,1),(487,2,16,1,0,0),(488,2,16,1,0,1),(489,3,16,1,0,0),(490,3,16,1,0,1),(491,4,16,1,0,0),(492,4,16,1,0,1),(493,5,16,1,0,0),(494,5,16,1,0,1),(495,6,16,1,0,0),(496,6,16,1,0,1),(497,7,16,1,0,0),(498,7,16,1,0,1),(499,8,16,1,0,0),(500,8,16,1,0,1),(501,9,16,1,0,0),(502,9,16,1,0,1),(503,10,16,1,0,0),(504,10,16,1,0,1),(505,11,16,1,0,0),(506,11,16,1,0,1),(507,12,16,1,0,0),(508,12,16,1,0,1),(509,13,16,1,0,0),(510,13,16,1,0,1),(511,14,16,1,0,0),(512,14,16,1,0,1),(513,15,16,1,0,0),(514,15,16,1,0,1),(515,16,16,1,0,0),(516,16,16,1,0,1);
/*!40000 ALTER TABLE `site_element_type` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Constraints for dumped tables
--

--
-- Constraints for table `contact`
--
ALTER TABLE `contact`
  ADD CONSTRAINT `contact_ibfk_1` FOREIGN KEY (`contact_type_id`) REFERENCES `contact_type` (`id`);

--
-- Constraints for table `diagnosis`
--
ALTER TABLE `diagnosis`
  ADD CONSTRAINT `diagnosis_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  ADD CONSTRAINT `diagnosis_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `diagnosis_ibfk_3` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`);

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
