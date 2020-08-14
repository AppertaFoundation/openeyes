<?php
echo '<?php'; ?>

/**
* Class <?php echo $this->className; ?>VariableTest
*/
class <?php echo $this->className; ?>VariableTest extends CDbTestCase
{
    protected $variable;

    public function setUp()
    {
        parent::setUp();
        $this->variable = new <?php echo $this->className; ?>Variable([1, 2, 3]);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->variable);
    }

    public function testGetVariableData()
    {
        // TODO: Use fixtures to populate the relevant database tables with dummy data.
        $variables = array($this->variable);

        // TODO: Populate the case search variable attributes here.
        $results = Yii::app()->searchProvider->getVariableData($variables);

        $this->assertCount(3, $results[$this->variable->field_name]);

        $this->markTestIncomplete('TODO');
    }
}
