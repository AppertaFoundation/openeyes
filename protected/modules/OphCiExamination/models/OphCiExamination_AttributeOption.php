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

use OE\factories\models\traits\HasFactory;

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
    use HasFactory;

    const SELECTION_LABEL_FIELD = 'value';
    protected $auto_update_relations = true;

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
        return [
            ['value', 'required'],
            ['value, delimiter, subspecialty_id, excluded_subspecialties', 'safe'],
            ['id, value, delimiter, excluded_subspecialties', 'safe', 'on' => 'search'],
            ['subspecialty_id, attribute_element_id', 'numerical', 'integerOnly' => true],
        ];
    }

    public function getLabel()
    {
        return ucfirst($this->value);
    }

    public function getSlug()
    {
        return $this->value . $this->delimiter . ' ';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'attribute_element' => [self::BELONGS_TO, OphCiExamination_AttributeElement::class, 'attribute_element_id'],
            'subspecialty' => [self::BELONGS_TO, \Subspecialty::class, 'subspecialty_id'],
            'excluded_subspecialties' => [
                self::MANY_MANY,
                \Subspecialty::class,
                'ophciexamination_attribute_option_exclude(option_id, subspecialty_id)'
            ]
        ];
    }

    public function attributeLabels()
    {
        return array(
            'attribute_element_id' => 'Element Attribute',
            'subspecialty_id' => 'Subspecialty',
        );
    }
}
