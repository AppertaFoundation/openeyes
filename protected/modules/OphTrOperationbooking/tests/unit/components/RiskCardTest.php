<?php

class RiskCardTest extends CDbTestCase
{
    protected $fixtures = array(
        'ophtroperationbooking_whiteboard' => OphTrOperationbooking_Whiteboard::class,
        'patient' => Patient::class,
        'event' => Event::class,
        'episode' => Episode::class,
        'disorder' => Disorder::class,
        'secondary_diagnosis' => SecondaryDiagnosis::class,
        'eye' => Eye::class,
    );

    protected $widget;
    public static function setupBeforeClass()
    {
        Yii::import('application.modules.OphTrOperationbooking.components.*');
    }

    protected function setUp()
    {
        parent::setUp();
        $this->widget = new RiskCard();
        $this->widget->whiteboard = $this->ophtroperationbooking_whiteboard('whiteboard1');
        $this->widget->data = $this->ophtroperationbooking_whiteboard('whiteboard1');
    }

    protected function tearDown()
    {
        unset($this->whiteboard, $this->widget);
        parent::tearDown();
    }

    public function testInit()
    {
        $this->widget->init();
        $this->assertEquals('Special', $this->widget->getType());
    }

    public function testRun()
    {
        $this->widget->init();
        ob_start();
        $this->widget->run();
        $content = ob_get_clean();
        $this->assertNotNull($content);
    }
}
