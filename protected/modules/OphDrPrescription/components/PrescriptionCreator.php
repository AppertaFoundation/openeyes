<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class PrescriptionCreator extends \EventCreator
{

    private $items = [];

    public function __construct($episode)
    {
        $prescription_event_type = \EventType::model()->find('name = "Prescription"');
        parent::__construct($episode, $prescription_event_type->id);

        $this->elements['Element_OphDrPrescription_Details'] = new \Element_OphDrPrescription_Details();
    }

    public function addMedicationSet($medication_set_id, $laterality = null)
    {
        $set = \MedicationSet::model()->findByPk($medication_set_id);
        foreach ($set->medicationSetItems as $medication_set_item) {
            $item = new \OphDrPrescription_Item();

            $item->dose = $medication_set_item->default_dose ?: $medication_set_item->medication->default_dose;
            $item->dose_unit_term = $medication_set_item->default_dose_unit_term ?: $medication_set_item->medication->default_dose_unit_term;
            $item->frequency_id = $medication_set_item->default_frequency_id;
            $item->route_id = $medication_set_item->default_route_id ?: $medication_set_item->medication->default_route_id;
            $item->medication_id = $medication_set_item->medication_id;
            $item->duration_id = $medication_set_item->default_duration_id;
            $item->dispense_condition_id = $medication_set_item->default_dispense_condition_id;
            $item->dispense_location_id = $medication_set_item->default_dispense_location_id;
            $item->start_date = date('Y-m-d');
            $item->usage_type = \OphDrPrescription_Item::getUsageType();
            $item->usage_subtype = \OphDrPrescription_Item::getUsageSubtype();

            $item->laterality = $laterality; // If default route is Eye or Ocular or ....

            $item_tapers = array();
            if ($medication_set_item->tapers) {
                foreach ($medication_set_item->tapers as $taper) {
                    $new_taper = new \OphDrPrescription_ItemTaper();
                    $new_taper->item_id = null;
                    $new_taper->frequency_id = $taper->frequency_id;
                    $new_taper->duration_id = $taper->duration_id;
                    $new_taper->dose = $taper->dose;
                    $item_tapers[] = $new_taper;
                }
            }
            $item->tapers = $item_tapers;

            $this->addItem($item);
        }
    }

    public function addItem(\OphDrPrescription_Item $item)
    {
        $this->items[] = $item;
    }

    protected function saveElements($event_id)
    {
        // now this part is needed only because of the afterValidate() in Element_OphDrPrescription_Details
        // the actual save of the items will be performed in $element->updateItems()
        $this->elements['Element_OphDrPrescription_Details']->items = $this->items;

        foreach ($this->elements as $element) {
            if ($element instanceof Element_OphDrPrescription_Details) {
                $element->event_id = $event_id;
                $element->draft = !Yii::app()->user->checkAccess('OprnCreatePrescription');
                if (!$element->save()) {
                    $this->addErrors($element->getErrors());
                    \OELog::log("Element_OphDrPrescription_Details:" . print_r($element->getErrors(), true));
                } else {
                    $element->updateItems($this->items);
                }
            }
        }

        return !$this->hasErrors();
    }
}
