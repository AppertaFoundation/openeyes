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

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "et_ophciexamination_medicationmanagement".
 *
 * The followings are the available columns in table 'et_ophciexamination_medicationmanagement':
 * @property integer $id
 * @property string $event_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property \Event $event
 * @property \User $createdUser
 * @property \User $lastModifiedUser
 */
class MedicationManagement extends BaseMedicationElement
{
    public $do_not_save_entries = false;

    public $widgetClass = 'OEModule\OphCiExamination\widgets\MedicationManagement';

    public static $entry_class = MedicationManagementEntry::class;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'et_ophciexamination_medicationmanagement';
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'event_id' => 'Event',
			'last_modified_user_id' => 'Last Modified User',
			'last_modified_date' => 'Last Modified Date',
			'created_user_id' => 'Created User',
			'created_date' => 'Created Date',
		);
	}

    public function getEntryRelations()
    {
        return array(
            'entries' => array(
                self::HAS_MANY,
                MedicationManagementEntry::class,
                array('id' => 'event_id'),
                'through' => 'event',
                'on' => "usage_type = '".MedicationManagementEntry::getUsageType()."' AND usage_subtype = '".MedicationManagementEntry::getUsageSubtype()."' ",
                'order' => 'entries.start_date_string_YYYYMMDD DESC, entries.end_date_string_YYYYMMDD DESC, entries.last_modified_date'
            ),
            'visible_entries' => array(
                self::HAS_MANY,
                MedicationManagementEntry::class,
                array('id' => 'event_id'),
                'through' => 'event',
                'on' => "hidden = 0 AND usage_type = '".MedicationManagementEntry::getUsageType()."' AND usage_subtype = '".MedicationManagementEntry::getUsageSubtype()."' ",
                'order' => 'visible_entries.start_date_string_YYYYMMDD DESC, visible_entries.end_date_string_YYYYMMDD DESC, visible_entries.last_modified_date'
            ),
        );
    }

    /**
     * @return MedicationManagementEntry[]
     */

    public function getContinuedEntries()
    {
        return array_filter($this->visible_entries, function($e){
            return $e->continue == 1;
        });
    }

    /**
     * @return MedicationManagementEntry[]
     */

    public function getStoppedEntries()
    {
        return array_filter($this->visible_entries, function($e){
            return !is_null($e->end_date_string_YYYYMMDD);
        });
    }

    /**
     * @return MedicationManagementEntry[]
     */

    public function getPrescribedEntries()
    {
        return array_filter($this->visible_entries, function($e){
            return $e->prescribe == 1;
        });
    }

    /**
     * @return MedicationManagementEntry[]
     */

    public function getOtherEntries()
    {
        return array_filter($this->visible_entries, function($e){
            return $e->prescribe == 0 && is_null($e->end_date_string_YYYYMMDD) && $e->continue == 0;
        });
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MedicationManagement the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getContainer_form_view()
    {
        return false;
    }

    public function getContainer_update_view()
    {
        return '//patient/element_container_form';
    }

    public function getContainer_create_view()
    {
        return '//patient/element_container_form';
    }

    protected function saveEntries()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition("event_id = :event_id AND usage_type = '".MedicationManagementEntry::getUsageType()."' AND usage_subtype = '".MedicationManagementEntry::getUsageSubtype()."'");
        $criteria->params['event_id'] = $this->event->id;
        $orig_entries = MedicationManagementEntry::model()->findAll($criteria);
        $saved_ids = array();
        foreach ($this->entries as $entry) {
            /** @var MedicationManagementEntry $entry */
            $entry->event_id = $this->event->id;

            /* Why do I have to do this? */
            if(isset($entry->id) && $entry->id > 0) {
                $entry->setIsNewRecord(false);
            }

            if(!$entry->save()) {
                foreach ($entry->errors as $err) {
                    $this->addError('entries', implode(', ', $err));
                }
                return false;
            }
            $saved_ids[] = $entry->id;
        }

        foreach ($orig_entries as $orig_entry) {
            if(!in_array($orig_entry->id, $saved_ids)) {
                $orig_entry->delete();
            }
        }
        if(count($this->getPrescribedEntries()) > 0) {
            $this->generatePrescriptionEvent();
        }
        return true;
    }

    private function generatePrescriptionEvent()
    {
        $prescription = new \Event();
        $prescription->episode_id = $this->event->episode_id;
        $criteria = new \CDbCriteria();
        $criteria->addCondition("class_name = :class_name");
        $criteria->params['class_name'] = 'OphDrPrescription';
        $prescription_event_type = \EventType::model()->findAll($criteria);
        $prescription->event_type_id = $prescription_event_type[0]->id;
        $prescription->event_date = $this->event->event_date;
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

    private function getPrescriptionDetails()
    {
        $entries = $this->getPrescribedEntries();
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

    public function loadFromExisting($element)
    {
        return;
    }
}
