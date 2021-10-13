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
 * This is the model class for table "et_ophinbiometry_lenstype".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $lens_id_left
 * @property int $lens_id_right
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property OphInBiometry_LensType_Lens $lens
 */
class Element_OphInBiometry_Measurement extends SplitEventTypeElement
{
    public $service;

    /**
     * set defaults
     */
    public function init(){
        $this->axial_length_left = null;
        $this->axial_length_right = null;
        
        $this->k1_left = null;
        $this->k1_right = null;
        $this->k2_left = null;
        $this->k2_right = null;
        
        $this->k1_axis_left = null;
        $this->k1_axis_right = null;
        
        $this->delta_k_left = null;
        $this->delta_k_right = null;
        $this->delta_k_axis_left = null;
        $this->delta_k_axis_right = null;
        
        $this->k2_axis_left = null;
        $this->k2_axis_right = null;
        $this->acd_left = null;
        $this->acd_right = null;
        $this->snr_left = null;
        $this->snr_right = null;
    }
    
    
    public function beforeSave(){
        if ($this->snr_left==='')$this->snr_left = null;
        if ($this->snr_right==='')$this->snr_right = null;
        return parent::beforeSave();
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
        return 'et_ophinbiometry_measurement';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        if (count(OphInBiometry_Imported_Events::model()->findAllByAttributes(array('event_id' => $this->event_id))) == 0) {
            return array(
                array(
                    'event_id, eye_id , k1_left, k1_right, k2_left, k2_right, k1_axis_left, k1_axis_right, axial_length_left, axial_length_right, snr_left, snr_right, k2_axis_left, k2_axis_right, delta_k_left, delta_k_right, delta_k_axis_left, delta_k_axis_right, acd_left, acd_right, refraction_sphere_left, refraction_sphere_right, refraction_delta_left, refraction_delta_right, refraction_axis_left, refraction_axis_right, eye_status_left, eye_status_right, comments',
                    'safe',
                ),
                array(
                    'k1_left, k1_right, k2_left, k2_right, axial_length_left, axial_length_right',
                    'match',
                    'pattern' => '/([0-9]*?)(\.[0-9]{0,2})?/',
                ),
                array(
                    'k1_axis_left, k1_axis_right, snr_left, snr_right',
                    'match',
                    'pattern' => '/([0-9]*?)(\.[0-9]{0,1})?/',
                ),
                array(
                    'k1_left, k2_left, k1_axis_left, axial_length_left',
                    'requiredIfSide',
                    'side' => 'left',
                ),
                array(
                    'k1_right, k2_right, k1_axis_right, axial_length_right',
                    'requiredIfSide',
                    'side' => 'right',
                ),
                array('snr_left', 'checkNumericRangeIfSide', 'side' => 'left',  'max' => 2000, 'min' => 10),
                array('snr_right', 'checkNumericRangeIfSide', 'side' => 'right', 'max' => 2000, 'min' => 10),
                array('k1_left, k2_left', 'checkNumericRangeIfSide', 'side' => 'left', 'max' => 60, 'min' => 30),
                array('k1_right, k2_right', 'checkNumericRangeIfSide', 'side' => 'right', 'max' => 60, 'min' => 30),
                array('k1_axis_left', 'checkNumericRangeIfSide', 'side' => 'left', 'max' => 180, 'min' => 0),
                array('k1_axis_right', 'checkNumericRangeIfSide', 'side' => 'right', 'max' => 180, 'min' => 0),
                array('axial_length_left', 'checkNumericRangeIfSide', 'side' => 'left', 'max' => 40, 'min' => 15),
                array('axial_length_right', 'checkNumericRangeIfSide', 'side' => 'right', 'max' => 40, 'min' => 15),
                array('k1_left, k1_right, k2_left, k2_right, axial_length_left, axial_length_right, delta_k_left, delta_k_right, acd_left, acd_right,', 'default', 'setOnEmpty' => true, 'value' => 0.00),
                array('k1_axis_left, k1_axis_right, k2_axis_left, k2_axis_right, delta_k_axis_left, delta_k_axis_right', 'default', 'setOnEmpty' => true, 'value' => 0.0),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, event_id', 'safe', 'on' => 'search'),
            );
        } else {
            return array(
                array(
                    'event_id, eye_id , k1_left, k1_right, k2_left, k2_right, k1_axis_left, k1_axis_right, axial_length_left, axial_length_right, snr_left, snr_right, k2_axis_left, k2_axis_right, delta_k_left, delta_k_right, delta_k_axis_left, delta_k_axis_right, acd_left, acd_right, refraction_sphere_left, refraction_sphere_right, refraction_delta_left, refraction_delta_right, refraction_axis_left, refraction_axis_right, eye_status_left, eye_status_right, comments',
                    'safe',
                ),
            );
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'element_type' => array(
                self::HAS_ONE,
                'ElementType',
                'id',
                'on' => "element_type.class_name='".get_class($this)."'",
            ),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'eye_status_left' => array(self::BELONGS_TO, 'Eye_Status', 'eye_status_left'),
            'eye_status_right' => array(self::BELONGS_TO, 'Eye_Status', 'eye_status_right'),
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
            'k1_left' => 'K1 (D)',
            'k1_right' => 'K1 (D)',
            'k2_left' => 'K2 (D)',
            'k2_right' => 'K2 (D)',
            'k1_axis_left' => 'Axis K1 (D)',
            'k1_axis_right' => 'Axis K1 (D)',
            'axial_length_left' => 'Axial length (mm)',
            'axial_length_right' => 'Axial length (mm)',
            'snr_left' => 'SNR',
            'snr_right' => 'SNR',
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

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function isRequiredInUI()
    {
        return true;
    }

    public function getContainer_form_view()
    {
        return false;
    }
}
