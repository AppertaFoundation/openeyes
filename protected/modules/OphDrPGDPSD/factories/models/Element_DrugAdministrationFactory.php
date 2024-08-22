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
use Patient;

use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;
use OEModule\OphDrPGDPSD\models\Element_DrugAdministration;
use OEModule\OphDrPGDPSD\models\OphDrPGDPSD_Assignment;

class Element_DrugAdministrationFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphDrPGDPSD'),
            'type' => 'pgdpsd',
        ];
    }

    /**
     * @param Patient $patient
     * @return Element_DrugAdministrationFactory
     */
    public function forPatient(Patient $patient)
    {
        return $this->state([
            'event_id' => EventFactory::forModule('OphDrPGDPSD')->forPatient($patient),
        ]);
    }

    /**
     * @param Institution|InstitutionFactory|string|int|null $institution
     * @param int $assignment_count
     * @param int $med_count
     * @return Element_DrugAdministrationFactory
     */
    public function withEntries($institution = null, $assignment_count = 1, $med_count = 1): self
    {
        return $this->afterCreating(function (Element_DrugAdministration $element) use ($institution, $assignment_count, $med_count) {
            $element->assignments = OphDrPGDPSD_Assignment::factory()
                ->count($assignment_count)
                ->forInstitution($institution)
                ->forPatient($element->event->episode->patient)
                ->withMeds($med_count)
                ->create();

            $element->save(false);
        });
    }

    /**
     * @param array $assignments
     * @return Element_DrugAdministrationFactory
     */
    public function forAssignments($assignments): self
    {
        return $this->afterCreating(function (Element_DrugAdministration $element) use ($assignments) {
            $element->assignments = $assignments;

            $element->save(false);
        });
    }
}
