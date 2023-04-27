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
 * class OphCiExamination_WorkflowTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers OEModule\OphCiExamination\models\OphCiExamination_Workflow
 * @group sample-data
 * @group workflow
 */
class OphCiExamination_WorkflowTest extends ModelTestCase
{
    use \WithTransactions;
    use \MocksSession;

    protected $element_cls = OphCiExamination_Workflow::class;

    /** @test */
    public function only_tenanted_institutions_permitted_for_institution_level_workflows()
    {
        $untenanted_institution = \Institution::factory()->create();

        $this->mockCurrentInstitution($untenanted_institution);

        $workflow = OphCiExamination_Workflow::factory()
                  ->forInstitution($untenanted_institution)
                  ->make();

        $this->assertAttributeInvalid($workflow, 'institution_id', 'not tenanted');

        $tenanted_institution = \Institution::factory()
                     ->isTenanted()
                     ->create();

        $this->mockCurrentInstitution($tenanted_institution);

        $workflow->institution_id = $tenanted_institution->id;

        $this->assertAttributeValid($workflow, 'institution_id');
    }

    /** @test */
    public function institution_can_only_be_different_from_current_institution_under_installation_admin_save_scenario()
    {
        list($current_institution, $other_institution) = \Institution::factory()
                                                          ->isTenanted()
                                                          ->count(2)
                                                          ->create();

        $this->mockCurrentInstitution($current_institution);

        $workflow = OphCiExamination_Workflow::factory()
            ->forInstitution($other_institution)
            ->make();

        $this->assertAttributeInvalid($workflow, 'institution_id', 'selected institution cannot be chosen');

        $workflow->setScenario('installationAdminSave');

        $this->assertAttributeValid($workflow, 'institution_id');
    }

    /** @test */
    public function only_workflows_without_associated_rules_can_change_institution()
    {
        $institution = \Institution::factory()
                     ->isTenanted()
                     ->create();

        $this->mockCurrentInstitution($institution);

        $workflow = OphCiExamination_Workflow::factory()
                  ->forInstitution($institution)
                  ->create();

        // allow institution change generally
        $workflow->setScenario('installationAdminSave');
        $workflow->institution_id = null;

        $this->assertAttributeValid($workflow, 'institution_id');

        OphCiExamination_Workflow_Rule::factory()
              ->forWorkflow($workflow)
              ->create();

        // reset the relation so that it is loaded fresh during validation
        unset($workflow->workflow_rules);

        $this->assertAttributeInvalid($workflow, 'institution_id', 'Cannot change the workflow\'s institution');
    }
}
