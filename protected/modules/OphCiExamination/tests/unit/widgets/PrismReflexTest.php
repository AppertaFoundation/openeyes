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
use OEModule\OphCiExamination\models\PrismReflex as DioptrePrismElement;
use OEModule\OphCiExamination\models\PrismReflex_Entry;
use OEModule\OphCiExamination\models\CorrectionType;
use OEModule\OphCiExamination\models\PrismReflex_Finding;
use OEModule\OphCiExamination\models\PrismReflex_PrismBase;
use OEModule\OphCiExamination\models\PrismReflex_PrismDioptre;
use OEModule\OphCiExamination\widgets\PrismReflex;

/**
 * Class PrismReflexTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\PrismReflex
 * @group sample-data
 * @group strabismus
 * @group prism-reflex
 */
class PrismReflexTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \WithFaker;

    protected $element_cls = DioptrePrismElement::class;
    protected $widget_cls = PrismReflex::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function edit_render()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = PrismReflex::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertContains('id="OEModule_OphCiExamination_models_PrismReflex_form"', $result);
    }

    /** @test */
    public function render_entry_template()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = PrismReflex::$EVENT_EDIT_MODE;

        ob_start();
        $widget->renderEntryTemplate();
        $result = ob_get_clean();

        $this->assertNotEmpty($result);
    }

    /** @test */
    public function edit_render_with_entries()
    {
        $element = new DioptrePrismElement();
        $entries = [];
        for ($i = 0; $i < rand(1, 5); $i++) {
            $entries[] = new PrismReflex_Entry();
        }
        $element->entries = $entries;

        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = PrismReflex::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        for ($i = 0; $i < count($element->entries); $i++) {
            $this->assertContains("data-key=\"$i\"", $result);
        }
    }

    /** @test */
    public function view_render()
    {
        $entry = new PrismReflex_Entry();
        $entry->setAttributes([
            'prismdioptre_id' => $this->getRandomLookup(PrismReflex_PrismDioptre::class)->getPrimaryKey(),
            'prismbase_id' => $this->getRandomLookup(PrismReflex_PrismBase::class)->getPrimaryKey(),
            'finding_id' => $this->getRandomLookup(PrismReflex_Finding::class)->getPrimaryKey(),
            'correctiontype_id' => $this->getRandomLookup(CorrectionType::class)->getPrimaryKey(),
            'with_head_posture' => $this->faker->randomElement(
                [
                    PrismReflex_Entry::$WITHOUT_HEAD_POSTURE,
                    PrismReflex_Entry::$WITH_HEAD_POSTURE
                ]
            )
        ]);

        $element = new DioptrePrismElement();
        $element->setAttributes(['comments' => $this->faker->realText()]);
        $element->entries = [$entry];

        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = PrismReflex::$EVENT_VIEW_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertContains((string) $entry->prismdioptre, $result);
        $this->assertContains((string) $entry->prismbase, $result);
        $this->assertContains((string) $entry->finding, $result);
        $this->assertContains((string) $entry->correctiontype, $result);
        $this->assertContains($entry->display_with_head_posture, $result);
        $this->assertContains(\OELinebreakReplacer::replace(\CHtml::encode($element->comments)), $result);
    }
}