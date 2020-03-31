<?php

class PatientAllergyParameterTest extends CDbTestCase
{
    public $parameter;

    protected $fixtures = array(
        'patients' => Patient::class,
        'allergy' => ':allergy'
    );

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        Yii::app()->getModule('OECaseSearch');
    }

    public function getArgs()
    {
        return array(
            'Equal' => array(
                'operation' => '=',
            ),
            'Not equal' => array(
                'operation' => '!=',
            ),
        );
    }

    public function setUp()
    {
        parent::setUp();
        $this->parameter = new PatientAllergyParameter();
        $this->parameter->id = 0;
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->parameter);
    }

    /**
     * @dataProvider getArgs
     * @param $operation
     */
    public function testQuery($operation)
    {
        $this->parameter->operation = $operation;
        $this->parameter->value = 1;

        $this->assertTrue($this->parameter->validate());

        $query = "SELECT DISTINCT p.id 
FROM patient p 
LEFT JOIN patient_allergy_assignment paa
  ON paa.patient_id = p.id
LEFT JOIN allergy a
  ON a.id = paa.allergy_id
WHERE a.id = :p_al_textValue_0";
        if ($operation !== '=') {
            $query = "SELECT DISTINCT p1.id
FROM patient p1
WHERE p1.id NOT IN (
{$query}
)";
        }
        $this->assertEquals($query, $this->parameter->query());
    }

    public function testGetCommonItemsForTerm()
    {
        $this->assertCount(3, PatientAllergyParameter::getCommonItemsForTerm('allergy'));
        $this->assertCount(1, PatientAllergyParameter::getCommonItemsForTerm('allergy 1'));
    }

    /**
     * @throws CException
     */
    public function testGetValueForAttribute()
    {
        $this->parameter->operation = '=';
        $this->parameter->value = 1;
        $expected = Allergy::model()->findByPk(1);

        $this->assertEquals('=', $this->parameter->getValueForAttribute('operation'));
        $this->assertEquals($expected->name, $this->parameter->getValueForAttribute('value'));

        $this->expectException('CException');
        $this->parameter->getValueForAttribute('invalid');
    }

    /**
     * @dataProvider getArgs
     * @param $operation
     */
    public function testGetAuditData($operation)
    {
        $this->parameter->operation =  $operation;
        $this->parameter->value = 1;
        $this->assertEquals("allergy: {$this->parameter->operation} \"allergy 1\"", $this->parameter->getAuditData());
    }

    public function testBindValues()
    {
        $this->parameter->value = 1;
        $expected = array(
            "p_al_textValue_0" => 1,
        );

        $this->assertEquals($expected, $this->parameter->bindValues());
    }
}
