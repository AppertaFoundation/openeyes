<?php
$counter = 0;
echo '<?php'; ?>

/**
* Class <?php echo $this->className; ?>VariableTest
*/
class <?php echo $this->className; ?>VariableTest extends CDbTestCase
{
    protected $variable;
    protected $searchProviders;
    protected $invalidProvider;

    public function setUp()
    {
        parent::setUp();
        $this->searchProviders = array();
        $this->variable = new <?php echo $this->className; ?>Variable([1, 2, 3]);
<?php foreach (explode(',', $this->searchProviders) as $provider) :?>
        $this->searchProviders[] = new <?php echo $provider; ?>('provider<?php echo $counter++;?>')
<?php endforeach;?>
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
