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
class m130913_000006_consolidation_for_ophciphasing extends OEMigration
{
    private $element_types;

    public function setData()
    {
        $this->element_types = array(
            'Element_OphCiPhasing_IntraocularPressure' => array('name' => 'Intraocular Pressure Phasing'),
        );
    }

    public function up()
    {
        if (!$this->consolidate(
            array(
                'm130218_153000_initial_migration',
                'm130321_143218_dilated_default_to_no',
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

        $event_type_id = $this->insertOEEventType('Phasing', 'OphCiPhasing', 'Ci');
        $this->insertOEElementType($this->element_types, $event_type_id);

        $this->execute("CREATE TABLE `et_ophciphasing_intraocularpressure` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `eye_id` int(10) unsigned DEFAULT '3',
			  `left_instrument_id` int(10) unsigned DEFAULT NULL,
			  `right_instrument_id` int(10) unsigned DEFAULT NULL,
			  `left_comments` text,
			  `right_comments` text,
			  `left_dilated` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `right_dilated` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `et_ophciphasing_intraocularpressure_e_id_fk` (`event_id`),
			  KEY `et_ophciphasing_intraocularpressure_c_u_id_fk` (`created_user_id`),
			  KEY `et_ophciphasing_intraocularpressure_l_m_u_id_fk` (`last_modified_user_id`),
			  KEY `et_ophciphasing_intraocularpressure_eye_fk` (`eye_id`),
			  KEY `et_ophciphasing_intraocularpressure_li_fk` (`left_instrument_id`),
			  KEY `et_ophciphasing_intraocularpressure_ri_fk` (`right_instrument_id`),
			  CONSTRAINT `et_ophciphasing_intraocularpressure_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophciphasing_intraocularpressure_eye_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
			  CONSTRAINT `et_ophciphasing_intraocularpressure_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophciphasing_intraocularpressure_li_fk` FOREIGN KEY (`left_instrument_id`) REFERENCES `ophciphasing_instrument` (`id`),
			  CONSTRAINT `et_ophciphasing_intraocularpressure_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophciphasing_intraocularpressure_ri_fk` FOREIGN KEY (`right_instrument_id`) REFERENCES `ophciphasing_instrument` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciphasing_instrument` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NOT NULL,
			  `display_order` int(10) unsigned DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophciphasing_instrument_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophciphasing_instrument_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophciphasing_instrument_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophciphasing_instrument_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciphasing_reading` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `value` int(10) unsigned DEFAULT NULL,
			  `side` tinyint(1) unsigned NOT NULL,
			  `element_id` int(10) unsigned NOT NULL,
			  `measurement_timestamp` time DEFAULT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophciphasing_reading_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophciphasing_reading_created_user_id_fk` (`created_user_id`),
			  KEY `ophciphasing_reading_element_id_fk` (`element_id`),
			  CONSTRAINT `ophciphasing_reading_element_id_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophciphasing_intraocularpressure` (`id`),
			  CONSTRAINT `ophciphasing_reading_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophciphasing_reading_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
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
