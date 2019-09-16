<?php

class WBCardTest extends CDbTestCase
{
    protected $widget;

    public static function setupBeforeClass()
    {
        Yii::import('application.modules.OphTrOperationbooking.components.*');
    }

    public function getScenarios()
    {
        return array(
            'List data' => array(
                'title' => 'Patient',
                'data' => array(
                    'Test Patient',
                    '21-12-1991',
                    '123456',
                ),
                'colour' => null,
                'type' => 'List',
                'editable' => false,
            ),
            'Editable List data' => array(
                'title' => 'Patient',
                'data' => array(
                    'Test Patient',
                    '21-12-1991',
                    '123456',
                ),
                'colour' => null,
                'type' => 'List',
                'editable' => true,
            ),
            'Single data' => array(
                'title' => 'Anaesthesia',
                'data' => 'Local',
                'colour' => null,
                'type' => 'Single',
                'editable' => false,
            ),
            'Single data with extra data and colour' => array(
                'title' => 'Procedure',
                'data' => array(
                    'content' => 'Left',
                    'extra_data' => 'Repositioning of Intraocular lens',
                ),
                'colour' => 'green',
                'type' => 'Single',
                'editable' => false,
            ),
            'Double data' => array(
                'title' => 'Biometry',
                'data' => array(
                    array(
                        'content' => '26.07',
                        'small_data' => 'mm',
                        'extra_data' => 'Axial Length',
                    ),
                    array(
                        'content' => '3.66',
                        'small_data' => 'mm',
                        'extra_data' => 'AxCD',
                    ),
                ),
                'colour' => null,
                'type' => 'Double',
                'editable' => false,
            ),
            'Empty card' => array(
                'title' => 'Predicted Outcome',
                'data' => null,
                'colour' => null,
                'type' => 'Empty',
                'editable' => false,
            ),
        );
    }

    protected function setUp()
    {
        parent::setUp();
        $this->widget = new WBCard();
    }

    protected function tearDown()
    {
        unset($this->widget);
        parent::tearDown();
    }

    /**
     * @covers WBCard::init
     * @param $title string
     * @param $data string|array
     * @param $colour string
     * @param $type string
     * @param $editable bool
     * @dataProvider getScenarios
     */
    public function testInit($title, $data, $colour, $type, $editable)
    {
        $this->widget->title = $title;
        $this->widget->data = $data;
        $this->widget->colour = $colour;
        $this->widget->editable = $editable;
        $this->widget->init();
        $this->assertEquals($type, $this->widget->getType());
    }

    /**
     * @covers WBCard::run
     * @param $title string
     * @param $data string|array
     * @param $colour string
     * @param $type string
     * @param $editable bool
     * @depends testInit
     * @dataProvider getScenarios
     */
    public function testRun($title, $data, $colour, $type, $editable = false)
    {
        $this->widget->title = $title;
        $this->widget->data = $data;
        $this->widget->colour = $colour;
        $this->widget->editable = $editable;
        $this->widget->init();
        $this->assertEquals($type, $this->widget->getType());
        ob_start();
        $this->widget->run();
        $output = ob_get_clean();
        $this->assertNotNull($output);
    }

    /**
     * @covers WBCard::getType
     * @param $title string
     * @param $data string|array
     * @param $colour string
     * @param $type string
     * @param $editable bool
     * @depends testInit
     * @dataProvider getScenarios
     */
    public function testGetType($title, $data, $colour, $type, $editable)
    {
        $this->widget->title = $title;
        $this->widget->data = $data;
        $this->widget->colour = $colour;
        $this->widget->editable = $editable;
        $this->widget->init();
        $this->assertEquals($type, $this->widget->getType());
    }
}
