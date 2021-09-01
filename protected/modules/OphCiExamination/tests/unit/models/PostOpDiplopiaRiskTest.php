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

use OEModule\OphCiExamination\models\PostOpDiplopiaRisk;
use OEModule\OphCiExamination\tests\unit\models\traits\HasBaseElementTests;

/**
 * Class PostOpDiplopiaRiskTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\PostOpDiplopiaRisk
 * @group sample-data
 * @group strabismus
 * @group post-op-diplopia-risk
 */
class PostOpDiplopiaRiskTest extends \ModelTestCase
{
    use \HasCoreEventElementTests;

    protected $element_cls = PostOpDiplopiaRisk::class;

    public function letterStringProvider()
    {
        return [
            ['foobar', 0, 'PODT: NOT at risk, foobar'],
            ['', 1, 'PODT: At risk'],
            ["foo bar,\nbaz", 1, 'PODT: At risk, foo bar, baz'],
        ];
    }

    /**
     * @dataProvider letterStringProvider
     */
    public function test_letter_string($comments, $at_risk, $expected)
    {
        $instance = new PostOpDiplopiaRisk();
        $instance->comments = $comments;
        $instance->at_risk = $at_risk;

        $this->assertEquals($expected, $instance->getLetter_string());
    }

    public function test_attribute_labels()
    {
        $instance = new PostOpDiplopiaRisk();

        foreach (['comments', 'at_risk'] as $attr) {
            $this->assertArrayHasKey($attr, $instance->attributeLabels());
        }
    }
}
