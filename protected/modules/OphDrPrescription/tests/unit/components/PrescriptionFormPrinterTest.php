<?php


class PrescriptionFormPrinterTest extends CDbTestCase
{
    protected $fixtures = array(
        'patients' => Patient::class,
        'users' => User::class,
        'firms' => Firm::class,
        'sites' => Site::class,
        'ophdrprescription_items' => OphDrPrescription_Item::class,
        'ophdrprescription_item_tapers' => OphDrPrescription_ItemTaper::class,
    );

    private $instance;

    public function setUp()
    {
        parent::setUp();
        $this->instance = new PrescriptionFormPrinter();
        $this->instance->patient = $this->patients('patient1');
        $this->instance->user = $this->users('user1');
        $this->instance->firm = $this->firms('firm1');
        $this->instance->site = $this->sites('site1');
        $this->instance->items = array($this->ophdrprescription_items('prescription_item1'), $this->ophdrprescription_items('prescription_item2'));
        $this->instance->init();
    }

    public function tearDown()
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
     */
    public function testRun()
    {
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

        ob_start();
        $this->instance->run();
        ob_end_clean();
        $expected = 1;
        $actual = $this->instance->getTotalPages();
        $this->assertEquals($expected, $actual);
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
        $item = $this->ophdrprescription_items('prescription_item1');
        $this->assertTrue($this->instance->isPrintable($item));

        $item = $this->ophdrprescription_items('prescription_item3');
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
     */
    public function testAddPages()
    {
        // Add single page.
        $current_pages = $this->instance->getTotalPages();
        $this->instance->addPages();

        $this->assertGreaterThan($current_pages, $this->instance->getTotalPages());

        // Add multiple pages at once.
        $current_pages = $this->instance->getTotalPages();
        $this->instance->addPages(3);

        $this->assertGreaterThan($current_pages, $this->instance->getTotalPages());
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

    /**
     * @covers PrescriptionFormPrinter::addSplitPage
     * @covers PrescriptionFormPrinter::resetSplitPageCount
     * @covers PrescriptionFormPrinter::getTotalSplitPages
     * @covers PrescriptionFormPrinter::getSplitPageNumber
     */
    public function testAddSplitPage()
    {
        // Ensure no issues if the line count is greater than the current maximum for a page.
        $this->instance->resetSplitPageCount(32);
        $this->instance->addSplitPage();
        $this->assertEquals(2, $this->instance->getTotalSplitPages());
        $this->assertEquals(2, $this->instance->getSplitPageNumber());

        // Ensure no issues if the line count is greater than the current maximum for a page and an extra page is attempted to be added.
        $this->instance->addSplitPage();
        $this->assertEquals(2, $this->instance->getTotalSplitPages());
        $this->assertEquals(2, $this->instance->getSplitPageNumber());

        // Ensure no issues if the line count is less than the current maximum for a page.
        $this->instance->resetSplitPageCount(20);
        $this->instance->addSplitPage();
        $this->assertEquals(1, $this->instance->getTotalSplitPages());
        $this->assertEquals(1, $this->instance->getSplitPageNumber());
    }

    /**
     * @covers PrescriptionFormPrinter::getSplitPageNumber
     */
    public function testGetSplitPageNumber()
    {
        $actual = $this->instance->getSplitPageNumber();
        $this->assertEquals(1, $actual);
    }

    /**
     * @covers PrescriptionFormPrinter::getTotalSplitPages
     */
    public function testGetTotalSplitPages()
    {
        $actual = $this->instance->getTotalSplitPages();
        $this->assertEquals(1, $actual);
    }

    /**
     * @covers PrescriptionFormPrinter::addSplitPage
     * @covers PrescriptionFormPrinter::resetSplitPageCount
     * @covers PrescriptionFormPrinter::getTotalSplitPages
     * @covers PrescriptionFormPrinter::getSplitPageNumber
     */
    public function testResetSplitPageCount()
    {
        // Ensure no issues if the line count is less than the current maximum for a page.
        $this->instance->resetSplitPageCount(20);
        $this->assertEquals(1, $this->instance->getTotalSplitPages());
        $this->assertEquals(1, $this->instance->getSplitPageNumber());

        $this->instance->addSplitPage();
        $this->instance->resetSplitPageCount(20);
        $this->assertEquals(1, $this->instance->getTotalSplitPages());
        $this->assertEquals(1, $this->instance->getSplitPageNumber());

        // Ensure no issues if the line count is greater than the current maximum for a page.
        $this->instance->resetSplitPageCount(32);
        $this->assertEquals(2, $this->instance->getTotalSplitPages());
        $this->assertEquals(1, $this->instance->getSplitPageNumber());

        // Ensure no issues if a page is added and then the counter is reset.
        $this->instance->addSplitPage();
        $this->instance->resetSplitPageCount(32);
        $this->assertEquals(2, $this->instance->getTotalSplitPages());
        $this->assertEquals(1, $this->instance->getSplitPageNumber());
    }
}
