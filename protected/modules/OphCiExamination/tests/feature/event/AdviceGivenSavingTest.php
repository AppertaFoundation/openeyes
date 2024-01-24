<?php
/**
 * (C) Apperta Foundation, 2024
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2024, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\tests\feature\event;

use Event;
use HasDatabaseAssertions;
use MakesApplicationRequests;
use MocksSession;
use OEDbTestCase;
use OEModule\OphCiExamination\models\AdviceGiven;
use OEModule\OphCiExamination\models\AdviceLeaflet;
use OEModule\OphCiExamination\models\Element_OphCiExamination_History;
use WithTransactions;

/**
 * @group sample-data
 */
class AdviceGivenSavingTest extends OEDbTestCase
{
    use HasDatabaseAssertions;
    use MocksSession;
    use MakesApplicationRequests;
    use WithTransactions;

    /** @test */
    public function element_can_be_removed_from_event()
    {
        list($user, $institution) = $this->createUserWithInstitution();

        // set up episode for new event to be attached to
        $episode = \Episode::factory()->create();

        $this->mockCurrentContext($episode->firm, null, $institution);

        $element = AdviceGiven::factory()
            ->forEpisode($episode)
            ->withLeaflets()
            ->create();

        $this->postEventUpdateWithOtherElement($element->event, $user, $institution);

        $this->assertElementDeleted($element);
    }

    protected function assertElementDeleted($element)
    {
        $this->assertFalse($element->refresh());
    }

    protected function postEventUpdateWithOtherElement(Event $event, $user, $institution)
    {

        $form_data = Element_OphCiExamination_History::factory()
            ->makeAsFormData(['event_id' => null]);

        $response = $this->actingAs($user, $institution)
            ->post('/OphCiExamination/Default/update/?id=' . $event->id, $form_data);

        $response->assertRedirectContains('/OphCiExamination/default/view/' . $event->id);
    }
}
