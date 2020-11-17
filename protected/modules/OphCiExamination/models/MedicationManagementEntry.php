<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * Class MedicationManagementEntry
 * @package OEModule\OphCiExamination\models
 *
 * @property \OphDrPrescription_DispenseCondition $dispense_condition
 * @property \OphDrPrescription_DispenseLocation $dispense_location
 * @property \OphDrPrescription_ItemTaper[] $tapers
 */
class MedicationManagementEntry extends \EventMedicationUse
{
    public $taper_support = true;

    /** @var int Temporary flag to store locked (non-editable) status */
    public $locked = 0;

    public static function getUsageType()
    {
        return "OphCiExamination";
    }

    public static function getUsageSubtype()
    {
        return "Management";
    }

    /**
     * Returns the static model of the specified AR class.
     */

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        return array_merge(
            parent::rules(),
            array(
                array('start_date', 'validateStartDate'),
                array('end_date', 'validateEndDate'),
                array('duration_id', 'validateDuration')
            )
        );
    }

    public function validateStartDate()
    {
        if (!isset($this->start_date) && !$this->hidden && $this->getScenario() == "to_be_prescribed") {
            $this->addError("start_date", "Start date must not be empty when prescribing");
        } else {
            $validator = new \OEFuzzyDateValidator();
            $validator->validateAttribute($this, "start_date");
        }
    }

    public function validateEndDate()
    {
        $current_date = date("Y-m-d");
        if ($this->end_date && $this->end_date < $current_date && !$this->hidden) {
            $this->addError("end_date", "Stop date cannot be in the past");
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array_merge(parent::relations(), array(
            'dispense_condition' => array(self::BELONGS_TO, 'OphDrPrescription_DispenseCondition', 'dispense_condition_id'),
            'dispense_location' => array(self::BELONGS_TO, 'OphDrPrescription_DispenseLocation', 'dispense_location_id'),
            'tapers' => array(self::HAS_MANY, \OphDrPrescription_ItemTaper::class, "item_id"),
        ));
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'prescribe' => 'Prescribe'
        ));
    }

    private function explodeDate($date_str)
    {
        return substr($date_str, 0, 4) . "-" . substr($date_str, 4, 2) . "-" . substr($date_str, 6, 2);
    }

    /**
     * Check if menegement entry is different to its
     * linked prescription entry
     *
     * @return bool  true if identical, false otherwise
     */

    public function compareToPrescriptionItem()
    {
        $my_attributes = $this->getAttributes();
        $their_attributes = $this->prescriptionItem->getAttributes();

        $attributes_to_check = array(
            'medication_id',
            'form_id',
            'laterality',
            'route_id',
            'frequency_id',
            'duration_id',
            'dose',
            'dispense_condition_id',
            'dispense_location_id',
            'comments',
        );

        $identical = true;
        foreach ($attributes_to_check as $attr) {
            if ($my_attributes[$attr] != $their_attributes[$attr]) {
                $identical = false;
            }
        }

        $prescription_tapers = $this->prescriptionItem->tapers;
        if (count($this->tapers) != count($prescription_tapers)) {
            $identical = false;
        } else {
            foreach ($this->tapers as $key => $taper) {
                if (!$taper->compareTo($prescription_tapers[$key])) {
                    $identical = false;
                }
            }
        }

        return $identical;
    }

    public function afterValidate()
    {
        if ($this->is_discontinued && !$this->prescribe) {
            $this->stopped_in_event_id = $this->event_id;
        }
        // validate Tapers
        foreach ($this->tapers as $key => $taper) {
            $taper->item_id = $this->id;
            if (!$taper->validate()) {
                foreach ($taper->getErrors() as $field => $error) {
                    $this->addError('taper_'.$key.'_'.$field, "Taper " . ($key + 1) . ' - ' . implode(', ', $error));
                }
            }
        }
        return false;

        return parent::afterValidate();
    }

    protected function beforeSave()
    {
        if ($this->hasLinkedPrescribedEntry()) {
            $end_date = $this->prescriptionItem->stopDateFromDuration();
            $this->end_date = $end_date ? $end_date->format('Y-m-d') : null;
        }

        if ($this->end_date) {
            if ($this->prescribe) {
                $this->setStopReasonTo('Course complete');
            }
        } else {
            $this->stop_reason_id = null;
        }

        return parent::beforeSave();
    }

    public function beforeDelete()
    {
        \Yii::app()->db->createCommand("DELETE FROM " . \OphDrPrescription_ItemTaper::model()->tableName() . " WHERE item_id = :item_id")->
        bindValues(array(":item_id" => $this->id))->execute();

        return parent::beforeDelete();
    }
}
