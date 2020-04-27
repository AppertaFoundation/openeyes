<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class RisksMigrateCommand extends PatientLevelMigration
{
    protected $event_type_cls = 'OphCiExamination';
    // Original table is renamed to this during the module database migration
    protected static $archived_entry_table = 'archive_patient_risk_assignment';
    // column on patient record indicating no entries have been explicitly recorded
    protected static $archived_no_values_col = 'archive_no_risks_date';
    protected static $no_values_col = 'no_risks_date';
    protected static $element_class = 'OEModule\OphCiExamination\models\HistoryRisks';
    protected static $entry_class = 'OEModule\OphCiExamination\models\HistoryRisksEntry';
    protected static $entry_attributes = array(
        'risk_id',
        'other',
        'comments'
    );

    public function getHelp()
    {
        return "Migrates the original Risk records to an examination event in change tracker episode\n";
    }

    /**
     * @return mixed
     * @inheritdoc
     */
    protected function getNewElement()
    {
        // sets the scenario to migration to customise the validation rules on the model
        return new static::$element_class('migration');
    }

    /**
     * @param $patient_id
     * @param null $no_entries_date
     * @param array $rows
     * @return bool
     * @inheritdoc
     */
    public function processPatient($patient_id, $no_entries_date = null, $rows = array())
    {
        // new model has an attribute that by implication is always true from the source records
        foreach ($rows as &$row) {
            $row['has_risk'] = true;
        }
        return parent::processPatient($patient_id, $no_entries_date, $rows);
    }
}
