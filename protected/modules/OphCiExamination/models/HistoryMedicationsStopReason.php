<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * Class HistoryMedicationsStopReason
 * @package OEModule\OphCiExamination\models
 *
 * @property integer $id
 * @property string $name
 * @property integer $display_order
 * @property boolean $active
 */
class HistoryMedicationsStopReason extends \BaseActiveRecordVersioned
{
    public function tableName()
    {
        return 'ophciexamination_medication_stop_reason';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.display_order');
    }

    public function rules()
    {
        return array(
            array('name, display_order, active', 'safe'),
        );
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Gets medication parameters changed stop reason id
     *
     * @return string | null
     */
    public static function getMedicationParametersChangedId() : ?string
    {
        $medication_changed_stop_reason = HistoryMedicationsStopReason::model()->findByAttributes(['name' => 'Medication parameters changed']);
        if ($medication_changed_stop_reason) {
            return $medication_changed_stop_reason->id;
        }

        return null;
    }
}
