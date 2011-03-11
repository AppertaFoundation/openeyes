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

INSERT INTO `disorder` (`id`, `fully_specified_name`, `term`, `systemic`) VALUES
(1, 'Myopia (disorder)', 'Myopia', 0),
(2, 'Retinal lattice degeneration (disorder)', 'Retinal lattice degeneration', 0),
(3, 'Posterior vitreous detachment (disorder)', 'Posterior vitreous detachment', 0),
(4, 'Vitreous hemorrhage (disorder)', 'Vitreous haemorrhage', 0),
(5, 'Essential hypertension (disorder)', 'Essential hypertension', 1),
(6, 'Diabetes mellitus type 1 (disorder)', 'Diabetes mellitus type 1', 1),
(7, 'Diabetes mellitus type 2 (disorder)', 'Diabetes mellitus type 2', 1),
(8, 'Myocardial infarction (disorder)', 'Myocardial infarction', 1);


CREATE TABLE IF NOT EXISTS `diagnosis` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `disorder_id` int(10) unsigned NOT NULL,
  `created_on` datetime NOT NULL,
  `location` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `user_id` (`user_id`),
  KEY `disorder_id` (`disorder_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `diagnosis`
--

INSERT INTO `diagnosis` (`id`, `patient_id`, `user_id`, `disorder_id`, `created_on`, `location`) VALUES
(1, 1, 1, 1, '0000-00-00 00:00:00', 0),
(2, 1, 1, 2, '0000-00-00 00:00:00', 1),
(3, 1, 1, 3, '0000-00-00 00:00:00', 2);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `diagnosis`
--
ALTER TABLE `diagnosis`
  ADD CONSTRAINT `diagnosis_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  ADD CONSTRAINT `diagnosis_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `diagnosis_ibfk_3` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`);
  
CREATE TABLE IF NOT EXISTS `common_ophthalmic_disorder` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `disorder_id` int(10) unsigned NOT NULL,
  `specialty_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `disorder_id` (`disorder_id`),
  KEY `specialty_id` (`specialty_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `common_ophthalmic_disorder`
--

INSERT INTO `common_ophthalmic_disorder` (`id`, `disorder_id`, `specialty_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `common_ophthalmic_disorder`
--
ALTER TABLE `common_ophthalmic_disorder`
  ADD CONSTRAINT `common_ophthalmic_disorder_ibfk_1` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`),
  ADD CONSTRAINT `common_ophthalmic_disorder_ibfk_2` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`);

CREATE TABLE IF NOT EXISTS `common_systemic_disorder` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `disorder_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `disorder_id` (`disorder_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `common_systemic_disorder`
--

INSERT INTO `common_systemic_disorder` (`id`, `disorder_id`) VALUES
(1, 5),
(2, 6),
(3, 7);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `common_systemic_disorder`
--
ALTER TABLE `common_systemic_disorder`
  ADD CONSTRAINT `common_systemic_disorder_ibfk_1` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`);
