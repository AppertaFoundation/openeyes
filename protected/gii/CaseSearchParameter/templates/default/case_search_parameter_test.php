<?php
echo '<?php'; ?>

/**
* Class <?php echo $this->className; ?>ParameterTest
*/
class <?php echo $this->className; ?>ParameterTest extends OEDbTestCase
{
    protected $parameter;
    protected $searchProviders;
    protected $invalidProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->parameter = new <?php echo $this->className; ?>Parameter();
        $this->parameter->id = 0;
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->parameter);
    }

    public function testSearch()
    {
        // TODO: Use fixtures to populate the relevant database tables with dummy data.
        $parameters = array();

        // TODO: Populate the case search parameter attributes here.
        $results = Yii::app()->searchProvider->search($parameters);

        $this->markTestIncomplete('TODO');
    }
}
