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
        unset($this->object);
        parent::tearDown();
    }

    public function getData()
    {
        return array(
            'IN' => array(
                'op' => 'IN',
            ),
            'NOT IN' => array(
                'op' => 'NOT IN'
            ),
            'INVALID' => array(
                'op' => 'no',
                'exception' => 'CHttpException'
            ),
        );
    }

    /**
     * @dataProvider getData
     * @param string $op
     * @param string|null $exception
     * @throws CHttpException
     */
    public function testQueryOperation($op, $exception = null)
    {
        if ($exception) {
            $this->expectException($exception);
        }
        $this->object->operation = $op;
        $this->object->query();

        $this->assertTrue(true);
    }
}
