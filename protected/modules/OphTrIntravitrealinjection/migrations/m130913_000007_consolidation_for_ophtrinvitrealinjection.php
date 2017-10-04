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
class m130913_000007_consolidation_for_ophtrinvitrealinjection extends OEMigration
{
    private $element_types;

    public function setData()
    {
        $this->element_types = array(
            'Element_OphTrIntravitrealinjection_Site' => array('name' => 'Site', 'display_order' => 1),
            'Element_OphTrIntravitrealinjection_Anaesthetic' => array('name' => 'Anaesthetic', 'display_order' => 1),
            'Element_OphTrIntravitrealinjection_Treatment' => array('name' => 'Treatment', 'display_order' => 1),
            'Element_OphTrIntravitrealinjection_AnteriorSegment' => array('name' => 'Anterior Segment', 'display_order' => 2),
            'Element_OphTrIntravitrealinjection_PostInjectionExamination' => array('name' => 'Post Injection Examination', 'display_order' => 3),
            'Element_OphTrIntravitrealinjection_Complications' => array('name' => 'Complications', 'display_order' => 4),
        );
    }

    public function up()
    {
        if (!$this->consolidate(
            array(
                'm130625_144651_event_type_OphTrIntravitrealinjection',
                'm130725_145929_drops_changes',
                'm130808_130727_missing_fields',
            )
        )
        ) {
            $this->createTables();
        }
    }

    public function createTables()
    {
        $this->setData();
        //disable foreign keys check
        $this->execute('SET foreign_key_checks = 0');

        Yii::app()->cache->flush();

        $event_type_id = $this->insertOEEventType('Intravitreal injection', 'OphTrIntravitrealinjection', 'Tr');
        $this->insertOEElementType($this->element_types, $event_type_id);

        $this->execute("CREATE TABLE `et_ophtrintravitinjection_anaesthetic` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '3',
			  `left_anaesthetictype_id` int(10) unsigned DEFAULT NULL,
			  `left_anaestheticdelivery_id` int(10) unsigned DEFAULT NULL,
			  `left_anaestheticagent_id` int(10) unsigned DEFAULT NULL,
			  `right_anaesthetictype_id` int(10) unsigned DEFAULT NULL,
			  `right_anaestheticdelivery_id` int(10) unsigned DEFAULT NULL,
			  `right_anaestheticagent_id` int(10) unsigned DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtrintravitinjection_anaesthetic_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtrintravitinjection_anaesthetic_cui_fk` (`created_user_id`),
			  KEY `et_ophtrintravitinjection_anaesthetic_ev_fk` (`event_id`),
			  KEY `et_ophtrintravitinjection_anaesthetic_eye_id_fk` (`eye_id`),
			  KEY `et_ophtrintravitinjection_anaesthetic_lat_id_fk` (`left_anaesthetictype_id`),
			  KEY `et_ophtrintravitinjection_anaesthetic_lad_id_fk` (`left_anaestheticdelivery_id`),
			  KEY `et_ophtrintravitinjection_anaesthetic_laa_id_fk` (`left_anaestheticagent_id`),
			  KEY `et_ophtrintravitinjection_anaesthetic_rat_id_fk` (`right_anaesthetictype_id`),
			  KEY `et_ophtrintravitinjection_anaesthetic_rad_id_fk` (`right_anaestheticdelivery_id`),
			  KEY `et_ophtrintravitinjection_anaesthetic_raa_id_fk` (`right_anaestheticagent_id`),
			  CONSTRAINT `et_ophtrintravitinjection_anaesthetic_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_anaesthetic_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_anaesthetic_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_anaesthetic_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_anaesthetic_lat_id_fk` FOREIGN KEY (`left_anaesthetictype_id`) REFERENCES `anaesthetic_type` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_anaesthetic_lad_id_fk` FOREIGN KEY (`left_anaestheticdelivery_id`) REFERENCES `anaesthetic_delivery` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_anaesthetic_laa_id_fk` FOREIGN KEY (`left_anaestheticagent_id`) REFERENCES `anaesthetic_agent` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_anaesthetic_rat_id_fk` FOREIGN KEY (`right_anaesthetictype_id`) REFERENCES `anaesthetic_type` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_anaesthetic_rad_id_fk` FOREIGN KEY (`right_anaestheticdelivery_id`) REFERENCES `anaesthetic_delivery` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_anaesthetic_raa_id_fk` FOREIGN KEY (`right_anaestheticagent_id`) REFERENCES `anaesthetic_agent` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophtrintravitinjection_anteriorseg` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '3',
			  `left_eyedraw` text,
			  `right_eyedraw` text,
			  `left_lens_status_id` int(10) unsigned DEFAULT NULL,
			  `right_lens_status_id` int(10) unsigned DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtrintravitinjection_anteriorseg_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtrintravitinjection_anteriorseg_cui_fk` (`created_user_id`),
			  KEY `et_ophtrintravitinjection_anteriorseg_ei_fk` (`eye_id`),
			  KEY `et_ophtrintravitinjection_anteriorseg_llsi_fk` (`left_lens_status_id`),
			  KEY `et_ophtrintravitinjection_anteriorseg_rlsi_fk` (`right_lens_status_id`),
			  CONSTRAINT `et_ophtrintravitinjection_anteriorseg_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_anteriorseg_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_anteriorseg_ei_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_anteriorseg_llsi_fk` FOREIGN KEY (`left_lens_status_id`) REFERENCES `ophtrintravitinjection_lens_status` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_anteriorseg_rlsi_fk` FOREIGN KEY (`right_lens_status_id`) REFERENCES `ophtrintravitinjection_lens_status` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophtrintravitinjection_complications` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '3',
			  `left_oth_descrip` text,
			  `right_oth_descrip` text,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtrintravitinjection_complicat_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtrintravitinjection_complicat_cui_fk` (`created_user_id`),
			  KEY `et_ophtrintravitinjection_complicat_ev_fk` (`event_id`),
			  KEY `et_ophtrintravitinjection_complicat_eye_id_fk` (`eye_id`),
			  CONSTRAINT `et_ophtrintravitinjection_complicat_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_complicat_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_complicat_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_complicat_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophtrintravitinjection_postinject` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned DEFAULT '3',
			  `left_finger_count` tinyint(1) unsigned DEFAULT '0',
			  `right_finger_count` tinyint(1) unsigned DEFAULT '0',
			  `left_iop_check` tinyint(1) unsigned DEFAULT '0',
			  `right_iop_check` tinyint(1) unsigned DEFAULT '0',
			  `left_drops_id` int(10) unsigned DEFAULT NULL,
			  `right_drops_id` int(10) unsigned DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtrintravitinjection_postinject_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtrintravitinjection_postinject_cui_fk` (`created_user_id`),
			  KEY `et_ophtrintravitinjection_postinject_ev_fk` (`event_id`),
			  KEY `et_ophtrintravitinjection_postinject_eye_id_fk` (`eye_id`),
			  KEY `et_ophtrintravitinjection_postinject_ldrops_id_fk` (`left_drops_id`),
			  KEY `et_ophtrintravitinjection_postinject_rdrops_id_fk` (`right_drops_id`),
			  CONSTRAINT `et_ophtrintravitinjection_postinject_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_postinject_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_postinject_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_postinject_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_postinject_ldrops_id_fk` FOREIGN KEY (`left_drops_id`) REFERENCES `ophtrintravitinjection_postinjection_drops` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_postinject_rdrops_id_fk` FOREIGN KEY (`right_drops_id`) REFERENCES `ophtrintravitinjection_postinjection_drops` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophtrintravitinjection_site` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `site_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtrintravitinjection_site_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtrintravitinjection_site_cui_fk` (`created_user_id`),
			  KEY `et_ophtrintravitinjection_site_ev_fk` (`event_id`),
			  KEY `et_ophtrintravitinjection_site_site_id_fk` (`site_id`),
			  CONSTRAINT `et_ophtrintravitinjection_site_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_site_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_site_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_site_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophtrintravitinjection_treatment` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '3',
			  `left_pre_antisept_drug_id` int(10) unsigned DEFAULT NULL,
			  `left_pre_skin_drug_id` int(10) unsigned DEFAULT NULL,
			  `left_pre_ioplowering_required` tinyint(1) DEFAULT NULL,
			  `left_drug_id` int(10) unsigned DEFAULT NULL,
			  `right_drug_id` int(10) unsigned DEFAULT NULL,
			  `left_number` int(10) unsigned DEFAULT NULL,
			  `left_batch_number` varchar(255) DEFAULT '',
			  `left_batch_expiry_date` date DEFAULT NULL,
			  `left_injection_given_by_id` int(10) unsigned DEFAULT NULL,
			  `left_injection_time` time DEFAULT NULL,
			  `left_post_ioplowering_required` tinyint(1) DEFAULT NULL,
			  `right_pre_antisept_drug_id` int(10) unsigned DEFAULT NULL,
			  `right_pre_skin_drug_id` int(10) unsigned DEFAULT NULL,
			  `right_pre_ioplowering_required` tinyint(1) DEFAULT NULL,
			  `right_number` int(10) unsigned DEFAULT NULL,
			  `right_batch_number` varchar(255) DEFAULT '',
			  `right_batch_expiry_date` date DEFAULT NULL,
			  `right_injection_given_by_id` int(10) unsigned DEFAULT NULL,
			  `right_injection_time` time DEFAULT NULL,
			  `right_post_ioplowering_required` tinyint(1) DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtrintravitinjection_treatment_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtrintravitinjection_treatment_cui_fk` (`created_user_id`),
			  KEY `et_ophtrintravitinjection_treatment_ev_fk` (`event_id`),
			  KEY `et_ophtrintravitinjection_treatment_eye_id_fk` (`eye_id`),
			  KEY `et_ophtrintravitinjection_treatment_lprad_id_fk` (`left_pre_antisept_drug_id`),
			  KEY `et_ophtrintravitinjection_treatment_lprsd_id_fk` (`left_pre_skin_drug_id`),
			  KEY `ophtrintravitinjection_treatment_ldrug_fk` (`left_drug_id`),
			  KEY `et_ophtrintravitinjection_treatment_linjection_given_by_id_fk` (`left_injection_given_by_id`),
			  KEY `et_ophtrintravitinjection_treatment_rprad_id_fk` (`right_pre_antisept_drug_id`),
			  KEY `et_ophtrintravitinjection_treatment_rprsd_id_fk` (`right_pre_skin_drug_id`),
			  KEY `ophtrintravitinjection_treatment_rdrug_fk` (`right_drug_id`),
			  KEY `et_ophtrintravitinjection_treatment_rinjection_given_by_id_fk` (`right_injection_given_by_id`),
			  CONSTRAINT `et_ophtrintravitinjection_treatment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_treatment_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_treatment_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_treatment_linjection_given_by_id_fk` FOREIGN KEY (`left_injection_given_by_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_treatment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_treatment_lprad_id_fk` FOREIGN KEY (`left_pre_antisept_drug_id`) REFERENCES `ophtrintravitinjection_antiseptic_drug` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_treatment_lprsd_id_fk` FOREIGN KEY (`left_pre_skin_drug_id`) REFERENCES `ophtrintravitinjection_skin_drug` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_treatment_rinjection_given_by_id_fk` FOREIGN KEY (`right_injection_given_by_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_treatment_rprad_id_fk` FOREIGN KEY (`right_pre_antisept_drug_id`) REFERENCES `ophtrintravitinjection_antiseptic_drug` (`id`),
			  CONSTRAINT `et_ophtrintravitinjection_treatment_rprsd_id_fk` FOREIGN KEY (`right_pre_skin_drug_id`) REFERENCES `ophtrintravitinjection_skin_drug` (`id`),
			  CONSTRAINT `ophtrintravitinjection_treatment_ldrug_fk` FOREIGN KEY (`left_drug_id`) REFERENCES `ophtrintravitinjection_treatment_drug` (`id`),
			  CONSTRAINT `ophtrintravitinjection_treatment_rdrug_fk` FOREIGN KEY (`right_drug_id`) REFERENCES `ophtrintravitinjection_treatment_drug` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrintravitinjection_anaestheticagent` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `anaesthetic_agent_id` int(10) unsigned NOT NULL,
			  `display_order` int(10) unsigned NOT NULL,
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrintravitinjection_anaestheticagent_ti_fk` (`anaesthetic_agent_id`),
			  KEY `ophtrintravitinjection_anaestheticagent_cui_fk` (`created_user_id`),
			  KEY `ophtrintravitinjection_anaestheticagent_lmui_fk` (`last_modified_user_id`),
			  CONSTRAINT `ophtrintravitinjection_anaestheticagent_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_anaestheticagent_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_anaestheticagent_ti_fk` FOREIGN KEY (`anaesthetic_agent_id`) REFERENCES `anaesthetic_agent` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrintravitinjection_anaestheticdelivery` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `anaesthetic_delivery_id` int(10) unsigned NOT NULL,
			  `display_order` int(10) unsigned NOT NULL,
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrintravitinjection_anaestheticdelivery_di_fk` (`anaesthetic_delivery_id`),
			  KEY `ophtrintravitinjection_anaestheticdelivery_cui_fk` (`created_user_id`),
			  KEY `ophtrintravitinjection_anaestheticdelivery_lmui_fk` (`last_modified_user_id`),
			  CONSTRAINT `ophtrintravitinjection_anaestheticdelivery_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_anaestheticdelivery_di_fk` FOREIGN KEY (`anaesthetic_delivery_id`) REFERENCES `anaesthetic_delivery` (`id`),
			  CONSTRAINT `ophtrintravitinjection_anaestheticdelivery_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrintravitinjection_anaesthetictype` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `anaesthetic_type_id` int(10) unsigned NOT NULL,
			  `display_order` int(10) unsigned NOT NULL,
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrintravitinjection_anaesthetictype_ti_fk` (`anaesthetic_type_id`),
			  KEY `ophtrintravitinjection_anaesthetictype_cui_fk` (`created_user_id`),
			  KEY `ophtrintravitinjection_anaesthetictype_lmui_fk` (`last_modified_user_id`),
			  CONSTRAINT `ophtrintravitinjection_anaesthetictype_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_anaesthetictype_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_anaesthetictype_ti_fk` FOREIGN KEY (`anaesthetic_type_id`) REFERENCES `anaesthetic_type` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrintravitinjection_antiseptic_drug` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrintravitinjection_antiseptic_drug_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtrintravitinjection_antiseptic_drug_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtrintravitinjection_antiseptic_drug_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_antiseptic_drug_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrintravitinjection_complicat` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `default` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `description_required` tinyint(1) NOT NULL DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrintravitinjection_complicat_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtrintravitinjection_complicat_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtrintravitinjection_complicat_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_complicat_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrintravitinjection_complicat_assignment` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `element_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '3',
			  `complication_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrintravitinjection_complicat_assignment_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtrintravitinjection_complicat_assignment_cui_fk` (`created_user_id`),
			  KEY `ophtrintravitinjection_complicat_assignment_ele_fk` (`element_id`),
			  KEY `ophtrintravitinjection_complicat_assign_eye_id_fk` (`eye_id`),
			  KEY `ophtrintravitinjection_complicat_assignment_lku_fk` (`complication_id`),
			  CONSTRAINT `ophtrintravitinjection_complicat_assignment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_complicat_assignment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_complicat_assignment_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophtrintravitinjection_complications` (`id`),
			  CONSTRAINT `ophtrintravitinjection_complicat_assign_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
			  CONSTRAINT `ophtrintravitinjection_complicat_assignment_lku_fk` FOREIGN KEY (`complication_id`) REFERENCES `ophtrintravitinjection_complicat` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrintravitinjection_injectionuser` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(10) unsigned NOT NULL,
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrintravitinjection_injectionuser_ui_fk` (`user_id`),
			  KEY `ophtrintravitinjection_injectionuser_cui_fk` (`created_user_id`),
			  KEY `ophtrintravitinjection_injectionuser_lmui_fk` (`last_modified_user_id`),
			  CONSTRAINT `ophtrintravitinjection_injectionuser_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_injectionuser_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_injectionuser_ui_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrintravitinjection_ioplowering` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrintravitinjection_ioplowering_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtrintravitinjection_ioplowering_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtrintravitinjection_ioplowering_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_ioplowering_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrintravitinjection_ioplowering_assign` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `element_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '3',
			  `ioplowering_id` int(10) unsigned NOT NULL,
			  `is_pre` tinyint(1) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrintravitinjection_ioplowering_assign_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtrintravitinjection_ioplowering_assign_cui_fk` (`created_user_id`),
			  KEY `ophtrintravitinjection_ioplowering_assign_ele_fk` (`element_id`),
			  KEY `ophtrintravitinjection_ioplowering_assign_eye_id_fk` (`eye_id`),
			  KEY `ophtrintravitinjection_ioplowering_assign_lku_fk` (`ioplowering_id`),
			  CONSTRAINT `ophtrintravitinjection_ioplowering_assign_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_ioplowering_assign_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_ioplowering_assign_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophtrintravitinjection_treatment` (`id`),
			  CONSTRAINT `ophtrintravitinjection_ioplowering_assign_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
			  CONSTRAINT `ophtrintravitinjection_ioplowering_assign_lku_fk` FOREIGN KEY (`ioplowering_id`) REFERENCES `ophtrintravitinjection_ioplowering` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrintravitinjection_lens_status` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `default_distance` decimal(2,1) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrintravitinjection_lens_status_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtrintravitinjection_lens_status_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtrintravitinjection_lens_status_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_lens_status_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrintravitinjection_postinjection_drops` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrintravitinjection_postinjection_drops_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtrintravitinjection_postinjection_drops_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtrintravitinjection_postinjection_drops_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_postinjection_drops_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrintravitinjection_skin_drug` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrintravitinjection_skin_drug_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtrintravitinjection_skin_drug_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtrintravitinjection_skin_drug_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_skin_drug_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrintravitinjection_treatment_drug` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `available` tinyint(1) NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrintravitinjection_treatment_drug_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtrintravitinjection_treatment_drug_cui_fk` (`created_user_id`),
			  CONSTRAINT `ophtrintravitinjection_treatment_drug_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrintravitinjection_treatment_drug_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
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
