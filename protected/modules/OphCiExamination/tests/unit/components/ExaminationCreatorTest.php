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

namespace OEModule\OphCiExamination\tests\unit\components;

use OEModule\OphCiExamination\components\ExaminationCreator;
use OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction;
use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\OphCiExamination_Instrument;
use OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_Refraction_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_Refraction_Type;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue;
use OEModule\OphCiExamination\tests\traits\InteractsWithVisualAcuity;

/**
 * Class ExaminationCreatorTest
 *
 * @package OEModule\OphCiExamination\tests\unit\components
 * @covers \OEModule\OphCiExamination\components\ExaminationCreator
 * @group sample-data
 * @group strabismus
 * @group visual-acuity
 */
class ExaminationCreatorTest extends \OEDbTestCase
{
    use \WithFaker;
    use InteractsWithVisualAcuity;

    public function setUp()
    {
        parent::setUp();

        \Yii::app()
            ->setComponent('session', $this->getMockBuilder(\CHttpSession::class)
            ->disableOriginalConstructor()
            ->getMock());
    }

    /** @test */
    public function visual_acuity_and_refraction_are_saved()
    {
        $creator = new ExaminationCreator();
        $patient = $this->getPatientWithEpisodes();
        $user  = $this->getRandomLookup(\User::class);
        $eventType = \EventType::model()->find('name = "Examination"');
        $refractionType = $this->getRandomLookup(OphCiExamination_Refraction_Type::class);
        $eyes = \Eye::model()->findAll();
        $eyeIds = [];
        foreach ($eyes as $eye) {
            $eyeIds[strtolower($eye->name)] = $eye->id;
        }

        $examinationRequest = $this->buildBasePatientRequest();

        $vaUnits = $this->getStandardVisualAcuityUnit();
        $examinationRequest['patient']['eyes'] = [$this->buildRequestDataForEye('right', $vaUnits)];

        $createdEvent = $creator->save($patient->episodes[0]->id, $user->id, $examinationRequest, $eventType, $eyeIds, $refractionType);

        $vaElement = Element_OphCiExamination_VisualAcuity::model()->find('event_id = :eid', [":eid" => $createdEvent->id]);
        $this->assertNotNull($vaElement);
        $this->assertVAElementIsCorrect($vaElement, $examinationRequest['patient']['eyes'], $vaUnits);
        $refractionElement =  Element_OphCiExamination_Refraction::model()->find('event_id = :eid', [":eid" => $createdEvent->id]);
        $this->assertNotNull($refractionElement);
        $this->assertRefractionElementIsCorrect($refractionElement, $examinationRequest['patient']['eyes'], $refractionType);
    }

    /**
     * Check that the element has been populated correctly based on the given request data
     *
     * @param Element_OphCiExamination_VisualAcuity $element
     * @param $eyesData
     * @param OphCiExamination_VisualAcuityUnit $vaUnits
     */
    public function assertVAElementIsCorrect(Element_OphCiExamination_VisualAcuity $element, $eyesData, OphCiExamination_VisualAcuityUnit $vaUnits)
    {
        foreach ($eyesData as $eyeData) {
            $side = $eyeData['label'];
            $this->assertVaElementReadingsAreCorrect($element->{"{$side}_readings"}, $eyeData['reading'][0]['visual_acuity'], $vaUnits);
        }
    }

    /**
     * Verify refraction data recorded correctly
     *
     * @param Element_OphCiExamination_Refraction $element
     * @param $eyesData
     * @param OphCiExamination_Refraction_Type $refractionType
     */
    public function assertRefractionElementIsCorrect(
        Element_OphCiExamination_Refraction $element,
        $eyesData,
        OphCiExamination_Refraction_Type $refractionType
    ) {
        foreach ($eyesData as $eyeData) {
            $side = $eyeData['label'];
            $this->assertCount(1, $element->{"{$side}_readings"});
            $this->assertRefractionReadingIsCorrect($element->{"{$side}_readings"}[0], $eyeData['reading'][0]['refraction'], $refractionType);
        }
    }

    public function assertRefractionReadingIsCorrect(
        OphCiExamination_Refraction_Reading $reading,
        $data,
        OphCiExamination_Refraction_Type $refractionType
    ) {
        // cast to float for numerical comparison
        $this->assertEquals((float)$data['sphere'], $reading->sphere);
        $this->assertEquals((float)$data['cylinder'], $reading->cylinder);
        $this->assertEquals($data['axis'], $reading->axis);
        $this->assertEquals($refractionType->id, $reading->type_id);
    }

    /**
     * Check the given readings against the given request data
     *
     * @param OphCiExamination_VisualAcuity_Reading[] $readings
     * @param array $data
     * @param OphCiExamination_VisualAcuityUnit $vaUnits
     */
    public function assertVaElementReadingsAreCorrect($readings, $data, OphCiExamination_VisualAcuityUnit $vaUnits)
    {
        for ($i = 0; $i < count($data); $i++) {
            $reading = $readings[$i]; // assuming stored in the same order for now.

            $this->assertEquals($vaUnits->id, $reading->unit_id);
            $this->assertEquals($reading->value,
                OphCiExamination_VisualAcuityUnitValue::model()->getBaseValue($vaUnits->id, $data[$i]['reading']));
            $this->assertEquals(strtolower($reading->method->name), strtolower($data[$i]['method']));
        }
    }

    protected function buildBasePatientRequest()
    {
        return [
            'examination_date' => $this->faker->dateTimeThisDecade()->format('Y-m-d\TH:i:sP'),
            'op_tom' => ['name' => $this->faker->name()],
            'patient' => [
                'ready_for_second_eye' => true, // probably should make this random,
                'eyes' => [],
                'comments' => $this->faker->sentence(10)
            ]
        ];
    }

    protected function buildRequestDataForEye($side, $vaUnits)
    {
        return [
            'label' => $side,
            'reading' => [
                [
                    'iop' => $this->buildRequestDataForIop(),
                    'refraction' => $this->buildRequestDataForRefraction(),
                    'visual_acuity' => [$this->buildRequestDataForVisualAcuity($vaUnits)],
                    'near_visual_acuity' => []
                ]
            ]
        ];
    }

    protected function buildRequestDataForIop()
    {
        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(['scale_id' => null]);
        $instrument = $this->getRandomLookup(OphCiExamination_Instrument::class,1, $criteria);

        return [
            'mm_hg' => $this->getRandomLookup(OphCiExamination_IntraocularPressure_Reading::class)->value,
            'instrument'=> $instrument->name
        ];
    }

    protected function buildRequestDataForRefraction()
    {
        return [
            'sphere' => $this->faker->randomElement(['+', '-']) . $this->faker->numberBetween(0, 20) . '.' . $this->faker->randomElement(['00', '25', '50', '75']),
            'cylinder' => $this->faker->randomElement(['+', '-']) . $this->faker->numberBetween(0, 3) . '.' . $this->faker->randomElement(['00', '25', '50', '75']),
            'axis' => $this->faker->numberBetween(-180, 180),
        ];
    }

    protected function buildRequestDataForVisualAcuity(OphCiExamination_VisualAcuityUnit $vaUnits)
    {
        return [
            'measure' => $vaUnits->name,
            'reading' => $this->faker->randomElement($vaUnits->selectableValues)->value,
            'method' => $this->getRandomLookup(OphCiExamination_VisualAcuity_Method::class)->name,
        ];
    }
}