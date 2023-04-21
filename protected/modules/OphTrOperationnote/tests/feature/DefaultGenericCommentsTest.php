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

namespace OEModule\OphTrOperationnote\tests\feature;

use \OE\factories\models\EventFactory;
use \OphTrOperationNote_Generic_Procedure_Data;

/**
 * @group sample-data
 * @group operation-note
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class DefaultGenericCommentsTest extends \OEDbTestCase
{
    use \WithFaker;
    use \WithTransactions;
    use \MocksSession;
    use \MakesApplicationRequests;

    protected const LOAD_ELEMENT_BY_PROCEDURE_URL = '/OphTrOperationnote/Default/loadElementByProcedure';

    /** @test */
    public function unset_default_comment_is_empty_when_carried_through_to_loaded_generic_elements()
    {
        list($user, $institution) = $this->createUserWithInstitution();
        $procedure = \Procedure::factory()->create();

        $data = $this->createDataForRequests($procedure);

        $response = $this->getGenericCommentText($user, $institution, $procedure);

        // No default comment set yet, so the generic comment text should be empty
        $this->assertEquals('', $response);
    }

    /** @test */
    public function set_default_comments_are_carried_through_to_loaded_generic_elements()
    {
        list($user, $institution) = $this->createUserWithInstitution();
        $procedure = \Procedure::factory()->create();
        $comment = $this->faker->words(4, true);

        $data = $this->createDataForRequests($procedure);

        $default = OphTrOperationNote_Generic_Procedure_Data::factory()
                 ->forProcedure($procedure)
                 ->forDefaultText($comment)
                 ->create();

        $response = $this->getGenericCommentText($user, $institution, $procedure);

        $this->assertEquals($comment, $response);
    }

    protected function createDataForRequests($procedure)
    {
        $event = EventFactory::forModule('OphTrOperationnote')->create();
        $patient = $event->episode->patient;
        $eye = \Eye::factory()->useExisting()->make();

        return [
            'procedure_id' => $procedure->id,
            'eye' => $eye->id,
            'patient_id' => $patient->id,
            'event_id' => $event->id
        ];
    }

    protected function getGenericCommentText($user, $institution, $procedure)
    {
        $data = $this->createDataForRequests($procedure);

        $url = self::LOAD_ELEMENT_BY_PROCEDURE_URL . '?' . http_build_query($data, '', '&');

        $response = $this->actingAs($user, $institution)
                  ->get($url);

        return $response->filter('[data-test="generic-procedure-comments"]')->text();
    }

    // TODO: Refactor this out along with similiar instances in other existing tests.
    // This is common functionality in other tests that use MakesApplicationRequests::actingAs.
    // They all mark it as something to refactor.
    protected function createUserWithInstitution()
    {
        $user = \User::model()->findByAttributes(['first_name' => 'admin']);

        $institution = \Institution::factory()
            ->withUserAsMember($user)
            ->create();

        return [$user, $institution];
    }
}
