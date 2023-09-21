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

namespace OEModule\OphDrPrescription\seeders;

use OE\seeders\BaseSeeder;

use Patient;
use Medication;
use MedicationSet;
use MedicationSetItem;

class PrescribableCommonSystemicDrugsSeeder extends BaseSeeder
{
    public function __invoke(): array
    {
        $patient = Patient::factory()->create();

        $unbranded_prescribable_medication = Medication::factory()->dmd()->prescribable()->create(['source_subtype' => 'UNMAPPED']);
        $branded_prescribable_medication = Medication::factory()->dmd()->prescribable()->create(['source_subtype' => 'AMP']);
        $nonprescribable_medication = Medication::factory()->create();

        $prescribable_sets = MedicationSet::model()->findByUsageCode('PRESCRIBABLE_DRUGS');
        $common_systemic_sets = MedicationSet::model()->findByUsageCode('COMMON_SYSTEMIC');

        $this->addMedicationToSets($unbranded_prescribable_medication, $prescribable_sets);
        $this->addMedicationToSets($branded_prescribable_medication, $prescribable_sets);

        $this->addMedicationToSets($unbranded_prescribable_medication, $common_systemic_sets);
        $this->addMedicationToSets($branded_prescribable_medication, $common_systemic_sets);
        $this->addMedicationToSets($nonprescribable_medication, $common_systemic_sets);

        return [
            'patientId' => $patient->id,
            'unbrandedPrescribableMedication' => $this->seededMedication($unbranded_prescribable_medication),
            'brandedPrescribableMedication' => $this->seededMedication($branded_prescribable_medication),
            'nonprescribableMedication' => $this->seededMedication($nonprescribable_medication)
        ];
    }

    protected function addMedicationToSets($medication, $sets)
    {
        foreach ($sets as $set) {
            MedicationSetItem::factory()->forMedicationSet($set)->forMedication($medication)->create();
        }
    }

    protected function seededMedication($medication)
    {
        return ['id' => $medication->id, 'term' => $medication->preferred_term];
    }
}
