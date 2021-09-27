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

class m160811_133414_clinical_disorder_section_comments extends CDbMigration
{
    public function up()
    {
        $this->createTable('et_ophcocvi_clinicinfo_disorder_section_comment', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'element_id' => 'int(10) unsigned NOT NULL',
            'ophcocvi_clinicinfo_disorder_section_id' => 'int(10) unsigned NOT NULL',
            'comments' => 'text',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'deleted' => 'tinyint(1) unsigned not null',
            'PRIMARY KEY (`id`)',
            'KEY `et_ophcocvi_clinicinfo_disorder_section_comment_lmui_fk` (`last_modified_user_id`)',
            'KEY `et_ophcocvi_clinicinfo_disorder_section_comment_cui_fk` (`created_user_id`)',
            'KEY `et_ophcocvi_clinicinfo_disorder_section_comment_ele_fk` (`element_id`)',
            'KEY `et_ophcocvi_clinicinfo_disorder_section_comment_lku_fk` (`ophcocvi_clinicinfo_disorder_section_id`)',
            'CONSTRAINT `et_ophcocvi_clinicinfo_disorder_section_comment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `et_ophcocvi_clinicinfo_disorder_section_comment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `et_ophcocvi_clinicinfo_disorder_section_comment_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophcocvi_clinicinfo` (`id`)',
            'CONSTRAINT `et_ophcocvi_clinicinfo_disorder_section_comment_lku_fk` FOREIGN KEY (`ophcocvi_clinicinfo_disorder_section_id`) REFERENCES `ophcocvi_clinicinfo_disorder_section` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
        $this->createTable('et_ophcocvi_clinicinfo_disorder_section_comment_version', array(
            'id' => 'int(10) unsigned NOT NULL',
            'element_id' => 'int(10) unsigned NOT NULL',
            'ophcocvi_clinicinfo_disorder_section_id' => 'int(10) unsigned NOT NULL',
            'comments' => 'text',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'version_date' => "datetime NOT NULL DEFAULT '1900-01-01 00:00:00'",
            'version_id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'deleted' => 'tinyint(1) unsigned not null',
            'PRIMARY KEY (`version_id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
        $this->addColumn('et_ophcocvi_clinicinfo_disorder_assignment', 'main_cause', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
        $this->addColumn('et_ophcocvi_clinicinfo_disorder_assignment_version', 'main_cause', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
        $this->addColumn('et_ophcocvi_clinicinfo_disorder_assignment_version', 'eye_id', 'int(10) unsigned NOT NULL');
        $this->addColumn('et_ophcocvi_clinicinfo_disorder_assignment_version', 'affected', 'tinyint(1) unsigned NOT NULL DEFAULT 0');

    }

    public function down()
    {
        $this->dropTable('et_ophcocvi_clinicinfo_disorder_section_comment');
        $this->dropTable('et_ophcocvi_clinicinfo_disorder_section_comment_version');
        $this->dropColumn('et_ophcocvi_clinicinfo_disorder_assignment', 'main_cause');
        $this->dropColumn('et_ophcocvi_clinicinfo_disorder_assignment_version', 'main_cause');
        $this->dropColumn('et_ophcocvi_clinicinfo_disorder_assignment_version', 'eye_id');
        $this->dropColumn('et_ophcocvi_clinicinfo_disorder_assignment_version', 'affected');
    }

}
