<?php
/**
 * Created by mike.smith@camc-ltd.co.uk
 */

namespace OEModule\OphCiExamination\tests\unit\models;

use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;

/**
 * This is a temporary name for this test file, the Strabismus prefix should be removed
 * when merging up to the core project. The separation is to prevent any merge challenges
 * further down the line
 *
 * Class Strabismus_Element_OphCiExamination_VisualAcuityTest
 *
 * @package OEModule\OphCiExamination\tests\unit\models
 * @covers \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity
 * @group sample-data
 * @group strabismus
 * @group visual-acuity
 */
class Strabismus_Element_OphCiExamination_VisualAcuityTest extends BaseVisualAcuityTest
{
    protected $element_cls = Element_OphCiExamination_VisualAcuity::class;
    protected $reading_cls = OphCiExamination_VisualAcuity_Reading::class;

    protected function getRandomUnit()
    {
        $unit_criteria = new \CDbCriteria();
        $unit_criteria->addColumnCondition(['is_near' => false]);
        return $this->getRandomLookup(OphCiExamination_VisualAcuityUnit::class, 1, $unit_criteria);
    }

    protected function getElementInstanceWithHeadPostureEntry()
    {
        $instance = $this->getElementInstance();
        $side = $this->faker->randomElement(['right', 'left', 'beo']);
        $reading = $this->generateVisualAcuityReading(true);
        $reading->setSideByString($side);
        $reading->with_head_posture = OphCiExamination_VisualAcuity_Reading::$WITH_HEAD_POSTURE;
        $instance->{"{$side}_readings"} = [$reading];

        return [$instance, "{$side}_readings.0"];
    }
}
