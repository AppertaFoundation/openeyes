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

use OEModule\OphCiExamination\models\CorrectionType;
use OEModule\OphCiExamination\models\SensoryFunction_Distance;
use OEModule\OphCiExamination\models\SensoryFunction_Entry;
use OEModule\OphCiExamination\models\SensoryFunction_EntryType;
use OEModule\OphCiExamination\models\SensoryFunction_Result;
use OEModule\OphCiExamination\models\traits\HasWithHeadPosture;
use OEModule\OphCiExamination\tests\unit\models\testingtraits\HasWithHeadPostureAttributesToTest;

/**
 * Class SensoryFunction_EntryTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\SensoryFunction_Entry
 * @group sample-data
 * @group strabismus
 * @group sensory-function
 */
class SensoryFunction_EntryTest extends \ModelTestCase
{
    use \HasStandardRelationsTests;
    use \HasRelationOptionsToTest;
    use HasWithHeadPostureAttributesToTest;

    protected $element_cls = SensoryFunction_Entry::class;

    public function belongs_to_relations()
    {
        return [
            ['entry_type', SensoryFunction_EntryType::class],
            ['distance', SensoryFunction_Distance::class],
            ['result', SensoryFunction_Result::class],
        ];
    }

    /**
     * @test
     * @dataProvider belongs_to_relations
     */
    public function relations_defined($relation, $relation_cls)
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();

        $this->assertArrayHasKey($relation, $relations);
        $this->assertEquals(SensoryFunction_Entry::BELONGS_TO, $relations[$relation][0]);
        $this->assertEquals($relation_cls, $relations[$relation][1]);
    }

    /**
     * @test
     * @dataProvider belongs_to_relations
     */
    public function relation_rules_defined($relation, $relation_cls)
    {
        $instance = $this->getElementInstance();
        $attr = $relation . '_id';

        $this->assertRelationRuleDefined($instance, $attr, $relation_cls);
        $this->assertContains($attr, $instance->getSafeAttributeNames());
    }

    /**
     * @test
     * @dataProvider belongs_to_relations
     */
    public function entry_type_options($relation, $relation_cls)
    {
        $instance = $this->getElementInstance();

        $this->assertOptionsAreRetrievable($instance, $relation, $relation_cls);
    }

    /** @test */
    public function correctiontype_relation()
    {
        $instance = $this->getElementInstance();
        $relations = $instance->relations();

        $this->assertArrayHasKey('correctiontypes', $relations);
        $this->assertEquals(SensoryFunction_Entry::MANY_MANY, $relations['correctiontypes'][0]);
        $this->assertEquals(CorrectionType::class, $relations['correctiontypes'][1]);
    }

    /** @test */
    public function correctiontypes_rule_defined()
    {
        $instance = $this->getElementInstance();
        // for auto saving
        $this->assertContains('correctiontypes', $instance->getSafeAttributeNames());
    }

    /** @test */
    public function correctiontype_options()
    {
        $instance = $this->getElementInstance();

        $this->assertOptionsAreRetrievable($instance, 'correctiontypes', CorrectionType::class);
    }

    public function string_provider()
    {
        $entry_type = $this->getRandomLookup(SensoryFunction_EntryType::class);
        $distance = $this->getRandomLookup(SensoryFunction_Distance::class);
        $result = $this->getRandomLookup(SensoryFunction_Result::class);
        $correctiontype = $this->getRandomLookup(CorrectionType::class);
        $multiple_correctiontypes = $this->getRandomLookup(CorrectionType::class, 2);

        return [
            [
                [
                    'entry_type_id' => $entry_type->getPrimaryKey(),
                    'distance_id' => $distance->getPrimaryKey(),
                    'result_id' => $result->getPrimaryKey()
                ],
                "$entry_type, $distance: $result"
            ],
            [
                [
                    'entry_type_id' => $entry_type->getPrimaryKey(),
                    'distance_id' => $distance->getPrimaryKey(),
                    'result_id' => $result->getPrimaryKey(),
                    'with_head_posture' => HasWithHeadPosture::$WITHOUT_HEAD_POSTURE
                ],
                "$entry_type, $distance, CHP " . HasWithHeadPosture::$DISPLAY_WITHOUT_HEAD_POSTURE . ": $result"
            ],
            [
                [
                    'entry_type_id' => $entry_type->getPrimaryKey(),
                    'distance_id' => $distance->getPrimaryKey(),
                    'result_id' => $result->getPrimaryKey(),
                    'with_head_posture' => HasWithHeadPosture::$WITH_HEAD_POSTURE
                ],
                "$entry_type, $distance, CHP " . HasWithHeadPosture::$DISPLAY_WITH_HEAD_POSTURE . ": $result"
            ],
            [
                [
                    'entry_type_id' => $entry_type->getPrimaryKey(),
                    'distance_id' => $distance->getPrimaryKey(),
                    'result_id' => $result->getPrimaryKey(),
                    'correctiontypes' => [$correctiontype],
                    'with_head_posture' => HasWithHeadPosture::$WITH_HEAD_POSTURE
                ],
                "$entry_type, $distance, " . $correctiontype->name . ", CHP " . HasWithHeadPosture::$DISPLAY_WITH_HEAD_POSTURE . ": $result"
            ],
            [
                [
                    'entry_type_id' => $entry_type->getPrimaryKey(),
                    'distance_id' => $distance->getPrimaryKey(),
                    'result_id' => $result->getPrimaryKey(),
                    'correctiontypes' => $multiple_correctiontypes
                ],
                "$entry_type, $distance, " . implode(", ", $multiple_correctiontypes) . ": $result"
            ]
        ];
    }

    /**
     * @param $attrs
     * @param $expected
     * @test
     * @dataProvider string_provider
     */
    public function string_response($attrs, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->setAttributes($attrs);

        $this->assertEquals($expected, (string) $instance);
    }

    public function correctiontypes_provider()
    {
        $correctiontype = $this->getRandomLookup(CorrectionType::class);
        $multiple_correctiontypes = $this->getRandomLookup(CorrectionType::class, 2);

        return [
            [[$correctiontype], $correctiontype->name],
            [$multiple_correctiontypes, implode(", ", $multiple_correctiontypes)],
            [[], "-"]
        ];
    }

    /**
     * @param $correctiontypes
     * @param $expected
     * @test
     * @dataProvider correctiontypes_provider
     */
    public function correctiontypes_display($correctiontypes, $expected)
    {
        $instance = $this->getElementInstance();
        $instance->correctiontypes = $correctiontypes;

        $this->assertEquals($expected, $instance->display_correctiontypes);
    }
}
