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

use OEModule\OphCiExamination\models\ConvergenceAccommodation;
use OEModule\OphCiExamination\models\CorrectionType;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasCorrectionTypeAttributeToTest;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureAttributesToTest;

/**
 * Class ConvergenceAccommodationTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\ConvergenceAccommodation
 * @group sample-data
 * @group strabismus
 * @group convergence-accommodation
 */
class ConvergenceAccommodationTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;
    use \HasRelationOptionsToTest;
    use \WithFaker;
    use HasWithHeadPostureAttributesToTest;

    use HasCorrectionTypeAttributeToTest;

    protected $element_cls = ConvergenceAccommodation::class;

    /** @test */
    public function comments_are_required()
    {
        $instance = $this->getElementInstance();
        $instance->comments = '';
        $this->assertAttributeInvalid($instance, 'comments', 'cannot be blank');
    }

    /** @test */
    public function comments_must_be_longer_than_5_characters()
    {
        $instance = $this->getElementInstance();
        $instance->comments = $this->faker->regexify('[A-Za-z0-9]{1,4}');
        $this->assertAttributeInvalid($instance, 'comments', 'too short');
    }

    /** @test */
    public function valid_check()
    {
        $instance = $this->getElementInstance();
        $instance->with_head_posture = $this->faker->randomElement([
            ConvergenceAccommodation::$WITH_HEAD_POSTURE,
            ConvergenceAccommodation::$WITHOUT_HEAD_POSTURE]);
        $instance->correctiontype_id = $this->getRandomLookup(CorrectionType::class)->getPrimaryKey();
        $instance->comments = $this->faker->text();

        $this->assertTrue($instance->validate(), 'validation failed: ' . print_r($instance->getErrors(), true));
    }

    public function letter_string_provider()
    {
        $correctionType = $this->getRandomLookup(CorrectionType::class);

        return [
                    [
                        [
                            'comments' => 'foo bar one'
                        ],
                        'Convergence And Accommodation: foo bar one'
                    ],
                    [
                        [
                            'comments' => 'foo bar two',
                            'with_head_posture' => ConvergenceAccommodation::$WITH_HEAD_POSTURE
                        ],
                        'Convergence And Accommodation: CHP: ' . ConvergenceAccommodation::$DISPLAY_WITH_HEAD_POSTURE . ' foo bar two'
                    ],
                    [
                        [
                            'comments' => 'foo bar three',
                            'with_head_posture' => ConvergenceAccommodation::$WITHOUT_HEAD_POSTURE
                        ],
                        'Convergence And Accommodation: CHP: ' . ConvergenceAccommodation::$DISPLAY_WITHOUT_HEAD_POSTURE . ' foo bar three'
                    ],
                    [
                        [
                            'comments' => 'foo bar four',
                            'correctiontype_id' => $correctionType->getPrimaryKey()
                        ],
                        'Convergence And Accommodation: ' . $correctionType->name . ' foo bar four'
                    ],
                ];
    }

    /**
     * @test
     * @dataProvider letter_string_provider
     */
    public function test_letter_string($attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->setAttributes($attrs);

        $this->assertEquals($expected, $instance->letter_string);
    }
}
