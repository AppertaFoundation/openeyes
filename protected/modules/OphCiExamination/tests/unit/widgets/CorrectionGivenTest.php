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
use OEModule\OphCiExamination\models\CorrectionGiven as CorrectionGivenModel;
use OEModule\OphCiExamination\tests\traits\InteractsWithCorrectionGiven;
use OEModule\OphCiExamination\widgets\CorrectionGiven;

/**
 * Class CorrectionGivenTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\CorrectionGiven
 * @group sample-data
 * @group strabismus
 * @group correction-given
 */
class CorrectionGivenTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \WithFaker;
    use \WithTransactions;
    use InteractsWithCorrectionGiven;

    protected $element_cls = CorrectionGivenModel::class;
    protected $widget_cls = CorrectionGiven::class;
    protected $controller_cls = DefaultController::class;

    /**
     * @test
     */
    public function edit_mode_render_is_successful()
    {
        $instance = new CorrectionGivenModel();
        $widget = $this->getWidgetInstanceForElement($instance);

        $widget->mode = CorrectionGiven::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        $this->assertNotEmpty($result);
        $this->assertStringContainsString('js-correction-given-form', $result);
    }

    /** @test */
    public function view_mode()
    {
        $instance = $this->generateSavedCorrectionGiven();
        $widget = $this->getWidgetInstanceForElement($instance);
        $widget->mode = CorrectionGiven::$EVENT_VIEW_MODE;

        $result = $this->getWidgetRender($widget);
        foreach (['right', 'left'] as $side) {
            $this->assertStringContainsString(
                $instance->getOrderLabelForSide($side),
                $result
            );
            $this->assertStringContainsString($instance->{"{$side}_refraction"}, $result);
        }
    }
}
