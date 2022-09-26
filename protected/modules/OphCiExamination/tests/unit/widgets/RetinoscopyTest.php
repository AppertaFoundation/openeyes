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
use OEModule\OphCiExamination\models\Retinoscopy as RetinoscopyModel;
use OEModule\OphCiExamination\tests\traits\InteractsWithRetinoscopy;
use OEModule\OphCiExamination\widgets\Retinoscopy;

/**
 * Class RetinoscopyTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\Retinoscopy
 * @group sample-data
 * @group strabismus
 * @group retinoscopy
 */
class RetinoscopyTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \WithFaker;
    use \WithTransactions;
    use InteractsWithRetinoscopy;

    protected $element_cls = RetinoscopyModel::class;
    protected $widget_cls = Retinoscopy::class;
    protected $controller_cls = DefaultController::class;

    public function setUp(): void
    {
        parent::setUp();
        \Yii::app()
            ->setComponent('session', $this->getMockBuilder(\CHttpSession::class)
                ->disableOriginalConstructor()
                ->getMock());
    }

    /**
     * @test
     */
    public function edit_mode_render_is_successful()
    {
        $instance = new RetinoscopyModel();
        $widget = $this->getWidgetInstanceForElement($instance);

        $widget->mode = Retinoscopy::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        $this->assertNotEmpty($result);
        $this->assertStringContainsString('js-retinoscopy-form', $result);
    }

    /** @test */
    public function view_mode()
    {
        $instance = $this->generateSavedRetinoscopyElementWithReadings();
        $widget = $this->getWidgetInstanceForElement($instance);
        $widget->mode = Retinoscopy::$EVENT_VIEW_MODE;

        $result = $this->getWidgetRender($widget);
        foreach (['right', 'left'] as $side) {
            $this->assertContains(
                $instance->{"{$side}_dilated"} ? "Dilated" : "Not dilated",
                $result
            );
            $this->assertContains($instance->{"{$side}_refraction"}, $result);
        }
    }

    /**
     * Override the base behaviour to disable calls to the widget method of the controller
     * because retinoscopy uses the eyedraw widget in rendering.
     */
    public function getController()
    {
        if (!$this->controller) {
            // disable the constructor, but otherwise leave the behaviour alone
            $this->controller = $this->getMockBuilder($this->controller_cls)
                ->disableOriginalConstructor()
                ->setMethods(['widget'])
                ->getMock();
        }
        return $this->controller;
    }
}
