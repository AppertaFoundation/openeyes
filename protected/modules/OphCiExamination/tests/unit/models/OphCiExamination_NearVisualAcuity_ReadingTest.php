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

use OEModule\OphCiExamination\models\Element_OphCiExamination_NearVisualAcuity;
use OEModule\OphCiExamination\models\OphCiExamination_NearVisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityOccluder;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuitySource;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;
use OEModule\OphCiExamination\models\traits\HasWithHeadPosture;

/**
 * Class OphCiExamination_NearVisualAcuity_ReadingTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\OphCiExamination_NearVisualAcuity_Reading
 * @group sample-data
 * @group strabismus
 * @group near-visual-acuity
 */
class OphCiExamination_NearVisualAcuity_ReadingTest extends BaseVisualAcuityReadingTest
{

    protected $element_cls = OphCiExamination_NearVisualAcuity_Reading::class;

    public function belongs_to_relations(): array
    {
        return [
            ['element', Element_OphCiExamination_NearVisualAcuity::class],
            ['unit', OphCiExamination_VisualAcuityUnit::class],
            ['method', OphCiExamination_VisualAcuity_Method::class],
            ['source', OphCiExamination_VisualAcuitySource::class],
            ['occluder', OphCiExamination_VisualAcuityOccluder::class]
        ];
    }

    public function belongs_to_relations_with_options(): array
    {
        return [
            ['method', OphCiExamination_VisualAcuity_Method::class],
            ['occluder', OphCiExamination_VisualAcuityOccluder::class]
        ];
    }

    /** @test */
    public function source_options_are_filtered_to_near()
    {
        $instance = $this->getElementInstance();

        $expected_pks = array_map(function($related_obj) {
            return $related_obj->getPrimaryKey();
        }, OphCiExamination_VisualAcuitySource::model()->active()->findAll([
            'condition' => 'is_near = 1'
        ]));

        $this->assertOptionsAreRetrievable(
            $instance,
            "source",
            OphCiExamination_VisualAcuitySource::class,
            $expected_pks
        );
    }

    public function complex_attrs_provider()
    {
        return [
            [["source"], null, null],
            [["source"], HasWithHeadPosture::$WITH_HEAD_POSTURE, "CHP: Used"],
            [["source", "occluder"], null, null],
        ];
    }

    /**
     * @param $relations
     * @param $with_head_posture
     * @test
     * @dataProvider complex_attrs_provider
     */
    public function complex_attrs_string($relations, $with_head_posture, $ending)
    {
        $instance = $this->getElementInstance();
        $data = $this->generateVAReadingData(true, [], true);

        $expected = [];
        foreach ($relations as $relation) {
            $instance->{"{$relation}_id"} = $data["{$relation}_id"];
            $expected[] = (string) $instance->$relation;
        }
        $instance->with_head_posture = $with_head_posture;

        if ($ending !== null) {
            $expected[] = $ending;
        }

        $this->assertEquals(implode(", ", $expected), $instance->getComplexAttributesString());
    }
}