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

    public function getQueryParams()
    {
        return array(
            'Greater than' => array(
                'op' => '>',
                'value' => 5,
            ),
            'Less than' => array(
                'op' => '>',
                'value' => 80,
            ),
            'Equal' => array(
                'op' => '>',
                'value' => 50,
            ),
            'Not equal' => array(
                'op' => '>',
                'value' => 50,
            ),
        );
    }

    /**
     * @dataProvider getQueryParams
     * @param $op
     * @param $value
     */
    public function testQuery($op, $value)
    {
        $this->parameter->value = $value;
        $this->parameter->operation = $op;
        $sqlValue = "SELECT id FROM patient WHERE TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) $op :p_a_value_0";
        $this->assertEquals(
            trim(preg_replace('/\s+/', ' ', $sqlValue)),
            trim(preg_replace('/\s+/', ' ', $this->parameter->query()))
        );
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
