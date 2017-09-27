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
class m130913_000012_consolidation_for_ophtrlaser extends OEMigration
{
    private $element_types;

    public function setData()
    {
        $this->element_types = array(
            'Element_OphTrLaser_Site' => array('name' => 'Site', 'display_order' => 1, 'required' => 1),
            'Element_OphTrLaser_Treatment' => array('name' => 'Treatment', 'display_order' => 2, 'required' => 1),
            'Element_OphTrLaser_AnteriorSegment' => array('name' => 'Anterior Segment', 'display_order' => 3, 'parent_element_type_id' => 'Element_OphTrLaser_Treatment', 'required' => 0, 'default' => 0),
            'Element_OphTrLaser_PosteriorPole' => array('name' => 'Posterior Pole', 'display_order' => 4, 'parent_element_type_id' => 'Element_OphTrLaser_Treatment', 'required' => 0, 'default' => 0),
            'Element_OphTrLaser_Fundus' => array('name' => 'Fundus', 'display_order' => 5, 'parent_element_type_id' => 'Element_OphTrLaser_Treatment', 'required' => 0, 'default' => 0),
            'Element_OphTrLaser_Comments' => array('name' => 'Comments', 'display_order' => 6, 'required' => 0, 'default' => 0),
        );
    }

    public function up()
    {
        if (!$this->consolidate(
            array(
                'm130408_140240_event_type_OphTrLaser',
                'm130712_113449_site_laser_table_isnt_element',
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

        $event_type_id = $this->insertOEEventType('Laser', 'OphTrLaser', 'Tr');
        $this->insertOEElementType($this->element_types, $event_type_id);

        $this->execute("CREATE TABLE `et_ophtrlaser_anteriorseg` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `left_eyedraw` varchar(4096) NOT NULL,
			  `right_eyedraw` varchar(4096) NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '3',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtrlaser_anteriorseg_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtrlaser_anteriorseg_cui_fk` (`created_user_id`),
			  KEY `et_ophtrlaser_anteriorseg_ev_fk` (`event_id`),
			  KEY `et_ophtrlaser_anteriorseg_eye_id_fk` (`eye_id`),
			  CONSTRAINT `et_ophtrlaser_anteriorseg_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrlaser_anteriorseg_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrlaser_anteriorseg_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtrlaser_anteriorseg_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophtrlaser_comments` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `comments` text,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtrlaser_comments_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtrlaser_comments_cui_fk` (`created_user_id`),
			  KEY `et_ophtrlaser_comments_ev_fk` (`event_id`),
			  CONSTRAINT `et_ophtrlaser_comments_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrlaser_comments_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrlaser_comments_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophtrlaser_fundus` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '3',
			  `left_eyedraw` varchar(4096) NOT NULL,
			  `right_eyedraw` varchar(4096) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtrlaser_fundus_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtrlaser_fundus_cui_fk` (`created_user_id`),
			  KEY `et_ophtrlaser_fundus_ev_fk` (`event_id`),
			  KEY `et_ophtrlaser_fundus_eye_id_fk` (`eye_id`),
			  CONSTRAINT `et_ophtrlaser_fundus_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrlaser_fundus_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrlaser_fundus_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtrlaser_fundus_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophtrlaser_posteriorpo` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '3',
			  `left_eyedraw` varchar(4096) NOT NULL,
			  `right_eyedraw` varchar(4096) NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtrlaser_posteriorpo_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtrlaser_posteriorpo_cui_fk` (`created_user_id`),
			  KEY `et_ophtrlaser_posteriorpo_ev_fk` (`event_id`),
			  KEY `et_ophtrlaser_posteriorpo_eye_id_fk` (`eye_id`),
			  CONSTRAINT `et_ophtrlaser_posteriorpo_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrlaser_posteriorpo_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrlaser_posteriorpo_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtrlaser_posteriorpo_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophtrlaser_site` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `site_id` int(10) unsigned NOT NULL,
			  `laser_id` int(10) unsigned NOT NULL,
			  `surgeon_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtrlaser_site_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtrlaser_site_cui_fk` (`created_user_id`),
			  KEY `et_ophtrlaser_site_ev_fk` (`event_id`),
			  KEY `et_ophtrlaser_site_site_fk` (`site_id`),
			  KEY `et_ophtrlaser_site_laser_fk` (`laser_id`),
			  KEY `et_ophtrlaser_site_surgeon_id_fk` (`surgeon_id`),
			  CONSTRAINT `et_ophtrlaser_site_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrlaser_site_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrlaser_site_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtrlaser_site_site_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
			  CONSTRAINT `et_ophtrlaser_site_laser_fk` FOREIGN KEY (`laser_id`) REFERENCES `ophtrlaser_site_laser` (`id`),
			  CONSTRAINT `et_ophtrlaser_site_surgeon_id_fk` FOREIGN KEY (`surgeon_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophtrlaser_treatment` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL DEFAULT '3',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtrlaser_treatment_lmui_fk` (`last_modified_user_id`),
			  KEY `et_ophtrlaser_treatment_cui_fk` (`created_user_id`),
			  KEY `et_ophtrlaser_treatment_ev_fk` (`event_id`),
			  KEY `et_ophtrlaser_treatment_eye_id_fk` (`eye_id`),
			  CONSTRAINT `et_ophtrlaser_treatment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrlaser_treatment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtrlaser_treatment_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtrlaser_treatment_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrlaser_laserprocedure` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `procedure_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrlaser_laserprocedure_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtrlaser_laserprocedure_cui_fk` (`created_user_id`),
			  KEY `ophtrlaser_laserprocedure_proc_fk` (`procedure_id`),
			  CONSTRAINT `ophtrlaser_laserprocedure_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrlaser_laserprocedure_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrlaser_laserprocedure_proc_fk` FOREIGN KEY (`procedure_id`) REFERENCES `proc` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrlaser_laserprocedure_assignment` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `procedure_id` int(10) unsigned NOT NULL,
			  `treatment_id` int(10) unsigned NOT NULL,
			  `eye_id` int(10) unsigned NOT NULL,
			  `display_order` tinyint(3) unsigned DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrlaser_laserprocedure_assignment_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtrlaser_laserprocedure_assignment_cui_fk` (`created_user_id`),
			  KEY `ophtrlaser_laserprocedure_assignment_proc_fk` (`procedure_id`),
			  KEY `ophtrlaser_laserprocedure_assignment_tr_fk` (`treatment_id`),
			  KEY `ophtrlaser_laserprocedure_assignment_eye_id_fk` (`eye_id`),
			  CONSTRAINT `ophtrlaser_laserprocedure_assignment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrlaser_laserprocedure_assignment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrlaser_laserprocedure_assignment_proc_fk` FOREIGN KEY (`procedure_id`) REFERENCES `proc` (`id`),
			  CONSTRAINT `ophtrlaser_laserprocedure_assignment_tr_fk` FOREIGN KEY (`treatment_id`) REFERENCES `et_ophtrlaser_treatment` (`id`),
			  CONSTRAINT `ophtrlaser_laserprocedure_assignment_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophtrlaser_site_laser` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128) NOT NULL,
			  `type` varchar(128) DEFAULT NULL,
			  `wavelength` int(10) unsigned DEFAULT NULL,
			  `display_order` int(10) unsigned NOT NULL DEFAULT '1',
			  `site_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtrlaser_site_laser_lmui_fk` (`last_modified_user_id`),
			  KEY `ophtrlaser_site_laser_cui_fk` (`created_user_id`),
			  KEY `ophtrlaser_site_laser_site_fk` (`site_id`),
			  CONSTRAINT `ophtrlaser_site_laser_site_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
			  CONSTRAINT `ophtrlaser_site_laser_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtrlaser_site_laser_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
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
