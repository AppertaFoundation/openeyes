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

use OE\factories\ModelFactory;
use OE\factories\models\EventFactory;
use OEModule\OphDrPGDPSD\models\Element_DrugAdministration;

class Element_DrugAdministration_recordFactory extends ModelFactory
{
    /**
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'event_id' => EventFactory::forModule('OphDrPGDPSD')
        ];
    }

    /**
     * @param int $assignment_count
     * @param int $med_count
     * @return Element_DrugAdministration_recordFactory
     */
    public function withEntries($assignment_count = 1, $med_count = 1): self
    {
        return $this->afterCreating(function (Element_DrugAdministration $element) use ($assignment_count, $med_count) {
            $element->entries = ModelFactory::factoryFor(OphDrPSDPGD_Assignment::class)
                ->count($assignment_count)
                ->withMeds($med_count)
                ->create([
                    'element_id' => $element->id
                ]);

            $element->save(false);
        });
    }
}
