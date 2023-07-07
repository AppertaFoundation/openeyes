<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
namespace OEModule\OphCiExamination\models;
use BaseEventTypeElement;
use CActiveRecord;
use CDbCriteria;
use Element_OphDrPrescription_Details;
use Event;
use EventMedicationUse;
use MedicationFrequency;
use MedicationLaterality;
use MedicationRoute;
use User;

abstract class BaseMedicationElement extends BaseEventTypeElement
{
    protected $default_view_order = 25;
    protected $auto_update_relations = false;
    protected $auto_validate_relations = false;
    protected $default_from_previous = true;
    /** @var Element_OphDrPrescription_Details */
    public $prescription_details = null;
    /** @var EventMedicationUse[] */
    public $entries_to_prescribe = array();
    /**
     * Allows to disable saving of entries, e.g
     * when a MedicationManagement element should handle
     * entries instead of a HistoryMedications element
     *
     * @var bool
     */
    public $do_not_save_entries = false;
    public $check_for_duplicate_entries = true;

    public static $entry_class = EventMedicationUse::class;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array(
            'PatientLevelElementBehaviour' => 'PatientLevelElementBehaviour',
        );
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, entries, prescription_id', 'safe'),
            array('entries', 'required', 'message' => 'At least one medication must be recorded, or the element should be removed.')
        );
    }
    /**
     * @inheritdoc
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array_merge(
            array(
                'event' => array(self::BELONGS_TO, Event::class, 'event_id'),
                'user' => array(self::BELONGS_TO, User::class, 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, User::class, 'last_modified_user_id'),
            ),
            $this->getEntryRelations()
        );
    }

    abstract public function getEntryRelations();

    /**
     * @inheritdoc
     */
    protected function afterSave()
    {
        if (!$this->do_not_save_entries) {
            $this->saveEntries();
        }
        parent::afterSave();
    }

    private function mergeSameMedication()
    {
        $entries_by_med_id = [];
        $entries = [];
        $match = false;

        foreach ($this->entries as $entry) {
            if (array_key_exists($entry->medication_id, $entries_by_med_id)) {
                foreach ($entries_by_med_id[$entry->medication_id] as $index) {
                    if ($entry->isEqualsAttributes($entries[$index], false)) {
                        $entries[$index]->laterality = 3;
                        $match = true;
                        break;
                    }
                }
                if (!$match) {
                    $entries[] = $entry;
                    $entries_by_med_id[$entry->medication_id][] = count($entries) - 1;
                }
                $match = false;
            } else {
                $entries[] = $entry;
                $entries_by_med_id[$entry->medication_id] = [count($entries) - 1];
            }
        }

        return $entries;
    }

    /**
     * Handles saving of related entries
     */
    protected function saveEntries()
    {
        $criteria = new CDbCriteria();
        $class = self::$entry_class;
        $criteria->addCondition("event_id = :event_id AND usage_type = '".$class::getUsageType()."' AND usage_subtype = '".$class::getUsageSubtype()."'");
        $criteria->params['event_id'] = $this->event_id;
        $orig_entries = EventMedicationUse::model()->findAll($criteria);
        $saved_ids = array();

        $entries = $this->mergeSameMedication();

        foreach ($entries as $entry) {
            /** @var EventMedicationUse $entry */
            $entry->event_id = $this->event_id;

            /* Why do I have to do this? */
            if (isset($entry->id) && $entry->id > 0) {
                $entry->setIsNewRecord(false);
            }

            /* ensure current usage type and subtype */
            $entry->usage_type = $class::getUsagetype();
            $entry->usage_subtype = $class::getUsageSubtype();

            if (!$entry->save()) {
                foreach ($entry->errors as $err) {
                    $this->addError('entries', implode(', ', $err));
                }
                return false;
            }
            $saved_ids[] = $entry->id;
        }
        foreach ($orig_entries as $entry) {
            if (!in_array($entry->id, $saved_ids)) {
                $entry->delete();
            }
        }

        return true;
    }

    /**
     * @return HistoryMedicationsStopReason[]
     */
    public function getStopReasonOptions()
    {
        return HistoryMedicationsStopReason::model()->findAll();
    }
    /**
     * @return CActiveRecord[]
     */
    public function getRouteOptions()
    {
        return MedicationRoute::model()->findAll([
            'condition' => 'source_type =:source_type AND is_active=1',
            'params' => [':source_type' => 'DM+D'],
            'order' => "term ASC"]);
    }
    /**
     * @return CActiveRecord[]
     */
    public function getFrequencyOptions()
    {
        return MedicationFrequency::model()->findAll('deleted_date IS NULL');
    }
    /**
     * @return CActiveRecord[]
     */
    public function getLateralityOptions()
    {
        return MedicationLaterality::model()->findAll('deleted_date IS NULL');
    }
    /**
     * Assorts entries into current, closed and prescribed sets
     */
    public function assortEntries()
    {
        $current = array();
        $closed = array();
        $prescribed = array();
        /** @var EventMedicationUse $entry */
        foreach ($this->entries as $entry) {
            if ($entry->usage_type == 'OphCiExamination') {
                if ($entry->isStopped()) {
                    $closed[] = $entry;
                } else {
                    $current[] = $entry;
                }
            } elseif ($entry->usage_type == 'OphDrPrescription') {
                if ($entry->isStopped()) {
                    $closed[] = $entry;
                } else {
                    $prescribed[] = $entry;
                }
            }
        }
        $this->current_entries = $current;
        $this->closed_entries = $closed;
        $this->prescribed_entries = $prescribed;
    }
    /**
     * Retrieves the entries that are tracking prescription items
     */
    public function getPrescriptionEntries()
    {
        return array_filter($this->entries, function ($entry) {
            return $entry->prescription_item_id !== null;
        });
    }
    /**
     * Retrieves new entries
     *
     * @deprecated
     */
    public function getNewEntries()
    {
        return array_filter($this->entries, function ($entry) {
            /** @var EventMedicationUse $entry */
            return !$entry->is_copied_from_previous_event && $entry->usage_type == 'OphCiExamination' && $entry->getIsNewRecord();
        });
    }
    /**
     * Returns whether any of the entries carry a risk
     *
     * @return bool
     */
    public function hasRisks()
    {
        foreach ($this->entries as $entry) {
            /** @var EventMedicationUse $entry */
            if ($entry->hasRisk()) {
                return true;
            }
        }
        return false;
    }
    public function __toString()
    {
        return 'Current: ' . implode(' <br /> ', $this->current_entries) .
            ' Stopped: ' . implode(' <br /> ', $this->closed_entries);
    }
    /**
     * @inheritdoc
     */
    function getTileSize($action)
    {
        return 2;
    }
    /**
     * @inheritdoc
     */
    public function isIndividual($action)
    {
        return $action!=='view';
    }

    /**
     * merges entries and selects only the latest medication
     * @param $entries
     * @param $widget
     * @return array
     * @throws \Exception
     */
    public function mergeMedicationEntries($entries, $widget = null) : array
    {
        $medication_entries = [];
        $already_converted_ids = [];

        foreach ($entries as $entry) {
            if ($entry->hasLinkedPrescribedEntry()) {
                $entry_to_add = $entry->prescriptionItem;
            } else {
                $entry_to_add = $entry;
            }
            $latest_medication = $entry_to_add->getLatestMedication();
            if (!in_array($latest_medication->medication_id, $already_converted_ids)) {
                $converted_entry = $this->createConvertedHistoryEntry($latest_medication, true, $widget);
                $converted_entry->event_id = $latest_medication->event_id ?? $latest_medication->copied_from_med_use_id;
                $converted_entry->is_copied_from_previous_event = true;
                if ($latest_medication->isPrescription()) {
                    $converted_entry->usage_type = 'OphDrPrescription';
                    $converted_entry->usage_subtype = '';
                }
                $medication_entries[] = $converted_entry;
                $already_converted_ids[] = $latest_medication->medication_id;
            }
        }

        return $medication_entries;
    }

    /**
     * filters out Medication History elements that are linked when showing in the view mode for Examination
     *
     * @param $entries
     * @param bool $edit_mode
     * @return array
     */
    public function filterHistoryAndManagementMedications($entries, bool $edit_mode = false) : array
    {
        $medication_management_entries = array_filter($entries, function ($entry)  use ($edit_mode) {
            if ($edit_mode) {
                return $entry->usage_subtype === 'Management' && !$entry->prescribe;
            }
            return $entry->usage_subtype === 'Management';
        });

        $history_medication_entries = array_filter($entries, function ($entry) {
            return $entry->usage_subtype === 'History';
        });

        $prescription_entries = array_filter($entries, function ($entry) {
            return $entry->usage_type === 'OphDrPrescription' && !$entry->prescribe && is_null($entry->latest_med_use_id);
        });

        $medication_management_entry_medication_ids = array_map(function ($medication_management_entry) {
            return $medication_management_entry->medication_id;
        }, $medication_management_entries);

        $history_medication_entries = array_filter($history_medication_entries, function ($entry) use ($medication_management_entry_medication_ids) {
            return !in_array($entry->medication_id, $medication_management_entry_medication_ids);
        });

        $new_entries =  array_merge($history_medication_entries, $medication_management_entries);
        $new_entries = array_merge($new_entries, $prescription_entries);

        return $new_entries;
    }

    /**
     * creates a new converted History entry from the entry in the parameter
     * @param $entry
     * @param bool $merging_medications
     * @param null $widget
     * @return EventMedicationUse
     * @throws \Exception
     */
    public function createConvertedHistoryEntry($entry, $merging_medications = false, $widget = null) : EventMedicationUse
    {
        $new = new EventMedicationUse();
        $prescription_item = null;
        $new->loadFromExisting($entry);
        $new->usage_type = EventMedicationUse::getUsageType();
        $new->usage_subtype = EventMedicationUse::getUsageSubtype();
        if ($merging_medications) {
            if (!$widget) {
                $widget = $this->widget;
            }
            if ($widget->inSummaryOrViewMode()) {
                $new->id = $entry->id;
            }
        }
        if ($entry->prescription_item_id) {
            $prescription_item =  $entry->prescriptionItem;
        } elseif ($entry->isPrescription()) {
            $prescription_item = new \OphDrPrescription_Item();
            $prescription_item->loadFromExisting($entry);
        }
        $prescription_end_date = $prescription_item ? $prescription_item->stopDateFromDuration() : null;
        if (!isset($new->end_date) && $prescription_end_date) {
            $new->end_date = $prescription_end_date->format('Y-m-d');
            $course_complete_model = HistoryMedicationsStopReason::model()->findByAttributes([
                'name' => 'Course complete'
            ]);
            $new->stop_reason_id = $course_complete_model ? $course_complete_model->id : null;
        }

        return $new;
    }

    public function isPreviousEntry($entry)
    {
        return $entry->copied_from_med_use_id || $entry->prescription_item_id !== '' || $entry->isPrescription();
    }

    public function afterValidate()
    {
        $previous_entries = array_filter($this->entries, function ($entry) {
            return $this->isPreviousEntry($entry);
        });

        $previous_medication_ids = array_unique(array_map(function ($entry) {
            return $entry->medication_id;
        }, $previous_entries));

        foreach ($this->entries as $key => $entry) {
            if ($this->check_for_duplicate_entries && !$entry->isStopped()) {
                if (in_array($entry->medication_id, $previous_medication_ids) && !$this->isPreviousEntry($entry)) {
                    $same_drug_entries = array_filter($this->entries, function ($current_entry) use ($entry) {
                        return $entry->isDuplicate($current_entry) && $entry !== $current_entry && !$current_entry->isStopped();
                    });

                    foreach ($same_drug_entries as $index => $same_drug_entry) {
                        if (!$this->isPreviousEntry($same_drug_entry)) {
                            if (!$this->getError("entries_{$index}_duplicate_error")) {
                                $this->addError("entries_{$index}_duplicate_error", ($index + 1) . '- The entry is duplicate');
                            }
                        }
                        if (!$this->getError("entries_{$key}_duplicate_error")) {
                            $this->addError("entries_{$key}_duplicate_error", ($key + 1) . '- The entry is duplicate');
                        }
                    }
                } else {
                    $previous_medication_ids[] = $entry->medication_id;
                }
            }

            // Validate entries
            if (!$entry->validate()) {
                foreach ($entry->getErrors() as $field => $error) {
                    $attr = "entries_{$key}_{$field}";
                    $this->addError($attr, ($key + 1) . ' - ' . implode(', ', $error));
                }
            }
        }

        parent::afterValidate();
    }
}
