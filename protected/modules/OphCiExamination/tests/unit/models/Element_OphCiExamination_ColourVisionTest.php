<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\tests\unit\models;


use ComponentStubGenerator;
use OEModule\OphCiExamination\models\Element_OphCiExamination_ColourVision;
use OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Method;
use OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Reading;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasSidedModelAssertions;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureEntriesToTest;

/**
 * Class Element_OphCiExamination_ColourVisionTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_ColourVision
 * @group sample-data
 * @group strabismus
 * @group colour-vision
 */
class Element_OphCiExamination_ColourVisionTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;
    use \WithFaker;
    use HasSidedModelAssertions;

    protected $element_cls = Element_OphCiExamination_ColourVision::class;

    /** @test */
    public function check_entries_relation()
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();

        foreach (["", "left_", "right_"] as $prefix) {
            $this->assertArrayHasKey("{$prefix}readings", $relations);
            $this->assertEquals(\CHasManyRelation::class, $relations["{$prefix}readings"][0]);
            $this->assertEquals(OphCiExamination_ColourVision_Reading::class, $relations["{$prefix}readings"][1]);
        }
    }

    /** @test */
    public function attribute_safety()
    {
        $instance = $this->getElementInstance();
        $safe = $instance->getSafeAttributeNames();

        $this->assertContains('event_id', $safe);
        $this->assertNotContains('readings', $safe, 'readings should be populated through left and right attributes');
        $this->assertContains('left_readings', $safe);
        $this->assertContains('right_readings', $safe);
    }

    /** @test */
    public function left_side_validation()
    {
        $instance = $this->getElementInstance();
        $instance->eye_id = \Eye::LEFT;

        $this->assertAttributeInvalid($instance, 'left_readings', 'cannot be blank');
        $this->assertEmpty($instance->getErrors('right_readings'));
    }

    /** @test */
    public function right_side_validation()
    {
        $instance = $this->getElementInstance();
        $instance->eye_id = \Eye::RIGHT;

        $this->assertAttributeInvalid($instance, 'right_readings', 'cannot be blank');
        $this->assertEmpty($instance->getErrors('left_readings'));
    }

    /** @test */
    public function both_side_validation()
    {
        $instance = $this->getElementInstance();
        $instance->eye_id = \Eye::BOTH;

        $this->assertAttributeInvalid($instance, 'left_readings', 'cannot be blank');
        $this->assertAttributeInvalid($instance, 'right_readings', 'cannot be blank');
    }

    /** @test */
    public function unique_method_validation()
    {
        $instance = $this->getElementInstance();
        $instance->eye_id = \Eye::LEFT;

        // create two readings with random values for the same reading method
        $method = $this->getRandomLookup(OphCiExamination_ColourVision_Method::class);
        $method_values = $method->values;

        $instance->left_readings = array_map(function($reading) use ($method_values) {
            $reading->value_id = $method_values[array_rand($method_values)]->id;
            return $reading;
        }, [new OphCiExamination_ColourVision_Reading(), new OphCiExamination_ColourVision_Reading()]);

        // this should not be allowed
        $this->assertAttributeInvalid($instance, 'left_readings', 'must only have unique reading methods');
    }

    /** @test */
    public function left_readings_are_validated()
    {
        $this->assertSidedRelationValidated(
            $this->element_cls,
            OphCiExamination_ColourVision_Reading::class,
            "left",
            "left_readings"
        );
    }

    /** @test */
    public function right_readings_are_validated()
    {
        $this->assertSidedRelationValidated(
            $this->element_cls,
            OphCiExamination_ColourVision_Reading::class,
            "right",
            "right_readings"
        );
    }

    public function extractPkFromModels($models)
    {
        return array_map(function($model) {
            return $model->getPrimaryKey();
        }, $models);
    }

    public function testGetUnusedReadingMethods()
    {
        $test = new Element_OphCiExamination_ColourVision();

        $used_method = $this->getRandomLookup(OphCiExamination_ColourVision_Method::class);
        $test->left_readings = array(ComponentStubGenerator::generate(OphCiExamination_ColourVision_Reading::class, ['method' => $used_method]));

        $this->assertNotContains($used_method->id, $this->extractPkFromModels($test->getUnusedReadingMethods('left')), 'Left methods should be restricted');
        $this->assertContains($used_method->id, $this->extractPkFromModels($test->getUnusedReadingMethods('right')), 'Right should return both methods');
    }
}
