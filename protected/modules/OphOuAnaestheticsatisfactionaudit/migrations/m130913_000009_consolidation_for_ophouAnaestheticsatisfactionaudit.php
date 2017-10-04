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
class m130913_000009_consolidation_for_ophouAnaestheticsatisfactionaudit extends OEMigration
{
    private $element_types;

    public function setData()
    {
        $this->element_types = array(
            'Element_OphOuAnaestheticsatisfactionaudit_Anaesthetist' => array('name' => 'Anaesthetist', 'display_order' => 10),
            'Element_OphOuAnaestheticsatisfactionaudit_Satisfaction' => array('name' => 'Satisfaction', 'display_order' => 20),
            'Element_OphOuAnaestheticsatisfactionaudit_VitalSigns' => array('name' => 'Vital Signs', 'display_order' => 30),
            'Element_OphOuAnaestheticsatisfactionaudit_Notes' => array('name' => 'Notes', 'display_order' => 40),
        );
    }

    public function up()
    {
        if (!$this->consolidate(
            array(
                'm121010_085427_event_type_OphAuAnaestheticsatisfactionaudit',
                'm121010_124852_create_anaesthetist_table',
                'm121011_093438_event_type_OphAuAnaestheticsatisfactionaudit',
                'm121011_161035_add_null_anaesthetist_options',
                'm121016_152255_remove_ramsay',
                'm121016_155738_fix_avpu',
                'm121016_164501_add_notes_element',
                'm121017_144604_change_to_outcomes_type',
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

        $event_type_id = $this->insertOEEventType('Anaesthetic Satisfaction Audit', 'OphOuAnaestheticsatisfactionaudit', 'Ou');
        $this->insertOEElementType($this->element_types, $event_type_id);

        $this->execute("CREATE TABLE `et_ophouanaestheticsataudit_anaesthetis` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `anaesthetist_id` int(10) unsigned DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `non_consultant` tinyint(1) NOT NULL DEFAULT '0',
			  `no_anaesthetist` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `et_ophauanaestheticsataudit_anaesthetis_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophauanaestheticsataudit_anaesthetis_cui_fk` (`created_user_id`),
			  KEY `et_ophauanaestheticsataudit_anaesthetis_ev_fk` (`event_id`),
			  KEY `et_ophauanaestheticsataudit_anaesthetis_anaesthetist_id_fk` (`anaesthetist_id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_anaesthetis_anaesthetist_id_fk` FOREIGN KEY (`anaesthetist_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_anaesthetis_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_anaesthetis_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_anaesthetis_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophouanaestheticsataudit_anaesthetist_lookup` (
			  `user_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`user_id`),
			  KEY `et_ophauanaestheticsataudit_anaesthetist_lookup_last_mod_user_fk` (`last_modified_user_id`),
			  KEY `et_ophauanaestheticsataudit_anaesthetist_lookup_created_user_fk` (`created_user_id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_anaesthetist_lookup_created_user_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_anaesthetist_lookup_last_mod_user_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_anaesthetist_lookup_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophouanaestheticsataudit_notes` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `comments` text,
			  `ready_for_discharge_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_auophanaestheticsataudit_notes_lmui_fk` (`last_modified_user_id`),
			  KEY `et_auophanaestheticsataudit_notes_cui_fk` (`created_user_id`),
			  KEY `et_auophanaestheticsataudit_notes_ev_fk` (`event_id`),
			  KEY `et_auophanaestheticsataudit_notes_ready_for_discharge_fk` (`ready_for_discharge_id`),
			  CONSTRAINT `et_auophanaestheticsataudit_notes_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_auophanaestheticsataudit_notes_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_auophanaestheticsataudit_notes_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_auophanaestheticsataudit_notes_ready_for_discharge_fk` FOREIGN KEY (`ready_for_discharge_id`) REFERENCES `et_ophouanaestheticsataudit_notes_ready_for_discharge` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophouanaestheticsataudit_notes_ready_for_discharge` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_auophanaestheticsataudit_notes_ready_for_discharge_lmui_fk` (`last_modified_user_id`),
			  KEY `et_auophanaestheticsataudit_notes_ready_for_discharge_cui_fk` (`created_user_id`),
			  CONSTRAINT `et_auophanaestheticsataudit_notes_ready_for_discharge_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_auophanaestheticsataudit_notes_ready_for_discharge_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophouanaestheticsataudit_satisfactio` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `pain` int(10) NOT NULL,
			  `nausea` int(10) NOT NULL,
			  `vomited` tinyint(1) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophauanaestheticsataudit_satisfactio_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophauanaestheticsataudit_satisfactio_cui_fk` (`created_user_id`),
			  KEY `et_ophauanaestheticsataudit_satisfactio_ev_fk` (`event_id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_satisfactio_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_satisfactio_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_satisfactio_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophouanaestheticsataudit_vitalsigns` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `respiratory_rate_id` int(10) unsigned NOT NULL,
			  `oxygen_saturation_id` int(10) unsigned NOT NULL,
			  `systolic_id` int(10) unsigned NOT NULL,
			  `body_temp_id` int(10) unsigned NOT NULL,
			  `heart_rate_id` int(10) unsigned NOT NULL,
			  `conscious_lvl_id` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_cui_fk` (`created_user_id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_ev_fk` (`event_id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_respiratory_rate_fk` (`respiratory_rate_id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_oxygen_saturation_fk` (`oxygen_saturation_id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_systolic_fk` (`systolic_id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_body_temp_fk` (`body_temp_id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_heart_rate_fk` (`heart_rate_id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_conscious_lvl_fk` (`conscious_lvl_id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_conscious_lvl_fk` FOREIGN KEY (`conscious_lvl_id`) REFERENCES `et_ophouanaestheticsataudit_vitalsigns_conscious_lvl` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_body_temp_fk` FOREIGN KEY (`body_temp_id`) REFERENCES `et_ophouanaestheticsataudit_vitalsigns_body_temp` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_heart_rate_fk` FOREIGN KEY (`heart_rate_id`) REFERENCES `et_ophouanaestheticsataudit_vitalsigns_heart_rate` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_oxygen_saturation_fk` FOREIGN KEY (`oxygen_saturation_id`) REFERENCES `et_ophouanaestheticsataudit_vitalsigns_oxygen_saturation` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_respiratory_rate_fk` FOREIGN KEY (`respiratory_rate_id`) REFERENCES `et_ophouanaestheticsataudit_vitalsigns_respiratory_rate` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_systolic_fk` FOREIGN KEY (`systolic_id`) REFERENCES `et_ophouanaestheticsataudit_vitalsigns_systolic` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophouanaestheticsataudit_vitalsigns_body_temp` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `score` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_body_temp_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_body_temp_cui_fk` (`created_user_id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_body_temp_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_body_temp_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophouanaestheticsataudit_vitalsigns_conscious_lvl` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `score` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_conscious_lvl_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_conscious_lvl_cui_fk` (`created_user_id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_conscious_lvl_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_conscious_lvl_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophouanaestheticsataudit_vitalsigns_heart_rate` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `score` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_heart_rate_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_heart_rate_cui_fk` (`created_user_id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_heart_rate_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_heart_rate_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophouanaestheticsataudit_vitalsigns_oxygen_saturation` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `score` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_oxygen_saturation_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_oxygen_saturation_cui_fk` (`created_user_id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_oxygen_saturation_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_oxygen_saturation_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophouanaestheticsataudit_vitalsigns_respiratory_rate` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `score` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_respiratory_rate_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_respiratory_rate_cui_fk` (`created_user_id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_respiratory_rate_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_respiratory_rate_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophouanaestheticsataudit_vitalsigns_systolic` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `score` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_systolic_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophauanaestheticsataudit_vitalsigns_systolic_cui_fk` (`created_user_id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_systolic_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophauanaestheticsataudit_vitalsigns_systolic_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
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
