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

namespace OEModule\OphCiExamination\tests\unit\models;


use OEModule\OphCiExamination\models\BirthHistory;
use OEModule\OphCiExamination\models\BirthHistory_DeliveryType;
use OEModule\OphCiExamination\tests\traits\InteractsWithBirthHistory;

/**
 * Class BirthHistoryTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\BirthHistory
 * @group sample-data
 * @group strabismus
 * @group birth-history
 */
class BirthHistoryTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;
    use \HasRelationOptionsToTest;
    use InteractsWithBirthHistory;
    use \WithFaker;
    use \WithTransactions;

    protected $element_cls = BirthHistory::class;

    /** @test */
    public function validation_error_is_set_when_no_attributes_set()
    {
        $instance = new BirthHistory();
        foreach ($instance->getSafeAttributeNames() as $attr) {
            $instance->$attr = '';
        }

        $result = $instance->validate();

        $this->assertFalse($result);
    }

    /**
     * @return array
     */
    public function basic_attribute_values()
    {
        return [
            ['weight_grams'],
            ['weight_ozs'],
            ['gestation_weeks'],
            ['had_neonatal_specialist_care'],
            ['was_multiple_birth']
        ];
    }

    /**
     * @param $attr
     * @param $value
     * @test
     * @dataProvider basic_attribute_values
     */
    public function no_validation_error_is_set_when_one_required_is_set($attr)
    {
        $instance = new BirthHistory();

        // need to ensure we get a value for weight attr from the generator
        $generatorAttrs = [
                'weight_grams' => ['weight_recorded_units' => BirthHistory::$WEIGHT_GRAMS],
                'weight_ozs' => ['weight_recorded_units' => BirthHistory::$WEIGHT_OZS]
            ][$attr] ?? [];
        $instance->$attr = $this->generateBirthHistoryData($generatorAttrs)[$attr];

        $this->assertTrue($instance->validate());
    }

    /** @test */
    public function weight_grams_max_validation()
    {
        $this->maxIntegerTest('weight_grams', 10000);
    }

    /** @test */
    public function weight_grams_min_validation()
    {
        $this->minIntegerTest('weight_grams', 225);
    }

    /** @test */
    public function weight_ozs_max_validation()
    {
        $this->maxIntegerTest('weight_ozs', 354);
    }

    /** @test */
    public function weight_ozs_min_validation()
    {
        $this->minIntegerTest('weight_ozs', 8);
    }

    /** @test */
    public function gestation_weeks_min_validation()
    {
        $this->minIntegerTest('gestation_weeks', 20);
    }

    /** @test */
    public function gestation_weeks_max_validation()
    {
        $this->maxIntegerTest('gestation_weeks', 42);
    }

    /** @test */
    public function had_neonatal_specialist_care_validation()
    {
        $this->validationForNRBooleansTest('had_neonatal_specialist_care');
    }

    /** @test */
    public function multiple_births_validation()
    {
        $this->validationForNRBooleansTest('was_multiple_birth');
    }

    /** @test */
    public function delivery_type_validation()
    {
        $instance = new BirthHistory();
        $instance->birth_history_delivery_type_id = 'foo';
        $this->assertAttributeInvalid(
            $instance,
            'birth_history_delivery_type_id',
            "{$instance->getAttributeLabel('birth_history_delivery_type_id')} is invalid"
        );

        $instance->birth_history_delivery_type_id = $this->generateBirthHistoryData()['birth_history_delivery_type_id'];
        $this->assertTrue($instance->validate());
    }

    /** @test */
    public function retrieving_delivery_type_options()
    {
        $this->assertOptionsAreRetrievable(new BirthHistory(), 'delivery_type', BirthHistory_DeliveryType::class);
    }

    /** @test */
    public function display_weight_for_grams_recorded_is_kgs()
    {
        $instance = new BirthHistory();
        $data = $this->generateBirthHistoryData(['weight_recorded_units' => BirthHistory::$WEIGHT_GRAMS]);
        $instance->weight_grams = $data['weight_grams'];
        $instance->weight_recorded_units = $data['weight_recorded_units'];

        $this->assertEquals(sprintf("%.3fkg", $data['weight_grams'] / 1000), $instance->display_weight);
    }

    /** @test */
    public function display_weight_for_ozs_recorded_is_lbs_and_ozs()
    {
        $instance = new BirthHistory();
        $instance->weight_ozs = 115;
        $instance->weight_recorded_units = BirthHistory::$WEIGHT_OZS;

        $this->assertEquals("7lb 3oz", $instance->display_weight);
    }

    /** @test */
    public function labelled_displayed_for_neonatal()
    {
        $instance = new BirthHistory();
        $value = $this->getValidNRBooleanValue();
        $instance->had_neonatal_specialist_care = $value;

        $this->assertEquals(
            $instance->getAttributeLabel('had_neonatal_specialist_care') . ": " . $this->getExpectedNRDisplay($value),
            $instance->display_labelled_had_neonatal_specialist_care
        );
    }

    /** @test */
    public function labelled_displayed_for_multiple_births()
    {
        $instance = new BirthHistory();
        $value = $this->getValidNRBooleanValue();
        $instance->was_multiple_birth = $value;

        $this->assertEquals(
            $instance->getAttributeLabel('was_multiple_birth') . ": " . $this->getExpectedNRDisplay($value),
            $instance->display_labelled_was_multiple_birth
        );
    }

    /** @test */
    public function null_display_multiple_births()
    {
        $instance = new BirthHistory();

        $this->assertNull($instance->display_was_multiple_birth);
    }

    /** @test */
    public function null_display_had_neonatal_specialist_care()
    {
        $instance = new BirthHistory();

        $this->assertNull($instance->display_had_neonatal_specialist_care);
    }

    /** @test */
    public function external_set_errors_are_returned_after_validation()
    {
        $instance = new BirthHistory();
        $instance->addExternalError('foo', 'bar');
        $this->assertFalse($instance->validate());
        $this->assertEquals(['bar'], $instance->getErrors('foo'));
    }

    /** @test */
    public function saved_had_neonatal_specialist_care_displayed_correctly()
    {
        $instance = new BirthHistory();
        $value = $this->getValidNRBooleanValue();
        $instance->had_neonatal_specialist_care = $value;

        $this->saveElement($instance);
        $savedInstance = BirthHistory::model()->findByPk($instance->getPrimaryKey());

        $this->assertEquals(
            $instance->getAttributeLabel('had_neonatal_specialist_care') . ": " . $this->getExpectedNRDisplay($value),
            $savedInstance->display_labelled_had_neonatal_specialist_care
        );
    }

    /** @test */
    public function saved_multiple_births_displayed_correctly()
    {
        $instance = new BirthHistory();
        $value = $this->getValidNRBooleanValue();
        $instance->was_multiple_birth = $value;

        $this->saveElement($instance);
        $savedInstance = BirthHistory::model()->findByPk($instance->getPrimaryKey());

        $this->assertEquals(
            $instance->getAttributeLabel('was_multiple_birth') . ": " . $this->getExpectedNRDisplay($value),
            $savedInstance->display_labelled_was_multiple_birth
        );
    }

    /** @test */
    public function letter_string_weight_grams()
    {
        $instance = $this->getElementInstance();
        $data = $this->generateBirthHistoryData(['weight_recorded_units' => BirthHistory::$WEIGHT_GRAMS]);
        $instance->weight_grams = $data['weight_grams'];
        $instance->weight_recorded_units = BirthHistory::$WEIGHT_GRAMS;

        $this->assertEquals('Birth History: ' . sprintf("%.3fkg", $data['weight_grams'] / 1000), $instance->letter_string);
    }

    /** @test */
    public function letter_string_weight_ozs()
    {
        $instance = $this->getElementInstance();
        $data = $this->generateBirthHistoryData(['weight_recorded_units' => BirthHistory::$WEIGHT_OZS]);
        $instance->weight_grams = $data['weight_ozs'];
        $instance->weight_recorded_units = BirthHistory::$WEIGHT_OZS;

        $this->assertEquals('Birth History: ' . $instance->display_weight, $instance->letter_string);
    }

    /** @test */
    public function letter_string_weight_delivery_method()
    {
        $instance = $this->getElementInstance();
        $delivery = $this->getRandomLookup(BirthHistory_DeliveryType::class);
        $instance->birth_history_delivery_type_id = $delivery->id;

        $this->assertEquals('Birth History: ' . $delivery->name, $instance->letter_string);
    }

    /** @test */
    public function letter_string_weight_gestation()
    {
        $instance = $this->getElementInstance();
        $data = $this->generateBirthHistoryData();
        $instance->gestation_weeks = $data['gestation_weeks'];

        $this->assertEquals("Birth History: {$data['gestation_weeks']} wks", $instance->letter_string);
    }

    /** @test */
    public function letter_string_neonatal()
    {
        $instance = $this->getElementInstance();
        $data = $this->generateBirthHistoryData();
        $instance->had_neonatal_specialist_care = $data['had_neonatal_specialist_care'];

        $this->assertEquals("Birth History: SCBU/NSCU: " . $this->getExpectedNRDisplay($data['had_neonatal_specialist_care']), $instance->letter_string);
    }

    /** @test */
    public function letter_string_multiple_births()
    {
        $instance = $this->getElementInstance();
        $data = $this->generateBirthHistoryData();
        $instance->was_multiple_birth = $data['was_multiple_birth'];

        $this->assertEquals("Birth History: Multiple birth: " . $this->getExpectedNRDisplay($data['was_multiple_birth']), $instance->letter_string);
    }

    /** @test */
    public function letter_string_combined()
    {
        $instance = $this->getElementInstance();
        $data = $this->generateBirthHistoryData();
        $instance->setAttributes($data);
        $instance->weight_recorded_units = $data['weight_recorded_units'];

        $this->assertEquals(
            sprintf(
                "Birth History: %s %s %s %s %s. %s",
                $instance->display_weight,
                $instance->delivery_type,
                $instance->display_gestation_weeks,
                $instance->display_labelled_had_neonatal_specialist_care,
                $instance->display_labelled_was_multiple_birth,
                $instance->comments
            ),
            $instance->letter_string
        );
    }

    protected function minIntegerTest($attribute, $minimum)
    {
        $this->assertMinValidation(BirthHistory::class, $attribute, $minimum);
    }

    protected function maxIntegerTest($attribute, $maximum)
    {
        $this->assertMaxValidation(BirthHistory::class, $attribute, $maximum);
    }

    protected function getExpectedNRDisplay($value)
    {
        return [
                BirthHistory::$YES => 'Yes',
                BirthHistory::$NO => 'No',
                BirthHistory::$NOT_RECORDED => 'Not Recorded',
            ][$value] ?? '';
    }

    protected function validationForNRBooleansTest($attribute)
    {
        $instance = new BirthHistory();
        $instance->$attribute = 'foo';
        $this->assertAttributeInvalid($instance, $attribute, "{$instance->getAttributeLabel($attribute)} is invalid");

        $instance->$attribute = $this->getInvalidNRBooleanValue();
        $this->assertAttributeInvalid($instance, $attribute, "{$instance->getAttributeLabel($attribute)} is invalid");

        $instance->$attribute = $this->getValidNRBooleanValue();
        $this->assertTrue($instance->validate());
    }
}
