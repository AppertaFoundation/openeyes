<?php

/**
 * Class PatientNumberParameterTest
 */
class PatientNumberParameterTest extends CDbTestCase
{
    protected $parameter;
    /**
     * @var DBProvider
     */
    protected $searchProvider;
    protected $fixtures = array(
        'patient' => 'Patient'
    );

    protected function setUp()
    {
        parent::setUp();
        $this->parameter = new PatientNumberParameter();
        $this->searchProvider = new DBProvider('mysql');
        $this->parameter->id = 0;
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->parameter, $this->searchProvider);
    }

    /**
     * @covers DBProvider::search()
     * @covers DBProvider::executeSearch()
     * @covers PatientNumberParameter::query()
     * @covers PatientNumberParameter::bindValues()
     */
    public function testSearch()
    {
        $expected = array($this->patient('patient1'));

        $this->parameter->operation = '=';
        $this->parameter->number = 12345;

        $secondParam = new PatientNumberParameter();
        $secondParam->operation = '=';
        $secondParam->number = 12345;

        $results = $this->searchProvider->search(array($this->parameter, $secondParam));

        $this->assertCount(1, $results);
        $actual = Patient::model()->findAllByPk($results[0]);

        $this->assertEquals($expected, $actual);
    }
}
