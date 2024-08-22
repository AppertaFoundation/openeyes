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

namespace OE\factories\models;

use OE\factories\ModelFactory;

use MedicationSet;
use Site;
use Subspecialty;

class MedicationUsageCodeFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'usage_code' => $this->faker->lexify('?????-?????'),
            'name' => $this->faker->word(2)
        ];
    }

    /**
     * @param string $usage_code
     * @return MedicationUsageCodeFactory
     */
    public function usageCode(string $usage_code): self
    {
        return $this->state([
            'usage_code' => $usage_code
        ]);
    }

    /**
     * @param string $name
     * @return MedicationUsageCodeFactory
     */
    public function name(string $name): self
    {
        return $this->state([
            'name' => $name
        ]);
    }

    /**
     * @return MedicationUsageCodeFactory
     */
    public function inactive(): self
    {
        return $this->state([
            'active' => false
        ]);
    }

    /**
     * @return MedicationUsageCodeFactory
     */
    public function hidden(): self
    {
        return $this->state([
            'hidden' => true
        ]);
    }
}
