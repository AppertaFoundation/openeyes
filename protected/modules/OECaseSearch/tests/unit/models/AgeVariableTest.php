<?php
/**
* Class VAVariableTest
*/
class AgeVariableTest extends CDbTestCase
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
        $this->variable = new AgeVariable([1, 2, 3]);
        $this->searchProviders[] = new DBProvider('provider0');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->variable, $this->searchProviders);
    }

    public function testGetVariableData()
    {
        $variables = array($this->variable);

        $this->assertEquals('age', $this->variable->field_name);
        $this->assertEquals('Age', $this->variable->label);
        $this->assertEquals('y', $this->variable->unit);
        $this->assertNotEmpty($this->variable->id_list);

        $results = $this->searchProviders[0]->getVariableData($variables);

        $this->assertCount(3, $results[$this->variable->field_name]);
    }
}
