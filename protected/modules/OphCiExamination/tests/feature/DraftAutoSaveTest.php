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

namespace OEModule\OphCiExamination\tests\feature;

use OE\factories\models\EventFactory;

/**
 * @group sample-data
 * @group examination
 */
class DraftAutoSaveTest extends \OEDbTestCase
{
    use \WithTransactions;
    use \MocksSession;
    use \MakesApplicationRequests;
    use \FakesSettingMetadata;

    /** @test */
    public function step_template_contains_correct_autosave_setting_when_auto_save_enabled_on()
    {
        $this->fakeSettingMetadata('auto_save_enabled', 'on');

        list($user, $institution) = $this->createUserWithInstitution();

        // set up patient and episode for new event to be attached to
        $episode = \Episode::factory()->create();
        $patient = $episode->patient;
        $event = EventFactory::forModule('OphCiExamination')->create([
            'episode_id' => $episode->id
        ]);

        $response = $this->actingAs($user, $institution)
            ->get("/OphCiExamination/default/step/?id={$event->id}&patient_id={$patient->id}")
            ->assertSuccessful()
            ->crawl();

        $this->assertTrue(filter_var($response->filter('.js-auto-save-enabled')->first()->attr('value'), FILTER_VALIDATE_BOOLEAN));
    }

    /** @test */
    public function step_template_contains_correct_autosave_setting_when_auto_save_enabled_off()
    {
        $this->fakeSettingMetadata('auto_save_enabled', 'off');

        list($user, $institution) = $this->createUserWithInstitution();

        // set up patient and episode for new event to be attached to
        $episode = \Episode::factory()->create();
        $patient = $episode->patient;
        $event = EventFactory::forModule('OphCiExamination')->create([
            'episode_id' => $episode->id
        ]);

        $response = $this->actingAs($user, $institution)
            ->get("/OphCiExamination/default/step/?id={$event->id}&patient_id={$patient->id}")
            ->assertSuccessful()
            ->crawl();

        $this->assertFalse(filter_var($response->filter('.js-auto-save-enabled')->first()->attr('value'), FILTER_VALIDATE_BOOLEAN));
    }
}
