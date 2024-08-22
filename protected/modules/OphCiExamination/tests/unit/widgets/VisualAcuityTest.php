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
use OEModule\OphCiExamination\models\Element_OphCiExamination_NearVisualAcuity;
use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;
use OEModule\OphCiExamination\tests\traits\InteractsWithVisualAcuity;
use OEModule\OphCiExamination\widgets\VisualAcuity;
use OEModule\OphCoCvi\components\OphCoCvi_API;

/**
 * Class VisualAcuityTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\VisualAcuity
 * @group sample-data
 * @group strabismus
 * @group visual-acuity
 */
class VisualAcuityTest extends \OEDbTestCase
{
    use \CreatesWidgets {
        getWidgetInstanceForElement as baseGetWidgetInstanceForElement;
    }
    use InteractsWithVisualAcuity;
    use \MocksSession;
    use \WithFaker;
    use \WithTransactions;

    protected $widget_cls = VisualAcuity::class;
    protected $controller_cls = DefaultController::class;

    public function setUp(): void
    {
        parent::setUp();
        $this->stubSession();
    }

    /** @test */
    public function edit_render()
    {
        $this->mockCviApi(['OphCoCvi']);
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = VisualAcuity::$EVENT_EDIT_MODE;
        $this->mockCviApi('OphCoCvi', false);

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        // have to check for mock derived name
        $mock_model_name = \CHtml::modelName($widget->element);
        $this->assertStringContainsString("id=\"{$mock_model_name}_form\"", $result);

        foreach (['right', 'left'] as $side) {
            foreach (['unable_to_assess', 'eye_missing'] as $expected_attribute) {
                $this->assertStringContainsString("[{$side}_{$expected_attribute}]", $result);
            }
        }
    }

    /** @test */
    public function entry_header()
    {
        $fake_attribute = $this->faker->word();
        $instance = new OphCiExamination_VisualAcuity_Reading();
        $widget = $this->getWidgetInstanceForElement();

        // can't DI the model response, so this simple test
        // kind of verifies that the widget method uses
        // the reading class method to get the appropriate
        // attribute label
        $this->assertEquals(
            $instance->getAttributeLabel($fake_attribute),
            $widget->getReadingAttributeLabel($fake_attribute));
    }

    /** @test */
    public function render_reading_template()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = VisualAcuity::$EVENT_EDIT_MODE;
        $this->mockCviApi('OphCoCvi', false);

        $side = $this->faker->randomElement(['right', 'left']);
        ob_start();
        $widget->renderReadingTemplateForSide($side);
        $result = ob_get_clean();

        $this->assertNotEmpty($result);
        $this->assertStringContainsString("{$side}_readings", $result);
    }

    /** @test */
    public function view_render()
    {
        $element = $this->generateSavedVisualAcuityElementWithReadings(true, [
            'left_readings' => [],
            'left_unable_to_assess' => true,
            'left_notes' => $this->faker->words(4, true)
        ]);
        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = VisualAcuity::$EVENT_VIEW_MODE;
        $this->mockCviApi('OphCoCvi', false);

        $result = $this->getWidgetRender($widget);
        $this->assertStringContainsString("Unable to assess", $result);
        $this->assertStringContainsString($element->right_readings[0]->unit->name, $result);
        $this->assertStringContainsString($element->left_notes, $result);
    }

    public function fixation_status_provider()
    {
        return [
            [Element_OphCiExamination_VisualAcuity::class, true],
            [Element_OphCiExamination_NearVisualAcuity::class, false]
        ];
    }

    /**
     * @param $cls
     * @param $expected
     * @test
     * @dataProvider fixation_status_provider
     */
    public function fixation_status_for_element_cls($cls, $expected)
    {
        $element = new $cls();
        $widget = $this->getWidgetInstanceForElement($element);

        $this->assertEquals($expected, $widget->readingsHaveFixation());
    }

    public function cvi_alert_provider()
    {
        return [
            [true, Element_OphCiExamination_VisualAcuity::class, true],
            [false, Element_OphCiExamination_VisualAcuity::class, false],
            [true, Element_OphCiExamination_NearVisualAcuity::class, false]
        ];
    }

    /**
     * @test
     * @dataProvider cvi_alert_provider
     */
    public function should_support_cvi_alert($cvi_enabled, $element_cls, $expected)
    {
        $element = new $element_cls();
        $this->mockCviApi($cvi_enabled);
        $widget = $this->getWidgetInstanceForElement($element);


        $this->assertEquals($expected, $widget->shouldTrackCviAlert());
    }

    protected function getWidgetInstanceForElement($element = null, $data = null)
    {
        if ($element === null) {
            $element = $this->getStandardVisualAcuityElementWithSettings();
            $element->record_mode = Element_OphCiExamination_VisualAcuity::RECORD_MODE_COMPLEX;
        }

        return $this->baseGetWidgetInstanceForElement($element, $data);
    }

    protected function mockCviApi($enabled)
    {
        if (!$enabled) {
            $this->addModuleAPIToMockApp(['OphCoCvi' => false]);
            return;
        }

        $this->addModuleAPIToMockApp(['OphCoCvi' => new OphCoCvi_API()]);
    }
}
