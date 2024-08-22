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

use CHttpException;
use MakesApplicationRequests;
use OEDbTestCase;
use OEModule\OphCiExamination\models\OphCiExamination_ElementSet;
use OEModule\OphCiExamination\models\OphCiExamination_Event_ElementSet_Assignment;
use OEModule\OphCiExamination\models\OphCiExamination_Workflow;
use OEModule\OphCiExamination\models\Element_OphCiExamination_History;
use OEModule\OphCiExamination\models\Allergies;
use WithTransactions;
use WorklistPatient;

/**
 * This test was introduced for the mitigation behaviour for OE-15094
 * Once session state is no longer used, the behaviour is unlikely to be
 * needed
 *
 * @group sample-data
 */
class StepActionWorklistValidationTest extends OEDbTestCase
{
    use WithTransactions;
    use MakesApplicationRequests;

    /** @test */
    public function exception_thrown_when_worklist_patient_and_event_patient_do_no_match()
    {
        list($user, $institution) = $this->createUserWithInstitution();

        $workflow = OphCiExamination_Workflow::factory()
            ->withElementSets([
                OphCiExamination_ElementSet::factory()->forElementClasses(Element_OphCiExamination_History::class),
                OphCiExamination_ElementSet::factory()->forMandatoryElementClasses(Allergies::class)
            ])
            ->create();

        $event = \Event::factory()
            ->forModule('OphCiExamination')
            ->create(['institution_id' => $institution->id]);

        OphCiExamination_Event_ElementSet_Assignment::factory()
            ->forElementSet($workflow->steps[0])
            ->create(['event_id' => $event->id]);

        $worklist_patient = WorklistPatient::factory()->create();

        $this->mockCurrentContext($event->episode->firm, null, $institution);

        $response = $this->actingAs($user)
            ->get('/OphCiExamination/default/step/?id=' . $event->id . '&worklist_patient_id='  . $worklist_patient->id);

        $response->assertException(CHttpException::class, ['statusCode' => 400]);
    }

    /** @test */
    public function exception_is_not_thrown_when_worklist_patient_and_event_patient_do_match()
    {
        list($user, $institution) = $this->createUserWithInstitution();

        $workflow = OphCiExamination_Workflow::factory()
            ->withElementSets([
                OphCiExamination_ElementSet::factory()->forElementClasses(Element_OphCiExamination_History::class),
                OphCiExamination_ElementSet::factory()->forMandatoryElementClasses(Allergies::class)
            ])
            ->create();

        $event = \Event::factory()
            ->forModule('OphCiExamination')
            ->create(['institution_id' => $institution->id]);

        OphCiExamination_Event_ElementSet_Assignment::factory()
            ->forElementSet($workflow->steps[0])
            ->create(['event_id' => $event->id]);

        $worklist_patient = WorklistPatient::factory()->create(['patient_id' => $event->episode->patient_id]);

        $this->mockCurrentContext($event->episode->firm, null, $institution);

        $response = $this->actingAs($user)
            ->get('/OphCiExamination/default/step/?id=' . $event->id . '&worklist_patient_id='  . $worklist_patient->id);

        $response->assertSuccessful();
    }
}
