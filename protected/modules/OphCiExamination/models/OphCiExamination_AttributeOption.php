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
 * This is the model class for table "ophciexamination_attribute_option".
 *
 * @property int $id
 * @property Subspecialty $subspecialty
 * @property OphCiExamination_AttributeElement $attribute_element
 * @property string $value
 * @property string $delimiter
 */
class OphCiExamination_AttributeOption extends \BaseActiveRecordVersioned
{
    const SELECTION_LABEL_FIELD = 'value';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_attribute_option';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('value, delimiter', 'required'),
            array('id, value, delimiter', 'safe', 'on' => 'search'),
            array('subspecialty_id, attribute_element_id', 'numerical', 'integerOnly' => true),
        );
    }

    public function getLabel()
    {
        return ucfirst($this->value);
    }

    public function getSlug()
    {
        return $this->value.$this->delimiter.' ';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
                'attribute_element' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_AttributeElement', 'attribute_element_id'),
                'subspecialty' => array(self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'attribute_element_id' => 'Element Attribute',
            'subspecialty_id' => 'Subspecialty',
        );
    }
}
