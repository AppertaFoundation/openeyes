<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class SplitEventTypeElement extends BaseEventTypeElement
{
    // these are legacy and should be removed one switch to using the constants on the Eye model
    public const LEFT = Eye::LEFT;
    public const RIGHT = Eye::RIGHT;
    public const BOTH = Eye::BOTH;

    public function hasLeft()
    {
        return $this->eye && $this->eye->id != Eye::RIGHT;
    }

    public function hasRight()
    {
        return $this->eye && $this->eye->id != Eye::LEFT;
    }

    /**
     * Returns a value indicating whether this event has the eye of the given side
     *
     * @param string $side The side of the eye to test for (either left or right)
     * @return bool True if this event has the eye of the given side
     * @throws InvalidArgumentException Thrown if the given eye is not valid
     */
    public function hasEye($side)
    {
        switch ($side) {
            case 'left':
                return $this->hasLeft();
            case 'right':
                return $this->hasRight();
            default:
                throw new InvalidArgumentException('Side must be either "left" or "right"');
        }
    }

    public static function eyeHasSide($side, $eye_id)
    {
        if ($side === 'left') {
            return $eye_id != Eye::RIGHT;
        } elseif ($side === 'right') {
            return $eye_id != Eye::LEFT;
        } else {
            return false;
        }
    }

    /**
     * An array of field suffixes that we should treat as "sided".
     * e.g. 'example' would indicate 'left_example' and 'right_example'.
     *
     * @return array:
     */
    public function sidedFields()
    {
        return array();
    }

    /**
     * An associative array of field suffixes and their default values.
     * Used for initialising sided fields.
     *
     * @return array
     */
    public function sidedDefaults()
    {
        return array();
    }

    protected function beforeSave()
    {
        // Need to clear any "sided" fields if that side isn't active
        if ($this->eye->id != Eye::BOTH) {
            foreach ($this->sidedFields() as $field_suffix) {
                if ($this->eye->id == Eye::LEFT) {
                    $this->{'right_' . $field_suffix} = null;
                } else {
                    $this->{'left_' . $field_suffix} = null;
                }
            }
        }

        return parent::beforeSave();
    }

    /**
     * Sided fields have the same defaults on left and right.
     */
    public function setDefaultOptions(Patient $patient = null)
    {
        parent::setDefaultOptions($patient);

        $this->setSideDefaultOptions('left');
        $this->setSideDefaultOptions('right');
    }

    protected function setSideDefaultOptions($side)
    {
        foreach ($this->sidedDefaults() as $field => $default) {
            $this->{$side . '_' . $field} = $default;
        }
    }

    protected function getSideDefaultOptions($side)
    {
        $fields = array();
        foreach ($this->sidedDefaults() as $field => $default) {
            $fields["{$side}_{$field}"] = $default;
        }
        return $fields;
    }

    /**
     * Used to initialise the missing side in an update form.
     */
    public function setUpdateOptions()
    {
        if ($this->eye->id == Eye::LEFT) {
            $this->setSideDefaultOptions('right');
        } elseif ($this->eye->id == Eye::RIGHT) {
            $this->setSideDefaultOptions('left');
        }
    }

    public function getUpdateOptions($context): array
    {
        if ($this->eye->id == Eye::LEFT) {
            $fields = $this->getSideDefaultOptions('right');
        } elseif ($this->eye->id == Eye::RIGHT) {
            $fields = $this->getSideDefaultOptions('left');
        }
        return $fields;
    }

    /**
     * Check numeric values within min and max range for the selected eye.
     *
     * @param $attribute
     * @param $params
     */
    public function checkNumericRangeIfSide($attribute, $params)
    {
        if (($params['side'] == 'left' && $this->eye_id != 2) || ($params['side'] == 'right' && $this->eye_id != 1)) {
            if ($this->$attribute != null) {
                if ($this->$attribute < $params['min']) {
                    if (!@$params['message']) {
                        $params['message'] = ucfirst($params['side']) . ' {attribute} is too small (need to be more than ' . $params['min'] . ').';
                    }
                    $params['{attribute}'] = $this->getAttributeLabel($attribute);
                } elseif ($this->$attribute > $params['max']) {
                    if (!@$params['message']) {
                        $params['message'] = ucfirst($params['side']) . ' {attribute} is too big (need to be less than ' . $params['max'] . ').';
                    }
                    $params['{attribute}'] = $this->getAttributeLabel($attribute);
                }
                if (isset($params['message'])) {
                    $this->addError($attribute, strtr($params['message'], $params));
                }
            }
        }
    }
}
