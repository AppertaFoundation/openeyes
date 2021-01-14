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

/**
 * Class NinePositions_MovementForGaze
 *
 * @package OEModule\OphCiExamination\models
 * @property int $reading_id
 * @property NinePositions_Reading $reading
 * @property string $gaze_type
 * @property int $movement_id
 * @property NinePositions_Movement $movement
 * @property int $eye_id
 */
class NinePositions_MovementForGaze extends \BaseElement
{
    use traits\HasRelationOptions;

    public const RIGHT_UP = 'right-up';
    public const RIGHT_MID = 'right-mid';
    public const RIGHT_DOWN = 'right-down';
    public const LEFT_UP = 'left-up';
    public const LEFT_MID = 'left-mid';
    public const LEFT_DOWN = 'left-down';

    public const GAZE_TYPES = [
        self::RIGHT_UP,
        self::RIGHT_MID,
        self::RIGHT_DOWN,
        self::LEFT_UP,
        self::LEFT_MID,
        self::LEFT_DOWN
    ];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_ninepositions_movementforgaze';
    }

    public function rules()
    {
        return [
            ['reading_id, gaze_type, movement_id, eye_id', 'safe'],
            ['gaze_type, movement_id, eye_id', 'required'],
            ['eye_id', 'in', 'range' => [\Eye::RIGHT, \Eye::LEFT]],
            [
                'gaze_type',
                'in',
                'range' => self::GAZE_TYPES,
                'message' => '{attribute} is invalid'
            ],
            [
                'movement_id',
                'exist',
                'allowEmpty' => false,
                'attributeName' => 'id',
                'className' => NinePositions_Movement::class,
                'message' => '{attribute} is invalid'
            ],
        ];
    }

    public function relations()
    {
        return [
            'reading' => [self::BELONGS_TO, NinePositions_Reading::class, 'reading_id'],
            'movement' => [self::BELONGS_TO, NinePositions_Movement::class, 'movement_id'],
            'user' => [self::BELONGS_TO, \User::class, 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
            'gaze_readings' => [self::HAS_MANY, NinePositions_AlignmentForGaze::class, 'reading_id']
        ];
    }

    public function isForSide($sideString)
    {
        return !empty($this->eye_id) && [
            'right' => \Eye::RIGHT,
            'left' => \Eye::LEFT
        ][$sideString] ?? null === (int) $this->eye_id;
    }
}
