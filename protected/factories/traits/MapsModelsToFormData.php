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

use BaseEventTypeElement;
use CModel;
use OE\factories\exceptions\FormMappingNotImplementedException;

/**
 * This trait sets up the method necessary for mapping models to form arrays.
 *
 * Initially this was set up to work directly with the factory, but have since
 * introduced static accessors to support calling the factory with already
 * existing models. The instance method pattern remains supported for backwards
 * compatibility.
 */
trait MapsModelsToFormData
{
    public static function generateFormData(CModel|array $model)
    {
        if (is_array($model) && empty($model)) {
            return [];
        }

        $form_data = is_array($model)
            ? array_map(fn ($instance) => static::mapInstanceToFormData($instance), $model)
            : static::mapInstanceToFormData($model);

        $form_field_key = static::resolveModelFormFieldName($model);

        return $form_field_key
            ? [$form_field_key => $form_data]
            : $form_data;
    }

    /**
     * This method should be overriden in factories to define the form field
     * mappings from a model instance.
     *
     * @param CModel $instance
     * @return array
     */
    public static function mapInstanceToFormData(\CModel $instance): array
    {
        throw new FormMappingNotImplementedException(static::class);
    }

    protected static function formFieldName(\CModel $model): ?string
    {
        if ($model instanceof BaseEventTypeElement) {
            return \CHtml::modelName($model);
        }

        return null;
    }

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
     * Make the model instance(s) for the factory, and return those along
     * with the default array representation for form use.
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
     * @param array|\CModel $elements
     * @return ?string
     */
    protected static function resolveModelFormFieldName($models): ?string
    {
        if ($models instanceof \CModel) {
            return self::formFieldName($models);
        } else {
            return self::formFieldName($models[0]);
        }
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

        $field_name = static::resolveModelFormFieldName($results);

        return $field_name
            ? [$field_name => $this->mapModelsToFormData($results)]
            : $this->mapModelsToFormData($results);
    }



    protected function mapModelsToFormData($models): array
    {
        if ($models instanceof \CModel) {
            return $this->mapModelToFormData($models);
        } else {
            return array_map([$this, 'mapModelToFormData'], $models);
        }
    }

    /**
     * This was the original method to be overridden for defining form field
     * mapping, but has been superseded by the static method. Remains in
     * place for backwards compatibility.
     *
     * @param $model
     * @return array
     */
    protected function mapModelToFormData($model): array
    {
        return static::mapInstanceToFormData($model);
    }
}
