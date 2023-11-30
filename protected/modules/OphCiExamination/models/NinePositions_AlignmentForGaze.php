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
 * Class NinePositions_AlignmentForGaze
 *
 * Stores the data for a specific gaze angle for a Nine Positions Reading
 *
 * @package OEModule\OphCiExamination\models
 * @property int $reading_id
 * @property NinePositions_Reading $reading
 * @property string $gaze_type
 * @property int $horizontal_angle
 * @property int $horizontal_e_deviation_id
 * @property NinePositions_HorizontalEDeviation $horizontal_e_deviation
 * @property int $horizontal_x_deviation_id
 * @property NinePositions_HorizontalXDeviation $horizontal_x_deviation
 * @property string $horizontal_prism_position
 * @property int $vertical_angle
 * @property int $vertical_deviation_id
 * @property NinePositions_VerticalDeviation $vertical_deviation
 * @property string $vertical_prism_position
 */
class NinePositions_AlignmentForGaze extends \BaseElement
{
    use traits\HasRelationOptions;

    public const BI = 'BI';
    public const BO = 'BO';
    public const HORIZONTAL_PRISMS = [self::BI, self::BO];

    public const BURE = 'BURE';
    public const BULE = 'BULE';
    public const BDRE = 'BDRE';
    public const BDLE = 'BDLE';
    public const RL = 'R/L';
    public const LR = 'L/R';

    public const VERTICAL_PRISMS = [self::BURE, self::BULE, self::BDRE, self::BDLE, self::RL, self::LR];

    public const RIGHT_UP = 'right-up';
    public const RIGHT_MID = 'right-mid';
    public const RIGHT_DOWN = 'right-down';
    public const CENTER_UP = 'center-up';
    public const CENTER_MID = 'center-mid';
    public const CENTER_DOWN = 'center-down';
    public const LEFT_UP = 'left-up';
    public const LEFT_MID = 'left-mid';
    public const LEFT_DOWN = 'left-down';
    public const HEAD_TILT_RIGHT = 'head-tilt-right';
    public const HEAD_TILT_LEFT = 'head-tilt-left';
    public const NEAR = 'near';

    public const GAZE_TYPES = [
        self::RIGHT_UP,
        self::RIGHT_MID,
        self::RIGHT_DOWN,
        self::CENTER_UP,
        self::CENTER_MID,
        self::CENTER_DOWN,
        self::LEFT_UP,
        self::LEFT_MID,
        self::LEFT_DOWN,
        self::HEAD_TILT_RIGHT,
        self::HEAD_TILT_LEFT,
        self::NEAR
    ];

    protected array $horizontal_attributes = [
        'horizontal_prism_position',
        'horizontal_e_deviation_id',
        'horizontal_x_deviation_id'
    ];

    protected array $vertical_attributes = [
        'vertical_prism_position',
        'vertical_deviation_id'
    ];

    /** @var array|string[] At least one of these attributes must be set for record to be valid */
    protected array $meaningful_attributes = [
        'horizontal_angle',
        'horizontal_prism_position',
        'horizontal_e_deviation_id',
        'horizontal_x_deviation_id',
        'vertical_angle',
        'vertical_prism_position',
        'vertical_deviation_id'
    ];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_ninepositions_alignmentforgaze';
    }

    public function rules()
    {
        return [
            [
                'reading_id, gaze_type, horizontal_angle, horizontal_e_deviation_id, horizontal_x_deviation_id'
                . 'horizontal_prism_position, vertical_angle, vertical_deviation_id, vertical_prism_position',
                'safe',
            ],
            [
                'gaze_type',
                'required'
            ],
            [
                'gaze_type',
                'in',
                'range' => self::GAZE_TYPES,
                'message' => '{attribute} is invalid'
            ],
            ['horizontal_angle', 'numerical', 'min' => 0, 'max' => 90],
            ['horizontal_prism_position', 'type', 'type' => 'string'],
            [
                'horizontal_prism_position',
                'in',
                'allowEmpty' => true,
                'range' => self::HORIZONTAL_PRISMS,
                'message' => '{attribute} is invalid'
            ],
            [
                'horizontal_e_deviation_id',
                'exist',
                'allowEmpty' => true,
                'attributeName' => 'id',
                'className' => NinePositions_HorizontalEDeviation::class,
                'message' => '{attribute} is invalid'
            ],
            [
                'horizontal_x_deviation_id',
                'exist',
                'allowEmpty' => true,
                'attributeName' => 'id',
                'className' => NinePositions_HorizontalXDeviation::class,
                'message' => '{attribute} is invalid'
            ],
            ['vertical_angle', 'numerical', 'min' => 0, 'max' => 50],
            ['vertical_prism_position', 'type', 'type' => 'string'],
            [
                'vertical_prism_position',
                'in',
                'allowEmpty' => true,
                'range' => self::VERTICAL_PRISMS,
                'message' => '{attribute} is invalid'
            ],
            [
                'vertical_deviation_id',
                'exist',
                'allowEmpty' => true,
                'attributeName' => 'id',
                'className' => NinePositions_VerticalDeviation::class,
                'message' => '{attribute} is invalid'
            ],
        ];
    }

    public function relations()
    {
        return [
            'reading' => [self::BELONGS_TO, NinePositions_Reading::class, 'reading_id'],
            'vertical_deviation' => [self::BELONGS_TO, NinePositions_VerticalDeviation::class, 'vertical_deviation_id'],
            'horizontal_e_deviation' => [
                self::BELONGS_TO,
                NinePositions_HorizontalEDeviation::class,
                'horizontal_e_deviation_id'
            ],
            'horizontal_x_deviation' => [
                self::BELONGS_TO,
                NinePositions_HorizontalXDeviation::class,
                'horizontal_x_deviation_id'
            ]
        ];
    }

    /**
     * @return string
     */
    public function getDisplay_horizontal()
    {
        if ($this->horizontal_prism_position) {
            return $this->horizontal_angle . $this->horizontal_prism_position;
        }
        if ($this->horizontal_e_deviation_id) {
            return $this->horizontal_angle . $this->horizontal_e_deviation->abbreviation;
        }
        return $this->horizontal_angle . ($this->horizontal_x_deviation ? $this->horizontal_x_deviation->abbreviation : '');
    }

    public function getDisplay_vertical()
    {
        if ($this->vertical_prism_position) {
            return $this->vertical_angle . $this->vertical_prism_position;
        }
        return $this->vertical_angle . ($this->vertical_deviation ? $this->vertical_deviation->abbreviation : $this->vertical_deviation);
    }

    protected function afterValidate()
    {
        parent::afterValidate();
        if (!$this->hasMaximumOfOneAttribute($this->horizontal_attributes)) {
            $this->addErrorToNonEmptyAttributes('Can only have one horizontal value', $this->horizontal_attributes);
        }
        if (!$this->hasMaximumOfOneAttribute($this->vertical_attributes)) {
            $this->addErrorToNonEmptyAttributes('Can only have one vertical value', $this->vertical_attributes);
        }

        if (!$this->hasAtleastOneMeaningfulAttribute()) {
            $this->addError('gaze_type', 'An alignment value is required');
        }
    }

    protected function addErrorToNonEmptyAttributes($error, $attributes)
    {
        foreach ($attributes as $attr) {
            if (!empty($this->$attr)) {
                $this->addError($attr, $error);
            }
        }
    }

    protected function hasMaximumOfOneAttribute($attributes)
    {
        return count(
            array_filter(
                $this->getAttributes($attributes),
                function ($val) {
                    return !empty($val);
                }
            )
        ) <= 1;
    }

    protected function hasAtleastOneMeaningfulAttribute()
    {
        return count(
            array_filter(
                $this->getAttributes($this->meaningful_attributes),
                function ($val) {
                    return $val !== '' && $val !== null;
                }
            )
        ) > 0;
    }

    public function __clone()
    {
        $this->unsetAttributes(['id', 'reading_id']);
        $this->setIsNewRecord(true);
    }
}
