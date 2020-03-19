<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT;

/**
* Class CCTVariableTest
*/
class CCTVariableTest extends CDbTestCase
{
    protected $variable;
    protected $searchProviders;
    protected $invalidProvider;

    protected $fixtures = array(
        'cct' => Element_OphCiExamination_AnteriorSegment_CCT::class,
        'events' => Event::class,
        'episodes' => Episode::class,
        'patients' => Patient::class,
        'event_types' => EventType::class,
        'firms' => Firm::class,
        'ssa' => ServiceSubspecialtyAssignment::class,
        'subspecialties' => Subspecialty::class,
        'contacts' => Contact::class,
    );

    public static function setUpBeforeClass()
    {
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->searchProviders = array();
        $this->variable = new CCTVariable([1, 2, 3]);
        $this->searchProviders[] = new DBProvider('provider0');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->variable, $this->searchProviders);
    }

    /**
     * @covers DBProvider::search()
     * @covers DBProvider::executeSearch()
     */
    public function testGetVariableData()
    {
        $variables = array($this->variable);

        $results = $this->searchProviders[0]->getVariableData($variables);

        $this->assertCount(1, $results[$this->variable->field_name]);
    }
}
