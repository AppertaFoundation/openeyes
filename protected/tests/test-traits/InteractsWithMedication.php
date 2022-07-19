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

 trait InteractsWithMedication
 {
    use WithFaker;

    public function generateSavedMedication(?array $attributes = []): Medication
    {
        $medication = new Medication();
        $medication->setAttributes($this->generateMedicationAttributes($attributes));
        $medication->save();

        return $medication;
    }

    public function generateMedicationAttributes(?array $attributes = []): array
    {
        return array_merge(
            [
                'source_type' => $this->faker->randomElement([Medication::SOURCE_TYPE_DMD, Medication::SOURCE_TYPE_LOCAL]),
                'preferred_term' => $this->faker->words(2, true),
                'preferred_code' => $this->faker->regexify('\w\w\w')
            ],
            $attributes
        );
    }

    public function createLocalMedication(?array $attributes = []): Medication
    {
        return $this->generateSavedMedication(array_merge($attributes, ['source_type' => Medication::SOURCE_TYPE_LOCAL]));
    }

    public function createDMDMedication(?array $attributes = []): Medication
    {
        return $this->generateSavedMedication(array_merge($attributes, ['source_type' => Medication::SOURCE_TYPE_DMD]));
    }
 }