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

use OE\factories\models\traits\HasFactory;
use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\traits\HasRelationOptions;
use OEModule\OphCiExamination\models\traits\HasSidedData;
use OEModule\OphCiExamination\widgets\Retinoscopy as RetinoscopyWidget;

class Retinoscopy extends \BaseEventTypeElement implements SidedData
{
    use traits\CustomOrdering;
    use HasFactory;
    use HasSidedData;
    use HasRelationOptions;

    protected $widgetClass = RetinoscopyWidget::class;
    protected $auto_validate_relations = true;
    protected $auto_update_relations = true;

    public const DILATED_DISPLAY = 'Dilated';
    public const NOT_DILATED_DISPLAY = 'Not dilated';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_retinoscopy';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                'event_id, eye_id, right_dilated, right_working_distance_id, right_angle, right_power1, right_power2, ' .
                'right_refraction, right_comments, right_eyedraw, left_dilated, left_working_distance_id, left_angle, left_power1, ' .
                'left_power2, left_refraction, left_comments, left_eyedraw', 'safe'],
            [
                'right_dilated, right_working_distance_id, right_angle, right_power1, right_power2, right_refraction, right_eyedraw',
                'requiredIfSide',
                'side' => 'right'
            ],
            [
                'left_dilated, left_working_distance_id, left_angle, left_power1, left_power2, left_refraction, left_eyedraw',
                'requiredIfSide',
                'side' => 'left'
            ],
            ['right_dilated', 'boolean'],
            ['left_dilated', 'boolean'],
            [
                'right_working_distance_id, left_working_distance_id',
                'exist',
                'allowEmpty' => true,
                'attributeName' => 'id',
                'className' => Retinoscopy_WorkingDistance::class,
                'message' => '{attribute} is invalid'
            ],
            [
                'right_angle, left_angle',
                'numerical',
                'integerOnly' => true,
                'min' => '0',
                'max' => '180'
            ],
            [
                'right_power1, right_power2, left_power1, left_power2',
                'numerical',
                'min' => '-30',
                'max' => '30'
            ],
        ];
    }

    /**
     * @return array
     */
    public function relations()
    {
        return [
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'right_working_distance' => [
                self::BELONGS_TO,
                Retinoscopy_WorkingDistance::class,
                'right_working_distance_id'
            ],
            'left_working_distance' => [
                self::BELONGS_TO,
                Retinoscopy_WorkingDistance::class,
                'left_working_distance_id'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'right_dilated' => 'Dilated',
            'right_working_distance_id' => 'Working',
            'right_angle' => 'Angle',
            'right_power1' => 'P1',
            'right_power2' => 'P2',
            'right_refraction' => 'Refraction',
            'right_comments' => 'Comments',
            'left_dilated' => 'Dilated',
            'left_working_distance_id' => 'Working',
            'left_angle' => 'Angle',
            'left_power1' => 'P1',
            'left_power2' => 'P2',
            'left_refraction' => 'Refraction',
            'left_comments' => 'Comments'
        ];
    }

    public function sidedFields(?string $side = null): array
    {
        // ignore side attribute as no distinction is made
        return [
            'dilated',
            'working_distance_id',
            'angle',
            'power1',
            'power2',
            'refraction',
            'eyedraw',
            'comments'
        ];
    }

    public function sidedDefaults(): array
    {
        return [
            'angle' => 0,
            'power1' => 0,
            'power2' => 0
        ];
    }

    /**
     * @return string
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public function getLetter_string()
    {
        return sprintf(
            "Retinoscopy: R: %s, L: %s",
            $this->letterStringForSide('right'),
            $this->letterStringForSide('left')
        );
    }

    protected function letterStringForSide($side)
    {
        if (!$this->hasEye($side)) {
            return "NR";
        }
        return sprintf(
            "%s%s%s",
            $this->{"{$side}_refraction"},
            $this->{"{$side}_dilated"} ? " dilated" : "",
            $this->{"{$side}_comments"} ? " - " . $this->{"{$side}_comments"} : ""
        );
    }

    public function getDisplay_right_dilated()
    {
        return $this->displayDilated($this->right_dilated);
    }

    public function getDisplay_left_dilated()
    {
        return $this->displayDilated($this->left_dilated);
    }

    public function getDisplay_right_power1()
    {
        return $this->displayPower($this->right_power1);
    }

    public function getDisplay_left_power1()
    {
        return $this->displayPower($this->left_power1);
    }

    public function getDisplay_right_power2()
    {
        return $this->displayPower($this->right_power2);
    }

    public function getDisplay_left_power2()
    {
        return $this->displayPower($this->left_power2);
    }

    protected function displayDilated($dilated)
    {
        if ($dilated === null) {
            return "";
        }

        return $dilated ? static::DILATED_DISPLAY : static::NOT_DILATED_DISPLAY;
    }

    protected function displayPower($power)
    {
        if ($power === null) {
            return "";
        }

        return sprintf("%+.2f", $power);
    }
}
