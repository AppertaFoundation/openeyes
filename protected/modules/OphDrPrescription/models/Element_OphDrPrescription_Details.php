<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * The followings are the available columns in table 'et_ophdrprescription_details':.
 *
 * @property string $id
 * @property int $event_id
 * @property string $comments
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property OphDrPrescription_Item[] $items
 */
class Element_OphDrPrescription_Details extends BaseEventTypeElement
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphDrPrescription_Details the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophdrprescription_details';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, comments, draft, print, edit_reason_id, edit_reason_other', 'safe'),
            array('items', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, comments', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'items' => array(
                self::HAS_MANY,
                OphDrPrescription_Item::class,
                array('event_id' => 'event_id')
            ),
            'edit_reason' => array(self::BELONGS_TO, 'OphDrPrescriptionEditReasons', 'edit_reason_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array();
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('comments', $this->comments, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Generates string for inclusion in letters that describes the prescription.
     *
     * @return string
     */
    public function getLetterText()
    {
        $return = '';
        foreach ($this->items as $item) {
            if ($return) {
                $return .= "\n";
            }
            $return .= $item->getDescription();

            if ($item->tapers) {
                foreach ($item->tapers as $taper) {
                    $return .= "\n   ".$taper->getDescription();
                }
            }
        }

        return $return;
    }

    /**
     * Get the common drugs for session firm.
     *
     * @TODO: move this out of the model - it's not the right place for it as it's relying on session information
     *
     * @return Medication[]
     */
    public function commonDrugs()
    {
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
        $site_id = Yii::app()->session['selected_site_id'];

        return Medication::model()->getSiteSubspecialtyMedications($site_id, $subspecialty_id);
    }

    /**
     * Get the drug list for a specified site and subspecialty.
     *
     * @param $site_id
     * @param $subspecialty_id
     *
     * @return Medication[]
     */
    public function commonDrugsBySiteAndSpec($site_id, $subspecialty_id)
    {
        return Medication::model()->getSiteSubspecialtyMedications($site_id, $subspecialty_id);
    }

    /**
     * Get the drug sets for the current firm.
     *
     * @TODO: move this out of the model - it's not the right place for it as it's relying on session information
     *
     * @return MedicationSet[]
     */
    public function drugSets()
    {
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
        $params = array(':subspecialty_id' => $subspecialty_id);

        return MedicationSet::model()->with("medicationSetRules")->findAll(array(
            "condition" => "medicationSetRules.subspecialty_id = :subspecialty_id AND usage_code = 'Drug' AND medicationSetRules.deleted_date IS NULL",
            "order" => "name",
            "params" => $params
        ));
    }

    /**
     * Get all drug sets for admin page.
     *
     * @return mixed
     */
    public function drugSetsAll()
    {
        $drugSets = DrugSet::model()->findAll(array(
            'with' => 'subspecialty',
            'order' => 't.name',
        ));

        /* foreach ($drugSets as $drugSet) {
          $drugSet->name = $drugSet->name . " - " . $drugSet->subspecialty->name;
          } */

        return $drugSets;
    }

    /**
     * Gets listdata for the drugtypes.
     *
     * @return array
     */
    public function drugTypes()
    {
        return Chtml::listData(MedicationSet::model()->with("medicationSetRules")->findAll(array(
            "condition" => "usage_code = 'DrugTag' AND medicationSetRules.deleted_date IS NULL",
            "order" => "name",
        )), 'id', 'name');
    }

    /*
     * When a prescription event is created as the result of a medication 
     * management element from an examination event,the prescription event 
     * should be locked for editing.
     * The only available action will be to save as final (or print final) or delete
     * 
     * @return bool
     */
    
    public function isEditableByMedication()
    {
        foreach ($this->items as $key => $item) {
            if($item->parent){
                return false;
            }  
        }
        return true;
    }
    

    /**
     * Validate prescription items.
     */
    protected function afterValidate()
    {
        // Check that fields on prescription items are set
        foreach ($this->items as $i => $item) {
            if (!$item->validate()) {
                foreach ($item->getErrors() as $fld => $err) {
                    $this->addError('items', 'Item ('.($i + 1).'): '.implode(', ', $err));
                }
            }
        }

        return parent::afterValidate();
    }

    protected function afterSave()
    {
        if(($this->draft == 0) && ($this->printed == 0)){
            $this->event->deleteIssue('Draft');
        } else if($this->draft == 1) {
            $this->event->addIssue('Draft');
        } else {
            $this->event->deleteIssue('Draft');
        }

        $this->auditAllergicDrugEntries();
        return parent::afterSave();
    }

    /**
     * Create and save appropriate Element_OphDrPrescription_Item and Element_OphDrPrescription_Item_Taper
     * models for this prescription based on the $items structure passed in.
     *
     * This instance must have been saved already.
     *
     * @param array() $items
     *
     * @throws Exception
     */
    public function updateItems($items)
    {
        $itemCount = count($items);
        if ($itemCount == 0) {
            throw new Exception('Item cannot be blank.');
        } else {
            if (!$this->id) {
                throw new Exception('Cannot call updateItems on unsaved instance.');
            }

            // Get a list of ids so we can keep track of what's been removed
            $existing_item_ids = array();
            $existing_taper_ids = array();
            // can't rely on relation, as this will have been set already
            foreach (OphDrPrescription_Item::model()->findAll("event_id = :eid AND usage_type = 'OphDrPrescription'", array(':eid' => $this->event_id)) as $item) {
                $existing_item_ids[$item->id] = $item->id;
                foreach ($item->tapers as $taper) {
                    $existing_taper_ids[$taper->id] = $taper->id;
                }
            }

            // Process (any) posted prescription items
            foreach ($items as $item) {
                if (isset($item['id']) && isset($existing_item_ids[$item['id']])) {
                    // Item is being updated
                    $item_model = OphDrPrescription_Item::model()->findByPk($item['id']);
                    unset($existing_item_ids[$item['id']]);
                } else {
                    // Item is new
                    $item_model = new OphDrPrescription_Item();
                    $item_model->event_id = $this->event_id;
                    $item_model->medication_id = $item['medication_id'];
                }

                // Save main item attributes

                $item_model->setAttributes($item);
                $item_model->start_date = substr($this->event->event_date, 0, 10);
                $item_model->save();

                // Tapering
                $new_tapers = (isset($item['taper'])) ? $item['taper'] : array();
                foreach ($new_tapers as $taper) {
                    if (isset($taper['id']) && isset($existing_taper_ids[$taper['id']])) {
                        // Taper is being updated
                        $taper_model = OphDrPrescription_ItemTaper::model()->findByPk($taper['id']);
                        unset($existing_taper_ids[$taper['id']]);
                    } else {
                        // Taper is new
                        $taper_model = new OphDrPrescription_ItemTaper();
                        $taper_model->item_id = $item_model->id;
                    }
                    $taper_model->dose = $taper['dose'];
                    $taper_model->frequency_id = $taper['frequency_id'];
                    $taper_model->duration_id = $taper['duration_id'];
                    $taper_model->save();
                }
            }

            // Delete existing relations to medication management items

            foreach ($existing_item_ids as $item_id) {
                $related = EventMedicationUse::model()->findAllByAttributes(['prescription_item_id' => $item_id]);
                foreach ($related as $record) {
                    $record->setAttribute('prescription_item_id', null);
                    $record->save();
                }
            }

            // Delete remaining (removed) ids
            OphDrPrescription_ItemTaper::model()->deleteByPk(array_values($existing_taper_ids));
            OphDrPrescription_Item::model()->deleteByPk(array_values($existing_item_ids));
        }

        if (!$this->draft) {
            $this->getApp()->event->dispatch('after_medications_save', array(
                'patient' => $this->event->getPatient(),
                'medications' => array_map(function($item) {return $item->medication; }, $this->items)
            ));
        }
    }

    /**
     * Returns string status to indicate if the prescription is draft or not.
     *
     * @return string
     */
    public function getInfotext()
    {
        if(($this->draft == 0) && ($this->printed == 0)){
            return 'Saved';
        } else if (!$this->printed) {
            return 'Draft';
        } else {
            return 'Printed';
        }
    }

    public function getContainer_form_view()
    {
        return false;
    }

    public function getContainer_print_view()
    {
        return false;
    }

    public function getPrint_view()
    {
        return 'print_'.$this->getDefaultView();
    }

    protected function auditAllergicDrugEntries()
    {
        $patient = $this->event->getPatient();
        $patient_allergies = $patient->allergies;
        $patient_allergies_from_drugs = [];
        $allergic_drugs = [];

        $drug_allergies_assignments = $this->getDrugAllergiesAssignments();

        foreach ($patient_allergies as $allergy) {
            foreach ($drug_allergies_assignments as $drug_allergy_assignment) {
                if ($allergy->id === $drug_allergy_assignment->allergy_id) {
                    $patient_allergies_from_drugs[$allergy->id] = $allergy->name;
                    $allergic_drugs[$drug_allergy_assignment->medication_id] = $drug_allergy_assignment->medication->preferred_term;
                }
            }
        }

        if (isset($allergic_drugs) && sizeof($allergic_drugs) !== 0 &&
            isset($patient_allergies_from_drugs) && sizeof($allergic_drugs) != 0) {
            Audit::add(
                'prescription', 'allergy-override', 'Allergies: ' .
                implode(' , ', $patient_allergies_from_drugs) . ' Drugs: ' . implode(' , ', $allergic_drugs),
                null,
                array('patient_id' => $patient->id)
            );
        }
    }

    protected function getDrugAllergiesAssignments()
    {
        $drug_allergies_assignments = [];
        foreach ($this->items as $prescription_item) {
            $drug_allergy_assignment = MedicationAllergyAssignment::model()->find('medication_id = :medication_id', [':medication_id' => $prescription_item->medication->id]);
            if (isset($drug_allergy_assignment)) {
                $drug_allergies_assignments[] = $drug_allergy_assignment;
            }
        }

        return $drug_allergies_assignments;
    }


}
