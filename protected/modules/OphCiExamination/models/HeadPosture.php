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

use OEModule\OphCiExamination\widgets\HeadPosture as HeadPostureWidget;

/**
 * Class HeadPosture
 *
 * @package OEModule\OphCiExamination\models
 * @property $tilt
 * @property array $tilt_options
 * @property $turn
 * @property array $turn_options
 * @property $chin
 * @property array $chin_options
 */
class HeadPosture extends \BaseEventTypeElement
{
    use traits\CustomOrdering;

    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    protected $widgetClass = HeadPostureWidget::class;

    public const RIGHT = 'right';
    public const RIGHT_DISPLAY = 'Right';
    public const LEFT = 'left';
    public const LEFT_DISPLAY = 'Left';

    public const DEPRESSED = 'depressed';
    public const DEPRESSED_DISPLAY = 'Depressed';
    public const ELEVATED = 'elevated';
    public const ELEVATED_DISPLAY = 'Elevated';

    public const at_least_one_required_attributes = ['turn', 'tilt', 'chin', 'comments'];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_headposture';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['event_id, tilt, turn, chin, comments', 'safe'],
            // The following rule is used by search().
            [
                'tilt, turn', 'in',
                'range' => [self::RIGHT, self::LEFT],
                'message' => '{attribute} is invalid'
            ],
            [
                'chin', 'in',
                'range' => [self::ELEVATED, self::DEPRESSED],
                'message' => '{attribute} is invalid'
            ],
            // Please remove those attributes that should not be searched.
            ['id, event_id, comments, at_risk',  'safe', 'on' => 'search']
        ];
    }

    /**
     * @return array
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
        ];
    }

    public function afterValidate()
    {
        $at_least_one_required_is_set = false;
        foreach (self::at_least_one_required_attributes as $attr) {
            if ($this->$attr !== null && $this->$attr !== '') {
                $at_least_one_required_is_set = true;
                break;
            }
        }

        if (!$at_least_one_required_is_set) {
            $this->addError('comments', 'At least one attribute must be recorded');
        }

        return parent::afterValidate();
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [];
    }

    public function getLetter_string()
    {
        $result = [];
        foreach (['tilt', 'turn', 'chin'] as $attr) {
            if ($this->$attr) {
                $result[] = $this->getAttributeLabel($attr) . ": " . $this->{"display_$attr"};
            }
        }
        $letter_string = implode(", ", $result);
        if (strlen($this->comments)) {
            $letter_string .= strlen($letter_string) ? " - " . $this->comments : $this->comments;
        }

        return $letter_string;
    }

    public function getTilt_options()
    {
        return [
            ['id' => self::RIGHT, 'name' => self::RIGHT_DISPLAY],
            ['id' => self::LEFT, 'name' => self::LEFT_DISPLAY],
        ];
    }

    public function getTurn_options()
    {
        return [
            ['id' => self::RIGHT, 'name' => self::RIGHT_DISPLAY],
            ['id' => self::LEFT, 'name' => self::LEFT_DISPLAY],
        ];
    }

    public function getChin_options()
    {
        return [
            ['id' => self::ELEVATED, 'name' => self::ELEVATED_DISPLAY],
            ['id' => self::DEPRESSED, 'name' => self::DEPRESSED_DISPLAY],
        ];
    }

    public function getDisplay_turn()
    {
        return [
                self::LEFT => self::LEFT_DISPLAY,
                self::RIGHT => self::RIGHT_DISPLAY
        ][$this->turn] ?? '-';
    }

    public function getDisplay_tilt()
    {
        return [
            self::LEFT => self::LEFT_DISPLAY,
            self::RIGHT => self::RIGHT_DISPLAY
        ][$this->tilt] ?? '-';
    }

    public function getDisplay_chin()
    {
        return [
                self::ELEVATED => self::ELEVATED_DISPLAY,
                self::DEPRESSED => self::DEPRESSED_DISPLAY
            ][$this->chin] ?? '-';
    }
}
