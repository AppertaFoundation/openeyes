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

use OEModule\OphCiExamination\models\NinePositions;
use OEModule\OphCiExamination\models\NinePositions_AlignmentForGaze;
use OEModule\OphCiExamination\models\NinePositions_MovementForGaze;
use OEModule\OphCiExamination\models\NinePositions_Reading;
use OEModule\OphCiExamination\tests\traits\InteractsWithNinePositions;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureAttributesToTest;

/**
 * Class NinePositionsReadingTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\NinePositions_Reading
 * @group sample-data
 * @group strabismus
 * @group nine-positions
 */
class NinePositions_ReadingTest extends \ModelTestCase
{
    use \WithTransactions;
    use \HasStandardRelationsTests;
    use HasWithHeadPostureAttributesToTest;
    use InteractsWithNinePositions;

    protected $element_cls = NinePositions_Reading::class;

    /** @test */
    public function eyedraw_attributes_required()
    {
        $instance = $this->getElementInstance();
        $this->assertAttributeInvalid($instance, 'left_eyedraw', 'cannot be blank');
        $this->assertAttributeInvalid($instance, 'right_eyedraw', 'cannot be blank');
    }

    /** @test */
    public function alignments_relation()
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();
        $relation_name = "alignments";
        $this->assertArrayHasKey($relation_name, $relations);
        $this->assertEquals(\CHasManyRelation::class, $relations[$relation_name][0]);
        $this->assertEquals(NinePositions_AlignmentForGaze::class, $relations[$relation_name][1]);
    }

    /** @test */
    public function check_attribute_safety()
    {
        $instance = $this->getElementInstance();
        $safe = $instance->getSafeAttributeNames();

        foreach ([
            'with_correction', 'with_head_posture', 'wong_supine_positive', 'hess_chart', 'right_dvd', 'left_dvd',
            'right_eyedraw', 'left_eyedraw', 'full_ocular_movement', 'comments', 'alignments', 'movements']
            as $attr
        ) {
            $this->assertContains($attr, $safe, "{$attr} must be set to be safe for saving");
        }
    }

    /** @test */
    public function movements_relations()
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();
        $relation_name = "movements";
        $this->assertArrayHasKey($relation_name, $relations);
        $this->assertEquals(\CHasManyRelation::class, $relations[$relation_name][0]);
        $this->assertEquals(NinePositions_MovementForGaze::class, $relations[$relation_name][1]);
    }

    /** @test */
    public function retrieves_specific_alignment()
    {
        $instance = $this->getElementInstance();
        $alignment = $this->generateAlignmentForGaze();
        $gaze_type = $alignment->gaze_type;

        $instance->alignments = [$alignment];

        $this->assertEquals($alignment, $instance->getAlignmentForGazeType($gaze_type));

        $other_gaze_type = $this->faker->randomElement(NinePositions_AlignmentForGaze::GAZE_TYPES);
        while ($other_gaze_type === $gaze_type) {
            $other_gaze_type = $this->faker->randomElement(NinePositions_AlignmentForGaze::GAZE_TYPES);
        }

        $this->assertNull($instance->getAlignmentForGazeType($other_gaze_type));
    }

    /** @test */
    public function retrieves_specific_movement()
    {
        $instance = $this->getElementInstance();
        $movement = $this->generateMovementForGaze();
        $gaze_type = $movement->gaze_type;
        $side = strtolower(\Eye::methodPostFix($movement->eye_id));

        $instance->movements = [$movement];

        $this->assertEquals($movement, $instance->getMovementForGazeType($side, $gaze_type));

        $other_gaze_type = $this->faker->randomElement(NinePositions_MovementForGaze::GAZE_TYPES);
        while ($other_gaze_type === $gaze_type) {
            $other_gaze_type = $this->faker->randomElement(NinePositions_MovementForGaze::GAZE_TYPES);
        }

        $this->assertNull($instance->getMovementForGazeType($side, $other_gaze_type));
    }

    /** @test */
    public function auto_saves_alignment_for_gaze_data()
    {
        $instance = $this->getSaveableElementInstance();

        $alignment_data = $this->generateAlignmentForGazeData();
        $instance->alignments = [$alignment_data];
        $instance->full_ocular_movement = true; // make instance valid

        $this->assertTrue($instance->validate(), "Invalid: " . print_r($instance->getErrors(), true));
        $this->assertTrue($instance->save(), "element should save successfully.");

        $savedInstance = NinePositions_Reading::model()->findByPk($instance->getPrimaryKey());

        $this->assertCount(1, $savedInstance->alignments);
        foreach ($alignment_data as $attr => $val) {
            $this->assertEquals($val, $savedInstance->alignments[0]->$attr);
        }
    }

    /** @test */
    public function auto_saves_movement_for_gaze_data()
    {
        $instance = $this->getSaveableElementInstance();
        $movement_data = $this->generateMovementForGazeData();
        $instance->movements = [$movement_data];

        $this->assertTrue($instance->validate(), "Invalid: " . print_r($instance->getErrors(), true));
        $this->assertTrue($instance->save(), "element should save successfully.");

        $savedInstance = NinePositions_Reading::model()->findByPk($instance->getPrimaryKey());

        $this->assertCount(1, $savedInstance->movements);
        foreach ($movement_data as $attr => $val) {
            $this->assertEquals($val, $savedInstance->movements[0]->$attr);
        }
    }

    /** @test */
    public function with_correction_attribute_validation()
    {
        $instance = $this->getElementInstance();
        $instance->with_correction = 'foo';

        $this->assertAttributeInvalid($instance, 'with_correction', 'invalid');

        $instance->with_correction = $this->faker->randomElement([NinePositions_Reading::WITH_CORRECTION, NinePositions_Reading::WITHOUT_CORRECTION]);
        $this->assertAttributeValid($instance, 'with_correction');
    }

    /** @test */
    public function dvd_can_be_integer_only()
    {
        $instance = $this->getElementInstance();
        foreach (['right', 'left'] as $side) {
            $attribute = "{$side}_dvd";
            $instance->$attribute = $this->faker->word();

            $this->assertAttributeInvalid($instance, $attribute, 'number');

            $instance->$attribute = $this->faker->numberBetween(0, 12);

            $this->assertAttributeValid($instance, $attribute);
        }
    }

    /** @test */
    public function display_head_posture()
    {
        $instance = $this->getElementInstance();

        $this->assertEmpty($instance->display_with_head_posture);

        $selected = $this
            ->faker
            ->randomElement($instance->with_head_posture_options);
        $instance->with_head_posture = $selected['id'];

        $this->assertEquals(
            $instance->getAttributeLabel('with_head_posture') . ': ' . $selected['name'],
            $instance->display_with_head_posture
        );
    }

    /** @test */
    public function display_with_correction()
    {
        $instance = $this->getElementInstance();
        $this->assertEmpty($instance->display_with_correction);

        $opts = [
            $instance::WITH_CORRECTION => 'Glasses',
            $instance::WITHOUT_CORRECTION => 'No glasses'
        ];
        $selected = $this
            ->faker
            ->randomElement(array_keys($opts));
        $instance->with_correction = $selected;

        $this->assertEquals(
            $opts[$selected],
            $instance->display_with_correction
        );
    }

    /** @test */
    public function display_wong_supine_positive()
    {
        $instance = $this->getElementInstance();
        $this->assertNotEmpty($instance->getAttributeLabel('wong_supine_positive'));
        $this->assertEmpty($instance->display_wong_supine_positive);

        $instance->wong_supine_positive = false;
        $this->assertEmpty($instance->display_wong_supine_positive);

        $instance->wong_supine_positive = true;
        $this->assertEquals(
            $instance->getAttributeLabel('wong_supine_positive'),
            $instance->display_wong_supine_positive
        );
    }

    /** @test */
    public function display_hess_chart()
    {
        $instance = $this->getElementInstance();
        $this->assertNotEmpty($instance->getAttributeLabel('hess_chart'));
        $this->assertEmpty($instance->display_hess_chart);

        $instance->hess_chart = false;
        $this->assertEmpty($instance->display_hess_chart);

        $instance->hess_chart = true;
        $this->assertEquals(
            $instance->getAttributeLabel('hess_chart'),
            $instance->display_hess_chart
        );
    }

    /** @test */
    public function display_full_ocular_movement()
    {
        $instance = $this->getElementInstance();
        $this->assertNotEmpty($instance->getAttributeLabel('full_ocular_movement'));
        $this->assertEmpty($instance->full_ocular_movement);

        $instance->full_ocular_movement = false;
        $this->assertEmpty($instance->display_full_ocular_movement);

        $instance->full_ocular_movement = true;
        $this->assertEquals(
            $instance->getAttributeLabel('full_ocular_movement'),
            $instance->display_full_ocular_movement
        );
    }

    /** @test */
    public function if_no_movement_values_recorded_full_ocular_movement_must_be_true()
    {
        $instance = $this->getSaveableElementInstance();

        // have at least one alignment data to ensure this doesn't prevent full ocular movement validation
        $alignment_data = $this->generateAlignmentForGazeData();
        $instance->alignments = [$alignment_data];

        $this->assertAttributeInvalid($instance, 'full_ocular_movement', 'must be set');
    }

    /** @test */
    public function full_ocular_movement_cannot_be_set_when_movement_value_recorded()
    {
        $instance = $this->getSaveableElementInstance();

        $movement_data = $this->generateMovementForGazeData();
        $instance->movements = [$movement_data];
        $instance->full_ocular_movement = true;

        $this->assertAttributeInvalid($instance, 'full_ocular_movement', 'cannot be set');
    }

    protected function getSaveableElementInstance()
    {
        $instance = $this->getElementInstance();
        $instance->left_eyedraw = $instance->right_eyedraw = 'foo'; // don't care about ed values

        // need to attach an element for the reading.
        $tmpElement = new NinePositions();
        $this->assertTrue($tmpElement->save(false));

        $instance->element_id = $tmpElement->id;

        return $instance;
    }

    /** @test */
    public function clone_should_unset_id_and_element_id_attributes()
    {
        $instance = $this->getElementInstance();
        $instance->id = 123;
        $instance->element_id = 456;

        $clone = clone $instance;

        $this->assertNull($clone->id);
        $this->assertNull($clone->element_id);
    }
}
