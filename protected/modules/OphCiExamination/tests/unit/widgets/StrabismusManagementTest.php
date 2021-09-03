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

namespace OEModule\OphCiExamination\tests\unit\widgets;

use OEModule\OphCiExamination\controllers\DefaultController;
use OEModule\OphCiExamination\models\StrabismusManagement as StrabismusManagementModel;
use OEModule\OphCiExamination\models\StrabismusManagement_Treatment;
use OEModule\OphCiExamination\models\StrabismusManagement_TreatmentReason;
use OEModule\OphCiExamination\widgets\StrabismusManagement;

/**
 * Class StrabismusManagementTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\StrabismusManagement
 * @group sample-data
 * @group strabismus
 * @group strabismus-management
 */
class StrabismusManagementTest extends \OEDbTestCase
{
    use \CreatesWidgets;

    protected $element_cls = StrabismusManagementModel::class;
    protected $widget_cls = StrabismusManagement::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function treatments_as_json()
    {
        $widget = $this->getWidgetInstanceForElement();
        $treatments = StrabismusManagement_Treatment::model()->findAll();
        $this->assertNotEquals(0, count($treatments), 'treatments must be loaded for this test.');

        $result = $widget->getJsonTreatments();

        $this->assertJson($result);
        $this->assertCount(count($treatments), \CJSON::decode($result));
    }

    /** @test */
    public function treatment_reasons_as_json()
    {
        $widget = $this->getWidgetInstanceForElement();
        $reasons = StrabismusManagement_TreatmentReason::model()->findAll();
        $this->assertNotEquals(0, count($reasons), 'reasons must be loaded for this test');

        $result = $widget->getJsonTreatmentReasons();

        $this->assertJson($result);
        $this->assertCount(count($reasons), \CJSON::decode($result));
    }


}
