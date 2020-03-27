<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue;

/**
* Class VAVariableTest
*/
class VAVariableTest extends CDbTestCase
{
    protected $variable;

    protected $fixtures = array(
        'va' => Element_OphCiExamination_VisualAcuity::class,
        'va_reading' => OphCiExamination_VisualAcuity_Reading::class,
        'va_unit_value' => OphCiExamination_VisualAcuityUnitValue::class,
        'events' => Event::class,
        'episodes' => Episode::class,
        'patients' => Patient::class,
        'event_types' => EventType::class,
        'firms' => Firm::class,
        'ssa' => ServiceSubspecialtyAssignment::class,
        'subspecialties' => Subspecialty::class,
        'contacts' => Contact::class,
    );

    public static function setUpBeforeClass()
    {
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->variable = new VAVariable([1, 2, 3]);
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
                'query_template' => 'SELECT 0.3 * FLOOR(LogMAR_value / 0.3) va, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
            FROM v_patient_va_converted
            WHERE patient_id IN (1, 2, 3)
            AND (:start_date IS NULL OR reading_date > :start_date)
            AND (:end_date IS NULL OR reading_date < :end_date)
            AND logMAR_value REGEXP \'[0-9]+\.?[0-9]*\'
            GROUP BY FLOOR(LogMAR_value / 0.3)
            ORDER BY 1'
            ),
            'Basic CSV' => array(
                'csv_mode' => 'BASIC',
                'query_template' => "SELECT 0.3 * FLOOR(LogMAR_value / 0.3) va, COUNT(*) frequency
            FROM v_patient_va_converted
            WHERE patient_id IN (1, 2, 3)
            AND (:start_date IS NULL OR reading_date > :start_date)
            AND (:end_date IS NULL OR reading_date < :end_date)
            AND logMAR_value REGEXP '[0-9]+(\.[0-9]*)?'
            GROUP BY FLOOR(LogMAR_value / 0.3)
            ORDER BY 1"
            ),
            'Advanced CSV' => array(
                'csv_mode' => 'ADVANCED',
                'query_template' => "SELECT p.nhs_num, LogMAR_value va, side, va.reading_date, null
            FROM v_patient_va_converted va
            JOIN patient p ON p.id = va.patient_id
            WHERE patient_id IN (1, 2, 3)
            AND (:start_date IS NULL OR reading_date > :start_date)
            AND (:end_date IS NULL OR reading_date < :end_date)
            ORDER BY 1, 2, 3, 4"
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
        $this->assertEquals($expected, $this->variable->query());
    }

    public function testGetVariableData()
    {
        $this->assertEquals('va', $this->variable->field_name);
        $this->assertEquals('VA', $this->variable->label);
        $this->assertEquals('logMAR', $this->variable->unit);
        $this->assertNotEmpty($this->variable->id_list);
        $variables = array($this->variable);

        $results = Yii::app()->searchProvider->getVariableData($variables);

        $this->assertCount(0, $results[$this->variable->field_name]);
    }
}
