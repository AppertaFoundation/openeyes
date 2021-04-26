<?php

/**
 * Class PatientStatisticTest
 * @method stats($fixtureId)
 * @method datapoints($fixtureId)
 * @covers PatientStatistic
 * @covers PatientStatisticDatapoint
 */
class PatientStatisticTest extends ActiveRecordTestCase
{
    protected $fixtures = array(
        'stats' => PatientStatistic::class,
        'datapoints' => PatientStatisticDatapoint::class
    );

    protected PatientStatistic $statistic;

    public function setUp()
    {
        parent::setUp();
        $this->statistic = $this->stats('stat1');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->statistic);
    }

    /**
     * @return int[][]
     */
    public function getPlotValues()
    {
        return array(
            'plot1' => array('x' => 1, 'y' => 7),
            'plot2' => array('x' => 2, 'y' => 9),
            'plot3' => array('x' => -1, 'y' => 3),
            'plot4' => array('x' => 0.5, 'y' => 6),
            'plot5' => array('x' => -2.2, 'y' => 0.6),
        );
    }

    public function getModel()
    {
        return PatientStatistic::model();
    }

    public function testGetMinDatapoint()
    {
        $expected = $this->datapoints('datapoint2');
        $this->assertEquals($expected, $this->statistic->getMinDatapoint());
    }

    /**
     * @dataProvider getPlotValues
     * @param $x float
     * @param $y float
     */
    public function testGetXForLinearY(float $x, float $y)
    {
        $this->assertEquals($x, $this->statistic->getXForLinearY($y));
    }

    public function testGetMaxDatapoint()
    {
        $expected = $this->datapoints('datapoint4');
        $this->assertEquals($expected, $this->statistic->getMaxDatapoint());
    }

    /**
     * @dataProvider getPlotValues
     * @param $x float
     * @param $y float
     */
    public function testGetLinearYForX(float $x, float $y)
    {
        $this->assertEquals($y, $this->statistic->getLinearYForX($x));
    }
}
