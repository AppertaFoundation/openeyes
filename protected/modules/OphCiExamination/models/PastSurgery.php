<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;


/**
 * Class PastSurgery
 *
 * @package OEModule\OphCiExamination\models
 *
 * @property int $id
 * @property int $event_id
 *
 * @property \EventType $eventType
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property PastSurgery_Operation[] $operations
 */
class PastSurgery extends \BaseEventTypeElement
{

    protected $auto_update_relations = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
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
        return 'et_ophciexamination_pastsurgery';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, operations', 'safe'),
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
            'operations' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\PastSurgery_Operation', 'element_id'),
        );
    }

    /**
     * Will duplicate values from the current socialhistory property of the given patient.
     *
     * @param \Patient $patient
     */
    public function initFromPatient(\Patient $patient)
    {
        $operations = array();
        foreach ($patient->previousOperations as $prev) {
            $op = new OphCiExamination_PastSurgery_Operation();
            $op->operation = $prev->operation;
            $op->side_id = $prev->side_id;
            $op->date = $prev->date;
            $op->previous_operation_id = $prev->id;
            $operations[] = $op;
        }
        $this->operations = $operations;
    }

    /**
     * Update the patient record from the operations set on this element.
     *
     * Assumes that if its been called, the patient level past operations should be set to sync with the operations
     * stored on this element.
     *
     * @param \Patient $patient
     * @throws \CDbException
     * @throws \Exception
     */
    public function updatePatient(\Patient $patient)
    {
        $to_keep = array();

        foreach ($this->getRelated('operations', true) as $op) {
            if (!$op->previousOperation) {
                $previous = new \PreviousOperation();
                $previous->patient_id = $patient->id;
                $previous->side_id = $op->side_id;
                $previous->date = $op->date;
                $previous->operation = $op->operation;
                if (!$previous->save()) {
                    throw new \Exception('Could not save previous operation for patient');
                }
                $op->previous_operation_id = $previous->id;

                if (!$op->save()) {
                    throw new \Exception('Could not link past surgery operation to patient previous operation');
                }
                $to_keep[] = $previous->id;
            } else {
                $to_keep[] = $op->previous_operation_id;
            }
        }

        $criteria = new \CDbCriteria();
        $criteria->compare('patient_id', $patient->id);
        $criteria->addNotInCondition('id', $to_keep);
        foreach (\PreviousOperation::model()->findAll($criteria) as $prev) {
            if (!$prev->delete()) {
                throw new \Exception('Unable to remove redundant previous operations from patient.');
            }
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(' // ', $this->operations);
    }
}