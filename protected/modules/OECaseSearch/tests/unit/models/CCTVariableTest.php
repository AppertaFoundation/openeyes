<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT;

/**
* Class CCTVariableTest
*/
class CCTVariableTest extends CDbTestCase
{
    protected $variable;
    protected $searchProviders;
    protected $invalidProvider;

    protected $fixtures = array(
        'cct' => Element_OphCiExamination_AnteriorSegment_CCT::class,
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
        $this->variable = new CCTVariable([1, 2, 3]);
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
                'query_template' => "
        SELECT value cct, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_cct
        WHERE patient_id IN (1, 2, 3)
        AND eye = '{{eye}}'
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY value"
            ),
            'Basic CSV' => array(
                'csv_mode' => 'BASIC',
                'query_template' => "
        SELECT value cct
        FROM v_patient_cct cct
        WHERE patient_id = p_outer.id
          AND eye = '{{eye}}'
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)"
            ),
            'Advanced CSV' => array(
                'csv_mode' => 'ADVANCED',
                'query_template' => "
        SELECT value cct
        FROM v_patient_cct cct
        WHERE patient_id = p_outer.id
          AND eye = '{{eye}}'
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)"
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
        $this->assertEquals('cct', $this->variable->field_name);
        $this->assertEquals('CCT', $this->variable->label);
        $this->assertEquals('microns', $this->variable->unit);
        $this->assertNotEmpty($this->variable->id_list);
        $variables = array($this->variable);

        $results = $this->searchProviders[0]->getVariableData($variables);

        $this->assertCount(2, $results[$this->variable->field_name]);
        $this->assertCount(1, $results[$this->variable->field_name][0]);
        $this->assertCount(0, $results[$this->variable->field_name][1]);
    }
}
