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

namespace OEModule\OphCiExamination\tests\unit\widgets;

use OEModule\OphCiExamination\components\OphCiExamination_API;
use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\tests\traits\InteractsWithVisualAcuity;

/**
 * Class OphCiExamination_Episode_VisualAcuityHistoryTest
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets
 * @group strabismus
 * @group visual-acuity
 * @covers OphCiExamination_Episode_VisualAcuityHistory
 */
class OphCiExamination_Episode_VisualAcuityHistoryTest extends \OEDbTestCase
{
    use \CreatesWidgets;
    use InteractsWithVisualAcuity;
    use \InteractsWithPatient;
    use \WithFaker;
    use \WithTransactions;

    protected $controller_cls = \PatientController::class;
    protected $mockApi;
    protected $mockEventType;

    public function setUp(): void
    {
        parent::setUp();
        \Yii::app()
            ->setComponent('session', $this->getMockBuilder(\CHttpSession::class)
                ->disableOriginalConstructor()
                ->getMock());
        // work around non-namespaced widget
        \Yii::import('OphCiExamination.widgets.OphCiExamination_Episode_VisualAcuityHistory');
        $this->mockApi = $this->getMockApi();
        $this->mockEventType = \ComponentStubGenerator::generate(\EventType::class, ['api' => $this->mockApi]);
    }

    /** @test */
    public function plotly_data_has_required_keys_even_for_recordless_patient()
    {
        $patient = $this->generateSavedPatient();
        $widget = $this->createWidgetWithProps(
            \OphCiExamination_Episode_VisualAcuityHistory::class,
            [
                'patient' => $patient,
                'event_type' => $this->mockEventType
            ]
        );
        $this->mockApi->method('getElements')
            ->with(Element_OphCiExamination_VisualAcuity::class, $patient, false)
            ->willReturn([]);

        $result_va = $widget->getPlotlyVaData();
        $this->assertIsArray($result_va);
        foreach (['right', 'left', 'beo'] as $side) {
            $this->assertArrayHasKey($side, $result_va);
        }

        $result_vfi = $widget->getPlotlyVfiData();
        $this->assertIsArray($result_vfi);
        foreach (['right', 'left'] as $side) {
            $this->assertArrayHasKey($side, $result_vfi);
        }
    }

    /** @test */
    public function plotly_data_returns_best_values_for_elements()
    {
        $patient = $this->generateSavedPatientWithEpisode();
        $event = $this->getEventToSaveWith($patient, [
            'event_date' => $this->faker->dateTimeBetween('-3 years')->format('Y-m-d')
        ]);

        $va = $this->generateVisualAcuityElementWithReadings(2, 2, 2, true);
        $va->event_id = $event->id;
        $this->assertTrue($va->save(), "couldn't save va: " . print_r($va->getErrors(), true));

        $widget = $this->createWidgetWithProps(
            \OphCiExamination_Episode_VisualAcuityHistory::class,
            [
                'patient' => $patient,
                'event_type' => \EventType::model()->find('class_name = ?', ['OphCiExamination'])
            ]
        );

        $result = $widget->getPlotlyVaData();

        foreach (['right', 'left', 'beo'] as $side) {
            $this->assertEquals($event->event_date, $result[$side]['x'][0]);
            // use the adjusted va to account for scaling of values 4 and below
            $this->assertEquals($widget->getAdjustedVA($va->getBestReading($side)->value), $result[$side]['y'][0]);
        }
    }

    protected function getMockApi()
    {
        return $this->getMockBuilder(OphCiExamination_API::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
    }
}
