<?php

class DashboardHelperTest extends PHPUnit_Framework_TestCase
{
    protected $_orig_moduleAPI;
    protected $moduleAPI;
    protected $controller;

    public function setUp()
    {
        $this->_orig_moduleAPI = Yii::app()->moduleAPI;

        $this->moduleAPI = $this->getMockBuilder('ModuleAPI')->disableOriginalConstructor()->getMock();
        \Yii::app()->setComponent('moduleAPI', $this->moduleAPI);
        $this->controller = $this->getMockBuilder('CController')->disableOriginalConstructor()->getMock();
        //new \CController('phpunit');
        \Yii::app()->setController($this->controller);
        parent::setUp();
    }

    public function tearDown()
    {
        \Yii::app()->setComponent('moduleAPI', $this->_orig_moduleAPI);
        \Yii::app()->setController(null);
        parent::tearDown();
    }

    /**
     * @covers DashboardHelper
     */
    public function testRender_misconfigured()
    {
        $user = $this->createMock('OEWebUser', array('checkAccess'));
        $test = new DashboardHelper(array('restricted' => 1), $user);
        $this->expectException('Exception', 'Invalid dashboard configuration: module, static or object definition required');

        $test->render();
    }

    /**
     * @covers DashboardHelper
     */
    public function testRender_Class()
    {
        $test = new DashboardHelper(array(
            array(
                'class' => 'TestDashboardClass',
                'method' => '',
            ),
        ));

        $this->assertNotNull($test);
    }

    /**
     * @covers DashboardHelper
     */
    public function test_getItemPosition_with_no_ordered_items()
    {
        $items = array(
            array('title' => 'test1', 'content' => 'test1'),
            array('title' => 'test2', 'content' => 'test2'),
        );

        $helper = new DashboardHelper($items);

        $this->assertEquals(1, $helper->getItemPosition($items[0]));
        $this->assertEquals(2, $helper->getItemPosition($items[1]));
    }

    /**
     * @covers DashboardHelper
     */
    public function test_getItemPosition_with_an_ordered_items()
    {
        $items = array(
            array('title' => 'test1', 'content' => 'test1'),
            array('title' => 'test2', 'content' => 'test2', 'position' => 3),
            array('title' => 'test3', 'content' => 'test3'),
        );

        $helper = new DashboardHelper($items);

        $this->assertEquals(4, $helper->getItemPosition($items[0]));
        $this->assertEquals(3, $helper->getItemPosition($items[1]));
        $this->assertEquals(5, $helper->getItemPosition($items[2]));
    }
}
