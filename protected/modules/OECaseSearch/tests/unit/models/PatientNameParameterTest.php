<?php

/**
 * Class PatientNameParameterTest
 */
class PatientNameParameterTest extends CDbTestCase
{
    protected $parameter;
    protected $searchProvider;
    protected $fixtures = array(
        'patient' => 'Patient',
        'contact' => 'Contact',
    );

    protected function setUp()
    {
        parent::setUp();
        $this->parameter = new PatientNameParameter();
        $this->searchProvider = new DBProvider('mysql');
        $this->parameter->id = 0;
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->parameter, $this->searchProvider);
    }

    /**
     * @covers DBProvider::search()
     * @covers DBProvider::executeSearch()
     * @covers PatientNameParameter::query()
     * @covers PatientNameParameter::bindValues()
     */
    public function testSearch()
    {
        $expected = array($this->patient('patient1'));

        $this->parameter->operation = 'LIKE';
        $this->parameter->patient_name = 'Jim';

        $secondParam = new PatientNameParameter();
        $secondParam->operation = 'LIKE';
        $secondParam->patient_name = 'Jim';

        $results = $this->searchProvider->search(array($this->parameter, $secondParam));

        $ids = array();

        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $actual = Patient::model()->findAllByPk($ids);

        $this->assertEquals($expected, $actual);

        $this->parameter->patient_name = 'Aylward';

        $results = $this->searchProvider->search(array($this->parameter));

        $ids = array();

        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $actual = Patient::model()->findAllByPk($ids);
        $this->assertEquals($expected, $actual);
    }
}
