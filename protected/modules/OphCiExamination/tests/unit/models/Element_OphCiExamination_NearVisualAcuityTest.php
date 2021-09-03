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
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;

/**
 * Class Element_OphCiExamination_VisualAcuityTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_NearVisualAcuity
 * @group sample-data
 * @group strabismus
 * @group near-visual-acuity
 */
class Element_OphCiExamination_NearVisualAcuityTest extends BaseVisualAcuityTest
{
    protected $element_cls = Element_OphCiExamination_NearVisualAcuity::class;
    protected $reading_cls = OphCiExamination_NearVisualAcuity_Reading::class;

    protected array $columns_to_skip = [
        'left_notes', 'right_notes', 'left_unable_to_assess', 'right_unable_to_assess', 'left_eye_missing',
        'right_eye_missing'
    ];

    protected function getRandomUnit()
    {
        $unit_criteria = new \CDbCriteria();
        $unit_criteria->addColumnCondition(['is_near' => true]);
        return $this->getRandomLookup(OphCiExamination_VisualAcuityUnit::class, 1, $unit_criteria);
    }

    protected function getElementInstanceWithHeadPostureEntry()
    {
        $instance = $this->getElementInstance();
        $side = $this->faker->randomElement(['right', 'left', 'beo']);
        $reading = $this->generateNearVisualAcuityReading(true);
        $reading->setSideByString($side);
        $reading->with_head_posture = OphCiExamination_NearVisualAcuity_Reading::$WITH_HEAD_POSTURE;
        $instance->{"{$side}_readings"} = [$reading];

        return [$instance, "{$side}_readings.0"];
    }
}