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

namespace OEModule\OphCiExamination\tests\unit\models;

use OEModule\OphCiExamination\models\OphCiExamination_Workflow;
use OEModule\OphCiExamination\models\OphCiExamination_Workflow_Rule;
use ModelTestCase;
use OE\factories\ModelFactory;

/**
 * class OphCiExamination_Workflow_RuleTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers OEModule\OphCiExamination\models\OphCiExamination_Workflow_Rule
 * @group sample-data
 * @group workflow
 */
class OphCiExamination_Workflow_RuleTest extends ModelTestCase
{
    use \WithTransactions;

    protected $element_cls = OphCiExamination_Workflow_Rule::class;

    public function setUp(): void
    {
        parent::setUp();

        // clear out the rules to allow us to set them up fresh with a known state
        OphCiExamination_Workflow_Rule::model()->deleteAll();
    }

    /** @test */
    public function workflow_cascading_find_order()
    {
        $institution = \Institution::factory()->isTenanted()->create();
        $firm = \Firm::factory()->create(['institution_id' => $institution]);
        $subspecialty = $firm->serviceSubspecialtyAssignment->subspecialty ?? null;
        $status = \EpisodeStatus::model()->find(['order' => 'RAND()']);

        $workflows = OphCiExamination_Workflow::factory()
                   ->forInstitution($institution)
                   ->forElementSet()
                   ->count(3)
                   ->create();

        $results = [];

        // Add each case in reverse order of specificity.
        // As each new and more specific rule is added, it should override all of those
        // added before and as each rule will have its own workflow, the list of workflows
        // collected from findWorkflow should only include each workflow exactly once.
        OphCiExamination_Workflow_Rule::factory()
            ->forWorkflow($workflows[0])
            ->create();

        $results[] = OphCiExamination_Workflow_Rule::model()->findWorkflowCascading($firm, $status->id);

        OphCiExamination_Workflow_Rule::factory()
            ->forWorkflow($workflows[1])
            ->forSubspecialty($subspecialty)
            ->create();

        $results[] = OphCiExamination_Workflow_Rule::model()->findWorkflowCascading($firm, $status->id);

        OphCiExamination_Workflow_Rule::factory()
            ->forWorkflow($workflows[2])
            ->forEpisodeStatus($status)
            ->create();

        $results[] = OphCiExamination_Workflow_Rule::model()->findWorkflowCascading($firm, $status->id);

        $this->assertModelArraysMatch($workflows, $results);
    }
}
