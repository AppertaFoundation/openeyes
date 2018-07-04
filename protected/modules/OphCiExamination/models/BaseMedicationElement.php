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
            array('event_id, entries', 'safe'),
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
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'entries' => array(
                self::HAS_MANY,
                \EventMedicationUse::class,
                array('id' => 'event_id'),
                'through' => 'event',
                'order' => 'entries.start_date_string_YYYYMMDD ASC, entries.end_date_string_YYYYMMDD ASC, entries.last_modified_date'
            ),
            'current_entries' => array(
                self::HAS_MANY,
                \EventMedicationUse::class,
                array('id' => 'event_id'),
                'on' => "usage_type = 'OphCiExamination' AND (current_entries.end_date_string_YYYYMMDD > NOW() OR current_entries.end_date_string_YYYYMMDD is NULL)",
                'through' => 'event',
                'order' => 'current_entries.start_date_string_YYYYMMDD DESC, current_entries.end_date_string_YYYYMMDD DESC, current_entries.last_modified_date'
            ),
            'closed_entries' => array(
                self::HAS_MANY,
                \EventMedicationUse::class,
                array('id' => 'event_id'),
                'on' => "usage_type = 'OphCiExamination' AND (closed_entries.end_date_string_YYYYMMDD < NOW() AND closed_entries.end_date_string_YYYYMMDD is NOT NULL)",
                'through'=>'event',
                'order' => 'closed_entries.start_date_string_YYYYMMDD ASC, closed_entries.end_date_string_YYYYMMDD ASC, closed_entries.last_modified_date'
            ),
            'prescribed_entries' => array(
                self::HAS_MANY,
                \EventMedicationUse::class,
                array('id' => 'event_id'),
                'on' => "usage_type = 'OphDrPrescription'",
                'through'=>'event',
                'order' => 'prescribed_entries.start_date_string_YYYYMMDD DESC, prescribed_entries.end_date_string_YYYYMMDD DESC, prescribed_entries.last_modified_date'
            )
        );
    }
    /**
     * @inheritdoc
     */
    protected function afterSave()
    {
        if(!$this->do_not_save_entries) {
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
        $criteria->addCondition("event_id = :event_id");
        $criteria->params['event_id'] = $this->event_id;
        $orig_entries = \EventMedicationUse::model()->findAll($criteria);
        $saved_ids = array();
        foreach ($this->entries as $entry) {
            /** @var \EventMedicationUse $entry */
            $entry->event_id = $this->event_id;
            if(!$entry->save()) {
                foreach ($entry->errors as $err) {
                    $this->addError('entries', implode(', ', $err));
                }
                return false;
            }
            $saved_ids[] = $entry->id;
        }
        foreach ($orig_entries as $entry) {
            if(!in_array($entry->id, $saved_ids)) {
                $entry->delete();
            }
        }
        if(count($this->entries_to_prescribe) > 0) {
            $this->generatePrescriptionEvent();
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
        return \RefMedicationRoute::model()->findAll('deleted_date IS NULL');
    }
    /**
     * @return \CActiveRecord[]
     */
    public function getFrequencyOptions()
    {
        return \RefMedicationFrequency::model()->findAll('deleted_date IS NULL');
    }
    /**
     * @return \CActiveRecord[]
     */
    public function getLateralityOptions()
    {
        return \RefMedicationLaterality::model()->findAll('deleted_date IS NULL');
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
            if($entry->usage_type == 'OphCiExamination') {
                if(!is_null($entry->end_date)) {
                    $closed[] = $entry;
                }
                else {
                    $current[] = $entry;
                }
            }
            elseif ($entry->usage_type == 'OphDrPrescription') {
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
        return array_filter($this->entries, function($entry) {
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
        return array_filter($this->entries, function($entry) {
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
        if ($action=='view'){
            return 25;
        }
        else{
            return parent::getDisplayOrder($action);
        }
    }
    public function afterValidate()
    {
        // Validate entries
        foreach ($this->entries as $key => $entry) {
            if(!$entry->validate()) {
                foreach ($entry->getErrors() as $field=>$error) {
                    if(in_array($field, ['dose', 'frequency_id', 'route_id', 'laterality'])) {
                        $attr = "entries_{$key}_dfrl_error";
                    }
                    else {
                        $attr = "entries_{$key}_{$field}_error";
                    }
                    $this->addError($attr, ($key+1).' - '.implode(', ', $error));
                }
            }
        }
        parent::afterValidate();
    }
    private function getPrescriptionDetails()
    {
        $entries = $this->entries_to_prescribe;
        if(is_null($this->prescription_details)) {
            $prescription_details = new \Element_OphDrPrescription_Details();
            $prescription_items = array();
            foreach($entries as $entry) {
                // search for the medication by name in the OLD drug table
                // insert if not found!!
                // same for route, frequency, duration!!!
                $prescription_items[] = $this->getPresciptionItem($entry);
            }
            $prescription_details->items = $prescription_items;
            $this->prescription_details = $prescription_details;
        }
        return $this->prescription_details;
    }
    private function generatePrescriptionEvent()
    {
        $entries = $this->entries_to_prescribe;
        $prescription = new \Event();
        $prescription->episode_id = $this->event->episode_id;
        $criteria = new \CDbCriteria();
        $criteria->addCondition("class_name = :class_name");
        $criteria->params['class_name'] = 'OphDrPrescription';
        $prescription_event_type = \EventType::model()->findAll($criteria);
        $prescription->event_type_id = $prescription_event_type[0]->id;
        if(!$prescription->save()) {
            \Yii::trace(print_r($prescription->errors, true));
        }
        $prescription_details = $this->getPrescriptionDetails();
        $prescription_details->event_id = $prescription->id;
        if(!$prescription_details->save()){
            \Yii::trace(print_r($prescription_details->errors, true));
        }
        foreach($prescription_details->items as $item){
            $item->prescription_id = $prescription_details->id;
            if(!$item->save()) {
                \Yii::trace(print_r($item->errors, true));
            }
        }
    }
    private function getPresciptionItem( $entry )
    {
        $item = new \OphDrPrescription_Item();
        $item->dose = $entry->dose;
        if($entry->frequency_id) {
            $selected_frequency = \RefMedicationFrequency::model()->findByPk($entry->frequency_id);
            $f_criteria = new \CDbCriteria();
            $f_criteria->addCondition("name = :name");
            $f_criteria->params['name'] = $selected_frequency->code;
            $frequency = \DrugFrequency::model()->findAll($f_criteria);
            if (count($frequency) > 0) {
                $item->frequency_id = $frequency[0]->id;
            } else {
                $new_freq = new \DrugFrequency();
                $new_freq->name = $selected_frequency->code;
                $new_freq->long_name = $selected_frequency->term;
                $new_freq->save();
                $item->frequency_id = $new_freq->id;
            }
        }
        if($entry->route_id) {
            $selected_route = \RefMedicationRoute::model()->findByPk($entry->route_id);
            $r_criteria = new \CDbCriteria();
            $r_criteria->addCondition("name = :name");
            $r_criteria->params['name'] = $selected_route->term;
            $route = \DrugRoute::model()->findAll($r_criteria);
            if (count($route) > 0) {
                $item->route_id = $route[0]->id;
            } else {
                $new_route = new \DrugRoute();
                $new_route->name = $selected_route->term;
                $new_route->save();
                $item->route_id = $new_route->id;
            }
        }
        $selected_drug = \RefMedication::model()->findByPk($entry->ref_medication_id);
        $d_criteria = new \CDbCriteria();
        $d_criteria->addCondition("name = :name");
        $d_criteria->params['name'] = $selected_drug->preferred_term;
        $drug = \Drug::model()->findAll($d_criteria);
        if(count($drug) > 0)
        {
            $item->drug_id = $drug[0]->id;
        }else
        {
            $new_drug = new \Drug();
            $new_drug->name = $selected_drug->preferred_term;
            $new_drug->tallman = $selected_drug->preferred_term;
            $new_drug->form_id = 4;
            if(!$new_drug->save())
            {
                print_r($new_drug->getErrors());
                die;
            }
            $item->drug_id = $new_drug->id;
        }
        $item->route_option_id = $entry->laterality;
        $item->duration_id = 13;
        $item->dispense_condition_id = 1;
        $item->dispense_location_id = 1;
        return $item;
    }
}