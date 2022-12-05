<?php

class EDCardTest extends OEDbTestCase
{
    private $imageCard;

    protected $fixtures = array(
        'eyes' => Eye::class,
    );

    public static function setUpBeforeClass(): void
    {
        Yii::import('application.modules.OphTrOperationbooking.components.*');
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->imageCard = new EDCard();
    }

    public function tearDown(): void
    {
        unset($this->imageCard);
        parent::tearDown();
    }

    public function getData()
    {
        return array(
            'Left Eye' => array(
                'doodles' => array(
                    'AntSegSteepAxis',
                    array('axis' => 50, 'flatK' => 20, 'steepK' => 50)
                ),
                'eye' => 'eyeLeft',
            ),
            'Right Eye' => array(
                'doodles' => array(
                    'AntSegSteepAxis',
                    array('axis' => 50, 'flatK' => 20, 'steepK' => 50)
                ),
                'eye' => 'eyeRight',
            ),
            'Both eyes' => array(
                'doodles' => array(
                    'AntSegSteepAxis',
                    array('axis' => 50, 'flatK' => 20, 'steepK' => 50)
                ),
                'eye' => 'eyeBoth',
            ),
        );
    }

    /**
     * @param $doodles array List of new doodle configurations.
     * @param $eye string Fixture ID for eye.
     * @dataProvider getData
     * @covers EDCard
     */
    public function testInit($doodles, $eye)
    {
        $this->imageCard->title = 'Axis';
        $this->imageCard->doodles = $doodles;
        $this->imageCard->eye = $this->eyes($eye);
        $this->imageCard->init();

        $this->assertNotNull($this->imageCard->data);
        $this->assertEquals($this->eyes($eye)->shortName, $this->imageCard->data['side']);
        $this->assertEquals($doodles[0], $this->imageCard->data['onReadyCommandArray'][0][1][0]);
        $this->assertEquals($doodles[1], $this->imageCard->data['onReadyCommandArray'][0][1][1]);
    }
}
