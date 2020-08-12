<?php


class DBProviderTest extends CDbTestCase
{
    public $searchProvider;

    protected $fixtures = array(
        'patients' => Patient::class,
        'procedures' => Procedure::class,
    );

    public static function setUpBeforeClass()
    {
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        $this->searchProvider = new DBProvider();
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->searchProvider);
    }

    /**
     * @covers DBProvider
     * @throws CException
     * @throws Exception
     */
    public function testGetVariableData()
    {
        $parameter = new PatientDeceasedParameter();
        $parameter->operation = false;

        $results =  $this->searchProvider->search(array($parameter));

        $variable = new AgeVariable(array_column($results, 'id'));
        $var_data = $this->searchProvider->getVariableData(array($variable));

        $this->assertCount(1, $var_data);
    }

    /**
     * @covers DBProvider
     */
    public function testGetDriver()
    {
        $this->assertEquals('mariadb', $this->searchProvider->driver);
    }

    /**
     * @covers DBProvider
     */
    public function testSetDriver()
    {
        $this->searchProvider->driver = 'mysql';
        $this->assertEquals('mysql', $this->searchProvider->driver);
    }

    /**
     * @covers SearchProvider
     * @covers DBProvider
     * @throws Exception
     */
    public function testSearch()
    {
        $parameter = new PatientDeceasedParameter();
        $parameter->operation = false;

        $results =  $this->searchProvider->search(array($parameter));

        $this->assertCount(8, $results);

        $parameter = new PreviousProceduresParameter();
        $parameter->operation = '=';
        $parameter->value = 1;

        $results = $this->searchProvider->search(array($parameter));

        $this->assertCount(0, $results);
    }
}
