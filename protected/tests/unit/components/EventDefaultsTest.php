<?php

class EventDefaultsTest extends OEDbTestCase
{
    private $event_type;
    private $context;

    protected $fixtures = array(
        'event_types' => EventType::class
    );

    public function setUp(): void
    {
        parent::setUp();
        $this->event_type = $this->event_types('event_type3');
        $this->context = array(
            'action' => 'create',
        );
    }

    public function tearDown(): void
    {
        $this->event_type = null;
        $this->context = null;
        parent::tearDown();
    }

    public function testForEventType()
    {
        $defaults = Yii::app()->eventDefaults->forEventType($this->event_type);
        $this->assertEquals($this->event_type, $defaults->getEventType());
    }

    public function testWithContext()
    {
        $defaults = Yii::app()->eventDefaults->withContext($this->context);
        $this->assertEquals($this->context, $defaults->getContext());
    }

    public function testGetDefaults()
    {
        $this->markTestIncomplete();
        /*$context = array();
        $defaults = Yii::app()->eventDefaults->forEventType($this->event_type)
            ->withContext($context)
            ->getDefaults();
        $this->assertEmpty($defaults);*/
    }
}
