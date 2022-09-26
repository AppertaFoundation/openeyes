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

namespace OEModule\OphCiExamination\tests\unit\models\traits;

use OEModule\OphCiExamination\models\interfaces\BEOSidedData;
use OEModule\OphCiExamination\models\traits\HasBEOSidedData;

/**
 * Class HasBEOSidedDataTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets\traits
 * @covers \OEModule\OphCiExamination\models\traits\HasBEOSidedData
 * @group sample-data
 * @group strabismus
 * @group visual-acuity
 */
class HasBEOSidedDataTest extends \OEDbTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->createTestTable('test_beo_sided_data', [
            'eye_id' => 'int(10) unsigned'
        ]);
    }

    public function side_values_provider()
    {
        return [
            [BEOSidedData::RIGHT, true, false, false],
            [BEOSidedData::LEFT, false, true, false],
            [BEOSidedData::BEO, false, false, true],
            [BEOSidedData::RIGHT | BEOSidedData::LEFT, true, true, false],
            [BEOSidedData::RIGHT | BEOSidedData::BEO, true, false, true],
            [BEOSidedData::LEFT | BEOSidedData::BEO, false, true, true],
            [BEOSidedData::RIGHT | BEOSidedData::LEFT | BEOSidedData::BEO, true, true, true],
        ];
    }

    /**
     * @test
     * @dataProvider side_values_provider
     * @param $value
     * @param $has_right
     * @param $has_left
     */
    public function side_values_checking($value, $has_right, $has_left, $has_beo)
    {
        $instance = new HasBEOSidedDataTest_TestClass();
        $instance->eye_id = $value;
        $this->assertEquals($has_right, $instance->hasRight());
        $this->assertEquals($has_left, $instance->hasLeft());
        $this->assertEquals($has_beo, $instance->hasBeo());
    }

    public function sides_provider()
    {
        return [
            ['right'],
            ['left'],
            ['beo']
        ];
    }

    /**
     * @param $side
     * @test
     * @dataProvider sides_provider
     */
    public function setting_side($side)
    {
        $instance = new HasBEOSidedDataTest_TestClass();
        $instance->eye_id = null;
        $instance->{"setHas" . ucfirst($side)}();

        $this->assertTrue($instance->{"has" . ucfirst($side)}());
    }

    public function removalProvider()
    {
        return [
            [BEOSidedData::RIGHT, 'setDoesNotHaveRight', [], ['hasRight', 'hasLeft', 'hasBeo']],
            [BEOSidedData::LEFT, 'setDoesNotHaveRight', ['hasLeft'], ['hasRight', 'hasBeo']],
            [BEOSidedData::LEFT | BEOSidedData::BEO, 'setDoesNotHaveRight', ['hasLeft', 'hasBeo'], ['hasRight']],
            [BEOSidedData::RIGHT | BEOSidedData::LEFT | BEOSidedData::BEO, 'setDoesNotHaveRight', ['hasLeft', 'hasBeo'], ['hasRight']],
            [BEOSidedData::RIGHT, 'setDoesNotHaveLeft', ['hasRight'], ['hasLeft', 'hasBeo']],
            [BEOSidedData::LEFT, 'setDoesNotHaveLeft', [], ['hasRight', 'hasLeft', 'hasBeo']],
            [BEOSidedData::LEFT | BEOSidedData::BEO, 'setDoesNotHaveLeft', ['hasBeo'], ['hasRight', 'hasLeft']],
            [BEOSidedData::RIGHT | BEOSidedData::LEFT | BEOSidedData::BEO, 'setDoesNotHaveLeft', ['hasRight', 'hasBeo'], ['hasLeft']],
            [BEOSidedData::RIGHT, 'setDoesNotHaveBeo', ['hasRight'], ['hasLeft', 'hasBeo']],
            [BEOSidedData::LEFT, 'setDoesNotHaveBeo', ['hasLeft'], ['hasRight', 'hasBeo']],
            [BEOSidedData::LEFT | BEOSidedData::BEO, 'setDoesNotHaveBeo', ['hasLeft'], ['hasRight', 'hasBeo']],
            [BEOSidedData::RIGHT | BEOSidedData::LEFT | BEOSidedData::BEO, 'setDoesNotHaveBeo', ['hasRight', 'hasLeft'], ['hasBeo']]
        ];
    }

    /**
     * @test
     * @dataProvider removalProvider
     */
    public function removingSide($initial, $removeMethod, $expectedTrue, $expectedFalse)
    {
        $instance = new HasBEOSidedDataTest_TestClass();
        $instance->eye_id = $initial;
        $instance->$removeMethod();

        foreach ($expectedTrue as $checkMethod) {
            $this->assertTrue($instance->$checkMethod(), "{$checkMethod} should be true");
        }

        foreach ($expectedFalse as $checkMethod) {
            $this->assertFalse($instance->$checkMethod(), "{$checkMethod} should be false");
        }
    }
}

class HasBEOSidedDataTest_TestClass extends \BaseActiveRecord
{
    use HasBEOSidedData;

    public function tableName()
    {
        return 'test_beo_sided_data';
    }
}
