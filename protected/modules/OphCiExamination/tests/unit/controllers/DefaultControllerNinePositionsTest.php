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

namespace OEModule\OphCiExamination\tests\unit\controllers;


use OEModule\OphCiExamination\models\NinePositions;
use OEModule\OphCiExamination\models\NinePositions_Reading;
use OEModule\OphCiExamination\tests\traits\InteractsWithNinePositions;

/**
 * Class DefaultControllerNinePositionsTest
 * @package OEModule\OphCiExamination\tests\unit\controllers
 * @group sample-data
 * @group strabismus
 * @group nine-positions
 */
class DefaultControllerNinePositionsTest extends BaseDefaultControllerTest
{
    use \WithTransactions;
    use InteractsWithNinePositions;

    /** @test */
    public function saving_a_simple_element()
    {
        $reading_data = $this->generateNinePositionsReadingData();
        // prevent error requiring HeadPosture element
        $reading_data['with_head_posture'] = NinePositions_Reading::$WITHOUT_HEAD_POSTURE;

        $saved_element = $this->createElementWithDataWithController([
            'readings' => [
                $reading_data
            ]
        ]);

        $this->assertNotNull($saved_element);
        $this->assertInstanceOf(NinePositions::class, $saved_element);
        $this->assertCount(1, $saved_element->readings);

        $saved_reading = $saved_element->readings[0];
        $direct_attrs = array_filter(
            array_keys($reading_data),
            function ($k) {
                return !in_array($k, ['alignments', 'movements']);
            }
        );
        foreach ($direct_attrs as $attr) {
            $this->assertEquals(
                $reading_data[$attr],
                $saved_reading->$attr,
                "{$attr} should be set to {$reading_data[$attr]}"
            );
        }

        $this->assertCount(count($reading_data['alignments']), $saved_reading->alignments);
        foreach ($reading_data['alignments'] as $alignment) {
            $saved_alignment = $saved_reading->getAlignmentForGazeType($alignment['gaze_type']);
            foreach ($alignment as $attr => $value) {
                $this->assertEquals($value, $saved_alignment[$attr], "{$attr} should be {$value} for {$alignment['gaze_type']}");
            }
        }

        $this->assertCount(count($reading_data['movements']), $saved_reading->movements);
        foreach ($reading_data['movements'] as $movement) {
            $saved_movement = $saved_reading->getMovementForGazeType($this->sideStringFromEyeId($movement['eye_id']), $movement['gaze_type']);
            foreach ($movement as $attr => $value) {
                $this->assertEquals($value, $saved_movement[$attr], "{$attr} should be {$value} for {$movement['gaze_type']}");
            }
        }
    }

    /**
     * Wrapper for full request cycle to mimic POST-ing the given data
     * to the controller.
     *
     * @param $data
     * @return mixed
     */
    protected function createElementWithDataWithController($data)
    {
        $model_name = \CHtml::modelName(NinePositions::class);
        $_POST[$model_name] = $data;

        $event_id = $this->performCreateRequestForRandomPatient();

        return NinePositions::model()->findByAttributes(['event_id' => $event_id]);
    }

    protected function sideStringFromEyeId($eyeId)
    {
        return [
            \Eye::RIGHT => 'right',
            \Eye::LEFT => 'left'
        ][$eyeId] ?? null;
    }
}
