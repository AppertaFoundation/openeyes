<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class Element_OphTrOperationbooking_PreAssessment extends BaseEventTypeElement
{
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
        return 'et_ophtroperationbooking_preassessment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('type_id', 'required'),
            array('event_id, type_id, location_id', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, type_id, location_id', 'safe', 'on' => 'search'),
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
            'element_type' => array(self::HAS_ONE, ElementType::class, 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
            'eventType' => array(self::BELONGS_TO, EventType::class, 'event_type_id'),
            'event' => array(self::BELONGS_TO, Event::class, 'event_id'),
            'user' => array(self::BELONGS_TO, User::class, 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, User::class, 'last_modified_user_id'),
            'type' => array(self::BELONGS_TO, OphTrOperationbooking_PreAssessment_Type::class, 'type_id'),
            'location' => array(self::BELONGS_TO, OphTrOperationbooking_PreAssessment_Location::class, 'location_id'),
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
            'type_id' => 'Type of pre-assessment patient requires?',
            'location_id' => 'Location of pre-assessment',
        );
    }

    /**
     * @return array to Pre assessment element dropdown options
     */
    public function getPreassessmentTypes()
    {
        $preassessment_types = OphTrOperationbooking_PreAssessment_Type::model()->findAllByAttributes(array('active' => 1));
        $options = array();
        foreach ($preassessment_types as $type) {
            $options[$type->id] = array('data-use-location' => $type->use_location == 1 ? 1 : 0);
        }
        return $options;
    }

    public function hasTypes()
    {
        return OphTrOperationbooking_PreAssessment_Type::model()->countByAttributes(['active' => 1]) > 0;
    }
}
