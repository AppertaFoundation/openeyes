<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "medication".
 *
 * The followings are the available columns in table 'medication':
 * @deprecated since v2.0
 *
 * @property int $id
 * @property int $patient_id
 * @property int $route_id
 * @property int $drug_id
 * @property int $medication_drug_id
 * @property int $option_id
 * @property int $frequency_id
 * @property string $start_date
 * @property string $end_date
 * @property int $prescription_item_id
 * @property Tag[] $tags
 */
class ArchiveMedication extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'archive_medication';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $required_fields = 'start_date';
        if (( null === SettingMetadata::model()->getSetting('enable_concise_med_history')) || !SettingMetadata::model()->getSetting('enable_concise_med_history')) {
            $required_fields .= ', frequency_id, route_id';
        }
        return array(
            array('medication_drug_id, drug_id, route_id, option_id, dose, frequency_id, start_date, end_date, stop_reason_id, prescription_item_id', 'safe'),
            array($required_fields, 'required'),
            array('start_date', 'OEFuzzyDateValidatorNotFuture'),
            array('end_date', 'OEFuzzyDateValidator'),
            array('option_id', 'validateOptionId'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'medication_drug' => array(self::BELONGS_TO, 'MedicationDrug', 'medication_drug_id'),
            'drug' => array(self::BELONGS_TO, 'Drug', 'drug_id'),
            'route' => array(self::BELONGS_TO, 'DrugRoute', 'route_id'),
            'option' => array(self::BELONGS_TO, 'DrugRouteOption', 'option_id'),
            'frequency' => array(self::BELONGS_TO, 'DrugFrequency', 'frequency_id'),
            'stop_reason' => array(self::BELONGS_TO, 'MedicationStopReason', 'stop_reason_id'),
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
            'tags' => array(self::MANY_MANY, 'Tag', 'medication_tag(tag_id, medication_id)'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'drug_id' => 'ArchiveMedication',
            'route_id' => 'Route',
            'option_id' => 'Option',
            'frequency_id' => 'Frequency',
            'stop_reason_id' => 'Reason for stopping',
        );
    }

    public function afterValidate()
    {
        if ($this->drug_id && $this->medication_drug_id) {
            $this->addError('drug_id', 'Cannot have two different drug types in the same medication record');
        }

        return parent::afterValidate();
    }

    public function validateOptionId()
    {
        if (!$this->option_id && $this->route && $this->route->options) {
            $this->addError('option_id', "Must specify an option for route '{$this->route->name}'");
        }
    }

    public function beforeSave()
    {
        if (!$this->end_date) {
            $this->stop_reason_id = null;
        }

        return parent::beforeSave();
    }

    /**
     * Will remove the patient adherence element if it is no longer relevant.
     */
    protected function removePatientAdherence()
    {
        $medications = $this->patient->patientMedications(new CDbCriteria());
        if (!count($medications)) {
            // delete the adherence as no longer applies
            if ($ad = $this->patient->adherence) {
                $ad->delete();
            }
        }
    }

    public function afterSave()
    {
        if ($this->end_date) {
            $this->removePatientAdherence();
        }

        return parent::afterSave();
    }

    public function afterDelete()
    {
        $this->removePatientAdherence();

        return parent::afterDelete();
    }

    /**
     * Wrapper for the drug name for display.
     *
     * @return string
     */
    public function getDrugLabel()
    {
        if ($this->drug) {
            return $this->drug->label;
        } elseif ($this->medication_drug) {
            return $this->medication_drug->name;
        } else {
            return '';
        }
    }

    /**
     * Takes a prescription item and sets the appropriate medication values from it.
     *
     * @param $item
     */
    public function createFromPrescriptionItem($item)
    {
        $endDate = $item->stopDateFromDuration();

        $this->drug_id = $item->drug_id;
        $this->drug = $item->drug;
        $this->route_id = $item->route_id;
        $this->route = $item->route;
        $this->option_id = $item->route_option_id;
        $this->route = $item->route;
        $this->dose = $item->dose;
        $this->frequency_id = $item->frequency_id;
        $this->frequency = $item->frequency;
        $this->start_date = $item->prescription->event->event_date;
        if ($endDate) {
            $this->end_date = $endDate->format('Y-m-d');
        }
        $this->prescription_item_id = $item->id;
    }

    /**
     * Is the medication current.
     *
     * @return bool
     */
    public function isCurrentMedication()
    {
        $now = new DateTime();

        return !$this->end_date || $this->end_date > $now->format('Y-m-d');
    }

    /**
     * Is the preview current.
     *
     * @return bool
     */
    public function isPreviousMedication()
    {
        $now = new DateTime();

        return $this->end_date && $this->end_date < $now->format('Y-m-d');
    }

    /**
     * @param $item
     *
     * @return bool
     */
    public function matches($item)
    {
        if ($this->drug_id === $item->drug_id) {
            return true;
        }

        if ($this->medication_drug->name === $item->drug->name) {
            return true;
        }

        return false;
    }
}
