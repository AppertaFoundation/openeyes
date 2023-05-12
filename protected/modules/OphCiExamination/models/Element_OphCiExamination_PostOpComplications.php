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
 * This is the model class for table "et_ophtrintravitinjection_complicat".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property string $oth_descrip
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property array(OphTrIntravitrealinjection_Complication) $left_complications
 * @property array(OphTrIntravitrealinjection_Complication) $right_complications
 */

namespace OEModule\OphCiExamination\models;

use Eye;

class Element_OphCiExamination_PostOpComplications extends \SplitEventTypeElement
{
    use traits\CustomOrdering;
    public $service;
    public $firm;
    public $subspecialty_id;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
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
        return 'et_ophciexamination_postop_complications';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('eye_id', 'checkComplicationEyePanels'),
            // array('left_complication_id', 'side' => 'left'),
            //  array('right_complication_id', 'side' => 'right'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, eye_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * Validation rule to assign complication to both eyes.
     *
     * @param type $attribute
     * @param type $params
     */
    public function checkComplicationEyePanels($attribute, $params)
    {
        $elementData = \Yii::app()->request->getParam('OEModule_OphCiExamination_models_Element_OphCiExamination_PostOpComplications', null);
        $eye_id = isset($elementData['eye_id']) ? $elementData['eye_id'] : null;

        $complication_items = \Yii::app()->request->getParam('complication_items', array());

        if (!isset($complication_items['R']) && ($eye_id == \Eye::BOTH || $eye_id == \Eye::RIGHT)) {
            $this->addError($attribute, 'Post Op Complication for Right Eye is missing, select complication or close the Right Eye panel');
        }

        if (!isset($complication_items['L']) && ($eye_id == \Eye::BOTH || $eye_id == \Eye::LEFT)) {
            $this->addError($attribute, 'Post Op Complication for Left Eye is missing, select complication or close the Left Eye panel');
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
            // class name for the relations automatically generated below.
            return array(
                    'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                    'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
                    'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                    'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                    'operation_notes' => array(self::BELONGS_TO, 'Event', 'event_id', 'on' => 'operation_notes.event_type_id = 4'),
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
            'right_values' => 'Post Op Complications',
        );
    }

    public function beforeDelete()
    {
        OphCiExamination_Et_PostOpComplications::model()->deleteAll('element_id = :element_id', array(':element_id' => $this->id));

        return parent::beforeDelete();
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

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

        /**
         * Returns recorded complications based on eye and operation note id.
         *
         * if no recorded complications are found and there are complication in the POST (user wants to save but the site redirect with form error)
         * we get the complications from the POST and display so the user does not have to select it again
         *
         * @param int $eye_id
         * @param int $operation_note_id
         *
         * @return array
         */
    public function getRecordedComplications($eye_id, $operation_note_id = null)
    {
        $recordedComplications = array();

        $model = new OphCiExamination_Et_PostOpComplications();

        $criteria = new \CDbCriteria();

        $criteria->addCondition('t.element_id = :element_id');
        $criteria->addCondition('t.eye_id = :eye_id');
        $criteria->params['element_id'] = $this->id;

        if ($operation_note_id) {
            $criteria->addCondition('t.operation_note_id = :operation_note_id');
            $criteria->params['operation_note_id'] = $operation_note_id;
        }

        $criteria->params['eye_id'] = $eye_id;

        $complications = $model->findAll($criteria);

        if (!$complications) {
            //check if the post contains any post op complication ( isnt saved )
            $postOpComplications = \Yii::app()->request->getParam('complication_items', null);

            $eyeLetter = $eye_id == \Eye::RIGHT ? 'R' : 'L';
            $complication_other = \Yii::app()->request->getParam('complication_other');

            if (isset($postOpComplications[$eyeLetter])) {
                $criteria = new \CDbCriteria();
                $criteria->addInCondition('id', $postOpComplications[$eyeLetter]);
                $complications = OphCiExamination_PostOpComplications::model()->findAll($criteria);

                if ($complications) {
                    foreach ($complications as $complication) {
                        $other = $complication->name == 'other' ? $complication_other[$eyeLetter] : null;
                        $recordedComplications[] = array(
                            'id' => $complication->id,
                            'name' => $complication->name,
                            'other' => $other
                        );
                    }
                }
            }
        } else {
            foreach ($complications as $complication) {
                $recordedComplications[] = array(
                    'id' => $complication->complication->id,
                    'name' => $complication->complication->name,
                    'other' => $complication->other
                );
            }
        }

        return $recordedComplications;
    }

    public function afterSave()
    {
        $complication_items = \Yii::app()->request->getParam('complication_items', false);
        $operation_note_id = \Yii::app()->request->getParam('OphCiExamination_postop_complication_operation_note_id', null);
        $complication_other_sided = \Yii::app()->request->getParam('complication_other');
        $elementData = \Yii::app()->request->getParam('OEModule_OphCiExamination_models_Element_OphCiExamination_PostOpComplications', null);
        $eye_id = isset($elementData['eye_id']) ? $elementData['eye_id'] : null;

        $model = new OphCiExamination_Et_PostOpComplications();

        $model->deleteAllByAttributes(array(
            'element_id' => $this->id,
            'operation_note_id' => $operation_note_id,
        ));

        if ($complication_items) {
            foreach (['left' => \Eye::LEFT, 'right' => \Eye::RIGHT] as $eye_side => $eye_id) {
                // L or R is used as an index to sided items
                $complication_items_index = ucfirst($eye_side[0]);
                if ($this->hasEye($eye_side) ) {
                    if (isset($complication_items[$complication_items_index])) {
                        $complication_other = isset($complication_other_sided[$complication_items_index]) ?
                            $complication_other_sided[$complication_items_index] : null;

                        $this->savePostOpComplicationElements($complication_items[$complication_items_index], $eye_id,
                            $operation_note_id, $complication_other);
                    }
                } else {
                    $this->deleteClosedEyeElements($eye_id);
                }
            }
        }
        parent::afterSave();
    }

    /**
     * @param $complication_items
     * @param $eye_id
     * @param $operation_note_id
     * @throws \Exception
     */
    private function savePostOpComplicationElements($complication_items, $eye_id, $operation_note_id, $complication_other)
    {
        foreach ($complication_items as $cKey => $complication_id) {
            $et_Complication = new OphCiExamination_Et_PostOpComplications();

            $et_Complication->element_id = $this->id;
            $et_Complication->complication_id = $complication_id;
            $et_Complication->eye_id = $eye_id;
            $et_Complication->operation_note_id = $operation_note_id;

            $other_complication_id = OphCiExamination_PostOpComplications::model()->find('name = ?', ['other'])->id;

            $et_Complication->other = $other_complication_id === $complication_id ? $complication_other : null;

            if (!$et_Complication->save()) {
                throw new Exception('Unable to save post op complication: ' . print_r($et_Complication->getErrors(), true));
            }
        }
    }

    /**
     * @param $eye_id
     * @throws \Exception
     */
    private function deleteClosedEyeElements($eye_id)
    {
        OphCiExamination_Et_PostOpComplications::model()->deleteAllByAttributes(
            [
                'element_id' => $this->id,
                'eye_id' => $eye_id
            ]
        );
    }

        /**
         * Returns the Opertion notes belongs to a patient.
         *
         * @return array list of op notes
         */
    public function getOperationNoteList()
    {
        $patient_id = \Yii::app()->request->getParam('patient_id');

        if (!$patient_id && isset($this->event->episode->patient->id) ) {
            $patient_id = $this->event->episode->patient->id;
        }

        $response = array();

        if ($patient_id) {
            $short_format = array();

            $event_type = \EventType::model()->find("class_name = 'OphTrOperationnote'");

            $criteria = new \CDbCriteria();

            $event = new \Event();

            $criteria->addCondition('patient_id = :patient_id');
            $criteria->addCondition('event_type_id = :event_type_id');
            $criteria->params['patient_id'] = $patient_id;
            $criteria->params['event_type_id'] = $event_type->id;
            $criteria->order = 't.event_date DESC';

            $eventLists = $event->with('episode')->findAll($criteria);

            foreach ($eventLists as $event) {
                $procedureListModel = new \Element_OphTrOperationnote_ProcedureList();

                $criteria = new \CDbCriteria();

                $criteria->addCondition('event_id = :event_id');
                $criteria->params['event_id'] = $event->id;

                $procedureList = $procedureListModel->findAll($criteria);

                $date = new \DateTime($event->event_date);
                $name = $date->format('d M Y').' ';

                $short_format = array();

                foreach ($procedureList as $procesdures) {
                    $name .= ($procesdures->eye_id != \Eye::BOTH ? ($procesdures->eye->name) : '').' ';

                    foreach ($procesdures->procedures as $procesdure) {
                        $short_format[] = $procesdure->short_format;
                    }

                    $name .= implode(' + ', $short_format);

                    if (strlen($name) > 60) {
                        $name = substr($name, 0, 57);
                        $name .= '...';
                    }
                }

                $response[$event->id] = $name;
            }
        }

        return $response;
    }

    public function getFullComplicationList($eye_id)
    {
        $list = \Yii::app()->db->createCommand()
            ->selectDistinct('c.name, etc.other')
            ->from('et_ophciexamination_postop_complications t')
            ->join('ophciexamination_postop_et_complications etc', 't.id = etc.element_id')
            ->join('ophciexamination_postop_complications c', 'etc.complication_id = c.id')
            ->where('(etc.eye_id=:eye_id) AND t.event_id = :event_id', array(':eye_id' => $eye_id, ':event_id' => $this->event->id))
            ->order('etc.created_date DESC')
            ->queryAll();

        return $list;
    }

    public function beforeValidate()
    {
        $complication_items = \Yii::app()->request->getParam('complication_items', false);
        $complication_other_sided = \Yii::app()->request->getParam('complication_other');

        if ($complication_items) {
            foreach (['left' => \Eye::LEFT, 'right' => \Eye::RIGHT] as $eye_side => $eye_id) {
                // L or R is used as an index to sided items
                $complication_items_index = ucfirst($eye_side[0]);
                if ($this->hasEye($eye_side)) {
                    if (isset($complication_items[$complication_items_index])) {
                        $complication_other = isset($complication_other_sided[$complication_items_index]) ?
                            $complication_other_sided[$complication_items_index] : null;

                        $this->validateComplicationItems($complication_items[$complication_items_index], $eye_id,
                            $complication_other);
                    }
                }
            }
        }
        return parent::beforeValidate();
    }

    private function validateComplicationItems($complication_items, $eye_id, $complication_other)
    {
        $other_complication_id = OphCiExamination_PostOpComplications::model()->find('name = ?', ['other'])->id;

        $eye_abbr = $eye_id === Eye::RIGHT ? 'R' : 'L';
        foreach ($complication_items as $cKey => $complication_id) {
            if ($other_complication_id == $complication_id && ($complication_other == "" || is_null($complication_other))) {
                $this->addError('complication_other[' . $eye_abbr . ']', 'Other cannot be empty');
            }
        }
    }
}
