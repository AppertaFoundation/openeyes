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
use OEModule\OphCiExamination\models\NinePositions_Reading;
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

    protected function getElementInstanceWithHeadPostureEntry()
    {
        $instance = $this->getElementInstance();
        $reading = new NinePositions_Reading();
        $reading->with_head_posture = NinePositions_Reading::$WITH_HEAD_POSTURE;
        $instance->readings = [$reading];
        return [$instance, 'readings.0'];
    }
}
