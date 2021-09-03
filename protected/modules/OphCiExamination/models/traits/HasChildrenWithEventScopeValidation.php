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

/**
 * Trait HasChildrenWithEventScopeValidation
 *
 * Allows validation of related properties dependent on event level conditions
 *
 * @package OEModule\OphCiExamination\models\traits
 */
trait HasChildrenWithEventScopeValidation
{
    protected static array $formatted_event_scoped_children;

    /**
     * Calls the event scoped validation method on the specified child relations, with the elements of
     * the event being validated
     *
     * @param $elements
     */
    public function eventScopeValidation($elements)
    {
        foreach (static::getEventScopedChildrenProperties() as $child_relation => $properties) {
            foreach ($this->$child_relation as $i => $child) {
                $original_errors = static::getModelErrorsForProperties($child, $properties);
                $child->eventScopeValidation($elements);
                $errors_after_event_validation = static::getModelErrorsForProperties($child, $properties);
                $this->addNewErrorsForProperty("{$child_relation}.{$i}", $original_errors, $errors_after_event_validation);
            }
        }
    }

    protected static function getEventScopedChildrenProperties()
    {
        if (!isset(static::$formatted_event_scoped_children)) {
            if (!defined(self::class . '::EVENT_SCOPED_CHILDREN') || !is_array(self::EVENT_SCOPED_CHILDREN)) {
                throw new \RuntimeException("class constant 'EVENT_SCOPED_CHILDREN' must be defined as an array");
            }
            static::$formatted_event_scoped_children = [];
            foreach (self::EVENT_SCOPED_CHILDREN as $relation => $properties) {
                static::$formatted_event_scoped_children[$relation] = is_array($properties) ? $properties : [$properties];
            }
        }

        return static::$formatted_event_scoped_children;
    }

    /**
     * @param $model
     * @param $properties
     * @return mixed|null
     */
    protected static function getModelErrorsForProperties($model, $properties)
    {
        return array_reduce(
            $properties,
            function ($errors, $property) use ($model) {
                $errors[$property] = $model->getErrors($property);
                return $errors;
            },
            []
        );
    }

    /**
     * @param $property
     * @param $original
     * @param $new
     */
    protected function addNewErrorsForProperty($property, $original, $new)
    {
        foreach (array_keys($new) as $err_key) {
            $new_errors = array_diff($new[$err_key], $original[$err_key]);
            foreach ($new_errors as $err) {
                $this->addError("{$property}", $err);
            }
        }
    }
}