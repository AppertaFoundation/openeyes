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

class m160819_143308_rename_table_for_patientfactor extends CDbMigration
{
    public function up()
    {
        $this->renameTable('ophcocvi_clinicinfo_patient_factor', 'ophcocvi_clericinfo_patient_factor');
        $this->renameTable('ophcocvi_clinicinfo_patient_factor_version', 'ophcocvi_clericinfo_patient_factor_version');
        $this->dropForeignKey('et_ophcocvi_clericinfo_patient_factor_answer_lku_fk', 'ophcocvi_clericinfo_patient_factor_answer');
        $this->dropForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_lku_fk', 'ophcocvi_clericinfo_patient_factor_answer_version');

        $this->renameColumn('ophcocvi_clericinfo_patient_factor_answer', 'ophcocvi_clinicinfo_patient_factor_id', 'patient_factor_id');
        $this->renameColumn('ophcocvi_clericinfo_patient_factor_answer_version', 'ophcocvi_clinicinfo_patient_factor_id', 'patient_factor_id');

        $this->addForeignKey('et_ophcocvi_clericinfo_patient_factor_answer_lku_fk', 'ophcocvi_clericinfo_patient_factor_answer', 'patient_factor_id', 'ophcocvi_clericinfo_patient_factor', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('et_ophcocvi_clericinfo_patient_factor_answer_lku_fk', 'ophcocvi_clericinfo_patient_factor_answer');
        $this->renameColumn('ophcocvi_clericinfo_patient_factor_answer', 'patient_factor_id', 'ophcocvi_clinicinfo_patient_factor_id');
        $this->renameColumn('ophcocvi_clericinfo_patient_factor_answer_version', 'patient_factor_id', 'ophcocvi_clinicinfo_patient_factor_id');

        $this->renameTable('ophcocvi_clericinfo_patient_factor', 'ophcocvi_clinicinfo_patient_factor');
        $this->renameTable('ophcocvi_clericinfo_patient_factor_version', 'ophcocvi_clinicinfo_patient_factor_version');

        $this->addForeignKey('et_ophcocvi_clericinfo_patient_factor_answer_lku_fk', 'ophcocvi_clericinfo_patient_factor_answer', 'ophcocvi_clinicinfo_patient_factor_id', 'ophcocvi_clinicinfo_patient_factor', 'id');
        $this->addForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_lku_fk', 'ophcocvi_clericinfo_patient_factor_answer_version', 'ophcocvi_clinicinfo_patient_factor_id', 'ophcocvi_clinicinfo_patient_factor', 'id');

    }
}
