<?php

/**
 * Class PatientDeceasedParameterTest
 * @covers PatientDeceasedParameter
 * @covers CaseSearchParameter
 * @method Patient patient($fixtureId)
 */
class PatientDeceasedParameterTest extends CDbTestCase
{
    protected PatientDeceasedParameter $parameter;
    protected $fixtures = array(
        'patient' => 'Patient',
    );

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->parameter = new PatientDeceasedParameter();
        $this->parameter->id = 0;
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->parameter);
    }

    /**
     * @throws CHttpException
     */
    public function testQuery(): void
    {
        $correctOps = array(
            '1',
            '0',
        );
        $invalidOps = array(
            'LIKE',
            'NOT LIKE',
        );

        // Ensure the query is correct for each operator.
        foreach ($correctOps as $operator) {
            $this->parameter->operation = $operator;
            $sqlValue = ($operator === '0') ? 'SELECT id FROM patient WHERE NOT(is_deceased)' : 'SELECT id FROM patient WHERE is_deceased';
            self::assertEquals(
                trim(preg_replace('/\s+/', ' ', $sqlValue)),
                trim(preg_replace('/\s+/', ' ', $this->parameter->query()))
            );
        }

        // Ensure that a HTTP exception is raised if an invalid operation is specified.
        $this->expectException(CHttpException::class);
        foreach ($invalidOps as $operator) {
            $this->parameter->operation = $operator;
            $this->parameter->query();
        }
    }

    public function testBindValues(): void
    {
        $this->parameter->operation = '1';
        $expected = array();

        // Ensure that all bind values are returned.
        self::assertEquals($expected, $this->parameter->bindValues());
    }

    public function testSearch(): void
    {
        // Ensure only the patient with the is_deceased as 1 fixture is returned.
        $match = array();
        for ($i = 9; $i < 11; $i++) {
            $match[] = $this->patient("patient$i");
        }

        $this->parameter->operation = '1';

        self::assertTrue($this->parameter->validate());

        $results = Yii::app()->searchProvider->search(array($this->parameter));

        $ids = array();
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $patients = Patient::model()->findAllByPk($ids);

        self::assertEquals($match, $patients);

        // Ensure all patient fixtures except patient9 are returned.
        $this->parameter->operation = '0';
        self::assertTrue($this->parameter->validate());
        $match = array();
        for ($i = 1; $i < 9; $i++) {
            $match[] = $this->patient("patient$i");
        }

        $results = Yii::app()->searchProvider->search(array($this->parameter));

        $ids = array();
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $patients = Patient::model()->findAllByPk($ids);

        self::assertEquals($match, $patients);
    }

    public function testGetAuditData(): void
    {
        $this->parameter->operation = true;
        $expected = "patient_deceased: True";
        self::assertEquals($expected, $this->parameter->getAuditData());

        $this->parameter->operation = false;
        $expected = "patient_deceased: False";
        self::assertEquals($expected, $this->parameter->getAuditData());
    }
}
