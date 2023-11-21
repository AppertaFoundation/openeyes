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
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureEntriesToTest;

/**
 * Class NinePositionsTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\NinePositions
 * @group sample-data
 * @group strabismus
 * @group nine-positions
 */
class NinePositionsTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;
    use HasWithHeadPostureEntriesToTest;
    use InteractsWithNinePositions;

    protected $element_cls = NinePositions::class;

    /** @test */
    public function readings_relation()
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();

        $this->assertArrayHasKey('readings', $relations);
        $this->assertEquals(\CHasManyRelation::class, $relations['readings'][0]);
        $this->assertEquals(NinePositions_Reading::class, $relations['readings'][1]);
    }

    /** @test */
    public function a_reading_is_required()
    {
        $instance = $this->getElementInstance();
        $instance->readings = [];
        $this->assertAttributeInvalid($instance, 'readings', 'cannot be blank');
    }

    /** @test */
    public function readings_are_validated()
    {
        $instance = $this->getElementInstance();

        $reading_mock = $this->createInvalidModelMock(NinePositions_Reading::class);
        $instance->readings = [$reading_mock];

        $this->assertFalse($instance->validate());
        $this->assertArrayHasKey('readings', $instance->getErrors());
    }

    /** @test */
    public function test_load_from_existing()
    {
        $original_element = $this->generateSavedNinePositionsWithReadings(2);

        $new_element = new NinePositions();
        $new_element->loadFromExisting($original_element);

        $original_element->refresh();

        $this->assertCount(2, $original_element->readings);
        $this->assertCount(2, $new_element->readings);

        foreach ($original_element->readings as $index => $original_reading) {
            $new_reading = $new_element->readings[$index];

            $this->assertReadings($original_reading, $new_reading);
            $this->assertAligmentForGaze($original_reading->alignments, $new_reading->alignments);
            $this->assertMovementForGaze($original_reading->movements, $new_reading->movements);
        }
    }

    /** @test */
    private function assertReadings(NinePositions_Reading $original_reading, NinePositions_Reading $new_reading): void
    {
        $this->assertNotEquals($original_reading->id, $new_reading->id);
        $this->assertNotEquals($original_reading->element_id, $new_reading->element_id);

        $attributes = [
            'with_correction',
            'with_head_posture',
            'wong_supine_positive',
            'hess_chart',
            'right_dvd',
            'left_dvd',
            'right_eyedraw',
            'left_eyedraw',
            'full_ocular_movement',
            'comments',
        ];

        foreach($attributes as $attr) {
            $this->assertEquals($original_reading->$attr, $new_reading->$attr);
        }
    }

    private function assertAligmentForGaze(array $original_alignments, array $new_alignments): void
    {
        $this->assertEquals(count($original_alignments), count($new_alignments));

        $attributes = [
            'gaze_type',
            'horizontal_angle',
            'horizontal_e_deviation_id',
            'horizontal_x_deviation_id',
            'horizontal_prism_position',
            'vertical_angle',
            'vertical_deviation_id',
            'vertical_prism_position'
        ];

        foreach ($original_alignments as $index => $original_alignment) {
            $new_alignment = $new_alignments[$index];
            $this->assertNotEquals($original_alignment->reading_id, $new_alignment->reading_id);

            foreach($attributes as $attr) {
                $this->assertEquals($original_alignment->$attr, $new_alignment->$attr, $attr);
            }
        }
    }

    private function assertMovementForGaze(array $original_movements, array $new_movements): void
    {
        $this->assertEquals(count($original_movements), count($new_movements));

        $attributes = [
            'gaze_type',
            'movement_id',
            'eye_id',
        ];

        foreach ($original_movements as $index => $original_movement) {
            $new_movement = $new_movements[$index];
            $this->assertNotEquals($original_movement->reading_id, $new_movement->reading_id);

            foreach($attributes as $attr) {
                $this->assertEquals($original_movement->$attr, $new_movement->$attr);
            }
        }
    }

    protected function getElementInstanceWithHeadPostureEntry()
    {
        $instance = $this->getElementInstance();
        $reading = new NinePositions_Reading();
        $reading->with_head_posture = NinePositions_Reading::$WITH_HEAD_POSTURE;
        $instance->readings = [$reading];
        return [$instance, 'readings.0'];
    }
}
