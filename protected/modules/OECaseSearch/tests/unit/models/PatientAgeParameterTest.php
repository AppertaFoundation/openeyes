<?php

/**
 * Class PatientAgeParameterTest
 *
 * @method Patient patient($fixtureId)
 */
class PatientAgeParameterTest extends CDbTestCase
{
    /**
     * @var PatientAgeParameter
     */
    protected $parameter;

    protected $fixtures = array(
        'patient' => 'Patient',
    );

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->parameter = new PatientAgeParameter();
        $this->parameter->id = 0;
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->parameter);
    }

    public function testQuery()
    {
        $correctOps = array(
            '>',
            '<',
            '=',
            '!='
        );

        // Ensure the query is correct for each operator.
        foreach ($correctOps as $id => $operator) {
            switch ($id) {
                case 0:
                    $this->parameter->value = 5;
                    $this->parameter->operation = '>';
                    break;
                case 1:
                    $this->parameter->value = 80;
                    $this->parameter->operation = '<';
                    break;
                case 2:
                    $this->parameter->value = 50;
                    $this->parameter->operation = '=';
                    break;
                case 3:
                    $this->parameter->value = 50;
                    $this->parameter->operation = '!=';
                    break;
            }
            $this->parameter->operation = $operator;
            if ($operator === 'BETWEEN') {
                $sqlValue = "SELECT id FROM patient
WHERE TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) $operator :p_a_min_0 AND :p_a_max_0";
            } else {
                $sqlValue = "SELECT id FROM patient WHERE TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) $operator :p_a_value_0";
            }
            $this->assertEquals(
                trim(preg_replace('/\s+/', ' ', $sqlValue)),
                trim(preg_replace('/\s+/', ' ', $this->parameter->query()))
            );
        }
    }

    public function testBindValues()
    {
        $this->parameter->value = 50;
        $expected = array(
            'p_a_value_0' => $this->parameter->value,
        );
        $actual = $this->parameter->bindValues();
        $this->assertEquals($expected, $actual);

        // Ensure that all bind values are integers.
        $this->assertInternalType('int', $actual['p_a_value_0']);
    }

    /**
     * @throws Exception
     */
    public function testSearch()
    {
        // test an exact search using a simple operation
        $patients = array($this->patient('patient1'));
        $this->parameter->operation = '=';
        $dob = new DateTime($this->patient('patient1')['dob']);
        $this->parameter->value = $dob->diff(new DateTime())->format('%y');
        $results = Yii::app()->searchProvider->search(array($this->parameter));
        $ids = array();

        // deconstruct the results list into a single array of primary keys.
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $patientList = Patient::model()->findAllByPk($ids);

        // Ensure that results are returned.
        $this->assertEquals($patients, $patientList);

        $this->parameter->operation = '<';
        $this->parameter->value = 1;
        $results = Yii::app()->searchProvider->search(array($this->parameter));

        // Ensure that no results are returned.
        $this->assertEmpty($results);
    }
}
