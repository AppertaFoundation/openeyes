<?php

    /**
     * Class OphDrPrescription_ItemTest
     * @property OphDrPrescription_Item $item
     */
class OphDrPrescription_ItemTest extends ActiveRecordTestCase
{
    private $items = array();
    protected $fixtures = array(
        'items' => OphDrPrescription_Item::class,
        'item_tapers' => OphDrPrescription_ItemTaper::class,
        'drug_routes' => MedicationRoute::class,
        'drug_frequencys' => MedicationFrequency::class,
        'medication_durations' => MedicationDuration::class,
        'drug_durations' => MedicationDuration::class,
        'medications' => Medication::class,
    );

    public function getModel()
    {
        return OphDrPrescription_Item::model();
    }

    protected array $columns_to_skip = [
        'start_date'
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->items[] = $this->items('prescription_item1');
        $this->items[] = $this->items('prescription_item2');
        $this->items[] = $this->items('prescription_item4');
        $this->items[] = $this->items('prescription_item6');
    }

    public function getLineUsage()
    {
        return array(
            'Single taper' => array(
                'lines' => 9,
                'index' => 0,
            ),
            'No taper' => array(
                'lines' => 6,
                'index' => 1,
            ),
            'Multiple tapers' => array(
                'lines' => 34,
                'index' => 2,
            ),
            'Simple duration' => array(
                'lines' => 5,
                'index' => 3,
            )
        );
    }

    public function tearDown(): void
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
        $settings = new SettingMetadata();
        $max_lines = $settings->getSetting('prescription_form_format') === 'WP10'
            ? OphDrPrescription_Item::MAX_WPTEN_LINE_CHARS : OphDrPrescription_Item::MAX_FPTEN_LINE_CHARS;
        foreach ($this->items as $item) {
            $drug_label = $item->medication->label;
            $dose = 'Dose: ' . (is_numeric($item->dose) ? "{$item->dose} {$item->dose_unit_term}" : $item->dose)
                . ', ' . $item->route->term . ($item->medicationLaterality ? ' (' . $item->medicationLaterality->name . ')' : null);
            $frequency = "Frequency: {$item->frequency->term} for {$item->medicationDuration->name}";

            $item->fpTenLinesUsed();
            $actual = $item->getAttrLength('item_drug');
            $this->assertEquals(ceil(strlen($drug_label) / $max_lines), $actual, "Drug label has $actual lines, expected 1.");

            $actual = $item->getAttrLength('item_dose');
            $this->assertEquals(ceil(strlen($dose) / $max_lines), $actual, "Dose has $actual lines, expected 1.");

            $actual = $item->getAttrLength('item_frequency');
            $this->assertEquals(ceil(strlen($frequency) / $max_lines), $actual, "Frequency has $actual lines, expected 1.");

            foreach ($item->tapers as $index => $taper) {
                $taper_dose = 'Dose: ' . (is_numeric($taper->dose) ? ($taper->dose . ' ' . $item->dose_unit_term) : $taper->dose)
                    . ', ' . $item->route->term . ($item->medicationLaterality ? ' (' . $item->medicationLaterality->name . ')' : null);
                $taper_frequency = "Frequency: {$taper->frequency->term} for {$taper->duration->name}";
                $actual = $item->getAttrLength("taper{$index}_label");
                $this->assertEquals(1, $actual, "Taper $index label has $actual lines, expected 1.");
                $actual = $item->getAttrLength("taper{$index}_dose");
                $this->assertEquals(ceil(strlen($taper_dose) / $max_lines), $actual, "Taper $index dose has $actual lines, expected 1.");
                $actual = $item->getAttrLength("taper{$index}_frequency");
                $this->assertEquals(
                    ceil(strlen($taper_frequency) / $max_lines),
                    $actual,
                    "Taper $index frequency has $actual lines, expected 1."
                );
            }
        }
    }

    /**
     * @covers OphDrPrescription_Item::fpTenLinesUsed()
     * @dataProvider getLineUsage
     * @param $lines
     * @param $index
     */
    public function testFpTenLinesUsed($lines, $index)
    {
        $actual = $this->items[$index]->fpTenLinesUsed();

        $this->assertEquals($lines, $actual, "Item has $actual lines, expected {$lines}.");
    }

    /**
     * @covers OphDrPrescription_Item::fpTenDose
     */
    public function testFpTenDose()
    {
        foreach ($this->items as $item) {
            $expected = strtoupper('Dose: ' . (is_numeric($item->dose) ? "{$item->dose} {$item->dose_unit_term}" : $item->dose)
                . ', ' . $item->route->term . ($item->medicationLaterality ? ' (' . $item->medicationLaterality->name . ')' : null));

            $actual = $item->fpTenDose();

            $this->assertEquals($expected, $actual);
        }
    }

    /**
     * @covers OphDrPrescription_Item::fpTenFrequency
     */
    public function testFpTenFrequency()
    {
        foreach ($this->items as $index => $item) {
            if ($index === 3) {
                $duration = strtoupper($item->medicationDuration->name);
                $expected = strtoupper("FREQUENCY: {$item->frequency->term} {$duration}");
            } else {
                $expected = strtoupper("FREQUENCY: {$item->frequency->term} FOR {$item->medicationDuration->name}");
            }
            $actual = $item->fpTenFrequency();

            $this->assertEquals($expected, $actual);
        }
    }
}
