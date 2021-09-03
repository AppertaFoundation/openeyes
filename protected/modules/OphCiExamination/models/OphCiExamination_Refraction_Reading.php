<?php

/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\traits\HasRelationOptions;

/**
 * Class OphCiExamination_Refraction_Reading
 *
 * @package OEModule\OphCiExamination\models
 * @property $sphere
 * @property $cylinder
 * @property int $axis
 * @property int $type_id
 * @property OphCiExamination_Refraction_Type $type
 * @property string $type_other
 */
class OphCiExamination_Refraction_Reading extends \BaseActiveRecordVersioned
{
    use HasRelationOptions;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_refraction_reading';
    }

    public function rules()
    {
        return [
            ['element_id, eye_id, sphere, cylinder, axis, type_id, type_other', 'safe'],
            ['eye_id', 'in', 'range' => [SidedData::RIGHT, SidedData::LEFT]],
            ['sphere, cylinder, axis', 'required'],
            ['sphere', 'numerical', 'min' => -45, 'max' => 45],
            ['cylinder', 'numerical', 'min' => -25, 'max' => 25],
            ['axis', 'numerical', 'min' => -180, 'max' => 180, 'integerOnly' => true],
            [
                'type_id', 'exist', 'allowEmpty' => true,
                'attributeName' => 'id',
                'className' => OphCiExamination_Refraction_Type::class,
                'message' => '{attribute} is invalid'
            ],
            ['type_id', 'requiredIfNotRefractionTypeOther'],
            ['type_other', 'requiredIfNotRefractionType']
        ];
    }

    public function relations()
    {
        return [
            'user' => [self::BELONGS_TO, \User::class, 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
            'type' => [self::BELONGS_TO, OphCiExamination_Refraction_Type::class, 'type_id'],
        ];
    }

    public function requiredIfNotRefractionTypeOther($attribute, $params)
    {
        if (empty($this->type_other) and !$this->$attribute) {
            $this->addError(
                $attribute,
                $this->getAttributeLabel($attribute) . ' cannot be blank. Please specify a valid refraction.'
            );
        }
    }

    public function requiredIfNotRefractionType($attribute, $params)
    {
        if (!$this->type_id && empty($this->$attribute)) {
            $this->addError(
                $attribute,
                $this->getAttributeLabel($attribute) . ' cannot be blank. Please specify a valid refraction.'
            );
        }
    }

    public function attributeLabels()
    {
        return [
            'sphere' => 'Sphere',
            'cylinder' => 'Cylinder',
            'axis' => 'Axis',
            'type_id' => 'Type',
            'type_other' => 'Type',
        ];
    }

    public function getSphericalEquivalent()
    {
        if (!is_numeric($this->sphere) || !is_numeric($this->cylinder)) {
            return null;
        }

        $se = $this->sphere + ($this->cylinder * 0.5);

        return ($se > 0 ? "+" : "") . number_format($se, 2);
    }

    /**
     * @return string
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public function getSphere_display()
    {
        return $this->getPrefixedTwoDPAttribute('sphere');
    }

    /**
     * @return string
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public function getCylinder_display()
    {
        return $this->getPrefixedTwoDPAttribute('cylinder');
    }

    /**
     * @return string
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public function getType_display()
    {
        return $this->type ? (string) $this->type : $this->type_other;
    }

    /**
     * @return string
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public function getRefraction_display()
    {
        return $this->sphere_display
            . "/"
            . $this->cylinder_display
            . " X " .
            $this->axis
            . "°";
    }

    public function __toString()
    {
         return $this->refraction_display
            . " SE:"
            . $this->getSphericalEquivalent()
            . " " . $this->type_display;
    }

    private function getPrefixedTwoDPAttribute($attribute)
    {
        if ($this->$attribute === null || $this->$attribute === '') {
            return "";
        }

        $prefix = $this->$attribute >= 0 ? "+" : "";
        return sprintf("{$prefix}%.2f", $this->$attribute);
    }
}
