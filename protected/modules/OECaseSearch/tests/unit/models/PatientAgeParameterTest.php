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
        'saved_search' => 'SavedSearch'
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
            'Invalid' => array(
                'op' => '',
                'value' => '',
                'error' => true
            )
        );
    }

    /**
     * @dataProvider getQueryParams
     * @param $op
     * @param $value
     * @param $error
     */
    public function testQuery($op, $value, $error = false)
    {
        $this->parameter->value = $value;
        $this->parameter->operation = $op;
        if ($error) {
            $this->parameter->validate();
            $this->assertNotEmpty($this->parameter->getErrors());
        } else {
            $sqlValue = "SELECT id FROM patient WHERE TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) $op :p_a_value_0";
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
        // test an exact search using a simple operation. This also ensures coverage of the constructors.
        $patients = array($this->patient('patient1'));
        $parameter = new PatientAgeParameter();
        $parameter->operation = '=';
        $dob = new DateTime($this->patient('patient1')['dob']);
        $parameter->value = $dob->diff(new DateTime())->format('%y');
        $results = Yii::app()->searchProvider->search(array($parameter));
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

    /**
     * @covers PatientAgeParameter
     * @throws Exception
     */
    public function testSaveSearch()
    {
        $this->parameter->operation = '<';
        $this->parameter->value = 50;
        $search = new SavedSearch();
        $search_criteria = serialize(array($this->parameter->saveSearch()));
        $expected = 'a:1:{i:0;a:5:{s:10:"class_name";s:19:"PatientAgeParameter";s:4:"name";s:3:"age";s:9:"operation";s:1:"<";s:2:"id";i:0;s:5:"value";i:50;}}';
        $this->assertEquals($expected, $search_criteria);

        $search->search_criteria = $search_criteria;
        $search->name = 'test';

        if (!$search->save()) {
            $this->fail('Unable to save search');
        }
    }

    /**
     * @depends testSaveSearch
     * @covers CaseSearchParameter
     * @throws Exception
     */
    public function testLoadSearch()
    {
        $this->parameter->operation = '<';
        $this->parameter->value = 50;
        $search = new SavedSearch();
        $search_criteria = serialize(array($this->parameter->saveSearch()));
        $search->search_criteria = $search_criteria;
        $search->name = 'test';

        if (!$search->save()) {
            $this->fail('Unable to save search');
        }

        $search->refresh();

        $loaded_search = SavedSearch::model()->findByPk($search->id);
        $loaded_criteria = unserialize($loaded_search->search_criteria, array('allowed_classes' => true));
        $loaded_parameter = new PatientAgeParameter();
        $loaded_parameter->loadSearch($loaded_criteria[0]);

        $this->assertEquals($this->parameter, $loaded_parameter);
    }

    public function testGetAuditData()
    {
        $this->parameter->operation = '=';
        $this->parameter->value = 50;

        $this->assertEquals('age: = 50', $this->parameter->getAuditData());
    }
}
