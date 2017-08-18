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
 *
 * relations:
 * @property HistoryMedications $element
 * @property \MedicationDrug $medication_drug
 * @property \Drug $drug
 * @property \DrugRoute $route
 * @property \DrugRouteOption $option
 * @property \DrugFrequency $frequency
 * @property HistoryMedicationsStopReason $stop_reason
 */
class HistoryMedicationsEntry extends \BaseElement
{
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
        );
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
        foreach (array('dose', 'route', 'option', 'frequency') as $k) {
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