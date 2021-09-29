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
use OEModule\OphCiExamination\models\ConvergenceAccommodation as ConvergenceAccommodationElement;
use OEModule\OphCiExamination\models\CorrectionType;
use OEModule\OphCiExamination\widgets\ConvergenceAccommodation;

/**
 * Class ConvergenceAccommodationTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\ConvergenceAccommodation
 * @group sample-data
 * @group strabismus
 * @group convergence-accommodation
 */
class ConvergenceAccommodationTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \WithFaker;

    protected $element_cls = ConvergenceAccommodationElement::class;
    protected $widget_cls = ConvergenceAccommodation::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function check_edit_render()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = ConvergenceAccommodation::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertContains('id="OEModule_OphCiExamination_models_ConvergenceAccommodation_form"', $result);
    }

    /** @test */
    public function check_view_render()
    {
        $element = new ConvergenceAccommodationElement();
        $element->setAttributes([
            'correctiontype_id' => $this->getRandomLookup(CorrectionType::class)->getPrimaryKey(),
            'with_head_posture' => $this->faker->randomElement([
                ConvergenceAccommodationElement::$WITHOUT_HEAD_POSTURE,
                ConvergenceAccommodationElement::$WITH_HEAD_POSTURE]
            ),
            'comments' => $this->faker->realText()
        ]);

        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = ConvergenceAccommodation::$EVENT_VIEW_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertContains((string) $element->correctiontype, $result);
        $this->assertContains($element->display_with_head_posture, $result);
        $this->assertContains($element->comments, $result);
    }
}
