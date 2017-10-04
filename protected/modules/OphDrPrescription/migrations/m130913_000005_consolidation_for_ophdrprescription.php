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
class m130913_000005_consolidation_for_ophdrprescription extends OEMigration
{
    private $element_types;

    public function setData()
    {
        $this->element_types = array(
            'Element_OphDrPrescription_Details' => array('name' => 'Details'),
        );
    }

    public function up()
    {
        if (!$this->consolidate(
            array(
                'm120423_114231_initial_migration_for_ophdrprescription',
                'm120510_152211_add_metadata_to_prescription_items',
                'm120515_174700_prescription_item_route_options',
                'm120516_164500_tapering',
                'm120529_142100_add_draft_and_locking',
                'm120712_074517_added_draft_field',
                'm130423_141157_print_flag',
                'm130604_131358_patient_shortcodes',
            )
        )
        ) {
            $this->createTables();
        } else {
            // Check to see if the out of order migration has ever been run, and if not run it
            $ooo_migration = $this->getDbConnection()->createCommand()
                ->select('version')
                ->from('tbl_migration')
                ->where('version = :version', array(':version' => 'm130904_134009_alter_comments_to_text'))
                ->queryColumn();
            if ($ooo_migration) {
                $this->getDbConnection()->createCommand()
                    ->delete('tbl_migration', array('version = :version', array(':version' => 'm130904_134009_alter_comments_to_text')));
            } else {
                $this->m130904_134009_alter_comments_to_text();
            }
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
        $this->setData();
        //disable foreign keys check
        Yii::app()->cache->flush();
        $this->execute('SET foreign_key_checks = 0');

        $event_type_id = $this->insertOEEventType('Prescription', 'OphDrPrescription', 'Dr');
        $this->insertOEElementType($this->element_types, $event_type_id);

        $this->execute("CREATE TABLE `et_ophdrprescription_details` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `comments` text,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `printed` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `draft` tinyint(1) unsigned NOT NULL DEFAULT '1',
			  `print` tinyint(1) unsigned NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `et_ophdrprescription_details_event_id_fk` (`event_id`),
			  KEY `et_ophdrprescription_details_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophdrprescription_details_last_modified_user_id_fk` (`last_modified_user_id`),
			  CONSTRAINT `et_ophdrprescription_details_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophdrprescription_details_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophdrprescription_details_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophdrprescription_item` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `prescription_id` int(10) unsigned NOT NULL,
			  `drug_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `dose` varchar(40) DEFAULT NULL,
			  `route_id` int(10) unsigned NOT NULL,
			  `frequency_id` int(10) unsigned NOT NULL,
			  `duration_id` int(10) unsigned NOT NULL,
			  `route_option_id` int(10) unsigned DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `ophdrprescription_details_prescription_id_fk` (`prescription_id`),
			  KEY `ophdrprescription_details_drug_id_fk` (`drug_id`),
			  KEY `ophdrprescription_details_created_user_id_fk` (`created_user_id`),
			  KEY `ophdrprescription_details_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophdrprescription_item_route_option_id_fk` (`route_option_id`),
			  KEY `ophdrprescription_item_route_id_fk` (`route_id`),
			  KEY `ophdrprescription_item_frequency_id_fk` (`frequency_id`),
			  KEY `ophdrprescription_item_duration_id_fk` (`duration_id`),
			  CONSTRAINT `ophdrprescription_item_duration_id_fk` FOREIGN KEY (`duration_id`) REFERENCES `drug_duration` (`id`),
			  CONSTRAINT `ophdrprescription_details_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophdrprescription_details_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`),
			  CONSTRAINT `ophdrprescription_details_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophdrprescription_details_prescription_id_fk` FOREIGN KEY (`prescription_id`) REFERENCES `et_ophdrprescription_details` (`id`),
			  CONSTRAINT `ophdrprescription_item_frequency_id_fk` FOREIGN KEY (`frequency_id`) REFERENCES `drug_frequency` (`id`),
			  CONSTRAINT `ophdrprescription_item_route_id_fk` FOREIGN KEY (`route_id`) REFERENCES `drug_route` (`id`),
			  CONSTRAINT `ophdrprescription_item_route_option_id_fk` FOREIGN KEY (`route_option_id`) REFERENCES `drug_route_option` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophdrprescription_item_taper` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `item_id` int(10) unsigned NOT NULL,
			  `dose` varchar(40) DEFAULT NULL,
			  `frequency_id` int(10) unsigned NOT NULL,
			  `duration_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophdrprescription_item_taper_item_id_fk` (`item_id`),
			  KEY `ophdrprescription_item_taper_frequency_id_fk` (`frequency_id`),
			  KEY `ophdrprescription_item_taper_duration_id_fk` (`duration_id`),
			  KEY `ophdrprescription_item_taper_created_user_id_fk` (`created_user_id`),
			  KEY `ophdrprescription_item_taper_last_modified_user_id_fk` (`last_modified_user_id`),
			  CONSTRAINT `ophdrprescription_item_taper_item_id_fk` FOREIGN KEY (`item_id`) REFERENCES `ophdrprescription_item` (`id`),
			  CONSTRAINT `ophdrprescription_item_taper_frequency_id_fk` FOREIGN KEY (`frequency_id`) REFERENCES `drug_frequency` (`id`),
			  CONSTRAINT `ophdrprescription_item_taper_duration_id_fk` FOREIGN KEY (`duration_id`) REFERENCES `drug_duration` (`id`),
			  CONSTRAINT `ophdrprescription_item_taper_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophdrprescription_item_taper_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);

        //enable foreign keys check
        $this->execute('SET foreign_key_checks = 1');
    }

    /**
     * Out of order migration missing from previous release.
     */
    protected function m130904_134009_alter_comments_to_text()
    {
        $this->alterColumn('et_ophdrprescription_details', 'comments', 'TEXT');
    }
}
