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

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "ophciexamination_attribute_element".
 *
 * @property int $id
 * @property OphCiExamination_Attribute $attribute
 * @property ElementType $element_type
 * @property OphCiExamination_AttributeOption[] $options
 */
class OphCiExamination_AttributeElement extends \BaseActiveRecordVersioned
{
    const SELECTION_ORDER = 'element_type_id,attribute_id';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_attribute_element';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('attribute_id, element_type_id', 'required'),
            array('id, attribute_id, element_type_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'attribute' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Attribute', 'attribute_id'),
            'element_type' => array(self::BELONGS_TO, 'ElementType', 'element_type_id'),
            'options' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_AttributeOption', 'attribute_element_id'),
        );
    }

    /**
     * Fetches all the options for this attribute_element, standard _and_ subspecialty specific.
     *
     * @param int $subspecialty_id
     *
     * @return OphCiExamination_AttributeOption[]
     */
    public function findAllOptionsForSubspecialty($subspecialty_id = null)
    {
        $condition = 'attribute_element_id = :attribute_element_id AND ';
        $params = array(':attribute_element_id' => $this->id);
        if ($subspecialty_id) {
            $condition .=  '(subspecialty_id = :subspecialty_id OR subspecialty_id IS NULL)';
            $params[':subspecialty_id'] = $subspecialty_id;
        } else {
            $condition .=  'subspecialty_id IS NULL';
        }

        return OphCiExamination_AttributeOption::model()->findAll($condition, $params);
    }

    public function getName()
    {
        return $this->element_type->name.' - '.$this->attribute->name;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'attribute.name' => 'Attribute Name',
            'attribute.label' => 'Attribute Label',
            'element_type.name' => 'Element Mapping',
            'attribute_elements.name' => 'Element Mapping',
        );
    }
}
