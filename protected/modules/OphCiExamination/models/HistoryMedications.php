<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
namespace OEModule\OphCiExamination\models;
/**
 * Class HistoryMedications
 * @package OEModule\OphCiExamination\models
 *
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property \EventMedicationUse[] $entries
 * @property HistoryMedicationsEntry[] $orderedEntries
 * @property HistoryMedicationsEntry[] $currentOrderedEntries
 * @property HistoryMedicationsEntry[] $stoppedOrderedEntries
 */
class HistoryMedications extends BaseMedicationElement
{
    public $widgetClass = 'OEModule\OphCiExamination\widgets\HistoryMedications';
    public $new_entries = array();
    public function tableName()
    {
        return 'et_ophciexamination_history_medications';
    }

    public function behaviors()
    {
        return array(
            'PatientLevelElementBehaviour' => 'PatientLevelElementBehaviour',
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, entries', 'safe'),
            array('entries', 'required', 'message' => 'At least one medication must be recorded, or the History Medications element should be removed.')
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array_merge(array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'entries' => array(
                self::HAS_MANY,
                'OEModule\OphCiExamination\models\HistoryMedicationsEntry',
                'element_id',
            ),
            'orderedEntries' => array(self::HAS_MANY,
                'OEModule\OphCiExamination\models\HistoryMedicationsEntry',
                'element_id',
                'order' => 'orderedEntries.start_date desc, orderedEntries.end_date desc, orderedEntries.last_modified_date'),
        ),
            $this->getEntryRelations()
        );
    }

    public function getStoppedOrderedEntries()
    {
        $stoppedEntries = array_filter($this->orderedEntries, function ($entry) {
            return $entry->end_date && $entry->end_date <= date('Y-m-d', strtotime($this->event->event_date));
        });
        return $stoppedEntries;
    }

    public function getCurrentOrderedEntries()
    {
        $currentEntries = array_filter($this->orderedEntries, function ($entry) {
            return !$entry->end_date || $entry->end_date > date('Y-m-d', strtotime($this->event->event_date));
        });
        return $currentEntries;
    }

    protected function errorAttributeException($attribute, $message)
    {
        if ($attribute === \CHtml::modelName($this) . '_entries') {
            if (preg_match('/^(\d+)/', $message, $match) === 1) {
                return \CHtml::modelName($this) . '_entries tbody tr:eq(' . ($match[1]-1) . ')';
            }
        }
        return parent::errorAttributeException($attribute, $message);
    }
    /**
     * @param HistoryMedications $element
     */
    public function loadFromExisting($element)
    {
        $entries = array();
        foreach ($element->entries as $entry) {
            $new = new \EventMedicationUse();
            $new->loadFromExisting($entry);
            $new->usage_type = \EventMedicationUse::getUsageType();
            $new->usage_subtype = \EventMedicationUse::getUsageSubtype();
            $entries[] = $new;
        }
        $this->entries = $entries;
        $this->assortEntries();
        $this->originalAttributes = $this->getAttributes();
    }

    public function getTileSize($action)
    {
        return $action === 'view' ? 2 : null;
    }

    public function isIndividual($action)
    {
        return $action !=='view';
    }

    public function getDisplayOrder($action, $as_parent = false)
    {
        return $action == 'view' ? 25 : parent::getDisplayOrder($action);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MedicationManagement the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getEntryRelations()
    {
        $usage_type_condition = "usage_type = '".\EventMedicationUse::getUsageType()."' AND usage_subtype = '".\EventMedicationUse::getUsageSubtype()."'";

        return array(
            'entries' => array(
                self::HAS_MANY,
                \EventMedicationUse::class,
                array('id' => 'event_id'),
                'through' => 'event',
                'on' => $usage_type_condition,
                'order' => 'entries.start_date DESC, entries.end_date DESC, entries.last_modified_date'
            ),
            'current_entries' => array(
                self::HAS_MANY,
                \EventMedicationUse::class,
                array('id' => 'event_id'),
                'on' => "$usage_type_condition AND (current_entries.end_date > NOW() OR ( current_entries.end_date is NULL OR current_entries.end_date = ''))",
                'through' => 'event',
                'order' => 'current_entries.start_date DESC, current_entries.end_date DESC, current_entries.last_modified_date'
            ),
            'closed_entries' => array(
                self::HAS_MANY,
                \EventMedicationUse::class,
                array('id' => 'event_id'),
                'on' => "$usage_type_condition AND (closed_entries.end_date < NOW() AND ( closed_entries.end_date is NOT NULL OR closed_entries.end_date != '') )",
                'through' => 'event',
                'order' => 'closed_entries.start_date ASC, closed_entries.end_date ASC, closed_entries.last_modified_date'
            ),
            'prescribed_entries' => array(
                self::HAS_MANY,
                \EventMedicationUse::class,
                array('id' => 'event_id'),
                'on' => $usage_type_condition,
                'through' => 'event',
                'order' => 'prescribed_entries.start_date DESC, prescribed_entries.end_date DESC, prescribed_entries.last_modified_date'
            )
        );
    }

    public function getEntriesForUntrackedPrescriptionItems($patient)
    {
        $untracked = array();
        $api = \Yii::app()->moduleAPI->get('OphDrPrescription');
        if ($api) {
            $tracked_prescr_item_ids = array_map(
                function ($entry) {
                    return $entry->prescription_item_id;
                },
                $this->getPrescriptionEntries()
            );
            $untracked_prescription_items = $api->getPrescriptionItemsForPatient($patient, $tracked_prescr_item_ids);
            if ($untracked_prescription_items) {
                foreach ($untracked_prescription_items as $item) {
                    $entry = new \EventMedicationUse();
                    $entry->loadFromPrescriptionItem($item);
                    $entry->usage_type = 'OphDrPrescription';
                    $untracked[] = $entry;
                }
            }
        }

        return $untracked;
    }

}
