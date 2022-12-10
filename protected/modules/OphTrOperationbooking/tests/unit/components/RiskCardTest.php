<?php
use \OEModule\OphCiExamination\models\OphCiExaminationRisk;

/**
 * Class RiskCardTest
 * @property $widget RiskCard
 */
class RiskCardTest extends OEDbTestCase
{
    protected $fixtures = array(
        'operations' => Element_OphTrOperationbooking_Operation::class,
        'operation_procedures' => OphTrOperationbooking_Operation_Procedures::class,
        'whiteboards' => OphTrOperationbooking_Whiteboard::class,
        'patients' => Patient::class,
        'events' => Event::class,
        'episodes' => Episode::class,
        'event_types' => EventType::class,
        'element_types' => ElementType::class,
        'eye' => Eye::class,
        'procedure_risk' => ProcedureRisk::class,
    );

    protected $widget;

    /**
     * @throws CException
     */
    public static function setUpBeforeClass(): void
    {
        Yii::app()->getModule('OphCiExamination');
        Yii::import('application.modules.OphTrOperationbooking.components.*');
        Yii::import('application.modules.OphTrOperationbooking.models.*');
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->widget = new RiskCard();
        $this->widget->data = $this->whiteboards('whiteboard1');
        $this->widget->data->loadData($this->widget->data->event_id);
    }

    public function tearDown(): void
    {
        unset($this->widget);
        parent::tearDown();
    }

    /**
     * @covers RiskCard
     */
    public function testInit()
    {
        $this->widget->init();
        $this->assertEquals('Special', $this->widget->getType());
        $this->assertNotNull($this->widget->getAlphaBlockerRisk());
        $this->assertNotNull($this->widget->getAnticoagulantRisk());
    }

    /**
     * @covers RiskCard
     */
    public function testGetAlphaBlockerRisk()
    {
        $this->widget->init();
        $criteria = new CDbCriteria();
        $criteria->addSearchCondition('name', 'Alpha blockers');
        $alpha_risk = OphCiExaminationRisk::model()->find($criteria);
        $this->assertEquals($alpha_risk->name, $this->widget->getAlphaBlockerRisk()->name);
    }

    /**
     * @covers RiskCard
     */
    public function testGetAnticoagulantRisk()
    {
        $this->widget->init();
        $criteria = new CDbCriteria();
        $criteria->addSearchCondition('name', 'Anticoagulants');
        $anticoag_risk = OphCiExaminationRisk::model()->find($criteria);
        $this->assertEquals($anticoag_risk->name, $this->widget->getAnticoagulantRisk()->name);
    }

    /**
     * @covers RiskCard
     */
    public function testRun()
    {
        $this->widget->data->loadData($this->widget->data->event_id);
        $this->widget->init();
        ob_start();
        $this->widget->run();
        $content = ob_get_clean();
        $this->assertNotNull($content);
    }
}
