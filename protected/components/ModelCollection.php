<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class ModelCollection
{
    private array $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Retrieves all of the values for a given attribute
     *
     * @param string $attribute
     * @return array
     * @throws Exception
     */
    public function pluck(string $attribute): array
    {
        return array_map(function($model) use ($attribute) {

            if (!$model->hasAttribute($attribute) && !$model->getMetaData()->hasRelation($attribute)) {
                throw new Exception("No '$attribute' attribute or relations does exist on '" . get_class($model) . "' model");
            }

            return $model->$attribute;
        }, $this->data);
    }

    /**
     * Gets the max value of a given key.
     *
     * @param string $attribute
     * @return mixed|null
     * @throws Exception
     */
    public function max(string $attribute)
    {
        $max = null;
        foreach ($this->data as $model) {
            if (!$model->hasAttribute($attribute)) {
                throw new Exception("The '$attribute' does not exist on '" . get_class($model) . "' model");
            }

            $max = ($max < $model->$attribute) ? $model->$attribute : $max;
        }

        return $max;
    }

    /**
     * Returns an array containing all the entries from $this->data that are not present in any of the parameter arrays
     * (compared by id)
     *
     * @param array $ids
     * @return array
     */
    public function diff(array $ids): array
    {
        return array_filter($this->data, function($model) use ($ids) {
            return !in_array($model->id, $ids);
        });
    }

    /**
     * Returns the models' attributes as an array
     * @return array
     */
    public function toArray(): array
    {
        return array_map(fn($m) => $m->attributes, $this->data);
    }
}
