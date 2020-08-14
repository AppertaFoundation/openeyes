<?php

/**
 * Class PatientNumberParameterTest
 * @method Patient patient($fixtureId)
 */
class PatientNumberParameterTest extends CDbTestCase
{
    protected $parameter;
    protected $fixtures = array(
        'patient' => 'Patient'
    );

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->parameter = new PatientNumberParameter();
        $this->parameter->id = 0;
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->parameter);
    }

    public function testSearch()
    {
        $expected = array($this->patient('patient1'));

        $this->parameter->operation = '=';
        $this->parameter->value = $this->patient('patient1')->id;

        $this->assertTrue($this->parameter->validate());

        $secondParam = new PatientNumberParameter();
        $secondParam->id = 1;
        $secondParam->operation = '=';
        $secondParam->value = $this->patient('patient1')->id;

        $this->assertTrue($secondParam->validate());

        $results = Yii::app()->searchProvider->search(array($this->parameter, $secondParam));

        $this->assertCount(1, $results);
        $actual = Patient::model()->findAllByPk($results[0]);

        $this->assertEquals($expected, $actual);
    }

    public function testGetAuditData()
    {
        $this->parameter->operation = '=';
        $this->parameter->value = 1;

        $expected = "patient_number: = {$this->patient('patient1')->hos_num}";

        $this->assertEquals($expected, $this->parameter->getAuditData());
    }

    public function testGetCommonItemsForTerm()
    {
        $this->assertCount(1, PatientNumberParameter::getCommonItemsForTerm(1));

        $this->assertCount(1, PatientNumberParameter::getCommonItemsForTerm(12345));
    }

    /**
     * @throws CException
     */
    public function testGetValueForAttribute()
    {
        $this->parameter->operation = '=';
        $this->parameter->value = 1;

        $this->assertEquals('=', $this->parameter->getValueForAttribute('operation'));
        $this->assertEquals($this->patient('patient1')->hos_num, $this->parameter->getValueForAttribute('value'));

        $this->expectException('CException');
        $this->parameter->getValueForAttribute('invalid');
    }
}
