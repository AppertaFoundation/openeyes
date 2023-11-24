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

use OEModule\OphCiExamination\models\traits\HasWithHeadPosture;

/**
 * Class NinePositions_Reading
 *
 * @package OEModule\OphCiExamination\models
 * @property int $element_id
 * @property bool $with_correction
 * @property bool $with_head_posture
 * @property bool $wong_supine_positive
 * @property bool $hess_chart
 * @property int $right_dvd
 * @property int $left_dvd
 * @property string $right_eyedraw
 * @property string $left_eyedraw
 * @property bool $full_ocular_movement
 * @property string $comments
 * @property NinePositions_AlignmentForGaze[] $alignments
 * @property NinePositions_MovementForGaze[] $movements
 */
class NinePositions_Reading extends \BaseElement
{
    use HasWithHeadPosture {
        getDisplay_with_head_posture as baseDisplay_with_head_posture;
    }

    public function defaultScope()
    {
        if ($this->getDefaultScopeDisabled()) {
            return [];
        }

        return [ 'with' => ['alignments', 'movements', 'right_movements', 'left_movements']];
    }

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    public const WITH_CORRECTION = '1';
    public const WITHOUT_CORRECTION = '0';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_ninepositions_reading';
    }

    public function rules()
    {
        return array_merge(
            [
                [
                    'element_id, with_correction, with_head_posture, wong_supine_positive, hess_chart, ' .
                    'full_ocular_movement, right_dvd, left_dvd, right_eyedraw, left_eyedraw, comments, alignments, ' .
                    'movements', 'safe'
                ],
                ['left_eyedraw, right_eyedraw', 'required'],
                [
                    'with_correction', 'in',
                    'range' => [static::WITH_CORRECTION, static::WITHOUT_CORRECTION],
                    'message' => '{attribute} is invalid'
                ],
                [
                    'full_ocular_movement', 'boolean'
                ],
                [
                    'full_ocular_movement', 'validateFullOcularMovement'
                ],
                ['right_dvd, left_dvd', 'numerical', 'integerOnly' => true]
            ],
            $this->rulesForWithHeadPosture()
        );
    }

    public function relations()
    {
        return [
            'element' => [self::BELONGS_TO, NinePositions::class, 'element_id'],
            'user' => [self::BELONGS_TO, \User::class, 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
            'alignments' => [self::HAS_MANY, NinePositions_AlignmentForGaze::class, 'reading_id'],
            'movements' => [self::HAS_MANY, NinePositions_MovementForGaze::class, 'reading_id'],
            'right_movements' => [
                self::HAS_MANY,
                NinePositions_MovementForGaze::class,
                'reading_id',
                'on' => 'right_movements.eye_id = ' . \Eye::RIGHT
            ],
            'left_movements' => [
                self::HAS_MANY,
                NinePositions_MovementForGaze::class,
                'reading_id',
                'on' => 'left_movements.eye_id = ' . \Eye::LEFT
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'with_correction' => 'Glasses',
            'with_head_posture' => 'CHP',
            'wong_supine_positive' => 'Wong supine positive',
            'hess_chart' => 'Hess chart',
            'right_dvd' => 'DVD',
            'left_dvd' => 'DVD',
        ];
    }

    /**
     * @param $gaze_type
     * @return NinePositions_AlignmentForGaze|null
     */
    public function getAlignmentForGazeType($gaze_type)
    {
        foreach ($this->alignments as $gaze_reading) {
            if ($gaze_reading->gaze_type === $gaze_type) {
                return $gaze_reading;
            }
        }
    }

    public function getMovementForGazeType($side, $gaze_type)
    {
        foreach ($this->movements as $movement_for_gaze) {
            if ($movement_for_gaze->isForSide($side) && $movement_for_gaze->gaze_type === $gaze_type) {
                return $movement_for_gaze;
            }
        }
    }

    public function getDisplay_with_correction()
    {
        return [
            static::WITH_CORRECTION => 'Glasses',
            static::WITHOUT_CORRECTION => 'No glasses'
        ][$this->with_correction] ?? '';
    }

    public function getDisplay_with_head_posture()
    {
        $display = $this->convertWithHeadPostureRecordToDisplay($this->with_head_posture);
        if ($display === null) {
            return "";
        }

        return $this->getAttributeLabel('with_head_posture') . ": {$display}";
    }

    public function getDisplay_wong_supine_positive()
    {
        return $this->wong_supine_positive ? $this->getAttributeLabel('wong_supine_positive') : '';
    }

    public function getDisplay_full_ocular_movement()
    {
        return $this->full_ocular_movement ? $this->getAttributeLabel('full_ocular_movement') : '';
    }

    public function getDisplay_hess_chart()
    {
        return $this->hess_chart ? $this->getAttributeLabel('hess_chart') : '';
    }

    public function getDisplay_comments()
    {
        if (!empty($this->comments)) {
            return "<span class=\"user-comment\">" . \OELinebreakReplacer::replace(\CHtml::encode($this->comments)) . "</span>";
        }
    }

    public function validateFullOcularMovement($attribute)
    {
        if (count($this->movements) > 0) {
            if ($this->$attribute) {
                $this->addError($attribute, $this->getAttributeLabel($attribute) . " cannot be set when ocular movements recorded.");
            }
        } else {
            if (!$this->$attribute) {
                $this->addError($attribute, $this->getAttributeLabel($attribute) . " must be set when no ocular movement recorded.");
            }
        }
    }

    public function __clone()
    {
        $this->unsetAttributes(['id', 'element_id']);

        foreach (['alignments', 'movements'] as $relation) {
            $this->$relation = array_map(fn($entry) => clone $entry, $this->$relation);
        }

        $this->setIsNewRecord(true);
    }
}
