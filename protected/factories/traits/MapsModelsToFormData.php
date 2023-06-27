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

namespace OE\factories\traits;

use OE\factories\exceptions\FormMappingNotImplementedException;

trait MapsModelsToFormData
{
    /**
     * @param array
     * @return array
     */
    public function makeAsFormData(array $attributes = [])
    {
        $results = $this->make($attributes);

        return $this->mapToFormData($results);
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return array<model(s), form_data>
     */
    public function makeWithFormData(array $attributes = []): array
    {
        $results = $this->make($attributes);

        return [$results, $this->mapToFormData($results)];
    }

    /**
     * @param array
     * @return array<model(s), form_data>
     */
    public function createWithFormData(array $attributes = [])
    {
        $results = $this->create($attributes);

        return [$results, $this->mapToFormData($results)];
    }

    /**
     * @param array|\CModel $results
     * @return array
     */
    protected function mapToFormData($results): array
    {
        if (!is_array($results) && !$results instanceof \CModel) {
            throw new \InvalidArgumentException('Cannot map non model data to form data');
        }

        $field_name = $this->resolveModelFormFieldName($results);

        return [
            $field_name => $this->mapModelsToFormData($results)
        ];
    }

    /**
     * @param array|\CModel $elements
     * @return string
     */
    protected function resolveModelFormFieldName($models): string
    {
        if ($models instanceof \CModel) {
            return \CHtml::modelName($models);
        } else {
            return \CHtml::modelName($models[0]);
        }
    }

    protected function mapModelsToFormData($models): array
    {
        if ($models instanceof \CModel) {
            return $this->mapModelToFormData($models);
        } else {
            return array_map([$this, 'mapModelToFormData'], $models);
        }
    }

    protected function mapModelToFormData($model): array
    {
        throw new FormMappingNotImplementedException(get_class($this));
    }
}
