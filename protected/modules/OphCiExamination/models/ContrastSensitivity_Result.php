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

/**
 * Class ContrastSensitivity_Result
 * @package OEModule\OphCiExamination\models
 * @property $eye_id
 * @property $contrastsensitivity_type_id
 * @property $value
 */
class ContrastSensitivity_Result extends \BaseElement
{
    use traits\HasCorrectionType;
    use traits\HasRelationOptions;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    protected $correction_type_attributes = ['correctiontype_id'];

    const BEO = 2;
    const LEFT = 1;
    const RIGHT = 0;

    const DISPLAY_BEO = "BEO";
    const DISPLAY_LEFT = "LEFT";
    const DISPLAY_RIGHT = "RIGHT";

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_contrastsensitivity_result';
    }

    public function rules()
    {
        return array_merge(
            [
                ['element_id, eye_id, contrastsensitivity_type_id, value', 'safe'],
                ['eye_id, contrastsensitivity_type_id, value', 'required'],
                [
                    'contrastsensitivity_type_id', 'exist', 'allowEmpty' => true,
                    'attributeName' => 'id',
                    'className' => ContrastSensitivity_Type::class,
                    'message' => '{attribute} is invalid'
                ],
                [
                    'value', 'numerical',
                    'min' => '0', 'max' => '9',
                    'integerOnly' => true,
                    'message' => '{attribute} is invalid'
                ],
                ['eye_id', 'in',
                    'range' => [self::BEO, self::LEFT, self::RIGHT],
                    'message' => '{attribute} is invalid'
                ]
            ],
            $this->rulesForCorrectionType()
        );
    }

    /**
     * @return array
     */
    public function relations()
    {
        return array_merge(
            [
                'element' => [self::BELONGS_TO, ContrastSensitivity::class, 'element_id'],
                'contrastsensitivity_type' => [self::BELONGS_TO, ContrastSensitivity_Type::class, 'contrastsensitivity_type_id'],
                'user' => [self::BELONGS_TO, \User::class, 'created_user_id'],
                'usermodified' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
            ],
            $this->relationsForCorrectionType(),
        );
    }

    public function attributeLabels()
    {
        return [
            'eye_id' => 'Laterality',
            'contrastsensitivity_type_id' => 'Sensitivity',
            'value' => 'Value',
            'correctiontype_id' => 'Correction',
            'with_head_posture' => 'Head Posture',
        ];
    }

    public function __toString()
    {
        $str = [
            $this->value,
        ];

        if ($this->correctiontype) {
            $str[] = "(" . $this->correctiontype . ")";
        }

        return implode(" ", $str);
    }

    /**
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public function getEye_options()
    {
        return [
            ['id' => self::LEFT, 'name' => self::DISPLAY_LEFT],
            ['id' => self::RIGHT, 'name' => self::DISPLAY_RIGHT],
            ['id' => self::BEO, 'name' => self::DISPLAY_BEO]
        ];
    }

    public function getEyeDisplay($eye_id)
    {
        return [
            self::LEFT => self::DISPLAY_LEFT,
            self::RIGHT => self::DISPLAY_RIGHT,
            self::BEO => self::DISPLAY_BEO
        ][$eye_id];
    }
}
