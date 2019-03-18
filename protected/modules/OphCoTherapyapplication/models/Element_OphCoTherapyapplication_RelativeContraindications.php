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
 * This is the model class for table "et_ophcotherapya_relativecon".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $cerebrovascular_accident
 * @property int $ischaemic_attack
 * @property int $myocardial_infarction
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 */
class Element_OphCoTherapyapplication_RelativeContraindications extends BaseEventTypeElement
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
        return 'et_ophcotherapya_relativecon';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, cerebrovascular_accident, ischaemic_attack, myocardial_infarction, ', 'safe'),
            array('cerebrovascular_accident, ischaemic_attack, myocardial_infarction, ', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, cerebrovascular_accident, ischaemic_attack, myocardial_infarction, ', 'safe', 'on' => 'search'),
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
            'cerebrovascular_accident' => 'Cerebrovascular accident',
            'ischaemic_attack' => 'Ischaemic Attack',
            'myocardial_infarction' => 'Myocardial Infarction',
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
        $criteria->compare('cerebrovascular_accident', $this->cerebrovascular_accident);
        $criteria->compare('ischaemic_attack', $this->ischaemic_attack);
        $criteria->compare('myocardial_infarction', $this->myocardial_infarction);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function eventPatientSuitability()
    {
        if ($this->event_id) {
            $criteria = new CDbCriteria();
            $criteria->compare('event_id', $this->event_id);

            return Element_OphCoTherapyapplication_PatientSuitability::model()->find($criteria);
        }

        return;
    }

    /**
     * the list of attributes on this elements that are fields for contraindication.
     *
     * @return multitype:string
     */
    public function contraindicationFields()
    {
        return array(
                'cerebrovascular_accident',
                'ischaemic_attack',
                'myocardial_infarction',
        );
    }

    public function hasAny()
    {
        foreach ($this->contraindicationFields() as $fld) {
            if ($this->$fld) {
                return true;
            }
        }

        return false;
    }

    public function isHiddenInUI()
    {
        return !$this->eventPatientSuitability() || !$this->eventPatientSuitability()->contraindicationsRequired();
    }

    public function getContainer_form_view()
    {
        return '//patient/element_container_form_no_bin';
    }
}
