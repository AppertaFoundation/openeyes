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
 * Class HistoryMedicationsEntry
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
        $required_fields = 'start_date';
        if (!isset($this->getApp()->params['enable_concise_med_history']) || !$this->getApp()->params['enable_concise_med_history'])
        {
            $required_fields .= ', frequency_id, route_id';
        }
        return array(
            array('element_id, medication_drug_id, drug_id, medication_name, route_id, option_id, dose, frequency_id, start_date, end_date, stop_reason_id, prescription_item_id', 'safe'),
            array($required_fields, 'required'),
            array('start_date', 'OEFuzzyDateValidatorNotFuture'),
            array('end_date', 'OEFuzzyDateValidator'),
            array('option_id', 'validateOptionId'),
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
     * @inheritdoc
     */
    protected function afterFind()
    {
        parent::afterFind();
        if ($this->end_date !== null) {
            $this->originallyStopped = true;
        }
        if ($this->prescription_item) {
            $this->initialiseFromPrescriptionItem();
        }
    }

    /**
     * To ensure that the entry always reflects the latest data from the prescription item,
     * we set it's properties from the prescription.
     */
    protected function initialiseFromPrescriptionItem()
    {
        if (!$item = $this->prescription_item) {
            throw new \CException('Cannot initialise entry with prescription item when no item set on ' . static::class);
        };

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
        $end_date = $item->stopDateFromDuration();
        $compare_date = new \DateTime();

        if ($this->element && $this->element->event && $this->element->event->event_date) {
            $compare_date = \DateTime::createFromFormat('Y-m-d', $this->element->event->event_date);
        }
        if ($end_date && $end_date < $compare_date) {
            $this->end_date = $end_date->format('Y-m-d');
        }

    }

    /**
     * @param static $element
     * @inheritdoc
     */
    public function loadFromExisting($element)
    {
        parent::loadFromExisting($element);
        if ($this->end_date !== null) {
            $this->originallyStopped = true;
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

    public function validateOptionId()
    {
        if (!$this->option_id && $this->route && $this->route->options) {
            $this->addError('option_id', "Must specify an option for route '{$this->route->name}'");
        }
    }

    public function afterValidate()
    {
        if (!$this->medication_name && !$this->medication_drug_id && !$this->drug_id) {
            $this->addError('medication_name', 'A drug must be provided.');
        }
        parent::afterValidate();
    }

    /**
     * @return string
     */
    public function getMedicationDisplay()
    {
        return $this->medication_name ? :
            ($this->medication_drug ? (string) $this->medication_drug :
                ($this->drug ? (string) $this->drug : ''));
    }

    /**
     * @return string
     */
    public function getAdministrationDisplay()
    {
        $res = array();
        foreach (array('dose', 'option', 'route', 'frequency') as $k) {
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
        foreach (array('Medication', 'Administration', 'Dates') as $k) {
            if ($str = $this->{'get' . $k . 'Display'}()) {
                $res[] = $str;
            }
        }
        return implode(', ', $res);
    }
}