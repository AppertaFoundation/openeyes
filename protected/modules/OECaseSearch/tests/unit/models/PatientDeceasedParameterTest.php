<?php

/**
 * Class PatientDeceasedParameterTest
 */
class PatientDeceasedParameterTest extends CDbTestCase
{
    protected $parameter;
    protected $searchProvider;
    protected $fixtures = array(
        'patient' => 'Patient',
    );

    protected function setUp()
    {
        parent::setUp();
        $this->parameter = new PatientDeceasedParameter();
        $this->searchProvider = new DBProvider('mysql');
        $this->parameter->id = 0;
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->parameter, $this->searchProvider);
    }

    /**
     * @covers PatientDeceasedParameter::query()
     */
    public function testQuery()
    {
        $correctOps = array(
            '1',
            '0',
        );
        $invalidOps = array(
            'LIKE',
            'NOT LIKE',
        );

        // Ensure the query is correct for each operator.
        foreach ($correctOps as $operator) {
            $this->parameter->operation = $operator;
            $sqlValue = ($operator === '0') ? "SELECT id FROM patient WHERE NOT(is_deceased)" : "SELECT id FROM patient";
            $this->assertEquals(
                trim(preg_replace('/\s+/', ' ', $sqlValue)),
                trim(preg_replace('/\s+/', ' ', $this->parameter->query($this->searchProvider)))
            );
        }

        // Ensure that a HTTP exception is raised if an invalid operation is specified.
        $this->setExpectedException(CHttpException::class);
        foreach ($invalidOps as $operator) {
            $this->parameter->operation = $operator;
            $this->parameter->query($this->searchProvider);
        }
    }

    /**
     * @covers PatientDeceasedParameter::bindValues()
     */
    public function testBindValues()
    {
        $this->parameter->operation = '1';
        $expected = array();

        // Ensure that all bind values are returned.
        $this->assertEquals($expected, $this->parameter->bindValues());
    }

    /**
     * @covers DBProvider::search()
     * @covers PatientDeceasedParameter::query()
     */
    public function testSearch()
    {
        // Ensure all patient fixtures are returned.
        $match = array();
        for ($i = 1; $i < 10; $i++) {
            $match[] = $this->patient("patient$i");
        }

        $this->parameter->operation = '1';

        $results = $this->searchProvider->search(array($this->parameter));

        $ids = array();
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $patients = Patient::model()->findAllByPk($ids);

        $this->assertEquals($match, $patients);

        // Ensure all patient fixtures except patient9 are returned.
        $this->parameter->operation = '0';
        $match = array();
        for ($i = 1; $i < 9; $i++) {
            $match[] = $this->patient("patient$i");
        }

        $results = $this->searchProvider->search(array($this->parameter));

        $ids = array();
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $patients = Patient::model()->findAllByPk($ids);

        $this->assertEquals($match, $patients);

    }
}
