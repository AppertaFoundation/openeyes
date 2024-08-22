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

use OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction;
use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\OphCiExamination_Refraction_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type;
use OEModule\OphCiExamination\tests\traits\InteractsWithRefraction;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasSidedModelAssertions;

/**
 * Class Element_OphCiExamination_RefractionTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction
 * @group sample-data
 * @group strabismus
 * @group refraction
 */
class Element_OphCiExamination_RefractionTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;
    use \WithTransactions;
    use InteractsWithRefraction;
    use HasSidedModelAssertions;

    protected $element_cls = Element_OphCiExamination_Refraction::class;
    // as text columns, these will be set to the empty string when nothing provided
    protected array $columns_to_skip = ['left_notes', 'right_notes'];

    public function side_provider()
    {
        return [
            ['right', SidedData::RIGHT],
            ['left', SidedData::LEFT]
        ];
    }

    /**
     * @test
     * @dataProvider side_provider
     * @param $side
     * @param $on_value
     */
    public function readings_relations_defined($side, $on_value)
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();

        $relation_name = "{$side}_readings";

        $this->assertArrayHasKey($relation_name, $relations);
        $this->assertEquals(\CHasManyRelation::class, $relations[$relation_name][0]);
        $this->assertEquals(OphCiExamination_Refraction_Reading::class, $relations[$relation_name][1]);
        $this->assertArrayHasKey('on', $relations[$relation_name]);
        $this->assertEquals("{$relation_name}.eye_id = $on_value", $relations[$relation_name]['on']);
    }

    /**
     * @test
     * @dataProvider side_provider
     * @param $invalid_side
     */
    public function side_must_have_a_reading($invalid_side)
    {
        $readings_attr = "{$invalid_side}_readings";

        $instance = $this->getElementInstance();
        // ensure side expected
        $instance->{"setHas" . ucfirst($invalid_side)}();
        $instance->$readings_attr = [];

        $this->assertAttributeInvalid($instance, $readings_attr, 'cannot be blank');
    }

    /**
     * @test
     * @dataProvider side_provider
     * @param $side
     */
    public function readings_are_validated($side)
    {
        $this->assertSidedRelationValidated(
            $this->element_cls,
            OphCiExamination_Refraction_Reading::class,
            $side,
            "{$side}_readings"
        );
    }

    /**
     * @test
     * @dataProvider side_provider
     * @param $invalid_side
     */
    public function reading_types_must_be_unique_for_side($invalid_side)
    {
        $reading_type = $this->getRandomLookup(OphCiExamination_Refraction_Type::class);
        $reading = $this->createValidatingModelMock(OphCiExamination_Refraction_Reading::class);
        $reading->type_id = $reading_type->id;
        $instance = $this->getElementInstance();
        $instance->{"setHas" . ucfirst($invalid_side)}(); // ensure side set

        $instance->{"{$invalid_side}_readings"} = [$reading, $reading];

        $this->assertAttributeInvalid($instance, "{$invalid_side}_readings", "Each reading type can only be recorded once");
    }

    /**
     * @test
     * @dataProvider side_provider
     * @param $invalid_side
     */
    public function reading_type_others_must_be_unique_for_side($invalid_side)
    {
        $reading = $this->createValidatingModelMock(OphCiExamination_Refraction_Reading::class);
        $reading->type_other = $this->faker->word();

        $instance = $this->getElementInstance();
        $instance->{"setHas" . ucfirst($invalid_side)}(); // ensure side set

        $instance->{"{$invalid_side}_readings"} = [$reading, $reading];
        $this->assertAttributeInvalid($instance, "{$invalid_side}_readings", "Each reading type can only be recorded once");
    }

    /**
     * @test
     * @dataProvider side_provider
     * @param $side
     * @param $eye_id
     */
    public function readings_are_saved_correctly($side, $eye_id)
    {
        $instance = $this->getElementInstance();
        $instance->event_id = $this->getEventToSaveWith()->getPrimaryKey();

        $readings_attr = "{$side}_readings";

        $reading_data = $this->generateRefractionReadingData();
        $instance->$readings_attr = [$reading_data];
        $instance->eye_id = $eye_id;

        $this->assertTrue($instance->validate(), "Invalid: " . print_r($instance->getErrors(), true));
        $this->assertTrue($instance->save(), "element should save successfully.");
        $savedInstance = $this->element_cls::model()->findByPk($instance->getPrimaryKey());

        $this->assertCount(1, $savedInstance->$readings_attr);
        foreach ($reading_data as $attr => $val) {
            $this->assertEquals($val, $savedInstance->$readings_attr[0]->$attr);
        }
    }

    /**
     * @test
     * @dataProvider side_provider
     * @param $side
     * @param $eye_side
     */
    public function priority_reading_relation_resolves_for_side($side, $eye_side)
    {
        $types = OphCiExamination_Refraction_Type::model()->findAll(['order' => 'priority asc']);
        $readings = [
            $this->generateRefractionReading(['eye_id' => $eye_side, 'type_id' => $types[1]->id]),
            $this->generateRefractionReading(['eye_id' => $eye_side, 'type_id' => $types[2]->id]),
            $this->generateRefractionReading(['eye_id' => $eye_side, 'type_id' => null, 'type_other' => $this->faker->words(2, true)]),
            $this->generateRefractionReading(['eye_id' => $eye_side, 'type_id' => $types[0]->id])
        ];

        $instance = $this->generateSavedRefractionWithReadings(["{$side}_readings" => $readings]);

        // get the expected priority reading
        $priority_reading = array_values(
            array_filter(
                $instance->{"{$side}_readings"},
                function ($reading) use ($types) {
                    return $reading->type_id == $types[0]->id;
                }
            )
        )[0];

        $this->assertEquals($priority_reading->id, $instance->{"{$side}_priority_reading"}->id);
    }

    /**
     * @test
     * @dataProvider side_provider
     * @param $side
     * @param $eye_side
     */
    public function priority_reading_works_with_other($side, $eye_side)
    {
        $readings = [
            $this->generateRefractionReading(['eye_id' => $eye_side, 'type_id' => null, 'type_other' => $this->faker->words(2, true)])
        ];

        $instance = $this->generateSavedRefractionWithReadings(["{$side}_readings" => $readings]);
        $this->assertNotNull($instance->{"{$side}_priority_reading"});
        $this->assertEquals($readings[0]->type_other, $instance->{"{$side}_priority_reading"}->type_other);
    }

    /**
     * @test
     */
    public function priority_reading_is_null_for_no_readings()
    {
        $instance = $this->getElementInstance();
        $this->assertNull($instance->right_priority_reading);
        $this->assertNull($instance->left_priority_reading);
    }

    /**
     * @test
     * @dataProvider side_provider
     * @param $side
     * @param $eye_side
     */
    public function priority_combined_returns_refraction_string($side, $eye_side)
    {
        $types = OphCiExamination_Refraction_Type::model()->findAll(['order' => 'priority asc']);

        $readings = [
            $this->generateRefractionReading(['eye_id' => $eye_side, 'type_id' => $types[2]->id]),
            $this->generateRefractionReading(['eye_id' => $eye_side, 'type_id' => $types[1]->id]),
        ];

        $instance = $this->generateSavedRefractionWithReadings(["{$side}_readings" => $readings]);

        $this->assertEquals($instance->{"{$side}_priority_reading"}->refraction_display, $instance->getPriorityReadingCombined($side));
    }

    /** @test */
    public function priority_combined_empty_string_for_null()
    {
        $instance = $this->getElementInstance();
        $this->assertEquals('', $instance->getPriorityReadingCombined('right'));
        $this->assertEquals('', $instance->getPriorityReadingCombined('left'));
    }

    /**
     * @test
     * @dataProvider side_provider
     * @param $side
     * @param $eye_side
     */
    public function priority_data_attributes_returns_correct_reading_data($side, $eye_side)
    {
        $types = OphCiExamination_Refraction_Type::model()->findAll(['order' => 'priority asc']);

        $readings = [
            $this->generateRefractionReading(['eye_id' => $eye_side, 'type_id' => $types[2]->id]),
            $this->generateRefractionReading(['eye_id' => $eye_side, 'type_id' => $types[1]->id]),
        ];

        $instance = $this->generateSavedRefractionWithReadings(["{$side}_readings" => $readings]);
        $priority_reading = $instance->{"{$side}_priority_reading"};
        $data_result = $instance->getPriorityReadingDataAttributes($side);

        foreach (['sphere', 'cylinder', 'axis'] as $attr) {
            $this->assertArrayHasKey($attr, $data_result);
            $this->assertEquals($priority_reading->$attr, $data_result[$attr]);
        }
        $this->assertArrayHasKey('type', $data_result);
        $this->assertEquals($priority_reading->type_display, $data_result['type']);
    }

    /** @test */
    public function priority_data_attributes_returns_keyed_empty_array()
    {
        $instance = $this->getElementInstance();
        foreach (['right', 'left'] as $side) {
            $data_result = $instance->getPriorityReadingDataAttributes($side);
            foreach (['sphere', 'cylinder', 'axis', 'type'] as $key) {
                $this->assertArrayHasKey($key, $data_result);
                $this->assertNull($data_result[$key]);
            }
        }
    }

    public function letter_string_provider()
    {
        return [
            [
                [
                    'foo'
                ],
                [],
                'Refraction: R: foo, L: NR'
            ],
            [
                [
                    'foo', 'bar', 'moo'
                ],
                [],
                'Refraction: R: foo, bar, moo, L: NR'
            ],
            [
                [],
                [
                    'foo', 'bar'
                ],
                'Refraction: R: NR, L: foo, bar'
            ],
            [
                [
                    'foo', 'bar'
                ],
                [
                    'baz'
                ],
                'Refraction: R: foo, bar, L: baz'
            ],
            [
                [],
                [],
                'Refraction: R: NR, L: NR'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider letter_string_provider
     * @param $right_strings
     * @param $left_strings
     * @param $expected
     */
    public function test_letter_string($right_strings, $left_strings, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->right_readings = $this->mockStringifiedReading($right_strings);
        $instance->left_readings = $this->mockStringifiedReading($left_strings);

        $this->assertEquals($expected, $instance->letter_string);
    }

    public function test_load_from_existing()
    {
        $original_element = $this->getElementInstance();
        $original_element->event_id = $this->getEventToSaveWith()->getPrimaryKey();

        $reading_data = ['right' => [], 'left' => []];

        foreach (['right', 'left'] as $side) {
            $reading_data[$side] = $this->generateRefractionReadingData();
            $original_element->{"{$side}_readings"} = [$reading_data[$side]];

            $original_element->eye_id = \Eye::getIdFromName($side);
        }
        $this->assertTrue($original_element->save(), "element should save successfully.");

        $new_element = $this->getElementInstance();
        $new_element->event_id = $this->getEventToSaveWith()->getPrimaryKey();

        $new_element->loadFromExisting($original_element);
        $this->assertTrue($new_element->save(), "element should save successfully.");

        $original_element->refresh();
        $new_element->refresh();

        foreach (['right_readings', 'left_readings'] as $relation) {
            $this->assertCount(1, $original_element->$relation);
            $this->assertCount(1, $new_element->$relation);

            foreach ($original_element->$relation as $key => $original_entry) {
                $new_entry = $new_element->$relation[$key];

                $this->assertNotEquals($original_entry->id, $new_entry->id);
                $this->assertNotEquals($original_entry->element_id, $new_entry->element_id);

                foreach (['sphere', 'cylinder', 'axis', 'type_id'] as $attr) {
                    $this->assertEquals($original_entry->$attr, $new_entry->$attr);
                }
            }
        }
    }

    protected function mockStringifiedReading($strings)
    {
        return array_map(function ($str) {
            $reading = $this->createMock(OphCiExamination_Refraction_Reading::class);
            $reading->method('__toString')
                ->will($this->returnValue($str));
            return $reading;
        }, $strings);
    }
}
