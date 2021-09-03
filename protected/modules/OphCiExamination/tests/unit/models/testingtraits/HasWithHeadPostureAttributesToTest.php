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

namespace OEModule\OphCiExamination\tests\unit\models\testingtraits;

use OEModule\OphCiExamination\models\traits\HasWithHeadPosture as HasWithHeadPostureTrait;

/**
 * Trait HasWithHeadPostureAttributesToTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models\testingtraits
 */
trait HasWithHeadPostureAttributesToTest
{
    public function head_posture_validation_provider()
    {
        return [
            [HasWithHeadPostureTrait::$WITH_HEAD_POSTURE, true],
            [HasWithHeadPostureTrait::$WITHOUT_HEAD_POSTURE, true],
            ['', true],
            ['foo', false]
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @test
     * @dataProvider head_posture_validation_provider
     */
    public function check_with_head_posture_validation($value, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->with_head_posture = $value;
        $this->assertEquals($expected, $instance->validate(['with_head_posture']));
    }

    /** @test */
    public function uses_head_posture_trait()
    {
        $instance = $this->getElementInstance();
        $this->assertContains(HasWithHeadPostureTrait::class, \OEDbTestCase::classUsesRecursive($instance));
    }
}