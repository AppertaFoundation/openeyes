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
use OEModule\OphCiExamination\models\BirthHistory as BirthHistoryModel;
use OEModule\OphCiExamination\widgets\BirthHistory;

/**
 * Class BirthHistoryTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @covers \OEModule\OphCiExamination\widgets\BirthHistory
 * @group sample-data
 * @group strabismus
 * @group birth-history
 */
class BirthHistoryTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use \HasModelAssertions;

    protected $element_cls = BirthHistoryModel::class;
    protected $widget_cls = BirthHistory::class;
    protected $controller_cls = DefaultController::class;

    /** @test */
    public function check_edit_render()
    {
        $widget = $this->getWidgetInstanceForElement();
        $widget->mode = $widget::$EVENT_EDIT_MODE;

        $result = $this->getWidgetRender($widget);

        // some very basic checks to verify no issues exist for basic rendering
        $this->assertNotEmpty($result);
        $this->assertContains('id="OEModule_OphCiExamination_models_BirthHistory_form"', $result);
    }

    /** @test */
    public function weight_input_type_defaults_to_kgs()
    {
        $widget = $this->getWidgetInstanceForElement();
        $this->assertEquals('kgs', $widget->inputWeightMode());
    }

    /** @test */
    public function weight_input_type_respects_element_value()
    {
        $element = new BirthHistoryModel();

        $widget = $this->getWidgetInstanceForElement($element);

        $element->weight_recorded_units = BirthHistoryModel::$WEIGHT_OZS;
        $this->assertEquals('lbs', $widget->inputWeightMode());

        $element->weight_recorded_units = BirthHistoryModel::$WEIGHT_GRAMS;
        $this->assertEquals('kgs', $widget->inputWeightMode());
    }

    /** @test */
    public function element_input_weight_kgs_is_set_when_created()
    {
        $element = new BirthHistoryModel();
        $weight = rand(1, 10000);

        $this->getWidgetInstanceForElement($element, ['input_weight_kgs' => $weight/1000]);

        $this->assertEquals($weight, $element->weight_grams);
        $this->assertEquals(BirthHistoryModel::$WEIGHT_GRAMS, $element->weight_recorded_units);
    }

    /** @test */
    public function element_input_weight_ozs_is_set_when_created()
    {
        $element = new BirthHistoryModel();
        $lbs = rand(1, 10);
        $ozs = rand(1, 15);

        $this->getWidgetInstanceForElement($element, ['input_weight_lbs' => "{$lbs}", 'input_weight_ozs' => ($ozs < 10 ? "0" : "") . "{$ozs}"]);

        $this->assertEquals((16*$lbs) + $ozs, $element->weight_ozs);
        $this->assertEquals(BirthHistoryModel::$WEIGHT_OZS, $element->weight_recorded_units);
    }

    /** @test */
    public function error_for_non_numeric_input_weight_kgs()
    {
        $element = new BirthHistoryModel();
        $this->getWidgetInstanceForElement($element, ['input_weight_kgs' => 'foo']);

        $this->assertAttributeInvalid($element, 'weight_grams', 'must be a number');
    }

    /** @test */
    public function input_ozs_invalid_ozs()
    {
        $element = new BirthHistoryModel();

        $this->getWidgetInstanceForElement($element, [
            'gestation_weeks' => 40, // ensure don't get failure because no required attributes set
            BirthHistory::$INPUT_LB_PORTION_FLD => (string) rand(1,11),
            BirthHistory::$INPUT_OZ_PORTION_FLD => (string) rand(16, 30)]);

        $this->assertAttributeInvalid($element, 'weight_ozs', "Too many ozs");
    }

    /** @test */
    public function error_for_non_numeric_input_weight_lbs()
    {
        $element = new BirthHistoryModel();
        $this->getWidgetInstanceForElement($element, [
            'gestation_weeks' => 40, // ensure don't get failure because no required attributes set
            BirthHistory::$INPUT_LB_PORTION_FLD => 'foo'
        ]);

        $this->assertAttributeInvalid($element, 'weight_ozs', 'Not a valid value');
    }

    /** @test */
    public function error_for_non_numeric_input_weight_ozs()
    {
        $element = new BirthHistoryModel();
        $this->getWidgetInstanceForElement($element, [
            'gestation_weeks' => 40, // ensure don't get failure because no required attributes set
            BirthHistory::$INPUT_OZ_PORTION_FLD => 'foo'
        ]);

        $this->assertAttributeInvalid($element, 'weight_ozs', 'Not a valid value');
    }

    public function getWeightPopulatedInstance()
    {
        $instance = new BirthHistoryModel();
        $instance->setAttributes([
            'weight_grams' => 2213,
            'weight_recorded_units' => BirthHistoryModel::$WEIGHT_GRAMS,
            'weight_ozs' => 78
        ]);
        return $instance;
    }

    /** @test */
    public function weight_attrs_emptied_by_empty_input()
    {
        $weight_attrs = ['weight_grams', 'weight_recorded_units', 'weight_ozs'];

        $element = $this->getWeightPopulatedInstance();
        $this->getWidgetInstanceForElement($element, ['input_weight_lb_ozs' => '']);

        foreach ($weight_attrs as $attr) {
            $this->assertEmpty($element->$attr, "$attr should be null when empty weight input");
        }

        $element = $this->getWeightPopulatedInstance();
        $this->getWidgetInstanceForElement($element, ['input_weight_kgs' => '']);

        foreach ($weight_attrs as $attr) {
            $this->assertEmpty($element->$attr, "$attr should be null when empty weight input");
        }
    }

    /** @test */
    public function input_weight_kgs_calculated_from_grams()
    {
        $element = new BirthHistoryModel();
        $element->weight_grams = rand(100, 10000);
        $widget = $this->getWidgetInstanceForElement($element);

        $this->assertEquals($element->weight_grams/1000, $widget->getInputWeightKgs());
        $this->assertNull($widget->getInputWeightLbsPortion());
        $this->assertNull($widget->getInputWeightOzsPortion());
    }

    /** @test */
    public function input_weight_lb_ozs_calculated_from_ozs()
    {
        $element = new BirthHistoryModel();
        $ozs = rand(0, 500);
        $element->weight_ozs = $ozs;
        $expected_ozs = $ozs % 16;
        $expected_lbs = ($ozs - $expected_ozs) / 16;

        $widget = $this->getWidgetInstanceForElement($element);

        $this->assertEquals($expected_lbs, $widget->getInputWeightLbsPortion());
        $this->assertEquals($expected_ozs, $widget->getInputWeightOzsPortion());
        $this->assertNull($widget->getInputWeightKgs());
    }

    /** @test */
    public function input_weight_mode_set_from_element_for_ozs()
    {
        $element = new BirthHistoryModel();
        $element->weight_ozs = 54;
        $element->weight_recorded_units = BirthHistoryModel::$WEIGHT_OZS;

        $widget = $this->getWidgetInstanceForElement($element);

        $this->assertEquals(BirthHistory::$INPUT_LB_OZS_MODE, $widget->inputWeightMode());
    }

    /** @test */
    public function input_weight_mode_set_from_element_for_grams()
    {
        $element = new BirthHistoryModel();
        $element->weight_grams = 4321;
        $element->weight_recorded_units = BirthHistoryModel::$WEIGHT_GRAMS;

        $widget = $this->getWidgetInstanceForElement($element);

        $this->assertEquals(BirthHistory::$INPUT_KGS_MODE, $widget->inputWeightMode());
    }

    /** @test */
    public function input_weight_mode_and_values_overridden_by_input_data_for_kgs()
    {
        $element = new BirthHistoryModel();
        $element->weight_ozs = 54;
        $element->weight_recorded_units = BirthHistoryModel::$WEIGHT_OZS;

        $widget = $this->getWidgetInstanceForElement($element, ['input_weight_kgs' => '3.42']);

        $this->assertEquals(BirthHistory::$INPUT_KGS_MODE, $widget->inputWeightMode());
        $this->assertEquals(3.42, $widget->getInputWeightKgs());
        $this->assertNull($widget->getInputWeightLbsPortion());
        $this->assertNull($widget->getInputWeightOzsPortion());
    }

    /** @test */
    public function input_weight_mode_and_values_overridden_by_input_data_for_ozs()
    {
        $element = new BirthHistoryModel();
        $element->weight_grams = 4321;
        $element->weight_recorded_units = BirthHistoryModel::$WEIGHT_GRAMS;

        $widget = $this->getWidgetInstanceForElement($element, [BirthHistory::$INPUT_LB_PORTION_FLD => "7", BirthHistory::$INPUT_OZ_PORTION_FLD => "4"]);

        $this->assertEquals(BirthHistory::$INPUT_LB_OZS_MODE, $widget->inputWeightMode());
        $this->assertEquals(7, $widget->getInputWeightLbsPortion());
        $this->assertEquals(4, $widget->getInputWeightOzsPortion());
        $this->assertEquals((7*16) + 4, $element->weight_ozs);
        $this->assertNull($widget->getInputWeightKgs());
    }
}