<?php
/**
 * Class InstitutionParameterTest
 * @covers InstitutionParameter
 * @covers CaseSearchParameter
 * @method institutions($fixtureId)
*/
class InstitutionParameterTest extends OEDbTestCase
{
    protected InstitutionParameter $parameter;

    protected $fixtures = array(
        'institutions' => 'Institution',
    );

    public function setUp(): void
    {
        parent::setUp();
        $this->parameter = new InstitutionParameter();
        $this->parameter->id = 0;
        $this->parameter->operation = '=';
    }

    public static function setUpBeforeClass(): void
    {
        Yii::app()->getModule('OECaseSearch');
        parent::setUpBeforeClass();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->parameter);
    }

    /**
     * @throws Exception
     */
    public function testBindValues(): void
    {
        $this->parameter->value = 1;

        $expected = array(
            ":i_value_0" => 1,
        );

        self::assertEquals($expected, $this->parameter->bindValues());

        // Test a currently supported template string to ensure the substitution is occurring correctly.
        $this->parameter->value = '{institution}';
        Yii::app()->session['selected_institution_id'] = 1;

        self::assertEquals($expected, $this->parameter->bindValues());
    }

    public function testGetCommonItemsForTerm(): void
    {
        self::assertCount(1, InstitutionParameter::getCommonItemsForTerm('The'));
    }

    public function testGetAuditData(): void
    {
        $this->parameter->value = 1;
        $institution = $this->institutions('moorfields');
        $expected = "Institution: = " . $institution->name;

        self::assertEquals($expected, $this->parameter->getAuditData());

        $this->parameter->value = '{institution}';
        $expected = "Institution: = [Current Institution]";

        self::assertEquals($expected, $this->parameter->getAuditData());
    }

    public function testSaveSearch(): void
    {
        $this->parameter->value = 1;

        $actual = $this->parameter->saveSearch();
        self::assertEquals(1, $actual['value']);
    }

    /**
     * @throws CException
     */
    public function testGetValueForAttribute(): void
    {
        $this->parameter->value = 1;
        $institution = $this->institutions('moorfields');
        self::assertEquals($institution->name, $this->parameter->getValueForAttribute('value'));

        $this->parameter->value = '{institution}';
        self::assertEquals('[Current Institution]', $this->parameter->getValueForAttribute('value'));

        $this->expectException('CException');
        $this->parameter->getValueForAttribute('invalid');
    }

    public function testQuery(): void
    {
        foreach (array('=', '!=') as $op) {
            $this->parameter->operation = $op;
            $expected = "SELECT DISTINCT p_0.id
FROM patient p_0
JOIN patient_identifier pi_0 ON pi_0.patient_id = p_0.id
JOIN patient_identifier_type pit_0 ON pit_0.id = pi_0.patient_identifier_type_id
WHERE :i_value_0 IS NULL OR pit_0.institution_id = :i_value_0";
            if ($this->parameter->operation !== '=') {
                $expected = "SELECT p_outer.id FROM patient p_outer WHERE p_outer.id NOT IN (
                {$expected}
            )";
            }
            self::assertEquals($expected, $this->parameter->query());
        }
    }
}
