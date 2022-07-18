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
use OEModule\OphCiExamination\models\NinePositions as NinePositionsElement;
use OEModule\OphCiExamination\models\NinePositions_Reading;
use OEModule\OphCiExamination\tests\traits\InteractsWithNinePositions;
use OEModule\OphCiExamination\widgets\NinePositions;

/**
 * Class NinePositionsTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\NinePositions
 * @group sample-data
 * @group strabismus
 * @group nine-positions
 */
class NinePositionsTest extends \OEDbTestCase
{
    use \WithTransactions;
    use \CreatesWidgets;
    use \WithFaker;
    use InteractsWithNinePositions;

    protected $element_cls = NinePositionsElement::class;
    protected $widget_cls = NinePositions::class;
    protected $controller_cls = DefaultController::class;

    protected $element_type_id;

    public function setUp()
    {
        parent::setUp();
        $this->element_type_id = \ElementType::model()->find('class_name = ?', [NinePositionsElement::class])->id;
    }

    /** @test */
    public function a_reading_is_rendered_in_edit_mode_when_no_reading_defined()
    {
        $instance = new NinePositionsElement();
        $instance->readings = [];
        $widget = $this->getWidgetInstanceForElement($instance);
        $widget->mode = NinePositions::$EVENT_EDIT_MODE;

        ob_start();
        $widget->renderReadingsForElement($instance);
        $result = ob_get_clean();

        $this->assertNotEmpty($result);

        // N.B. this is potentially fragile if class assignment order changes in the view HTML
        $this->assertEquals(1, substr_count($result, 'class="js-reading'));
    }

    /** @test */
    public function view_render()
    {
        $element = $this->generateSavedNinePositionsWithReadings();
        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = NinePositions::$EVENT_VIEW_MODE;

        $result = $this->getWidgetRender($widget);

        foreach ($element->readings as $reading) {
            $this->assertContains($reading->comments, $result);
            foreach ($reading->alignments as $alignment) {
                $this->assertNotEmpty($alignment->display_horizontal);
                $this->assertContains($alignment->display_horizontal, $result);
                $this->assertNotEmpty($alignment->display_vertical);
                $this->assertContains($alignment->display_vertical, $result);
            }
        }
    }

    public function reading_attribute_provider()
    {
        return [
            [NinePositions::ENABLE_DVD],
            [NinePositions::ENABLE_HEAD_POSTURE],
            [NinePositions::ENABLE_CORRECTION],
            [NinePositions::ENABLE_WONG_SUPINE_POSITIVE],
            [NinePositions::ENABLE_HESS_CHART]
        ];
    }

    /**
     * @test
     * @dataProvider reading_attribute_provider
     */
    public function reading_attribute_enabled_checks_setting($flag)
    {
        $widget = $this->getWidgetInstanceForElement();
        $setting = \SettingMetadata::model()->find(
            'element_type_id = ? and `key` = ?', [
                $this->element_type_id,
                $flag
        ]);

        $setting->default_value = 0;
        $setting->save();

        $this->assertFalse($widget->isReadingAttributeEnabled($flag));
    }

    /**
     * Override the base behaviour to disable calls to the widget method of the controller
     * because nine positions uses the eyedraw widget in rendering.
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
