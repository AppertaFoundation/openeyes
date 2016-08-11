<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openbenefits.org.uk
 *
 * @author OpenEyes <info@openbenefits.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Created by PhpStorm.
 * User: himanshu
 * Date: 16/04/15
 * Time: 13:16.
 */

/**
 * This is the model class for table "drug".
 *
 * The followings are the available columns in table 'drug':
 *
 * @property int $id
 * @property string $name
 */
class FormularyDrugs extends BaseActiveRecordVersioned
{
    protected $auto_update_relations = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Benefit the static model class
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
        return 'drug';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array(
                'name, aliases, tallman, type_id, form_id, dose_unit,default_dose,default_route_id,default_frequency_id,default_duration_id, preservative_free, active, allergy_warnings',
                'safe',
            ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'allergy_warnings' => array(self::MANY_MANY, 'Drug', 'drug_allergy_assignment(drug_id,allergy_id)'),
            'drug_type' => array(self::BELONGS_TO, 'DrugType', 'type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'type_id' => 'Type',
            'tallman' => 'Tall Man Name',
            'form_id' => 'Form',
            'default_route_id' => 'Default Route',
            'default_frequency_id' => 'Default Frequency',
            'default_duration_id' => 'Default Duration',
            'drug_type.name' => 'Type',
        );
    }
}
