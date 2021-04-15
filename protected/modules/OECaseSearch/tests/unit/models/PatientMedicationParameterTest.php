<?php

/**
 * Class PatientMedicationParameterTest
 * @covers PatientMedicationParameter
 * @covers CaseSearchParameter
 * @method medications($fixtureId)
 */
class PatientMedicationParameterTest extends CDbTestCase
{
    /**
     * @var $object PatientMedicationParameter
     */
    protected PatientMedicationParameter $object;

    protected $fixtures = array(
        'patients' => Patient::class,
        'medications' => Medication::class,
        'medication_institutions' => Medication_Institution::class,
    );

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        Yii::app()->session['selected_institution_id'] = 1;
        Yii::app()->getModule('OECaseSearch');
    }

    public static function tearDownAfterClass()
    {
        parent::setUpBeforeClass();
        unset(Yii::app()->session['selected_institution_id']);
    }

    public function setUp()
    {
        parent::setUp();
        $this->object = new PatientMedicationParameter();
        $this->object->id = 0;
    }

    public function tearDown()
    {
        unset($this->object);
        parent::tearDown();
    }

    public function getOperations(): array
    {
        return array(
            'Equal' => array(
                'operator' => '=',
            ),
            'Not equal' => array(
                'operator' => '!=',
            ),
        );
    }

    /**
     * @dataProvider getOperations
     * @param $operator
     */
    public function testQuery($operator): void
    {
        $this->object->value = 5;
        $this->object->operation = $operator;

        self::assertTrue($this->object->validate());

        $sqlValue = "
SELECT p.id
FROM patient p
LEFT JOIN patient_medication_assignment m
ON m.patient_id = p.id
LEFT JOIN medication md
ON md.id = m.medication_drug_id
WHERE md.id != :p_m_value_0
OR m.id IS NULL";

        if ($operator === '=') {
            $sqlValue = "
SELECT p.id
FROM patient p
JOIN patient_medication_assignment m
ON m.patient_id = p.id
LEFT JOIN medication d
ON d.id = m.medication_drug_id
WHERE d.id = :p_m_value_0";
        }

        self::assertEquals(
            trim(preg_replace('/\s+/', ' ', $sqlValue)),
            trim(preg_replace('/\s+/', ' ', $this->object->query()))
        );
    }

    public function testBindValues(): void
    {
        $this->object->value = 5;
        $expected = array(
            'p_m_value_0' => $this->object->value,
        );

        // Ensure that all bind values are returned.
        self::assertEquals($expected, $this->object->bindValues());
    }

    /**
     * @dataProvider getOperations
     * @param $operator
     * @throws CException
     */
    public function testGetValueForAttribute($operator): void
    {
        $this->object->value = 5;
        $this->object->operation = $operator;

        $expected = $this->medications('drug5')->preferred_term;

        self::assertEquals($operator, $this->object->getValueForAttribute('operation'));
        self::assertEquals($expected, $this->object->getValueForAttribute('value'));

        $this->expectException('CException');
        $this->object->getValueForAttribute('invalid');
    }

    public function testGetCommonItemsForTerm(): void
    {
        $expected = 2;
        self::assertCount(1, PatientMedicationParameter::getCommonItemsForTerm('Acetazolamide'));
        self::assertEquals($expected, PatientMedicationParameter::getCommonItemsForTerm('Acetazolamide')[0]['id']);

        self::assertCount(1, PatientMedicationParameter::getCommonItemsForTerm('Acetazolamide 250mg modified release capsules'));
        self::assertEquals($expected, PatientMedicationParameter::getCommonItemsForTerm('Acetazolamide 250mg modified release capsules')[0]['id']);
    }

    /**
     * @dataProvider getOperations
     * @param $operator
     */
    public function testGetAuditData($operator): void
    {
        $this->object->operation = $operator;
        $this->object->value = 2;

        $expected = "medication: $operator \"2\"";

        self::assertEquals($expected, $this->object->getAuditData());
    }
}
