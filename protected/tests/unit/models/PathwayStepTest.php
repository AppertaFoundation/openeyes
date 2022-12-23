<?php

/**
 * Class PathwayStepTest
 * @covers PathwayStep
 * @method Pathway pathways($fixtureID)
 * @method PathwayStep steps($fixtureID)
 */
class PathwayStepTest extends ActiveRecordTestCase
{
    protected $fixtures = array(
        'worklists' => Worklist::class,
        'worklist_patients' => WorklistPatient::class,
        'pathways' => Pathway::class,
        'steps' => PathwayStep::class,
    );

    public function getModel()
    {
        return PathwayStep::model();
    }

    public function testGetStatusString(): void
    {
        $step1 = $this->steps('step1');
        $step2 = $this->steps('step2');
        $step3 = $this->steps('step3');

        self::assertEquals('done', $step1->getStatusString());
        self::assertEquals('active', $step2->getStatusString());
        self::assertEquals('todo', $step3->getStatusString());
    }

    public function testToJSON(): void
    {
        $step = $this->steps('step1');
        $expected = [
            'id' => '1',
            'patient_id' => '1',
            'status' => 'done',
            'type' => 'process',
            'short_name' => 'Bio',
            'start_time' => null,
            'end_time' => null,
            'state_data' => null,
            'start_timestamp' => null,
            'now_timestamp' => time(),
            'pathway_id' => '1',
        ];
        $this->assertEquals($expected, $step->toJSON());

        $step = $this->steps('step2');
        $expected = [
            'id' => 2,
            'patient_id' => 1,
            'status' => 'active',
            'type' => 'process',
            'short_name' => 'Exam',
            'start_time' => null,
            'end_time' => null,
            'state_data' => null,
            'start_timestamp' => null,
            'now_timestamp' => time(),
            'pathway_id' => 1,
        ];
        $this->assertEquals($expected, $step->toJSON());
    }

    /**
     * @throws Exception
     */
    public function testPrevStatus(): void
    {
        $step = $this->steps('step1');
        $step->prevStatus();
        self::assertEquals(PathwayStep::STEP_STARTED, $step->status);

        $step = $this->steps('step2');
        $step->prevStatus();
        self::assertEquals(PathwayStep::STEP_REQUESTED, $step->status);

        $step->prevStatus();
        self::assertEquals(PathwayStep::STEP_CONFIG, $step->status);

        $this->expectException('Exception');
        $step->prevStatus();
    }

    /**
     * @throws Exception
     */
    public function testNextStatus(): void
    {
        $step = $this->steps('step3');
        $step->nextStatus();
        self::assertEquals(PathwayStep::STEP_STARTED, $step->status);

        $step = $this->steps('step2');
        $step->nextStatus();
        self::assertEquals(PathwayStep::STEP_COMPLETED, $step->status);

        $step->nextStatus();
        self::assertEquals(PathwayStep::STEP_COMPLETED, $step->status);
    }

    /**
     * @throws Exception
     */
    public function testGetStepStartTime(): void
    {
        $pathway = $this->pathways('pathway6');
        $pathway->startPathway();
        $checkin_time = null;
        foreach($pathway->steps as $step) {
            if ($step->short_name === 'checkin') {
                // getStepStartTime returns mm:ss string, convert it to datetime
                $checkin_time = DateTime::createFromFormat('H:i', $step->getStepStartTime());
                break;
            }
        }
        // assert checkin_time is a DateTime instance
        self::assertEquals(true, $checkin_time instanceof DateTime);
    }

    /**
     * @throws Exception
     */
    public function testGetStepStartTimeDNA(): void
    {
        $pathway = $this->pathways('pathway6');
        $checkin_time = null;

        foreach($pathway->steps as $step) {
            if ($step->short_name === 'checkin') {
                // setup DNA state
                $step->status = (string)PathwayStep::STEP_COMPLETED;
                $step->start_time = date('Y-m-d H:i:s');
                $step->end_time = date('Y-m-d H:i:s');
                $pathway->did_not_attend = true;
                $pathway->save();
                $pathway->refresh();
                $checkin_time = $step->getStepStartTime();
                break;
            }
        }

        self::assertEquals('DNA', $checkin_time);
    }
}
