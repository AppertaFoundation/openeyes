<?php

    /**
     * Class OphDrPrescription_ItemTest
     * @property OphDrPrescription_Item $item
     */
class OphDrPrescription_ItemTest extends CDbTestCase
{
    private $items = array();
    protected $fixtures = array(
        'ophdrprescription_items' => OphDrPrescription_Item::class,
        'ophdrprescription_item_tapers' => OphDrPrescription_ItemTaper::class,
        'drug_routes' => DrugRoute::class,
        'drug_frequencys' => DrugFrequency::class,
        'drug_durations' => DrugDuration::class,
    );

    public function setUp()
    {
        parent::setUp();
        $this->items[] = $this->ophdrprescription_items('prescription_item1');
        $this->items[] = $this->ophdrprescription_items('prescription_item2');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->item, $this->tapered_item);
    }

    /**
     * @covers OphDrPrescription_Item::getAttrLength()
     * @covers OphDrPrescription_Item::fpTenLinesUsed()
     */
    public function testGetAttrLength()
    {
        foreach ($this->items as $item) {
            $drug_label = $item->drug->label;
            $dose = 'Dose: ' . (is_numeric($item->dose) ? "{$item->dose} {$item->drug->dose_unit}" : $item->dose) . ', ' . $item->route->name . ($item->route_option ? ' (' . $item->route_option->name . ')' : null);
            $frequency = "Frequency: {$item->frequency->long_name} for {$item->duration->name}";

            $item->fpTenLinesUsed();
            $actual = $item->getAttrLength('item_drug');
            $this->assertEquals(ceil(strlen($drug_label) / MAX_FPTEN_LINE_CHARS), $actual, "Drug label has $actual lines, expected 1.");

            $actual = $item->getAttrLength('item_dose');
            $this->assertEquals(ceil(strlen($dose) / MAX_FPTEN_LINE_CHARS), $actual, "Dose has $actual lines, expected 1.");

            $actual = $item->getAttrLength('item_frequency');
            $this->assertEquals(ceil(strlen($frequency) / MAX_FPTEN_LINE_CHARS), $actual, "Frequency has $actual lines, expected 1.");

            foreach ($item->tapers as $index => $taper) {
                $taper_dose = 'Dose: ' . (is_numeric($taper->dose) ? ($taper->dose . ' ' . $item->drug->dose_unit) : $taper->dose) . ', ' . $item->route->name . ($item->route_option ? ' (' . $item->route_option->name . ')' : null);
                $taper_frequency = "Frequency: {$taper->frequency->long_name} for {$taper->duration->name}";
                $actual = $item->getAttrLength("taper{$index}_label");
                $this->assertEquals(1, $actual, "Taper $index label has $actual lines, expected 1.");
                $actual = $item->getAttrLength("taper{$index}_dose");
                $this->assertEquals(ceil(strlen($taper_dose) / MAX_FPTEN_LINE_CHARS), $actual, "Taper $index dose has $actual lines, expected 1.");
                $actual = $item->getAttrLength("taper{$index}_frequency");
                $this->assertEquals(ceil(strlen($taper_frequency) / MAX_FPTEN_LINE_CHARS), $actual, "Taper $index frequency has $actual lines, expected 1.");
            }
        }
    }

    /**
     * @covers OphDrPrescription_Item::fpTenLinesUsed()
     */
    public function testFpTenLinesUsed()
    {
        $length_list = array(9, 5);
        foreach ($this->items as $index => $item) {
            $actual = $item->fpTenLinesUsed();
            $this->assertEquals($length_list[$index], $actual, "Item has $actual lines, expected {$length_list[$index]}.");
        }
    }

    /**
     * @covers OphDrPrescription_Item::fpTenDose
     */
    public function testFpTenDose()
    {
        foreach ($this->items as $item) {
            $expected = 'Dose: ' . (is_numeric($item->dose) ? "{$item->dose} {$item->drug->dose_unit}" : $item->dose) . ', ' . $item->route->name . ($item->route_option ? ' (' . $item->route_option->name . ')' : null);
            $actual = $item->fpTenDose();

            $this->assertEquals($expected, $actual);
        }
    }

    /**
     * @covers OphDrPrescription_Item::fpTenFrequency
     */
    public function testFpTenFrequency()
    {
        foreach ($this->items as $item) {
            $expected = "Frequency: {$item->frequency->long_name} for {$item->duration->name}";
            $actual = $item->fpTenFrequency();

            $this->assertEquals($expected, $actual);
        }
    }
}
