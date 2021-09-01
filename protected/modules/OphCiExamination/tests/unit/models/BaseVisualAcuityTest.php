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


use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\interfaces\BEOSidedData;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;
use OEModule\OphCiExamination\tests\traits\InteractsWithVisualAcuity;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureEntriesToTest;

abstract class BaseVisualAcuityTest extends \ModelTestCase
{
    use \HasStandardRelationsTests;
    use \WithTransactions;
    use \WithFaker;
    use InteractsWithVisualAcuity;
    use HasWithHeadPostureEntriesToTest;

    // as text columns, these will be set to the empty string when nothing provided
    protected array $columns_to_skip = ['left_notes', 'right_notes'];

    public function test_has_event_relation()
    {
        $instance = $this->getElementInstance();

        $relations = $instance->relations();
        $this->assertArrayHasKey('event', $relations);
    }

    public function side_provider()
    {
        return [
            ['right', ($this->reading_cls)::RIGHT],
            ['left', ($this->reading_cls)::LEFT],
            ['beo', ($this->reading_cls)::BEO]
        ];
    }

    /**
     * @test
     * @dataProvider side_provider
     */
    public function readings_relations_defined($side, $on_value)
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();

        $relation_name = "{$side}_readings";

        $this->assertArrayHasKey($relation_name, $relations);
        $this->assertEquals(\CHasManyRelation::class, $relations[$relation_name][0]);
        $this->assertEquals($this->reading_cls, $relations[$relation_name][1]);
        $this->assertArrayHasKey('on', $relations[$relation_name]);
        $this->assertEquals("{$relation_name}.side = $on_value", $relations[$relation_name]['on']);
    }

    public function side_attribute_value_provider()
    {
        return [
            [BEOSidedData::LEFT, true],
            [BEOSidedData::RIGHT, true],
            [BEOSidedData::BEO, true],
            [BEOSidedData::RIGHT | BEOSidedData::BEO, true],
            [BEOSidedData::RIGHT | BEOSidedData::LEFT, true],
            [BEOSidedData::LEFT | BEOSidedData::BEO, true],
            [BEOSidedData::RIGHT | BEOSidedData::LEFT | BEOSidedData::BEO, true],
            ['foo', false],
            [8, false]
        ];
    }

    /**
     * @param $value
     * @param $expected_valid
     * @test
     * @dataProvider side_attribute_value_provider
     */
    public function side_attribute_validation($value, $expected_valid)
    {
        $instance = $this->getElementInstance();
        $instance->record_mode = ($this->element_cls)::RECORD_MODE_COMPLEX;
        $instance->eye_id = $value;

        if ($expected_valid) {
            $this->assertAttributeValid($instance, 'eye_id');
        } else {
            $this->assertAttributeInvalid($instance, 'eye_id', 'invalid');
        }
    }

    public function side_dataProvider()
    {
        return [
            ['beo'],
            ['left'],
            ['right']
        ];
    }

    /**
     * @test
     * @dataProvider side_dataProvider
     */
    public function side_must_be_marked_unassessable_or_have_a_reading($invalid_side)
    {
        $instance = $this->getElementInstance();
        $instance->{"setHas" . ucfirst($invalid_side)}(); // ensure side set

        // VA uses a psuedo attribute of left and right for errors, so cannot check attribute specific
        // validation error, and must check the errors array instead.
        $this->assertFalse($instance->validate());
        $errors = $instance->getErrors();
        $this->assertArrayHasKey($invalid_side, $errors);
        $this->assertRegExp("/has no data/", $errors[$invalid_side][0]);
    }

    /**
     * @test
     * @dataProvider side_dataProvider
     */
    public function side_cannot_be_unassessable_and_have_a_reading($invalid_side)
    {
        $valid_reading = $this->createValidatingModelMock($this->reading_cls);
        $instance = $this->getElementInstance();
        $instance->{"setHas" . ucfirst($invalid_side)}(); // ensure side set
        $instance->{"{$invalid_side}_readings"} = [$valid_reading];

        $unable_to_assess_attr = ($invalid_side === 'beo')
            ? 'beo_unable_to_assess'
            : $this->faker->randomElement(["{$invalid_side}_unable_to_assess", "{$invalid_side}_eye_missing"]);

        $instance->$unable_to_assess_attr = true;

        $this->assertAttributeInvalid($instance, $unable_to_assess_attr, "Cannot be ");
    }

    /**
     * @test
     * @dataProvider side_dataProvider
     */
    public function reading_methods_must_be_unique_for_side($invalid_side)
    {
        $method = $this->getRandomLookup(OphCiExamination_VisualAcuity_Method::class);
        $reading = $this->createValidatingModelMock($this->reading_cls);
        $reading->method_id = $method->id;
        $instance = $this->getElementInstance();
        $instance->{"setHas" . ucfirst($invalid_side)}(); // ensure side set

        $instance->{"{$invalid_side}_readings"} = [$reading, $reading];

        $this->assertAttributeInvalid($instance, "{$invalid_side}_readings", "Each method type can only be recorded once");
    }

    /**
     * @test
     */
    public function invalid_to_record_beo_readings_in_simple_mode()
    {
        $instance = $this->getElementInstance();
        $instance->record_mode = ($this->element_cls)::RECORD_MODE_SIMPLE;
        $instance->setHasBeo();

        $reading = $this->createValidatingModelMock($this->reading_cls);
        $instance->beo_readings = [$reading];

        $this->assertAttributeInvalid($instance, "eye_id", "Cannot record BEO in this mode.");
    }

    /** @test */
    public function default_unit_id_is_set()
    {
        $instance = $this->getMockBuilder($this->element_cls)
            ->disableOriginalConstructor()
            ->setMethods(['getSetting'])
            ->getMock();
        $instance->expects($this->any())
            ->method('getSetting')
            ->will($this->returnValueMap([
                ['unit_id', 'foo'],
            ]));

        $instance->setDefaultOptions();

        $this->assertEquals('foo', $instance->unit_id);
    }

    /** @test */
    public function unit_relation_works_with_non_db_attribute()
    {
        $unit_criteria = new \CDbCriteria();
        $unit_criteria->addColumnCondition(['is_near' => false]);
        $unit = $this->getRandomLookup(OphCiExamination_VisualAcuityUnit::class, 1, $unit_criteria);

        $instance = $this->getElementInstance();
        $instance->unit_id = $unit->id;

        $this->assertEquals($unit->id, $instance->unit->id);
    }

    /** @test */
    public function view_title_uses_unit_property_for_simple_mode()
    {
        $unit = $this->getRandomUnit();

        // chose to mock to avoid unit setting permutations for this test
        $instance = $this->getMockBuilder($this->element_cls)
            ->setMethods(['getElementTypeName', 'getUnit'])
            ->getMock();
        $instance->expects($this->any())
            ->method('getElementTypeName')
            ->willReturn('FooBar');
        $instance->expects($this->any())
            ->method('getUnit')
            ->willReturn($unit);

        $this->assertEquals("FooBar <small>{$unit->name}</small>", $instance->getViewTitle());
    }

    /**
     * When recording in complex mode, each reading has its own units, so not appropriate
     * to include in the title of the element.
     *
     * @test
     */
    public function view_title_ignores_units_when_in_complex_mode()
    {
        $unit = $this->getRandomUnit();

        // chose to mock to avoid unit setting permutations for this test
        $instance = $this->getMockBuilder($this->element_cls)
            ->setMethods(['getElementTypeName', 'getUnit'])
            ->getMock();
        $instance->expects($this->any())
            ->method('getElementTypeName')
            ->willReturn('FooBar');
        $instance->expects($this->any())
            ->method('getUnit')
            ->willReturn($unit);

        $instance->record_mode = ($this->element_cls)::RECORD_MODE_COMPLEX;

        $this->assertEquals("FooBar", $instance->getViewTitle());
    }

    /** @test */
    public function unit_property_is_derived_from_reading_units_when_set()
    {
        $instance = $this->getElementInstance();
        $side = $this->faker->randomElement(['right', 'left']);
        $unit = $this->getRandomUnit();
        $reading = new $this->reading_cls;
        $reading->unit_id = $unit->id;
        $instance->{"{$side}_readings"} = [$reading];

        $this->assertEquals($unit->id, $instance->unit->id);
    }

    /** @test */
    public function unit_property_derived_from_attribute_when_set()
    {
        $instance = $this->getElementInstance();
        $unit = $this->getRandomUnit();
        $instance->unit_id = $unit->id;

        $this->assertEquals($unit->id, $instance->unit->id);
    }

    /** @test */
    public function unit_property_falls_back_to_setting_metadata_when_no_readings_available()
    {
        $unit = $this->getRandomUnit();

        $instance = $this->getMockBuilder($this->element_cls)
            ->disableOriginalConstructor()
            ->setMethods(['getSetting'])
            ->getMock();
        $instance->expects($this->any())
            ->method('getSetting')
            ->will($this->returnValueMap([
                ['unit_id', $unit->id],
            ]));

        $this->assertEquals($unit->id, $instance->unit->id);
    }

    public function not_recorded_text_for_side_provider()
    {
        return [
            ['right', [], 'not recorded'],
            ['right', ['right_unable_to_assess' => true], 'Unable to assess'],
            ['right', ['right_unable_to_assess' => true], 'Unable to assess'],
            ['right', ['right_unable_to_assess' => true, 'right_eye_missing' => true], 'Unable to assess, Eye missing'],
            ['right', ['right_unable_to_assess' => false, 'right_eye_missing' => true], 'Eye missing'],
            ['right', ['right_behaviour_assessed' => true], 'Visual behaviour assessed'],
            ['left', [], 'not recorded'],
            ['left', ['left_unable_to_assess' => true], 'Unable to assess'],
            ['left', ['left_unable_to_assess' => true, 'left_eye_missing' => true], 'Unable to assess, Eye missing'],
            ['left', ['left_unable_to_assess' => false, 'left_eye_missing' => true], 'Eye missing'],
            ['left', ['left_behaviour_assessed' => true], 'Visual behaviour assessed'],
            ['beo', [], 'not recorded'],
            ['beo', ['beo_unable_to_assess' => true], 'Unable to assess BEO'],
            ['beo', ['beo_behaviour_assessed' => true], 'Visual behaviour assessed']
        ];
    }

    /**
     * @param $side
     * @param $element_attrs
     * @param $expected
     * @test
     * @dataProvider not_recorded_text_for_side_provider
     */
    public function not_recorded_text_for_side($side, $element_attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->{"setHas" . ucfirst($side)}();
        $instance->setAttributes($element_attrs);

        $this->assertEquals($expected, $instance->getNotRecordedTextForSide($side));
    }

    public function can_eye_have_readings_provider()
    {
        return [
            ['right', [], true],
            ['right', ['right_unable_to_assess' => true], false],
            ['right', ['right_eye_missing' => true], false],
            ['right', ['right_behaviour_assessed' => true], false],
            ['left', [], true],
            ['left', ['left_unable_to_assess' => true], false],
            ['left', ['left_eye_missing' => true], false],
            ['left', ['left_behaviour_assessed' => true], false],
            ['beo', [], true],
            ['beo', ['left_unable_to_assess'], true], # beo unaffected by settings for specific eye
            ['beo', ['beo_unable_to_assess' => true], false],
            ['beo', ['beo_behaviour_assessed' => true], false],
        ];
    }

    /**
     * @param $side
     * @param $element_attrs
     * @param $expected
     * @test
     * @dataProvider can_eye_have_readings_provider
     */
    public function can_eye_have_readings_when_has_side($side, $element_attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->{"setHas" . ucfirst($side)}();
        $instance->setAttributes($element_attrs);

        $this->assertEquals($expected, $instance->eyeCanHaveReadings($side));
    }

    public function reading_state_and_notes_provider()
    {
        return [
            ['right', [], "not recorded"],
            ['right', ['right_unable_to_assess' => true, 'right_notes' => 'foo'], 'Unable to assess foo'],
            ['right', ['right_unable_to_assess' => true, 'right_eye_missing' => true, 'right_notes' => 'foo'], 'Unable to assess, Eye missing foo'],
            ['right', ['right_behaviour_assessed' => true, 'right_notes' => 'foo bar'], 'Visual behaviour assessed foo bar'],
            ['left', [], "not recorded"],
            ['left', ['left_unable_to_assess' => true, 'left_notes' => 'foo'], 'Unable to assess foo'],
            ['left', ['left_unable_to_assess' => true, 'left_eye_missing' => true, 'left_notes' => 'foo'], 'Unable to assess, Eye missing foo'],
            ['left', ['left_behaviour_assessed' => true, 'left_notes' => 'foo bar'], 'Visual behaviour assessed foo bar'],
            ['beo', [], "not recorded"],
            ['beo', ['beo_unable_to_assess' => true, 'beo_notes' => 'foo'], 'Unable to assess BEO foo'],
            ['beo', ['beo_behaviour_assessed' => true, 'beo_notes' => 'foo bar'], 'Visual behaviour assessed foo bar'],
        ];
    }

    /**
     * @param $side
     * @param $element_attrs
     * @param $expected
     * @test
     * @dataProvider reading_state_and_notes_provider
     */
    public function reading_state_and_notes($side, $element_attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->{"setHas" . ucfirst($side)}();
        $instance->setAttributes($element_attrs);

        $this->assertEquals($expected, $instance->getReadingStateAndNotes($side));
    }

    /**
     * Get a random VA Unit that is relevant to the VA implementation
     *
     * @return mixed
     */
    abstract protected function getRandomUnit();
}
