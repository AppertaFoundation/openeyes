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

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "et_ophtroperationbooking_diagnosis".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 * @property int $disorder_id
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property Eye $eye
 */
class Element_OphTrOperationbooking_Diagnosis extends BaseEventTypeElement
{
    use HasFactory;
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
        return 'et_ophtroperationbooking_diagnosis';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, eye_id, disorder_id, ', 'safe'),
            array('eye_id, disorder_id, ', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, eye_id, disorder_id, ', 'safe', 'on' => 'search'),
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
            'disorder' => array(self::BELONGS_TO, 'Disorder', 'disorder_id'),
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
            'eye_id' => 'Eyes',
            'disorder_id' => 'Diagnosis',
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
        $criteria->compare('eye_id', $this->eye_id);
        $criteria->compare('disorder_id', $this->disorder_id);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * Reset diagnosis data on the event based on the diagnosis set on this element.
     */
    protected function afterSave()
    {
        $patient_has_disorder = true;
        if (!SecondaryDiagnosis::model()->count('patient_id=? and disorder_id=? and eye_id in (' . $this->eye_id . ',3)', array($this->event->episode->patient_id, $this->disorder_id))) {
            if (!Episode::model()->count('patient_id=? and disorder_id=? and eye_id in (' . $this->eye_id . ',3)', array($this->event->episode->patient_id, $this->disorder_id))) {
                $patient_has_disorder = false;
            }
        }

        if (!$this->event->episode->eye && !$this->event->episode->disorder_id) {
            if (!$patient_has_disorder) {
                $this->event->episode->setPrincipalDiagnosis($this->disorder_id, $this->eye_id, (isset($this->date) ? $this->date : false));
                $sd = SecondaryDiagnosis::model()->find('patient_id=? and disorder_id=? and eye_id = ?', array($this->event->episode->patient_id, $this->disorder_id, 3));
                if ($sd) {
                    $this->event->episode->patient->removeDiagnosis($sd->id);

                    if (in_array($this->eye_id, array(1, 2))) {
                        $this->event->episode->patient->addDiagnosis($this->disorder_id, $this->eye_id == 1 ? 2 : 1);
                    }
                }
            }
        } elseif (!$patient_has_disorder) {
            $this->event->episode->patient->addDiagnosis($this->disorder_id, $this->eye_id);
        }

        return parent::afterSave();
    }

    public function getContainer_view_view()
    {
        return false;
    }
}
