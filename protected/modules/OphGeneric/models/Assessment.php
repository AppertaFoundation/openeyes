<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphGeneric\models;

class Assessment extends \SplitEventTypeElement
{
    public $widgetClass = 'OEModule\OphGeneric\widgets\AssessmentElement';
    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;
    protected $default_from_previous = false;
    protected $relation_defaults = array(
        'left_assessment' => array(
            'eye_id' => \Eye::LEFT,
        ),
        'right_assessment' => array(
            'side' => \Eye::RIGHT,
        ),
    );

    public function tableName()
    {
        return 'et_ophgeneric_assessment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['event_id, last_modified_user_id, created_user_id', 'length', 'max' => 10],
            ['eye_id', 'safe'],
            ['id, event_id, last_modified_user_id, last_modified_date, created_user_id, created_date, eye_id', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'createdUser' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'lastModifiedUser' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'readings' => array(self::HAS_MANY, 'OEModule\OphGeneric\models\AssessmentEntry', 'element_id'),
            'left_assessment' => array(self::HAS_ONE, 'OEModule\OphGeneric\models\AssessmentEntry', 'element_id', 'on' => 'left_assessment.eye_id = ' . \Eye::LEFT),
            'right_assessment' => array(self::HAS_ONE, 'OEModule\OphGeneric\models\AssessmentEntry', 'element_id', 'on' => 'right_assessment.eye_id = ' . \Eye::RIGHT),
        ];
    }

    public function behaviors()
    {
        return [
            'PatientLevelElementBehaviour' => 'PatientLevelElementBehaviour',
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}
