<?php

/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\tests\unit\widgets;


use OEModule\OphCiExamination\controllers\DefaultController;
use OEModule\OphCiExamination\models\SystemicSurgery as SystemicSurgeryModel;
use OEModule\OphCiExamination\widgets\SystemicSurgery;
use OEModule\OphCiExamination\components\OphCiExamination_API;

/**
 * Class SystemicSurgeryTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\SystemicSurgery
 * @group sample-data
 * @group systemic-surgery
 */
class SystemicSurgeryTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \HasModelAssertions;
    use \MocksSession;
    use \InteractsWithCommonPreviousSystemicOperation;
    use \InteractsWithInstitution;
    use \WithFaker;
    use \WithTransactions;

    protected $element_cls = SystemicSurgeryModel::class;
    protected $widget_cls = SystemicSurgery::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function check_edit_render()
    {
        $this->mockCurrentInstitution(); // Systemic Surgery does require a session set institution
        $this->mockModuleApisWithRequiredSystemicSurgicalHistory();

        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = SystemicSurgery::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('id="OEModule_OphCiExamination_models_SystemicSurgery_form"', $result);
    }

    /** @test */
    public function previous_operation_options_are_filtered_by_institution()
    {
        // TODO: use factories in this test once the model factory code is merged in.
        $selectedInstitution = $this->generateSavedInstitution();

        $expectedOperations = [];
        for ($i = 0; $i < rand(1, 5); $i++) {
            $expectedOperations[] = $this->generateCommonPreviousSystemicOperationForInstitution($selectedInstitution);
        }

        // generate unattached to ensure it's filtered
        $this->generateCommonPreviousSystemicOperation();

        $this->mockCurrentInstitution($selectedInstitution);

        $widget =  $this->getWidgetInstanceForElement();

        $result = $widget->getPreviousOperationOptions();
        $this->assertModelArraysMatch($expectedOperations, $result);
    }

    /**
     * Systemic Surgery requires op note and exam apis, so we mock out here
     */
    protected function mockModuleApisWithRequiredSystemicSurgicalHistory(?array $history = [])
    {
        $examAPI = $this->getMockBuilder(OphCiExamination_API::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRequiredSystemicSurgicalHistory'])
            ->getMock();
        $examAPI->method('getRequiredSystemicSurgicalHistory')
            ->willReturn($history);

        $this->addModuleAPIToMockApp([
            'OphTrOperationnote' => null,
            'OphCiExamination' => $examAPI
        ]);
    }
}
