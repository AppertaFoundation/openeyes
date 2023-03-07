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

use OE\factories\ModelFactory;

class TrialPatientFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'trial_id' => Trial::factory(),
            'external_trial_identifier' => $this->faker->firstName(),
            'patient_id' => Patient::factory(),
            'treatment_type_id' => TreatmentType::factory()->useExisting([
                'code' => $this->faker->randomElement([TreatmentType::UNKNOWN_CODE, TreatmentType::INTERVENTION_CODE, TreatmentType::PLACEBO_CODE])
            ]),
            'status_id' => TrialPatientStatus::factory()->useExisting([
                'code' => $this->faker->randomElement([TrialPatientStatus::SHORTLISTED_CODE, TrialPatientStatus::ACCEPTED_CODE, TrialPatientStatus::REJECTED_CODE])
            ])
        ];
    }
}
