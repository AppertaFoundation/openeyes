<?php

namespace OEModule\OphCiExamination\tests\unit\models;

use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityFixation;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityOccluder;
use OEModule\OphCiExamination\tests\traits\InteractsWithVisualAcuity;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureAttributesToTest;

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

abstract class BaseVisualAcuityReadingTest extends \ModelTestCase
{
    use HasWithHeadPostureAttributesToTest;
    use \HasRelationOptionsToTest;
    use InteractsWithVisualAcuity;
    use \WithFaker;

    abstract public function belongs_to_relations(): array;

    abstract public function belongs_to_relations_with_options(): array;

    /**
     * @test
     * @dataProvider belongs_to_relations
     */
    public function relations_defined($relation, $relation_cls)
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();

        $this->assertArrayHasKey($relation, $relations);
        $this->assertEquals(\CBelongsToRelation::class, $relations[$relation][0]);
        $this->assertEquals($relation_cls, $relations[$relation][1]);
    }

    /**
     * @test
     * @dataProvider belongs_to_relations_with_options
     */
    public function reading_options($relation, $relation_cls)
    {
        $instance = $this->getElementInstance();

        $this->assertOptionsAreRetrievable($instance, $relation, $relation_cls);
    }

    public function is_right_provider()
    {
        return [
            [OphCiExamination_VisualAcuity_Reading::RIGHT, true],
            [OphCiExamination_VisualAcuity_Reading::LEFT, false],
            [null, false]
        ];
    }

    /**
     * @test
     * @dataProvider is_right_provider
     */
    public function isRight($side, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->side = $side;

        $this->assertEquals($expected, $instance->isRight());
    }

    public function is_left_provider()
    {
        return [
            [OphCiExamination_VisualAcuity_Reading::LEFT, true],
            [OphCiExamination_VisualAcuity_Reading::RIGHT, false],
            [null, false]
        ];
    }

    /**
     * @test
     * @dataProvider is_left_provider
     */
    public function isLeft($side, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->side = $side;

        $this->assertEquals($expected, $instance->isLeft());
    }

    /** @test */
    public function closest_with_no_units_returns_recorded_unit_value()
    {
        $unit = $this->getStandardVisualAcuityUnit();
        $recorded_unit_value = $this->faker->randomElement($unit->selectableValues);

        $reading = $this->getElementInstance();
        $reading->unit_id = $unit->id;

        $this->assertEquals($recorded_unit_value->id, $reading->getClosest($recorded_unit_value->base_value)->id);
    }

    /** @test */
    public function convert_to_with_no_units_returns_recorded_value()
    {
        $unit = $this->getStandardVisualAcuityUnit();
        $recorded_unit_value = $this->faker->randomElement($unit->selectableValues);

        $reading = $this->getElementInstance();
        $reading->unit_id = $unit->id;

        $this->assertEquals($recorded_unit_value->value, $reading->convertTo($recorded_unit_value->base_value));
    }

    /** @test */
    public function display_value_uses_convert_to_with_reading_properties()
    {
        $instance = $this->getMockBuilder($this->element_cls)
            ->disableOriginalConstructor()
            ->setMethods(['convertTo'])
            ->getMock();
        $instance->unit_id = 'foo';
        $instance->value = 'bar';

        $instance->expects($this->once())
            ->method('convertTo')
            ->with($this->equalTo('bar'), $this->equalTo('foo'))
            ->willReturn('baz');

        $this->assertEquals('baz', $instance->display_value);
    }
}