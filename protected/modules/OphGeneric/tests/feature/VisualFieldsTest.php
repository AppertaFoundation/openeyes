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

namespace OEModule\OphGeneric\tests\feature;

use Event;
use EventSubtype;
use OE\factories\models\EventFactory;
use OEModule\OphGeneric\components\EventManager;
use OEModule\OphGeneric\models\HFA;
use OEModule\OphGeneric\models\HFAEntry;

/**
 * @group sample-data
 * @group ophgeneric
 * @group visual-fields
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class VisualFieldsTest extends \OEDbTestCase
{
    use \WithTransactions;
    use \MocksSession;
    use \MakesApplicationRequests;

    /** @test */
    public function right_sided_manual_hfa_data_saved()
    {
        $this->performManualDataSave([$this->getHFAEntryFormData(\Eye::RIGHT)], \Eye::RIGHT);
    }

    /** @test */
    public function left_sided_manual_hfa_data_saved()
    {
        $this->performManualDataSave([$this->getHFAEntryFormData(\Eye::LEFT)], \Eye::LEFT);
    }

    /** @test */
    public function both_sided_manual_hfa_data_saved()
    {
        $this->performManualDataSave([$this->getHFAEntryFormData(\Eye::BOTH)], \Eye::BOTH);
    }

    /** @test */
    public function print_title_is_displayed_correctly()
    {
        $event_subtype = $this->setEventSubtypeElements();

        $event = EventFactory::forModule('OphGeneric')->withSubType($event_subtype)
            ->withElements([HFA::class])
            ->create();

        list($user, $institution, $episode) = $this->createEpisodeContext();

        $expected_title = EventManager::forEvent($event)->getDisplayName();

        $response = $this->actingAs($user)
            ->get('/OphGeneric/Default/print?id=' . $event->id);

        $title = $response->filter('.print-title');
        $this->assertEquals($expected_title, $title->first()->innerText());
    }

    protected function performManualDataSave($hfaEntryFormData, $eye_id)
    {
        list($user, $institution, $episode) = $this->createEpisodeContext();

        $event_subtype = $this->setEventSubtypeElements();

        $formData = array_merge(
            [
                'OEModule_OphGeneric_models_HFA' => [
                    'hfaEntry' => $hfaEntryFormData,
                ]
            ],
            [
                'patient_id' => $episode->patient->id,
                'event_subtype' => $event_subtype->event_subtype,
            ]
        );

        $response = $this->actingAs($user, $institution)
            ->post('/OphGeneric/Default/create', $formData);

        $response->assertRedirectContains('view', 'Expected to redirect to a view of the created event');

        $event = Event::model()->findByAttributes([
            'episode_id' => $episode->id
        ]);
        $hfa = HFA::model()->findByAttributes([
            'event_id' => $event->id
        ]);
        $hfa_entry_ids = array_map(function ($entry) {
            return $entry->id;
        }, $hfa->hfaEntry);

        // assert eye value matches
        $this->assertEquals($eye_id, $hfa->eye_id);

        // assert hfa entries are set correctly
        foreach ($hfaEntryFormData as $expectedHfaEntryAttributes) {
            $expectedHfaEntryAttributes['element_id'] = $hfa->id;
            $hfaEntry = HFAEntry::model()->findByAttributes($expectedHfaEntryAttributes);
            $this->assertContains($hfaEntry->id, $hfa_entry_ids);
        }
    }

    protected function createEpisodeContext()
    {
        $user = \User::model()->findByAttributes(['first_name' => 'admin']);

        $institution = \Institution::factory()
            ->withUserAsMember($user)
            ->create();

        // set up patient and episode for new event to be attached to
        $episode = \Episode::factory()->create();

        $this->mockCurrentContext($episode->firm, null, $institution);

        return [$user, $institution, $episode];
    }

    protected function setEventSubtypeElements()
    {
        return EventSubtype::factory()
                    ->allowManualEntry()
                    ->withElementTypes([
                        HFA::class
                    ])
                    ->create();
    }

    protected function getHFAEntryFormData($eye_id)
    {
        $hfa_entry = HFAEntry::factory()
            ->create([
                'eye_id' => $eye_id
            ]);

        return [
            'eye_id' => $hfa_entry->eye_id,
            'mean_deviation' => $hfa_entry->mean_deviation,
            'visual_field_index' => $hfa_entry->visual_field_index
        ];
    }
}
