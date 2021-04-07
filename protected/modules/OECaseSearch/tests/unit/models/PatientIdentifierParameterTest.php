<?php

class PatientIdentifierParameterTest extends CDbTestCase
{
    protected $fixtures = array(
        'institution' => Institution::class,
        'site' => Site::class,
        'patient_identifier_type' => PatientIdentifierType::class,
        'patient' => Patient::class,
        'patient_identifier' => PatientIdentifier::class,
    );

    public PatientIdentifierParameter $parameter;
    private bool $ignoreTests = false;

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
        $this->parameter->type = (int)(PatientIdentifierType::model()->findByPk(1))->id;
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->parameter);
    }

    public function testBindValues()
    {
        $this->parameter->value = 12345;

        $expected = array(
            "p_id_number_0" => 12345,
            "p_type_0" => 1,
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

        $this->assertEquals('Identifier Type', $labels['type']);
    }

    public function testGetAuditData()
    {
        $this->parameter->value = 12345;
        $expected = "patient_identifier: = 12345 (ID)";

        $this->assertEquals($expected, $this->parameter->getAuditData());
    }

    public function testSaveSearch()
    {
        $this->parameter->value = 12345;

        $actual = $this->parameter->saveSearch();
        $this->assertEquals(1, $actual['type']);
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
            $this->parameter->value = 12345;
            $this->assertEquals('Identifier Type - ID', $this->parameter->getValueForAttribute('type'));
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
            $all_types = Yii::app()->db->createCommand(
                'SELECT id, short_title FROM patient_identifier_type'
            )->queryAll();
            $types = array();
            foreach ($all_types as $type) {
                $types[$type['id']] = $type['short_title'];
            }
            $this->assertEquals($types, $this->parameter->getAllTypes());
        }
    }

    public function testQuery()
    {
        $this->parameter->value = 12345;
        $this->parameter->operation = '=';

        $this->assertTrue($this->parameter->validate());
        $expected = "SELECT DISTINCT p.patient_id 
FROM patient_identifier p
WHERE (:p_type_0 IS NULL OR p.patient_identifier_type_id = :p_type_0)
  AND p.value = :p_id_number_0";
        $this->assertEquals($expected, $this->parameter->query());
    }
}
