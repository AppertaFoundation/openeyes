<?php

class PatientIdentifierParameterTest extends CDbTestCase
{
    protected $fixtures = array(
        'patients' => Patient::class,
        'identifiers' => PatientIdentifier::class,
    );

    public $parameter;
    private $ignoreTests = false;

    public static function setUpBeforeClass()
    {
        /**
         * @var $module OECaseSearchModule
         */
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->parameter = new PatientIdentifierParameter();
        $this->parameter->id = 0;
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->parameter);
    }

    public function testBindValues()
    {
        $this->parameter->code = 'RVEEH_UR';
        $this->parameter->value = 12345;

        $expected = array(
            "p_id_number_0" => 12345,
            "p_code_0" => 'RVEEH_UR',
        );

        $this->assertEquals($expected, $this->parameter->bindValues());
    }

    public function testGetCommonItemsForTerm()
    {
        $this->assertCount(1, PatientIdentifierParameter::getCommonItemsForTerm(123));
    }

    public function testAttributeLabels()
    {
        $labels = $this->parameter->attributeLabels();

        $this->assertEquals('Code', $labels['code']);
    }

    public function testGetAuditData()
    {
        $this->parameter->code = 'RVEEH_UR';
        $this->parameter->value = 12345;
        $expected = "patient_identifier: = RVEEH_UR 12345";

        $this->assertEquals($expected, $this->parameter->getAuditData());
    }

    public function testSaveSearch()
    {
        $this->parameter->code = 'RVEEH UR';
        $this->parameter->value = 12345;

        $actual = $this->parameter->saveSearch();
        $this->assertEquals('RVEEH UR', $actual['code']);
    }

    /**
     * @throws CException
     */
    public function testGetValueForAttribute()
    {
        if (!$this->ignoreTests && !isset(Yii::app()->params['patient_identifiers'])) {
            $this->ignoreTests = true;
            $this->markTestSkipped('Patient identifiers not configured.');
        } else {
            $this->parameter->code = 'RVEEH_UR';
            $this->parameter->value = 12345;
            $this->assertEquals('Code - RVEEH_UR', $this->parameter->getValueForAttribute('code'));
            $this->assertEquals(12345, $this->parameter->getValueForAttribute('value'));

            $this->expectException('CException');
            $this->parameter->getValueForAttribute('invalid');
        }
    }

    /**
     * @throws CException
     */
    public function testGetAllCodes()
    {
        if (!$this->ignoreTests && !isset(Yii::app()->params['patient_identifiers'])) {
            $this->ignoreTests = true;
            $this->markTestSkipped('Patient identifiers not configured.');
        } else {
            $all_codes = Yii::app()->db->createCommand('SELECT DISTINCT code FROM patient_identifier')->queryAll();
            $codes = array();
            foreach ($all_codes as $code) {
                $codes[$code['code']] = $code['code'];
            }
            $this->assertEquals($codes, $this->parameter->getAllCodes());
        }
    }

    public function testQuery()
    {
        $this->parameter->value = 12345;
        $this->parameter->code = 'RVEEH_UR';
        $this->parameter->operation = '=';

        $this->assertTrue($this->parameter->validate());
        $expected = "SELECT DISTINCT p.patient_id 
FROM patient_identifier p
WHERE p.code = :p_code_0 AND p.value = :p_id_number_0";

        $this->assertEquals($expected, $this->parameter->query());
    }
}
