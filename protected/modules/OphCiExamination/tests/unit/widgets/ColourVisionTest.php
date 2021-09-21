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
use OEModule\OphCiExamination\models\Element_OphCiExamination_ColourVision;
use OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Reading;
use OEModule\OphCiExamination\widgets\ColourVision;

/**
 * Class ColourVisionTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\ColourVision
 * @group sample-data
 * @group strabismus
 * @group colour-vision
 */
class ColourVisionTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \WithFaker;

    protected $element_cls = Element_OphCiExamination_ColourVision::class;
    protected $widget_cls = ColourVision::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function check_edit_render()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = ColourVision::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertContains('id="OEModule_OphCiExamination_models_Element_OphCiExamination_ColourVision_form"', $result);
    }

    public function side_provider()
    {
        return [
            ['right'],
            ['left']
        ];
    }

    /**
     * @test
     * @dataProvider side_provider
     */
    public function render_entry_template($side)
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = ColourVision::$EVENT_EDIT_MODE;

        ob_start();
        $widget->renderReadingTemplateForSide($side);
        $result = ob_get_clean();

        $this->assertNotEmpty($result);
    }

    /**
     * @test
     * @dataProvider side_provider
     * @param $side
     */
    public function check_edit_render_with_sided_entries($side)
    {
        $element = new Element_OphCiExamination_ColourVision();
        $readings = [];
        for ($i = 0; $i < rand(1, 5); $i++) {
            $readings[] = new OphCiExamination_ColourVision_Reading();
        }
        $element->{"{$side}_readings"} = $readings;

        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = ColourVision::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        for ($i = 0; $i < count($readings); $i++) {
            $this->assertContains("data-key=\"$i\"", $result);
        }
    }
}
