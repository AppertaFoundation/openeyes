<?php

/**
 * Class PatientAgeParameterTest
 */
class PatientAgeParameterTest extends CDbTestCase
{
    /**
     * @var PatientAgeParameter
     */
    protected $parameter;

    /**
     * @var DBProvider
     */
    protected $searchProvider;

    protected $fixtures = array(
        'patient' => 'Patient',
    );

    protected function setUp()
    {
        parent::setUp();
        $this->parameter = new PatientAgeParameter();
        $this->searchProvider = new DBProvider('mysql');
        $this->parameter->id = 0;
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->parameter, $this->searchProvider);
    }

    /**
     * @covers PatientAgeParameter::query()
     */
    public function testQuery()
    {
        $this->parameter->textValue = 5;
        $this->parameter->minValue = 5;
        $this->parameter->maxValue = 80;
        $correctOps = array(
            '>=',
            '<=',
            'BETWEEN',
        );
        $invalidOps = array(
            'LIKE',
            'NOT LIKE',
        );

        // Ensure the query is correct for each operator.
        foreach ($correctOps as $operator) {
            $this->parameter->operation = $operator;
            if ($operator === 'BETWEEN') {
                $sqlValue = "SELECT id FROM patient WHERE TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) $operator :p_a_min_0 AND :p_a_max_0";
            } else {
                $sqlValue = "SELECT id FROM patient WHERE TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) $operator :p_a_value_0";
            }
            $this->assertEquals(
                trim(preg_replace('/\s+/', ' ', $sqlValue)),
                trim(preg_replace('/\s+/', ' ', $this->parameter->query($this->searchProvider))));
        }

        // Ensure that a HTTP exception is raised if an invalid operation is specified.
        $this->setExpectedException(CHttpException::class);
        foreach ($invalidOps as $operator) {
            $this->parameter->operation = $operator;
            $this->parameter->query($this->searchProvider);
        }
    }

    /**
     * @covers PatientAgeParameter::bindValues()
     */
    public function testBindValues()
    {
        $this->parameter->textValue = 5;
        $this->parameter->operation = 'BETWEEN';
        $this->parameter->minValue = 5;
        $this->parameter->maxValue = 80;
        $expected = array(
            'p_a_min_0' => $this->parameter->minValue,
            'p_a_max_0' => $this->parameter->maxValue,
        );
        $actual = $this->parameter->bindValues();

        // Ensure that (if all elements are set) all bind values are returned.
        $this->assertEquals($expected, $actual);

        // Ensure that all bind values are integers.
        $this->assertTrue(is_int($actual['p_a_min_0']) and is_int($actual['p_a_max_0']));

        // Ensure that only attributes with values are returned from the bindValues list.
        unset($expected['p_a_value_0']);
        $this->parameter->textValue = null;

        $this->assertEquals($expected, $this->parameter->bindValues());
    }

    /**
     * @covers DBProvider::search()
     * @covers PatientAgeParameter::query()
     */
    public function testSearchSingleInput()
    {
        // test an exact search using a simple operation
        $patients = array($this->patient('patient1'));
        $this->parameter->operation = 'BETWEEN';
        $dob = new DateTime($this->patient['patient1']['dob']);
        $this->parameter->maxValue = $this->parameter->minValue = $dob->diff(new DateTime())->format('%y');
        $results = $this->searchProvider->search(array($this->parameter));
        $ids = array();

        // deconstruct the results list into a single array of primary keys.
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $patientList = Patient::model()->findAllByPk($ids);

        // Ensure that results are returned.
        $this->assertEquals($patients, $patientList);

        $this->parameter->operation = 'BETWEEN';
        $this->parameter->minValue = 1;
        $this->parameter->maxValue = 1;
        $results = $this->searchProvider->search(array($this->parameter));

        // Ensure that no results are returned.
        $this->assertEmpty($results);
    }

    /**
     * @covers DBProvider::search()
     * @covers PatientAgeParameter::query()
     */
    public function testSearchDualInput()
    {
        $patients = array();
        $this->parameter->operation = 'BETWEEN';
        $this->parameter->minValue = 5;
        $this->parameter->maxValue = 80;

        for ($i = 1; $i < 10; $i++) {
            $patients[] = $this->patient("patient$i");
        }

        $results = $this->searchProvider->search(array($this->parameter));

        $ids = array();

        // deconstruct the results list into a single array of primary keys.
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $patientList = Patient::model()->findAllByPk($ids);

        // Ensure that results are returned.
        $this->assertEquals($patients, $patientList);
    }
}
