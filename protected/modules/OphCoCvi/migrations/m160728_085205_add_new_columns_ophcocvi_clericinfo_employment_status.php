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

class m160728_085205_add_new_columns_ophcocvi_clericinfo_employment_status extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophcocvi_clericinfo_employment_status', 'child_default', 'tinyint(1) unsigned NOT NULL DEFAULT 0 AFTER `name` ');
        $this->addColumn('ophcocvi_clericinfo_employment_status', 'social_history_occupation_id', 'int(12) NULL AFTER `child_default`  ');
        $this->addColumn('ophcocvi_clericinfo_employment_status', 'active', 'tinyint(1) unsigned not null default 1 AFTER `social_history_occupation_id` ');
        $this->addColumn('ophcocvi_clericinfo_employment_status_version', 'child_default', 'tinyint(1) unsigned NOT NULL DEFAULT 0 AFTER `name`');
        $this->addColumn('ophcocvi_clericinfo_employment_status_version', 'social_history_occupation_id', 'int(12) NULL AFTER `child_default`');
        $this->addColumn('ophcocvi_clericinfo_employment_status_version', 'active', 'tinyint(1) unsigned not null default 1 AFTER `social_history_occupation_id`');

        $this->update('ophcocvi_clericinfo_employment_status', array('child_default' => true), 'name = :name', array(':name' => 'Child'));
        foreach (array('Retired', 'Employed', 'Unemployed', 'Student') as $label) {
            $sh_occ = $this->dbConnection->createCommand()->select('id')->from('socialhistory_occupation')
                ->where('name = :name', array(':name' => $label))
                ->queryRow();

            if ($sh_occ) {
                $this->update('ophcocvi_clericinfo_employment_status', array('social_history_occupation_id' => $sh_occ['id']), 'name = :name', array(':name' => $label));
            }
        }
    }

    public function down()
    {
        $this->dropColumn('ophcocvi_clericinfo_employment_status', 'active');
        $this->dropColumn('ophcocvi_clericinfo_employment_status', 'social_history_occupation_id');
        $this->dropColumn('ophcocvi_clericinfo_employment_status', 'child_default');
        $this->dropColumn('ophcocvi_clericinfo_employment_status_version', 'active');
        $this->dropColumn('ophcocvi_clericinfo_employment_status_version', 'social_history_occupation_id');
        $this->dropColumn('ophcocvi_clericinfo_employment_status_version', 'child_default');
    }
}
