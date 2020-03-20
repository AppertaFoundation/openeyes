<?php

/**
 * Class PatientNameParameterTest
 * @method Patient patient($fixtureId)
 * @method Contact contact($fixtureId)
 */
class PatientNameParameterTest extends CDbTestCase
{
    protected $parameter;
    protected $searchProvider;
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
        $this->searchProvider = new DBProvider('mysql');
        $this->parameter->id = 0;
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->parameter, $this->searchProvider);
    }

    public function testSearch()
    {
        $expected = array($this->patient('patient1'));

        $this->parameter->operation = 'LIKE';
        $this->parameter->value = 'Jim';

        $secondParam = new PatientNameParameter();
        $secondParam->operation = 'LIKE';
        $secondParam->value = 'Jim';

        $results = $this->searchProvider->search(array($this->parameter, $secondParam));

        $ids = array();

        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $actual = Patient::model()->findAllByPk($ids);

        $this->assertEquals($expected, $actual);

        $this->parameter->value = 'Aylward';

        $results = $this->searchProvider->search(array($this->parameter));

        $ids = array();

        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $actual = Patient::model()->findAllByPk($ids);
        $this->assertEquals($expected, $actual);
    }
}
