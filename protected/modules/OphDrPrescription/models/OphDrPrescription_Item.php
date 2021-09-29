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

use OEModule\OphCiExamination\models\MedicationManagementEntry;

/**
 * Class OphDrPrescription_Item
 * @property OphDrPrescription_ItemTaper[] $tapers
 * @property Element_OphDrPrescription_Details $prescription
 * @property OphDrPrescription_DispenseCondition $dispense_condition
 * @property OphDrPrescription_DispenseLocation $dispense_location
 */
class OphDrPrescription_Item extends EventMedicationUse
{
    // Maximum characters per line on FP10 form is roughly 31.
    // Maximum characters per line on WP10 form is roughly 30.
    // Assuming the space left of the white margin can be used for printing, this could be expanded further.
    const MAX_FPTEN_LINE_CHARS = 31;
    const MAX_WPTEN_LINE_CHARS = 30;

    public $original_item_id;
    public $from_medication_management = false;

    public $taper_support = true;

        private $fpten_line_usage = array();
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
            array('dose, dispense_location_id, dispense_condition_id, start_date, frequency_id, duration_id, route_id', 'required'),
            array('duration_id', 'validateDuration')
        ));
    }

    public function relations()
    {
        return array_merge(parent::relations(), array(
            'tapers' => array(self::HAS_MANY, OphDrPrescription_ItemTaper::class, 'item_id'),
            'prescription' => array(
                self::BELONGS_TO,
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
        return $this->getMedicationDisplay(true);
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
                $this->dose = $defaults->default_dose ? $defaults->default_dose : $this->medication->default_dose;
                $this->dose_unit_term = $defaults->default_dose_unit_term ? $defaults->default_dose_unit_term : $this->medication->default_dose_unit_term;
                $this->form_id = $defaults->default_form_id ? $defaults->default_form_id : $this->medication->default_form_id;
            } else {
                $this->frequency_id = null;
                $this->route_id = $this->medication->default_route_id ?? null;
                $this->dose = $this->medication->default_dose ?? null;
                $this->dose_unit_term = $this->medication->default_dose_unit_term ?? null;
                $this->form_id = $this->medication->default_form_id ?? null;
            }
        }
    }

    public function afterValidate()
    {
        foreach ($this->tapers as $i => $taper) {
            if (!$taper->validate()) {
                foreach ($taper->getErrors() as $fld => $err) {
                    $this->addError("taper_{$i}_{$fld}", 'Taper (' . ($i + 1) . '): ' . implode(', ', $err));
                }
            }
        }
    }

    /**
     * @param bool $include_tapers
     * @return DateTime|null
     * @throws Exception
     */
    public function stopDateFromDuration($include_tapers = true)
    {
        if (in_array($this->medicationDuration->name, array('Other', 'Ongoing')) || is_null($this->prescription->event)) {
            return null;
        }

        $start_date = new DateTime($this->prescription->event->event_date);
        if ($this->medicationDuration->name === 'Once') {
            return $start_date;
        }
        $end_date = $start_date->add(DateInterval::createFromDateString($this->medicationDuration->name));
        if ($include_tapers) {
            foreach ($this->tapers as $taper) {
                if (in_array($taper->duration->name, array('Other', 'Ongoing'))) {
                    return null;
                }

                if ($taper->duration->name !== 'Once') {
                    $end_date = $end_date->add(DateInterval::createFromDateString($taper->duration->name));
                }
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
        $drug_label = $this->medication->label;

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

    /**
     * Update the item based on its
     * management item
     */

    public function updateFromManagementItem()
    {
        $attributes_to_check = array(
            'medication_id',
            'pgdpsd_id',
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

        if (!$mgment_item = MedicationManagementEntry::model()->findByAttributes(array("prescription_item_id" => $this->id))) {
            return false;
        }
        /** @var MedicationManagementEntry $mgment_item */
        foreach ($attributes_to_check as $attribute) {
            $this->setAttribute($attribute, $mgment_item->getAttribute($attribute));
        }

        $this->updateTapers($mgment_item->tapers);
        $this->refresh();
        $this->save();
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
        if (preg_match("/^\d+/", $this->medicationDuration->name)) {
            return 'FREQUENCY: ' . strtoupper($this->frequency->term) . ' FOR ' . strtoupper($this->medicationDuration->name);
        }

        return 'FREQUENCY: ' . strtoupper($this->frequency->term) . ' ' . strtoupper($this->medicationDuration->name);
    }

    public function fpTenDose()
    {
        return 'DOSE: ' . (is_numeric($this->dose) ? strtoupper($this->dose) . ' ' . strtoupper($this->dose_unit_term) : strtoupper($this->dose))
            . ', ' . strtoupper($this->route->term)
            . ($this->medicationLaterality ? ' (' . strtoupper($this->medicationLaterality->name) . ')' : null);
    }

    protected function beforeDelete()
    {
        foreach ($this->tapers as $taper) {
            $taper->delete();
        }
        return parent::beforeDelete();
    }

    protected function beforeSave()
    {

        $end_date = $this->stopDateFromDuration();
        $this->end_date = $end_date ? $end_date->format('Y-m-d') : null;
        if ($this->end_date) {
            $this->setStopReasonTo('Course complete');
        } else {
            $this->stop_reason_id = null;
        }

        return parent::beforeSave();
    }
}
