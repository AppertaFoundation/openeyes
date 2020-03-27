<?php

/**
 * Class PreviousTrialParameterTest
 */
class PreviousTrialParameterTest extends CDbTestCase
{
    /**
     * @var PreviousTrialParameter $object
     */
    protected $object;

    public static function setUpBeforeClass()
    {
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->object = new PreviousTrialParameter();
        $this->object->id = 0;
    }

    public function tearDown()
    {
        unset($this->object); // start from scratch for each test.
        parent::tearDown();
    }

    /**
     *
     * @throws CHttpException
     */
    public function testQueryOperation()
    {
        $validOps = array(
            'IN',
            'NOT IN',
        );

        foreach ($validOps as $op) {
            try {
                $this->object->operation = $op;
                $this->object->query();
            } catch (Exception $e) {
                $this->fail('Failed on valid query operation ' . $op . ': ' . $e);
            }
            $this->assertTrue(true);
        }

        // Ensure that a HTTP exception is raised if an invalid operation is specified.
        $this->expectException(CHttpException::class);
        $this->object->operation = 'no';
        $this->object->query();
    }
}
