<?php

/**
 * (C) Copyright Apperta Foundation 2023
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

 namespace OEModule\OphCiExamination\factories\models;

use Event;
use EventMedicationUse;
use OE\factories\ModelFactory;
use OEModule\OphCiExamination\models\MedicationManagement;
use OEModule\OphCiExamination\models\MedicationManagementEntry;

class MedicationManagementFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory()
        ];
    }

    public function forEvent($event)
    {
        return $this->state([
            'event_id' => $event
        ]);
    }

    public function withEntries(int $count = 1)
    {
        return $this->afterCreating(function (MedicationManagement $mm) use ($count) {
            EventMedicationUse::factory()
                ->forEvent($mm->event)
                ->forUsageType(MedicationManagementEntry::getUsageType())
                ->forUsageSubtype(MedicationManagementEntry::getUsageSubtype())
                ->count($count)
                ->create();
        });
    }

    public function withPrescribedEntries(int $count = 1)
    {
        return $this->afterCreating(function (MedicationManagement $mm) use ($count) {
            EventMedicationUse::factory()
                ->forEvent($mm->event)
                ->forUsageType(MedicationManagementEntry::getUsageType())
                ->forUsageSubtype(MedicationManagementEntry::getUsageSubtype())
                ->prescribed()
                ->count($count)
                ->create();
        });
    }

    public function withPrescription()
    {
        return $this->afterCreating(function (MedicationManagement $mm) {
            $prescription_event = Event::factory()
                ->forModule("OphDrPrescription")
                ->create();
            $mm->prescription_id = $prescription_event->id;

            foreach ($mm->entries as $entry) {
                $prescription_item = clone $entry;
                $prescription_item->usage_type = "OphDrPrescription";
                $prescription_item->usage_subtype = "";
                $prescription_item->bound_key = substr(bin2hex(openssl_random_pseudo_bytes(10)), 0, 10);
                $prescription_item->prescribe = 0;
                $prescription_item->save();
                $entry->prescription_item_id = $prescription_item->id;
            }
        });
    }
}
