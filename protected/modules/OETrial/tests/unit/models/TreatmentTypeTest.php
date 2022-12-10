<?php

class TreatmentTypeTest extends ActiveRecordTestCase
{
    public TreatmentType $instance;

    public function setUp(): void
    {
        parent::setUp();
        $this->instance = new TreatmentType();
    }

    public function tearDown(): void
    {
        unset($this->instance);
        parent::tearDown();
    }

    public function getModel()
    {
        return TreatmentType::model();
    }

    public function testGetOptions()
    {
        $expected = CHtml::listData(TreatmentType::model()->findAll(), 'id', 'name');

        $this->assertEquals($expected, TreatmentType::getOptions());
    }
}
