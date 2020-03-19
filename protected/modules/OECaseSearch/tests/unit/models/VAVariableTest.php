<?php
/**
* Class VAVariableTest
*/
class VAVariableTest extends CDbTestCase
{
    protected $variable;
    protected $searchProviders;
    protected $invalidProvider;

    public static function setUpBeforeClass()
    {
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->searchProviders = array();
        $this->variable = new VAVariable([1, 2, 3]);
        $this->searchProviders[] = new DBProvider('provider0');
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
        $variables = array($this->variable);

        $results = $this->searchProviders[0]->getVariableData($variables);

        $this->assertCount(3, $results[$this->variable->field_name]);
    }
}
