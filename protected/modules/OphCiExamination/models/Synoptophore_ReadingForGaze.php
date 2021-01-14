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
 * Class Synoptophore_ReadingForGaze
 * @package OEModule\OphCiExamination\models
 * @property $gaze_type
 * @property $horizontal_angle
 * @property $vertical_power
 * @property $direction_id
 * @property $direction
 * @property $torsion
 * @property $deviation_id
 * @property $deviation
 * @property $side
 */
class Synoptophore_ReadingForGaze extends \BaseElement
{
    use traits\HasRelationOptions;

    protected $auto_validate_relations = true;

    public const RIGHT_UP = 'right-up';
    public const RIGHT_MID = 'right-mid';
    public const RIGHT_DOWN = 'right-down';
    public const CENTER_UP = 'center-up';
    public const CENTER_MID = 'center-mid';
    public const CENTER_DOWN = 'center-down';
    public const LEFT_UP = 'left-up';
    public const LEFT_MID = 'left-mid';
    public const LEFT_DOWN = 'left-down';

    protected static $at_least_one_required_attributes = [
        "horizontal_angle", "vertical_power", "direction_id", "torsion", "deviation_id"
    ];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_synoptophore_readingforgaze';
    }

    public function rules()
    {
        return array_merge(
            [
                ['element_id, gaze_type, horizontal_angle, vertical_power, direction_id, torsion, deviation_id, eye_id', 'safe'],
                ['gaze_type, eye_id', 'required'],
                ['eye_id', 'in', 'range' => [\Eye::RIGHT, \Eye::LEFT]],
                [
                    'gaze_type', 'in',
                    'range' => $this->getValidGazeTypes(),
                    'message' => '{attribute} is invalid'
                ],
                [
                    'horizontal_angle', 'numerical',
                    'min' => 0, 'max' => '40',
                    'integerOnly' => true,
                    'message' => '{attribute} is invalid'
                ],
                [
                    'vertical_power', 'numerical',
                    'min' => '0', 'max' => '50',
                    'integerOnly' => true,
                    'message' => '{attribute} is invalid'
                ],
                [
                    'torsion', 'numerical',
                    'min' => '-60', 'max' => '60',
                    'integerOnly' => true,
                    'message' => '{attribute} is invalid'
                ],
                [
                    'direction_id', 'exist', 'allowEmpty' => true,
                    'attributeName' => 'id',
                    'className' => Synoptophore_Direction::class,
                    'message' => '{attribute} is invalid'
                ],
                [
                    'deviation_id', 'exist', 'allowEmpty' => true,
                    'attributeName' => 'id',
                    'className' => Synoptophore_Deviation::class,
                    'message' => '{attribute} is invalid'
                ],
            ]
        );
    }

    /**
     * @return array
     */
    public function relations()
    {
        return array_merge(
            [
                'element' => [self::BELONGS_TO, Synoptophore::class, 'element_id'],
                'direction' => [self::BELONGS_TO, Synoptophore_Direction::class, 'direction_id'],
                'deviation' => [self::BELONGS_TO, Synoptophore_Deviation::class, 'deviation_id'],
                'user' => [self::BELONGS_TO, \User::class, 'created_user_id'],
                'usermodified' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
            ]
        );
    }

    public function attributeLabels()
    {
        return [
            'horizontal_angle' => 'H Angle (deg)',
            'direction' => 'Order',
            'vertical_power' => 'V Angle (Δ)',
            'torsion' => 'Torsion',
            'deviation' => 'Deviation',
        ];
    }

    protected function afterValidate()
    {
        $this->validateAtLeastOneRequireAttributeIsSet();

        parent::afterValidate();
    }

    public function getValidGazeTypes()
    {
        return [
            self::RIGHT_UP,
            self::RIGHT_MID,
            self::RIGHT_DOWN,
            self::CENTER_UP,
            self::CENTER_MID,
            self::CENTER_DOWN,
            self::LEFT_UP,
            self::LEFT_MID,
            self::LEFT_DOWN
        ];
    }

    public function __toString()
    {
        $reading_string = [];
        if ($this->horizontal_angle !== null && $this->horizontal_angle !== "") {
            $reading_string[] = "+{$this->horizontal_angle}°";
        }
        if ($this->vertical_power !== null && $this->vertical_power !== "") {
            $reading_string[] = "{$this->vertical_power}Δ";
        }
        foreach (['direction', 'torsion'] as $attr) {
            if ($this->$attr !== null && $this->$attr !== "") {
                $reading_string[] = $this->$attr;
            }
        }
        if ($this->deviation) {
            $reading_string[] = $this->deviation->abbreviation;
        }

        return implode(" ", $reading_string);
    }

    private function validateAtLeastOneRequireAttributeIsSet()
    {
        foreach (static::$at_least_one_required_attributes as $attr) {
            if ($this->$attr !== null && $this->$attr !== '') {
                return;
            }
        }

        $this->addError('gaze_type', 'At least one attribute must be recorded');
    }
}
