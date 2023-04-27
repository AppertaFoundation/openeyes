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
use OEModule\OphCiExamination\models\StereoAcuity as StereoAcuityElement;
use OEModule\OphCiExamination\models\StereoAcuity_Entry;
use OEModule\OphCiExamination\widgets\StereoAcuity;

/**
 * Class StereoAcuityTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\StereoAcuity
 * @group sample-data
 * @group strabismus
 * @group stereo-acuity
 */
class StereoAcuityTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \WithFaker;

    protected $element_cls = StereoAcuityElement::class;
    protected $widget_cls = StereoAcuity::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function edit_render()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = StereoAcuity::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('id="OEModule_OphCiExamination_models_StereoAcuity_form"', $result);
    }

    /** @test */
    public function render_entry_template()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = StereoAcuity::$EVENT_EDIT_MODE;

        ob_start();
        $widget->renderEntryTemplate();
        $result = ob_get_clean();

        $this->assertNotEmpty($result);
    }

    /** @test */
    public function edit_render_with_entries()
    {
        $element = new StereoAcuityElement();
        $entries = [];
        for ($i = 0; $i < rand(1, 5); $i++) {
            $entries[] = new StereoAcuity_Entry();
        }
        $element->entries = $entries;

        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = StereoAcuity::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        for ($i = 0; $i < count($element->entries); $i++) {
            $this->assertStringContainsString("data-key=\"$i\"", $result);
        }
    }

    /** @test */
    public function entries_should_be_set_empty_on_instance_when_none_provided_in_data()
    {
        $element = new StereoAcuityElement();
        $element->entries = [new StereoAcuity_Entry(), new StereoAcuity_Entry()];

        $widget = $this->getWidgetInstanceForElement($element, []);

        $this->assertCount(0, $element->entries);
    }
}
