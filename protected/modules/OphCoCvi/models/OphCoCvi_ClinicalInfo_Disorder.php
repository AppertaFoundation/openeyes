<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoCvi\models;

/**
 * This is the model class for table "ophcocvi_clinicinfo_disorder".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property string $name
 * @property integer $section_id
 * @property string $code
 * @property boolean $active
 * @property integer $disorder_id
 *
 * The followings are the available model relations:
 *
 * @property \ElementType $element_type
 * @property \EventType $eventType
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property OphCoCvi_ClinicalInfo_Disorder_Section $section
 * @property \Disorder $disorder
 */

class OphCoCvi_ClinicalInfo_Disorder extends \BaseActiveRecordVersioned
{
    const PATIENT_TYPE_ADULT = 0;
    const PATIENT_TYPE_CHILD = 1;

    /**
     * Returns the static model of the specified AR class.
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
        return 'ophcocvi_clinicinfo_disorder';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name,section_id,patient_type, comments_allowed, active, disorder_id', 'safe'),
            array('name,section_id', 'required'),
            array('name', 'length', 'max' => 128),
            array('code', 'length', 'max' => 20),
            array('id, name,section_id,disorder_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element_type' => array(
                self::HAS_ONE,
                'ElementType',
                'id',
                'on' => "element_type.class_name='" . get_class($this) . "'"
            ),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'section' => array(
                self::BELONGS_TO,
                'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section',
                'section_id'
            ),
            'disorder'  => array(self::BELONGS_TO, 'Disorder', 'disorder_id'),
        );
    }

    /**
     * Add Lookup behaviour
     *
     * @return array
     */
    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'ICD 10 Code',
            'section_id' => 'Section',
            'disorder_id' => 'Disorder',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /*
    public function getAllDisorderForSection($disorder_section)
    {
        return($this->findAll('`active` = ? and section_id = ?',array(1, $disorder_section->id)));
    }


    /**
     * Get all the patient disorders based on eye as array
     *
     * @param $side
     * @return array|mixed|null
     */
    /*
    public function getAllPatientDisorderIds($side)
    {
        $patient_disorder_list = array();
        $side_value = strtolower($side) ===  'right' ? \Eye::RIGHT : \Eye::LEFT;
        if ($patient_id = \Yii::app()->getRequest()->getQuery('patient_id'))
        {
            $patient_disorders = \Patient::model()->findByPk($patient_id)->getAllDisorders($side_value);
            foreach ($patient_disorders as $disorder) {
                $patient_disorder_list[] = $disorder->id;
            }
        }
        return $patient_disorder_list;
    }


    public function getDisordersWithValuesAndComments($element,$side,$disorder_section)
    {
        $disorders = array();
        $index_key = 0;
        $disorder_list = $this->getAllDisorderForSection($disorder_section);
        $disorder_ids_for_eye = $this->getAllPatientDisorderIds($side);
        foreach ($disorder_list as $disorder) {
            if (\Yii::app()->controller->action->id === 'create' )
            {
                $disorders[$index_key]['status'] = 0;
                if (in_array($disorder->disorder_id, $disorder_ids_for_eye)) {
                    $disorders[$index_key]['status'] = 1;
                }
                $disorders[$index_key]['value'] = 0;
            }
            else {
            $disorders[$index_key]['status'] = Element_OphCoCvi_ClinicalInfo_Disorder_Assignment::model()
                ->getDisorderAffectedStatus($disorder->id,$element->id,$side);
            $disorders[$index_key]['value'] = Element_OphCoCvi_ClinicalInfo_Disorder_Assignment::model()
                    ->getDisorderMainCause($disorder->id,$element->id,$side);
            }
            $disorders[$index_key]['disorder'] = $disorder;
            $index_key++;
        }
        return $disorders;
    }
    */
}
