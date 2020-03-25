<?php
/**
* Class VAVariableTest
*/
class AgeVariableTest extends CDbTestCase
{
    protected $variable;
    protected $searchProviders;
    protected $invalidProvider;

    public static function setUpBeforeClass()
    {
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->searchProviders = array();
        $this->variable = new AgeVariable([1, 2, 3]);
        $this->searchProviders[] = new DBProvider('provider0');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->variable, $this->searchProviders);
    }

    public function getData()
    {
        return array(
            'Standard' => array(
                'csv_mode' => null,
                'query_template' => 'SELECT TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) age, COUNT(*) frequency, GROUP_CONCAT(DISTINCT id) patient_id_list
        FROM patient p
        WHERE p.id IN (1, 2, 3)
            AND (:start_date IS NULL OR p.created_date > :start_date)
        AND (:end_date IS NULL OR p.created_date < :end_date)
        GROUP BY TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE()))',
            ),
            'Basic CSV' => array(
                'csv_mode' => 'BASIC',
                'query_template' => 'SELECT TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) age
        FROM patient p
        JOIN contact c ON c.id = p.contact_id
        WHERE p.id = p_outer.id
            AND (:start_date IS NULL OR p.created_date > :start_date)
        AND (:end_date IS NULL OR p.created_date < :end_date)'
            ),
            'Advanced CSV' => array(
                'csv_mode' => 'ADVANCED',
                'query_template' => 'SELECT TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) age
        FROM patient p
        JOIN contact c ON c.id = p.contact_id
        WHERE p.id = p_outer.id
            AND (:start_date IS NULL OR p.created_date > :start_date)
        AND (:end_date IS NULL OR p.created_date < :end_date)'
            ),
        );
    }

    /**
     * @dataProvider getData
     * @param $csv_mode
     * @param $query_template
     */
    public function testQuery($csv_mode, $query_template)
    {
        $expected = $query_template;
        $this->variable->csv_mode = $csv_mode;
        $this->assertEquals($expected, $this->variable->query($this->searchProviders[0]));
    }

    public function testGetVariableData()
    {
        $variables = array($this->variable);

        $this->assertEquals('age', $this->variable->field_name);
        $this->assertEquals('Age', $this->variable->label);
        $this->assertEquals('y', $this->variable->unit);
        $this->assertNotEmpty($this->variable->id_list);

        $results = $this->searchProviders[0]->getVariableData($variables);

        $this->assertCount(1, $results[$this->variable->field_name]);
        $this->assertCount(3, $results[$this->variable->field_name][0]);
    }
}
