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

class m201216_164927_drop_foreign_keys_from_version extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_lmui_fk', 'ophcocvi_clericinfo_patient_factor_answer_version');
        $this->dropForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_cui_fk', 'ophcocvi_clericinfo_patient_factor_answer_version');
        $this->dropForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_ele_fk', 'ophcocvi_clericinfo_patient_factor_answer_version');
    }

    public function down()
    {
        $this->addForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_lmui_fk', 'ophcocvi_clericinfo_patient_factor_answer_version', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_cui_fk', 'ophcocvi_clericinfo_patient_factor_answer_version', 'created_user_id', 'user', 'id');
        $this->addForeignKey('acv_et_ophcocvi_clericinfo_patient_factor_answer_ele_fk', 'ophcocvi_clericinfo_patient_factor_answer_version', 'element_id', 'et_ophcocvi_clericinfo', 'id');
    }
}
