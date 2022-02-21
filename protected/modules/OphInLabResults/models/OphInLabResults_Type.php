<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class OphInLabResults_Type
 * @property Institution[] $institutions
 * @property OphInLabResults_Type_Institution $institutionAssignments
 */
class OphInLabResults_Type extends BaseActiveRecordVersioned
{
    use MappedReferenceData;

    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION;
    }

    protected function mappingColumn(int $level): string
    {
        return 'labresults_type_id';
    }

    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphInLabResults_Type|BaseActiveRecord static model class
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
        return 'ophinlabresults_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('type, result_element_id, ', 'required'),
            array('min_range', 'minRangeValidation'),
            array('max_range', 'maxRangeValidation'),
            array('normal_min', 'normalMinValueValidation'),
            array('normal_max', 'normalMaxValueValidation'),
            array('type, result_element_id, field_type_id, show_units, allow_unit_change, default_units, custom_warning_message, min_range, max_range,
            normal_min, normal_max, show_on_whiteboard, display_order', 'safe'),
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
            'result_element_type' => array(self::BELONGS_TO, 'ElementType', 'result_element_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'institutions' => array(self::MANY_MANY, 'Institution', 'ophinlabresults_type_institution(labresults_type_id, institution_id)'),
            'institutionAssignments' => array(self::HAS_MANY, 'ophinlabresults_type_institution', 'labresults_type_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'fieldType' => [self::BELONGS_TO, 'OphInLabResults_Field_Type', 'field_type_id'],
            'resultOptions' => [self::HAS_MANY, 'OphInLabResults_Type_Options', 'type']
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'type' => 'Type',
            'result_element_type' => 'Result Type',
        );
    }

    public function normalMinValueValidation($attribute, $params)
    {
        if (isset($this->$attribute)) {
            if ($this->normal_max && $this->$attribute > $this->normal_max) {
                $this->addError(
                    $attribute,
                    $attribute . ' has to be lower than the normal max value'
                );
            }

            if ($this->min_range && $this->$attribute < $this->min_range) {
                $this->addError(
                    $attribute,
                    $attribute . ' has to be higher than the range min'
                );
            }
        }
    }

    public function normalMaxValueValidation($attribute, $params)
    {
        if (isset($this->$attribute)) {
            if ($this->normal_min && $this->$attribute < $this->normal_min) {
                $this->addError(
                    $attribute,
                    $attribute . ' has to be higher than the normal min value'
                );
            }
            if ($this->max_range && $this->$attribute > $this->max_range) {
                $this->addError(
                    $attribute,
                    $attribute . ' has to be lower than the range max'
                );
            }
        }
    }

    public function minRangeValidation($attribute, $params)
    {
        if (isset($this->$attribute) && $this->max_range) {
            if ($this->$attribute > $this->max_range) {
                $this->addError(
                    $attribute,
                    $attribute . ' has to be lower than max range'
                );
            }
        }
    }

    public function maxRangeValidation($attribute, $params)
    {
        if (isset($this->$attribute)&& $this->min_range) {
            if ($this->$attribute < $this->min_range) {
                $this->addError(
                    $attribute,
                    $attribute . ' has to be higher than min range'
                );
            }
        }
    }

    public function hasInstitutionMapping(int $id)
    {
        foreach ($this->institutions as $institution) {
            if ((int)$institution->id === $id) {
                return true;
            }
        }
        return false;
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.display_order');
    }
}
