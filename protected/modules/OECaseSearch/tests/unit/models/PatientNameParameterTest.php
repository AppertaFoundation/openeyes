<?php

/**
 * Class PatientNameParameterTest
 * @method Patient patient($fixtureId)
 * @method Contact contact($fixtureId)
 */
class PatientNameParameterTest extends CDbTestCase
{
    protected $parameter;
    protected $fixtures = array(
        'patient' => 'Patient',
        'contact' => 'Contact',
    );

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->parameter = new PatientNameParameter();
        $this->parameter->id = 0;
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->parameter);
    }

    /**
     * @covers PatientNameParameter
     */
    public function testSearch()
    {
        $expected = array($this->patient('patient1'));

        $this->parameter->operation = '=';
        $this->parameter->value = 1;

        $secondParam = new PatientNameParameter();
        $secondParam->id = 1;
        $secondParam->operation = '=';
        $secondParam->value = $this->patient('patient1')->id;

        $this->assertTrue($secondParam->validate());

        $results = Yii::app()->searchProvider->search(array($this->parameter, $secondParam));

        $ids = array();

        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $actual = Patient::model()->findAllByPk($ids);

        $this->assertEquals($expected, $actual);

        $this->parameter->value = $this->patient('patient1')->id;

        $this->assertTrue($this->parameter->validate());

        $results = Yii::app()->searchProvider->search(array($this->parameter));

        $ids = array();

        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $actual = Patient::model()->findAllByPk($ids);
        $this->assertEquals($expected, $actual);
    }

    public function testGetAuditData()
    {
        $this->parameter->operation = '=';
        $this->parameter->value = 1;

        $expected = "patient_name: = \"{$this->patient('patient1')->getFullName()}\"";

        $this->assertEquals($expected, $this->parameter->getAuditData());
    }

    public function testGetCommonItemsForTerm()
    {
        $this->assertCount(1, PatientNameParameter::getCommonItemsForTerm('Jim'));

        $this->assertCount(1, PatientNameParameter::getCommonItemsForTerm('jim'));

        $this->assertCount(1, PatientNameParameter::getCommonItemsForTerm('Aylward'));

        $this->assertCount(1, PatientNameParameter::getCommonItemsForTerm('Jim Aylward'));
    }

    /**
     * @throws CException
     */
    public function testGetValueForAttribute()
    {
        $this->parameter->operation = '=';
        $this->parameter->value = 1;

        $this->assertEquals('=', $this->parameter->getValueForAttribute('operation'));
        $this->assertEquals($this->patient('patient1')->getFullName(), $this->parameter->getValueForAttribute('value'));

        $this->expectException('CException');
        $this->parameter->getValueForAttribute('invalid');
    }
}
