<?php

/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\tests\unit\controllers;

use InteractsWithEventTypeElements;
use OEModule\OphCiExamination\controllers\NinePositionsController;

/**
 * Class NinePositionsControllerTest
 *
 * @package OEModule\OphCiExamination\tests\unit\controllers
 * @covers \OEModule\OphCiExamination\controllers\DefaultController
 * @group sample-data
 * @group strabismus
 * @group nine-positions
 */
class NinePositionsControllerTest extends BaseControllerTest
{
    use InteractsWithEventTypeElements;

    public function setUp(): void
    {
        parent::setUp();
        // stub out components that will cause failures
        \Yii::app()->setComponent('assetManager', $this->getMockAssetManager());
        \Yii::app()->setComponent('request', $this->getMockRequest());
        $this->mockSession();
    }

    /** @test */
    public function get_exception_for_non_existent_patient()
    {
        $_GET['patient_id'] = 1;
        $_GET['index'] = 1;
        $this->expectException(\CHttpException::class);
        $this->performReadingFormRequest();
    }

    /** @test */
    public function get_reading_form_with_provided_index()
    {
        $_GET['patient_id'] = $this->getPatientWithEpisodes()->id;
        $_GET['index'] = 'foo';

        ob_start();

        $this->performReadingFormRequest();

        $response = ob_get_contents();

        ob_end_clean();

        $this->assertStringContainsString('ed_drawing_edit_left_ninepositions_foo', $response);
    }

    protected function performReadingFormRequest()
    {
        $controller = $this->getController(NinePositionsController::class);

        $action = $controller->createAction('readingForm');

        $controller->runAction($action);
    }
}
