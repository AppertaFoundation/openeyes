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
use OEModule\OphCiExamination\models\RedReflex as RedReflexElement;
use OEModule\OphCiExamination\tests\traits\InteractsWithRedReflex;
use OEModule\OphCiExamination\widgets\RedReflex;

/**
 * Class RedReflexTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @group sample-data
 * @group strabismus
 * @group red-reflex
 */
class RedReflexTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use InteractsWithRedReflex;

    protected $element_cls = RedReflexElement::class;
    protected $widget_cls = RedReflex::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function edit_render()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = RedReflex::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        // have to check for mock derived name
        $model_name = \CHtml::modelName($widget->element);
        $this->assertStringContainsString($model_name, $result);
    }

    /** @test */
    public function check_view_render()
    {
        $element = new RedReflexElement();
        $element->setAttributes($this->generateRedReflexData());

        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = RedReflex::$EVENT_VIEW_MODE;

        $result = $this->getWidgetRender($widget);
        $this->assertNotEmpty($result);

        foreach (['right', 'left'] as $side)  {
            if ($element->hasEye($side)) {
                $this->assertStringContainsString($element->{"{$side}_has_red_reflex"} ? "Yes" : "No", $result);
            } else {
                $this->assertStringContainsString("Not recorded", $result);
            }
        }
    }
}
