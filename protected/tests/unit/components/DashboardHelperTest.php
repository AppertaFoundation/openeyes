<?php

class DashboardHelperTest extends PHPUnit_Framework_TestCase
{
    protected $_orig_moduleAPI;
    protected $moduleAPI;
    protected $controller;

    public function setUp()
    {
        $this->_orig_moduleAPI = \Yii::app()->moduleAPI;

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
    }

    public function testRender_misconfigured()
    {
        $user =  $this->getMock('OEWebUser', array('checkAccess'));
        $test = new DashboardHelper(array('restricted' => 1), $user);
        $this->setExpectedException('Exception', "Invalid dashboard configuration: module, static or object definition required");

        $test->render();
    }

    public function testRender_correct()
    {
        $first_db = array(
            'title' => 'first',
            'content' => 'first render');
        $second_db = array(
            'title' => 'second',
            'content' => 'second render');

        // two different module APIs for dashboard generation
        $first = $this->getMockBuilder('BaseAPI')->disableOriginalConstructor()->setMethods(array('renderDashboard'))->getMock();
        $second = $this->getMockBuilder('BaseAPI')->disableOriginalConstructor()->setMethods(array('renderDashboard'))->getMock();

        // static render values for these mock apis
        $first->expects($this->any())
            ->method('renderDashboard')
            ->will($this->returnValue($first_db));
        $second->expects($this->any())
            ->method('renderDashboard')
            ->will($this->returnValue($second_db));

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
                'module' => 'firstModule',
            ),
            array(
                'restricted' => array('onlysecond'),
                'module' => 'secondModule'
            )
        ), $user);

        $this->controller->expects($this->at(0))
            ->method('renderPartial')
            ->with($this->anything(), array('items' => array(
                $first_db), 'sortable' => false), true, false)
            ->will($this->returnValue('first render'));

        $this->controller->expects($this->at(1))
            ->method('renderPartial')
            ->with($this->anything(), array('items' => array(
                $first_db, $second_db
            ), 'sortable' => true), true, false)
            ->will($this->returnValue('first rendersecond render'));

        $this->assertEquals('first render', $test->render());
        $test->sortable = true;
        $this->assertEquals('first rendersecond render', $test->render());
    }

    public function testRender_Class()
    {
        $test = new DashboardHelper(array(
            array(
                'class' => 'TestDashboardClass',
                'method' => ''
            )
        ));
    }
}
