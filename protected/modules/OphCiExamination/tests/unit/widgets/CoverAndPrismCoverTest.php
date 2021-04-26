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
use OEModule\OphCiExamination\models\CoverAndPrismCover as CoverAndPrismCoverElement;
use OEModule\OphCiExamination\models\CoverAndPrismCover_Entry;
use OEModule\OphCiExamination\models\CoverAndPrismCover_Correction;
use OEModule\OphCiExamination\models\CoverAndPrismCover_HorizontalPrism;
use OEModule\OphCiExamination\models\CoverAndPrismCover_Distance;
use OEModule\OphCiExamination\models\CoverAndPrismCover_VerticalPrism;
use OEModule\OphCiExamination\tests\traits\InteractsWithCoverAndPrismCover;
use OEModule\OphCiExamination\widgets\CoverAndPrismCover;

/**
 * Class CoverAndPrismCoverTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\CoverAndPrismCover
 * @group sample-data
 * @group strabismus
 * @group cover-test
 */
class CoverAndPrismCoverTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \WithFaker;
    use InteractsWithCoverAndPrismCover;

    protected $element_cls = CoverAndPrismCoverElement::class;
    protected $widget_cls = CoverAndPrismCover::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function check_edit_render()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = CoverAndPrismCover::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertContains('id="OEModule_OphCiExamination_models_CoverAndPrismCover_form"', $result);
    }

    /** @test */
    public function render_entry_template()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = CoverAndPrismCover::$EVENT_EDIT_MODE;

        ob_start();
        $widget->renderEntryTemplate();
        $result = ob_get_clean();

        $this->assertNotEmpty($result);
    }

    /** @test */
    public function check_edit_render_with_entries()
    {
        $element = new CoverAndPrismCoverElement();
        $entries = [];
        for ($i = 0; $i < rand(1, 5); $i++) {
            $entries[] = new CoverAndPrismCover_Entry();
        }
        $element->entries = $entries;

        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = CoverAndPrismCover::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        for ($i = 0; $i < count($element->entries); $i++) {
            $this->assertContains("data-key=\"$i\"", $result);
        }
    }

    /** @test */
    public function check_view_render()
    {
        $entry = new CoverAndPrismCover_Entry();
        $entry->setAttributes($this->generateCoverAndPrismCoverEntryData());

        $element = new CoverAndPrismCoverElement();
        $element->setAttributes(['comments' => $this->faker->realText()]);
        $element->entries = [$entry];

        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = CoverAndPrismCover::$EVENT_VIEW_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertContains((string)$entry->horizontal_prism, $result);
        $this->assertContains((string)$entry->horizontal_value, $result);
        $this->assertContains((string)$entry->vertical_prism, $result);
        $this->assertContains((string)$entry->vertical_value, $result);
        $this->assertContains((string)$entry->correctiontype, $result);
        $this->assertContains($entry->display_with_head_posture, $result);
        $this->assertContains((string)$entry->distance, $result);
        // entry comment
        $this->assertContains(htmlentities($entry->comments, ENT_QUOTES), $result);
        // element comment
        $this->assertContains(htmlentities($element->comments, ENT_QUOTES), $result);
    }
}