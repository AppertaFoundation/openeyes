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
use OEModule\OphCiExamination\models\Synoptophore as SynoptophoreElement;
use OEModule\OphCiExamination\tests\traits\InteractsWithSynoptophore;
use OEModule\OphCiExamination\widgets\Synoptophore;

/**
 * Class SynoptophoreTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\Synoptophore
 * @group sample-data
 * @group strabismus
 * @group synoptophore
 */
class SynoptophoreTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \WithFaker;
    use InteractsWithSynoptophore;
    use \WithTransactions;


    protected $element_cls = SynoptophoreElement::class;
    protected $widget_cls = Synoptophore::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function edit_render()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = Synoptophore::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        // have to check for mock derived name
        $model_name = \CHtml::modelName($widget->element);
        $this->assertStringContainsString("id=\"{$model_name}_form\"", $result);
    }

    /** @test */
    public function check_view_render()
    {
        $element = new SynoptophoreElement();
        $element->setAttributes($this->generateSynoptophoreData());

        $side = $this->faker->randomElement(['right', 'left']);
        $reading = $this->generateSynoptophoreReading();

        $element->{"setHas" . ucfirst($side)}();
        $element->{"{$side}_readings"} = [$reading];

        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = Synoptophore::$EVENT_VIEW_MODE;

        $result = $this->getWidgetRender($widget);
        $this->assertNotEmpty($result);

        $this->assertStringContainsString((string)$reading, $result);

        $this->assertStringContainsString((string)$element->angle_from_primary, $result);
        // element comment
        $this->assertStringContainsString(htmlentities($element->comments, ENT_QUOTES), $result);
    }
}
