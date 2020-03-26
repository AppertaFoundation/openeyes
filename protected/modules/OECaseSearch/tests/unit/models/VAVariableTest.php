<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue;

/**
* Class VAVariableTest
*/
class VAVariableTest extends CDbTestCase
{
    protected $variable;
    protected $searchProviders;
    protected $invalidProvider;

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
        $this->searchProviders = array();
        $this->variable = new VAVariable([1, 2, 3]);
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
                'query_template' => 'SELECT snellen_value va, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
            FROM v_patient_va_converted
            WHERE patient_id IN (1, 2, 3)
            AND (:start_date IS NULL OR reading_date > :start_date)
            AND (:end_date IS NULL OR reading_date < :end_date)
            GROUP BY snellen_value
            ORDER BY snellen_value'
            ),
            'Basic CSV' => array(
                'csv_mode' => 'BASIC',
                'query_template' => "SELECT snellen_value va, COUNT(*) frequency
            FROM v_patient_va_converted
            WHERE patient_id IN (1, 2, 3)
            AND (:start_date IS NULL OR reading_date > :start_date)
            AND (:end_date IS NULL OR reading_date < :end_date)
            GROUP BY snellen_value
            ORDER BY snellen_value"
            ),
            'Advanced CSV' => array(
                'csv_mode' => 'ADVANCED',
                'query_template' => "SELECT p.nhs_num, snellen_value va, side, va.reading_date, null
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
        $this->assertEquals($expected, $this->variable->query($this->searchProviders[0]));
    }

    public function testGetVariableData()
    {
        $this->assertEquals('va', $this->variable->field_name);
        $this->assertEquals('VA', $this->variable->label);
        $this->assertNull($this->variable->unit);
        $this->assertNotEmpty($this->variable->id_list);
        $variables = array($this->variable);

        $results = $this->searchProviders[0]->getVariableData($variables);

        $this->assertCount(1, $results[$this->variable->field_name]);
        $this->assertEquals('6/9', $results[$this->variable->field_name][0]['va']);
        $this->assertEquals(2, $results[$this->variable->field_name][0]['frequency']);
        $this->assertEquals(1, $results[$this->variable->field_name][0]['patient_id_list']);
    }
}
