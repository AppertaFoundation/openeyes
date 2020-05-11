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
class m130913_000002_consolidation_for_ophcocorrespondence extends OEMigration
{
    public function up()
    {
        if (!$this->consolidate(
            array(
                'm120510_102418_ophcocorrespondence_consolidated',
                'm120515_085148_add_locked_field_to_letter_element',
                'm120515_122109_store_site_id_with_letter',
                'm120515_134047_store_previously_edited_letters',
                'm120613_090046_letter_macro_should_have_a_site_id_column',
                'm120613_090658_letter_string_site_id_field',
                'm120625_121556_add_missing_site_id_foreign_key',
                'm120625_122414_add_missing_site_id_foreign_key',
                'm120821_092310_firm_site_secretary_table',
                'm120828_140031_enable_macro_cc_to_gp',
                'm120920_071943_add_direct_line_number_to_correspondence_element',
                'm121025_104309_findings_letter_string_group',
                'm121105_095011_add_findings_options_for_history_and_adnexal_comorbidity',
                'm121108_132537_letter_enc_list',
                'm121128_103043_firm_secretary_fax_numbers',
                'm121128_110606_add_fax_field_to_letter_element',
                'm121129_145842_clinic_date',
                'm130423_135641_print_all_flag',
                'm130531_134251_mark_as_support_service_event_type',
                'm130603_103105_dr_function_setup',
            )
        )
        ) {
            $this->createTables();
        }
    }

    public function down()
    {
        echo "You cannot migrate down past a consolidation migration\n";

        return false;
    }

    public function safeUp()
    {
        $this->up();
    }

    public function safeDown()
    {
        $this->down();
    }

    protected function createTables()
    {
        //disable foreign keys check
        $this->execute('SET foreign_key_checks = 0');

        Yii::app()->cache->flush();

        $event_group_id = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('code = ?', array('Co'))->queryScalar();
        $this->insert(
            'event_type',
            array(
                'name' => 'Correspondence',
                'event_group_id' => $event_group_id,
                'class_name' => 'OphCoCorrespondence',
                'support_services' => 1,
            )
        );
        $event_type_id = $this->dbConnection->getLastInsertID();

        $element_types = array(
            'ElementLetter' => array('name' => 'Letter', 'display_order' => 1),
        );

        $this->insertOEElementType($element_types, $event_type_id);

        $this->execute("CREATE TABLE `et_ophcocorrespondence_firm_letter_macro` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `firm_id` int(10) unsigned NOT NULL,
			  `name` varchar(64) DEFAULT NULL,
			  `recipient_patient` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `recipient_doctor` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `use_nickname` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `body` text,
			  `cc_patient` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `display_order` tinyint(3) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `episode_status_id` int(10) unsigned DEFAULT NULL,
			  `cc_doctor` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcocorrespondence_flm_firm_id_fk` (`firm_id`),
			  KEY `et_ophcocorrespondence_flm_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophcocorrespondence_flm_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `et_ophcocorrespondence_flm_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_flm_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_flm_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophcocorrespondence_firm_letter_string` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `letter_string_group_id` int(10) unsigned NOT NULL,
			  `firm_id` int(10) unsigned NOT NULL,
			  `name` varchar(64) DEFAULT NULL,
			  `body` text,
			  `display_order` tinyint(3) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `event_type` varchar(64) DEFAULT NULL,
			  `element_type` varchar(64) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `et_ophcocorrespondence_fls_letter_string_group_id_fk` (`letter_string_group_id`),
			  KEY `et_ophcocorrespondence_fls_firm_id_fk` (`firm_id`),
			  KEY `et_ophcocorrespondence_fls_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophcocorrespondence_fls_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `et_ophcocorrespondence_fls_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_fls_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_fls_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_fls_letter_string_group_id_fk` FOREIGN KEY (`letter_string_group_id`) REFERENCES `et_ophcocorrespondence_letter_string_group` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophcocorrespondence_firm_site_secretary` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `firm_id` int(10) unsigned NOT NULL,
			  `site_id` int(10) unsigned DEFAULT NULL,
			  `direct_line` varchar(64) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `fax` varchar(64) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `et_ophcocorrespondence_fss_firm_id_fk` (`firm_id`),
			  KEY `et_ophcocorrespondence_fss_site_id_fk` (`site_id`),
			  KEY `et_ophcocorrespondence_fss_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophcocorrespondence_fss_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `et_ophcocorrespondence_fss_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_fss_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_fss_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_fss_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophcocorrespondence_letter` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `use_nickname` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `date` datetime NOT NULL,
			  `address` varchar(1024) DEFAULT NULL,
			  `introduction` varchar(255) DEFAULT NULL,
			  `re` varchar(1024) DEFAULT NULL,
			  `body` text,
			  `footer` varchar(2048) DEFAULT NULL,
			  `cc` text,
			  `draft` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `print` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `site_id` int(10) NOT NULL,
			  `direct_line` varchar(32) DEFAULT NULL,
			  `fax` varchar(64) NOT NULL,
			  `clinic_date` date DEFAULT NULL,
			  `print_all` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcocorrespondence_letter_event_id_fk` (`event_id`),
			  KEY `et_ophcocorrespondence_letter_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophcocorrespondence_letter_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `et_ophcocorrespondence_letter_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_letter_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_letter_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophcocorrespondence_letter_macro` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(64) DEFAULT NULL,
			  `recipient_patient` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `recipient_doctor` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `use_nickname` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `body` text,
			  `cc_patient` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `display_order` tinyint(3) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `episode_status_id` int(10) unsigned DEFAULT NULL,
			  `site_id` int(10) unsigned NOT NULL,
			  `cc_doctor` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcocorrespondence_lm_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophcocorrespondence_lm_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophcocorrespondence_lm_site_id_fk` (`site_id`),
			  CONSTRAINT `et_ophcocorrespondence_lm_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_lm_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_lm_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophcocorrespondence_letter_old` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `letter_id` int(10) unsigned NOT NULL,
			  `use_nickname` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `date` datetime NOT NULL,
			  `address` varchar(1024) DEFAULT NULL,
			  `introduction` varchar(255) DEFAULT NULL,
			  `re` varchar(1024) DEFAULT NULL,
			  `body` text,
			  `footer` varchar(2048) DEFAULT NULL,
			  `cc` text,
			  `draft` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `print` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `site_id` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `et_ophcocorrespondence_letter_old_letter_id_fk` (`letter_id`),
			  KEY `et_ophcocorrespondence_letter_old_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophcocorrespondence_letter_old_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophcocorrespondence_letter_old_site_id_fk` (`site_id`),
			  CONSTRAINT `et_ophcocorrespondence_letter_old_letter_id_fk` FOREIGN KEY (`letter_id`) REFERENCES `et_ophcocorrespondence_letter` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_letter_old_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_letter_old_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_letter_old_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophcocorrespondence_letter_string` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `letter_string_group_id` int(10) unsigned NOT NULL,
			  `name` varchar(64) DEFAULT NULL,
			  `body` text,
			  `display_order` tinyint(3) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `site_id` int(10) unsigned NOT NULL,
			  `event_type` varchar(64) DEFAULT NULL,
			  `element_type` varchar(64) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `et_ophcocorrespondence_ls2_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophcocorrespondence_ls2_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophcocorrespondence_ls2_letter_string_group_id_fk` (`letter_string_group_id`),
			  KEY `et_ophcocorrespondence_ls2_created_site_id_fk` (`site_id`),
			  CONSTRAINT `et_ophcocorrespondence_ls2_created_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_ls2_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_ls2_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_ls2_letter_string_group_id_fk` FOREIGN KEY (`letter_string_group_id`) REFERENCES `et_ophcocorrespondence_letter_string_group` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophcocorrespondence_letter_string_group` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(64) DEFAULT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcocorrespondence_lsg_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophcocorrespondence_lsg_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `et_ophcocorrespondence_lsg_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_lsg_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophcocorrespondence_subspecialty_letter_macro` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `subspecialty_id` int(10) unsigned NOT NULL,
			  `name` varchar(64) DEFAULT NULL,
			  `recipient_patient` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `recipient_doctor` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `use_nickname` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `body` text,
			  `cc_patient` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `display_order` tinyint(3) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `episode_status_id` int(10) unsigned DEFAULT NULL,
			  `cc_doctor` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `et_ophcocorrespondence_slm2_subspecialty_id_fk` (`subspecialty_id`),
			  KEY `et_ophcocorrespondence_slm2_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophcocorrespondence_slm2_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `et_ophcocorrespondence_slm2_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_slm2_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_slm2_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophcocorrespondence_subspecialty_letter_string` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `letter_string_group_id` int(10) unsigned NOT NULL,
			  `subspecialty_id` int(10) unsigned NOT NULL,
			  `name` varchar(64) DEFAULT NULL,
			  `body` text,
			  `display_order` tinyint(3) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `event_type` varchar(64) DEFAULT NULL,
			  `element_type` varchar(64) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `et_ophcocorrespondence_sls_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophcocorrespondence_sls_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophcocorrespondence_sls_letter_string_group_id_fk` (`letter_string_group_id`),
			  KEY `et_ophcocorrespondence_sls_subspecialty_id_fk` (`subspecialty_id`),
			  CONSTRAINT `et_ophcocorrespondence_sls_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_sls_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_sls_letter_string_group_id_fk` FOREIGN KEY (`letter_string_group_id`) REFERENCES `et_ophcocorrespondence_letter_string_group` (`id`),
			  CONSTRAINT `et_ophcocorrespondence_sls_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophcocorrespondence_letter_enclosure` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `element_letter_id` int(10) unsigned NOT NULL,
			  `content` varchar(128) DEFAULT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophcocorrespondence_letter_enclosure_element_letter_id_fk` (`element_letter_id`),
			  KEY `ophcocorrespondence_letter_enclosure_lmiu_fk` (`last_modified_user_id`),
			  KEY `ophcocorrespondence_letter_enclosure_cu_fk` (`created_user_id`),
			  CONSTRAINT `ophcocorrespondence_letter_enclosure_element_letter_id_fk` FOREIGN KEY (`element_letter_id`) REFERENCES `et_ophcocorrespondence_letter` (`id`),
			  CONSTRAINT `ophcocorrespondence_letter_enclosure_lmiu_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophcocorrespondence_letter_enclosure_cu_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);

        //enable foreign keys check
        $this->execute('SET foreign_key_checks = 1');
    }
}
