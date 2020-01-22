<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use PrescriptionCreator;

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
 * @property int $prescription_id
 *
 * The followings are the available model relations:
 * @property \Event $event
 * @property \User $createdUser
 * @property \User $lastModifiedUser
 * @property \Element_OphDrPrescription_Details $prescription
 *
 * @method auditAllergicDrugEntries($target, $action = "allergy_override")
 */
class MedicationManagement extends BaseMedicationElement
{
    use traits\CustomOrdering;
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

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            array(
                "AllergicDrugEntriesBehavior" => array(
                    "class" => "application.behaviors.AllergicDrugEntriesBehavior",
                ),
            )
        );
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
            'prescription_id' => 'Prescription'
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
                'order' => 'entries.start_date DESC, entries.end_date DESC, entries.last_modified_date'
            ),
            'visible_entries' => array(
                self::HAS_MANY,
                MedicationManagementEntry::class,
                array('id' => 'event_id'),
                'through' => 'event',
                'on' => "hidden = 0 AND usage_type = '".MedicationManagementEntry::getUsageType()."' AND usage_subtype = '".MedicationManagementEntry::getUsageSubtype()."' ",
                'order' => 'visible_entries.start_date DESC, visible_entries.end_date DESC, visible_entries.last_modified_date'
            ),
            'prescription' => array(self::BELONGS_TO, \Element_OphDrPrescription_Details::class, 'prescription_id'),
        );
    }

    /**
     * @return MedicationManagementEntry[]
     */

    public function getContinuedEntries()
    {
        $event_date = substr($this->event->event_date, 0, 10);

        return array_filter($this->visible_entries, function ($e) use ($event_date) {
            return ($e->start_date < $event_date &&
                (is_null($e->end_date) || $e->end_date > date('Y-m-d'))
            );
        });
    }

    /**
     * @return MedicationManagementEntry[]
     */

    public function getEntriesStartedToday()
    {
        $event_date = substr($this->event->event_date, 0, 10);

        return array_filter($this->visible_entries, function ($e) use ($event_date) {
            return ($e->start_date == $event_date && is_null($e->end_date) || $e->end_date > date('Y-m-d'));
        });
    }

    /**
     * @return MedicationManagementEntry[]
     */

    public function getStoppedEntries()
    {
        return array_filter($this->visible_entries, function ($e) {
            return !is_null($e->end_date) && $e->end_date <= date('Y-m-d');
        });
    }

    /**
     * @return MedicationManagementEntry[]
     */

    public function getEntriesStoppedToday()
    {
        $event_date = $this->event->event_date;
        return array_filter($this->visible_entries, function ($e) use ($event_date) {
            return ($e->end_date == $event_date);
        });
    }

    /**
     * @return MedicationManagementEntry[]
     */

    public function getPrescribedEntries($visible_only = true)
    {
        $property = $visible_only ? "visible_entries" : "entries";

        return array_filter($this->$property, function ($e) {
            return $e->prescribe == 1;
        });
    }

    public function getOtherEntries()
    {
        $continued = array_map(function ($e) {
            return $e->id;

        }, $this->getContinuedEntries());
        $stopped = array_map(function ($e) {
            return $e->id;

        }, $this->getStoppedEntries());
        $prescribed = array_map(function ($e) {
            return $e->id;

        }, $this->getPrescribedEntries());
        $exclude = array_merge($continued, $stopped, $prescribed);
        $other = array();
        foreach ($this->visible_entries as $entry) {
            if (!in_array($entry->id, $exclude)) {
                $other[] = $entry;
            }
        }

        return $other;
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
        $class = self::$entry_class;

        foreach ($this->entries as $entry) {
            $entry->event_id = $this->event->id;

            /* Why do I have to do this? */
            if (isset($entry->id) && $entry->id > 0) {
                $entry->setIsNewRecord(false);
                $is_new = false;
            } else {
                $is_new = true;
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

            if ($is_new) {
                $id = \Yii::app()->db->getLastInsertID();
                $entry->id = $id;
            }

            if (!$entry->saveTapers()) {
                foreach ($entry->errors as $err) {
                    $this->addError('entries', implode(', ', $err));
                }
                return false;
            }

            $saved_ids[] = $entry->id;

            if ($entry->prescribe) {
                $this->entries_to_prescribe[] = $entry;
            }
        }

        foreach ($orig_entries as $orig_entry) {
            if (!in_array($orig_entry->id, $saved_ids)) {
                $orig_entry->delete();
            }
        }

        $this->createOrUpdatePrescriptionEvent();

        return true;
    }

    private function createOrUpdatePrescriptionEvent()
    {
        if (!is_null($this->prescription_id)) {
            // prescription exists

            /** @var \Element_OphDrPrescription_Details $prescription */
            $prescription = $this->prescription;
            $changed = false;

            /* items to update or remove */
            $existing_mgment_items = array();
            foreach ($prescription->items as $prescription_Item) {
                if ($mgment_item = MedicationManagementEntry::model()->find("prescription_item_id=".$prescription_Item->id)) {
                    /** @var MedicationManagementEntry $mgment_item */
                    $existing_mgment_items[] = $mgment_item->id;

                    if ($mgment_item->prescribe == 0) {
                        //manaemenet item was updated as not prescribed
                        $pitem = \EventMedicationUse::model()->findAllByAttributes(['prescription_item_id' => $prescription_Item->id]);
                        foreach ($pitem as $p) {
                            // we need to remove all links
                            $p->prescription_item_id = null;
                            $p->save();
                        }
                        $prescription_Item->delete();
                        $changed = true;
                    } else if (!$mgment_item->compareToPrescriptionItem()) {
                        //manaemenet item was updated
                        $prescription_Item->updateFromManagementItem();
                        $changed = true;
                    }
                } else {
                    // management item was deleted
                    $prescription_Item->delete();
                    $changed = true;
                }
            }

            /* items to add */
            foreach ($this->entries_to_prescribe as $entry) {
                if (!in_array($entry->id, $existing_mgment_items)) {
                    $prescription_Item = new \OphDrPrescription_Item();
                    $prescription_Item->event_id =$prescription->event_id;
                    $prescription_Item->bound_key = substr(bin2hex(openssl_random_pseudo_bytes(10)), 0, 10);

                    $prescription_Item->setAttributes(array(
                        'usage_type' => \OphDrPrescription_Item::getUsageType(),
                        'usage_subtype' => \OphDrPrescription_Item::getUsageSubtype(),
                        'medication_id' => $entry->medication_id,
                        'form_id' => $entry->form_id,
                        'laterality' => $entry->laterality,
                        'route_id' => $entry->route_id,
                        'frequency_id' => $entry->frequency_id,
                        'duration_id' => $entry->duration_id,
                        'dose' => $entry->dose,
                                                'dose_unit_term' => $entry->dose_unit_term,
                        'start_date' => $entry->start_date,
                        'dispense_location_id' => $entry->dispense_location_id,
                        'dispense_condition_id' => $entry->dispense_condition_id
                    ));
                    $p_tapers = array();
                    foreach ($entry->tapers as $taper) {
                        $new_taper = new \OphDrPrescription_ItemTaper();
                        $new_taper->item_id = null;
                        $new_taper->frequency_id = $taper->frequency_id;
                        $new_taper->duration_id = $taper->duration_id;
                        $new_taper->dose = $taper->dose;
                        $p_tapers[] = $new_taper;
                    }
                    $prescription_Item->tapers = $p_tapers;
                    if (!$prescription_Item->save()) {
                        throw new \Exception("Error while saving prescription item: ".print_r($prescription_Item->errors, true));
                    }
                    $prescription_Item->saveTapers();
                    $entry->prescription_item_id = $prescription_Item->id;
                    $entry->save();
                    $changed = true;
                }
            }

            if ($changed) {
                if (empty(\OphDrPrescription_Item::model()->findAllByAttributes(['event_id' => $prescription->event_id]))) {
                    // if no more items on the prescription, delete it
                    \Yii::app()->db->createCommand("UPDATE ".$this->tableName()." SET prescription_id=NULL WHERE id=".$this->id)->execute();
                    $prescription->delete();
                    $prescription->event->softDelete("Deleted via examination clinical management");
                } else {
                    // update prescription with message
                    $edit_reason = "Updated via examination clinical management";
                    $prescription->edit_reason_other = $edit_reason;
                    $prescription->draft = 1;
                    $prescription->save();
                }
            }
        } else {
            // prescription does not exist yet
            if (!empty($this->entries_to_prescribe)) {
                $this->generatePrescriptionEvent();
            }
        }
    }

    private function generatePrescriptionEvent()
    {
        $prescription_creator = new PrescriptionCreator($this->event->episode);
        $prescription_creator->patient = $this->event->episode->patient;

        $entries = $this->entries_to_prescribe;
        foreach ($entries as $entry) {
            $item = $this->getPrescriptionItem($entry);
            $item->original_item_id = $entry->id;
            $item->bound_key =  substr(bin2hex(openssl_random_pseudo_bytes(10)), 0, 10);

            $prescription_creator->addItem($item);
        }

        $prescription_creator->elements['Element_OphDrPrescription_Details']->draft = 1;

        $prescription_creator->save();

        if (!$prescription_creator->hasErrors()) {
            \Yii::trace(print_r($prescription_creator->getErrors(), true));
        }

        foreach ($prescription_creator->elements['Element_OphDrPrescription_Details']->items as $item) {
            $class = self::$entry_class;
            $entry = $class::model()->findBypk($item->original_item_id);
            $entry->prescription_item_id = $item->id;
            $entry->save();
        }

        $this->prescription_id = $prescription_creator->elements['Element_OphDrPrescription_Details']->id;

        // To save in afterSave when it's a new record without doing an sql query we have to set the
                // the isNewRecord to false , before saving the attribute and setting it back to true afterwards
                // We're also using saveAttributes to avoid any calls to beforeSave and AfterSave
        if ($this->getIsNewRecord()) {
            $this->setIsNewRecord(false);
            $this->saveAttributes(['prescription_id']);
            $this->setIsNewRecord(true);
        } else {
            $this->saveAttributes(['prescription_id']);
        }
    }

    private function getPrescriptionItem(\EventMedicationUse $entry)
    {
        $item = new \OphDrPrescription_Item();

        $item->dose = $entry->dose;
        $item->dose_unit_term = $entry->dose_unit_term;
        $item->frequency_id = $entry->frequency_id;
        $item->route_id = $entry->route_id;
        $item->medication_id = $entry->medication_id;
        $item->duration_id = $entry->duration_id;
        $item->dispense_condition_id = $entry->dispense_condition_id;
        $item->dispense_location_id = $entry->dispense_location_id;
        $item->laterality = $entry->laterality;
        $item->start_date = $entry->start_date;

        $item->usage_type = \OphDrPrescription_Item::getUsageType();
        $item->usage_subtype = \OphDrPrescription_Item::getUsageSubtype();

        $item_tapers = array();
        if (!empty($entry->tapers)) {
            foreach ($entry->tapers as $taper) {
                $new_taper = new \OphDrPrescription_ItemTaper();
                $new_taper->item_id = null;
                $new_taper->frequency_id = $taper->frequency_id;
                $new_taper->duration_id = $taper->duration_id;
                $new_taper->dose = $taper->dose;
                $item_tapers[] = $new_taper;
            }
        }
        $item->tapers = $item_tapers;

        return $item;
    }

    public function loadFromExisting($element)
    {
        return;
    }

    protected function afterSave()
    {
        parent::afterSave();
        $this->auditAllergicDrugEntries("medication_management");
    }

    /**
     * @return MedicationManagementEntry[]
     *
     * Compatibility function for AllergicDrugEntriesBehavior
     */

    public function getEntries()
    {
        return $this->entries;
    }

    public function afterDelete()
    {
        foreach ($this->entries as $entry) {
            $entry->delete();
        }

        parent::afterDelete();
    }

    public function softDelete()
    {
        foreach ($this->entries as $entry) {
            $entry->prescription_item_id = null;
            $entry->save();
        }

        parent::afterDelete();
    }
}
