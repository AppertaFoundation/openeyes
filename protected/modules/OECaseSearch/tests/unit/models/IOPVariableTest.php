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
    protected $searchProviders;
    protected $invalidProvider;

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
        $this->searchProviders = array();
        $this->variable = new IOPVariable([1, 2, 3]);
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
                'query_template' => '
        SELECT value iop, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_iop
        WHERE patient_id IN (1, 2, 3)
        AND eye = \'{{eye}}\'
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY value'
            ),
            'Basic CSV' => array(
                'csv_mode' => 'BASIC',
                'query_template' => "
        SELECT iop.value iop
        FROM v_patient_iop iop
        WHERE iop.patient_id = p_outer.id
          AND iop.eye = '{{eye}}'
          AND iop.event_id = (
              SELECT MAX(iop2.event_id)
              FROM v_patient_iop iop2
              WHERE iop2.patient_id = iop.patient_id
                AND iop2.eye = iop.eye
                AND (:start_date IS NULL OR iop2.event_date > :start_date)
                AND (:end_date IS NULL OR iop2.event_date < :end_date)
          )
        AND iop.reading_time = (
            SELECT MAX(iop3.reading_time)
            FROM v_patient_iop iop3
            WHERE iop3.patient_id = iop.patient_id
              AND iop3.eye = iop.eye
              AND iop3.event_id IN (
                SELECT MAX(iop4.event_id)
                FROM v_patient_iop iop4
                WHERE iop4.patient_id = iop3.patient_id
                  AND iop4.eye = iop3.eye
                  AND (:start_date IS NULL OR iop4.event_date > :start_date)
                  AND (:end_date IS NULL OR iop4.event_date < :end_date)
              )
        )
        GROUP BY iop.value"
            ),
            'Advanced CSV' => array(
                'csv_mode' => 'ADVANCED',
                'query_template' => "
        SELECT iop.value iop
        FROM v_patient_iop iop
        WHERE iop.patient_id = p_outer.id
          AND iop.eye = '{{eye}}'
          AND iop.event_id = (
              SELECT MAX(iop2.event_id)
              FROM v_patient_iop iop2
              WHERE iop2.patient_id = iop.patient_id
                AND iop2.eye = iop.eye
                AND (:start_date IS NULL OR iop2.event_date > :start_date)
                AND (:end_date IS NULL OR iop2.event_date < :end_date)
          )
        AND iop.reading_time = (
            SELECT MAX(iop3.reading_time)
            FROM v_patient_iop iop3
            WHERE iop3.patient_id = iop.patient_id
              AND iop3.eye = iop.eye
              AND iop3.event_id IN (
                SELECT MAX(iop4.event_id)
                FROM v_patient_iop iop4
                WHERE iop4.patient_id = iop3.patient_id
                  AND iop4.eye = iop3.eye
                  AND (:start_date IS NULL OR iop4.event_date > :start_date)
                  AND (:end_date IS NULL OR iop4.event_date < :end_date)
              )
        )
        GROUP BY iop.value"
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
        foreach (array('L', 'R') as $eye) {
            $expected = str_replace('{{eye}}', $eye, $query_template);
            $this->variable->eye = $eye;
            $this->variable->csv_mode = $csv_mode;
            $this->assertEquals($expected, $this->variable->query($this->searchProviders[0]));
        }
    }

    public function testGetVariableData()
    {
        $this->assertEquals('iop', $this->variable->field_name);
        $this->assertEquals('IOP', $this->variable->label);
        $this->assertEquals('mm Hg', $this->variable->unit);
        $this->assertNotEmpty($this->variable->id_list);
        $variables = array($this->variable);

        $results = $this->searchProviders[0]->getVariableData($variables);

        $this->assertCount(2, $results[$this->variable->field_name]);
        $this->assertCount(0, $results[$this->variable->field_name][0]);
        $this->assertCount(1, $results[$this->variable->field_name][1]);
    }
}
