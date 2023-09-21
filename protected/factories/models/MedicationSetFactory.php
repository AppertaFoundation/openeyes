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

class MedicationSetFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(3)
        ];
    }

    /**
     * @param string $name
     * @return MedicationSetFactory
     */
    public function name(string $name): self
    {
        return $this->state([
            'name' => $name
        ]);
    }

    /**
     * @param MedicationSet|MedicationSetFactory|string|int|null $antecedent
     * @return MedicationSetFactory
     */
    public function forAntecedent($antecedent = null): self
    {
        $antecedent ??= MedicationSet::factory();

        return $this->state([
            'antecedent_medication_set_id' => $antecedent
        ]);
    }

    /**
     * @param Date|DateTime|string $date
     * @return MedicationSetFactory
     */
    public function deletedAt($date): self
    {
        return $this->state([
            'deleted_date' => $date
        ]);
    }

    /**
     * @return MedicationSetFactory
     */
    public function hidden(): self
    {
        return $this->state([
            'hidden' => true
        ]);
    }

    /**
     * @return MedicationSetFactory
     */
    public function automatic(): self
    {
        return $this->state([
            'automatic' => true
        ]);
    }
}
