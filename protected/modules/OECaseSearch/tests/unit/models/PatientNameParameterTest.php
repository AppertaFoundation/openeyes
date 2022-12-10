<?php

/**
 * Class PatientNameParameterTest
 * @covers PatientNameParameter
 * @covers CaseSearchParameter
 * @method Patient patient($fixtureId)
 * @method Contact contact($fixtureId)
 */
class PatientNameParameterTest extends OEDbTestCase
{
    protected PatientNameParameter $parameter;
    protected $fixtures = array(
        'patient' => 'Patient',
        'contact' => 'Contact',
    );

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->parameter = new PatientNameParameter();
        $this->parameter->id = 0;
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->parameter);
    }

    /**
     * @covers PatientNameParameter
     */
    public function testSearch(): void
    {
        $expected = array($this->patient('patient1'));

        $this->parameter->operation = '=';
        $this->parameter->value = 1;

        $secondParam = new PatientNameParameter();
        $secondParam->id = 1;
        $secondParam->operation = '=';
        $secondParam->value = $this->patient('patient1')->id;

        self::assertTrue($secondParam->validate());

        $results = Yii::app()->searchProvider->search(array($this->parameter, $secondParam));

        $ids = array();

        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $actual = Patient::model()->findAllByPk($ids);

        self::assertEquals($expected, $actual);

        $this->parameter->value = $this->patient('patient1')->id;

        self::assertTrue($this->parameter->validate());

        $results = Yii::app()->searchProvider->search(array($this->parameter));

        $ids = array();

        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $actual = Patient::model()->findAllByPk($ids);
        self::assertEquals($expected, $actual);
    }

    public function testGetAuditData(): void
    {
        $this->parameter->operation = '=';
        $this->parameter->value = 1;

        $expected = "patient_name: = \"{$this->patient('patient1')->getFullName()}\"";

        self::assertEquals($expected, $this->parameter->getAuditData());
    }

    public function testGetCommonItemsForTerm(): void
    {
        self::assertCount(1, PatientNameParameter::getCommonItemsForTerm('Jim'));

        self::assertCount(1, PatientNameParameter::getCommonItemsForTerm('jim'));

        self::assertCount(1, PatientNameParameter::getCommonItemsForTerm('Aylward'));

        self::assertCount(1, PatientNameParameter::getCommonItemsForTerm('Jim Aylward'));
    }

    /**
     * @throws CException
     */
    public function testGetValueForAttribute(): void
    {
        $this->parameter->operation = '=';
        $this->parameter->value = 1;

        self::assertEquals('=', $this->parameter->getValueForAttribute('operation'));
        self::assertEquals($this->patient('patient1')->getFullName(), $this->parameter->getValueForAttribute('value'));

        $this->parameter->value = -1;
        self::assertEquals('Unknown', $this->parameter->getValueForAttribute('value'));

        $this->expectException('CException');
        $this->parameter->getValueForAttribute('invalid');
    }
}
