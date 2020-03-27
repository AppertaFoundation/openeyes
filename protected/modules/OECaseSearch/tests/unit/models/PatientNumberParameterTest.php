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

        $secondParam = new PatientNumberParameter();
        $secondParam->operation = '=';
        $secondParam->value = $this->patient('patient1')->id;

        $results = Yii::app()->searchProvider->search(array($this->parameter, $secondParam));

        $this->assertCount(1, $results);
        $actual = Patient::model()->findAllByPk($results[0]);

        $this->assertEquals($expected, $actual);
    }
}
