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
use OEModule\OphCiExamination\widgets\HistoryMedications as HistoryMedicationsWidget;
use CHtml;
use Event;
use EventMedicationUse;
use User;
use Yii;

/**
 * Class HistoryMedications
 *
 * @package OEModule\OphCiExamination\models
 *
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property EventMedicationUse[] $entries
 * @property HistoryMedicationsEntry[] $current_entries
 * @property HistoryMedicationsEntry[] $closed_entries
 * @property HistoryMedicationsEntry[] $prescribed_entries
 */
class HistoryMedications extends BaseMedicationElement
{
    use traits\CustomOrdering;
    protected $default_view_order = 25;
    protected $auto_validate_relations = true;

    protected $widgetClass = HistoryMedicationsWidget::class;
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
            array('event_id, entries, no_systemic_medications_date, no_ophthalmic_medications_date', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array_merge(
            array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'entries' => array(
                self::HAS_MANY,
                'OEModule\OphCiExamination\models\HistoryMedicationsEntry',
                'element_id',
            ),
            ),
            $this->getEntryRelations()
        );
    }

    public function afterValidate()
    {
        $no_systemic_medications = true;
        $no_ophthalmic_medications = true;
        foreach ($this->entries as $entry) {
            if ($entry->route_id && $entry->route->isEyeRoute()) {
                $no_ophthalmic_medications = false;
            } else {
                $no_systemic_medications = false;
            }

            if (!$no_systemic_medications && !$no_ophthalmic_medications) {
                break;
            }
        }

        $no_medications = 'no_ophthalmic_medications';
        if (!$this->{$no_medications . '_date'} && ${$no_medications}) {
            $this->addError($no_medications, 'Please confirm the patient is not taking any eye medications');
        }

        parent::afterValidate();
    }

    protected function errorAttributeException($attribute, $message)
    {
        if ($attribute === CHtml::modelName($this) . '_entries') {
            if (preg_match('/^(\d+)/', $message, $match) === 1) {
                return CHtml::modelName($this) . '_entries tbody tr:eq(' . ($match[1]-1) . ')';
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
            $new = $this->createConvertedHistoryEntry($entry);
            $new->is_copied_from_previous_event = true;
            $entries[] = $new;
        }
        $this->no_systemic_medications_date = $element->no_systemic_medications_date;
        $this->no_ophthalmic_medications_date = $element->no_ophthalmic_medications_date;
        $this->entries = $entries;
        $this->assortEntries();
        $this->originalAttributes = $this->getAttributes();
    }

    /**
     * @return bool
     */
    public function hasRisks()
    {
        foreach ($this->entries as $entry) {
            if (!$entry->isStopped() && $entry->hasRisk()) {
                return true;
            }
        }
        return false;
    }

    public function __toString()
    {
        return 'Current: ' . implode(' <br /> ', $this->currentOrderedEntries) .
            ' Stopped: ' . implode(' <br /> ', $this->stoppedOrderedEntries);
    }


    public function getTileSize($action)
    {
        $action_list = array('view', 'removed');
        return in_array($action, $action_list) ? 2 : null;
    }

    public function isIndividual($action)
    {
        $action_list = array('view', 'removed');
        return !in_array($action, $action_list);
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
        $usage_type_condition = "usage_type = '". EventMedicationUse::getUsageType()."' AND usage_subtype = '". EventMedicationUse::getUsageSubtype()."'";

        return array(
            'entries' => array(
                self::HAS_MANY,
                EventMedicationUse::class,
                array('id' => 'event_id'),
                'through' => 'event',
                'on' => $usage_type_condition,
                'order' => 'entries.start_date DESC, entries.end_date DESC, entries.last_modified_date'
            ),
            'current_entries' => array(
                self::HAS_MANY,
                EventMedicationUse::class,
                array('id' => 'event_id'),
                'on' => "$usage_type_condition AND (current_entries.end_date > NOW() OR current_entries.end_date is NULL)",
                'through' => 'event',
                'order' => 'current_entries.start_date DESC, current_entries.end_date DESC, current_entries.last_modified_date'
            ),
            'closed_entries' => array(
                self::HAS_MANY,
                EventMedicationUse::class,
                array('id' => 'event_id'),
                'on' => "$usage_type_condition AND (closed_entries.end_date < NOW() AND closed_entries.end_date is NOT NULL)",
                'through' => 'event',
                'order' => 'closed_entries.start_date ASC, closed_entries.end_date ASC, closed_entries.last_modified_date'
            ),
            'prescribed_entries' => array(
                self::HAS_MANY,
                EventMedicationUse::class,
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
        $api = Yii::app()->moduleAPI->get('OphDrPrescription');
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
                    $entry = new EventMedicationUse();
                    $entry->loadFromPrescriptionItem($item);
                    $entry->usage_type = 'OphDrPrescription';
                    $entry->latest_med_use_id = $item->latest_med_use_id;
                    if ($this->widget && $this->widget->inSummaryOrViewMode()) {
                        $entry->id = $item->id;
                    }
                    $untracked[] = $entry;
                }
            }
        }

        return $untracked;
    }
}
