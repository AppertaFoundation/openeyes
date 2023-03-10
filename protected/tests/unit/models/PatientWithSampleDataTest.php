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

use Patient;

/**
 * @group sample-data
 * @group patient
 */
class PatientWithSampleDataTest extends OEDbTestCase
{
    use WithTransactions;
    use HasModelAssertions;

    /** @test */
    public function patients_with_no_episodes_are_not_excluded_when_episodes_relation_is_eager_loaded()
    {
        $patient = Patient::factory()->create();

        $this->assertModelArraysMatch([$patient], Patient::model()->with('episodes')->findAllByAttributes(['id' => $patient->id]));
    }

    /** @test */
    public function patients_with_only_a_change_tracker_episode_are_not_excluded_when_episodes_relation_is_eager_loaded()
    {
        $patient = Patient::factory()->create();
        Episode::factory()->changeTracker()->create([
            'patient_id' => $patient
        ]);

        $this->assertModelArraysMatch([$patient], Patient::model()->with('episodes')->findAllByAttributes(['id' => $patient->id]));
    }

    /** @test */
    public function episodes_relation_excludes_change_tracker_episodes()
    {
        $patient = Patient::factory()->create();
        $expected = Episode::factory()
            ->count(2)
            ->create([
                'patient_id' => $patient
            ]);
        // should not be in relation
        Episode::factory()
            ->changeTracker()
            ->create([
                'patient_id' => $patient
            ]);

        $this->assertModelArraysMatch($expected, $patient->episodes);
    }

    /** @test */
    public function patients_with_only_a_legacy_episode_are_not_excluded_when_episodes_relation_is_eager_loaded()
    {
        $patient = Patient::factory()->create();
        Episode::factory()->legacy()->create([
            'patient_id' => $patient
        ]);

        $this->assertModelArraysMatch([$patient], Patient::model()->with('episodes')->findAllByAttributes(['id' => $patient->id]));
    }

    /** @test */
    public function episodes_relation_excludes_legacy_episodes()
    {
        $patient = Patient::factory()->create();
        $expected = Episode::factory()
            ->count(2)
            ->create([
                'patient_id' => $patient
            ]);
        // should not be in relation
        Episode::factory()
            ->legacy()
            ->create([
                'patient_id' => $patient
            ]);

        $this->assertModelArraysMatch($expected, $patient->episodes);
    }
}
