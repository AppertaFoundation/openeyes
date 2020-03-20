<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure;
use OEModule\OphCiExamination\models\OphCiExamination_Instrument;
use OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value;

/**
* Class IOPVariableTest
*/
class IOPVariableTest extends CDbTestCase
{
    protected $variable;
    protected $searchProviders;
    protected $invalidProvider;

    protected $fixtures = array(
        'iop' => Element_OphCiExamination_IntraocularPressure::class,
        'iop_value' => OphCiExamination_IntraocularPressure_Value::class,
        'iop_instrument' => OphCiExamination_Instrument::class,
        'events' => Event::class,
        'episodes' => Episode::class,
        'patients' => Patient::class,
    );

    public static function setUpBeforeClass()
    {
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->searchProviders = array();
        $this->variable = new IOPVariable([1, 2, 3]);
        $this->searchProviders[] = new DBProvider('provider0');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->variable, $this->searchProviders);
    }

    public function testGetVariableData()
    {
        $this->assertEquals('iop', $this->variable->field_name);
        $this->assertEquals('IOP', $this->variable->label);
        $this->assertEquals('mm Hg', $this->variable->unit);
        $this->assertNotEmpty($this->variable->id_list);
        $variables = array($this->variable);

        $results = $this->searchProviders[0]->getVariableData($variables);

        $this->assertCount(1, $results[$this->variable->field_name]);
    }
}
