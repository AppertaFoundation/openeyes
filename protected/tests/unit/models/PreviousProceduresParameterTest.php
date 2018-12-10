<?php
/**
* Class PreviousProceduresParameterTest
*/
class PreviousProceduresParameterTest extends CDbTestCase
{
    protected $parameter;
    protected $searchProviders;
    protected $invalidProvider;

    protected function setUp()
    {
        parent::setUp();
        $this->searchProviders = array();
        $this->parameter = new PreviousProceduresParameter();
        $this->searchProviders[] = new DBProvider('provider0');
        $this->parameter->id = 0;
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->parameter, $this->searchProviders);
    }

}
