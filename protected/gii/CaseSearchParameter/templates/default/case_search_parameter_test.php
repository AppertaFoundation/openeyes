<?php
$counter = 0;
echo '<?php'; ?>

/**
* Class <?php echo $this->className; ?>ParameterTest
*/
class <?php echo $this->className; ?>ParameterTest extends CDbTestCase
{
    protected $parameter;
    protected $searchProviders;
    protected $invalidProvider;

    protected function setUp()
    {
        parent::setUp();
        $this->searchProviders = array();
        $this->parameter = new <?php echo $this->className; ?>Parameter();
<?php foreach (explode(',', $this->searchProviders) as $provider):?>
        $this->searchProviders[] = new <?php echo $provider; ?>('provider<?php echo $counter++;?>')
<?php endforeach;?>
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
