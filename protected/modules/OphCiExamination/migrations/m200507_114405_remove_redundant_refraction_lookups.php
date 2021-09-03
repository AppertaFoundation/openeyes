<?php

/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m200507_114405_remove_redundant_refraction_lookups extends OEMigration
{
    public function up()
    {
        $this->dropOETable('ophciexamination_refraction_cylinder_integer', true);
        $this->dropOETable('ophciexamination_refraction_fraction', true);
        $this->dropOETable('ophciexamination_refraction_sphere_integer', true);
        $this->dropOETable('ophciexamination_refraction_sign', true);
    }

    public function down()
    {
        $this->migrationEcho(
            "*****************\n** WARNING: recreating tables for migration consistency, " .
            "BUT data will not be repopulated in redundant tables. **\n******************\n"
        );

        // These table structures have just been grabbed from old migrations and are only in place for
        // up/down support on the migration. These tables have long been redundant so the data is not important.
        $this->execute("CREATE TABLE `ophciexamination_refraction_sign` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(4) DEFAULT NULL,
				`value` varchar(4) DEFAULT NULL,
				`display_order` tinyint(3) unsigned DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_refraction_sign_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_refraction_sign_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_refraction_sign_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_refraction_sign_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->versionExistingTable('ophciexamination_refraction_sign');

        $this->execute("CREATE TABLE `ophciexamination_refraction_fraction` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(4) DEFAULT NULL,
				`value` varchar(3) DEFAULT NULL,
				`display_order` tinyint(3) unsigned DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_refraction_fraction_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_refraction_fraction_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_refraction_fraction_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_refraction_fraction_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");
        $this->versionExistingTable('ophciexamination_refraction_fraction');

        $this->createTable('ophciexamination_refraction_cylinder_integer', [
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'value' => 'varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL',
            'display_order' => 'tinyint(3) unsigned DEFAULT 0',
            'sign_id' => 'int(10) unsigned not null',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
            'KEY `ophciexamination_refraction_cylinder_integer_lmui_fk` (`last_modified_user_id`)',
            'KEY `ophciexamination_refraction_cylinder_integer_cui_fk` (`created_user_id`)',
            'KEY `ophciexamination_refraction_cylinder_integer_sign_id_fk` (`sign_id`)',
            'CONSTRAINT `ophciexamination_refraction_cylinder_integer_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `ophciexamination_refraction_cylinder_integer_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `ophciexamination_refraction_cylinder_integer_sign_id_fk` FOREIGN KEY (`sign_id`) REFERENCES `ophciexamination_refraction_sign` (`id`)',
        ], 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->versionExistingTable('ophciexamination_refraction_cylinder_integer');

        $this->createTable('ophciexamination_refraction_sphere_integer', [
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'value' => 'varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL',
            'display_order' => 'tinyint(3) unsigned DEFAULT 0',
            'sign_id' => 'int(10) unsigned not null',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
            'KEY `ophciexamination_refraction_sphere_integer_lmui_fk` (`last_modified_user_id`)',
            'KEY `ophciexamination_refraction_sphere_integer_cui_fk` (`created_user_id`)',
            'KEY `ophciexamination_refraction_sphere_integer_sign_id_fk` (`sign_id`)',
            'CONSTRAINT `ophciexamination_refraction_sphere_integer_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `ophciexamination_refraction_sphere_integer_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `ophciexamination_refraction_sphere_integer_sign_id_fk` FOREIGN KEY (`sign_id`) REFERENCES `ophciexamination_refraction_sign` (`id`)',
        ], 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->versionExistingTable('ophciexamination_refraction_sphere_integer');
    }
}
