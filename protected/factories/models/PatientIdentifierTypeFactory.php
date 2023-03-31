<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OE\factories\models;

use Institution;
use OE\factories\ModelFactory;
use PatientIdentifierType;
use Site;

/**
 * class PatientIdentifierTypeFactory
 *
 * This factory provides interface for retrieving existing instances via the useExisting pattern
 */
class PatientIdentifierTypeFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'usage_type' => $this->faker->randomElement([PatientIdentifierType::LOCAL_USAGE_TYPE, PatientIdentifierType::GLOBAL_USAGE_TYPE]),
            'short_title' => $this->faker->words(3, true),
            'long_title' => $this->faker->words(5, true),
            'institution_id' => Institution::factory()->useExisting(),
            'site_id' => function ($attributes) {
                return Site::factory()->useExisting(['institution_id' => $attributes['institution_id']]);
            },
            'unique_row_string' => $this->faker->words(5, true),
            'validate_regex' => '/^\d*$/'
        ];
    }

    public function local(): self
    {
        return $this->state([
            'usage_type' => PatientIdentifierType::LOCAL_USAGE_TYPE
        ]);
    }
}
