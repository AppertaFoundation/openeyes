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

use OEModule\OphCiExamination\models\Allergies;
use OEModule\OphCiExamination\models\Element_OphCiExamination_History;
use OEModule\OphCiExamination\models\OphCiExamination_ElementSet;
use OEModule\OphCiExamination\models\OphCiExamination_Event_ElementSet_Assignment;
use OEModule\OphCiExamination\models\OphCiExamination_Workflow;

/**
 * Feature test introduced to check the right controls are rendered on the page
 * for mandatory element behaviour in examination
 *
 * @group sample-data
 * @group examination
 * @group workflow
 */
class MandatoryElementExaminationWorkflowTest extends \OEDbTestCase
{
    use \MocksSession;
    use \MakesApplicationRequests;
    use \WithTransactions;

    /** @test */
    public function next_workflow_step_maintains_mandatory_elementary_state_after_validation_error()
    {
        list($user, $institution) = $this->createUserWithInstitution();

        $workflow = OphCiExamination_Workflow::factory()
            ->withElementSets([
                OphCiExamination_ElementSet::factory()->forElementClasses(Element_OphCiExamination_History::class),
                OphCiExamination_ElementSet::factory()->forMandatoryElementClasses(Allergies::class)
            ])
            ->create();

        $event = OphCiExamination_Event_ElementSet_Assignment::factory()
            ->forElementSet($workflow->steps[0])
            ->create()->event;

        $this->mockCurrentContext($event->episode->firm, null, $institution);

        $response = $this->actingAs($user, $institution)
            ->post(
                // need to use non path based query params
                '/OphCiExamination/default/step/?id=' . $event->id,
                // and submit form keys to ensure elements are initialised for parsing
                [
                    \CHtml::modelName(Element_OphCiExamination_History::class) => ['description' => ''],
                    \CHtml::modelName(Allergies::class) => ['no_allergies' => '0']
                ]
            );

        $response_dom = $response->crawl();

        $expected_to_be_mandatory = $response_dom
            ->filter('section.element[data-element-type-id="' . $workflow->steps[1]->items[0]->element_type_id . '"]');

        // mandatory attribute is set to string values
        $this->assertEquals('true', $expected_to_be_mandatory->attr('data-mandatory'));
    }
}
