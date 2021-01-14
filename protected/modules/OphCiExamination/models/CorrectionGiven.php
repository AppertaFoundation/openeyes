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
use OEModule\OphCiExamination\models\traits\HasSidedData;
use OEModule\OphCiExamination\widgets\CorrectionGiven as CorrectionGivenWidget;

class CorrectionGiven extends \BaseEventTypeElement implements SidedData
{
    use traits\CustomOrdering;
    use HasSidedData;

    public const SOURCE_ELEMENT_TYPES = [Retinoscopy::class, Element_OphCiExamination_Refraction::class];
    public const ORDER_AS_FOUND_LABEL = "Order as Found";
    public const ORDER_AS_ADJUSTED_LABEL = "Order as Adjusted";

    protected $widgetClass = CorrectionGivenWidget::class;
    protected static $cached_as_found_element_types;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_correction_given';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                'event_id, eye_id, right_as_found, right_as_found_element_type_id, right_refraction, ' .
                'left_as_found, left_as_found_element_type_id, left_refraction', 'safe',
            ],
            ['right_as_found, left_as_found', 'boolean'],
            ['right_as_found, right_refraction', 'requiredIfSide', 'side' => 'right'],
            ['left_as_found, left_refraction', 'requiredIfSide', 'side' => 'left'],
            ['right_as_found_element_type_id', 'validateAsFoundElementTypeId', 'side' => 'right'],
            ['left_as_found_element_type_id', 'validateAsFoundElementTypeId', 'side' => 'left']
        ];
    }

    /**
     * @return array
     */
    public function relations()
    {
        return [
            'event' => [self::BELONGS_TO, \Event::class, 'event_id'],
            'user' => [self::BELONGS_TO, \User::class, 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
            'right_as_found_element_type' => [self::BELONGS_TO, \ElementType::class, 'right_as_found_element_type_id'],
            'left_as_found_element_type' => [self::BELONGS_TO, \ElementType::class, 'left_as_found_element_type_id']
        ];
    }

    public function sidedFields(?string $side = null): array
    {
        return ['as_found', 'as_found_element_type_id', 'refraction'];
    }

    public function sidedDefaults(): array
    {
        return [];
    }

    public function validateAsFoundElementTypeId($attribute, $params)
    {
        if (!$this->{"has" . ucfirst($params['side'])}() || !(bool) $this->{$params['side'] . "_as_found"}) {
            return;
        }

        if (!$this->$attribute) {
            $this->addError($attribute, "{attribute} cannot be blank");
            return;
        }

        if (!$this->isValidAsFoundElementTypeId($this->$attribute)) {
            $this->addError($attribute, '{attribute} is invalid');
        }
    }

    /**
     * @return string
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
    public function getLetter_string()
    {
        return sprintf(
            "Correction Given: R: %s, L: %s",
            $this->letterStringForSide('right'),
            $this->letterStringForSide('left')
        );
    }

    public function getOrderLabelForSide($side)
    {
        if (!$this->hasEye($side)) {
            return;
        }

        if ($this->{"{$side}_as_found"}) {
            return sprintf(
                "%s (%s)",
                static::ORDER_AS_FOUND_LABEL,
                strtolower($this->{"{$side}_as_found_element_type"}->name)
            );
        }

        return static::ORDER_AS_ADJUSTED_LABEL;
    }

    /**
     * @param $side
     * @return string
     */
    protected function letterStringForSide($side)
    {
        if (!$this->hasEye($side)) {
            return "NR";
        }

        return sprintf(
            "%s (%s)",
            $this->{"{$side}_refraction"},
            $this->{"{$side}_as_found"} ?
                strtolower($this->{"{$side}_as_found_element_type"}->name) :
                "adjusted"
        );
    }

    public function getAs_found_element_type_options()
    {
        if (!static::$cached_as_found_element_types) {
            $criteria = new \CDbCriteria();
            $criteria->addInCondition('class_name', static::SOURCE_ELEMENT_TYPES);
            static::$cached_as_found_element_types = \ElementType::model()->findAll($criteria);
        }

        return static::$cached_as_found_element_types;
    }

    protected function isValidAsFoundElementTypeId($element_type_id)
    {
        return in_array(
            $element_type_id,
            array_map(
                function ($et) {
                    return $et->id;
                },
                $this->as_found_element_type_options
            )
        );
    }
}
