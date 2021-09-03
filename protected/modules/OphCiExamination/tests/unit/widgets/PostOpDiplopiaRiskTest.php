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
use OEModule\OphCiExamination\models\PostOpDiplopiaRisk as PostOpDiplopiaRiskElement;
use OEModule\OphCiExamination\widgets\PostOpDiplopiaRisk;

/**
 * Class PostOpDiplopiaTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\PostOpDiplopiaRisk
 * @group sample-data
 * @group strabismus
 * @group post-op-diplopia-risk
 */
class PostOpDiplopiaRiskTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \WithFaker;

    protected $element_cls = PostOpDiplopiaRiskElement::class;
    protected $widget_cls = PostOpDiplopiaRisk::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function check_edit_render()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = PostOpDiplopiaRisk::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertContains('id="OEModule_OphCiExamination_models_PostOpDiplopiaRisk_element"', $result);
    }

    /** @test */
    public function check_view_render()
    {
        $element = new PostOpDiplopiaRiskElement();
        $element->setAttributes([
            'at_risk' => $this->faker->randomElement([0, 1]),
            'comments' => $this->faker->sentences(2, true)
        ]);

        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = PostOpDiplopiaRisk::$EVENT_VIEW_MODE;

        $result = $this->getWidgetRender($widget);
        $this->assertNotEmpty($result);

        $this->assertStringContainsString($element->comments, $result);
        $this->assertStringContainsString($element->at_risk ? "Yes" : "No", $result);
    }
}
