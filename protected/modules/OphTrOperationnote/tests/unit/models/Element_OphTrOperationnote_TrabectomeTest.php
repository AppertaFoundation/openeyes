<?php

/**
 * Class Element_OphTrOperationnote_TrabectomeTest
 *
 * @method trabec($fixtureId)
 */
class Element_OphTrOperationnote_TrabectomeTest extends ActiveRecordTestCase
{
    protected $fixtures = array(
        'trabec' => Element_OphTrOperationnote_Trabectome::class,
        'trabec_comp_ass' => OphTrOperationnote_Trabectome_ComplicationAssignment::class,
        'event' => Event::class,
        'episode' => Episode::class,
        'patient' => Patient::class,
    );
    private Element_OphTrOperationnote_Trabectome $model;
    public function setUp(): void
    {
        $this->model = new Element_OphTrOperationnote_Trabectome();
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->model);
    }

    public function getData()
    {
        return array(
            'New element' => array(
                'fixture' => null,
                'total_complications' => 0,
                'other_complications' => false,
                'complication_str' => 'None',
            ),
            'Existing element' => array(
                'fixture' => 'trabec1',
                'total_complications' => 2,
                'other_complications' => true,
                'complication_str' => 'Haemorrhage, Test complication',
            )
        );
    }

    public function getModel()
    {
        return Element_OphTrOperationnote_Trabectome::model();
    }

    /**
     * @dataProvider getData
     * @param $fixture
     * @param $total_complications
     */
    public function testGetComplicationIDs($fixture, $total_complications)
    {
        if ($fixture) {
            $this->model = $this->trabec($fixture);
        }
        $this->assertCount($total_complications, $this->model->getComplicationIDs());
    }

    /**
     * @dataProvider getData
     * @param $fixture
     * @throws Exception
     */
    public function testRequiredIfComplicationOther($fixture)
    {
        if ($fixture) {
            $this->model = $this->trabec($fixture);

            $this->model->save();
            if ($this->model->hasErrors()) {
                var_export($this->model->getErrors());
            }
            $this->assertCount(0, $this->model->getErrors());
        } else {
            $this->model->event_id = 2;
            $this->model->power_id = 1;
            $this->model->hpmc = 1;
            $this->model->blood_reflux = 0;
            $this->model->description = 'New model';
            if (!$this->model->insert()) {
                $this->fail(var_export($this->model->getErrors(), true));
            }
            $this->model->updateComplications(array(5));

            // Validation error is deferred, so save again with no other changes.
            $this->model->save();
            $this->assertCount(1, $this->model->getErrors());

            $this->model->complication_other = 'Test';
            $this->model->save();
            $this->assertCount(0, $this->model->getErrors());
        }
    }

    /**
     * @dataProvider getData
     * @param $fixture
     * @param $total_complications
     * @param bool $other_complications
     */
    public function testHasOtherComplication($fixture, $total_complications = null, $other_complications = false)
    {
        if ($fixture) {
            $this->model = $this->trabec($fixture);
        }
        $this->assertCount($total_complications, $this->model->complications);
        $this->assertEquals($other_complications, $this->model->hasOtherComplication());
    }

    /**
     * @dataProvider getData
     * @param $fixture
     * @param $total_complications
     * @param $other_complications
     * @param string|null $complication_str
     */
    public function testGetComplicationsString($fixture, $total_complications, $other_complications, $complication_str = null)
    {
        if ($fixture) {
            $this->model = $this->trabec($fixture);
        }
        $this->assertCount($total_complications, $this->model->complications);
        if ($other_complications) {
            $this->assertTrue($this->model->hasOtherComplication());
        }
        $this->assertEquals($complication_str, $this->model->getComplicationsString());
    }

    /**
     * @dataProvider getData
     * @param $fixture
     * @param $total_complications
     * @throws Exception
     */
    public function testUpdateComplications($fixture)
    {
        if ($fixture) {
            $this->model = $this->trabec($fixture);
        } else {
            $this->model->event_id = 3;
            $this->model->power_id = 1;
            $this->model->hpmc = 1;
            $this->model->blood_reflux = 0;
            $this->model->description = 'New model';
            if (!$this->model->insert()) {
                $this->fail(var_export($this->model->getErrors(), true));
            }
        }

        $this->model->updateComplications(array(1, 2, 5));
        $this->assertCount(3, $this->model->complications);
    }
}
