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
        $this->controller = new \CController('phpunit');
        \Yii::app()->setController($this->controller);
        parent::setUp();
    }

    public function tearDown()
    {
        \Yii::app()->setComponent('moduleAPI', $this->_orig_moduleAPI);
        \Yii::app()->setController(null);
    }

    public function testRender_misconfigured()
    {
        $user =  $this->getMock('OEWebUser', array('checkAccess'));
        $test = new DashboardHelper(array('restricted' => 1), $user);
        $this->setExpectedException('Exception', "Invalid dashboard configuration, api definition required");

        $test->render();
    }

    public function testRender_correct()
    {
        // two different module APIs for dashboard generation
        $first = $this->getMockBuilder('BaseAPI')->disableOriginalConstructor()->setMethods(array('renderDashboard'))->getMock();
        $second = $this->getMockBuilder('BaseAPI')->disableOriginalConstructor()->setMethods(array('renderDashboard'))->getMock();

        // static render values for these mock apis
        $first->expects($this->any())
            ->method('renderDashboard')
            ->will($this->returnValue('first render'));
        $second->expects($this->any())
            ->method('renderDashboard')
            ->will($this->returnValue('second render'));

        // return the appropriate module api mocks
        $this->moduleAPI->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(
                array(
                    array('firstModule', $first),
                    array('secondModule', $second)
                )
            ));

        $user =  $this->getMock('OEWebUser', array('checkAccess'));
        // alternate restriction to test impact
        $user->expects($this->at(0))
            ->method('checkAccess')
            ->with('onlysecond')
            ->will($this->returnValue(false));

        $user->expects($this->at(1))
            ->method('checkAccess')
            ->with('onlysecond')
            ->will($this->returnValue(true));


        $test = new DashboardHelper(array(
            array(
                'api' => 'firstModule',
            ),
            array(
                'restricted' => array('onlysecond'),
                'api' => 'secondModule'
            )
        ), $user);

        $this->assertEquals('first render', $test->render());
        $this->assertEquals('first rendersecond render', $test->render());
    }
}
