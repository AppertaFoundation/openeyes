<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class FamilyHistoryMigrateCommand extends PatientLevelMigration
{
    protected $event_type_cls = 'OphCiExamination';
    // Original table is renamed to this during the module database migration
    protected static $archived_entry_table = 'archive_family_history';
    // column on patient record indicating no entries have been explicitly recorded
    protected static $archived_no_values_col = 'archive_no_family_history_date';
    protected static $no_values_col = 'no_family_history_date';
    protected static $element_class = 'OEModule\OphCiExamination\models\FamilyHistory';
    protected static $entry_class = 'OEModule\OphCiExamination\models\FamilyHistory_Entry';
    protected static $entry_attributes = array(
        'relative_id',
        'other_relative',
        'side_id',
        'condition_id',
        'other_condition',
        'comments'
    );

    public function getHelp()
    {
        return "Migrates the original Family History record to an examination event in change tracker episode\n";
    }

}
