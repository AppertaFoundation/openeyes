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

/**
 * Class NinePositions_MovementTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\NinePositions_Movement
 * @group sample-data
 * @group strabismus
 * @group nine-positions
 */
class NinePositions_MovementTest extends \ModelTestCase
{
    use \WithFaker;

    protected $element_cls = NinePositions_Movement::class;

    /** @test */
    public function name_max_length()
    {
        $instance = $this->getElementInstance();
        $too_long = rand(8, 128);
        $instance->name = $this->faker->regexify('[A-Za-z0-9]{' . $too_long . '}');
        $this->assertAttributeInvalid($instance, 'name', 'too long');
    }

    /** @test */
    public function has_lookup_behaviour()
    {
        $instance = $this->getElementInstance();
        $this->assertContains(\LookupTable::class, $instance->behaviors());
    }

    /** @test */
    public function stringification()
    {
        $instance = $this->getElementInstance();
        $instance->name = $this->faker->regexify('[A-Za-z0-9]{1,7}');
        $this->assertEquals($instance->name, (string) $instance);
    }
}
