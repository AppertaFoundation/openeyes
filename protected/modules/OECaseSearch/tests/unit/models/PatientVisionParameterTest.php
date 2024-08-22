<?php


use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue;

/**
 * Class PatientVisionParameterTest
 * @covers PatientVisionParameter
 * @covers CaseSearchParameter
 */
class PatientVisionParameterTest extends OEDbTestCase
{
    public PatientVisionParameter $parameter;
    protected $fixtures = array(
        'patients' => 'Patient',
        'va_readings' => OphCiExamination_VisualAcuity_Reading::class,
        'va' => Element_OphCiExamination_VisualAcuity::class,
        'va_unit_values' => OphCiExamination_VisualAcuityUnitValue::class,
    );

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->parameter = new PatientVisionParameter();
        $this->parameter->id = 0;
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->parameter);
    }

    public function testSaveSearch(): void
    {
        $this->parameter->operation = 'IN';
        $this->parameter->value = 101;
        $this->parameter->bothEyesIndicator = true;

        $expected = serialize(array(
            'class_name' => 'PatientVisionParameter',
            'name' => 'vision',
            'operation' => 'IN',
            'id' => 0,
            'value' => 101,
            'bothEyesIndicator' => true
        ));

        self::assertEquals($expected, serialize($this->parameter->saveSearch()));
    }

    /**
     * @dataProvider getOperators
     * @param $op
     * @param bool $both_eyes
     */
    public function testGetAuditData($op, $both_eyes = false): void
    {
        if ($op === '=') {
            $this->parameter->operation = 'IN';
        } else {
            $this->parameter->operation = 'NOT IN';
        }
        $this->parameter->value = 10;
        $expected = "vision: {$this->parameter->operation} {$this->parameter->value}";
        if ($both_eyes) {
            $expected .= ' searching both eyes';
            $this->parameter->bothEyesIndicator = true;
        }

        self::assertEquals($expected, $this->parameter->getAuditData());
    }

    public function getOperators(): array
    {
        return array(
            'INCLUDES single-eye' => array(
                'op' => '=',
            ),
            'DOES NOT INCLUDE single-eye' => array(
                'op' => '!=',
            ),
            'INCLUDES both-eye' => array(
                'op' => '=',
                'both_eyes' => true,
            ),
            'DOES NOT INCLUDE both-eye' => array(
                'op' => '!=',
                'both_eyes' => true,
            ),
        );
    }

    /**
     * @dataProvider getOperators
     * @param $op
     * @param bool $both_eyes
     */
    public function testQuery($op, $both_eyes = false): void
    {
        $second_operation = 'OR';
        if ($both_eyes) {
            $second_operation = 'AND';
            $this->parameter->bothEyesIndicator = true;
        }

        $this->parameter->operation = $op;

        $this->parameter->value = 1;

        self::assertTrue($this->parameter->validate());

        $expected = "SELECT DISTINCT t5.patient_id
FROM (
       SELECT DISTINCT patient_id, MAX(left_va_value) AS left_va_value, MAX(right_va_value) AS right_va_value
       FROM (
              SELECT patient_id                      AS patient_id,
                     IF(va_side = 0, va_value, NULL) AS left_va_value,
                     IF(va_side = 1, va_value, NULL) AS right_va_value
              FROM (
                     SELECT t1.patient_id, t1.va_value, t1.va_side
                     FROM (SELECT patient.id             AS patient_id,
                                  ovr.value              AS va_value,
                                  ovr.side               AS va_side,
                                  ovr.last_modified_date AS date
                           FROM patient
                                  LEFT JOIN episode e ON patient.id = e.patient_id
                                  LEFT JOIN event ON event.episode_id = e.id
                                  LEFT JOIN et_ophciexamination_visualacuity eov ON event.id = eov.event_id
                                  LEFT JOIN ophciexamination_visualacuity_reading ovr ON eov.id = ovr.element_id
                           WHERE ovr.value IS NOT NULL
                             AND ovr.side IS NOT NULL
                             AND ovr.last_modified_date IS NOT NULL) t1
                     WHERE t1.date = (SELECT MAX(t2.date)
                                      FROM (SELECT patient.id             AS patient_id,
                                                   ovr.side               AS va_side,
                                                   ovr.last_modified_date AS date
                                            FROM patient
                                                   LEFT JOIN episode e ON patient.id = e.patient_id
                                                   LEFT JOIN event ON event.episode_id = e.id
                                                   LEFT JOIN et_ophciexamination_visualacuity eov
                                                       ON event.id = eov.event_id
                                                   LEFT JOIN ophciexamination_visualacuity_reading ovr
                                                       ON eov.id = ovr.element_id
                                            WHERE ovr.value IS NOT NULL
                                              AND ovr.side IS NOT NULL
                                              AND ovr.last_modified_date IS NOT NULL) t2
                                      WHERE t2.patient_id = t1.patient_id
                                        AND t1.va_side = t2.va_side)
                   ) t3) t4
       GROUP BY patient_id) t5
WHERE (t5.left_va_value {$op} :p_v_value_0)
{$second_operation} (t5.right_va_value {$op} :p_v_value_0)";

        // Using str_replace here because the line endings are different
        // between the text block above and the block used within the parameter.
        self::assertEquals(str_replace("\r\n", "\n", $expected), $this->parameter->query());
    }

    /**
     * @throws CException
     */
    public function testGetValueForAttribute(): void
    {
        $this->parameter->operation = 'IN';
        $this->parameter->value = 101;
        $this->parameter->bothEyesIndicator = true;

        self::assertEquals('IN', $this->parameter->getValueForAttribute('operation'));
        self::assertEquals('6/9', $this->parameter->getValueForAttribute('value'));
        self::assertEquals('Both eyes', $this->parameter->getValueForAttribute('bothEyesIndicator'));

        $this->expectException('CException');

        $this->parameter->getValueForAttribute('invalid');
    }

    public function testBindValues(): void
    {
        $this->parameter->value = 10;
        $expected = array(
            "p_v_value_0" => (int)$this->parameter->value
        );

        self::assertEquals($expected, $this->parameter->bindValues());
    }
}
