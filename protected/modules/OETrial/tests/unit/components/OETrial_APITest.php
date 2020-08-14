<?php


class OETrial_APITest extends CTestCase
{
    protected $api_instance;

    public static function setUpBeforeClass() : void
    {
        parent::setUpBeforeClass();
        Yii::app()->getModule('OETrial');
    }

    public function setUp() : void
    {
        parent::setUp();
        $this->api_instance = new OETrial_API();
    }

    public function tearDown() : void
    {
        unset($this->api_instance);
        parent::tearDown();
    }

    public function getTestData()
    {
        return array(
            'Existing view file' => array(
                'type' => 'trial',
                'partial' => '_form',
                'expected' => Yii::getPathOfAlias('application.modules.OETrial.views') . '/trial/_form.php'
            ),
            'Existing directory, non existent file' => array(
                'type' => 'trial',
                'partial' => 'not_real_view',
                'expected' => false,
            ),
            'Non-existant directory' => array(
                'type' => 'not_real',
                'partial' => 'empty',
                'expected' => false
            )
        );
    }

    /**
     * @covers OETrial_API
     * @dataProvider getTestData
     * @param $type
     * @param $partial
     * @param $expected
     */
    public function testFindViewFile($type, $partial, $expected)
    {
        $this->assertEquals($expected, $this->api_instance->findViewFile($type, $partial));
    }
}
