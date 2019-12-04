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
 * This is the model class for table "et_ophinbiometry_calculation".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property string $target_refraction
 * @property int $formula_id
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property OphInBiometry_Calculation_Formula $formula
 */
class Element_OphInBiometry_Calculation extends SplitEventTypeElement
{
    public $service;

    /**
     * set defaults
     */
    public function init(){
        $this->target_refraction_left = null;
        $this->target_refraction_right = null;
    }

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
        return 'et_ophinbiometry_calculation';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, target_refraction_left, eye_id, formula_id_left,target_refraction_right, formula_id_right, comments, comments_left, comments_right', 'safe'),
            array('target_refraction_left, target_refraction_right', 'match', 'pattern' => '/([0-9]*?)(\.[0-9]{0,2})?/'),
            array('target_refraction_left', 'checkNumericRangeIfSide', 'side' => 'left', 'max' => 10, 'min' => -10),
            array('target_refraction_right', 'checkNumericRangeIfSide', 'side' => 'right', 'max' => 10, 'min' => -10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, ', 'safe', 'on' => 'search'),
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
            'formula_left' => array(self::BELONGS_TO, 'OphInBiometry_Calculation_Formula', 'formula_id_left'),
            'formula_right' => array(self::BELONGS_TO, 'OphInBiometry_Calculation_Formula', 'formula_id_right'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
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
            'target_refraction_left' => 'Target Refraction',
            'formula_id_left' => 'Formula',
            'target_refraction_right' => 'Target Refraction',
            'formula_id_right' => 'Formula',
            'emmetropia_left' => 'Emmetropic IOL power',
            'emmetropia_right' => 'Emmetropic IOL power',
            'comments_right' => 'General Comments',
            'comments_left' => 'General Comments',
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

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function isRequiredInUI()
    {
        return false;
    }
}
