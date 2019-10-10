<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_Risks;

class OphTrOperationbooking_WhiteboardTest extends CDbTestCase
{
    protected $fixtures = array(
        'operations' => Element_OphTrOperationbooking_Operation::class,
        'whiteboards' => OphTrOperationbooking_Whiteboard::class,
        'biometry_measurement' => Element_OphInBiometry_Measurement::class,
        'biometry_selection' => Element_OphInBiometry_Selection::class,
        'biometry_calculation' => Element_OphInBiometry_Calculation::class,
        'procedures' => OphTrOperationbooking_Operation_Procedures::class,
        'risks' => Element_OphCiExamination_Risks::class,
        'patients' => Patient::class,
        'events' => Event::class,
        'episodes' => Episode::class
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
                'booking_id' => 5,
                'fixtureId' => null,
                'procedure' => 'Dacrocystogram',
                'aconst' => null,
                'equipment' => null,
                'comment' => null,
                'complexity' => Element_OphTrOperationbooking_Operation::COMPLEXITY_MEDIUM,
            ),
            'Existing editable whiteboard' => array(
                'booking_id' => 6,
                'fixtureId' => 'whiteboard1',
                'procedure' => 'Repositioning of Intraocular lens',
                'aconst' => 118.0,
                'equipment' => "Test equipment 1\nTest equipment 2",
                'comment' => 'Test whiteboard',
                'complexity' => Element_OphTrOperationbooking_Operation::COMPLEXITY_MEDIUM,
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
     * @throws CHttpException
     */
    public function testLoadData($booking_id, $fixtureId, $procedure, $aconst, $equipment, $comment, $complexity)
    {
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

    /**
     * @param $booking_id int
     * @param $fixtureId string|null
     * @dataProvider getBookings
     * @covers OphTrOperationbooking_Whiteboard::getPatientRisksDisplay
     * @throws CHttpException
     */
    public function testGetPatientRisksDisplay($booking_id, $fixtureId)
    {
        $total_risks = 0;
        if ($fixtureId !== null) {
            $this->whiteboard = $this->whiteboards($fixtureId);
        } else {
            $this->whiteboard = new OphTrOperationbooking_Whiteboard();
            $this->whiteboard->loadData($booking_id);
        }
        $risks = $this->whiteboard->getPatientRisksDisplay($total_risks);
        $this->assertEquals(0, $total_risks);
        $this->assertEquals('<div class="alert-box success">No Risks</div>', $risks);
    }
}
