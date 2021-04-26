<?php

/**
 * Class PatientStatisticDatapointTest
 * @method stats($fixtureId)
 * @method datapoints($fixtureId)
 * @covers PatientStatisticDatapoint
 */
class PatientStatisticDatapointTest extends ActiveRecordTestCase
{
    protected $fixtures = array(
        'stats' => PatientStatistic::class,
        'datapoints' => PatientStatisticDatapoint::class
    );

    protected PatientStatisticDatapoint $datapoint;

    public function setUp()
    {
        parent::setUp();
        $this->datapoint = $this->datapoints('datapoint1');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->datapoint);
    }

    public function getModel()
    {
        return PatientStatisticDatapoint::model();
    }

    public function testGetLinearY()
    {
        $x = $this->datapoint->x_value;
        $m = $this->datapoint->statistic->gradient;
        $b = $this->datapoint->statistic->y_intercept;

        // Expected value follows the formula y = mx + b
        $expected = $m * $x + $b;
        $this->assertEquals($expected, $this->datapoint->getLinearY());

        if ($expected !== $this->datapoint->y_value) {
            $this->assertNotEquals($this->datapoint->y_value, $this->datapoint->getLinearY());
        }
    }
}
