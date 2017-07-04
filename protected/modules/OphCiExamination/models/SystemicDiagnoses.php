<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * Class SystemicDiagnoses
 * @package OEModule\OphCiExamination\models
 *
 * @property int $id
 * @property int $event_id
 *
 * @property \EventType $eventType
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property SystemicDiagnoses_Diagnosis[] $diagnoses
 * @property SystemicDiagnoses_Diagnosis[] $orderedDiagnoses
 */
class SystemicDiagnoses extends \BaseEventTypeElement
{
    protected $auto_update_relations = true;
    public $widgetClass = 'OEModule\OphCiExamination\widgets\SystemicDiagnoses';
    protected $default_from_previous = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return static
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_systemic_diagnoses';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array(
            'PatientLevelElementBehaviour' => 'PatientLevelElementBehaviour',
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, diagnoses', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id',  'safe', 'on' => 'search')
        );
    }

    /**
     * @return array
     */
    public function relations()
    {
        return array(
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'diagnoses' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\SystemicDiagnoses_Diagnosis', 'element_id'),
            'orderedDiagnoses' => array(self::HAS_MANY,
                'OEModule\OphCiExamination\models\SystemicDiagnoses_Diagnosis',
                'element_id',
                'order' => 'orderedDiagnoses.date desc, orderedDiagnoses.last_modified_date'),
        );
    }

    /**
     * Will duplicate values from the current socialhistory property of the given patient.
     *
     * @param static $element
     */
    public function loadFromExisting($element)
    {
        $diagnoses = array();
        foreach ($element->orderedDiagnoses as $prev) {
            $diagnosis = new SystemicDiagnoses_Diagnosis();
            $diagnosis->disorder_id = $prev->disorder_id;
            $diagnosis->side_id = $prev->side_id;
            $diagnosis->date = $prev->date;
            $diagnoses[] = $diagnosis;
        }
        $this->diagnoses = $diagnoses;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(' // ', $this->orderedDiagnoses);
    }

}