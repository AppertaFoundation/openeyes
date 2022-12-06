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
use OEModule\OphCiExamination\models\ContrastSensitivity as ContrastSensitivityElement;
use OEModule\OphCiExamination\models\ContrastSensitivity_Result;
use OEModule\OphCiExamination\models\ContrastSensitivity_Type;
use OEModule\OphCiExamination\models\CorrectionType;
use OEModule\OphCiExamination\widgets\ContrastSensitivity;

/**
 * Class ContrastSensitivityTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\ContrastSensitivity
 * @group sample-data
 * @group strabismus
 * @group contrast-sensitivity
 */
class ContrastSensitivityTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \WithFaker;

    protected $element_cls = ContrastSensitivityElement::class;
    protected $widget_cls = ContrastSensitivity::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function check_edit_render()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = ContrastSensitivity::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('id="OEModule_OphCiExamination_models_ContrastSensitivity_form"', $result);
    }

    /** @test */
    public function render_entry_template()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = ContrastSensitivity::$EVENT_EDIT_MODE;

        ob_start();
        $widget->renderEntryTemplate();
        $result = ob_get_clean();

        $this->assertNotEmpty($result);
    }

    /** @test */
    public function check_edit_render_with_entries()
    {
        $element = new ContrastSensitivityElement();
        $entries = [];
        for ($i = 0; $i < rand(1, 5); $i++) {
            $entries[] = new ContrastSensitivity_Result();
        }
        $element->results = $entries;

        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = ContrastSensitivity::$EVENT_EDIT_MODE;

        $output = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($output);
        for ($i = 0; $i < count($element->results); $i++) {
            $this->assertStringContainsString("data-key=\"$i\"", $output);
        }
    }

    /** @test */
    public function check_view_render()
    {
        $entry = new ContrastSensitivity_Result();
        $entry->setAttributes([
            'contrastsensitivity_type_id' => $this->getRandomLookup(ContrastSensitivity_Type::class)->getPrimaryKey(),
            'value' => $this->faker->numberBetween(0, 9),
            'eye_id' => $eye = [
                ContrastSensitivity_Result::BEO,
                ContrastSensitivity_Result::LEFT,
                ContrastSensitivity_Result::RIGHT
            ][random_int(0, 2)],
            'correctiontype_id' => $this->getRandomLookup(CorrectionType::class)->getPrimaryKey()
        ]);

        $element = new ContrastSensitivityElement();
        $element->setAttributes(['comments' => $this->faker->realText()]);
        $element->results = [$entry];

        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = ContrastSensitivity::$EVENT_VIEW_MODE;

        $output = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($output);
        $this->assertStringContainsString((string)$entry->contrastsensitivity_type, $output);
        $this->assertStringContainsString((string)$entry->value, $output);
        $this->assertStringContainsString((string)$entry->correctiontype, $output);

        if ($entry->eye_id == ContrastSensitivity_Result::BEO) {
            $this->assertStringContainsString('<i class="oe-i beo small pad"></i>', $output);
        } elseif ($entry->eye_id == ContrastSensitivity_Result::LEFT) {
            $this->assertStringContainsString('<i class="oe-i laterality L small pad"></i>', $output);
        } elseif ($entry->eye_id == ContrastSensitivity_Result::RIGHT) {
            $this->assertStringContainsString('<i class="oe-i laterality R small pad"></i>', $output);
        }

        // element comment
        $this->assertStringContainsString(htmlentities($element->comments, ENT_QUOTES), $output);
    }
}
