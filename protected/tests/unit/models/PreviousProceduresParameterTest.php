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
        $this->searchProviders[] = new DBProvider('provider0')
        $this->parameter->id = 0;
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->parameter, $this->searchProviders);
    }

    /**
     * @covers DBProvider::search()
     * @covers DBProvider::executeSearch()
     */
    public function testSearch()
    {
        // TODO: Use fixtures to populate the relevant database tables with dummy data.
        $parameters = array();

        // TODO: Populate the case search parameter attributes here.
        $results = $this->searchProviders[0]->search($parameters);

        $this->markTestIncomplete('TODO');
    }
}
