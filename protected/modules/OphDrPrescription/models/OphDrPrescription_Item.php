<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * The followings are the available columns in table 'ophdrprescription_item':.
 *
 * @property string $id
 * @property string $dose
 * @property DrugDuration $duration
 * @property DrugFrequency $frequency
 * @property DrugRoute $route
 * @property DrugRouteOption $route_option
 * @property Drug $drug
 * @property Prescription $prescription
 * @property OphDrPrescription_ItemTaper[] $tapers
 * @property string $comments
 */

class OphDrPrescription_Item extends BaseActiveRecordVersioned
{
    private $fpten_line_usage = array();

    // Maximum characters per line on FP10 form is roughly 31.
    // Maximum characters per line on WP10 form is roughly 30.
    // Assuming the space left of the white margin can be used for printing, this could be expanded further.
    const MAX_FPTEN_LINE_CHARS = 31;
    const MAX_WPTEN_LINE_CHARS = 30;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphDrPrescription_Item the static model class
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
        return 'ophdrprescription_item';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('drug_id, dose, route_id, frequency_id, duration_id, dispense_condition_id, dispense_location_id', 'required'),
            array('route_option_id', 'validateRouteOption'),
            array('comments', 'length', 'max'=>256),
            array('drug_id, dose, route_id, frequency_id, duration_id, id, route_option_id, last_modified_user_id,
            last_modified_date, created_user_id, created_date, dispense_condition_id, dispense_location_id, comments', 'safe'),
            //array('', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, dose, prescription_id, drug_id, route_id, route_option_id, frequency_id, duration_id,
            dispense_condition_id, dispense_location_id', 'safe', 'on' => 'search'),
        );
    }

    public function validateRouteOption($attribute, $params)
    {
        if ($this->route && $this->route->options) {
            foreach ($this->route->options as $option) {
                if ($option->id == $this->route_option_id) {
                    // Option is valid for this route
                    return;
                }
            }
        } else {
            // Route options are ignored
            return;
        }
        $this->addError($attribute, 'Route requires option selection');
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'tapers' => array(self::HAS_MANY, 'OphDrPrescription_ItemTaper', 'item_id'),
            'prescription' => array(self::BELONGS_TO, 'Element_OphDrPrescription_Details', 'prescription_id'),
            'duration' => array(self::BELONGS_TO, 'DrugDuration', 'duration_id'),
            'frequency' => array(self::BELONGS_TO, 'DrugFrequency', 'frequency_id'),
            'route' => array(self::BELONGS_TO, 'DrugRoute', 'route_id'),
            'route_option' => array(self::BELONGS_TO, 'DrugRouteOption', 'route_option_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'drug' => array(self::BELONGS_TO, 'Drug', 'drug_id'),
            'dispense_condition' => array(self::BELONGS_TO, 'OphDrPrescription_DispenseCondition', 'dispense_condition_id'),
            'dispense_location' => array(self::BELONGS_TO, 'OphDrPrescription_DispenseLocation', 'dispense_location_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'drug_id' => 'Drug',
            'dose' => 'Dose',
            'duration_id' => 'Duration',
            'frequency_id' => 'Frequency',
            'route_id' => 'Route',
            'route_option_id' => 'Options',
            'dispense_condition_id' => 'Dispense Condition',
            'dispense_location_id' => 'Dispense Location',
            'comments' => 'Comments',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('dose', $this->dose, true);
        $criteria->compare('prescription_id', $this->prescription_id, true);
        $criteria->compare('drug_id', $this->drug_id, true);
        $criteria->compare('duration_id', $this->duration_id, true);
        $criteria->compare('frequency_id', $this->frequency_id, true);
        $criteria->compare('route_id', $this->route_id, true);
        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Get the number of lines an attribute will use on an FP10 form.
     * @param $attr string
     * @return int
     */
    public function getAttrLength($attr)
    {
        return $this->fpten_line_usage[$attr];
    }

    public function getDescription()
    {
        $return = $this->drug->label;
        $return .= ', '.$this->dose;
        $return .= ' '.$this->frequency->name;
        $return .= ' '.$this->route->name;
        if ($this->route_option) {
            $return .= ' ('.$this->route_option->name.')';
        }
        if (preg_match('/^[0-9]+/', $this->duration->name)) {
            $return .= ' for '.$this->duration->name;
        } else {
            $return .= ' '.$this->duration->name;
        }

        return $return;
    }

    public function loadDefaults()
    {
        if ($this->drug) {
            $this->duration_id = $this->drug->default_duration_id;
            $this->frequency_id = $this->drug->default_frequency_id;
            $this->route_id = $this->drug->default_route_id;
            $this->dose = trim($this->drug->default_dose);
        }
    }

    public function afterValidate()
    {
        foreach ($this->tapers as $i => $taper) {
            if (!$taper->validate()) {
                foreach ($taper->getErrors() as $fld => $err) {
                    $this->addError('tapers', 'Taper ('.($i + 1).'): '.implode(', ', $err));
                }
            }
        }
    }

    /**
     * @return DateTime|null
     * @throws Exception
     */
    public function stopDateFromDuration($include_tapers = true)
    {
        if (in_array($this->duration->name, array('Other', 'Once', 'Until review'))) {
            return null;
        }

        $start_date = new DateTime($this->prescription->event->event_date);
        $end_date = $start_date->add(DateInterval::createFromDateString($this->duration->name));
        if ($include_tapers) {
            foreach ($this->tapers as $taper) {
                if (in_array($taper->duration->name, array('Other', 'Until review'))) {
                    return null;
                }
                $end_date = $end_date->add(DateInterval::createFromDateString($taper->duration->name));
            }
        }
        return $end_date;
    }

    /**
     * Get the number of lines that will be printed out for this specific item.
     * @return int Number of lines used.
     */
    public function fpTenLinesUsed()
    {
        $settings = new SettingMetadata();
        $max_lines = $settings->getSetting('prescription_form_format') === 'WP10' ? self::MAX_WPTEN_LINE_CHARS : self::MAX_FPTEN_LINE_CHARS;
        $item_lines_used = 0;
        $drug_label = $this->drug->label;

        foreach (array(
            'item_drug' => $drug_label,
            'item_dose' => $this->fpTenDose(),
            'item_frequency' => $this->fpTenFrequency(),
            'item_comment' => "Comment: $this->comments"
                 ) as $key => $value) {
            if ($value) {
                $this->fpten_line_usage[$key] =  substr_count(wordwrap($value, $max_lines, '/newline/'), '/newline/') + 1;
            } else {
                $this->fpten_line_usage[$key] = 0;
            }
        }

        foreach ($this->tapers as $index => $taper) {
            foreach (array(
                         "taper{$index}_label" => 'then',
                         "taper{$index}_dose" => $taper->fpTenDose(),
                         "taper{$index}_frequency" => $taper->fpTenFrequency(),
                     ) as $key => $value) {
                $this->fpten_line_usage[$key] =  substr_count(wordwrap($value, $max_lines, '/newline/'), '/newline/') + 1;
            }
        }

        foreach ($this->fpten_line_usage as $line) {
            $item_lines_used += $line;
        }

        if ($item_lines_used > PrescriptionFormPrinter::MAX_FPTEN_LINES) {
            // Add the extra horizontal rule at the bottom of each split print page to the line count.
            $item_lines_used += (int)floor($item_lines_used / PrescriptionFormPrinter::MAX_FPTEN_LINES);
        }

        // Return the truncated number of lines.
        return $item_lines_used;
    }

    public function getAdministrationDisplay()
    {
        $dose = (string) $this->dose;
        $freq = (string) $this->frequency;
        if ($this->tapers) {
            $last_taper = array_slice($this->tapers, -1)[0];
            $last_dose = (string) $last_taper->dose;
            if ($last_dose != $dose) {
                $dose .= ' - ' . $last_dose;
            }
            $last_freq = (string) $last_taper->frequency;
            if ($last_freq != $freq) {
                $freq .= ' - ' . $last_freq;
            }
        }
        return $dose . ($this->route_option ? ' ' . $this->route_option : '') . ' ' . $this->route . ' ' . $freq;
    }

    public function fpTenFrequency()
    {
        if (preg_match("/^\d+/", $this->duration->name)) {
            return 'FREQUENCY: ' . strtoupper($this->frequency->long_name) . ' FOR ' . strtoupper($this->duration->name);
        }

        return 'FREQUENCY: ' . strtoupper($this->frequency->long_name) . ' ' . strtoupper($this->duration->name);
    }

    public function fpTenDose()
    {
        return 'DOSE: ' . (is_numeric($this->dose) ? strtoupper($this->dose) . ' ' . strtoupper($this->drug->dose_unit) : strtoupper($this->dose))
            . ', ' . strtoupper($this->route->name) . ($this->route_option ? ' (' . strtoupper($this->route_option->name) . ')' : null);
    }
}
