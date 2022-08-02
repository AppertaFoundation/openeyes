<?php

/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\ModelFactory;

/**
 * @group sample-data
 * @group disorder
 * @group diagnoses
 */
class PatientDiagnosesTest extends \OEDbTestCase
{
    use WithTransactions;

    /** @test */
    public function db_view_contains_principle_diagnosis()
    {
        $patient = ModelFactory::factoryFor(Patient::class)->create();
        $disorder = ModelFactory::factoryFor(Disorder::class)->withICD10()->create();
        $eye = ModelFactory::factoryFor(Eye::class)->useExisting()->create();

        $view_attributes = [
            'patient_id' => $patient->id,
            'disorder_id' => $disorder->id,
            'icd10_code' => $disorder->icd10_code,
            'icd10_term' => $disorder->icd10_term,
            'side' => $eye->getShortName()
        ];

        $this->assertDatabaseDoesntHave('v_patient_diagnoses', $view_attributes);

        $episode = ModelFactory::factoryFor(Episode::class)
            ->withPrincipalDiagnosis($disorder->id, $eye->id)
            ->create();

        $this->assertDatabaseHas('v_patient_diagnoses', $view_attributes);
    }

    /** @test */
    public function db_view_contains_patient_secondary_diagnosis()
    {
        $patient = ModelFactory::factoryFor(Patient::class)->create();
        $disorder = ModelFactory::factoryFor(Disorder::class)->withICD10()->create();
        $eye = ModelFactory::factoryFor(Eye::class)->useExisting()->create();

        $view_attributes = [
            'patient_id' => $patient->id,
            'disorder_id' => $disorder->id,
            'icd10_code' => $disorder->icd10_code,
            'icd10_term' => $disorder->icd10_term,
            'side' => $eye->getShortName()
        ];

        $this->assertDatabaseDoesntHave('v_patient_diagnoses', $view_attributes);

        $secondary_diagnosis = ModelFactory::factoryFor(SecondaryDiagnosis::class)->create([
            'patient_id' => $patient->id,
            'disorder_id' => $disorder->id,
            'eye_id' => $eye->id
        ]);
        $this->assertDatabaseHas('v_patient_diagnoses', $view_attributes);
    }

    protected function assertDatabaseHas(string $table, array $attributes)
    {
        $cmd = $this->generateDatabaseCountQuery($table, $attributes);

        $this->assertGreaterThanOrEqual(1, $cmd->queryScalar());
    }

    protected function assertDatabaseDoesntHave(string $table, array $attributes)
    {
        $cmd = $this->generateDatabaseCountQuery($table, $attributes);

        $this->assertEquals(0, $cmd->queryScalar());
    }

    protected function generateDatabaseCountQuery(string $table, array $attributes)
    {
        $db = $this->getFixtureManager()->dbConnection;

        $wheres = [];
        $params = [];
        foreach ($attributes as $col => $val) {
            $wheres[] = "$col = :_$col";
            $params[":_$col"] = $val;
        }

        return $db->createCommand()
            ->select('COUNT(*)')
            ->from($table)
            ->where(implode(' AND ', $wheres), $params);
    }
}
