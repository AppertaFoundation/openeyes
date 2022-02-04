<?php
/**
 * Class VAVariableTest
 * @covers AgeVariable
 * @covers CaseSearchVariable
 */
class AgeVariableTest extends CDbTestCase
{
    protected AgeVariable $variable;

    protected $fixtures = array(
        'patients' => Patient::class,
    );

    public static function setUpBeforeClass()
    {
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->variable = new AgeVariable([1, 2, 3]);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->variable);
    }

    public function getData(): array
    {
        return array(
            'Standard' => array(
                'csv_mode' => null,
                'query_template' => 'SELECT (10*FLOOR(TIMESTAMPDIFF(YEAR, p.dob, IFNULL(p.date_of_death, CURDATE()))/10)) age, COUNT(*) frequency, GROUP_CONCAT(DISTINCT id) patient_id_list
    FROM patient p
    WHERE p.id IN (1, 2, 3)
    AND (:start_date IS NULL OR p.created_date > :start_date)
    AND (:end_date IS NULL OR p.created_date < :end_date)
    GROUP BY FLOOR(TIMESTAMPDIFF(YEAR, p.dob, IFNULL(p.date_of_death, CURDATE()))/10)
    ORDER BY 1',
            ),
            'Basic CSV' => array(
                'csv_mode' => 'BASIC',
                'query_template' => 'SELECT (10*FLOOR(TIMESTAMPDIFF(YEAR, p.dob, IFNULL(p.date_of_death, CURDATE()))/10)) age, COUNT(*) frequency
        FROM patient p
        WHERE p.id IN (1, 2, 3)
        AND (:start_date IS NULL OR p.created_date > :start_date)
        AND (:end_date IS NULL OR p.created_date < :end_date)
        GROUP BY FLOOR(TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE()))/10)
        ORDER BY 1'
            ),
            'Advanced CSV' => array(
                'csv_mode' => 'ADVANCED',
                'query_template' => 'SELECT (
            SELECT pi.value
            FROM patient_identifier pi
                JOIN patient_identifier_type pit ON pit.id = pi.patient_identifier_type_id
            WHERE pi.patient_id = p.id
            AND pit.usage_type = \'GLOBAL\'
            ) nhs_num, TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) age, p.created_date, null
        FROM patient p
        JOIN contact c ON c.id = p.contact_id
        WHERE p.id IN (1, 2, 3)
        AND (:start_date IS NULL OR p.created_date > :start_date)
        AND (:end_date IS NULL OR p.created_date < :end_date)
        ORDER BY 1, 2, 3'
            ),
        );
    }

    /**
     * @dataProvider getData
     * @param $csv_mode
     * @param $query_template
     */
    public function testQuery($csv_mode, $query_template): void
    {
        $expected = $query_template;
        $this->variable->csv_mode = $csv_mode;
        self::assertEquals($expected, $this->variable->query());
    }

    public function testGetVariableData(): void
    {
        $variables = array($this->variable);

        $expected_rows = Yii::app()->db->createCommand()
            ->select('(10*FLOOR(TIMESTAMPDIFF(YEAR, p.dob, IFNULL(p.date_of_death, CURDATE()))/10)) age')
            ->from('patient p')
            ->where('p.id IN (1, 2, 3)')
            ->group('FLOOR(TIMESTAMPDIFF(YEAR, p.dob, IFNULL(p.date_of_death, CURDATE()))/10)')
            ->order('(1)')
            ->queryColumn();

        $this->assertEquals('age', $this->variable->field_name);
        $this->assertEquals('Age', $this->variable->label);
        $this->assertEquals('Age (y)', $this->variable->x_label);
        $this->assertNotEmpty($this->variable->id_list);

        $results = Yii::app()->searchProvider->getVariableData($variables);

        $this->assertCount(count($expected_rows), $results[$this->variable->field_name]);
    }
}
