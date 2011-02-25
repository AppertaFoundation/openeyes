-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 25, 2011 at 12:38 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `openeyes`
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

INSERT INTO `event` (`id`, `episode_id`, `user_id`, `event_type_id`, `datetime`) VALUES
(1, 1, 1, 1, '0000-00-00 00:00:00'),
(2, 1, 1, 1, '0000-00-00 00:00:00'),
(3, 1, 1, 1, '0000-00-00 00:00:00'),
(4, 1, 1, 1, '0000-00-00 00:00:00'),
(5, 2, 1, 1, '0000-00-00 00:00:00'),
(6, 2, 1, 1, '0000-00-00 00:00:00'),
(7, 3, 1, 1, '0000-00-00 00:00:00'),
(8, 3, 1, 1, '0000-00-00 00:00:00'),
(9, 3, 1, 1, '2011-02-23 18:11:01'),
(10, 3, 1, 1, '2011-02-23 18:11:53'),
(11, 4, 1, 1, '2011-02-23 18:12:21'),
(12, 5, 1, 1, '2011-02-23 18:12:45'),
(13, 6, 1, 1, '2011-02-23 18:14:51'),
(14, 6, 1, 1, '2011-02-23 18:14:59'),
(15, 6, 1, 1, '2011-02-24 11:01:52'),
(16, 1, 1, 1, '2011-02-24 11:49:58'),
(17, 1, 1, 1, '2011-02-24 15:36:30');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_1` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`),
  ADD CONSTRAINT `event_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `event_3` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`id`);
