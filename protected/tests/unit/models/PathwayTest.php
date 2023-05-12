<?php

/**
 * Class PathwayTest
 * @covers Pathway
 * @method Pathway pathways($fixtureID)
 * @method PathwayStep steps($fixtureID)
 */
class PathwayTest extends ActiveRecordTestCase
{
    protected $fixtures = array(
        'worklists' => Worklist::class,
        'worklist_patients' => WorklistPatient::class,
        'pathways' => Pathway::class,
        'pathway_steps' => PathwayStep::class,
    );

    public function getModel()
    {
        return Pathway::model();
    }

    /**
     * @return string[][]
     */
    public function getPathwayFixtures(): array
    {
        return array(
            array('fixture_id' => 'pathway1', 'status_string' => 'later'),
            array('fixture_id' => 'pathway2', 'status_string' => 'active'),
            array('fixture_id' => 'pathway3', 'status_string' => 'long-wait'),
            array('fixture_id' => 'pathway4', 'status_string' => 'checked-out'),
            array('fixture_id' => 'pathway5', 'status_string' => 'done'),
        );
    }

    /**
     * @dataProvider getPathwayFixtures
     * @param string $fixture_id
     * @param string $status_string
     */
    public function testGetStatusString(string $fixture_id, string $status_string): void
    {
        self::assertEquals($status_string, $this->pathways($fixture_id)->getStatusString());
    }

    /**
     * @dataProvider getPathwayFixtures
     * @param string $fixture_id
     */
    public function testGetPathwayStatusHTML(string $fixture_id): void
    {
        $pathway = $this->pathways($fixture_id);
        $class = 'oe-i pad js-has-tooltip ';
        switch ($pathway->status) {
            case Pathway::STATUS_LATER:
                $class .= 'no-permissions small-icon';
                $tooltip_text = 'Pathway not started';
                break;
            case Pathway::STATUS_DISCHARGED:
                $class .= 'save medium-icon js-pathway-complete';
                $tooltip_text = 'Pathway completed';
                break;
            case Pathway::STATUS_DONE:
                // Done.
                // Show undo icon only if the pathway has incomplete steps
                if ($pathway->hasIncompleteSteps()) {
                    $class .= 'undo medium-icon js-pathway-reactivate';
                    $tooltip_text = 'Re-activate pathway to add steps';
                } else {
                    $class .= 'oe-i save medium-icon pad js-tooltip';
                    $tooltip_text = 'Pathway complete';
                }
                break;
            default:
                // Covers all 'active' statuses, including long-wait and break.
                $class .= 'save-blue medium-icon js-pathway-finish';
                $tooltip_text = 'Quick complete pathway';
                break;
        }
        $expected = "<i class=\"$class\" data-tooltip-content=\"$tooltip_text\" data-visit-id=\"$pathway->id\"></i>";
        self::assertEquals($expected, $pathway->getPathwayStatusHTML());
    }

    /**
     * @dataProvider getPathwayFixtures
     * @param string $fixture_id
     * @throws Exception
     */
    public function testRemoveIncompleteSteps(string $fixture_id): void
    {
        $pathway = $this->pathways($fixture_id);
        $initial_completed_steps = count($pathway->completed_steps);
        $initial_incomplete_steps = count($pathway->requested_steps) + count($pathway->started_steps);
        self::assertEquals($initial_incomplete_steps, $pathway->removeIncompleteSteps());
        $pathway->refresh();

        self::assertCount(0, $pathway->requested_steps);
        self::assertCount(0, $pathway->started_steps);
        self::assertCount($initial_completed_steps, $pathway->completed_steps);
    }

    /**
     * @dataProvider getPathwayFixtures
     * @param string $fixture_id
     * @throws Exception
     */
    public function testCompleteIncompleteSteps(string $fixture_id): void
    {
        $pathway = $this->pathways($fixture_id);
        $initial_requested_steps = count($pathway->requested_steps);
        $initial_active_steps = count($pathway->started_steps);
        $initial_completed_steps = count($pathway->completed_steps);
        $pathway->completeIncompleteSteps();
        $pathway->refresh();

        self::assertCount(0, $pathway->requested_steps);
        self::assertCount(0, $pathway->started_steps);
        self::assertCount(
            $initial_completed_steps + $initial_active_steps + $initial_requested_steps,
            $pathway->completed_steps
        );
    }

    /**
     * @dataProvider getPathwayFixtures
     * @param string $fixture_id
     */
    public function testPeek(string $fixture_id): void
    {
        $pathway = $this->pathways($fixture_id);
        $first_requested = empty($pathway->requested_steps) ? null : $pathway->requested_steps[0];
        $first_active = empty($pathway->started_steps) ? null : $pathway->started_steps[0];
        $last_completed = empty($pathway->completed_steps) ? null : $pathway->completed_steps[count($pathway->completed_steps) - 1];

        $this->assertEquals($first_requested, $pathway->peek(PathwayStep::STEP_REQUESTED));
        $this->assertEquals($first_active, $pathway->peek(PathwayStep::STEP_STARTED));
        $this->assertEquals($last_completed, $pathway->peek(PathwayStep::STEP_COMPLETED));
    }

    /**
     * @dataProvider getPathwayFixtures
     * @param string $fixture_id
     */
    public function testStepsAsJSON(string $fixture_id): void
    {
        $json = array();
        $pathway = $this->pathways($fixture_id);
        foreach ($pathway->requested_steps as $step) {
            $json['requested_steps'][] = $this->makeStepJsonComparable($step->toJSON());
        }
        foreach ($pathway->started_steps as $step) {
            $json['started_steps'][] = $this->makeStepJsonComparable($step->toJSON());
        }
        foreach ($pathway->completed_steps as $step) {
            $json['completed_steps'][] = $this->makeStepJsonComparable($step->toJSON());
        }

        $result = $pathway->stepsAsJSON();
        foreach ($result as $k => $result_json) {
            $this->assertEquals(
                $json[$k],
                array_map(function ($json) {
                    return $this->makeStepJsonComparable($json);
                }, $result_json)
            );
        }
    }

    /**
     * @dataProvider getPathwayFixtures
     * @param string $fixture_id
     * @throws Exception
     */
    public function testEnqueue(string $fixture_id): void
    {
        $pathway = $this->pathways($fixture_id);

        // Enqueue an existing step that has had a status change.
        // If there are requested steps, grab the first one. Otherwise, grab the first completed step.
        $step = $pathway->peek(PathwayStep::STEP_REQUESTED) ?? $pathway->peek(PathwayStep::STEP_COMPLETED);

        $step->status = PathwayStep::STEP_STARTED;
        self::assertTrue($pathway->enqueue($step));

        // Enqueue a newly created step.
        $step = new PathwayStep();
        $step->pathway_id = $pathway->id;
        $step->step_type_id = 10;
        $step->short_name = 'Rx';
        $step->long_name = 'Prescription';
        $step->status = PathwayStep::STEP_REQUESTED;
        self::assertTrue($pathway->enqueue($step));
    }

    /**
     * Strips out variable attributes
     */
    public function makeStepJsonComparable($json)
    {
        foreach (['now_timestamp'] as $incomparable) {
            unset($json[$incomparable]);
        }

        return $json;
    }
}
