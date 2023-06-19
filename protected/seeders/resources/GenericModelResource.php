<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OE\seeders\resources;

use CActiveRecord;

class GenericModelResource extends SeededResource
{
    private array $exclude_attributes = [];

    public static function from(CActiveRecord $instance): self
    {
        return new self($instance);
    }

    public function toArray(): array
    {
        return $this->modelToArray($this->instance, $this->exclude_attributes);
    }

    public function exclude(array $attributes = [])
    {
        $this->exclude_attributes = $attributes;

        return $this;
    }

    protected function modelsToArrays(array $instances, $exclude = [])
    {
        return array_map(function ($instance) use ($exclude) {
            return $this->modelToArray($instance, $exclude);
        }, $instances);
    }

    protected function modelToArray($instance, $exclude = [])
    {
        return array_merge(
            array_filter(
                $instance->getAttributes(),
                function ($key, $value) use ($exclude) {
                    return !in_array($key, $exclude);
                },
                ARRAY_FILTER_USE_BOTH
            ),
            $this->getRelationsForModel($instance, $exclude)
        );
    }

    protected function getRelationsForModel(CActiveRecord $instance, $exclude = [])
    {
        $relations = [];
        foreach ($instance->relations() as $relation => $definition) {
            if (in_array($relation, array_merge(['user', 'usermodified'], $exclude))) {
                continue;
            }
            if ($relation === 'event') {
                $relations[$relation] = SeededEventResource::from($instance->$relation)->inSummary()->toArray();
                continue;
            }
            if ($definition[0] === CActiveRecord::BELONGS_TO) {
                $instance->$relation ? ($instance->$relation instanceof \CModel && $relations[$relation] = $instance->$relation ? $instance->$relation->getAttributes() : $instance->$relation) : null;
            }
            if ($definition[0] === CActiveRecord::HAS_MANY) {
                $relations[$relation] = $this->modelsToArrays($instance->$relation, $exclude);
            }
        }
        return $relations;
    }
}
