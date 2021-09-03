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

class OERequiredTogetherValidator extends BaseOEValidator
{
    public $attributes = [];
    public $message = "{attribute} is required with {other_attributes}";

    public function validate($object, $attributes = null)
    {
        if (!$this->attributesAreRelevantForThisValidator($attributes)) {
            return;
        }

        $set_attributes = array_filter(
            $this->attributes,
            function($attr) use ($object) {
                return $this->objectAttributeIsSet($object, $attr);
            }
        );

        if (count($set_attributes) === 0 || count($set_attributes) === count($this->attributes)) {
            return;
        }

        $this->addErrorsForMissingAttributes(
            $object,
            $attributes === null ? $this->attributes : array_intersect($this->attributes, $attributes),
            $set_attributes);
    }

    /**
     * @inheritDoc
     */
    public function validateAttribute($model, $attribute)
    {
        throw new RuntimeException(static::class . " cannot validate a single attribute");
    }

    /**
     *
     * @param $object
     * @param $attributes
     * @param $set_attributes
     */
    protected function addErrorsForMissingAttributes($object, $attributes, $set_attributes)
    {
        $attributes ??= $this->attributes;
        array_map(
            function ($attr) use ($object, $attributes) {
                $this->addError(
                    $object,
                    $attr,
                    $this->message,
                    ['{other_attributes}' => $this->getOtherAttributesText($object, $attr, $attributes)]
                );
            },
            array_diff($attributes, $set_attributes)
        );
    }

    private function getOtherAttributesText($object, $attr, $all_attrs)
    {
        return implode(
            ",",
            array_map(
                function($attr) use ($object) {
                    return $object->getAttributeLabel($attr);
                },
                array_diff($all_attrs, [$attr])
            )
        );
    }
}
