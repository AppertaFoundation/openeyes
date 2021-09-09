<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m160728_104209_add_columns_generic_type_lookups extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophcocvi_clericinfo_contact_urgency', 'code', 'VARCHAR(20) AFTER `name` ');
        $this->addColumn('ophcocvi_clericinfo_contact_urgency', 'active', 'tinyint(1) unsigned not null default 1 AFTER `code` ');
        $this->addColumn('ophcocvi_clericinfo_contact_urgency_version', 'code', 'VARCHAR(20) AFTER `name` ');
        $this->addColumn('ophcocvi_clericinfo_contact_urgency_version', 'active', 'tinyint(1) unsigned not null default 1 AFTER `code` ');
        $this->addColumn('ophcocvi_clinicinfo_field_of_vision', 'code', 'VARCHAR(20) AFTER `name` ');
        $this->addColumn('ophcocvi_clinicinfo_field_of_vision', 'active', 'tinyint(1) unsigned not null default 1 AFTER `code` ');
        $this->addColumn('ophcocvi_clinicinfo_field_of_vision_version', 'code', 'VARCHAR(20) AFTER `name` ');
        $this->addColumn('ophcocvi_clinicinfo_field_of_vision_version', 'active', 'tinyint(1) unsigned not null default 1 AFTER `code` ');
        $this->addColumn('ophcocvi_clinicinfo_low_vision_status', 'code', 'VARCHAR(20) AFTER `name` ');
        $this->addColumn('ophcocvi_clinicinfo_low_vision_status', 'active', 'tinyint(1) unsigned not null default 1 AFTER `code` ');
        $this->addColumn('ophcocvi_clinicinfo_low_vision_status_version', 'code', 'VARCHAR(20) AFTER `name` ');
        $this->addColumn('ophcocvi_clinicinfo_low_vision_status_version', 'active', 'tinyint(1) unsigned not null default 1 AFTER `code` ');
    }

    public function down()
    {
        $this->dropColumn('ophcocvi_clericinfo_contact_urgency', 'code');
        $this->dropColumn('ophcocvi_clericinfo_contact_urgency', 'active');
        $this->dropColumn('ophcocvi_clericinfo_contact_urgency_version', 'code');
        $this->dropColumn('ophcocvi_clericinfo_contact_urgency_version', 'active');
        $this->dropColumn('ophcocvi_clinicinfo_field_of_vision', 'code');
        $this->dropColumn('ophcocvi_clinicinfo_field_of_vision', 'active');
        $this->dropColumn('ophcocvi_clinicinfo_field_of_vision_version', 'code');
        $this->dropColumn('ophcocvi_clinicinfo_field_of_vision_version', 'active');
        $this->dropColumn('ophcocvi_clinicinfo_low_vision_status', 'code');
        $this->dropColumn('ophcocvi_clinicinfo_low_vision_status', 'active');
        $this->dropColumn('ophcocvi_clinicinfo_low_vision_status_version', 'code');
        $this->dropColumn('ophcocvi_clinicinfo_low_vision_status_version', 'active');
    }
}
