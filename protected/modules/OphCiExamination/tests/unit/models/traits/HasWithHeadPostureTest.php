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

use ModelTestCase;
use OEModule\OphCiExamination\models\HeadPosture;
use OEModule\OphCiExamination\models\traits\HasWithHeadPosture;

/**
 * Class HasWithHeadPostureTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models\traits
 * @covers \OEModule\OphCiExamination\models\traits\HasWithHeadPosture
 * @group sample-data
 * @group strabismus
 * @group head-posture
 */
class HasWithHeadPostureTest extends \OEDbTestCase
{
    use \HasModelAssertions;

    public function setUp()
    {
        parent::setUp();

        $this->createTestTable('test_has_with_head_posture', [
            'with_head_posture' => 'boolean'
        ]);
    }

    /** @test */
    public function cannot_set_random_value_to_with_head_posture()
    {
        $instance = new HasWithHeadPosture_TestClass();
        $instance->with_head_posture = 'foo';
        $this->assertAttributeInvalid($instance, 'with_head_posture', 'is invalid');
    }

    /** @test */
    public function options_available()
    {
        $instance = new HasWithHeadPosture_TestClass();
        $this->assertCount(2, $instance->with_head_posture_options);
    }

    public function head_posture_display_provider()
    {
        return [
            [HasWithHeadPosture_TestClass::$WITH_HEAD_POSTURE, HasWithHeadPosture_TestClass::$DISPLAY_WITH_HEAD_POSTURE],
            [HasWithHeadPosture_TestClass::$WITHOUT_HEAD_POSTURE, HasWithHeadPosture_TestClass::$DISPLAY_WITHOUT_HEAD_POSTURE],
            ['', '-']
        ];
    }

    /**
     * @test
     * @dataProvider head_posture_display_provider
     * @param $value
     * @param $expected
     */
    public function head_posture_display_value_is_correct($value, $expected)
    {
        $instance = new HasWithHeadPosture_TestClass();
        $instance->with_head_posture = $value;

        $this->assertEquals($expected, $instance->display_with_head_posture);
    }

    /** @test */
    public function event_level_validation_requires_head_posture_element_when_marked_as_used()
    {
        $instance = new HasWithHeadPosture_TestClass();
        $instance->with_head_posture = HasWithHeadPosture::$WITH_HEAD_POSTURE;

        $instance->eventScopeValidation([]);

        $this->assertAttributeHasError($instance, 'with_head_posture', 'has not been recorded');
    }

    /** @test */
    public function event_level_validation_passes_with_head_posture_element_when_marked_as_used()
    {
        $instance = new HasWithHeadPosture_TestClass();
        $instance->with_head_posture = HasWithHeadPosture::$WITH_HEAD_POSTURE;

        $instance->eventScopeValidation([new HeadPosture()]);

        $this->assertEmpty($instance->getErrors('with_head_posture'));
    }

    /** @test */
    public function event_level_validation_does_not_require_head_posture_element_when_marked_as_not_used()
    {
        $instance = new HasWithHeadPosture_TestClass();
        $instance->with_head_posture = HasWithHeadPosture::$WITHOUT_HEAD_POSTURE;

        $instance->eventScopeValidation([]);

        $this->assertEmpty($instance->getErrors('with_head_posture'));
    }
}

class HasWithHeadPosture_TestClass extends \BaseEventTypeElement
{
    use HasWithHeadPosture;

    public function tableName()
    {
        return 'test_has_with_head_posture';
    }

    public function rules()
    {
        return $this->rulesForWithHeadPosture();
    }
}
