<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_OCT;

/**
* Class CRTVariableTest
*/
class CRTVariableTest extends CDbTestCase
{
    protected $variable;

    protected $fixtures = array(
        'crt' => Element_OphCiExamination_OCT::class,
        'events' => Event::class,
        'episodes' => Episode::class,
        'patients' => Patient::class,
        'event_types' => EventType::class,
        'firms' => Firm::class,
        'ssa' => ServiceSubspecialtyAssignment::class,
        'subspecialties' => Subspecialty::class,
    );

    public static function setUpBeforeClass()
    {
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->variable = new CRTVariable([1, 2, 3]);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->variable);
    }

    public function getData()
    {
        return array(
            'Standard' => array(
                'csv_mode' => null,
                'query_template' => '
        SELECT 10 * FLOOR(value/10) crt, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_crt
        WHERE patient_id IN (1, 2, 3)
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY FLOOR(value/10)
        ORDER BY 1'
            ),
            'Basic CSV' => array(
                'csv_mode' => 'BASIC',
                'query_template' => '
        SELECT 10 * FLOOR(value/10) crt, COUNT(*) frequency
        FROM v_patient_crt
        WHERE patient_id IN (1, 2, 3)
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY FLOOR(value/10)
        ORDER BY 1'
            ),
            'Advanced CSV' => array(
                'csv_mode' => 'ADVANCED',
                'query_template' => '
        SELECT p.nhs_num, crt.value, crt.side, crt.event_date, null
        FROM v_patient_crt crt
        JOIN patient p ON p.id = crt.patient_id
        WHERE patient_id IN (1, 2, 3)
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        ORDER BY 1, 2, 3, 4'
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
        $this->variable->csv_mode = $csv_mode;
        $this->assertEquals($query_template, $this->variable->query());
    }

    public function testGetVariableData()
    {
        $this->assertEquals('crt', $this->variable->field_name);
        $this->assertEquals('CRT', $this->variable->label);
        $this->assertEquals('CRT (microns)', $this->variable->x_label);
        $this->assertNotEmpty($this->variable->id_list);
        $variables = array($this->variable);

        $results = Yii::app()->searchProvider->getVariableData($variables);

        $this->assertCount(1, $results[$this->variable->field_name]);
    }
}
