<?php
/**
 * (C) Apperta Foundation, 2024
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2024, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class Element_OphTrOperationnote_GenericProcedureFactory extends FactoryForOperationnoteElement
{
    public function definition(): array
    {
        return array_merge(
            parent::definition(),
            [
                'proc_id' => Procedure::factory()->useExisting(),
                'comments' => ''
            ]
        );
    }

    public function forProcedure($procedure): self
    {
        return $this->state([
            'proc_id' => $procedure
        ]);
    }

    public static function generateFormData($model): array
    {
        // override because form doesn't follow standard convention of containing all
        // fields within the model name array key
        if (is_array($model)) {
            return array_merge_recursive(...array_map(fn ($i) => static::mapInstanceToFormData($i), $model));
        } else {
            return self::mapInstanceToFormData($model);
        }
    }

    public static function mapInstanceToFormData($instance): array
    {
        return [
            self::resolveModelFormFieldName($instance) => [
                $instance->proc_id => [
                    'id' => $instance->id,
                    'proc_id' => $instance->proc_id,
                    'comments' => $instance->comments
                ]
            ]
        ];
    }
}
