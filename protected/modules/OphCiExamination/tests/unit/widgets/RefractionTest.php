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
use OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction;
use OEModule\OphCiExamination\models\OphCiExamination_Refraction_Reading;
use OEModule\OphCiExamination\tests\traits\InteractsWithRefraction;
use OEModule\OphCiExamination\widgets\Refraction;

/**
 * Class RefractionTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\Refraction
 * @group sample-data
 * @group strabismus
 * @group refraction
 */
class RefractionTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \WithTransactions;
    use InteractsWithRefraction;

    protected $element_cls = Element_OphCiExamination_Refraction::class;
    protected $widget_cls = Refraction::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function check_edit_render()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = Refraction::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('id="OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_right_form"', $result);
        $this->assertStringContainsString('id="OEModule_OphCiExamination_models_Element_OphCiExamination_Refraction_left_form"', $result);
    }

    /** @test */
    public function reading_attribute_label()
    {
        $widget = $this->getWidgetInstanceForElement();

        // unable to DI this, but it at least ensure the method works and is return something consistent with the reading class
        $reading = OphCiExamination_Refraction_Reading::model();
        $attr = $this->faker->randomElement(['axis', 'type_id', 'foo bar']);

        $this->assertEquals($reading->getAttributeLabel($attr), $widget->getReadingAttributeLabel($attr));
    }

    public function side_provider()
    {
        return [
            ['right', 'right_readings'],
            ['left', 'left_readings']
        ];
    }

    /**
     * @test
     * @dataProvider side_provider
     */
    public function render_entry_template($side, $expected_readings_prefix)
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = Refraction::$EVENT_EDIT_MODE;

        ob_start();
        $widget->renderReadingTemplateForSide($side);
        $result = ob_get_clean();

        $this->assertNotEmpty($result);
        $this->assertStringContainsString($expected_readings_prefix, $result);
    }

    /** @test */
    public function reading_render_forces_other_type()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = Refraction::$EVENT_EDIT_MODE;
        $reading = $this->generateRefractionReading();
        $reading->type_id = null;
        $reading->type_other = '';

        ob_start();
        $widget->renderReadingsForElement([$reading]);
        $result = ob_get_clean();

        $this->assertNotEmpty($result);
        $this->assertStringContainsString('value="__other__" selected', $result, 'Other type should be selected');
    }

    /** @test */
    public function view_render()
    {
        $element = $this->generateSavedRefractionWithReadings();
        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = Refraction::$EVENT_VIEW_MODE;

        $result = $this->getWidgetRender($widget);

        foreach (['right', 'left'] as $side) {
            $this->assertStringContainsString($element->{"{$side}_notes"}, $result);
            foreach ($element->{"{$side}_readings"} as $reading) {
                foreach (['sphere', 'cylinder', 'type'] as $attr) {
                    $this->assertStringContainsString($reading->{"{$attr}_display"}, $result, "reading result should be displayed");
                }
                $this->assertStringContainsString((string) $reading->axis, $result, "reading result should be displayed");
            }
        }
    }
}
