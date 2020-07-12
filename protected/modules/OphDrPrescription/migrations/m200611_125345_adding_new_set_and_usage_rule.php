<?php
/**
 * OpenEyes.
 *
 * (C) Copyright Apperta Foundation 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class m200611_125345_adding_new_set_and_usage_rule extends OEMigration
{
    public function up()
    {
        $this->insert(
            'medication_usage_code',
            ['usage_code' => 'PRESCRIBABLE_DRUGS', 'name' => 'Prescribable Drugs', 'active' => 1]
        );

        $usage_code_id = $this->dbConnection->getLastInsertID();

        $this->insert(
            'medication_set',
            ['name' => 'Prescribable Drugs', 'automatic' => 1]
        );
        $set_id = $this->dbConnection->getLastInsertID();

        $this->insert('medication_set_rule', [
                'medication_set_id' => $set_id,
                'usage_code_id' => $usage_code_id
        ]);

        $formulary_id = $this->dbConnection->createCommand()->select('id')->from('medication_set')->where('name = :name', [':name' => 'Formulary'])->queryScalar();

        $this->insert('medication_set_auto_rule_set_membership', [
            'target_medication_set_id' => $set_id,
            'source_medication_set_id' => $formulary_id,
        ]);

        $set = \MedicationSet::model()->findByPk($set_id);
        if ($set) {
            $set->populateAuto();
        }
    }

    public function down()
    {
        $usage_code_id = $this->dbConnection->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = "PRESCRIBABLE_DRUGS"')->queryScalar();
        $medication_set_rule = \MedicationSetRule::model()->findByAttributes(['usage_code_id' => $usage_code_id]);
        $set = \MedicationSet::model()->findByPk($medication_set_rule->medication_set_id);

        $this->delete('medication_set_auto_rule_set_membership', 'target_medication_set_id = ?', [$set->id]);
        $this->delete('medication_set_rule', 'medication_set_id = ?', [$set->id]);
        $set->delete();
        $this->delete('medication_usage_code', 'usage_code = ?', ['PRESCRIBABLE_DRUGS']);
    }
}
