<?php
/**
* Class VAVariableTest
*/
class VAVariableTest extends CDbTestCase
{
    protected $variable;
    protected $searchProviders;
    protected $invalidProvider;

    public function setUp()
    {
        parent::setUp();
        $this->searchProviders = array();
        $this->variable = new VAVariable([1, 2, 3]);
        $this->searchProviders[] = new DBProvider('provider0')
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->variable, $this->searchProviders);
    }

    /**
     * @covers DBProvider::search()
     * @covers DBProvider::executeSearch()
     */
    public function testGetVariableData()
    {
        // TODO: Use fixtures to populate the relevant database tables with dummy data.
        $variables = array();

        // TODO: Populate the case search parameter attributes here.
        $results = $this->searchProviders[0]->getVariableData($variables);

        $this->markTestIncomplete('TODO');
    }
}
