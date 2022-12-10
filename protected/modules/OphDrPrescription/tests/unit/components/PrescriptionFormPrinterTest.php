<?php

/**
 * Class PrescriptionFormPrinterTest
 * @method patients($fixtureId)
 * @method prescription_items($fixtureId)
 * @method prescription_item_tapers($fixtureId)
 * @method users($fixtureId)
 * @method firms($fixtureId)
 * @method sites($fixtureId)
 */
class PrescriptionFormPrinterTest extends OEDbTestCase
{
    protected $fixtures = array(
        'patients' => Patient::class,
        'users' => User::class,
        'firms' => Firm::class,
        'sites' => Site::class,
        'prescription_items' => OphDrPrescription_Item::class,
        'prescription_item_tapers' => OphDrPrescription_ItemTaper::class,
        'drug_duration' => MedicationDuration::class,
        'drug_route' => MedicationRoute::class,
        'drug_frequency' => MedicationFrequency::class,
    );

    private PrescriptionFormPrinter $instance;

    public function setUp(): void
    {
        parent::setUp();
        $this->instance = new PrescriptionFormPrinter();
        $this->instance->patient = $this->patients('patient1');
        $this->instance->user = $this->users('user1');
        $this->instance->firm = $this->firms('firm1');
        $this->instance->site = $this->sites('site1');
        $this->instance->items = array($this->prescription_items('prescription_item1'), $this->prescription_items('prescription_item2'));
        $this->instance->init();
    }

    public static function setUpBeforeClass(): void
    {
        Yii::app()->session['selected_institution_id'] = 1;
    }

    public static function tearDownAfterClass(): void
    {
        unset(Yii::app()->session['selected_institution_id']);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->instance);
    }
    /**
     * @covers PrescriptionFormPrinter::enableSplitPrint
     * @covers PrescriptionFormPrinter::disableSplitPrint
     * @covers PrescriptionFormPrinter::isSplitPrinting
     */
    public function testIsSplitPrinting()
    {
        $this->instance->enableSplitPrint();
        $this->assertTrue($this->instance->isSplitPrinting());

        $this->instance->disableSplitPrint();
        $this->assertFalse($this->instance->isSplitPrinting());
    }

    /**
     * @covers PrescriptionFormPrinter::init
     */
    public function testInit()
    {
        $settings = new SettingMetadata();
        $default_prescription_cost_code = $settings->getSetting('default_prescription_cost_code');
        $prescription_form_format = $settings->getSetting('prescription_form_format');

        $this->assertEquals($default_prescription_cost_code, $this->instance->getDefaultCostCode());
        $this->assertEquals($prescription_form_format, $this->instance->getPrintMode());
    }

    /**
     * @covers PrescriptionFormPrinter::init
     * @covers PrescriptionFormPrinter::run
     * @throws CException
     */
    public function testRun()
    {
        ob_start();
        $this->instance->run();
        $widget_output = ob_get_clean();
        $this->assertNotNull($widget_output);

        $this->instance->items[] = $this->prescription_items('prescription_item4');
        $this->instance->init();

        ob_start();
        $this->instance->run();
        $widget_output = ob_get_clean();
        $this->assertNotNull($widget_output);
    }

    /**
     * @covers PrescriptionFormPrinter::setCurrentAttr
     * @covers PrescriptionFormPrinter::getCurrentItemAttr
     */
    public function testGetCurrentItemAttr()
    {
        $this->instance->setCurrentAttr('dose');
        $this->assertEquals('item_dose', $this->instance->getCurrentItemAttr());

        $this->instance->setCurrentAttr('frequency', 0);
        $this->assertEquals('taper0_frequency', $this->instance->getCurrentItemAttr());
    }

    /**
     * @covers PrescriptionFormPrinter::setCurrentAttr
     * @covers PrescriptionFormPrinter::getCurrentItemAttr
     */
    public function testSetCurrentAttr()
    {
        $this->instance->setCurrentAttr('dose');
        $this->assertEquals('item_dose', $this->instance->getCurrentItemAttr());

        $this->instance->setCurrentAttr('frequency', 0);
        $this->assertEquals('taper0_frequency', $this->instance->getCurrentItemAttr());

        $this->instance->setCurrentAttr();
        $this->assertNull($this->instance->getCurrentItemAttr());
    }

    /**
     * @covers PrescriptionFormPrinter::getTotalPages
     */
    public function testGetTotalPages()
    {
        $expected = 1;
        $actual = $this->instance->getTotalPages();
        $this->assertEquals($expected, $actual);

        $expected = 2;
        $this->instance->items = array($this->prescription_items('prescription_item4'));
        $this->instance->init();
        $actual = $this->instance->getTotalPages();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers PrescriptionFormPrinter::getPageNumber
     * @covers PrescriptionFormPrinter::init
     * @throws CException
     */
    public function testGetPageNumber()
    {
        // Before output, ensure the page number is 1.
        $expected = 1;
        $this->assertEquals($expected, $this->instance->getPageNumber());

        ob_start();
        $this->instance->run();
        ob_end_clean();

        // After output, ensure the page number is still 1.
        $expected = 1;
        $this->assertEquals($expected, $this->instance->getPageNumber());

        // Before output, ensure the page number has been reset to 1. Position of items in the array is important to
        // page structure, so ensure the item is inserted at the end before proceeding.
        $this->instance->items[] = $this->prescription_items('prescription_item4');
        $this->instance->init();
        $this->assertCount(3, $this->instance->items);
        $this->assertEquals($this->prescription_items('prescription_item4'), $this->instance->items[2]);

        $expected = 1;
        $this->assertEquals($expected, $this->instance->getPageNumber());

        ob_start();
        $this->instance->run();
        ob_end_clean();

        // After output, ensure the page number has increased to 3.
        $expected = 3;
        $this->assertEquals($expected, $this->instance->getPageNumber());

        // Rearrange the array so the split-print item is the first item in the list.
        $this->instance->items = array(
            $this->prescription_items('prescription_item4'),
            $this->prescription_items('prescription_item1'),
            $this->prescription_items('prescription_item2'),
        );
        $this->instance->init();
        $this->assertCount(3, $this->instance->items);
        $this->assertEquals($this->prescription_items('prescription_item4'), $this->instance->items[0]);

        $expected = 1;
        $this->assertEquals($expected, $this->instance->getPageNumber());

        ob_start();
        $this->instance->run();
        ob_end_clean();

        // After output, ensure the page number has increased to 3.
        $expected = 3;
        $this->assertEquals($expected, $this->instance->getPageNumber());

        // Before output, ensure the page number has been reset to 1.
        $this->instance->items = array($this->prescription_items('prescription_item4'));
        $this->instance->init();
        $this->assertCount(1, $this->instance->items);
        $expected = 1;
        $this->assertEquals($expected, $this->instance->getPageNumber());

        ob_start();
        $this->instance->run();
        ob_end_clean();

        // After output, ensure the page number has increased to 2.
        $expected = 2;
        $this->assertEquals($expected, $this->instance->getPageNumber());
    }

    /**
     * @covers PrescriptionFormPrinter::enableSplitPrint
     */
    public function testEnableSplitPrint()
    {
        $this->instance->enableSplitPrint();
        $this->assertTrue($this->instance->isSplitPrinting());
    }

    /**
     * @covers PrescriptionFormPrinter::disableSplitPrint
     * @covers PrescriptionFormPrinter::isSplitPrinting
     */
    public function testDisableSplitPrint()
    {
        $this->instance->disableSplitPrint();
        $this->assertFalse($this->instance->isSplitPrinting());
    }

    /**
     * @covers PrescriptionFormPrinter::getDefaultCostCode
     */
    public function testGetDefaultCostCode()
    {
        $settings = new SettingMetadata();
        $default_prescription_cost_code = $settings->getSetting('default_prescription_cost_code');

        $this->assertEquals($default_prescription_cost_code, $this->instance->getDefaultCostCode());
    }

    /**
     * @covers PrescriptionFormPrinter::isPrintable
     */
    public function testIsPrintable()
    {
        $item = $this->prescription_items('prescription_item1');
        $this->assertTrue($this->instance->isPrintable($item));

        $item = $this->prescription_items('prescription_item3');
        $this->assertFalse($this->instance->isPrintable($item));
    }

    /**
     * @covers PrescriptionFormPrinter::setCurrentAttrStr
     * @covers PrescriptionFormPrinter::getCurrentItemAttr
     */
    public function testSetCurrentAttrStr()
    {
        $this->instance->setCurrentAttrStr('item_dose');
        $this->assertEquals('item_dose', $this->instance->getCurrentItemAttr());

        $this->instance->setCurrentAttrStr('taper0_frequency');
        $this->assertEquals('taper0_frequency', $this->instance->getCurrentItemAttr());
    }

    /**
     * @covers PrescriptionFormPrinter::addPages
     * @covers PrescriptionFormPrinter::getPageNumber
     * @covers PrescriptionFormPrinter::getTotalPages
     * @covers PrescriptionFormPrinter::init
     */
    public function testAddPages()
    {
        // Add single page for no effect.
        $this->instance->addPages();

        $this->assertEquals(1, $this->instance->getTotalPages());
        $this->assertEquals(1, $this->instance->getPageNumber());

        // Add single page for multi-page item.
        $this->instance->items = array($this->prescription_items('prescription_item4'));
        $this->instance->init();
        $current_pages = $this->instance->getTotalPages();
        $this->instance->addPages();

        $this->assertEquals($current_pages, $this->instance->getTotalPages());
        $this->assertEquals(2, $this->instance->getPageNumber());

        $this->instance->items[] = $this->prescription_items('prescription_item3');
        $this->instance->items[] = $this->prescription_items('prescription_item5');
        $this->instance->init();
        $current_pages = $this->instance->getTotalPages();
        $this->assertEquals($current_pages, $this->instance->getTotalPages());
        $this->assertEquals(1, $this->instance->getPageNumber());
        $this->instance->addPages(1);

        $this->assertEquals($current_pages, $this->instance->getTotalPages());
        $this->assertEquals(2, $this->instance->getPageNumber());
    }

    /**
     * @covers PrescriptionFormPrinter::getPrintMode
     */
    public function testGetPrintMode()
    {
        $settings = new SettingMetadata();
        $prescription_form_format = $settings->getSetting('prescription_form_format');

        $this->assertEquals($prescription_form_format, $this->instance->getPrintMode());
    }

    /**
     * This regression test ensures that the total item count is not polluted by the widget generation process.
     * @covers PrescriptionFormPrinter::getTotalItems
     * @covers PrescriptionFormPrinter::run
     * @throws CException
     */
    public function testGetTotalItems()
    {
        $expected = 2;
        $actual = $this->instance->getTotalItems();
        $this->assertEquals($expected, $actual);

        ob_start();
        $this->instance->run();
        ob_end_clean();

        $actual = $this->instance->getTotalItems();
        $this->assertEquals($expected, $actual);
    }
}
