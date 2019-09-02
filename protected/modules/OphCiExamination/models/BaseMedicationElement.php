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
abstract class BaseMedicationElement extends \BaseEventTypeElement
{
    protected $auto_update_relations = false;
    protected $auto_validate_relations = false;
    protected $default_from_previous = true;
    /** @var \Element_OphDrPrescription_Details */
    public $prescription_details = null;
    /** @var \EventMedicationUse[] */
    public $entries_to_prescribe = array();
    /**
     * Allows to disable saving of entries, e.g
     * when a MedicationManagement element should handle
     * entries instead of a HistoryMedications element
     *
     * @var bool
     */
    public $do_not_save_entries = false;

    public static $entry_class = \EventMedicationUse::class;

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
        return array_merge(array(
                'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
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
    /**
     * Handles saving of related entries
     */
    protected function saveEntries()
    {
        $criteria = new \CDbCriteria();
        $class = self::$entry_class;
        $criteria->addCondition("event_id = :event_id AND usage_type = '".$class::getUsageType()."' AND usage_subtype = '".$class::getUsageSubtype()."'");
        $criteria->params['event_id'] = $this->event_id;
        $orig_entries = \EventMedicationUse::model()->findAll($criteria);
        $saved_ids = array();
        foreach ($this->entries as $entry) {
            /** @var \EventMedicationUse $entry */
            $entry->event_id = $this->event_id;

            /* Why do I have to do this? */
            if (isset($entry->id) && $entry->id > 0) {
                $entry->setIsNewRecord(false);
            }

            /* ensure corrent usage type and subtype */
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
     * @return \CActiveRecord[]
     */
    public function getRouteOptions()
    {
        return \MedicationRoute::model()->findAll('deleted_date IS NULL');
    }
    /**
     * @return \CActiveRecord[]
     */
    public function getFrequencyOptions()
    {
        return \MedicationFrequency::model()->findAll('deleted_date IS NULL');
    }
    /**
     * @return \CActiveRecord[]
     */
    public function getLateralityOptions()
    {
        return \MedicationLaterality::model()->findAll('deleted_date IS NULL');
    }
    /**
     * Assorts entries into current, closed and prescribed sets
     */
    public function assortEntries()
    {
        $current = array();
        $closed = array();
        $prescribed = array();
        /** @var \EventMedicationUse $entry */
        foreach ($this->entries as $entry) {
            if ($entry->usage_type == 'OphCiExamination') {
                if (!is_null($entry->end_date) && $entry->end_date < date("Y-m-d")) {
                    $closed[] = $entry;
                } else {
                    $current[] = $entry;
                }
            } elseif ($entry->usage_type == 'OphDrPrescription') {
                $prescribed[] = $entry;
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
            /** @var \EventMedicationUse $entry */
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
            /** @var \EventMedicationUse $entry */
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
     * @inheritdoc
     */
    public function getDisplayOrder($action)
    {
        if ($action=='view') {
            return 25;
        } else {
            return parent::getDisplayOrder($action);
        }
    }
    public function afterValidate()
    {
        // Validate entries
        foreach ($this->entries as $key => $entry) {
            if (!$entry->validate()) {
                foreach ($entry->getErrors() as $field => $error) {
                    if (in_array($field, ['dose', 'frequency_id', 'route_id', 'laterality', 'option_id'])) {
                        $attr = "entries_{$key}_dfrl_error";
                    } else {
                        $attr = "entries_{$key}_{$field}_error";
                    }
                    $this->addError($attr, ($key+1).' - '.implode(', ', $error));
                }
            }
        }

        parent::afterValidate();
    }
}