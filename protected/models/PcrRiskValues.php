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
 * This is the model class for table "pcr_risk_value".
 *
 * The followings are the available columns in table 'pcr_risk_value':
 *
 * @property string glaucoma
 *
 * The following are the available model relations:
 * @property Patient $patient
 * @property Eye $eye
 * @property DoctorGrade $doctorGrade
 */
class PcrRiskValues extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Address the static model class
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
        return 'pcr_risk_values';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['doctor_grade_id', 'required'],
            ['glaucoma, pxf, diabetic, no_fundal_view, brunescent_white_cataract, alpha_receptor_blocker, can_lie_flat', 'length', 'max' => 1],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'patient' => [self::BELONGS_TO, 'Patient', 'patient_id'],
            'eye' => [self::BELONGS_TO, 'Eye', 'eye_id'],
            'doctorGrade' => [self::BELONGS_TO, 'DoctorGrade', 'doctor_grade_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'doctor_grade_id' => 'Surgeon Grade',
        ];
    }
}
