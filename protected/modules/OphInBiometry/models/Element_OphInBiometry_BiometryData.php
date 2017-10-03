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
 * This is the model class for table "et_ophinbiometry_biometrydat".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property string $axial_length_left
 * @property string $r1_left
 * @property string $r2_left
 * @property string $acd_left
 * @property string $scleral_thickness_left
 * @property string $axial_length_right
 * @property string $r1_right
 * @property string $r2_right
 * @property string $acd_right
 * @property string $scleral_thickness_right
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 */
class Element_OphInBiometry_BiometryData extends SplitEventTypeElement
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
        return 'et_ophinbiometry_biometrydat';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, eye_id, '.
            'axial_length_left, r1_left, r2_left,  r1_axis_left, acd_left, scleral_thickness_left,'.
            'axial_length_right, r1_right, r2_right,  r1_axis_right, acd_right, scleral_thickness_right',  'safe', ),
            array('r1_left,r1_left, r2_left', 'requiredIfSide', 'side' => 'left'),
            array('r1_right,r1_right, r2_right', 'requiredIfSide', 'side' => 'right'),
            // Please remove those attributes that should not be searched.
            array('id, event_id', 'safe', 'on' => 'search'),
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
            'axial_length_left' => 'Axial Length',
            'r1_left' => 'R1',
            'r2_left' => 'R2',
            'r1_axis_left' => 'Axis',
            'acd_left' => 'ACD',
            'scleral_thickness_left' => 'Scleral Thickness',
            'axial_length_right' => 'Axial Length',
            'r1_right' => 'R1',
            'r2_right' => 'R2',
            'r1_axis_right' => 'Axis',
            'acd_right' => 'ACD',
            'scleral_thickness_right' => 'Scleral Thickness',
        );
    }

    public function setDefaultOptions(Patient $patient = null)
    {
        // It is necessary to set these values to be an integer to prevent eyedraw
        // bound fields from breaking. See [ORB-340]
        $this->axial_length_left = 0;
        $this->axial_length_right = 0;
        parent::setDefaultOptions();
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

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function isRequiredInUI()
    {
        return false;
    }
}
