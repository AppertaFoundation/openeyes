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
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
namespace OEModule\OphDrPGDPSD\factories\models;

use Institution;
use OE\factories\ModelFactory;
use Patient;
use WorklistPatient;

use OEModule\OphDrPGDPSD\models\{
    OphDrPGDPSD_PGDPSD,
    OphDrPGDPSD_Assignment
};

class OphDrPGDPSD_AssignmentFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'visit_id' => function ($attributes) {
                return WorklistPatient::factory()->state([
                    'patient_id' => $attributes['patient_id']
                ]);
            },
            'institution_id' => Institution::factory()->useExisting(),
        ];
    }

    /**
     * @param Institution|InstitutionFactory|string|int|null $institution
     * @return OphDrPGDPSD_AssignmentFactory
     */
    public function forInstitution($institution = null): self
    {
        $institution ??= ModelFactory::factoryFor(Institution::class);

        return $this->state([
            'institution_id' => $institution,
        ]);
    }

    /**
     * @param Patient|PatientFactory|string|int|null $patient
     * @return OphDrPGDPSD_AssignmentFactory
     */
    public function forPatient($patient = null): self
    {
        $patient ??= Patient::factory();

        return $this->state([
            'patient_id' => $patient,
            'visit_id' => function ($attributes) {
                return WorklistPatient::factory()->state([
                    'patient_id' => $attributes['patient_id']
                ]);
            }
        ]);
    }

    /**
     * @param int $count
     * @return OphDrPGDPSD_AssignmentFactory
     */
    public function withMeds($count = 1): self
    {
        return $this->afterCreating(function (OphDrPGDPSD_Assignment $assignment) use ($count) {
            $assignment->assigned_meds = ModelFactory::factoryFor(OphDrPGDPSD_AssignmentMeds::class)
                ->count($count)
                ->create([
                    'assignment_id' => $assignment->id
                ]);

            $assignment->save(false);
        });
    }

    /**
     * @param OphDrPGDPSD_PGDPSD|OphDrPGDPSD_PGDPSDFactory|string|int $pgdpsd
     * @return OphDrPGDPSD_PGDPSDMedsFactory
     */
    public function forPGDPSD($pgdpsd = null): self
    {
        $pgdpsd ??= OphDrPGDPSD_PGDPSD::factory();

        return $this->state([
            'pgdpsd_id' => $pgdpsd
        ]);
    }
}
