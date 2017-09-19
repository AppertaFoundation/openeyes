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
 * This is the model class for table "et_ophtrintravitinjection_postinject".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 * @property bool $left_finger_count
 * @property bool $right_finger_count
 * @property bool $left_iop_check
 * @property bool $right_iop_check
 * @property int $left_drops_id
 * @property int $right_drops_id
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property OphTrIntravitrealinjection_PostInjectionDrops $left_drops
 * @property OphTrIntravitrealinjection_PostInjectionDrops $right_drops
 */
class Element_OphTrIntravitrealinjection_PostInjectionExamination extends SplitEventTypeElement
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
        return 'et_ophtrintravitinjection_postinject';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, eye_id, left_finger_count, left_iop_check, left_iop_instrument_id, left_iop_reading_id, left_drops_id,'.
                'right_finger_count, right_iop_check, right_iop_instrument_id, right_iop_reading_id, right_drops_id', 'safe', ),
            array('eye_id', 'required'),
            array('left_finger_count, left_iop_check, left_drops_id', 'requiredIfSide', 'side' => 'left'),
            array('right_finger_count, right_iop_check, right_drops_id', 'requiredIfSide', 'side' => 'right'),
            // The following rule is used by search().
            array('id, event_id, eye_id, left_finger_count, right_finger_count, left_iop_check, right_iop_check', 'safe', 'on' => 'search'),
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
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'left_drops' => array(self::BELONGS_TO, 'OphTrIntravitrealinjection_PostInjectionDrops', 'left_drops_id'),
            'right_drops' => array(self::BELONGS_TO, 'OphTrIntravitrealinjection_PostInjectionDrops', 'right_drops_id'),
        );
    }

    public function sidedFields()
    {
        return array('finger_count', 'iop_check', 'drops_id');
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'left_finger_count' => 'Counting Fingers Checked?',
            'right_finger_count' => 'Counting Fingers Checked?',
            'left_iop_check' => 'IOP Needs to be Checked?',
            'right_iop_check' => 'IOP Needs to be Checked?',
            'left_drops_id' => 'Post Injection Drops',
            'right_drops_id' => 'Post Injection Drops',
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
        $criteria->compare('left_finger_count', $this->left_finger_count);
        $criteria->compare('right_finger_count', $this->right_finger_count);
        $criteria->compare('left_iop_check', $this->left_iop_check);
        $criteria->compare('right_iop_check', $this->right_iop_check);
        $criteria->compare('left_drops_id', $this->left_drops_id);
        $criteria->compare('right_drops_id', $this->right_drops_id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
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
}
