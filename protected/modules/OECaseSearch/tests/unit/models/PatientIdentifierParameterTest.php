<?php

/**
 * Class PatientIdentifierParameterTest
 * @covers PatientIdentifierParameter
 * @covers CaseSearchParameter
 */
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

    /**
     * @throws Exception
     */
    public function testBindValues(): void
    {
        $this->parameter->value = 12345;

        $expected = array(
            "p_id_number_0" => 12345,
            "p_type_0" => 1,
        );

        self::assertEquals($expected, $this->parameter->bindValues());

        // Test a currently supported template string to ensure the substitution is occurring correctly.
        $this->parameter->type = '{institution}';
        Yii::app()->session['selected_institution_id'] = 1;

        $expected = array(
            "p_id_number_0" => 12345,
            "p_type_0" => Institution::model()->getCurrent()->id,
        );

        self::assertEquals($expected, $this->parameter->bindValues());
    }

    public function testGetCommonItemsForTerm(): void
    {
        self::assertCount(1, PatientIdentifierParameter::getCommonItemsForTerm(123));
    }

    public function testAttributeLabels(): void
    {
        $labels = $this->parameter->attributeLabels();

        self::assertEquals('Identifier Type', $labels['type']);
    }

    public function testGetAuditData(): void
    {
        $this->parameter->value = 12345;
        $expected = "patient_identifier: = 12345 (ID)";

        self::assertEquals($expected, $this->parameter->getAuditData());

        $this->parameter->type = '{institution}';
        $expected = "patient_identifier: = 12345 ([ID for Current Institution])";

        self::assertEquals($expected, $this->parameter->getAuditData());
    }

    public function testSaveSearch(): void
    {
        $this->parameter->value = 12345;

        $actual = $this->parameter->saveSearch();
        self::assertEquals(1, $actual['type']);
    }

    /**
     * @throws CException
     */
    public function testGetValueForAttribute(): void
    {
        if (!$this->ignoreTests && !isset(Yii::app()->params['patient_identifiers'])) {
            $this->ignoreTests = true;
            self::markTestSkipped('Patient identifiers not configured.');
        } else {
            $this->parameter->value = 12345;
            self::assertEquals('Identifier Type - ID', $this->parameter->getValueForAttribute('type'));
            self::assertEquals(12345, $this->parameter->getValueForAttribute('value'));

            $this->parameter->type = '{institution}';
            self::assertEquals('Identifier Type - [ID for Current Institution]', $this->parameter->getValueForAttribute('type'));
            self::assertEquals(12345, $this->parameter->getValueForAttribute('value'));

            $this->parameter->type = null;
            self::assertEquals('All identifier types', $this->parameter->getValueForAttribute('type'));
            self::assertEquals(12345, $this->parameter->getValueForAttribute('value'));

            $this->expectException('CException');
            $this->parameter->getValueForAttribute('invalid');
        }
    }

    public function testGetAllCodes(): void
    {
        if (!$this->ignoreTests && !isset(Yii::app()->params['patient_identifiers'])) {
            $this->ignoreTests = true;
            self::markTestSkipped('Patient identifiers not configured.');
        } else {
            $all_types = Yii::app()->db->createCommand('SELECT id, short_title FROM patient_identifier_type')
                ->queryAll();
            $types = array();
            foreach ($all_types as $type) {
                $types[$type['id']] = $type['short_title'];
            }
            self::assertEquals($types, $this->parameter->getAllTypes());
        }
    }

    public function testQuery(): void
    {
        $this->parameter->value = 12345;
        $this->parameter->operation = '=';

        self::assertTrue($this->parameter->validate());
        $expected = "SELECT DISTINCT p.patient_id 
FROM patient_identifier p
WHERE (:p_type_0 IS NULL OR p.patient_identifier_type_id = :p_type_0)
  AND p.value = :p_id_number_0";
        self::assertEquals($expected, $this->parameter->query());
    }
}
