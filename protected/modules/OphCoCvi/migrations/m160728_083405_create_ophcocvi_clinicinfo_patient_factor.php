<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m160728_083405_create_ophcocvi_clinicinfo_patient_factor extends CDbMigration
{
    public function up()
    {
        $this->createTable('ophcocvi_clinicinfo_patient_factor', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'name' => 'varchar(255) NOT NULL',
            'code' => 'varchar(20) NOT NULL',
            'require_comments' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
            'comments_label' => 'varchar(128) NOT NULL',
            'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'active' => 'tinyint(1) unsigned NOT NULL DEFAULT 1',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'deleted' => 'tinyint(1) unsigned not null',
            'PRIMARY KEY (`id`)',
            'KEY `ophcocvi_clinicinfo_patient_factor_lmui_fk` (`last_modified_user_id`)',
            'KEY `ophcocvi_clinicinfo_patient_factor_cui_fk` (`created_user_id`)',
            'CONSTRAINT `ophcocvi_clinicinfo_patient_factor_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `ophcocvi_clinicinfo_patient_factor_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createTable('ophcocvi_clinicinfo_patient_factor_version', array(
            'id' => 'int(10) unsigned NOT NULL',
            'name' => 'varchar(255) NOT NULL',
            'code' => 'varchar(20) NOT NULL',
            'require_comments' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
            'comments_label' => 'varchar(128) NOT NULL',
            'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'active' => 'tinyint(1) unsigned NOT NULL DEFAULT 1',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'deleted' => 'tinyint(1) unsigned not null',
            'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
            'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'PRIMARY KEY (`version_id`)',
            'KEY `acv_ophcocvi_clinicinfo_patient_factor_lmui_fk` (`last_modified_user_id`)',
            'KEY `acv_ophcocvi_clinicinfo_patient_factor_cui_fk` (`created_user_id`)',
            'KEY `ophcocvi_clinicinfo_patient_factor_aid_fk` (`id`)',
            'CONSTRAINT `acv_ophcocvi_clinicinfo_patient_factor_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `acv_ophcocvi_clinicinfo_patient_factor_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `ophcocvi_clinicinfo_patient_factor_aid_fk` FOREIGN KEY (`id`) REFERENCES `ophcocvi_clinicinfo_patient_factor` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
    }

    public function down()
    {
        $this->dropTable('ophcocvi_clinicinfo_patient_factor_version');
        $this->dropTable('ophcocvi_clinicinfo_patient_factor');
    }
}
