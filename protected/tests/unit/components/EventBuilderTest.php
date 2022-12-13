<?php

class EventBuilderTest extends OEDbTestCase
{
    private $event_type;
    private $elements;

    protected $fixtures = array(
        'event_types' => EventType::class,
        'element_types' => ElementType::class,
    );

    public function setUp(): void
    {
        parent::setUp();
        $this->event_type = $this->event_types('event_type3');
        $this->elements = array(
            $this->element_types('ophhistory')->getInstance(),
        );
    }

    public function tearDown(): void
    {
        $this->event_type = null;
        $this->elements = null;
        parent::tearDown();
    }

    public function testForEventType()
    {
        $builder = Yii::app()->eventBuilder->forEventType($this->event_type);
        $this->assertEquals($this->event_type, $builder->getEventType());
    }

    public function testForElements()
    {
        $builder = Yii::app()->eventBuilder->forElements($this->elements);
        $this->assertEquals($this->elements, $builder->getElements());
    }

    public function testApplyData()
    {
        $data = array();
        $elements = Yii::app()->eventBuilder->forEventType($this->event_type)
            ->forElements($this->elements)
            ->applyData($data)
            ->getElements();
        $this->assertNotEmpty($elements);
    }

    public function testGetElements()
    {
        $elements = Yii::app()->eventBuilder->forEventType($this->event_type)
            ->forElements($this->elements)
            ->getElements();
        $this->assertCount(count($this->elements), $elements);
    }
}
