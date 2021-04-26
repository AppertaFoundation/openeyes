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

namespace OEModule\OphCiExamination\models\traits;

use OEModule\OphCiExamination\models\interfaces\SidedData;
use Patient;

/**
 * Trait HasSidedData
 *
 * Provides functionality for elements supporting left and right records.
 * Use in conjunction with SidedData Interface.
 *
 * @package OEModule\OphCiExamination\models\traits
 */
trait HasSidedData
{
    public function sideStrings()
    {
        return ['left', 'right'];
    }

    public static function isValueForLeft($value): bool
    {
        return ((int)$value & SidedData::LEFT) === SidedData::LEFT;
    }

    public static function isValueForRight($value): bool
    {
        return ((int)$value & SidedData::RIGHT) === SidedData::RIGHT;
    }

    public function hasLeft()
    {
        return $this->eye_id && self::isValueForLeft($this->eye_id);
    }

    public function hasRight()
    {
        return $this->eye_id && self::isValueForRight($this->eye_id);
    }

    public function setHasRight()
    {
        $this->eye_id |= SidedData::RIGHT;
    }

    public function setDoesNotHaveRight()
    {
        if ($this->hasRight()) {
            $this->eye_id ^= SidedData::RIGHT;
        }
    }

    public function setDoesNotHaveLeft()
    {
        if ($this->hasLeft()) {
            $this->eye_id ^= SidedData::LEFT;
        }
    }

    public function setHasLeft()
    {
        $this->eye_id |= SidedData::LEFT;
    }

    public function hasEye($side)
    {
        if (in_array($side, $this->sideStrings())) {
            return $this->{"has" . ucfirst($side)}();
        }
        throw new InvalidArgumentException('Side must be either ' . implode(', ', $this->sideStrings()));
    }

    public function sideAttributeValidation($attribute, $params)
    {
        if ((int)$this->$attribute < 1 || (int)$this->$attribute > 3) {
            $this->addError($attribute, $params['message'] ?? '{attribute} is invalid.');
        }
    }

    /**
     * Sided fields have the same defaults on left and right.
     *
     * @param Patient|null $patient
     */
    public function setDefaultOptions(Patient $patient = null)
    {
        parent::setDefaultOptions($patient);

        foreach ($this->sideStrings() as $side) {
            $this->setSideDefaultOptions($side);
        }
    }

    protected function beforeSave()
    {
        // Need to clear any "sided" fields if that side isn't active
        foreach ($this->sideStrings() as $side) {
            if (!$this->hasEye($side)) {
                foreach ($this->sidedFields($side) as $field) {
                    $this->{"{$side}_{$field}"} = null;
                }
            }
        }

        return parent::beforeSave();
    }

    /**
     * @param $side
     */
    protected function setSideDefaultOptions($side)
    {
        foreach ($this->sidedDefaults() as $field => $default) {
            $this->{"{$side}_{$field}"} = $default;
        }
    }

    /**
     * Used to initialise missing sides in an update form.
     */
    public function setUpdateOptions()
    {
        foreach ($this->sideStrings() as $side) {
            if (!$this->hasEye($side)) {
                $this->setSideDefaultOptions($side);
            }
        }
    }

    /**
     * Check numeric values within min and max range for the selected eye.
     *
     * @param $attribute
     * @param $params
     */
    public function checkNumericRangeIfSide($attribute, $params)
    {
        if ($this->hasEye($params['side'])) {
            if ($this->$attribute !== null && $this->$attribute !== '') {
                $message = null;
                if ($this->$attribute < $params['min']) {
                    $message = $params['message'] ?? ucfirst($params['side']).' {attribute} is too small (need to be more than {min}).';
                } elseif ($this->$attribute > $params['max']) {
                    $message = $params['message'] ?? ucfirst($params['side']).' {attribute} is too big (need to be less than {max}).';
                }
                if ($message !== null) {
                    $this->addError($attribute, strtr($message, $params));
                }
            }
        }
    }
}
