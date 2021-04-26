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

use OEModule\OphCiExamination\widgets\RedReflex as RedReflexWidget;

/**
 * Class RedReflex
 *
 * @package OEModule\OphCiExamination\models
 * @property int $id
 * @property int $event_id
 * @property int $eye_id
 * @property $right_has_red_reflex
 * @property $left_has_red_reflex
 * @property $letter_string
 */
class RedReflex extends \BaseEventTypeElement implements interfaces\SidedData
{
    use traits\HasSidedData;
    use traits\CustomOrdering;

    public const HAS_RED_REFLEX = '1';
    public const NO_RED_REFLEX = '0';

    protected $widgetClass = RedReflexWidget::class;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_red_reflex';
    }

    public function rules()
    {
        return [
            ['event_id, eye_id, right_has_red_reflex, left_has_red_reflex', 'safe'],
            [
                'right_has_red_reflex, left_has_red_reflex', 'in',
                'range' => [self::HAS_RED_REFLEX, self::NO_RED_REFLEX],
                'message' => '{attribute} is invalid'
            ]
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

        ];
    }

    public function attributeLabels()
    {
        return [
            'right_has_red_reflex' => 'Has red reflex',
            'left_has_red_reflex' => 'Has red reflex'
        ];
    }

    /**
     * @inheritDoc
     */
    public function sidedFields(?string $side = null): array
    {
        return [
            'has_red_reflex'
        ];
    }

    /**
     * @inheritDoc
     */
    public function sidedDefaults(): array
    {
        return [];
    }

    public function getLetter_string()
    {
        $result = $this->getElementTypeName() . ":";
        if ($this->hasRight()) {
            $result .= " R: " . ($this->right_has_red_reflex ? "Y" : "N");
        }
        if ($this->hasLeft()) {
            $result .= " L: " . ($this->left_has_red_reflex ? "Y" : "N");
        }

        return $result;
    }
}
