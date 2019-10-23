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
 * Class OphDrPrescription_Item
 * @property OphDrPrescription_ItemTaper[] $tapers
 * @property Element_OphDrPrescription_Details $prescription
 * @property OphDrPrescription_DispenseCondition $dispense_condition
 * @property OphDrPrescription_DispenseLocation $dispense_location
 */
class OphDrPrescription_Item extends EventMedicationUse
{

    /**
     * @var int
     *
     * The item from which this item was copied
     */

    public $original_item_id;

    public $taper_support = true;

		private $fpten_line_usage = array();

		// Maximum characters per line on FP10 form is roughly 38.
		// Maximum characters per line on WP10 form is roughly 32.
		// Assuming the space left of the white margin can be used for printing, this could be expanded further.
		const MAX_FPTEN_LINE_CHARS = 38;
		const MAX_WPTEN_LINE_CHARS = 32;
    /**
     * Returns the static model of the specified AR class.
     */

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getUsageType()
    {
        return "OphDrPrescription";
    }

    public static function getUsageSubtype()
    {
        return "";
    }

    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('dose, dispense_location_id, dispense_condition_id, start_date, frequency_id', 'required'),
        ));
    }

    public function relations()
    {
        return array_merge(parent::relations(), array(
            'tapers' => array(self::HAS_MANY, OphDrPrescription_ItemTaper::class, 'item_id'),
            'prescription' => array(
                self::HAS_ONE,
                Element_OphDrPrescription_Details::class,
                array('event_id' => 'event_id'),
            ),
            'dispense_condition' => array(self::BELONGS_TO, 'OphDrPrescription_DispenseCondition', 'dispense_condition_id'),
            'dispense_location' => array(self::BELONGS_TO, 'OphDrPrescription_DispenseLocation', 'dispense_location_id'),
            'parent' => array(self::HAS_MANY, OphDrPrescription_Item::class, 'prescription_item_id'),
        ));
    }

    /**
     * @return string
     * Method to ensure backwards compatibility
     */

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
        return $this->getMedicationDisplay();
    }

    public function loadDefaults(MedicationSet $set = null)
    {
        if ($this->medication_id) {
            $defaults = false;

            if (!is_null($set)) {
                $defaults = MedicationSetItem::model()->find(array(
                    'condition' => 'medication_set_id = :med_set_id AND medication_id = :medication_id',
                    'params' => array(':med_set_id' => $set->id, ':medication_id' => $this->medication_id)
                ));
            }

            if ($defaults) {
                /** @var MedicationSetItem $defaults */
                $this->frequency_id = $defaults->default_frequency_id;
                $this->route_id = $defaults->default_route_id ? $defaults->default_route_id : $this->medication->default_route_id;
                $this->dose = $defaults->default_dose;
                $this->dose_unit_term = $defaults->default_dose_unit_term ? $defaults->default_dose_unit_term : $this->medication->default_dose_unit_term;
                $this->form_id = $defaults->default_form_id ? $defaults->default_form_id : $this->medication->default_form_id;
            } else {
                $this->frequency_id = null;
                $this->route_id = $this->medication->default_route_id;
                $this->dose = 1;
                $this->dose_unit_term = $this->medication->default_dose_unit_term;
                $this->form_id = $this->medication->default_form_id;
            }
        }
    }

    public function afterValidate()
    {
        foreach ($this->tapers as $i => $taper) {
            if (!$taper->validate()) {
                foreach ($taper->getErrors() as $fld => $err) {
                    $this->addError('tapers', 'Taper (' . ($i + 1) . '): ' . implode(', ', $err));
                }
            }
        }
    }

    /**
     * @return DateTime|null
     * @throws Exception
     */
    public function stopDateFromDuration()
    {
        if (in_array($this->drugDuration->name, array('Other', 'Until review'))) {
            return null;
        }

        $start_date = new DateTime($this->prescription->event->event_date);
        $end_date = $start_date->add(DateInterval::createFromDateString($this->drugDuration->name));
        foreach ($this->tapers as $taper) {
            if (in_array($taper->duration->name, array('Other', 'Until review'))) {
                return null;
            }
            $end_date->add(DateInterval::createFromDateString($taper->duration->name));
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
        $dose = (string)$this->dose;
        $freq = (string)$this->frequency;
        $route = (string)$this->route;

        if ($this->tapers) {
            $last_taper = array_slice($this->tapers, -1)[0];
            $last_dose = (string)$last_taper->dose;
            if ($last_dose != $dose) {
                $dose .= ' - ' . $last_dose;
            }
            $last_freq = (string)$last_taper->frequency;
            if ($last_freq != $freq) {
                $freq .= ' - ' . $last_freq;
            }
        }
        return $dose . ($this->laterality ? ' ' . $this->getLateralityDisplay() : '') . ' ' . $route . ' ' . $freq;
    }

    /**
     * Update the item based on its
     * management item
     */

    public function updateFromManagementItem()
    {
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
        );

        if (!$mgment_item = \OEModule\OphCiExamination\models\MedicationManagementEntry::model()->findByAttributes(array("prescription_item_id" => $this->id))) {
            return false;
        }
        /** @var \OEModule\OphCiExamination\models\MedicationManagementEntry $mgment_item */
        foreach ($attributes_to_check as $attribute) {
            $this->setAttribute($attribute, $mgment_item->getAttribute($attribute));
        }

        $this->save();
        $this->updateTapers($mgment_item->tapers);
    }

    public function updateTapers(array $tapers)
    {
        foreach ($this->tapers as $taper) {
            $taper->delete();
        }

        foreach ($tapers as $taper) {
            $new_taper = new OphDrPrescription_ItemTaper();
            $new_taper->setAttributes([
                'item_id' => $this->id,
                'frequency_id' => $taper->frequency_id,
                'duration_id' => $taper->duration_id,
                'dose' => $taper->dose
            ]);

            $new_taper->save();
        }

    }

    public function fpTenFrequency()
    {
        return "Frequency: {$this->frequency->long_name} for {$this->duration->name}";
    }

    public function fpTenDose()
    {
        return 'Dose: ' . (is_numeric($this->dose) ? "{$this->dose} {$this->drug->dose_unit}" : $this->dose)
            . ', ' . $this->route->name . ($this->route_option ? ' (' . $this->route_option->name . ')' : null);
    }

    public function saveTapers()
    {
        foreach ($this->tapers as $taper) {
            $taper->item_id = $this->id;
            $taper->save();
        }
    }

    public function beforeDelete()
    {
        \Yii::app()->db->createCommand("DELETE FROM " . \OphDrPrescription_ItemTaper::model()->tableName() . " WHERE item_id = :item_id")->
        bindValues(array(":item_id" => $this->id))->execute();

        return parent::beforeDelete();
    }
}