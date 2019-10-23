<?php

use OEModule\OphCiExamination\models\HistoryRisks;
use OEModule\OphCiExamination\models\HistoryRisksEntry;

class OphTrOperationbooking_WhiteboardTest extends CDbTestCase
{
    protected $fixtures = array(
        'operations' => Element_OphTrOperationbooking_Operation::class,
        'operation_procedures' => OphTrOperationbooking_Operation_Procedures::class,
        'whiteboards' => OphTrOperationbooking_Whiteboard::class,
        'biometry_measurement' => Element_OphInBiometry_Measurement::class,
        'biometry_selection' => Element_OphInBiometry_Selection::class,
        'biometry_calculation' => Element_OphInBiometry_Calculation::class,
        'procedures' => OphTrOperationbooking_Operation_Procedures::class,
        'patients' => Patient::class,
        'events' => Event::class,
        'episodes' => Episode::class,
        'event_types' => EventType::class,
        'element_types' => ElementType::class,
        'eye' => Eye::class,
        'procedure_risk' => ProcedureRisk::class,
        'history_risks' => HistoryRisks::class,
        'history_risk_entry' => HistoryRisksEntry::class,
    );

    public static function setUpBeforeClass()
    {
        Yii::app()->getModule('OphCiExamination');
    }

    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->whiteboard);
        parent::tearDown();
    }

    public function getBookings()
    {
        return array(
            'New whiteboard' => array(
                'booking_id' => 26,
                'fixtureId' => null,
                'procedure' => 'Foobar Procedure',
                'aconst' => null,
                'equipment' => null,
                'comment' => null,
                'complexity' => Element_OphTrOperationbooking_Operation::COMPLEXITY_MEDIUM,
                'expected_total_risks' => 3,
            ),
            'Existing editable whiteboard' => array(
                'booking_id' => 26,
                'fixtureId' => 'whiteboard1',
                'procedure' => 'Foobar Procedure',
                'aconst' => 118.0,
                'equipment' => "Test equipment 1\nTest equipment 2",
                'comment' => 'Test whiteboard',
                'complexity' => Element_OphTrOperationbooking_Operation::COMPLEXITY_MEDIUM,
                'expected_total_risks' => 3,
            ),
            'Existing non-editable whiteboard' => array(
                'booking_id' => null,
                'fixtureId' => 'whiteboard2',
                'procedure' => 'Test Procedure',
                'aconst' => 118.0,
                'equipment' => "Test equipment 1\nTest equipment 2",
                'comment' => 'Test whiteboard',
                'complexity' => Element_OphTrOperationbooking_Operation::COMPLEXITY_MEDIUM,
                'expected_total_risks' => 3,
            ),
        );
    }

    /**
     * @param $booking_id int
     * @param $fixtureId string|null
     * @param $procedure string
     * @param $aconst double|null
     * @param $equipment string
     * @param $comment string
     * @param $complexity int
     * @dataProvider getBookings
     * @covers OphTrOperationbooking_Whiteboard::loadData
     * @covers OphTrOperationbooking_Whiteboard::recentBiometry
     * @covers OphTrOperationbooking_Whiteboard::recentBiometryReport
     * @covers OphTrOperationbooking_Whiteboard::allergyString
     * @covers OphTrOperationbooking_Whiteboard::alphaBlockerStatusAndDate
     * @covers OphTrOperationbooking_Whiteboard::anticoagsStatusAndDate
     * @covers OphTrOperationbooking_Whiteboard::operation
     * @covers OphTrOperationbooking_Whiteboard::getDisplayHasRisk
     * @throws CHttpException
     */
    public function testLoadData($booking_id, $fixtureId, $procedure, $aconst, $equipment, $comment, $complexity = 0)
    {
        if ($booking_id) {
            if ($fixtureId !== null) {
                $this->whiteboard = $this->whiteboards($fixtureId);
            } else {
                $this->whiteboard = new OphTrOperationbooking_Whiteboard();
            }
            $this->whiteboard->loadData($booking_id);
            $this->assertEquals($procedure, $this->whiteboard->procedure);
            $this->assertEquals($aconst, $this->whiteboard->aconst);
            $this->assertEquals($equipment, $this->whiteboard->predicted_additional_equipment);
            $this->assertEquals($comment, $this->whiteboard->comments);
            $this->assertEquals($complexity, $this->whiteboard->complexity);
        }
    }

    /**
     * @param $booking_id int
     * @param $fixtureId string|null
     * @param $procedure string
     * @param $aconst double|null
     * @param $equipment string
     * @param $comment string
     * @param $complexity int
     * @param $expected_total_risks int
     * @dataProvider getBookings
     * @covers OphTrOperationbooking_Whiteboard::getPatientRisksDisplay
     * @depends testLoadData
     * @throws CHttpException
     */
    public function testGetPatientRisksDisplay(
        $booking_id,
        $fixtureId,
        $procedure,
        $aconst = null,
        $equipment = null,
        $comment = null,
        $complexity = 0,
        $expected_total_risks = 0
    ) {
        $total_risks = 0;
        if ($fixtureId !== null) {
            $this->whiteboard = $this->whiteboards($fixtureId);

            if ($booking_id) {
                $this->whiteboard->loadData($booking_id);
            } else {
                // Map the booking element to the whiteboard for its event.
                $this->whiteboard->booking = Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($this->whiteboard->event_id));
            }
        } else {
            $this->whiteboard = new OphTrOperationbooking_Whiteboard();
            $this->whiteboard->loadData($booking_id);
        }
        $risks = $this->whiteboard->getPatientRisksDisplay($total_risks);
        $this->assertEquals($expected_total_risks, $total_risks);
        if ($expected_total_risks > 0) {
            $this->assertNotNull($risks);
        } else {
            $this->assertEquals('', $risks);
        }
    }
}
