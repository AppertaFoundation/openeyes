<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure;
use OEModule\OphCiExamination\models\OphCiExamination_Instrument;
use OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value;

/**
* Class IOPVariableTest
*/
class IOPVariableTest extends CDbTestCase
{
    protected $variable;

    protected $fixtures = array(
        'iop' => Element_OphCiExamination_IntraocularPressure::class,
        'iop_value' => OphCiExamination_IntraocularPressure_Value::class,
        'iop_instrument' => OphCiExamination_Instrument::class,
        'events' => Event::class,
        'episodes' => Episode::class,
        'patients' => Patient::class,
    );

    public static function setUpBeforeClass()
    {
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->variable = new IOPVariable([1, 2, 3]);
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
                'var_mode' => 'first',
                'query_template' => '
        SELECT 10 * FLOOR(value/10) iop, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_iop iop
        WHERE iop.patient_id IN (1, 2, 3)
        AND iop.event_date = ({{date}})
        AND iop.reading_time = ({{time}})
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY 10 * FLOOR(value/10)
        ORDER BY 1'
            ),
            'Basic CSV' => array(
                'csv_mode' => 'BASIC',
                'var_mode' => 'first',
                'query_template' => '
        SELECT 10 * FLOOR(value/10) iop, COUNT(*) frequency
        FROM v_patient_iop iop
        WHERE iop.patient_id IN (1, 2, 3)
        AND iop.event_date = ({{date}})
        AND iop.reading_time = ({{time}})
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY 10 * FLOOR(value/10)
        ORDER BY 1'
            ),
            'Advanced CSV' => array(
                'csv_mode' => 'ADVANCED',
                'var_mode' => 'last',
                'query_template' => '
        SELECT p.nhs_num, iop.value iop, iop.side, iop.event_date, iop.reading_time
        FROM v_patient_iop iop
        JOIN patient p ON p.id = iop.patient_id
        WHERE iop.patient_id IN (1, 2, 3)
        AND iop.event_date = ({{date}})
        AND iop.reading_time = ({{time}})
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        ORDER BY 1, 2, 3, 4, 5'
            ),
        );
    }

    /**
     * @dataProvider getData
     * @param $csv_mode
     * @param $var_mode
     * @param $query_template
     */
    public function testQuery($csv_mode, $var_mode, $query_template)
    {
        $this->variable->query_flags = array($var_mode);
        $date_predicate = null;
        $time_predicate = null;
        if ($var_mode === 'first') {
            $date_predicate = 'SELECT MIN(event_date) FROM v_patient_iop iop2 WHERE iop2.patient_id = iop.patient_id AND iop2.eye = iop.eye';
            $time_predicate = 'SELECT MIN(reading_time) FROM v_patient_iop iop2 WHERE iop2.patient_id = iop.patient_id AND iop2.eye = iop.eye AND iop2.event_date = iop.event_date';
        } elseif ($var_mode === 'last') {
            $date_predicate = 'SELECT MAX(event_date) FROM v_patient_iop iop2 WHERE iop2.patient_id = iop.patient_id AND iop2.eye = iop.eye';
            $time_predicate = 'SELECT MAX(reading_time) FROM v_patient_iop iop2 WHERE iop2.patient_id = iop.patient_id AND iop2.eye = iop.eye AND iop2.event_date = iop.event_date';
        }
        $expected = str_replace('{{date}}', $date_predicate, str_replace('{{time}}', $time_predicate, $query_template));
        $this->variable->csv_mode = $csv_mode;
        $this->assertEquals($expected, $this->variable->query());
    }

    public function testGetVariableData()
    {
        $this->variable->query_flags = array('first');
        $this->assertEquals('iop', $this->variable->field_name);
        $this->assertEquals('IOP', $this->variable->label);
        $this->assertEquals('IOP (mm Hg)', $this->variable->x_label);
        $this->assertNotEmpty($this->variable->id_list);
        $variables = array($this->variable);

        $results = Yii::app()->searchProvider->getVariableData($variables);

        $this->assertCount(1, $results[$this->variable->field_name]);
        $this->assertEquals('20', $results[$this->variable->field_name][0]['iop']);
        $this->assertEquals('1', $results[$this->variable->field_name][0]['frequency']);
        $this->assertEquals('1', $results[$this->variable->field_name][0]['patient_id_list']);
    }
}
