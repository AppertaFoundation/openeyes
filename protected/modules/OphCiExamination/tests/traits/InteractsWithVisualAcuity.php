<?php

namespace OEModule\OphCiExamination\tests\traits;

use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\OphCiExamination_NearVisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityFixation;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityOccluder;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuitySource;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;

/**
 * Trait InteractsWithVisualAcuity
 *
 * @package OEModule\OphCiExamination\tests\traits
 */
trait InteractsWithVisualAcuity
{
    use \InteractsWithEventTypeElements;

    protected function getStandardVisualAcuityUnit($near = false)
    {
        $unit_criteria = new \CDbCriteria();
        $unit_criteria->select = 't.*, count(values.id) as valuesTotal';
        $unit_criteria->addColumnCondition(['is_near' => $near, 'active' => true]);
        $unit_criteria->join = 'join ophciexamination_visual_acuity_unit_value `values` on (values.unit_id = t.id)';
        $unit_criteria->group = 't.id';
        $unit_criteria->having = 'valuesTotal > 0';
        return $this->getRandomLookup(OphCiExamination_VisualAcuityUnit::class, 1, $unit_criteria);
    }

    protected function getRandomAcuitySource($near = false)
    {
        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(['is_near' => $near, 'active' => true]);
        return $this->getRandomLookup(OphCiExamination_VisualAcuitySource::class, 1, $criteria);
    }

    /**
     * @param OphCiExamination_VisualAcuityUnit|null $unit
     * @param int $reading_count
     */
    protected function getStandardVisualAcuityElementWithSettings(?OphCiExamination_VisualAcuityUnit $unit = null, $reading_count = 0)
    {
        if ($unit === null) {
            $unit = $this->getStandardVisualAcuityUnit();
        }

        $instance = $this->getMockBuilder(Element_OphCiExamination_VisualAcuity::class)
            ->setMethods(['getSetting'])
            ->getMock();
        $instance->expects($this->any())
            ->method('getSetting')
            ->will($this->returnValueMap([
                ['unit_id', $unit->id],
                ['default_rows', $reading_count]
            ]));

        return $instance;
    }

    protected function generateSavedVisualAcuityElementWithReadings($complex = false, $attrs = [])
    {
        $element = new Element_OphCiExamination_VisualAcuity();
        $element->setHasRight();
        $element->setHasLeft();
        if ($complex) {
            $element->setHasBeo();
        }

        $element->record_mode = $complex
            ? Element_OphCiExamination_VisualAcuity::RECORD_MODE_COMPLEX
            : Element_OphCiExamination_VisualAcuity::RECORD_MODE_SIMPLE;

        $element->setAttributes($attrs);
        $sides = [
            'right' => OphCiExamination_VisualAcuity_Reading::RIGHT,
            'left' => OphCiExamination_VisualAcuity_Reading::LEFT,
        ];
        if ($complex) {
            $sides['beo'] = OphCiExamination_VisualAcuity_Reading::BEO;
        }

        foreach ($sides as $side => $side_key) {
            if (!array_key_exists("{$side}_readings", $attrs)) {
                $reading = $this->generateVisualAcuityReading();
                $reading->side = $side_key;
                $element->{"{$side}_readings"} = [$reading];
            }
        }


        return $this->saveElement($element);
    }

    protected function generateVisualAcuityElementWithReadings($right_count = 0, $left_count = 0, $beo_count = 0, $complex = false)
    {
        $element = new Element_OphCiExamination_VisualAcuity();

        $element->record_mode = ($beo_count > 0 || $complex)
            ? Element_OphCiExamination_VisualAcuity::RECORD_MODE_COMPLEX
            : Element_OphCiExamination_VisualAcuity::RECORD_MODE_SIMPLE;

        foreach (['right', 'left', 'beo'] as $side) {
            $count = ${"{$side}_count"};
            $readings = [];
            $reading_methods = [];
            if ($count > 0) {
                $element->{"setHas" . ucfirst($side)}();
                for ($i = 0; $i < $count; $i++) {
                    $reading = $this->generateVisualAcuityReading($complex, $reading_methods);
                    $reading->setSideByString($side);
                    $readings[] = $reading;
                    $reading_methods[] = $reading->method_id;
                }
            } else {
                $element->{"setDoesNotHave" . ucfirst($side)}();
            }

            $element->{"{$side}_readings"} = $readings;
        }

        return $element;
    }

    protected function generateVisualAcuityReading($complex = false, $used_method_ids = [])
    {

        $reading = new OphCiExamination_VisualAcuity_Reading();
        $reading->setAttributes($this->generateVAReadingData($complex, $used_method_ids));

        return $reading;
    }

    protected function generateNearVisualAcuityReading($complex = false, $used_method_ids = [])
    {

        $reading = new OphCiExamination_NearVisualAcuity_Reading();
        $reading->setAttributes($this->generateVAReadingData($complex, $used_method_ids, true));

        return $reading;
    }

    protected function generateVAReadingData($complex = false, $used_method_ids = [], $near = false)
    {
        $unit = $this->getStandardVisualAcuityUnit($near);
        $data = [
            'unit_id' => $unit->id,
            'value' => $this->faker->randomElement($unit->selectableValues)->base_value,
            'method_id' => $this->getNotUsedMethodId($used_method_ids)
        ];

        if ($complex) {
            $data = array_merge(
                $data,
                [
                    'source_id' => $this->getRandomAcuitySource($near)->id,
                    'occluder_id' => $this->getRandomLookup(OphCiExamination_VisualAcuityOccluder::class)->id,
                    'with_head_posture' => $this->faker->randomElement([
                        OphCiExamination_VisualAcuity_Reading::$WITH_HEAD_POSTURE,
                        OphCiExamination_VisualAcuity_Reading::$WITHOUT_HEAD_POSTURE
                    ])
                ]
            );
        }

        if (!$near) {
            $data['fixation_id'] = $this->getRandomLookup(OphCiExamination_VisualAcuityFixation::class)->id;
        }

        return $data;
    }

    protected function getNotUsedMethodId($used_method_ids = [])
    {
        $method_criteria = null;
        if (count($used_method_ids)) {
            $method_criteria = new \CDbCriteria();
            $method_criteria->addNotInCondition('id', $used_method_ids);
        }

        return $this->getRandomLookup(OphCiExamination_VisualAcuity_Method::class, 1, $method_criteria)->id;
    }
}
