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

//use OEDbTestCase;
use OE\factories\ModelFactory;

//use WithTransactions;
//use MakesApplicationRequests;

class EventDraftsTest extends OEDbTestCase
{
    use WithTransactions;
    use MakesApplicationRequests;

    /** @test */
    public function url_encoded_data_is_decoded_once_for_autosave()
    {
        $institution = Institution::factory()->useExisting()->create();

        $user = User::factory()
              ->withAuthItems(['User', 'Edit', 'View clinical'])
              ->create();

        $episode = Episode::factory()->create();

        $value = "/+ \\";
        $url_encoded_value = 'dummy_value=' . urlencode($value);
        $json_encoded_value = json_encode(['dummy_value' => $value]);

        $data = [
            'is_auto_save' => '1',
            'OE_episode_id' => $episode->id,
            'OE_module_class' => 'OphCiExamination',
            'form_data' => json_encode($url_encoded_value)
        ];

        $result = $this->actingAs($user, $institution)->post('/OphCiExamination/Default/saveDraft', $data);

        $result->assertContentTypeHeaderSent('application/json');

        $result = json_decode($result->response, true);

        $this->assertArrayHasKey('draft_id', $result, 'saveDraft did not return a draft_id');
        $draft = EventDraft::model()->findByPk($result['draft_id']);

        $this->assertNotNull($draft, 'Event Draft was not created');
        $this->assertEquals($json_encoded_value, $draft->data, 'Test data and Event Draft data do not match');
    }
}
