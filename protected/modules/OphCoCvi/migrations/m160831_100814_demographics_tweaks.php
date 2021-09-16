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

class m160831_100814_demographics_tweaks extends CDbMigration
{
    public function up()
    {
        $this->dropColumn('et_ophcocvi_demographics', 'gender');
        $this->dropColumn('et_ophcocvi_demographics_version', 'gender');
        $this->dropColumn('et_ophcocvi_demographics', 'name');
        $this->dropColumn('et_ophcocvi_demographics_version', 'name');
        $this->addColumn('et_ophcocvi_demographics', 'title_surname', 'varchar(120)');
        $this->addColumn('et_ophcocvi_demographics_version', 'title_surname', 'varchar(120)');
        $this->addColumn('et_ophcocvi_demographics', 'other_names', 'varchar(100)');
        $this->addColumn('et_ophcocvi_demographics_version', 'other_names', 'varchar(100)');
        $this->addColumn('et_ophcocvi_demographics', 'gender_id', 'int(10) unsigned');
        $this->addColumn('et_ophcocvi_demographics_version', 'gender_id', 'int(10) unsigned');
        $this->addForeignKey('et_ophcocvi_demographics_gui_fk', 'et_ophcocvi_demographics', 'gender_id', 'gender', 'id');
        $this->addColumn('et_ophcocvi_demographics', 'ethnic_group_id', 'int(10) unsigned');
        $this->addColumn('et_ophcocvi_demographics_version', 'ethnic_group_id', 'int(10) unsigned');
        $this->addForeignKey('et_ophcocvi_demographics_egui_fk', 'et_ophcocvi_demographics', 'ethnic_group_id', 'ethnic_group', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('et_ophcocvi_demographics_egui_fk', 'et_ophcocvi_demographics');
        $this->dropForeignKey('et_ophcocvi_demographics_gui_fk', 'et_ophcocvi_demographics');
        $this->dropColumn('et_ophcocvi_demographics_version', 'gender_id');
        $this->dropColumn('et_ophcocvi_demographics', 'gender_id');
        $this->dropColumn('et_ophcocvi_demographics_version', 'ethnic_group_id');
        $this->dropColumn('et_ophcocvi_demographics', 'ethnic_group_id');
        $this->dropColumn('et_ophcocvi_demographics_version', 'title_surname');
        $this->dropColumn('et_ophcocvi_demographics', 'title_surname');
        $this->dropColumn('et_ophcocvi_demographics_version', 'other_names');
        $this->dropColumn('et_ophcocvi_demographics', 'other_names');

        $this->addColumn('et_ophcocvi_demographics', 'gender', 'varchar(20)');
        $this->addColumn('et_ophcocvi_demographics_version', 'gender', 'varchar(20)');
        $this->addColumn('et_ophcocvi_demographics', 'name', 'varchar(255)');
        $this->addColumn('et_ophcocvi_demographics_version', 'name', 'varchar(255)');
    }

}
