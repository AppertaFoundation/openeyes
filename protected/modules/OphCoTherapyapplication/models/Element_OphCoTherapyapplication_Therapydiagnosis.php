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
 * This is the model class for table "et_ophcotherapya_therapydiag".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $left_diagnosis1_id
 * @property int $right_diagnosis1_id
 * @property int $left_diagnosis2_id
 * @property int $right_diagnosis2_id
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property Disorder $left_diagnosis1
 * @property Disorder $right_diagnosis1
 * @property Disorder $left_diagnosis2
 * @property Disorder $right_diagnosis2
 */
class Element_OphCoTherapyapplication_Therapydiagnosis extends SplitEventTypeElement
{
    public $service;

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
        return 'et_ophcotherapya_therapydiag';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, left_diagnosis1_id, left_diagnosis2_id, right_diagnosis1_id, right_diagnosis2_id, eye_id', 'safe'),
            array('left_diagnosis1_id', 'requiredIfSide', 'side' => 'left'),
            array('left_diagnosis2_id', 'requiredIfSecondary', 'side' => 'left', 'dependent' => 'left_diagnosis1_id'),
            array('right_diagnosis1_id', 'requiredIfSide', 'side' => 'right'),
            array('right_diagnosis2_id', 'requiredIfSecondary', 'side' => 'right', 'dependent' => 'right_diagnosis1_id'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, left_diagnosis1_id, right_diagnosis1_id, left_diagnosis2_id, right_diagnosis2_id, eye_id', 'safe', 'on' => 'search'),
        );
    }

    public function sidedFields()
    {
        return array('diagnosis1_id', 'diagnosis2_id');
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
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'left_diagnosis1' => array(self::BELONGS_TO, 'Disorder', 'left_diagnosis1_id'),
            'right_diagnosis1' => array(self::BELONGS_TO, 'Disorder', 'right_diagnosis1_id'),
            'left_diagnosis2' => array(self::BELONGS_TO, 'Disorder', 'left_diagnosis2_id'),
            'right_diagnosis2' => array(self::BELONGS_TO, 'Disorder', 'right_diagnosis2_id'),
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
            'left_diagnosis1_id' => 'Diagnosis',
            'right_diagnosis1_id' => 'Diagnosis',
            'left_diagnosis2_id' => 'Secondary To',
            'right_diagnosis2_id' => 'Secondary To',
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

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('left_diagnosis1_id', $this->left_diagnosis1_id);
        $criteria->compare('right_diagnosis1_id', $this->right_diagnosis1_id);
        $criteria->compare('left_diagnosis2_id', $this->left_diagnosis2_id);
        $criteria->compare('right_diagnosis2_id', $this->right_diagnosis2_id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Get a list of level 1 disorders for this element (appends any level 1 disorder that has been selected for this
     * element but aren't part of the default list).
     *
     * @return Disorder[]
     */
    public function getLevel1Disorders()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'parent_id IS NULL';
        $criteria->order = 'display_order asc';

        $therapy_disorders = OphCoTherapyapplication_TherapyDisorder::model()->with('disorder')->findAll($criteria);

        $disorders = array();
        $disorder_ids = array();
        foreach ($therapy_disorders as $td) {
            $disorders[] = $td->disorder;
            $disorder_ids[] = $td->disorder->id;
        }
        // if this element has been created with a disorder outside of the standard list, needs to be available in the
        // list for selection to be maintained
        foreach (array('left', 'right') as $side) {
            if ($this->{$side.'_diagnosis1_id'} && !in_array($this->{$side.'_diagnosis1_id'}, $disorder_ids)) {
                $disorders[] = $this->{$side.'_diagnosis1'};
            }
        }

        return $disorders;
    }

    /**
     * retrieve a list of disorders that are defined as level 2 disorders for the given disorder.
     *
     * @param unknown $therapyDisorder
     *
     * @return Disorder[]
     */
    public function getLevel2Disorders($disorder)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'parent_id IS NULL AND disorder_id = :did';
        $criteria->params = array(':did' => $disorder->id);
        $disorders = array();

        if ($td = OphCoTherapyapplication_TherapyDisorder::model()->find($criteria)) {
            $disorders = $td->getLevel2Disorders();
            $dids = array();
            foreach ($disorders as $d) {
                $dids[] = $d->id;
            }
            foreach (array('left', 'right') as $side) {
                if ($this->{$side.'_diagnosis1_id'} == $disorder->id
                    && $this->{$side.'_diagnosis2'}
                    && !in_array($this->{$side.'_diagnosis2_id'}, $dids)) {
                    $disorders[] = $this->{$side.'_diagnosis2'};
                }
            }
        }

        return $disorders;
    }

    protected function beforeSave()
    {
        return parent::beforeSave();
    }

    protected function afterSave()
    {
        return parent::afterSave();
    }

    protected function beforeValidate()
    {
        return parent::beforeValidate();
    }

    /**
     * return a string representation of the diagnoses set for the given side.
     *
     * @param string $side 'left' or 'right'
     *
     * @return string
     */
    public function getDiagnosisStringForSide($side)
    {
        $res = '';
        if ($this->{$side.'_diagnosis1'}) {
            $res .= $this->{$side.'_diagnosis1'}->term;
        }
        if ($this->{$side.'_diagnosis2'}) {
            $res .= ' secondary to '.$this->{$side.'_diagnosis2'}->term;
        }

        return $res;
    }

    /*
     * check a level 2 diagnosis is provided for level 1 diagnoses that require it (need to check the side as well though)
    */
    public function requiredIfSecondary($attribute, $params)
    {
        if (($params['side'] == 'left' && $this->eye_id != Eye::RIGHT) || ($params['side'] == 'right' && $this->eye_id != Eye::LEFT)) {
            if ($this->$params['dependent'] && !$this->$attribute) {
                $criteria = new CDbCriteria();
                // FIXME: mysql dependent NULL check
                $criteria->condition = 'disorder_id = :did AND parent_id IS NULL';
                $criteria->params = array(':did' => $this->$params['dependent']);
                if ($td = OphCoTherapyapplication_TherapyDisorder::model()->with('disorder')->find($criteria)) {
                    if ($td->getLevel2Disorders()) {
                        $this->addError($attribute, $td->disorder->term.' must be secondary to another diagnosis');
                    }
                }
            }
        }
    }

    public function getContainer_form_view()
    {
        return '//patient/element_container_form_no_bin';
    }
}
