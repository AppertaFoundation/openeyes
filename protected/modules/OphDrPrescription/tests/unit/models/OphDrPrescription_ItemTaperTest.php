<?php

    /**
     * Class OphDrPrescription_ItemTaperTest
     * @property OphDrPrescription_ItemTaper $instance
     */
class OphDrPrescription_ItemTaperTest extends ActiveRecordTestCase
{
    protected $fixtures = array(
        'item_tapers' => OphDrPrescription_ItemTaper::class,
        'items' => OphDrPrescription_Item::class,
        'durations' => MedicationDuration::class,
        'frequencys' => MedicationFrequency::class,
        'routes' => MedicationRoute::class,
    );

    private $instance;

    public function getModel()
    {
        return $this->instance;
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->instance = $this->item_tapers('prescription_item_taper1');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->instance);
    }

    /**
     * @covers OphDrPrescription_ItemTaper::fpTenDose
     */
    public function testFpTenDose()
    {
        $expected = strtoupper('DOSE: '
            . (is_numeric($this->instance->dose) ? "{$this->instance->dose} {$this->instance->item->dose_unit_term}" : $this->instance->dose)
            . ', ' . $this->instance->item->route->term
            . ($this->instance->item->medicationLaterality ? ' (' . $this->instance->item->medicationLaterality->name . ')' : null));
        $actual = $this->instance->fpTenDose();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers OphDrPrescription_ItemTaper::fpTenFrequency
     */
    public function testFpTenFrequency()
    {
        $expected = strtoupper("FREQUENCY: {$this->instance->frequency->term} FOR {$this->instance->duration->name}");
        $actual = $this->instance->fpTenFrequency();

        $this->assertEquals($expected, $actual);

        $this->instance = $this->item_tapers('prescription_item_taper8');
        $duration = strtolower($this->instance->duration->name);
        $expected = strtoupper("FREQUENCY: {$this->instance->frequency->term} {$duration}");
        $actual = $this->instance->fpTenFrequency();

        $this->assertEquals($expected, $actual);
    }
}
