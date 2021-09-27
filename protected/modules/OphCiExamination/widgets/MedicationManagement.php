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

    public function init()
    {
        parent::init();
    }

    protected function isAtTip()
    {
        return true; //TODO idk
    }

    private function setEntries()
    {
        $new_entries = [];
        $history_element = $this->element->getModuleApi()->getLatestElement(\OEModule\OphCiExamination\models\HistoryMedications::class, $this->patient);
        if (!is_null($history_element)) {
            $entries = array_merge($history_element->current_entries, $history_element->getEntriesForUntrackedPrescriptionItems($this->patient));
            $element = $this->element->getModuleApi()->getLatestElement(\OEModule\OphCiExamination\models\MedicationManagement::class, $this->patient);
            if ($element) {
                $entries = array_merge($entries, $element->visible_entries);
            }

            foreach ($entries as $entry) {
                $medication_management_entry = $this->isMedicationManagementEntry($entry);
                if ($medication_management_entry) {
                    $new_entry = $this->setNewMedicationManagementEntry($entry, true);
                    $new_entries[] = $new_entry;
                }
            }
        } else {
            $api = \Yii::app()->moduleAPI->get('OphDrPrescription');
            $untracked_prescription_items = $api->getPrescriptionItemsForPatient($this->patient);

            $prescription_items = [];

            foreach ($untracked_prescription_items as $key => $item) {
                if ($item->latest_med_use_id) {
                    $latest_medication = \EventMedicationUse::model()->findByPk($item->latest_med_use_id);
                    if ($latest_medication && $latest_medication->prescribe === '0' && $latest_medication->usage_subtype === 'Management') {
                        $prescription_items[] = $item;
                    }
                } else {
                    $prescription_items[] = $item;
                }
            }
            $element = $this->element->getModuleApi()->getLatestElement(\OEModule\OphCiExamination\models\MedicationManagement::class, $this->patient);
            $entries = $element ? array_merge($prescription_items, $element->visible_entries) : $prescription_items;

            foreach ($entries as $item) {
                $medication_management_entry = $this->isMedicationManagementEntry($item);
                if ($medication_management_entry) {
                    $entry = $this->setNewMedicationManagementEntry($item);
                    $new_entries[] = $entry;
                }
            }
        }

            $this->element->entries = $new_entries;
    }

    /**
     *
     * Check if entry is in meds management set
     * @param $entry
     * @return bool
     */
    private function isMedicationManagementEntry($entry) : bool
    {
        if ($entry->medication) {
            foreach ($entry->medication->medicationSets as $medSet) {
                if ($medSet->name === "medication_management") {
                    return true;
                }
            }
        }

         return false;
    }

    /**
     *
     * sets new MedicationManagementEntry
     * @param $entry
     * @param bool $examination_element_exists
     * @return MedicationManagementEntry
     */
    private function setNewMedicationManagementEntry($entry, bool $examination_element_exists = false) : MedicationManagementEntry
    {
        $new_entry = new MedicationManagementEntry();
        if ($entry->isPrescription() && !$examination_element_exists) {
            $new_entry->loadFromPrescriptionItem($entry);
            $new_entry->usage_type = 'OphDrPrescription';
            $new_entry->usage_subtype = '';
        } elseif ($entry->prescription_item_id) {
            $new_entry->attributes = $entry->getAttributes();
            $new_entry->prescription_item_id = null;
            $new_entry->bound_key = $entry->bound_key;
            $new_entry->usage_type = 'OphDrPrescription';
            $new_entry->usage_subtype = '';
        } else {
            $new_entry->attributes = $entry->getOriginalAttributes();
            $new_entry->bound_key = $entry->bound_key;
            $new_entry->id = null;
            $new_entry->setIsNewRecord(true);
        }
        $new_entry->latest_med_use_id = $entry->latest_med_use_id;

        return $new_entry;
    }

    public function getViewData()
    {
        if (empty($this->element->entries) && !\Yii::app()->request->isPostRequest) {
            $this->setEntries();
        }

        return parent::getViewData();
    }

    /**
     * Returns a field widget class by type
     *
     * @param int $type
     * @return string
     * @throws Exception In case $type is invalid
     */
    public function getWidgetClassByType(int $type): string
    {
        $field_types = self::getFieldTypes();
        if (array_key_exists($type, $field_types)) {
            return $field_types[$type];
        }

        throw new Exception("Signature type " . $type . " not defined");
    }

    /**
     * @return string[]
     */
    private static function getFieldTypes(): array
    {
        return [
            \BaseSignature::TYPE_LOGGEDIN_USER => \EsignPINField::class,
            \BaseSignature::TYPE_OTHER_USER => \EsignUsernamePINField::class,
            \BaseSignature::TYPE_PATIENT => \EsignSignatureCaptureField::class,
            \BaseSignature::TYPE_LOGGEDIN_MED_USER => EsignPINFieldMedication::class
        ];
    }
}
