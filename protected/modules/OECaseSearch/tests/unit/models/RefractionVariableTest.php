<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction;
use OEModule\OphCiExamination\models\OphCiExamination_Refraction_Reading;

/**
 * Class RefractionVariableTest
 * @covers RefractionVariable
 * @covers CaseSearchVariable
*/
class RefractionVariableTest extends CDbTestCase
{
    protected RefractionVariable $variable;

    protected $fixtures = array(
        'refraction' => Element_OphCiExamination_Refraction::class,
        'refraction_readings' => OphCiExamination_Refraction_Reading::class,
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
        $this->variable = new RefractionVariable([1, 2, 3]);
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
                'query_template' => '
        SELECT FLOOR(value) refraction, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_refraction
        WHERE patient_id IN (1, 2, 3)
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY FLOOR(value)
        ORDER BY 1'
            ),
            'Basic CSV' => array(
                'csv_mode' => 'BASIC',
                'query_template' => '
        SELECT FLOOR(value) refraction, COUNT(*) frequency
        FROM v_patient_refraction
        WHERE patient_id IN (1, 2, 3)
        AND (:start_date IS NULL OR event_date > :start_date)
        AND (:end_date IS NULL OR event_date < :end_date)
        GROUP BY FLOOR(value)
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
            ) nhs_num, r.value, r.side, r.event_date, null
        FROM v_patient_refraction r
        JOIN patient p ON p.id = r.patient_id
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
    public function testQuery($csv_mode, $query_template): void
    {
        $this->variable->csv_mode = $csv_mode;
        self::assertEquals($query_template, $this->variable->query());
    }

    public function testGetVariableData(): void
    {
        self::assertEquals('refraction', $this->variable->field_name);
        self::assertEquals('Refraction', $this->variable->label);
        self::assertEquals('Refraction (mean sph)', $this->variable->x_label);
        self::assertNotEmpty($this->variable->id_list);
        $variables = array($this->variable);
        $query = 'SELECT FLOOR(value) refraction, COUNT(*) frequency, GROUP_CONCAT(DISTINCT patient_id) patient_id_list
        FROM v_patient_refraction
        WHERE patient_id IN (1, 2, 3)
        GROUP BY FLOOR(value)
        ORDER BY 1';
        $expected = Yii::app()->db->createCommand("SELECT COUNT(*) FROM ({$query}) t")->queryScalar();

        $results = Yii::app()->searchProvider->getVariableData($variables);

        self::assertCount($expected, $results[$this->variable->field_name]);
    }
}
