<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use OE\factories\models\traits\HasFactory;

class OphCiExamination_IntraocularPressure_Value extends \BaseActiveRecordVersioned
{
    use HasFactory;

    public function tableName()
    {
        return 'ophciexamination_intraocularpressure_value';
    }

    public function rules()
    {
        return array(
            array('eye_id, reading_time, reading_id, instrument_id, qualitative_reading_id', 'safe'),
            array('eye_id, reading_time', 'required'),
            array('eye_id', 'in', 'range' => array(\Eye::LEFT, \Eye::RIGHT)),
            array('reading_time', 'match', 'pattern' => '/^\d{2}:\d{2}$/', 'message' => '{attribute} must be in format HH:MM'),
        );
    }

    public function relations()
    {
        return array(
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'reading' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Reading', 'reading_id'),
            'instrument' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Instrument', 'instrument_id'),
            'qualitative_reading' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Qualitative_Scale_Value', 'qualitative_reading_id'),
        );
    }

    public function init()
    {
        if ($this->getScenario() !== 'exam_creator') {
            if (($default_instrument_id = Element_OphCiExamination_IntraocularPressure::model()->getSetting('default_instrument_id'))) {
                $this->instrument_id = $default_instrument_id;
            }

            if (($default_reading_id = Element_OphCiExamination_IntraocularPressure::model()->getSetting('default_reading_id'))) {
                $this->reading_id = $default_reading_id;
            }
        }
        $this->reading_time = date('H:i', time());
    }

    public function afterValidate()
    {
        if (!$this->reading_id && !$this->qualitative_reading_id) {
            $this->addError('reading_id', 'Either a numerical reading or a qualitative reading must be specified.');
        }

        return parent::afterValidate();
    }
}
