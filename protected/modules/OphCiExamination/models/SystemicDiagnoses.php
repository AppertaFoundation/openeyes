<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
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
     * @var bool flag to indicate whether we should update the patient level data
     */
    protected $update_patient_level = false;

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
     * Will duplicate values from the given SystemicDiagnoses element.
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
     * @param \Patient $patient
     */
    public function setDefaultOptions(\Patient $patient = null)
    {

        if ($patient) {
            $diagnoses = array();
            foreach ($patient->getSystemicDiagnoses() as $sd) {
                $diagnoses[] = SystemicDiagnoses_Diagnosis::fromSecondaryDiagnosis($sd);
            }
            $this->diagnoses = $diagnoses;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(' // ', $this->orderedDiagnoses);
    }

    public $cached_tip_status = null;

    protected function calculateTipStatus()
    {
        if ($this->tipCheck()) {
            if ($this->isNewRecord) {
                return true;
            }

            // the element is the latest element, need to see if all the
            // diagnoses in the element are still at the tip
            foreach ($this->diagnoses as $diagnosis) {
                if (!$diagnosis->isAtTip()) {
                    return false;
                }
            }

            // and finally whether there is a discrepancy between the patients secondary
            // diagnoses and the number of diagnoses on the element (i.e. one has been
            // added from elsewhere)
            $patient = $this->event->getPatient();
            return count($this->diagnoses) === count($patient->getSystemicDiagnoses());
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isAtTip()
    {
        // TODO: consolidate the cached status and the patient update flag
        if ($this->cached_tip_status === null) {
            $this->cached_tip_status = $this->calculateTipStatus();
        }
        return $this->cached_tip_status;
    }

    /**
     * Call this method before updating any attributes on the instance.
     */
    public function storePatientUpdateStatus()
    {
        $this->update_patient_level = $this->isAtTip();
    }

    /**
     * Validate the diagnoses
     */
    protected function afterValidate()
    {
        foreach ($this->diagnoses as $i => $diagnosis) {
            if (!$diagnosis->validate()) {
                foreach ($diagnosis->getErrors() as $fld => $err) {
                    $this->addError('diagnoses', 'Diagnosis ('.($i + 1).'): '.implode(', ', $err));
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave()
    {
        parent::afterSave();
        if ($this->update_patient_level) {
            // extract event from the event id of the element - in afterSave the relation doesn't
            // work when the instance has only just been saved
            $event = \Event::model()->findByPk($this->event_id);
            $patient = $event->getPatient();
            $sd_ids_to_keep = array();
            // update or create the secondary diagnoses for the diagnoses on this element
            foreach ($this->diagnoses as $diagnosis) {
                $sd = $diagnosis->updateAndGetSecondaryDiagnosis($patient);
                $sd_ids_to_keep[] = $sd->id;
            }

            // then delete any other secondary diagnoses still on the patient.
            foreach ($patient->getSystemicDiagnoses() as $sd) {
                if (!in_array($sd->id, $sd_ids_to_keep)) {
                    $sd->delete();
                }
            }
        }
    }
}