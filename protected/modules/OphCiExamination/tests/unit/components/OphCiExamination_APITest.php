<?php

use OEModule\OphCiExamination\models\OphCiExamination_Instrument;
use OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure;
use OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value;
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models;
use OEModule\OphCiExamination\components\OphCiExamination_API;

/**
 * @property OEModule\OphCiExamination\components\OphCiExamination_API $api
 */
class OphCiExamination_APITest extends CDbTestCase
{
    private $api;

    public static function setupBeforeClass()
    {
        Yii::app()->getModule('OphCiExamination');
        Yii::app()->session['selected_institution_id'] = 1;
    }

    public static function tearDownAfterClass()
    {
        unset(Yii::app()->session['selected_institution_id']);
    }

    public function setUp()
    {
        parent::setUp();

        $dataContext = new DataContext(Yii::app(), ['subspecialties' => Subspecialty::model()->findByPk(2)]);
        $this->api = new OphCiExamination_API(Yii::app(), $dataContext);
    }

    public $fixtures = array(
        'ssa' => 'ServiceSubspecialtyAssignment',
        'firm' => 'Firm',
        'patient' => 'Patient',
        'episode' => 'Episode',
        'element_types' => 'ElementType',
        'event' => 'Event',
        'cct' => '\OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT',
        'cct_method' => '\OEModule\OphCiExamination\models\OphCiExamination_AnteriorSegment_CCT_Method',
        'gonioscopy' => '\OEModule\OphCiExamination\models\Element_OphCiExamination_Gonioscopy',
        'iop' => '\OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure',
        'iop_value' => '\OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value',
        'optic_disc' => '\OEModule\OphCiExamination\models\Element_OphCiExamination_OpticDisc',
        'event_type' => 'EventType',
        'et_iop' => Element_OphCiExamination_IntraocularPressure::class,
        'iop_values' => OphCiExamination_IntraocularPressure_Value::class,
        'instrument' => OphCiExamination_Instrument::class,
        'targetiop' => '\OEModule\OphCiExamination\models\OphCiExamination_TargetIop',
        'overallmanagementplan' => '\OEModule\OphCiExamination\models\Element_OphCiExamination_OverallManagementPlan',
        'va' => '\OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity',
        'va_readings' => '\OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading',
        'va_unit_values' => '\OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue',
    );

    public function testgetSnellenVisualAcuityForSide_hasReading()
    {
        foreach (array('Left', 'Right') as $side) {
            $method = 'getSnellenVisualAcuityFor'.$side;
            $this->assertEquals('6/9', $this->api->$method($this->patient('patient1')));
        }
    }

    public function testgetSnellenVisualAcuityForSide_hasNoReading()
    {
        foreach (array('Left', 'Right') as $side) {
            $method = 'getSnellenVisualAcuityFor'.$side;
            $this->assertNull($this->api->$method($this->patient('patient2')));
        }
    }

    public function testgetSnellenVisualAcuityForBoth_hasReading()
    {
        $this->assertEquals('6/9 on the right and 6/9 on the left', $this->api->getSnellenVisualAcuityForBoth($this->patient('patient1'), true));
    }

    public function testgetSnellenVisualAcuityForBoth_hasNoReading()
    {
        $this->assertEquals('not recorded on the right and not recorded on the left', $this->api->getSnellenVisualAcuityForBoth($this->patient('patient2'), false));
    }

    public function testGetPrincipalCCT()
    {
        $event = $this->createEvent();
        $element = $this->createCctElement($event, Eye::BOTH);

        $principalCCT = $this->api->getPrincipalCCT($this->patient('patient1'));
        $expected = 'Left Eye: 50 µm using Ultrasound pachymetry. Right Eye: 50 µm using Ultrasound pachymetry. ';
        $this->assertEquals($expected, $principalCCT);
    }

    public function testGetPrincipalCCTNoPrincipalEye()
    {
        $episode = $this->episode('episode2');

        $episode->eye_id = null;
        if (!$episode->save()) {
            throw new Exception('Failed to save episode: '.print_r($episode->getErrors(), true));
        }

        $principalCCT = $this->api->getPrincipalCCT($this->patient('patient1'));
        $expected = '';
        $this->assertEquals($expected, $principalCCT);
    }

    public function testGetPrincipalCCTRight()
    {
        $episode = $this->episode('episode2');
        $episode->eye_id = Eye::RIGHT;
        if (!$episode->save()) {
            throw new Exception('Failed to save episode: '.print_r($episode->getErrors(), true));
        }

        $event = $this->createEvent();
        $element = $this->createCctElement($event, Eye::RIGHT);

        $principalCCT = $this->api->getPrincipalCCT($this->patient('patient1'));
        $expected = 'Right Eye: 50 µm using Ultrasound pachymetry. ';
        $this->assertEquals($expected, $principalCCT);
    }

    public function testGetPrincipalCCTLeft()
    {
        $episode = $this->episode('episode2');
        $episode->eye_id = Eye::LEFT;
        if (!$episode->save()) {
            throw new Exception('Failed to save episode: '.print_r($episode->getErrors(), true));
        }

        $event = $this->createEvent();
        $element = $this->createCctElement($event, Eye::LEFT);

        $principalCCT = $this->api->getPrincipalCCT($this->patient('patient1'));
        $expected = 'Left Eye: 50 µm using Ultrasound pachymetry. ';
        $this->assertEquals($expected, $principalCCT);
    }

    public function testGetPrincipalCCT_NotLatestEvent()
    {
        $event1 = $this->createEvent(date('Y-m-d 23:59:58'));
        $element = $this->createCctElement($event1, Eye::BOTH);

        $event2 = $this->createEvent(date('Y-m-d 23:59:59'));

        $expected = 'Left Eye: 50 µm using Ultrasound pachymetry. Right Eye: 50 µm using Ultrasound pachymetry. ';
        $this->assertEquals($expected, $this->api->getPrincipalCCT($this->patient('patient1')));
    }

    public function testGetCCTLeft_NotLatestEvent()
    {
        $event1 = $this->createEvent(date('Y-m-d 23:59:58'));
        $element = $this->createCctElement($event1, Eye::LEFT);

        $event2 = $this->createEvent(date('Y-m-d 23:59:59'));

        $expected = '50 µm';
        $this->assertEquals($expected, $this->api->getCCTLeft($this->patient('patient1')));
    }

    public function testGetCCTRight_NotLatestEvent()
    {
        $event1 = $this->createEvent(date('Y-m-d 23:59:58'));
        $element = $this->createCctElement($event1, Eye::RIGHT);

        $event2 = $this->createEvent(date('Y-m-d 23:59:59'));

        $expected = '50 µm';
        $this->assertEquals($expected, $this->api->getCCTRight($this->patient('patient1')));
    }
    public function testGetCCTAbbr_Right()
    {
        $event = $this->createEvent();
        $this->createCctElement($event, Eye::RIGHT);

        $expected = 'r:50';
        $this->assertEquals($expected, $this->api->getCCTAbbr($this->patient('patient1')));
    }

    public function testGetCCTAbbr_Left()
    {
        $event = $this->createEvent();
        $this->createCctElement($event, Eye::LEFT);

        $expected = 'l:50';
        $this->assertEquals($expected, $this->api->getCCTAbbr($this->patient('patient1')));
    }

    public function testGetCCTAbbr_Both()
    {
        $event = $this->createEvent();
        $this->createCctElement($event, Eye::BOTH);

        $expected = 'r:50, l:50';
        $this->assertEquals($expected, $this->api->getCCTAbbr($this->patient('patient1')));
    }

    public function testGetCCTDate_Right()
    {
            $event = $this->createEvent(date('Y-m-d 23:59:58'));
            $this->createCctElement($event, Eye::RIGHT);

            $expected = date("Y-m-d 23:59:58");

            $this->assertEquals($expected, $this->api->getCCTDate($this->patient('patient1')));
    }

    public function testGetCCTDate_Left()
    {
        $event = $this->createEvent(date('Y-m-d 23:59:58'));
        $this->createCctElement($event, Eye::LEFT);

        $expected = date("Y-m-d 23:59:58");

        $this->assertEquals($expected, $this->api->getCCTDate($this->patient('patient1')));
    }

    public function testGetCCTDate_Both()
    {
        $event = $this->createEvent(date('Y-m-d 23:59:58'));
        $this->createCctElement($event, Eye::BOTH);

        $expected = date("Y-m-d 23:59:58");

        $this->assertEquals($expected, $this->api->getCCTDate($this->patient('patient1')));
    }

    public function testGetCCTDate_NotRecorded()
    {
        $this->assertNull($this->api->getTargetIOP($this->patient('patient1')));
    }

    public function testGetPrincipalVanHerick()
    {
        $event = $this->createEvent();
        $element = $this->createVanHerickElement($event, Eye::BOTH);

        $principalVH = $this->api->getPrincipalVanHerick($this->patient('patient1'));
        $expected = 'Left Eye: Van Herick grade is Grade 3 (41-75%). Right Eye: Van Herick grade is Grade 3 (41-75%). ';
        $this->assertEquals($expected, $principalVH);
    }

    public function testGetPrincipalVanHerickNoPrincipalEye()
    {
        $episode = $this->episode('episode2');

        $episode->eye_id = null;
        if (!$episode->save()) {
            throw new Exception('Failed to save episode: '.print_r($episode->getErrors(), true));
        }

        $event = $this->createEvent();
        $element = $this->createVanHerickElement($event, Eye::BOTH);

        $principalVH = $this->api->getPrincipalVanHerick($this->patient('patient1'));
        $expected = '';
        $this->assertEquals($expected, $principalVH);
    }

    public function testGetPrincipalVanHerickRight()
    {
        $episode = $this->episode('episode2');

        $episode->eye_id = Eye::RIGHT;
        if (!$episode->save()) {
            throw new Exception('Failed to save episode: '.print_r($episode->getErrors(), true));
        }

        $event = $this->createEvent();
        $element = $this->createVanHerickElement($event, Eye::RIGHT);

        $principalVH = $this->api->getPrincipalVanHerick($this->patient('patient1'));
        $expected = 'Right Eye: Van Herick grade is Grade 3 (41-75%). ';
        $this->assertEquals($expected, $principalVH);
    }

    public function testGetPrincipalVanHerickLeft()
    {
        $episode = $this->episode('episode2');

        $episode->eye_id = Eye::LEFT;
        if (!$episode->save()) {
            throw new Exception('Failed to save episode: '.print_r($episode->getErrors(), true));
        }

        $event = $this->createEvent();
        $element = $this->createVanHerickElement($event, Eye::LEFT);

        $principalVH = $this->api->getPrincipalVanHerick($this->patient('patient1'));
        $expected = 'Left Eye: Van Herick grade is Grade 3 (41-75%). ';
        $this->assertEquals($expected, $principalVH);
    }

    public function testGetPrincipalOpticDiscDescription()
    {
        $event = $this->createEvent();
        $element = $this->createOpticDiscElement($event, Eye::BOTH);

        $principalODD = $this->api->getPrincipalOpticDiscDescription($this->patient('patient1'));
        $expected = "Left Eye:\nr2\nld\nRight Eye:\nr1\nrd";
        $this->assertEquals($expected, $principalODD);
    }

    public function testGetPrincipalOpticDiscDescriptionNoPrincipalEye()
    {
        $episode = $this->episode('episode2');

        $episode->eye_id = null;
        if (!$episode->save()) {
            throw new Exception('Failed to save episode: '.print_r($episode->getErrors(), true));
        }

        $event = $this->createEvent();
        $element = $this->createOpticDiscElement($event, Eye::BOTH);

        $principalODD = $this->api->getPrincipalOpticDiscDescription($this->patient('patient1'));
        $expected = '';
        $this->assertEquals($expected, $principalODD);
    }

    public function testGetPrincipalOpticDiscDescriptionRight()
    {
        $episode = $this->episode('episode2');

        $episode->eye_id = Eye::RIGHT;
        if (!$episode->save()) {
            throw new Exception('Failed to save episode: '.print_r($episode->getErrors(), true));
        }

        $event = $this->createEvent();
        $element = $this->createOpticDiscElement($event, Eye::BOTH);

        $principalODD = $this->api->getPrincipalOpticDiscDescription($this->patient('patient1'));
        $expected = "Right Eye:\nr1\nrd";
        $this->assertEquals($expected, $principalODD);
    }

    public function testGetPrincipalOpticDiscDescriptionLeft()
    {
        $episode = $this->episode('episode2');

        $episode->eye_id = Eye::LEFT;
        if (!$episode->save()) {
            throw new Exception('Failed to save episode: '.print_r($episode->getErrors(), true));
        }

        $event = $this->createEvent();
        $element = $this->createOpticDiscElement($event, Eye::BOTH);

        $principalODD = $this->api->getPrincipalOpticDiscDescription($this->patient('patient1'));
        $expected = "Left Eye:\nr2\nld";
        $this->assertEquals($expected, $principalODD);
    }

    public function testGetLetterIOPReadingAbbr()
    {
        $event = $this->createEvent();
        $element = $this->createIopElement($event, Eye::RIGHT);
        $this->addIopReading($element, Eye::RIGHT, 1);

        $expected = 'r:1 (recorded on ' . date("j M Y") . ')';
        $this->assertEquals($expected, $this->api->getLetterIOPReadingAbbrLast6weeks($this->patient('patient1')));
    }

    public function testGetLetterIOPReadingAbbr_Right_Avg()
    {
        $event = $this->createEvent();
        $element = $this->createIopElement($event, Eye::RIGHT);
        $this->addIopReading($element, Eye::RIGHT, 1);
        $this->addIopReading($element, Eye::RIGHT, 3);

        $expected = 'r:2 (avg) (recorded on ' . date("j M Y") . ')';
        $this->assertEquals($expected, $this->api->getLetterIOPReadingAbbrLast6weeks($this->patient('patient1')));
    }

    public function testGetLetterIOPReadingAbbr_Left()
    {
        $event = $this->createEvent();
        $element = $this->createIopElement($event, Eye::LEFT);
        $this->addIopReading($element, Eye::LEFT, 2);

        $expected = 'l:2 (recorded on ' . date("j M Y") . ')';
        $this->assertEquals($expected, $this->api->getLetterIOPReadingAbbrLast6weeks($this->patient('patient1')));
    }

    public function testGetLetterIOPReadingAbbr_Left_Avg()
    {
        $event = $this->createEvent();
        $element = $this->createIopElement($event, Eye::LEFT);
        $this->addIopReading($element, Eye::LEFT, 2);
        $this->addIopReading($element, Eye::LEFT, 3);

        $expected = 'l:3 (avg) (recorded on ' . date("j M Y") . ')';
        $this->assertEquals($expected, $this->api->getLetterIOPReadingAbbrLast6weeks($this->patient('patient1')));
    }

    public function testGetLetterIOPReadingAbbr_Both()
    {
        $event = $this->createEvent();
        $element = $this->createIopElement($event, Eye::BOTH);
        $this->addIopReading($element, Eye::RIGHT, 1);
        $this->addIopReading($element, Eye::LEFT, 2);

        $expected = 'r:1, l:2 (recorded on ' . date("j M Y") . ')';
        $this->assertEquals($expected, $this->api->getLetterIOPReadingAbbrLast6weeks($this->patient('patient1')));
    }

    public function testGetLetterIOPReadingAbbr_Both_Avg()
    {
        $event = $this->createEvent();
        $element = $this->createIopElement($event, Eye::BOTH);
        $this->addIopReading($element, Eye::RIGHT, 1);
        $this->addIopReading($element, Eye::RIGHT, 3);
        $this->addIopReading($element, Eye::LEFT, 2);
        $this->addIopReading($element, Eye::LEFT, 3);

        $expected = 'r:2 (avg), l:3 (avg) (recorded on ' . date("j M Y") . ')';
        $this->assertEquals($expected, $this->api->getLetterIOPReadingAbbrLast6weeks($this->patient('patient1')));
    }

    public function testGetLetterIOPReadingRightNoUnits()
    {
        $event = $this->createEvent();
        $element = $this->createIopElement($event, Eye::BOTH);
        $this->addIopReading($element, Eye::RIGHT, 1);
        $this->addIopReading($element, Eye::RIGHT, 3);

        $expected = '2';
        $this->assertEquals($expected, trim($this->api->getLetterIOPReadingRightNoUnitsLast6weeks($this->patient('patient1'))));
    }

    public function testGetLetterIOPReadingLeftNoUnits()
    {
        $event = $this->createEvent();
        $element = $this->createIopElement($event, Eye::BOTH);
        $this->addIopReading($element, Eye::LEFT, 3);
        $this->addIopReading($element, Eye::LEFT, 3);

        $expected = '3';
        $this->assertEquals($expected, trim($this->api->getLetterIOPReadingLeftNoUnitsLast6weeks($this->patient('patient1'))));
    }

    public function testGetLetterIOPReadingLeftNoUnitsNotRecorded()
    {
        $event = $this->createEvent();
        $element = $this->createIopElement($event, Eye::BOTH);
        $this->addIopReading($element, Eye::RIGHT, 3);

        $expected = 'NR';
        $this->assertEquals($expected, trim($this->api->getLetterIOPReadingLeftNoUnitsLast6weeks($this->patient('patient1'))));
    }

    public function testGetLetterIOPReadingRightNoUnitsNotRecorded()
    {
        $event = $this->createEvent();
        $element = $this->createIopElement($event, Eye::LEFT);
        $this->addIopReading($element, Eye::LEFT, 3);

        $expected = 'NR';
        $this->assertEquals($expected, trim($this->api->getLetterIOPReadingRightNoUnitsLast6weeks($this->patient('patient1'))));
    }

    public function testGetCCTRight_NoUnits()
    {
        $event = $this->createEvent(date('Y-m-d 23:59:58'));
        $element = $this->createCctElement($event, Eye::RIGHT);

        $expected = '50';
        $this->assertEquals($expected, $this->api->getCCTRightNoUnits($this->patient('patient1')));
    }

    public function testGetCCTLeft_NoUnits()
    {
        $event = $this->createEvent(date('Y-m-d 23:59:58'));
        $element = $this->createCctElement($event, Eye::LEFT);

        $expected = '50';
        $this->assertEquals($expected, $this->api->getCCTLeftNoUnits($this->patient('patient1')));
    }

    public function testGetCCTRight_NoUnits_NotRecorded()
    {
        $event = $this->createEvent(date('Y-m-d 23:59:58'));
        $element = $this->createCctElement($event, Eye::LEFT);

        $expected = 'NR';
        $this->assertEquals($expected, $this->api->getCCTRightNoUnits($this->patient('patient1')));
    }

    public function testGetCCTLeft_NoUnits_NotRecorded()
    {
        $event = $this->createEvent(date('Y-m-d 23:59:58'));
        $element = $this->createCctElement($event, Eye::RIGHT);

        $expected = 'NR';
        $this->assertEquals($expected, $this->api->getCCTLeftNoUnits($this->patient('patient1')));
    }

    public function testGetTargetIOP()
    {
        $event = $this->createEvent(date('Y-m-d 23:59:58'));
        $element = $this->createOverallManagementPlanElement($event, 10, 20);

        $targetIop = $this->api->getTargetIOP($this->patient('patient1'));
        $expected = array('left' => 10, 'right' => 20);
        $this->assertEquals($expected, $targetIop);
    }

    public function testGetTargetIOPOneSideNull()
    {
        $event = $this->createEvent(date('Y-m-d 23:59:58'));
        $element = $this->createOverallManagementPlanElement($event, null, 15);

        $targetIop = $this->api->getTargetIOP($this->patient('patient1'));
        $expected = array('left' => null, 'right' => 15);
        $this->assertEquals($expected, $targetIop);
    }

    public function testGetTargetIOPReturnsNull()
    {
        $this->assertNull($this->api->getTargetIOP($this->patient('patient1')));
    }

    public function testGetIOPValuesAsTable()
    {
        $event = $this->createEvent(date('Y-m-d 23:59:58'));
        $iop = $this->createIopElement($event, Eye::BOTH);
        $this->addIopReading($iop, Eye::LEFT, 2);
        $this->addIopReading($iop, Eye::RIGHT, 2);
        $this->createCctElement($event, Eye::BOTH);

        $iopTable = $this->api->getIOPValuesAsTable($this->patient('patient1'));
        $expected = '<table class="borders"><colgroup><col class="cols-6"><col class="cols-6"></colgroup><tr><td>RE [50]</td><td>LE [50]</td></tr>'.
            '<tr><td>2:Gold</td><td>2:Gold</td></tr></table>';
        $this->assertEquals($expected, $iopTable);
    }

    public function testGetIOPValuesAsTableNotRecorded()
    {
        $this->assertEquals('', $this->api->getIOPValuesAsTable($this->patient('patient2')));
    }

    private function createEvent($event_date = null)
    {
        $event = new Event();
        $event->episode_id = $this->episode['episode2']['id'];
        $event->event_type_id = Yii::app()->db->createCommand('select id from event_type where class_name = "OphCiExamination"')->queryScalar();
        $event->firm_id = 2;
        if ($event_date) {
            $event->event_date = $event_date;
        }
        $event->delete_pending = 0;
        $event->save(false);

        return $event;
    }

    private function createCctElement(Event $event, $eye_id)
    {
        $element = new models\Element_OphCiExamination_AnteriorSegment_CCT();
        $element->event_id = $event->id;
        $element->eye_id = $eye_id;

        if ($eye_id == Eye::LEFT || $eye_id == Eye::BOTH) {
            $element->left_method_id = $this->cct_method['method1']['id'];
            $element->left_value = 50;
        }

        if ($eye_id == Eye::RIGHT || $eye_id == Eye::BOTH) {
            $element->right_method_id = $this->cct_method['method1']['id'];
            $element->right_value = 50;
        }

        $element->save(false);

        return $element;
    }

    private function createIopElement(Event $event, $eye_id)
    {
        $element = new models\Element_OphCiExamination_IntraocularPressure();
        $element->event_id = $event->id;
        $element->eye_id = $eye_id;
        $element->save(false);

        return $element;
    }

    /**
     * @param Element_OphCiExamination_IntraocularPressure $element
     * @param $eye_id
     * @param $value
     * @return OphCiExamination_IntraocularPressure_Value
     * @throws CException
     * @throws Exception
     */
    private function addIopReading(models\Element_OphCiExamination_IntraocularPressure $element, $eye_id, $value)
    {
        $reading = new models\OphCiExamination_IntraocularPressure_Value();
        $reading->element_id = $element->id;
        $reading->eye_id = $eye_id;
        $reading->instrument_id = 1;
        $reading->reading_id = Yii::app()->db->createCommand(
            'select id from ophciexamination_intraocularpressure_reading where value = ?'
        )->queryScalar(array($value));
        $reading->save(false);

        return $reading;
    }

    private function createOverallManagementPlanElement(Event $event, $left_target, $right_target)
    {
        $element = new models\Element_OphCiExamination_OverallManagementPlan();
        $element->event_id = $event->id;
        $element->left_target_iop_id = $left_target;
        $element->right_target_iop_id = $right_target;
        $element->gonio_id = 2;
        $element->clinic_interval_id = 1;
        $element->photo_id = 1;
        $element->oct_id = 4;
        $element->hfa_id = 1;
        $element->eye_id = 3;

        $element->save(false);

        return $element;
    }

    private function createVanHerickElement(Event $event, $eye_id)
    {
        $element = new models\VanHerick();
        $element->event_id = $event->id;
        $element->eye_id = $eye_id;
        $element->left_van_herick_id = 5;
        $element->right_van_herick_id = 5;
        $element->save(false);

        return $element;
    }

    private function createOpticDiscElement(Event $event, $eye_id)
    {
        $element = new models\Element_OphCiExamination_OpticDisc();
        $element->event_id = $event->id;
        $element->eye_id = $eye_id;
        $element->left_description = "ld";
        $element->right_description = "rd";
        $element->left_cd_ratio_id = 3;
        $element->right_cd_ratio_id = 4;
        $element->left_lens_id = 1;
        $element->right_lens_id = 1;
        $element->right_ed_report = "r1";
        $element->left_ed_report = "r2";
        $element->save(false);

        return $element;
    }
}
