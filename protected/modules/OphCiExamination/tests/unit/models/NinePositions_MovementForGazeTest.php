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

use OEModule\OphCiExamination\models\NinePositions_Movement;
use OEModule\OphCiExamination\models\NinePositions_MovementForGaze;
use OEModule\OphCiExamination\models\NinePositions_Reading;

/**
 * Class NinePositions_MovementForGazeTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\NinePositions_MovementForGaze
 * @group sample-data
 * @group strabismus
 * @group nine-positions
 */
class NinePositions_MovementForGazeTest extends \ModelTestCase
{
    use \HasRelationOptionsToTest;
    use \HasModelAssertions;
    use \WithFaker;

    protected $element_cls = NinePositions_MovementForGaze::class;


    public function auto_belongs_to_relations_provider()
    {
        return [
            ['movement', 'movement_id', NinePositions_Movement::class]
        ];
    }

    /**
     * @test
     * @dataProvider auto_belongs_to_relations_provider
     * @param $relation
     * @param $attribute
     * @param $cls
     */
    public function auto_belongs_to_relations_defined($relation, $attribute, $cls)
    {
        $this->assertBelongsToCompletelyDefined($relation, $attribute, $cls);
    }

    /** @test */
    public function gaze_type_required()
    {
        $instance = $this->getElementInstance();
        $this->assertAttributeInvalid($instance, 'gaze_type', 'blank');
    }

    /** @test */
    public function invalid_gaze_type_triggers_validation_error()
    {
        $instance = $this->getElementInstance();
        $instance->gaze_type = $this->faker->word();
        $this->assertAttributeInvalid($instance, 'gaze_type', 'invalid');
    }

    /** @test */
    public function is_for_side_left()
    {
        $instance = $this->getElementInstance();
        $instance->eye_id = \Eye::LEFT;
        $this->assertTrue(!$instance->isForSide('right'));
    }

    /** @test */
    public function is_for_side_right()
    {
        $instance = $this->getElementInstance();
        $instance->eye_id = \Eye::RIGHT;
        $this->assertTrue(!$instance->isForSide('left'));
    }
}
