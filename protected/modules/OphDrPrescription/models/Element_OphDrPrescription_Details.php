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
 * @property Item[] $items
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
            'items' => array(self::HAS_MANY, 'OphDrPrescription_Item', 'prescription_id'),
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
     * @return Drug[]
     */
    public function commonDrugs()
    {
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
        $site_id = Yii::app()->session['selected_site_id'];
        $params = array(':subSpecialtyId' => $subspecialty_id, ':siteId' => $site_id);

        return Drug::model()->active()->findAll(array(
                    'condition' => 'ssd.subspecialty_id = :subSpecialtyId AND ssd.site_id = :siteId',
                    'join' => 'JOIN site_subspecialty_drug ssd ON ssd.drug_id = t.id',
                    'order' => 'name',
                    'params' => $params,
        ));
    }

    /**
     * Get the drug list for a specified site and subspecialty.
     *
     * @param $site_id
     * @param $subspecialty_id
     *
     * @return SiteSubspecialtyDrug[]
     */
    public function commonDrugsBySiteAndSpec($site_id, $subspecialty_id)
    {
        $params = array(':subSpecialtyId' => $subspecialty_id, ':siteId' => $site_id);

        return SiteSubspecialtyDrug::model()->with('drugs')->findAll(array(
                    'condition' => 't.subspecialty_id = :subSpecialtyId AND t.site_id = :siteId',
                    'order' => 'name',
                    'params' => $params,
        ));
    }

    /**
     * Get the drug sets for the current firm.
     *
     * @TODO: move this out of the model - it's not the right place for it as it's relying on session information
     *
     * @return DrugSet[]
     */
    public function drugSets()
    {
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
        $params = array(':subspecialty_id' => $subspecialty_id);

        return DrugSet::model()->findAll(array(
                    'condition' => 'subspecialty_id = :subspecialty_id AND active = 1',
                    'order' => 'name',
                    'params' => $params,
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
     * @TODO: Should this be a static method on the DrugType model, rather than here?
     *
     * @return CHtml::listData
     */
    public function drugTypes()
    {
        $drugTypes = CHtml::listData(DrugType::model()->active()->findAll(array(
                            'order' => 'name',
                        )), 'id', 'name');

        natcasesort($drugTypes);

        return $drugTypes;
    }

    /**
     * Prescription is always editable.
     *
     * @return bool
     */
    public function isEditable()
    {
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
            foreach (OphDrPrescription_Item::model()->findAll('prescription_id = :pid', array(':pid' => $this->id)) as $item) {
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
                    $item_model->prescription_id = $this->id;
                    $item_model->drug_id = $item['drug_id'];
                }

                // Save main item attributes
                $item_model->dose = $item['dose'];
                $item_model->route_id = $item['route_id'];

                if (isset($item['route_option_id'])) {
                    $item_model->route_option_id = $item['route_option_id'];
                } else {
                    $item_model->route_option_id = null;
                }
                $item_model->frequency_id = $item['frequency_id'];
                $item_model->duration_id = $item['duration_id'];
                $item_model->dispense_condition_id = $item['dispense_condition_id'];
                $item_model->dispense_location_id = $item['dispense_location_id'];

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

            // Delete remaining (removed) ids
            OphDrPrescription_ItemTaper::model()->deleteByPk(array_values($existing_taper_ids));
            OphDrPrescription_Item::model()->deleteByPk(array_values($existing_item_ids));
        }

        if (!$this->draft) {
            $this->getApp()->event->dispatch('after_medications_save', array(
                'patient' => $this->event->getPatient(),
                'drugs' => array_map(function($item) {return $item->drug; }, $this->items)
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
}
