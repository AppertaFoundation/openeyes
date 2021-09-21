<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;


/**
 * Class ObservationEntry
 * @package OEModule\OphCiExamination\models
 *
 * @property int $id
 * @property int $element_id
 */
class ObservationEntry extends \BaseElement
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return PreviousOperation the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ophciexamination_observation_entry';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_id', 'length', 'max'=>11),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('blood_pressure_systolic, blood_pressure_diastolic, o2_sat, pulse', 'length', 'max'=>3),
            array('blood_glucose, hba1c', 'length', 'max'=>4),
            array('height, weight', 'length', 'max'=>5),
            array('last_modified_date, created_date, taken_at', 'safe'),
            array('blood_pressure_systolic', 'numerical','min'=>0, 'max'=>400),
            array('blood_pressure_diastolic', 'numerical','min'=>0, 'max'=>400),
            array('o2_sat', 'numerical','min'=>0, 'max'=>100),
            array('blood_glucose', 'numerical', 'min'=>0.0, 'max'=>50.0),
            array('hba1c', 'numerical', 'min'=>0, 'max'=>1000),
            array('height', 'numerical', 'min'=>0.0, 'max'=>250.0),
            array('weight', 'numerical', 'min'=>0.0, 'max'=>250.0),
            array('pulse', 'numerical', 'min'=>0, 'max'=>200),
            array('temperature', 'numerical', 'min'=>30.0, 'max'=>45.0),
            array('blood_pressure_systolic,blood_pressure_diastolic,o2_sat,blood_glucose,hba1c,height,weight,pulse,temperature', 'default', 'setOnEmpty' => true, 'value' => null),
            array('blood_pressure_systolic,blood_pressure_diastolic,o2_sat,blood_glucose,hba1c,height,weight,pulse,temperature', \OEAtLeastOneRequiredValidator::class),
            array('id, element_id, blood_pressure_systolic, blood_pressure_diastolic, o2_sat, blood_glucose, hba1c, height, weight, pulse, temperature, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'element' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Observations', 'element_id'),
        );
    }

    /*
     * Calculate BMI
     * @params $weight weight in kg
     * @params $height height in centimeters
     * @return float
     */
    public function bmiCalculator($weight, $height)
    {
        $height_meter = $height / 100;
        $result = $weight / ($height_meter * $height_meter);

        return number_format((float)$result, 2, '.', '');
    }
}
