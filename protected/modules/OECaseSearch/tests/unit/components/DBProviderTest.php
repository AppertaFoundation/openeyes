<?php

/**
 * Class DBProviderTest
 * @covers DBProvider
 * @covers SearchProvider
 */
class DBProviderTest extends CDbTestCase
{
    public DBProvider $searchProvider;

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
    public function testGetVariableData(): void
    {
        $parameter = new PatientDeceasedParameter();
        $parameter->operation = false;

        $results =  $this->searchProvider->search(array($parameter));

        $variable = new AgeVariable(array_column($results, 'id'));
        $var_data = $this->searchProvider->getVariableData(array($variable));

        self::assertCount(1, $var_data);
    }

    /**
     * @covers DBProvider
     */
    public function testGetDriver(): void
    {
        self::assertEquals('mariadb', $this->searchProvider->driver);
    }

    /**
     * @covers DBProvider
     */
    public function testSetDriver(): void
    {
        $this->searchProvider->driver = 'mysql';
        self::assertEquals('mysql', $this->searchProvider->driver);
    }

    /**
     * @covers SearchProvider
     * @covers DBProvider
     * @throws Exception
     */
    public function testSearch(): void
    {
        $parameter = new PatientDeceasedParameter();
        $parameter->id = 1;
        $parameter->operation = false;

        $results =  $this->searchProvider->search(array($parameter));

        self::assertCount(8, $results);

        $parameter = new PreviousProceduresParameter();
        $parameter->operation = '=';
        $parameter->value = 1;
        $parameter->id = 1;

        $results = $this->searchProvider->search(array($parameter));

        self::assertCount(0, $results);
    }
}
