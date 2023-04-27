<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\controllers\DefaultController;
use OEModule\OphCiExamination\models\PupillaryAbnormalities as PupillaryAbnormalitiesElement;
use OEModule\OphCiExamination\widgets\PupillaryAbnormalities;

/**
 * @group sample-data
 * @group pupillary-abnormalities
 * @group pupils
 */
class PupillaryAbnormalitiesTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \WithFaker;
    use MocksSession;

    protected $element_cls = PupillaryAbnormalitiesElement::class;
    protected $widget_cls = PupillaryAbnormalities::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function edit_render()
    {
        $this->mockCurrentContext();
        $widget = $this->getWidgetInstanceForElement(null, []);
        $widget->mode = PupillaryAbnormalities::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('id="OEModule_OphCiExamination_models_PupillaryAbnormalities_form"', $result);
    }
}
