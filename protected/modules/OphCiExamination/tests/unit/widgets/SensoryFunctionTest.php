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
use OEModule\OphCiExamination\models\SensoryFunction as SensoryFunctionElement;
use OEModule\OphCiExamination\models\SensoryFunction_Entry;
use OEModule\OphCiExamination\widgets\SensoryFunction;

/**
 * Class SensoryFunctionTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\SensoryFunction
 * @group sample-data
 * @group strabismus
 * @group sensory-function
 */
class SensoryFunctionTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \WithFaker;

    protected $element_cls = SensoryFunctionElement::class;
    protected $widget_cls = SensoryFunction::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function edit_render()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = $widget::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertContains('id="OEModule_OphCiExamination_models_SensoryFunction_form"', $result);
    }

    /** @test */
    public function render_entry_template()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = SensoryFunction::$EVENT_EDIT_MODE;

        ob_start();
        $widget->renderEntryTemplate();
        $result = ob_get_clean();

        $this->assertNotEmpty($result);
    }

    /** @test */
    public function entries_should_be_set_empty_on_instance_when_none_provided_in_data()
    {
        $element = new SensoryFunctionElement();
        $element->entries = [new SensoryFunction_Entry()];

        $this->getWidgetInstanceForElement($element, []);

        $this->assertCount(0, $element->entries);
    }

    /** @test */
    public function should_default_to_pro_mode_for_a_single_entry()
    {
        $widget = $this->getWidgetInstanceForElement();

        $this->assertTrue($widget->shouldDisplayProViewForEntries([new SensoryFunction_Entry()]));
    }

    /** @test */
    public function should_default_to_expanded_mode_for_multiple_entries()
    {
        $widget = $this->getWidgetInstanceForElement();

        $this->assertFalse($widget->shouldDisplayProViewForEntries([new SensoryFunction_Entry(), new SensoryFunction_Entry()]));
    }
}
