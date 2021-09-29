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

namespace OEModule\OphCiExamination\models;

use Yii;

/**
 * This is the model class for table "et_ophciexamination_dilation".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 *
 *
 * The followings are the available model relations:
 */
class Element_OphCiExamination_Dilation extends \SplitEventTypeElement
{
    use traits\CustomOrdering;

    protected $errorExceptions = array(
      'OEModule_OphCiExamination_models_Element_OphCiExamination_Dilation_left_treatments' => 'dilation_left',
      'OEModule_OphCiExamination_models_Element_OphCiExamination_Dilation_right_treatments' => 'dilation_right',
    );

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphCiExamination_Dilation
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
        return 'et_ophciexamination_dilation';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('eye_id, left_comments, right_comments', 'safe'),
                array('id, event_id, eye_id', 'safe', 'on' => 'search'),
                array('left_treatments', 'requiredIfSide', 'side' => 'left'),
                array('right_treatments', 'requiredIfSide', 'side' => 'right'),
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
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'treatments' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_Dilation_Treatment', 'element_id'),
            'right_treatments' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_Dilation_Treatment', 'element_id', 'on' => 'right_treatments.side = '.OphCiExamination_Dilation_Treatment::RIGHT),
            'left_treatments' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_Dilation_Treatment', 'element_id', 'on' => 'left_treatments.side = '.OphCiExamination_Dilation_Treatment::LEFT),
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
            'eye_id' => 'Eye',
            'left_treatments' => 'Treatments',
            'right_treatments' => 'Treatments',
            'left_comments' => 'Comments',
            'right_comments' => 'Comments'
        );
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

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * Get the a list of dilation drugs that have not been used on the given side.
     *
     * @param $side
     *
     * @return array
     */
    public function getUnselectedDilationDrugs($side)
    {
        $treatments = $this->{$side.'_treatments'};
        $criteria = new \CDbCriteria();
        $drug_ids = \CHtml::listData($treatments, 'id', 'drug_id');
        $criteria->addNotInCondition('id', $drug_ids);
        $criteria->order = 'display_order asc';

        return \CHtml::listData(OphCiExamination_Dilation_Drugs::model()->findAll($criteria), 'id', 'name');
    }

    /**
     * Get the list with all dilation drugs.
     *
     * @param $side
     *
     * @return array
     */
    public function getAllDilationDrugs($side)
    {
        $criteria = new \CDbCriteria();
        $criteria->order = 'display_order asc';
        return \CHtml::listData(OphCiExamination_Dilation_Drugs::model()->findAll($criteria), 'id', 'name');
    }


    /**
     * Validate each of the dilation treatments.
     */
    protected function afterValidate()
    {
        foreach (array('left' => 'hasLeft', 'right' => 'hasRight') as $side => $checkFunc) {
            if ($this->$checkFunc()) {
                foreach ($this->{$side.'_treatments'} as $i => $treat) {
                    if (!$treat->validate()) {
                        foreach ($treat->getErrors() as $fld => $err) {
                            $this->addError($side.'_treatments', ucfirst($side).' treatment ('.($i + 1).'): '.implode(', ', $err));
                        }
                    }
                }
            }
        }
    }

    /**
     * extends standard delete method to remove all the treatments.
     *
     * (non-PHPdoc)
     *
     * @see CActiveRecord::delete()
     */
    public function delete()
    {
        $transaction = Yii::app()->db->getCurrentTransaction() === null
                ? Yii::app()->db->beginTransaction()
                : false;

        try {
            foreach ($this->treatments as $treatment) {
                if (!$treatment->delete()) {
                    throw new Exception('Delete treatment failed: '.print_r($treatment->getErrors(), true));
                }
            }
            if (parent::delete()) {
                if ($transaction) {
                    $transaction->commit();
                }
            } else {
                throw new Exception('unable to delete');
            }
        } catch (Exception $e) {
            if ($transaction) {
                $transaction->rollback();
            }
            throw $e;
        }
    }

    /**
     * Concatenate all fields of the given treatment to create a unique id
     *
     * @param $treatment
     * @return string
     */
    public function createUIDTreatments($treatment)
    {
        $index = "";
        foreach ($treatment as $key=>$treat) {
            $index .= $treat;
        }
        return $index;
    }

    /**
     * Update the dilation treatments - depends on their only being one treatment of a particular drug on a given side.
     *
     * @param $side
     * @param $treatments
     *
     * @throws Exception
     */
    public function updateTreatments($side, $treatments)
    {
        if ($side == \Eye::LEFT) {
            $side = OphCiExamination_Dilation_Treatment::LEFT;
        } else {
            $side = OphCiExamination_Dilation_Treatment::RIGHT;
        }

        $curr_by_id = array();
        $save = array();

        foreach ($this->treatments as $t) {
            if ($t->side == $side) {
                $curr_by_id[$this->createUIDTreatments($t)] = $t;
            }
        }

        foreach ($treatments as $treat) {
            if (!array_key_exists($this->createUIDTreatments($treat), $curr_by_id)) {
                $t_obj = new OphCiExamination_Dilation_Treatment();
            } else {
                $t_obj = $curr_by_id[$this->createUIDTreatments($treat)];
                unset($curr_by_id[$this->createUIDTreatments($treat)]);
            }

            $t_obj->attributes = $treat;
            $t_obj->element_id = $this->id;
            $t_obj->side = $side;
            $treatment_time = (date('H:i', strtotime($treat['treatment_time'])));
            $t_obj->treatment_time = $treatment_time;
            $save[] = $t_obj;
        }
        foreach ($save as $s) {
            if (!$s->save()) {
                throw new Exception('unable to save treatment:'.print_r($s->getErrors(), true));
            };
        }

        foreach ($curr_by_id as $curr) {
            if (!$curr->delete()) {
                throw new Exception('unable to delete treatment:'.print_r($curr->getErrors(), true));
            }
        }
    }

    public function canViewPrevious()
    {
        return true;
    }

    public function getPrint_view()
    {
        return 'print_'.$this->getDefaultView();
    }
}
