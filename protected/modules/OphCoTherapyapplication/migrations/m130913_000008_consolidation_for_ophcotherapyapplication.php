<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class m130913_000008_consolidation_for_ophcotherapyapplication extends OEMigration
{
    private $element_types;

    public function setData()
    {
        $this->element_types = array(
            'Element_OphCoTherapyapplication_Therapydiagnosis' => array('name' => 'Diagnosis', 'display_order' => 10),
            'Element_OphCoTherapyapplication_PatientSuitability' => array('name' => 'Patient Suitability', 'display_order' => 15),
            'Element_OphCoTherapyapplication_RelativeContraindications' => array('name' => 'Relative ContraIndications', 'display_order' => 20),
            'Element_OphCoTherapyapplication_MrServiceInformation' => array('name' => 'MR Service Information', 'display_order' => 30),
            'Element_OphCoTherapyapplication_ExceptionalCircumstances' => array('name' => 'Exceptional Circumstances', 'display_order' => 40),
            'Element_OphCoTherapyapplication_Email' => array('name' => 'Application Email', 'display_order' => 50, 'default' => 0),
        );
    }

    public function up()
    {
        if (!$this->consolidate(
            array(
                'm130703_152448_event_type_OphCoTherapyapplication',
                'm130724_103306_previousintervention_tweaks',
                'm130725_075105_status_flag_for_therapy_application_email_element',
                'm130813_141710_release_tweaks',
            )
        )
        ) {
            return $this->createTables();
        }
    }

    public function createTables()
    {
        if (!Yii::app()->hasModule('OphTrIntravitrealinjection')) {
            echo '
			-----------------------------------
			Skipping OphTrIntravitrealinjection - missing module dependency
			-----------------------------------
			';

            return false;
            //throw new Exception("OphTrIntravitrealinjection is required for this module to work");
        }

        if (!in_array('ophtrintravitinjection_treatment_drug', $this->dbConnection->getSchema()->tableNames)) {
            echo '
			-----------------------------------
			Skipping OphTrIntravitrealinjection - missing module table ophtrintravitinjection_treatment_drug dependency
			-----------------------------------
			';

            return false;
            //throw new Exception("OphTrIntravitrealinjection is required for this module to work");
        }

        $this->setData();
        //disable foreign keys check
        $this->execute('SET foreign_key_checks = 0');

        Yii::app()->cache->flush();

        $event_type_id = $this->insertOEEventType('Therapy Application', 'OphCoTherapyapplication', 'Co');
        $this->insertOEElementType($this->element_types, $event_type_id);

        $this->execute("CREATE TABLE `et_ophcotherapya_email` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '3',
			  `left_email_text` text,
			  `right_email_text` text,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `sent` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcotherapya_email_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophcotherapya_email_cui_fk` (`created_user_id`),
			  KEY `et_ophcotherapya_email_ev_fk` (`event_id`),
			  CONSTRAINT `et_ophcotherapya_email_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_email_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophcotherapya_email_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophcotherapya_exceptional` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '3',
			  `left_standard_intervention_exists` tinyint(1) unsigned NOT NULL,
			  `left_standard_intervention_id` int(10) unsigned DEFAULT NULL,
			  `left_standard_previous` tinyint(1) DEFAULT NULL,
			  `left_condition_rare` tinyint(1) DEFAULT NULL,
			  `left_incidence` text,
			  `left_intervention_id` int(10) unsigned DEFAULT NULL,
			  `left_description` text,
			  `left_patient_different` text,
			  `left_patient_gain` text,
			  `left_patient_factors` tinyint(1) unsigned DEFAULT NULL,
			  `left_patient_factor_details` text,
			  `left_start_period_id` int(10) unsigned DEFAULT NULL,
			  `left_urgency_reason` text,
			  `right_standard_intervention_exists` tinyint(1) unsigned DEFAULT NULL,
			  `right_standard_intervention_id` int(10) unsigned DEFAULT NULL,
			  `right_standard_previous` tinyint(1) DEFAULT NULL,
			  `right_condition_rare` tinyint(1) DEFAULT NULL,
			  `right_incidence` text,
			  `right_intervention_id` int(10) unsigned DEFAULT NULL,
			  `right_description` text,
			  `right_patient_different` text,
			  `right_patient_gain` text,
			  `right_patient_factors` tinyint(1) unsigned DEFAULT NULL,
			  `right_patient_factor_details` text,
			  `right_start_period_id` int(10) unsigned DEFAULT NULL,
			  `right_urgency_reason` text,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `left_patient_expectations` text,
			  `right_patient_expectations` text,
			  PRIMARY KEY (`id`),
			  KEY `et_ophcotherapya_exceptional_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophcotherapya_exceptional_cui_fk` (`created_user_id`),
			  KEY `et_ophcotherapya_exceptional_ev_fk` (`event_id`),
			  KEY `et_ophcotherapya_exceptional_lsi_fk` (`left_standard_intervention_id`),
			  KEY `et_ophcotherapya_exceptional_linterventions_fk` (`left_intervention_id`),
			  KEY `et_ophcotherapya_exceptional_rsi_fk` (`right_standard_intervention_id`),
			  KEY `et_ophcotherapya_exceptional_rinterventions_fk` (`right_intervention_id`),
			  KEY `et_ophcotherapya_exceptional_eye_id_fk` (`eye_id`),
			  KEY `et_ophcotherapya_exceptional_lspid_fk` (`left_start_period_id`),
			  KEY `et_ophcotherapya_exceptional_rspid_fk` (`right_start_period_id`),
			  CONSTRAINT `et_ophcotherapya_exceptional_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_exceptional_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophcotherapya_exceptional_linterventions_fk` FOREIGN KEY (`left_intervention_id`) REFERENCES `et_ophcotherapya_exceptional_intervention` (`id`),
			  CONSTRAINT `et_ophcotherapya_exceptional_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_exceptional_lsi_fk` FOREIGN KEY (`left_standard_intervention_id`) REFERENCES `ophcotherapya_exceptional_standardintervention` (`id`),
			  CONSTRAINT `et_ophcotherapya_exceptional_lspid_fk` FOREIGN KEY (`left_start_period_id`) REFERENCES `ophcotherapya_exceptional_startperiod` (`id`),
			  CONSTRAINT `et_ophcotherapya_exceptional_rinterventions_fk` FOREIGN KEY (`right_intervention_id`) REFERENCES `et_ophcotherapya_exceptional_intervention` (`id`),
			  CONSTRAINT `et_ophcotherapya_exceptional_rsi_fk` FOREIGN KEY (`right_standard_intervention_id`) REFERENCES `ophcotherapya_exceptional_standardintervention` (`id`),
			  CONSTRAINT `et_ophcotherapya_exceptional_rspid_fk` FOREIGN KEY (`right_start_period_id`) REFERENCES `ophcotherapya_exceptional_startperiod` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophcotherapya_exceptional_intervention` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `description_label` varchar(128) NOT NULL,
			  `is_deviation` tinyint(1) NOT NULL DEFAULT '0',
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcotherapya_exceptional_intervention_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophcotherapya_exceptional_intervention_cui_fk` (`created_user_id`),
			  CONSTRAINT `et_ophcotherapya_exceptional_intervention_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_exceptional_intervention_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophcotherapya_mrservicein` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `consultant_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcotherapya_mrservicein_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophcotherapya_mrservicein_cui_fk` (`created_user_id`),
			  KEY `et_ophcotherapya_mrservicein_ev_fk` (`event_id`),
			  KEY `et_ophcotherapya_mrservicein_consultant_id_fk` (`consultant_id`),
			  CONSTRAINT `et_ophcotherapya_mrservicein_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_mrservicein_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_mrservicein_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophcotherapya_mrservicein_consultant_id_fk` FOREIGN KEY (`consultant_id`) REFERENCES `firm` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophcotherapya_patientsuit` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '3',
			  `left_treatment_id` int(10) unsigned DEFAULT NULL,
			  `left_angiogram_baseline_date` date DEFAULT NULL,
			  `left_nice_compliance` tinyint(1) unsigned DEFAULT NULL,
			  `right_treatment_id` int(10) unsigned DEFAULT NULL,
			  `right_angiogram_baseline_date` date DEFAULT NULL,
			  `right_nice_compliance` tinyint(1) unsigned DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcotherapya_patientsuit_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophcotherapya_patientsuit_cui_fk` (`created_user_id`),
			  KEY `et_ophcotherapya_patientsuit_ev_fk` (`event_id`),
			  KEY `et_ophcotherapya_patientsuit_ltreatment_fk` (`left_treatment_id`),
			  KEY `et_ophcotherapya_patientsuit_rtreatment_fk` (`right_treatment_id`),
			  KEY `et_ophcotherapya_patientsuit_eye_id_fk` (`eye_id`),
			  CONSTRAINT `et_ophcotherapya_patientsuit_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_patientsuit_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_patientsuit_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophcotherapya_patientsuit_ltreatment_fk` FOREIGN KEY (`left_treatment_id`) REFERENCES `ophcotherapya_treatment` (`id`),
			  CONSTRAINT `et_ophcotherapya_patientsuit_rtreatment_fk` FOREIGN KEY (`right_treatment_id`) REFERENCES `ophcotherapya_treatment` (`id`),
			  CONSTRAINT `et_ophcotherapya_patientsuit_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophcotherapya_relativecon` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `cerebrovascular_accident` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `ischaemic_attack` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `myocardial_infarction` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcotherapya_relativecon_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophcotherapya_relativecon_cui_fk` (`created_user_id`),
			  KEY `et_ophcotherapya_relativecon_ev_fk` (`event_id`),
			  CONSTRAINT `et_ophcotherapya_relativecon_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_relativecon_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_relativecon_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophcotherapya_therapydiag` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '3',
			  `left_diagnosis1_id` BIGINT unsigned DEFAULT NULL,
			  `left_diagnosis2_id` BIGINT unsigned DEFAULT NULL,
			  `right_diagnosis1_id` BIGINT unsigned DEFAULT NULL,
			  `right_diagnosis2_id` BIGINT unsigned DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcotherapya_therapydiag_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophcotherapya_therapydiag_cui_fk` (`created_user_id`),
			  KEY `et_ophcotherapya_therapydiag_ev_fk` (`event_id`),
			  KEY `et_ophcotherapya_therapydiag_ldiagnosis1_id_fk` (`left_diagnosis1_id`),
			  KEY `et_ophcotherapya_therapydiag_rdiagnosis1_id_fk` (`right_diagnosis1_id`),
			  KEY `et_ophcotherapya_therapydiag_ldiagnosis2_id_fk` (`left_diagnosis2_id`),
			  KEY `et_ophcotherapya_therapydiag_rdiagnosis2_id_fk` (`right_diagnosis2_id`),
			  KEY `et_ophcotherapya_therapydiag_eye_id_fk` (`eye_id`),
			  CONSTRAINT `et_ophcotherapya_therapydiag_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_therapydiag_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_therapydiag_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophcotherapya_therapydiag_ldiagnosis1_id_fk` FOREIGN KEY (`left_diagnosis1_id`) REFERENCES `disorder` (`id`),
			  CONSTRAINT `et_ophcotherapya_therapydiag_rdiagnosis1_id_fk` FOREIGN KEY (`right_diagnosis1_id`) REFERENCES `disorder` (`id`),
			  CONSTRAINT `et_ophcotherapya_therapydiag_ldiagnosis2_id_fk` FOREIGN KEY (`left_diagnosis2_id`) REFERENCES `disorder` (`id`),
			  CONSTRAINT `et_ophcotherapya_therapydiag_rdiagnosis2_id_fk` FOREIGN KEY (`right_diagnosis2_id`) REFERENCES `disorder` (`id`),
			  CONSTRAINT `et_ophcotherapya_therapydiag_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_decisiontree` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophcotherapya_decisiontree_lmui_fk` (`last_modified_user_id`),
			  KEY `ophcotherapya_decisiontree_cui_fk` (`created_user_id`),
			  CONSTRAINT `et_ophcotherapya_decisiontree_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_decisiontree_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_decisiontreenode` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `decisiontree_id` int(10) unsigned NOT NULL,
			  `parent_id` int(10) unsigned DEFAULT NULL,
			  `question` varchar(256) DEFAULT NULL,
			  `outcome_id` int(10) unsigned DEFAULT NULL,
			  `default_function` varchar(64) DEFAULT NULL,
			  `default_value` varchar(16) DEFAULT NULL,
			  `response_type_id` int(10) unsigned DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophcotherapya_decisiontreenode_lmui_fk` (`last_modified_user_id`),
			  KEY `ophcotherapya_decisiontreenode_cui_fk` (`created_user_id`),
			  KEY `ophcotherapya_decisiontreenode_dti_fk` (`decisiontree_id`),
			  KEY `ophcotherapya_decisiontreenode_pi_fk` (`parent_id`),
			  KEY `ophcotherapya_decisiontreenode_oi_fk` (`outcome_id`),
			  KEY `ophcotherapya_decisiontreenode_rti_fk` (`response_type_id`),
			  CONSTRAINT `ophcotherapya_decisiontreenode_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_decisiontreenode_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_decisiontreenode_dti_fk` FOREIGN KEY (`decisiontree_id`) REFERENCES `ophcotherapya_decisiontree` (`id`),
			  CONSTRAINT `ophcotherapya_decisiontreenode_pi_fk` FOREIGN KEY (`parent_id`) REFERENCES `ophcotherapya_decisiontreenode` (`id`),
			  CONSTRAINT `ophcotherapya_decisiontreenode_oi_fk` FOREIGN KEY (`outcome_id`) REFERENCES `ophcotherapya_decisiontreeoutcome` (`id`),
			  CONSTRAINT `ophcotherapya_decisiontreenode_rti_fk` FOREIGN KEY (`response_type_id`) REFERENCES `ophcotherapya_decisiontreenode_responsetype` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_decisiontreenode_responsetype` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `label` varchar(32) NOT NULL,
			  `datatype` varchar(16) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophcotherapya_decisiontreenode_rtype_lmui_fk` (`last_modified_user_id`),
			  KEY `ophcotherapya_decisiontreenode_rtype_cui_fk` (`created_user_id`),
			  CONSTRAINT `et_ophcotherapya_decisiontreenode_rtype_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_decisiontreenode_rtype_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_decisiontreenodechoice` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `node_id` int(10) unsigned NOT NULL,
			  `label` varchar(32) NOT NULL,
			  `display_order` int(10) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophcotherapya_decisiontreenodechoice_lmui_fk` (`last_modified_user_id`),
			  KEY `ophcotherapya_decisiontreenodechoice_cui_fk` (`created_user_id`),
			  KEY `ophcotherapya_decisiontreenodechoice_ni_fk` (`node_id`),
			  CONSTRAINT `ophcotherapya_decisiontreenodechoice_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_decisiontreenodechoice_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_decisiontreenodechoice_ni_fk` FOREIGN KEY (`node_id`) REFERENCES `ophcotherapya_decisiontreenode` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_decisiontreenoderule` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `node_id` int(10) unsigned NOT NULL,
			  `parent_check` varchar(4) DEFAULT NULL,
			  `parent_check_value` varchar(16) DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophcotherapya_decisiontreenoderule_lmui_fk` (`last_modified_user_id`),
			  KEY `ophcotherapya_decisiontreenoderule_cui_fk` (`created_user_id`),
			  KEY `ophcotherapya_decisiontreenoderule_ni_fk` (`node_id`),
			  CONSTRAINT `ophcotherapya_decisiontreenoderule_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_decisiontreenoderule_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_decisiontreenoderule_ni_fk` FOREIGN KEY (`node_id`) REFERENCES `ophcotherapya_decisiontreenode` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_decisiontreeoutcome` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(32) NOT NULL,
			  `outcome_type` varchar(16) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophcotherapya_decisiontreeoutcome_lmui_fk` (`last_modified_user_id`),
			  KEY `ophcotherapya_decisiontreeoutcome_cui_fk` (`created_user_id`),
			  CONSTRAINT `et_ophcotherapya_decisiontreeoutcome_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_decisiontreeoutcome_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_email_attachment` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `element_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL,
			  `file_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcotherapya_email_att_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophcotherapya_email_att_cui_fk` (`created_user_id`),
			  KEY `et_ophcotherapya_email_att_ei_fk` (`element_id`),
			  KEY `et_ophcotherapya_email_att_eyei_fk` (`eye_id`),
			  KEY `et_ophcotherapya_email_att_fi_fk` (`file_id`),
			  CONSTRAINT `et_ophcotherapya_email_att_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_email_att_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_email_att_ei_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophcotherapya_email` (`id`),
			  CONSTRAINT `et_ophcotherapya_email_att_eyei_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
			  CONSTRAINT `et_ophcotherapya_email_att_fi_fk` FOREIGN KEY (`file_id`) REFERENCES `protected_file` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_exceptional_deviationreason` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `enabled` tinyint(1) NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophcotherapya_exceptional_deviationreason_lmui_fk` (`last_modified_user_id`),
			  KEY `ophcotherapya_exceptional_deviationreason_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophcotherapya_exceptional_deviationreason_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_exceptional_deviationreason_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_exceptional_deviationreason_ass` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `element_id` int(10) unsigned NOT NULL,
			  `side_id` tinyint(1) NOT NULL,
			  `deviationreason_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcotherapya_except_devrass_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophcotherapya_except_devrass_cui_fk` (`created_user_id`),
			  KEY `et_ophcotherapya_except_devrass_ei_fk` (`element_id`),
			  KEY `et_ophcotherapya_except_devrass_ci_fk` (`deviationreason_id`),
			  CONSTRAINT `et_ophcotherapya_except_devrass_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_except_devrass_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_except_devrass_ei_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophcotherapya_exceptional` (`id`),
			  CONSTRAINT `et_ophcotherapya_except_devrass_ci_fk` FOREIGN KEY (`deviationreason_id`) REFERENCES `ophcotherapya_exceptional_deviationreason` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_exceptional_filecoll_assignment` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `exceptional_id` int(10) unsigned NOT NULL,
			  `exceptional_side_id` tinyint(1) NOT NULL,
			  `collection_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcotherapya_except_filecollass_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophcotherapya_except_filecollass_cui_fk` (`created_user_id`),
			  KEY `et_ophcotherapya_except_filecollass_ei_fk` (`exceptional_id`),
			  KEY `et_ophcotherapya_except_filecollass_ci_fk` (`collection_id`),
			  CONSTRAINT `et_ophcotherapya_except_filecollass_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_except_filecollass_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_except_filecollass_ei_fk` FOREIGN KEY (`exceptional_id`) REFERENCES `et_ophcotherapya_exceptional` (`id`),
			  CONSTRAINT `et_ophcotherapya_except_filecollass_ci_fk` FOREIGN KEY (`collection_id`) REFERENCES `ophcotherapya_filecoll` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_exceptional_pastintervention` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `exceptional_id` int(10) unsigned NOT NULL,
			  `exceptional_side_id` tinyint(1) NOT NULL,
			  `treatment_id` int(10) unsigned DEFAULT NULL,
			  `stopreason_id` int(10) unsigned NOT NULL,
			  `start_date` datetime NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `stopreason_other` text,
			  `comments` text,
			  `start_va` varchar(255) DEFAULT NULL,
			  `end_va` varchar(255) DEFAULT NULL,
			  `end_date` datetime NOT NULL,
			  `is_relevant` tinyint(1) NOT NULL DEFAULT '0',
			  `relevanttreatment_id` int(10) unsigned DEFAULT NULL,
			  `relevanttreatment_other` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `ophcotherapya_exceptional_previntervention_lmui_fk` (`last_modified_user_id`),
			  KEY `ophcotherapya_exceptional_previntervention_cui_fk` (`created_user_id`),
			  KEY `ophcotherapya_exceptional_previntervention_ei_fk` (`exceptional_id`),
			  KEY `ophcotherapya_exceptional_previntervention_ti_fk` (`treatment_id`),
			  KEY `ophcotherapya_exceptional_previntervention_sri_fk` (`stopreason_id`),
			  KEY `ophcotherapya_pastintervention_rtui_fk` (`relevanttreatment_id`),
			  CONSTRAINT `ophcotherapya_exceptional_previntervention_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_exceptional_previntervention_ei_fk` FOREIGN KEY (`exceptional_id`) REFERENCES `et_ophcotherapya_exceptional` (`id`),
			  CONSTRAINT `ophcotherapya_exceptional_previntervention_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_exceptional_previntervention_sri_fk` FOREIGN KEY (`stopreason_id`) REFERENCES `ophcotherapya_exceptional_pastintervention_stopreason` (`id`),
			  CONSTRAINT `ophcotherapya_exceptional_previntervention_ti_fk` FOREIGN KEY (`treatment_id`) REFERENCES `ophcotherapya_treatment` (`id`),
			  CONSTRAINT `ophcotherapya_pastintervention_rtui_fk` FOREIGN KEY (`relevanttreatment_id`) REFERENCES `ophcotherapya_relevanttreatment` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_exceptional_pastintervention_stopreason` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `other` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `ophcotherapya_exceptional_previntervention_stopreason_lmui_fk` (`last_modified_user_id`),
			  KEY `ophcotherapya_exceptional_previntervention_stopreason_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophcotherapya_exceptional_previntervention_stopreason_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_exceptional_previntervention_stopreason_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_exceptional_standardintervention` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `enabled` tinyint(1) NOT NULL DEFAULT '1',
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophcotherapya_exceptional_standardintervention_lmui_fk` (`last_modified_user_id`),
			  KEY `ophcotherapya_exceptional_standardintervention_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophcotherapya_exceptional_standardintervention_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_exceptional_standardintervention_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_exceptional_startperiod` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `urgent` tinyint(1) DEFAULT '0',
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `enabled` tinyint(1) NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `application_description` varchar(511) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `ophcotherapya_exceptional_startperiod_lmui_fk` (`last_modified_user_id`),
			  KEY `ophcotherapya_exceptional_startperiod_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophcotherapya_exceptional_startperiod_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_exceptional_startperiod_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_filecoll` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(256) NOT NULL,
			  `zipfile_id` int(10) unsigned DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `summary` text,
			  PRIMARY KEY (`id`),
			  KEY `et_ophcotherapya_filecoll_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophcotherapya_filecoll_cui_fk` (`created_user_id`),
			  KEY `et_ophcotherapya_filecoll_zi_fk` (`zipfile_id`),
			  CONSTRAINT `et_ophcotherapya_filecoll_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_filecoll_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_filecoll_zi_fk` FOREIGN KEY (`zipfile_id`) REFERENCES `protected_file` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_filecoll_assignment` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `collection_id` int(10) unsigned NOT NULL,
			  `file_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcotherapya_filecollass_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophcotherapya_filecollass_cui_fk` (`created_user_id`),
			  KEY `et_ophcotherapya_filecollass_ci_fk` (`collection_id`),
			  KEY `et_ophcotherapya_filecollass_fi_fk` (`file_id`),
			  CONSTRAINT `et_ophcotherapya_filecollass_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_filecollass_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_filecollass_ci_fk` FOREIGN KEY (`collection_id`) REFERENCES `ophcotherapya_filecoll` (`id`),
			  CONSTRAINT `et_ophcotherapya_filecollass_fi_fk` FOREIGN KEY (`file_id`) REFERENCES `protected_file` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_patientsuit_decisiontreenoderesponse` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `patientsuit_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL,
			  `node_id` int(10) unsigned NOT NULL,
			  `value` varchar(255) DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophcotherapya_patientsuit_dtnoderesponse_lmui_fk` (`last_modified_user_id`),
			  KEY `ophcotherapya_patientsuit_dtnoderesponse_cui_fk` (`created_user_id`),
			  KEY `ophcotherapya_patientsuit_dtnoderesponse_psi_fk` (`patientsuit_id`),
			  KEY `ophcotherapya_patientsuit_dtnoderesponse_eye_id_fk` (`eye_id`),
			  CONSTRAINT `ophcotherapya_patientsuit_dtnoderesponse_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_patientsuit_dtnoderesponse_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
			  CONSTRAINT `ophcotherapya_patientsuit_dtnoderesponse_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_patientsuit_dtnoderesponse_psi_fk` FOREIGN KEY (`patientsuit_id`) REFERENCES `et_ophcotherapya_patientsuit` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_relevanttreatment` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `other` tinyint(1) NOT NULL DEFAULT '0',
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophcotherapya_relevanttreatment_lmui_fk` (`last_modified_user_id`),
			  KEY `ophcotherapya_relevanttreatment_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophcotherapya_relevanttreatment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_relevanttreatment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_therapydisorder` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `disorder_id` BIGINT unsigned NOT NULL,
			  `parent_id` int(10) unsigned DEFAULT NULL,
			  `display_order` int(10) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophcotherapya_therapydisorder_lmui_fk` (`last_modified_user_id`),
			  KEY `ophcotherapya_therapydisorder_cui_fk` (`created_user_id`),
			  KEY `ophcotherapya_therapydisorder_di_fk` (`disorder_id`),
			  KEY `ophcotherapya_therapydisorder_pi_fk` (`parent_id`),
			  CONSTRAINT `ophcotherapya_therapydisorder_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_therapydisorder_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_therapydisorder_di_fk` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`),
			  CONSTRAINT `ophcotherapya_therapydisorder_pi_fk` FOREIGN KEY (`parent_id`) REFERENCES `ophcotherapya_therapydisorder` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_treatment` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `drug_id` int(10) unsigned NOT NULL,
			  `decisiontree_id` int(10) unsigned DEFAULT NULL,
			  `contraindications_required` tinyint(1) NOT NULL,
			  `template_code` varchar(8) DEFAULT NULL,
			  `intervention_name` varchar(128) NOT NULL,
			  `dose_and_frequency` varchar(256) NOT NULL,
			  `administration_route` varchar(256) NOT NULL,
			  `cost` int(10) unsigned NOT NULL,
			  `cost_type_id` int(10) unsigned NOT NULL,
			  `monitoring_frequency` int(10) unsigned NOT NULL,
			  `monitoring_frequency_period_id` int(10) unsigned NOT NULL,
			  `duration` varchar(512) NOT NULL,
			  `toxicity` text NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophcotherapya_treatment_lmui_fk` (`last_modified_user_id`),
			  KEY `ophcotherapya_treatment_cui_fk` (`created_user_id`),
			  KEY `ophcotherapya_treatment_dti_fk` (`decisiontree_id`),
			  KEY `ophcotherapya_treatment_dri_fk` (`drug_id`),
			  KEY `ophcotherapya_treatment_ct_fk` (`cost_type_id`),
			  KEY `ophcotherapya_treatment_mfp_fk` (`monitoring_frequency_period_id`),
			  CONSTRAINT `ophcotherapya_treatment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_treatment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcotherapya_treatment_dti_fk` FOREIGN KEY (`decisiontree_id`) REFERENCES `ophcotherapya_decisiontree` (`id`),
			  CONSTRAINT `ophcotherapya_treatment_dri_fk` FOREIGN KEY (`drug_id`) REFERENCES `ophtrintravitinjection_treatment_drug` (`id`),
			  CONSTRAINT `ophcotherapya_treatment_ct_fk` FOREIGN KEY (`cost_type_id`) REFERENCES `ophcotherapya_treatment_cost_type` (`id`),
			  CONSTRAINT `ophcotherapya_treatment_mfp_fk` FOREIGN KEY (`monitoring_frequency_period_id`) REFERENCES `period` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcotherapya_treatment_cost_type` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcotherapya_treatment_cost_type_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophcotherapya_treatment_cost_type_cui_fk` (`created_user_id`),
			  CONSTRAINT `et_ophcotherapya_treatment_cost_type_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcotherapya_treatment_cost_type_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);

        //enable foreign keys check
        $this->execute('SET foreign_key_checks = 1');
    }

    public function down()
    {
        echo 'Down method not supported on consolidation';
    }
}
