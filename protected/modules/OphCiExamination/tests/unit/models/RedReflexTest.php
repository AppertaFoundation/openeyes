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

use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\RedReflex;
use OEModule\OphCiExamination\tests\traits\InteractsWithRedReflex;

/**
 * Class RedReflexTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @group sample-data
 * @group strabismus
 * @group red-reflex
 */
class RedReflexTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;
    use \WithTransactions;
    use InteractsWithRedReflex;

    protected $element_cls = RedReflex::class;

    /** @test */
    public function attribute_safety()
    {
        $instance = $this->getElementInstance();
        $safe = $instance->getSafeAttributeNames();

        $this->assertContains('event_id', $safe);
        $this->assertContains('right_has_red_reflex', $safe);
        $this->assertContains('left_has_red_reflex', $safe);
    }

    public function letter_string_provider()
    {
        return [
            [['eye_id' => SidedData::RIGHT, 'right_has_red_reflex' => RedReflex::HAS_RED_REFLEX], "Red Reflex: R: Y"],
            [['eye_id' => SidedData::RIGHT, 'right_has_red_reflex' => RedReflex::NO_RED_REFLEX], "Red Reflex: R: N"],
            [['eye_id' => SidedData::LEFT, 'left_has_red_reflex' => RedReflex::HAS_RED_REFLEX], "Red Reflex: L: Y"],
            [['eye_id' => SidedData::LEFT, 'left_has_red_reflex' => RedReflex::NO_RED_REFLEX], "Red Reflex: L: N"],
            [[
                'eye_id' => SidedData::RIGHT | SidedData::LEFT,
                'right_has_red_reflex' => RedReflex::HAS_RED_REFLEX,
                'left_has_red_reflex' => RedReflex::HAS_RED_REFLEX
            ], "Red Reflex: R: Y L: Y"],
            [[
                'eye_id' => SidedData::RIGHT | SidedData::LEFT,
                'right_has_red_reflex' => RedReflex::NO_RED_REFLEX,
                'left_has_red_reflex' => RedReflex::HAS_RED_REFLEX
            ], "Red Reflex: R: N L: Y"],
        ];
    }

    /**
     * @param $attrs
     * @param $expected
     * @test
     * @dataProvider letter_string_provider
     */
    public function letter_string($attrs, $expected)
    {
        $instance = $this->generateSavedRedReflex($attrs);

        $this->assertEquals($expected, $instance->letter_string);
    }
}
