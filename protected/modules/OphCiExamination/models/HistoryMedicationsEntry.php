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
use OEModule\OphCiExamination\widgets\BaseMedicationWidget;

/**
 * Class HistoryMedicationsEntry - Supports linking to prescription items as shadow records
 * to enable a full medication history to be displayed in one place.
 *
 * @package OEModule\OphCiExamination\models
 *
 * attributes:
 * @property string $medication_name
 * @property string $dose
 * @property date $start_date
 * @property date $end_date
 * @property int $prescription_item_id
 *
 * relations:
 * @property HistoryMedications $element
 * @property \MedicationDrug $medication_drug
 * @property \Drug $drug
 * @property \DrugRoute $route
 * @property \DrugRouteOption $option
 * @property \DrugFrequency $frequency
 * @property HistoryMedicationsStopReason $stop_reason
 * @property \OphDrPrescription_Item $prescription_item
 */
class HistoryMedicationsEntry extends \BaseElement
{
    /**
     * @var bool Tracking variable used when creating/editing entries
     */
    public $originallyStopped = false;

    public $prescription_not_synced = null;
    public $prescription_item_deleted = null;
    public $prescription_event_deleted = null;

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
        return 'ophciexamination_history_medications_entry';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('element_id, medication_drug_id, drug_id, medication_name, route_id, option_id, dose, units, '
                .'frequency_id, start_date, end_date, stop_reason_id, prescription_item_id', 'safe'),
            array('start_date', 'OEFuzzyDateValidatorNotFuture'),
            array('end_date', 'OEFuzzyDateValidator'),
            array('option_id', 'validateOptionId'),
            array('prescription_item_id', 'validatePrescriptionItem'),
            array('start_date, end_date', 'default', 'setOnEmpty' => true, 'value' => null)
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\HistoryMedications', 'element_id'),
            'medication_drug' => array(self::BELONGS_TO, 'MedicationDrug', 'medication_drug_id'),
            'drug' => array(self::BELONGS_TO, 'Drug', 'drug_id'),
            'route' => array(self::BELONGS_TO, 'DrugRoute', 'route_id'),
            'option' => array(self::BELONGS_TO, 'DrugRouteOption', 'option_id'),
            'frequency' => array(self::BELONGS_TO, 'DrugFrequency', 'frequency_id'),
            'stop_reason' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\HistoryMedicationsStopReason', 'stop_reason_id'),
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
            'prescription_item' => array(self::BELONGS_TO, 'OphDrPrescription_Item', 'prescription_item_id')
        );
    }

    /**
     * Abstraction to set up the entry state based on its current attributes
     */
    protected function updateStateProperties()
    {
        if ($this->end_date !== null
            && $this->end_date <= date('Y-m-d', strtotime($this->element->event->event_date))) {
            $this->originallyStopped = true;
        }
        if ($this->prescription_item_id) {
            $this->initialiseFromPrescriptionItem();
        }
    }

    /**
     * @inheritdoc
     */
    protected function afterFind()
    {
        parent::afterFind();
        $this->updateStateProperties();
    }

    /**
     * @param static $element
     * @inheritdoc
     */
    public function loadFromExisting($element)
    {
        parent::loadFromExisting($element);
        $this->updateStateProperties();
    }

    /**
     * Set all the appropriate attributes on this Entry to those on the given
     * prescription item.
     *
     * @param $item
     */
    private function clonefromPrescriptionItem($item)
    {
        $this->drug_id = $item->drug_id;
        $this->drug = $item->drug;
        $this->route_id = $item->route_id;
        $this->route = $item->route;
        $this->option_id = $item->route_option_id;
        $this->route = $item->route;
        $this->dose = $item->dose;
        $this->frequency_id = $item->frequency_id;
        $this->frequency = $item->frequency;
        $this->start_date = date('Y-m-d', strtotime($item->prescription->event->event_date));
        if (!$this->end_date) {
            $end_date = $item->stopDateFromDuration();

            if ($end_date !== null) {
                if (strtotime($end_date->format('Y-m-d')) < time()) {
                    $this->originallyStopped = true;
                }
                $this->end_date = $end_date->format('Y-m-d');
            }
        }
    }

    /**
     * When an entry is related to a prescription item, it's attributes should match,
     * and if not we need to set flags on it so that the user can be alerted as
     * appropriate.
     */
    protected function initialiseFromPrescriptionItem()
    {
        if (!$item = $this->prescription_item) {
            $this->prescription_item_deleted = true;
            $this->prescription_not_synced = true;
            return;
        }

        if (!$item->prescription->event) {
            // default scope on the event will mean event relation is null if it's been deleted
            $this->prescription_event_deleted = true;
            return;
        }

        if ($this->isNewRecord) {
            // must be creating a new 'shadow' record so we default everything from the prescription item
            $this->cloneFromPrescriptionItem($item);
        } else {
            // need to check if the prescription item still has the same values
            foreach (array('drug_id', 'dose', 'route_id', 'frequency_id') as $attr) {
                if ($this->$attr != $item->$attr) {
                    $this->prescription_not_synced = true;
                    break;
                }
            }
            // TODO: resolve the disparity in attribute names here
            if ($this->option_id !== $item->route_option_id) {
                $this->prescription_not_synced = true;
            }
        }
    }

    /**
     * Expects a compatible prescription item to load data from.
     *
     * @param $item
     */
    public function loadFromPrescriptionItem($item)
    {
        $this->prescription_item_id = $item->id;
        $this->prescription_item = $item;
        $this->initialiseFromPrescriptionItem();
    }

    /**
     * require an option selection when a route is chosen that has options
     */
    public function validateOptionId()
    {
        if (!$this->option_id && $this->route && $this->route->options) {
            $this->addError('option_id', "Must specify an option for route '{$this->route->name}'");
        }
    }

    /**
     * Simple check to ensure the prescription item id is valid as there is no FK constraint on this attribute.
     */
    public function validatePrescriptionItem()
    {
        if ($this->prescription_item_id) {
            if ($api = $this->getApp()->moduleAPI->get('OphDrPrescription')) {
                if (!$api->validatePrescriptionItemId($this->prescription_item_id)) {
                    $this->addError('prescription_item_id', 'Invalid prescription item, please restart the medication element.');
                }
            } else {
                // in the unlikely event that the prescription event has been turned off since the record was created
                // we don't want to invalidate an update.
                if ($this->getScenario() === 'insert') {
                    $this->addError('prescription_item_id', 'Cannot link medication to prescription without prescription module.');
                }
            }
        }
    }

    /**
     * Check element attributes to determine if anything has been set that would allow it to be recorded
     * Can be used to remove entries from the containing element.
     *
     * @return bool
     */
    public function hasRecordableData()
    {
        foreach (array('medication_drug_id', 'drug_id', 'medication_name', 'route_id', 'option_id', 'dose', 'units',
            'frequency_id', 'end_date', 'stop_reason_id') as $attr) {
            if ($this->$attr) {
                return true;
            }
        }
        if ($this->start_date && \Helper::formatFuzzyDate($this->start_date) != date('Y')) {
            return true;
        }
        return false;
    }

    public function beforeValidate()
    {
        if (strpos($this->drug_id, '@@M') !== false) {
            $medication_data = explode('@@M', $this->drug_id);
            $this->medication_drug_id = $medication_data[0];
            $this->drug_id = null;
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        if (!$this->medication_name && !$this->medication_drug_id && !$this->drug_id) {
            $this->addError('medication_name', 'A drug must be provided.');
        }
        if ($this->start_date && $this->end_date && $this->start_date > $this->end_date) {
            $this->addError('end_date', 'Stop date must be on or after start date');
        }
        parent::afterValidate();
    }

    /**
     * @return bool
     */
    public function hasRisk()
    {
        $med = $this->drug ? : $this->medication_drug ? : null;

        if ($med) {
            return count(OphCiExaminationRisk::findForTagIds(array_map(
                function($t) {
                    return $t->id;
                }, $med->tags
            ))) > 0;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getMedicationDisplay()
    {
        return $this->medication_name ? :
            ($this->medication_drug ? (string) $this->medication_drug :
                ($this->drug ? $this->drug->tallmanlabel : ''));
    }

    /**
     * @return string
     */
    public function getAdministrationDisplay()
    {
        $res = array();
        foreach (array('dose', 'units', 'option', 'route', 'frequency') as $k) {
            if ($this->$k) {
                $res[] = $this->$k;
            }
        }
        return implode(' ', $res);
    }

    /**
     * @return string
     */
    public function getDatesDisplay()
    {
        $res = array();
        if ($this->start_date) {
            $res[] = \Helper::formatFuzzyDate($this->start_date);
        }
        if ($this->end_date) {
            if (count($res)) {
                $res[] = '-';
            }
            $res[] = \Helper::formatFuzzyDate($this->end_date);
        }
        if ($this->stop_reason) {
            $res[] = "({$this->stop_reason})";
        }
        return implode(' ', $res);
    }

    public function getEndDateDisplay($empty_text = '')
    {
        if ($this->end_date) {
            return \Helper::formatFuzzyDate($this->end_date);
        } else {
            return $empty_text;
        }
    }

    public function getStartDateDisplay()
    {
        return '<div class="oe-date">' . \Helper::convertFuzzyDate2HTML($this->start_date) . '</div>';
    }

    public function getStopDateDisplay()
    {
        return '<div class="oe-date">' . \Helper::convertFuzzyDate2HTML($this->end_date) . '</div>';
    }

    public function getStopReasonDisplay(){
        $res = array();
        if ($this->stop_reason) {
            $res[] = "{$this->stop_reason}";
        }
        return implode(' ', $res);
    }
    /**
     * Assumes that all route options indicate laterality.
     *
     * @return string
     */
    public function getLateralityDisplay()
    {
        if ($this->option) {
            switch (strtolower($this->option)) {
                case 'left':
                    return 'L';
                case 'right':
                    return 'R';
                case 'both':
                    return 'B';
                default:
                    return '?';
            }
        }
    }

    public function getDoseAndFrequency(){
        $result = [];

        if ($this->dose) {
            if ($this->units) {
                $result[] = $this->dose . ' ' . $this->units;
            } else {
                $result[] = $this->dose;
            }
        }

        if ($this->frequency) {
            $result[] = $this->frequency;
        }

        return implode(' , ', $result    );
    }

    /**
     * @return \DrugRouteOption[]
     */
    public function routeOptions()
    {
        if ($this->route) {
            return $this->route->options;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $res = array();
        foreach (array('ArchiveMedication', 'Administration', 'Dates') as $k) {
            if ($str = $this->{'get' . $k . 'Display'}()) {
                $res[] = $str;
            }
        }
        return implode(', ', $res);
    }

    /**
     * @return bool
     */
    public function prescriptionNotCurrent()
    {
        return ($this->prescription_item_id
            && ($this->prescription_item_deleted
                || $this->prescription_not_synced
                || $this->prescription_event_deleted));
    }
}
