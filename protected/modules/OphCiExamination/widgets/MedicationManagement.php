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

namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\MedicationManagement as MedicationManagementElement;
use OEModule\OphCiExamination\models\MedicationManagementEntry;

class MedicationManagement extends BaseMedicationWidget
{
    protected static $elementClass = MedicationManagementElement::class;

    protected function isAtTip()
    {
        return true; //TODO idk
    }

    private function setEntries()
    {
            $new_entries = [];
            $element = $this->element->getModuleApi()->getLatestElement(\OEModule\OphCiExamination\models\HistoryMedications::class, $this->patient);
        if (!is_null($element)) {
            $entries = array_merge($element->current_entries, $element->getEntriesForUntrackedPrescriptionItems($this->patient));
            /** @var MedicationManagementElement $element */
            foreach ($entries as $entry) {
                /** @var \EventMedicationUse $new_entry */
                $medication_management_entry = false;

                if ($entry->medication) {
                    foreach ($entry->medication->medicationSets as $medSet) {
                        if ($medSet->name === "medication_management") {
                            $medication_management_entry = true;
                            break;
                        }
                    }
                }

                if ($medication_management_entry) {
                    $new_entry = new MedicationManagementEntry();
                    if ($entry->prescription_item_id) {
                                            $new_entry->attributes = $entry->getAttributes();
                                            $new_entry->prescription_item_id = null;
                                            $new_entry->bound_key = $entry->bound_key;
                                            $new_entry->usage_type = 'OphDrPrescription';
                    } else {
                        $new_entry->attributes = $entry->getOriginalAttributes();
                        $new_entry->bound_key = $entry->bound_key;
                        $new_entry->id = null;
                        $new_entry->setIsNewRecord(true);
                    }
                    $new_entries[] = $new_entry;
                }
            }
        } else {
            $api = \Yii::app()->moduleAPI->get('OphDrPrescription');
            $untracked_prescription_items = $api->getPrescriptionItemsForPatient($this->patient);
            foreach ($untracked_prescription_items as $item) {
                $medication_management_entry = false;
                // Check if it's meds management set
                foreach ($item->medication->medicationSets as $medSet) {
                    if ($medSet->name === "medication_management") {
                        $medication_management_entry = true;
                        break;
                    }
                }
                if ($medication_management_entry) {
                    $entry = new MedicationManagementEntry();
                    $entry->loadFromPrescriptionItem($item);
                    $entry->usage_type = 'OphDrPrescription';

                    $new_entries[] = $entry;
                }
            }
        }

            $this->element->entries = $new_entries;
    }

    public function getViewData()
    {
        if (empty($this->element->entries) && !\Yii::app()->request->isPostRequest) {
            $this->setEntries();
        }

        return parent::getViewData();
    }
}