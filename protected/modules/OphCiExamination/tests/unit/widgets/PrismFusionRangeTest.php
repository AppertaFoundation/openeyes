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
use OEModule\OphCiExamination\models\PrismFusionRange as PrismFusionRangeModel;
use OEModule\OphCiExamination\tests\traits\InteractsWithPrismFusionRange;
use OEModule\OphCiExamination\widgets\PrismFusionRange;

/**
 * Class PrismFusionRangeTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\PrismFusionRange
 * @group sample-data
 * @group strabismus
 * @group prism-fusion-range
 * @group pfr
 */
class PrismFusionRangeTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \WithTransactions;
    use InteractsWithPrismFusionRange;

    protected $element_cls = PrismFusionRangeModel::class;
    protected $widget_cls = PrismFusionRange::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function check_edit_render()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = PrismFusionRange::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // very simple checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertContains('id="OEModule_OphCiExamination_models_PrismFusionRange_form"', $result);
    }

    /** @test */
    public function render_entry_template()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = PrismFusionRange::$EVENT_EDIT_MODE;

        ob_start();
        $widget->renderEntryTemplate();
        $result = ob_get_clean();

        $this->assertNotEmpty($result);
    }

    /** @test */
    public function check_view_render()
    {
        $element = $this->generateSavedPrismFusionRangeWithEntries(2);
        $widget = $this->getWidgetInstanceForElement($element);
        $widget->mode = PrismFusionRange::$EVENT_VIEW_MODE;

        $result = $this->getWidgetRender($widget);

        $this->assertNotEmpty($result);

        foreach ($element->entries as $entry) {
            foreach (['near', 'distance'] as $kind) {
                foreach (['bo', 'bi', 'bu', 'bd'] as $attr) {
                    $this->assertContains((string) $entry->{"{$kind}_{$attr}"}, $result);
                }
            }
            $this->assertContains($entry->correctiontype->name, $entry->correctiontype);
            $this->assertContains($entry->display_with_head_posture, $entry->display_with_head_posture);
        }
        $this->assertContains($element->comments, $result);
    }
}
