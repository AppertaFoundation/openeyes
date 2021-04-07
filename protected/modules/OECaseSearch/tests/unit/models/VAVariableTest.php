<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue;

/**
* Class VAVariableTest
*/

class VAVariableTest extends CDbTestCase
{
    protected VAVariable $variable;

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
                'query_template' => 'SELECT
                0.3 * FLOOR(va1.LogMAR_value / 0.3) va,
                COUNT(*) frequency,
                GROUP_CONCAT(DISTINCT va1.patient_id) patient_id_list
            FROM v_patient_va_converted va1
            WHERE va1.patient_id IN (1, 2, 3)
            AND (:start_date IS NULL OR va1.reading_date > :start_date)
            AND (:end_date IS NULL OR va1.reading_date < :end_date)
            AND va1.logMAR_value REGEXP \'[0-9]+\.?[0-9]*\'
            AND va1.LogMAR_value = (
                SELECT MAX(va2.LogMAR_value)
                FROM v_patient_va_converted va2
                WHERE va2.patient_id = va1.patient_id
                AND va2.eye = va1.eye
            )
            GROUP BY 0.3 * FLOOR(LogMAR_value / 0.3)
            ORDER BY 1'
            ),
            'Basic CSV' => array(
                'csv_mode' => 'BASIC',
                'query_template' => "SELECT 0.3 * FLOOR(va1.LogMAR_value / 0.3) va, COUNT(*) frequency
            FROM v_patient_va_converted va1
            WHERE va1.patient_id IN (1, 2, 3)
            AND va1.LogMAR_value = (
                SELECT MAX(va2.LogMAR_value)
                FROM v_patient_va_converted va2
                WHERE va2.patient_id = va1.patient_id
                AND va2.eye = va1.eye
            )
            AND (:start_date IS NULL OR va1.reading_date > :start_date)
            AND (:end_date IS NULL OR va1.reading_date < :end_date)
            AND va1.logMAR_value REGEXP '[0-9]+(\.[0-9]*)?'
            GROUP BY 0.3 * FLOOR(LogMAR_value / 0.3)
            ORDER BY 1"
            ),
            'Advanced CSV' => array(
                'csv_mode' => 'ADVANCED',
                'query_template' => "SELECT (
            SELECT pi.value
            FROM patient_identifier pi
                JOIN patient_identifier_type pit ON pit.id = pi.patient_identifier_type_id
            WHERE pi.patient_id = p.id
            AND pit.usage_type = 'GLOBAL'
            ) nhs_num,
            va1.LogMAR_value va,
            va1.side, DATE_FORMAT(MAX(va1.reading_date), '%d-%m-%Y'),
            DATE_FORMAT(MAX(va1.reading_date), '%H:%i:%s')
            FROM v_patient_va_converted va1
            JOIN patient p ON p.id = va1.patient_id
            WHERE va1.patient_id IN (1, 2, 3)
            AND va1.LogMAR_value = (
                SELECT MAX(va2.LogMAR_value)
                FROM v_patient_va_converted va2
                WHERE va2.patient_id = va1.patient_id
                AND va2.eye = va1.eye
            )
            AND (:start_date IS NULL OR va1.reading_date > :start_date)
            AND (:end_date IS NULL OR va1.reading_date < :end_date)
            GROUP BY p.nhs_num, va1.LogMAR_value, va1.side
            ORDER BY p.nhs_num, va1.LogMAR_value, va1.side"
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
        $this->assertEquals('VA (best)', $this->variable->label);
        $this->assertEquals('VA (LogMAR)', $this->variable->x_label);
        $this->assertNotEmpty($this->variable->id_list);
        $variables = array($this->variable);

        $results = Yii::app()->searchProvider->getVariableData($variables);

        $this->assertCount(0, $results[$this->variable->field_name]);
    }
}
